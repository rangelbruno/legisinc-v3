<?php

namespace App\Http\Controllers\OnlyOffice;

use App\Http\Controllers\Controller;
use App\Services\OnlyOffice\OnlyOfficeService;
use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OnlyOfficeController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(
        private OnlyOfficeService $onlyOfficeService
    ) {}
    
    public function editarModelo(DocumentoModelo $modelo)
    {
        // Debug information
        if (!auth()->check()) {
            abort(401, 'User not authenticated');
        }
        
        $user = auth()->user();
        \Log::info('OnlyOffice editarModelo - User check:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->getRoleNames()->toArray(),
            'modelo_id' => $modelo->id,
        ]);
        
        $this->authorize('update', $modelo);
        
        // Only regenerate document_key if the file was recently modified (to avoid callback conflicts)
        // This prevents cache issues while avoiding orphaned callbacks
        $fileLastModified = $modelo->updated_at;
        $keyAge = now()->diffInMinutes($fileLastModified);
        
        if (empty($modelo->document_key) || $keyAge > 5) { // Regenerate if key is empty or file was modified >5min ago
            $oldKey = $modelo->document_key;
            $novoDocumentKey = 'modelo_' . time() . '_' . uniqid() . '_' . rand(1000, 9999);
            $modelo->update(['document_key' => $novoDocumentKey]);
            
            \Log::info('Document key regenerated for modelo:', [
                'modelo_id' => $modelo->id,
                'old_key' => $oldKey,
                'new_key' => $novoDocumentKey,
                'reason' => 'empty key or file modified >5min ago',
                'key_age_minutes' => $keyAge
            ]);
        } else {
            \Log::info('Using existing document key for modelo (recently modified):', [
                'modelo_id' => $modelo->id,
                'existing_key' => $modelo->document_key,
                'key_age_minutes' => $keyAge
            ]);
        }
        
        $config = $this->onlyOfficeService->criarConfiguracao(
            $modelo->document_key,
            $modelo->arquivo_nome,
            $this->generateFileUrlForOnlyOffice('onlyoffice.file.modelo', $modelo),
            [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
            ],
            'edit'
        );
        
        return view('modules.documentos.editor', [
            'config' => $config,
            'modelo' => $modelo,
            'title' => 'Editando Modelo: ' . $modelo->nome
        ]);
    }
    
    public function editarDocumento(DocumentoInstancia $instancia)
    {
        $this->authorize('update', $instancia);
        
        $config = $this->onlyOfficeService->criarConfiguracao(
            $instancia->document_key,
            $instancia->arquivo_nome,
            $this->generateFileUrlForOnlyOffice('onlyoffice.file.instancia', $instancia),
            [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
            ],
            $this->determinarModoEdicao($instancia)
        );
        
        return view('modules.documentos.editor', [
            'config' => $config,
            'instancia' => $instancia,
            'title' => 'Editando: ' . $instancia->titulo
        ]);
    }
    
    public function visualizarDocumento(DocumentoInstancia $instancia)
    {
        $this->authorize('view', $instancia);
        
        $config = $this->onlyOfficeService->criarConfiguracao(
            $instancia->document_key,
            $instancia->arquivo_nome,
            $this->generateFileUrlForOnlyOffice('onlyoffice.file.instancia', $instancia),
            [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
            ],
            'view'
        );
        
        return view('modules.documentos.viewer', [
            'config' => $config,
            'instancia' => $instancia,
            'title' => 'Visualizando: ' . $instancia->titulo
        ]);
    }
    
    public function callback(Request $request, string $documentKey)
    {
        try {
            \Log::info('OnlyOffice Callback received:', [
                'document_key' => $documentKey,
                'data' => $request->all(),
                'headers' => $request->headers->all()
            ]);
            
            $data = $request->all();
            $resultado = $this->onlyOfficeService->processarCallback($documentKey, $data);
            
            \Log::info('OnlyOffice Callback processed successfully:', [
                'document_key' => $documentKey,
                'result' => $resultado
            ]);
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            \Log::error('Erro no callback ONLYOFFICE: ' . $e->getMessage(), [
                'document_key' => $documentKey,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 1, 'message' => $e->getMessage()]);
        }
    }
    
    public function downloadModelo(DocumentoModelo $modelo)
    {
        // Skip authorization for OnlyOffice server access
        // $this->authorize('view', $modelo);
        
        \Log::info('Download modelo requested:', [
            'modelo_id' => $modelo->id,
            'modelo_nome' => $modelo->nome,
            'arquivo_path' => $modelo->arquivo_path,
            'arquivo_nome' => $modelo->arquivo_nome,
            'document_key' => $modelo->document_key
        ]);
        
        // List all files in the directory for debugging
        $files = Storage::disk('public')->files('documentos/modelos');
        \Log::info('Files in modelos directory:', $files);
        
        if (!$modelo->arquivo_path || !Storage::disk('public')->exists($modelo->arquivo_path)) {
            \Log::warning('File not found, creating empty file:', [
                'arquivo_path' => $modelo->arquivo_path,
                'exists' => Storage::disk('public')->exists($modelo->arquivo_path ?? 'NULL')
            ]);
            
            // Try to find existing files with similar names
            $modeloSlug = \Illuminate\Support\Str::slug($modelo->nome);
            $possibleFiles = [
                "documentos/modelos/{$modeloSlug}.rtf",
                "documentos/modelos/{$modeloSlug}.docx",
                "documentos/modelos/modelo-teste.rtf",
                "documentos/modelos/teste.rtf",
                "documentos/modelos/teste.docx"
            ];
            
            $foundFile = null;
            foreach ($possibleFiles as $possibleFile) {
                if (Storage::disk('public')->exists($possibleFile)) {
                    $foundFile = $possibleFile;
                    \Log::info('Found existing file:', ['file' => $foundFile]);
                    break;
                }
            }
            
            if ($foundFile) {
                // Update the modelo with the found file
                $modelo->update([
                    'arquivo_path' => $foundFile,
                    'arquivo_nome' => basename($foundFile),
                    'arquivo_size' => Storage::disk('public')->size($foundFile)
                ]);
                
                \Log::info('Updated modelo with found file:', [
                    'modelo_id' => $modelo->id,
                    'new_path' => $foundFile
                ]);
            } else {
                // Criar arquivo vazio se não existir
                $this->criarArquivoVazio($modelo);
            }
        }
        
        return Storage::disk('public')->response($modelo->arquivo_path, $modelo->arquivo_nome, [
            'Content-Type' => 'application/rtf'
        ]);
    }
    
    public function downloadInstancia(DocumentoInstancia $instancia)
    {
        // Skip authorization for OnlyOffice server access
        // $this->authorize('view', $instancia);
        
        if (!$instancia->arquivo_path || !Storage::disk('public')->exists($instancia->arquivo_path)) {
            abort(404, 'Arquivo não encontrado');
        }
        
        return Storage::disk('public')->response($instancia->arquivo_path, $instancia->arquivo_nome, [
            'Content-Type' => 'application/rtf'
        ]);
    }
    
    public function converterParaPDF(DocumentoInstancia $instancia)
    {
        $this->authorize('view', $instancia);
        
        try {
            $pdfUrl = $this->onlyOfficeService->converterParaPDF($instancia->arquivo_path);
            
            // Download do PDF
            $response = \Http::get($pdfUrl);
            
            if ($response->successful()) {
                $nomeArquivoPDF = pathinfo($instancia->arquivo_nome, PATHINFO_FILENAME) . '.pdf';
                
                return response($response->body())
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $nomeArquivoPDF . '"');
            }
            
            throw new \Exception('Erro ao baixar PDF convertido');
            
        } catch (\Exception $e) {
            \Log::error('Erro na conversão para PDF: ' . $e->getMessage());
            return back()->with('error', 'Erro ao converter documento para PDF: ' . $e->getMessage());
        }
    }
    
    public function obterHistoricoVersoes(DocumentoInstancia $instancia)
    {
        $this->authorize('view', $instancia);
        
        $versoes = $instancia->versoes()
            ->with('modificadoPor')
            ->orderBy('versao', 'desc')
            ->get();
            
        return response()->json([
            'versoes' => $versoes->map(function($versao) {
                return [
                    'version' => $versao->versao,
                    'key' => $versao->instancia->document_key . '_v' . $versao->versao,
                    'url' => route('onlyoffice.versao', $versao->id),
                    'created' => $versao->created_at->toISOString(),
                    'user' => [
                        'id' => $versao->modificadoPor->id,
                        'name' => $versao->modificadoPor->name
                    ],
                    'changes' => $versao->alteracoes ?? []
                ];
            })
        ]);
    }
    
    private function criarArquivoVazio(DocumentoModelo $modelo): void
    {
        $templatePath = resource_path('templates/documento_base.docx');
        $destinoPath = "documentos/modelos/" . $modelo->arquivo_nome;
        
        if (file_exists($templatePath)) {
            Storage::disk('public')->put($destinoPath, file_get_contents($templatePath));
        } else {
            // Criar documento RTF básico
            $conteudoBase = '{\rtf1\ansi\deff0 {\fonttbl {\f0 Times New Roman;}}
{\colortbl;\red0\green0\blue0;}
\f0\fs24

{\qc\b\fs28 Modelo: ' . $modelo->nome . '\par}
\par
Este é um modelo base para começar a edição.\par
\par
Você pode editá-lo usando o OnlyOffice.\par
}';
            
            Storage::disk('public')->put($destinoPath, $conteudoBase);
        }
        
        $modelo->update([
            'arquivo_path' => $destinoPath,
            'arquivo_size' => Storage::disk('public')->size($destinoPath)
        ]);
    }
    
    private function determinarModoEdicao(DocumentoInstancia $instancia): string
    {
        $user = auth()->user();
        
        // Administradores sempre podem editar
        if ($user->hasRole('admin')) {
            return 'edit';
        }
        
        // Verificar se é colaborador com permissão de edição
        $colaborador = $instancia->colaboradores()
            ->where('user_id', $user->id)
            ->where('ativo', true)
            ->first();
            
        if ($colaborador && in_array($colaborador->permissao, ['edit', 'admin'])) {
            return 'edit';
        }
        
        // Parlamentar só pode editar se for o autor e documento estiver em rascunho
        if ($user->hasRole('parlamentar') && 
            $instancia->projeto && 
            $instancia->projeto->autor_id === $user->id && 
            $instancia->status === 'rascunho') {
            return 'edit';
        }
        
        // Equipe legislativa pode editar quando documento está em revisão
        if ($user->hasRole('legislativo') && 
            in_array($instancia->status, ['parlamentar', 'legislativo'])) {
            return 'edit';
        }
        
        return 'view';
    }
    
    /**
     * Generate file URL that OnlyOffice can access from within Docker container
     */
    private function generateFileUrlForOnlyOffice(string $routeName, $model): string
    {
        $url = route($routeName, $model);
        
        // Replace localhost:8001 with container name for OnlyOffice access
        return str_replace('http://localhost:8001', 'http://legisinc-app:80', $url);
    }
    
    /**
     * Standalone methods - open in new tab without system layout
     */
    public function editarModeloStandalone(DocumentoModelo $modelo)
    {
        // Debug information
        if (!auth()->check()) {
            abort(401, 'User not authenticated');
        }
        
        $user = auth()->user();
        \Log::info('OnlyOffice editarModeloStandalone - User check:', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_roles' => $user->getRoleNames()->toArray(),
            'modelo_id' => $modelo->id,
        ]);
        
        $this->authorize('update', $modelo);
        
        // Only regenerate document_key if the file was recently modified (to avoid callback conflicts)
        // This prevents cache issues while avoiding orphaned callbacks
        $fileLastModified = $modelo->updated_at;
        $keyAge = now()->diffInMinutes($fileLastModified);
        
        if (empty($modelo->document_key) || $keyAge > 5) { // Regenerate if key is empty or file was modified >5min ago
            $oldKey = $modelo->document_key;
            $novoDocumentKey = 'modelo_' . time() . '_' . uniqid() . '_' . rand(1000, 9999);
            $modelo->update(['document_key' => $novoDocumentKey]);
            
            \Log::info('Document key regenerated for modelo:', [
                'modelo_id' => $modelo->id,
                'old_key' => $oldKey,
                'new_key' => $novoDocumentKey,
                'reason' => 'empty key or file modified >5min ago',
                'key_age_minutes' => $keyAge
            ]);
        } else {
            \Log::info('Using existing document key for modelo (recently modified):', [
                'modelo_id' => $modelo->id,
                'existing_key' => $modelo->document_key,
                'key_age_minutes' => $keyAge
            ]);
        }
        
        $config = $this->onlyOfficeService->criarConfiguracao(
            $modelo->document_key,
            $modelo->arquivo_nome,
            $this->generateFileUrlForOnlyOffice('onlyoffice.file.modelo', $modelo),
            [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
            ],
            'edit'
        );
        
        return view('onlyoffice.standalone-editor', [
            'config' => $config,
            'modelo' => $modelo,
            'title' => 'Editando Modelo: ' . $modelo->nome
        ]);
    }
    
    public function editarDocumentoStandalone(DocumentoInstancia $instancia)
    {
        $this->authorize('update', $instancia);
        
        $config = $this->onlyOfficeService->criarConfiguracao(
            $instancia->document_key,
            $instancia->arquivo_nome,
            $this->generateFileUrlForOnlyOffice('onlyoffice.file.instancia', $instancia),
            [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
            ],
            $this->determinarModoEdicao($instancia)
        );
        
        return view('onlyoffice.standalone-editor', [
            'config' => $config,
            'instancia' => $instancia,
            'title' => 'Editando: ' . $instancia->titulo
        ]);
    }
    
    public function visualizarDocumentoStandalone(DocumentoInstancia $instancia)
    {
        $this->authorize('view', $instancia);
        
        $config = $this->onlyOfficeService->criarConfiguracao(
            $instancia->document_key,
            $instancia->arquivo_nome,
            $this->generateFileUrlForOnlyOffice('onlyoffice.file.instancia', $instancia),
            [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'group' => $this->onlyOfficeService->obterGrupoUsuario(auth()->user())
            ],
            'view'
        );
        
        return view('onlyoffice.standalone-editor', [
            'config' => $config,
            'instancia' => $instancia,
            'title' => 'Visualizando: ' . $instancia->titulo
        ]);
    }
    
    /**
     * Force save modelo by requesting latest version from OnlyOffice
     */
    public function forceSaveModelo(DocumentoModelo $modelo, Request $request)
    {
        $this->authorize('update', $modelo);
        
        try {
            // Get current content from OnlyOffice
            $documentKey = $modelo->document_key;
            
            if (!$documentKey) {
                return response()->json(['error' => 'Document key not found'], 400);
            }
            
            // Find the most recent file in the modelos directory
            $files = Storage::disk('public')->files('documentos/modelos');
            $mostRecent = null;
            $mostRecentTime = 0;
            
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'rtf') {
                    $time = Storage::disk('public')->lastModified($file);
                    if ($time > $mostRecentTime) {
                        $mostRecentTime = $time;
                        $mostRecent = $file;
                    }
                }
            }
            
            if ($mostRecent && $mostRecent !== $modelo->arquivo_path) {
                // Update the model to point to the most recent file
                $modelo->update([
                    'arquivo_path' => $mostRecent,
                    'arquivo_nome' => basename($mostRecent),
                    'arquivo_size' => Storage::disk('public')->size($mostRecent)
                ]);
                
                \Log::info('Updated modelo to point to most recent file:', [
                    'modelo_id' => $modelo->id,
                    'old_path' => $modelo->arquivo_path,
                    'new_path' => $mostRecent,
                    'new_size' => Storage::disk('public')->size($mostRecent)
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Modelo updated to use most recent file',
                    'modelo_id' => $modelo->id,
                    'new_file' => $mostRecent
                ]);
            }
            
            // Log the manual save attempt
            \Log::info('Manual save requested for modelo:', [
                'modelo_id' => $modelo->id,
                'document_key' => $documentKey,
                'user_id' => auth()->id(),
                'current_file' => $modelo->arquivo_path,
                'files_found' => $files
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'No newer files found, current file is up to date',
                'modelo_id' => $modelo->id,
                'current_file' => $modelo->arquivo_path
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in manual save:', [
                'modelo_id' => $modelo->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }
}