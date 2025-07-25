<?php

namespace App\Http\Controllers\Documento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento\DocumentoModelo;
use App\Models\TipoProposicao;
use App\Models\Projeto;
use App\Services\Documento\DocumentoService;
use App\Services\Documento\VariavelService;
use App\Services\Documento\DocumentoModeloService;
use Illuminate\Support\Facades\Storage;

class DocumentoModeloController extends Controller
{
    public function __construct(
        private DocumentoService $documentoService,
        private VariavelService $variavelService,
        private DocumentoModeloService $documentoModeloService
    ) {}

    public function index(Request $request)
    {
        $query = DocumentoModelo::with(['tipoProposicao', 'creator']);
        
        // Filtro por categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        
        // Filtro por tipo de proposição
        if ($request->filled('tipo_proposicao_id')) {
            $query->where('tipo_proposicao_id', $request->tipo_proposicao_id);
        }
        
        // Filtro por template/personalizado
        if ($request->filled('is_template')) {
            $query->where('is_template', $request->is_template === 'true');
        }
        
        // Filtro por status
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === 'true');
        }
        
        // Busca por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhere('template_id', 'like', "%{$search}%");
            });
        }
        
        // Ordenação
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        
        if ($orderBy === 'categoria') {
            $query->orderBy('categoria', $orderDir)
                  ->orderBy('ordem')
                  ->orderBy('nome');
        } else {
            $query->orderBy($orderBy, $orderDir);
        }
        
        $modelos = $query->paginate(15)->appends($request->all());
        
        // Carregar dados para os filtros
        $categorias = DocumentoModelo::CATEGORIAS;
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        
        return view('documentos.modelos.index', compact('modelos', 'categorias', 'tiposProposicao'));
    }

    public function create()
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        
        return view('documentos.modelos.create', compact('tiposProposicao'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'arquivo' => 'required|file|mimes:docx|max:10240' // 10MB
        ]);

        try {
            $arquivo = $request->file('arquivo');
            
            $errorsValidacao = $this->variavelService->validarFormatoDocumento($arquivo);
            if (!empty($errorsValidacao)) {
                return back()->withErrors($errorsValidacao)->withInput();
            }

            $path = $arquivo->store('documentos/modelos');
            
            $variaveis = $this->variavelService->extrairVariaveisDeUpload($arquivo);
            
            $modelo = DocumentoModelo::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo_proposicao_id' => $request->tipo_proposicao_id,
                'arquivo_path' => $path,
                'arquivo_nome' => $arquivo->getClientOriginalName(),
                'arquivo_size' => $arquivo->getSize(),
                'variaveis' => $variaveis,
                'versao' => '1.0',
                'created_by' => auth()->id()
            ]);

            return redirect()->route('documentos.modelos.index')
                           ->with('success', 'Modelo criado com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao criar modelo de documento: ' . $e->getMessage());
            return back()->withErrors(['Erro interno do servidor'])->withInput();
        }
    }

    public function show(DocumentoModelo $modelo)
    {
        $modelo->load(['tipoProposicao', 'creator', 'instancias.projeto']);
        $variaveisFormatadas = $this->variavelService->formatarVariaveisParaExibicao($modelo->variaveis ?? []);
        
        return view('documentos.modelos.show', compact('modelo', 'variaveisFormatadas'));
    }

    public function edit(DocumentoModelo $modelo)
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        $variaveisFormatadas = $this->variavelService->formatarVariaveisParaExibicao($modelo->variaveis ?? []);
        
        return view('documentos.modelos.edit', compact('modelo', 'tiposProposicao', 'variaveisFormatadas'));
    }

    public function update(Request $request, DocumentoModelo $modelo)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'ativo' => 'boolean',
            'arquivo' => 'nullable|file|mimes:docx|max:10240'
        ]);

        try {
            $dadosAtualizacao = [
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'tipo_proposicao_id' => $request->tipo_proposicao_id,
                'ativo' => $request->boolean('ativo', true)
            ];

            if ($request->hasFile('arquivo')) {
                $arquivo = $request->file('arquivo');
                
                $errorsValidacao = $this->variavelService->validarFormatoDocumento($arquivo);
                if (!empty($errorsValidacao)) {
                    return back()->withErrors($errorsValidacao)->withInput();
                }

                if ($modelo->arquivo_path && \Storage::exists($modelo->arquivo_path)) {
                    \Storage::delete($modelo->arquivo_path);
                }

                $path = $arquivo->store('documentos/modelos');
                $variaveis = $this->variavelService->extrairVariaveisDeUpload($arquivo);
                
                $dadosAtualizacao = array_merge($dadosAtualizacao, [
                    'arquivo_path' => $path,
                    'arquivo_nome' => $arquivo->getClientOriginalName(),
                    'arquivo_size' => $arquivo->getSize(),
                    'variaveis' => $variaveis,
                    'versao' => $this->incrementarVersao($modelo->versao)
                ]);
            }

            $modelo->update($dadosAtualizacao);

            return redirect()->route('documentos.modelos.show', $modelo)
                           ->with('success', 'Modelo atualizado com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar modelo de documento: ' . $e->getMessage());
            return back()->withErrors(['Erro interno do servidor'])->withInput();
        }
    }

    public function destroy(DocumentoModelo $modelo)
    {
        $isAjax = request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest';
        \Log::info('Iniciando exclusão do modelo', [
            'modelo_id' => $modelo->id, 
            'is_ajax' => $isAjax,
            'headers' => request()->headers->all()
        ]);
        
        try {
            if ($modelo->instancias()->exists()) {
                \Log::warning('Modelo possui instâncias associadas', ['modelo_id' => $modelo->id]);
                
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este modelo possui documentos associados e não pode ser excluído.'
                    ], 422);
                }
                return back()->withErrors(['Este modelo possui documentos associados e não pode ser excluído.']);
            }

            if ($modelo->arquivo_path && Storage::exists($modelo->arquivo_path)) {
                \Log::info('Deletando arquivo do modelo', ['arquivo_path' => $modelo->arquivo_path]);
                Storage::delete($modelo->arquivo_path);
            }

            \Log::info('Deletando modelo do banco de dados', ['modelo_id' => $modelo->id]);
            $modelo->delete();
            
            \Log::info('Modelo excluído com sucesso', ['modelo_id' => $modelo->id]);

            if ($isAjax) {
                \Log::info('Retornando resposta AJAX de sucesso');
                return response()->json([
                    'success' => true,
                    'message' => 'Modelo excluído com sucesso!'
                ]);
            }

            return redirect()->route('documentos.modelos.index')
                           ->with('success', 'Modelo excluído com sucesso!');
                           
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir modelo de documento: ' . $e->getMessage(), [
                'modelo_id' => $modelo->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir modelo: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['Erro interno do servidor']);
        }
    }

    public function downloadModelo(DocumentoModelo $modelo, Projeto $projeto)
    {
        try {
            $instancia = $this->documentoService->criarInstanciaDocumento($projeto->id, $modelo->id);
            $caminhoArquivo = $this->documentoService->gerarDocumentoComVariaveis($instancia);
            
            return response()->download($caminhoArquivo, $instancia->arquivo_nome);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar documento: ' . $e->getMessage());
            return back()->withErrors(['Erro ao gerar documento: ' . $e->getMessage()]);
        }
    }

    public function download(DocumentoModelo $modelo)
    {
        try {
            // Log debug info
            \Log::info('DocumentoModeloController download requested:', [
                'modelo_id' => $modelo->id,
                'arquivo_path' => $modelo->arquivo_path,
                'arquivo_nome' => $modelo->arquivo_nome
            ]);
            
            // List all files in the directory for debugging
            $files = Storage::disk('public')->files('documentos/modelos');
            \Log::info('Files in modelos directory:', $files);
            
            if (!$modelo->arquivo_path || !Storage::disk('public')->exists($modelo->arquivo_path)) {
                \Log::warning('File not found, trying to fix path:', [
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
                    return back()->withErrors(['Arquivo modelo não encontrado.']);
                }
            }

            // Use Storage facade for public disk with cache-busting headers
            return Storage::disk('public')->response($modelo->arquivo_path, $modelo->arquivo_nome, [
                'Content-Type' => 'application/rtf',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Last-Modified' => $modelo->updated_at->format('D, d M Y H:i:s T'),
                'ETag' => '"' . md5($modelo->updated_at->timestamp . $modelo->id) . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao baixar modelo: ' . $e->getMessage(), [
                'modelo_id' => $modelo->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['Erro ao baixar arquivo.']);
        }
    }
    
    public function getLastUpdate(DocumentoModelo $modelo)
    {
        return response()->json([
            'id' => $modelo->id,
            'updated_at' => $modelo->updated_at->toISOString(),
            'timestamp' => $modelo->updated_at->timestamp,
            'arquivo_size' => $modelo->arquivo_size,
            'arquivo_nome' => $modelo->arquivo_nome,
            'document_key' => $modelo->document_key
        ]);
    }

    private function incrementarVersao(string $versaoAtual): string
    {
        $partes = explode('.', $versaoAtual);
        $major = (int) ($partes[0] ?? 1);
        $minor = (int) ($partes[1] ?? 0);
        
        $minor++;
        
        return $major . '.' . $minor;
    }
    
    // Métodos para integração ONLYOFFICE
    
    public function createOnlyOffice()
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        
        return view('documentos.modelos.create-onlyoffice', compact('tiposProposicao'));
    }
    
    public function storeOnlyOffice(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:255|unique:documento_modelos,nome',
                'descricao' => 'nullable|string|max:1000',
                'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
                'variaveis' => 'nullable|array',
                'variaveis.*' => 'string',
                'icon' => 'nullable|string|max:100'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        $modelo = $this->documentoModeloService->criarModelo($validated);
        
        // Se for request AJAX, retornar JSON com URL
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'modelo_id' => $modelo->id,
                'editor_url' => route('onlyoffice.standalone.editor.modelo', ['modelo' => $modelo->id]),
                'message' => 'Modelo criado com sucesso!'
            ]);
        }
        
        // Request normal, redirect
        return redirect()
            ->route('onlyoffice.standalone.editor.modelo', ['modelo' => $modelo->id])
            ->with('success', 'Modelo criado com sucesso! Agora você pode editá-lo online.');
    }
    
    public function editorOnlyOffice(DocumentoModelo $modelo)
    {
        return redirect()->route('onlyoffice.editor.modelo', $modelo);
    }
    
    public function duplicateOnlyOffice(DocumentoModelo $modelo)
    {
        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();
        
        return view('documentos.modelos.duplicate-onlyoffice', compact('modelo', 'tiposProposicao'));
    }
    
    public function storeDuplicateOnlyOffice(Request $request, DocumentoModelo $modelo)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:documento_modelos,nome',
            'descricao' => 'nullable|string|max:1000',
            'tipo_proposicao_id' => 'nullable|exists:tipo_proposicoes,id',
            'variaveis' => 'nullable|array',
            'variaveis.*' => 'string',
            'icon' => 'nullable|string|max:100'
        ]);
        
        $novoModelo = $this->documentoModeloService->duplicarModelo($modelo, $validated);
        
        return redirect()
            ->route('onlyoffice.editor.modelo', $novoModelo)
            ->with('success', 'Modelo duplicado com sucesso! Agora você pode editá-lo online.');
    }
    
    public function apiList(Request $request)
    {
        $tipoProposicaoId = $request->get('tipo_proposicao_id');
        
        $modelos = $this->documentoModeloService->obterModelosDisponiveis($tipoProposicaoId);
        
        return response()->json([
            'modelos' => $modelos->map(function($modelo) {
                return [
                    'id' => $modelo->id,
                    'nome' => $modelo->nome,
                    'descricao' => $modelo->descricao ?? '',
                    'icon' => $modelo->icon ?? 'ki-duotone ki-document',
                    'variaveis' => $modelo->variaveis ?? [],
                    'is_template' => $modelo->is_template ?? false,
                    'template_id' => $modelo->template_id ?? null
                ];
            })
        ]);
    }
}
