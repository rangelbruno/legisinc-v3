<?php

namespace App\Http\Controllers\Documento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Documento\DocumentoModelo;
use App\Models\Documento\DocumentoInstancia;
use App\Models\TipoProposicao;
use App\Models\Projeto;
use App\Services\Documento\DocumentoService;
use App\Services\Documento\VariavelService;

class DocumentoEditorController extends Controller
{
    public function __construct(
        private DocumentoService $documentoService,
        private VariavelService $variavelService
    ) {}

    public function index()
    {
        $modelos = DocumentoModelo::ativos()
            ->with(['tipoProposicao'])
            ->orderBy('nome')
            ->get();

        $tiposProposicao = TipoProposicao::ativos()->ordenados()->get();

        return view('documentos.editor.index', compact('modelos', 'tiposProposicao'));
    }

    public function create(Request $request)
    {
        $modeloId = $request->get('modelo_id');
        $projetoId = $request->get('projeto_id');

        $modelo = null;
        $projeto = null;
        $variaveisData = [];

        if ($modeloId) {
            $modelo = DocumentoModelo::findOrFail($modeloId);
            $variaveisData = $this->variavelService->obterVariaveisComValoresPadrao($modelo->variaveis ?? []);
        }

        if ($projetoId) {
            $projeto = Projeto::findOrFail($projetoId);
            if ($modelo) {
                $variaveisData = $this->variavelService->preencherVariaveisComDadosProjeto($modelo->variaveis ?? [], $projeto);
            }
        }

        $modelos = DocumentoModelo::ativos()
            ->with(['tipoProposicao'])
            ->orderBy('nome')
            ->get();

        $projetos = Projeto::orderBy('created_at', 'desc')->limit(50)->get();

        return view('documentos.editor.create-simple', compact('modelo', 'projeto', 'variaveisData', 'modelos', 'projetos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modelo_id' => 'nullable|exists:documento_modelos,id',
            'projeto_id' => 'nullable|exists:projetos,id',
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'variaveis' => 'nullable|array',
            'formato_exportacao' => 'required|in:docx,pdf'
        ]);

        try {
            $modelo = null;
            if ($request->modelo_id) {
                $modelo = DocumentoModelo::findOrFail($request->modelo_id);
            }
            
            // Criar instância do documento
            $instancia = DocumentoInstancia::create([
                'projeto_id' => $request->projeto_id,
                'modelo_id' => $request->modelo_id,
                'titulo' => $request->titulo,
                'conteudo_personalizado' => $request->conteudo,
                'variaveis_personalizadas' => $request->variaveis ?? [],
                'status' => 'rascunho',
                'created_by' => auth()->id()
            ]);

            // Gerar arquivo baseado no conteúdo do editor
            $caminhoArquivo = $this->documentoService->gerarDocumentoDoEditor(
                $instancia,
                $request->conteudo,
                $request->variaveis ?? [],
                $request->formato_exportacao
            );

            return response()->json([
                'success' => true,
                'message' => 'Documento gerado com sucesso!',
                'download_url' => route('documentos.editor.download', $instancia->id),
                'instancia_id' => $instancia->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar documento do editor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar documento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download(DocumentoInstancia $instancia)
    {
        try {
            if (!$instancia->arquivo_gerado_path || !file_exists(storage_path('app/' . $instancia->arquivo_gerado_path))) {
                return back()->withErrors(['Arquivo não encontrado.']);
            }

            return response()->download(
                storage_path('app/' . $instancia->arquivo_gerado_path),
                $instancia->arquivo_gerado_nome ?? 'documento.docx'
            );

        } catch (\Exception $e) {
            \Log::error('Erro ao baixar documento: ' . $e->getMessage());
            return back()->withErrors(['Erro ao baixar arquivo.']);
        }
    }

    public function getVariaveisModelo($modelo_id)
    {
        try {
            $modelo = DocumentoModelo::findOrFail($modelo_id);
            $variaveis = $this->variavelService->obterVariaveisComValoresPadrao($modelo->variaveis ?? []);
            
            return response()->json([
                'success' => true,
                'variaveis' => $variaveis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar variáveis do modelo.'
            ], 500);
        }
    }

    public function preencherVariaveisProjeto(Request $request)
    {
        $request->validate([
            'modelo_id' => 'required|exists:documento_modelos,id',
            'projeto_id' => 'required|exists:projetos,id'
        ]);

        try {
            $modelo = DocumentoModelo::findOrFail($request->modelo_id);
            $projeto = Projeto::findOrFail($request->projeto_id);
            
            $variaveis = $this->variavelService->preencherVariaveisComDadosProjeto(
                $modelo->variaveis ?? [],
                $projeto
            );
            
            return response()->json([
                'success' => true,
                'variaveis' => $variaveis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao preencher variáveis com dados do projeto.'
            ], 500);
        }
    }
}