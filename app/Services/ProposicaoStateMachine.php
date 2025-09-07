<?php

namespace App\Services;

use App\Models\Proposicao;
use App\Models\ProposicaoStatusHistory;
use Illuminate\Support\Facades\Log;
use Exception;

class ProposicaoStateMachine
{
    /**
     * Transições válidas entre status
     * Cada status pode transicionar apenas para os listados em seu array
     */
    const TRANSICOES_VALIDAS = [
        'rascunho' => ['em_analise_legislativo'],
        'em_analise_legislativo' => ['aprovado_assinatura', 'rascunho'],
        'aprovado_assinatura' => ['assinado'],
        'assinado' => ['protocolado'], 
        'protocolado' => [] // Estado final
    ];

    /**
     * Descrições dos status para logs e UI
     */
    const DESCRICOES_STATUS = [
        'rascunho' => 'Em rascunho pelo parlamentar',
        'em_analise_legislativo' => 'Em análise pelo setor legislativo',
        'aprovado_assinatura' => 'Aprovado, aguardando assinatura',
        'assinado' => 'Assinado digitalmente',
        'protocolado' => 'Protocolado oficialmente'
    ];

    /**
     * Verifica se uma transição é permitida
     */
    public function podeTransicionar(string $statusAtual, string $novoStatus): bool
    {
        if (!isset(self::TRANSICOES_VALIDAS[$statusAtual])) {
            return false;
        }

        return in_array($novoStatus, self::TRANSICOES_VALIDAS[$statusAtual]);
    }

    /**
     * Executa transição de status com validações e auditoria
     */
    public function transicionar(Proposicao $proposicao, string $novoStatus, int $userId, ?string $observacoes = null): bool
    {
        $statusAnterior = $proposicao->status;

        // Validar transição
        if (!$this->podeTransicionar($statusAnterior, $novoStatus)) {
            throw new InvalidStatusTransitionException(
                "Transição inválida: '{$statusAnterior}' → '{$novoStatus}'"
            );
        }

        // Validações específicas por transição
        $this->validarPreCondicoes($proposicao, $statusAnterior, $novoStatus);

        Log::info('Executando transição de status', [
            'proposicao_id' => $proposicao->id,
            'status_anterior' => $statusAnterior,
            'status_novo' => $novoStatus,
            'user_id' => $userId
        ]);

        // Executar transição
        $proposicao->update(['status' => $novoStatus]);

        // Registrar no histórico
        $this->registrarHistorico($proposicao, $statusAnterior, $novoStatus, $userId, $observacoes);

        // Executar ações pós-transição
        $this->executarAcoesPosTransicao($proposicao, $statusAnterior, $novoStatus);

        // Métricas
        $this->incrementarMetrica('proposicao_status_transition', [
            'from' => $statusAnterior,
            'to' => $novoStatus
        ]);

        Log::info('Transição de status concluída', [
            'proposicao_id' => $proposicao->id,
            'status_novo' => $novoStatus
        ]);

        return true;
    }

    /**
     * Validações específicas antes da transição
     */
    private function validarPreCondicoes(Proposicao $proposicao, string $de, string $para): void
    {
        switch ($para) {
            case 'aprovado_assinatura':
                // Deve ter conteúdo aprovado pelo legislativo
                if (!$proposicao->arquivo_path || !file_exists(storage_path('app/' . $proposicao->arquivo_path))) {
                    throw new Exception('Proposição deve ter arquivo para ser aprovada para assinatura');
                }
                break;

            case 'assinado':
                // Deve ter PDF para assinatura
                if (!$proposicao->arquivo_pdf_para_assinatura) {
                    throw new Exception('PDF para assinatura deve estar disponível');
                }
                break;

            case 'protocolado':
                // Deve estar assinado e ter número
                if (!$proposicao->arquivo_pdf_assinado) {
                    throw new Exception('Proposição deve estar assinada para ser protocolada');
                }
                if (!$proposicao->numero) {
                    throw new Exception('Número de protocolo deve ser atribuído');
                }
                break;
        }
    }

    /**
     * Ações automáticas após mudança de status
     */
    private function executarAcoesPosTransicao(Proposicao $proposicao, string $de, string $para): void
    {
        switch ($para) {
            case 'aprovado_assinatura':
                // Invalidar PDF anterior para forçar regeneração
                $proposicao->update([
                    'arquivo_pdf_para_assinatura' => null,
                    'pdf_gerado_em' => null,
                    'pdf_conversor_usado' => null
                ]);
                
                // Disparar job para gerar PDF
                dispatch(new \App\Jobs\GerarPDFProposicaoJob($proposicao, 'para_assinatura'));
                break;

            case 'protocolado':
                // Disparar job para gerar PDF protocolado final
                dispatch(new \App\Jobs\GerarPDFProposicaoJob($proposicao, 'protocolado'));
                break;
        }
    }

    /**
     * Registra transição no histórico
     */
    private function registrarHistorico(Proposicao $proposicao, string $de, string $para, int $userId, ?string $observacoes): void
    {
        if (!class_exists('App\Models\ProposicaoStatusHistory')) {
            // Tabela ainda não foi criada, pular
            return;
        }

        ProposicaoStatusHistory::create([
            'proposicao_id' => $proposicao->id,
            'status_anterior' => $de,
            'status_novo' => $para,
            'user_id' => $userId,
            'observacoes' => $observacoes,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent()
        ]);
    }

    /**
     * Obter histórico de transições de uma proposição
     */
    public function getHistorico(Proposicao $proposicao): array
    {
        if (!class_exists('App\Models\ProposicaoStatusHistory')) {
            return [];
        }

        return ProposicaoStatusHistory::where('proposicao_id', $proposicao->id)
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Obter próximos status possíveis
     */
    public function getProximosStatus(string $statusAtual): array
    {
        return self::TRANSICOES_VALIDAS[$statusAtual] ?? [];
    }

    /**
     * Verificar se status é final
     */
    public function isStatusFinal(string $status): bool
    {
        return empty(self::TRANSICOES_VALIDAS[$status]);
    }

    /**
     * Obter estatísticas de transições
     */
    public function getEstatisticas(?int $dias = 30): array
    {
        if (!class_exists('App\Models\ProposicaoStatusHistory')) {
            return [];
        }

        $since = now()->subDays($dias);
        
        return [
            'total_transicoes' => ProposicaoStatusHistory::where('created_at', '>=', $since)->count(),
            'por_status' => ProposicaoStatusHistory::where('created_at', '>=', $since)
                ->selectRaw('status_novo, COUNT(*) as total')
                ->groupBy('status_novo')
                ->pluck('total', 'status_novo')
                ->toArray(),
            'por_transicao' => ProposicaoStatusHistory::where('created_at', '>=', $since)
                ->selectRaw('CONCAT(status_anterior, " → ", status_novo) as transicao, COUNT(*) as total')
                ->groupBy('transicao')
                ->pluck('total', 'transicao')
                ->toArray()
        ];
    }

    /**
     * Helper para métricas (placeholder)
     */
    private function incrementarMetrica(string $name, array $labels = []): void
    {
        // TODO: Implementar com Prometheus/StatsD quando disponível
        Log::info("Metric: {$name}", $labels);
    }
}

/**
 * Exception para transições inválidas
 */
class InvalidStatusTransitionException extends Exception
{
    //
}