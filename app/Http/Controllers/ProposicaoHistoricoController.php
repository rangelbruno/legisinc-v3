<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Models\ProposicaoHistorico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ProposicaoHistoricoController extends Controller
{
    /**
     * Exibir histórico completo de uma proposição
     */
    public function index(Proposicao $proposicao): JsonResponse
    {
        // Verificar permissões - apenas usuários autorizados podem ver histórico
        $this->authorize('view', $proposicao);
        
        $historico = ProposicaoHistorico::porProposicao($proposicao->id)
            ->with(['usuario:id,name,email'])
            ->orderBy('data_alteracao', 'desc')
            ->get();
            
        return response()->json([
            'proposicao' => [
                'id' => $proposicao->id,
                'tipo' => $proposicao->tipo,
                'ementa' => $proposicao->ementa,
                'status' => $proposicao->status,
                'autor' => $proposicao->autor->name ?? 'Desconhecido'
            ],
            'historico' => $historico->map(function ($item) {
                return [
                    'id' => $item->id,
                    'usuario' => $item->usuario?->name ?? 'Sistema',
                    'acao' => $item->acao,
                    'tipo_alteracao' => $item->tipo_alteracao,
                    'resumo' => $item->resumo,
                    'data_alteracao' => $item->data_alteracao->format('d/m/Y H:i:s'),
                    'origem' => $item->origem,
                    'diff_info' => $this->formatarDiffInfo($item),
                    'metadados' => $item->metadados
                ];
            }),
            'estatisticas' => $this->calcularEstatisticas($historico)
        ]);
    }

    /**
     * Exibir detalhes de uma alteração específica
     */
    public function show(Proposicao $proposicao, ProposicaoHistorico $historico): JsonResponse
    {
        $this->authorize('view', $proposicao);
        
        // Verificar se o histórico pertence à proposição
        if ($historico->proposicao_id !== $proposicao->id) {
            abort(404);
        }
        
        return response()->json([
            'historico' => [
                'id' => $historico->id,
                'usuario' => $historico->usuario?->name ?? 'Sistema',
                'acao' => $historico->acao,
                'tipo_alteracao' => $historico->tipo_alteracao,
                'data_alteracao' => $historico->data_alteracao->format('d/m/Y H:i:s'),
                'origem' => $historico->origem,
                'arquivo_anterior' => $historico->arquivo_path_anterior,
                'arquivo_novo' => $historico->arquivo_path_novo,
                'conteudo_anterior' => $historico->conteudo_anterior,
                'conteudo_novo' => $historico->conteudo_novo,
                'diff_conteudo' => $historico->diff_conteudo,
                'tamanho_anterior' => $historico->tamanho_anterior,
                'tamanho_novo' => $historico->tamanho_novo,
                'metadados' => $historico->metadados,
                'ip_usuario' => $historico->ip_usuario,
                'user_agent' => $historico->user_agent,
                'observacoes' => $historico->observacoes
            ]
        ]);
    }

    /**
     * Interface web para visualização do histórico
     */
    public function webView(Proposicao $proposicao)
    {
        $this->authorize('view', $proposicao);
        
        return view('proposicoes.historico', compact('proposicao'));
    }

    /**
     * Relatório de auditoria para administradores
     */
    public function relatorioAuditoria(Request $request): JsonResponse
    {
        // Apenas administradores podem ver relatório completo
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem gerar relatórios de auditoria.');
        }
        
        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'usuario_id' => 'nullable|exists:users,id',
            'origem' => 'nullable|in:onlyoffice,web,api,system',
            'limit' => 'nullable|integer|min:1|max:1000'
        ]);
        
        $query = ProposicaoHistorico::with(['usuario:id,name,email', 'proposicao:id,tipo,ementa']);
        
        // Filtros
        if ($request->data_inicio && $request->data_fim) {
            $query->porPeriodo(
                Carbon::parse($request->data_inicio)->startOfDay(),
                Carbon::parse($request->data_fim)->endOfDay()
            );
        }
        
        if ($request->usuario_id) {
            $query->porUsuario($request->usuario_id);
        }
        
        if ($request->origem) {
            $query->where('origem', $request->origem);
        }
        
        $historico = $query->orderBy('data_alteracao', 'desc')
            ->limit($request->limit ?? 100)
            ->get();
            
        return response()->json([
            'total_registros' => $historico->count(),
            'filtros_aplicados' => $request->only(['data_inicio', 'data_fim', 'usuario_id', 'origem']),
            'historico' => $historico->map(function ($item) {
                return [
                    'id' => $item->id,
                    'proposicao_id' => $item->proposicao_id,
                    'proposicao_tipo' => $item->proposicao?->tipo,
                    'proposicao_ementa' => $item->proposicao?->ementa,
                    'usuario' => $item->usuario?->name ?? 'Sistema',
                    'acao' => $item->acao,
                    'tipo_alteracao' => $item->tipo_alteracao,
                    'origem' => $item->origem,
                    'data_alteracao' => $item->data_alteracao->format('d/m/Y H:i:s'),
                    'ip_usuario' => $item->ip_usuario,
                    'resumo' => $item->resumo,
                    'diff_resumo' => $this->formatarDiffInfo($item)
                ];
            })
        ]);
    }

    /**
     * Formatar informações de diff para exibição
     */
    private function formatarDiffInfo(?ProposicaoHistorico $historico): ?array
    {
        if (!$historico || !$historico->diff_conteudo) {
            return null;
        }
        
        $diff = $historico->diff_conteudo;
        
        switch ($diff['tipo'] ?? '') {
            case 'criacao':
                return [
                    'tipo' => 'Criação',
                    'descricao' => "Adicionado {$diff['caracteres_adicionados']} caracteres",
                    'cor' => 'success'
                ];
                
            case 'edicao':
                $mudanca = $diff['diferenca_caracteres'] ?? 0;
                $sinal = $mudanca > 0 ? '+' : '';
                $similarity = $diff['similarity_percent'] ?? 0;
                
                return [
                    'tipo' => 'Edição',
                    'descricao' => "Alteração: {$sinal}{$mudanca} caracteres (Similaridade: {$similarity}%)",
                    'cor' => isset($diff['mudanca_significativa']) ? 'warning' : 'info',
                    'significativa' => isset($diff['mudanca_significativa'])
                ];
                
            case 'remocao':
                return [
                    'tipo' => 'Remoção',
                    'descricao' => "Removido {$diff['caracteres_removidos']} caracteres",
                    'cor' => 'danger'
                ];
                
            default:
                return [
                    'tipo' => 'Alteração',
                    'descricao' => 'Conteúdo modificado',
                    'cor' => 'secondary'
                ];
        }
    }

    /**
     * Calcular estatísticas do histórico
     */
    private function calcularEstatisticas($historico): array
    {
        $total = $historico->count();
        
        if ($total === 0) {
            return [
                'total_alteracoes' => 0,
                'usuarios_envolvidos' => 0,
                'periodo' => null,
                'origem_mais_comum' => null,
                'acao_mais_comum' => null
            ];
        }
        
        $usuarios = $historico->pluck('usuario_id')->filter()->unique()->count();
        $primeiro = $historico->last();
        $ultimo = $historico->first();
        
        $origens = $historico->groupBy('origem')->map->count()->sortDesc();
        $acoes = $historico->groupBy('acao')->map->count()->sortDesc();
        
        return [
            'total_alteracoes' => $total,
            'usuarios_envolvidos' => $usuarios,
            'periodo' => [
                'inicio' => $primeiro?->data_alteracao->format('d/m/Y H:i'),
                'fim' => $ultimo?->data_alteracao->format('d/m/Y H:i')
            ],
            'origem_mais_comum' => $origens->keys()->first(),
            'acao_mais_comum' => $acoes->keys()->first(),
            'distribuicao_origens' => $origens->toArray(),
            'distribuicao_acoes' => $acoes->toArray()
        ];
    }
}
