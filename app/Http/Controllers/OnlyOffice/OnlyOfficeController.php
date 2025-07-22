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
        $this->authorize('view', $modelo);
        
        if (!$modelo->arquivo_path || !Storage::exists($modelo->arquivo_path)) {
            // Criar arquivo vazio se não existir
            $this->criarArquivoVazio($modelo);
        }
        
        return Storage::response($modelo->arquivo_path, $modelo->arquivo_nome, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }
    
    public function downloadInstancia(DocumentoInstancia $instancia)
    {
        $this->authorize('view', $instancia);
        
        if (!$instancia->arquivo_path || !Storage::exists($instancia->arquivo_path)) {
            abort(404, 'Arquivo não encontrado');
        }
        
        return Storage::response($instancia->arquivo_path, $instancia->arquivo_nome, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
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
            Storage::copy($templatePath, $destinoPath);
        } else {
            // Criar documento Word básico
            $conteudoBase = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:body>
        <w:p>
            <w:r>
                <w:t>Modelo: ' . $modelo->nome . '</w:t>
            </w:r>
        </w:p>
    </w:body>
</w:document>';
            
            Storage::put($destinoPath, $conteudoBase);
        }
        
        $modelo->update([
            'arquivo_path' => $destinoPath,
            'arquivo_size' => Storage::size($destinoPath)
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
}