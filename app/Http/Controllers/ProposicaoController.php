<?php

namespace App\Http\Controllers;

use App\Models\TipoProposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposicaoController extends Controller
{
    /**
     * Tela inicial para criação de proposição (Parlamentar)
     */
    public function create()
    {
        // Buscar tipos ativos do banco de dados
        $tipos = TipoProposicao::getParaDropdown();
        
        return view('proposicoes.create', compact('tipos'));
    }

    /**
     * Salvar dados básicos da proposição como rascunho
     */
    public function salvarRascunho(Request $request)
    {
        // Validar se o tipo existe e está ativo
        try {
            $tiposValidos = TipoProposicao::ativos()->pluck('codigo')->toArray();
        } catch (\Exception $e) {
            // Fallback para tipos padrão
            $tiposValidos = array_keys(TipoProposicao::getTiposPadrao());
        }
        
        $request->validate([
            'tipo' => 'required|in:' . implode(',', $tiposValidos),
            'ementa' => 'required|string|max:1000',
        ]);

        // TODO: Create proper Proposicao model and database table
        $proposicao = (object) [
            'id' => rand(1000, 9999), // Temporary ID for demo
            'tipo' => $request->tipo,
            'ementa' => $request->ementa,
            'autor_id' => Auth::id(),
            'status' => 'rascunho',
            'ano' => date('Y'),
        ];

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
        // Verificar se o tipo existe
        $tipoProposicao = TipoProposicao::buscarPorCodigo($tipo);
        
        if (!$tipoProposicao || !$tipoProposicao->ativo) {
            return response()->json([], 404);
        }
        
        // TODO: Implement proper model search for proposições
        // When Proposicao model is created, this should query the models table
        $modelos = collect([
            (object) [
                'id' => 1, 
                'nome' => 'Modelo Padrão', 
                'descricao' => 'Modelo padrão para ' . $tipoProposicao->nome,
                'template_padrao' => $tipoProposicao->template_padrao
            ]
        ]);

        return response()->json($modelos);
    }

    /**
     * Tela de preenchimento do modelo selecionado
     */
    public function preencherModelo($proposicaoId, $modeloId)
    {
        // TODO: Implement proper authorization and model loading
        // $this->authorize('update', $proposicao);
        
        $proposicao = (object) ['id' => $proposicaoId]; // Temporary
        $modelo = (object) ['id' => $modeloId, 'nome' => 'Modelo Temporário']; // Temporary
        
        return view('proposicoes.preencher-modelo', compact('proposicao', 'modelo'));
    }

    /**
     * Gerar texto editável baseado no modelo preenchido
     */
    public function gerarTexto(Request $request, $proposicaoId)
    {
        // TODO: Implement proper authorization
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo_modelo' => 'required|array',
            'modelo_id' => 'required|integer'
        ]);

        // TODO: Implement proper template processing
        $textoGerado = "Texto gerado automaticamente baseado no modelo selecionado.\n\n";
        $textoGerado .= "Dados preenchidos:\n";
        foreach ($request->conteudo_modelo as $campo => $valor) {
            $textoGerado .= "- {$campo}: {$valor}\n";
        }

        return response()->json([
            'success' => true,
            'texto_gerado' => $textoGerado,
            'message' => 'Texto gerado com sucesso!'
        ]);
    }

    /**
     * Tela de edição final do documento
     */
    public function editarTexto($proposicaoId)
    {
        // TODO: Implement proper authorization and model loading
        // $this->authorize('update', $proposicao);
        
        $proposicao = (object) [
            'id' => $proposicaoId,
            'conteudo' => 'Conteúdo temporário da proposição...'
        ];
        
        return view('proposicoes.editar-texto', compact('proposicao'));
    }

    /**
     * Salvar alterações no texto
     */
    public function salvarTexto(Request $request, $proposicaoId)
    {
        // TODO: Implement proper authorization and model update
        // $this->authorize('update', $proposicao);
        
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        // TODO: Update proposicao in database
        // $proposicao->update(['conteudo' => $request->conteudo]);

        return response()->json([
            'success' => true,
            'message' => 'Texto salvo com sucesso!'
        ]);
    }

    /**
     * Enviar proposição para análise do legislativo
     */
    public function enviarLegislativo($proposicaoId)
    {
        // TODO: Implement proper authorization and validation
        // $this->authorize('update', $proposicao);
        
        // TODO: Validate proposicao has content
        // if (!$proposicao->hasContent()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Proposição deve ter conteúdo antes de ser enviada.'
        //     ], 400);
        // }

        // TODO: Update status and add tramitacao
        // $proposicao->update(['status' => 'enviado_legislativo']);
        // $proposicao->adicionarTramitacao(...);

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
        // TODO: Query actual proposições from database
        $proposicoes = collect([
            (object) [
                'id' => 1,
                'tipo' => 'projeto_lei_ordinaria',
                'ementa' => 'Exemplo de proposição',
                'status' => 'rascunho',
                'created_at' => now(),
                'autor' => Auth::user()
            ]
        ]);

        // For now, create a mock paginator
        $proposicoes = new \Illuminate\Pagination\LengthAwarePaginator(
            $proposicoes,
            $proposicoes->count(),
            15,
            1,
            ['path' => request()->url()]
        );

        return view('proposicoes.minhas-proposicoes', compact('proposicoes'));
    }

    /**
     * Visualizar proposição
     */
    public function show($proposicaoId)
    {
        // TODO: Implement proper authorization and model loading
        // $this->authorize('view', $proposicao);
        
        $proposicao = (object) [
            'id' => $proposicaoId,
            'tipo' => 'projeto_lei_ordinaria',
            'ementa' => 'Exemplo de proposição',
            'status' => 'rascunho',
            'conteudo' => 'Conteúdo da proposição...',
            'autor' => Auth::user(),
            'created_at' => now()
        ];
        
        return view('proposicoes.show', compact('proposicao'));
    }

    /**
     * Processar template do modelo com os dados preenchidos
     */
    private function processarTemplate($modelo, array $dados): string
    {
        // TODO: Implement proper template processing
        $template = $modelo->template ?? 'Template padrão: {{conteudo}}';
        
        // Substituir placeholders pelos dados preenchidos
        foreach ($dados as $campo => $valor) {
            $placeholder = "{{" . $campo . "}}";
            $template = str_replace($placeholder, $valor, $template);
        }
        
        return $template;
    }
}