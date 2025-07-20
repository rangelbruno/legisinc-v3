<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\ModeloProjeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoController extends Controller
{
    /**
     * Tela inicial para criação de proposição (Parlamentar)
     */
    public function create()
    {
        $tipos = Projeto::TIPOS;
        
        return view('proposicoes.create', compact('tipos'));
    }

    /**
     * Salvar dados básicos da proposição como rascunho
     */
    public function salvarRascunho(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:' . implode(',', array_keys(Projeto::TIPOS)),
            'ementa' => 'required|string|max:1000',
        ]);

        $proposicao = Projeto::create([
            'tipo' => $request->tipo,
            'ementa' => $request->ementa,
            'autor_id' => Auth::id(),
            'status' => 'rascunho',
            'ano' => date('Y'),
        ]);

        return response()->json([
            'success' => true,
            'proposicao_id' => $proposicao->id,
            'message' => 'Rascunho salvo com sucesso!'
        ]);
    }

    /**
     * Buscar modelos baseados no tipo de proposição
     */
    public function buscarModelos($tipo)
    {
        $modelos = ModeloProjeto::where('tipo_projeto', $tipo)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'descricao']);

        return response()->json($modelos);
    }

    /**
     * Tela de preenchimento do modelo selecionado
     */
    public function preencherModelo(Projeto $proposicao, $modeloId)
    {
        $this->authorize('update', $proposicao);
        
        $modelo = ModeloProjeto::findOrFail($modeloId);
        
        return view('proposicoes.preencher-modelo', compact('proposicao', 'modelo'));
    }

    /**
     * Gerar texto editável baseado no modelo preenchido
     */
    public function gerarTexto(Request $request, Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo_modelo' => 'required|array',
            'modelo_id' => 'required|exists:modelo_projetos,id'
        ]);

        $modelo = ModeloProjeto::findOrFail($request->modelo_id);
        
        // Gerar texto baseado no template do modelo
        $textoGerado = $this->processarTemplate($modelo, $request->conteudo_modelo);
        
        $proposicao->update([
            'conteudo_modelo' => $request->conteudo_modelo,
            'conteudo' => $textoGerado,
            'status' => 'em_elaboracao'
        ]);

        return response()->json([
            'success' => true,
            'texto_gerado' => $textoGerado,
            'message' => 'Texto gerado com sucesso!'
        ]);
    }

    /**
     * Tela de edição final do documento
     */
    public function editarTexto(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        return view('proposicoes.editar-texto', compact('proposicao'));
    }

    /**
     * Salvar alterações no texto
     */
    public function salvarTexto(Request $request, Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        $proposicao->update([
            'conteudo' => $request->conteudo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Texto salvo com sucesso!'
        ]);
    }

    /**
     * Enviar proposição para análise do legislativo
     */
    public function enviarLegislativo(Projeto $proposicao)
    {
        $this->authorize('update', $proposicao);
        
        if (!$proposicao->hasContent()) {
            return response()->json([
                'success' => false,
                'message' => 'Proposição deve ter conteúdo antes de ser enviada.'
            ], 400);
        }

        $proposicao->update([
            'status' => 'enviado_legislativo'
        ]);

        $proposicao->adicionarTramitacao(
            'Enviado para análise legislativa',
            'em_elaboracao',
            'enviado_legislativo'
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposição enviada para análise legislativa!'
        ]);
    }

    /**
     * Listagem das próprias proposições (Parlamentar)
     */
    public function minhasProposicoes()
    {
        $proposicoes = Projeto::where('autor_id', Auth::id())
            ->with(['autor', 'revisor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('proposicoes.minhas-proposicoes', compact('proposicoes'));
    }

    /**
     * Visualizar proposição
     */
    public function show(Projeto $proposicao)
    {
        $this->authorize('view', $proposicao);
        
        $proposicao->load(['autor', 'revisor', 'funcionarioProtocolo', 'tramitacao']);
        
        return view('proposicoes.show', compact('proposicao'));
    }

    /**
     * Processar template do modelo com os dados preenchidos
     */
    private function processarTemplate(ModeloProjeto $modelo, array $dados): string
    {
        $template = $modelo->template ?? '';
        
        // Substituir placeholders pelos dados preenchidos
        foreach ($dados as $campo => $valor) {
            $placeholder = "{{" . $campo . "}}";
            $template = str_replace($placeholder, $valor, $template);
        }
        
        return $template;
    }
}