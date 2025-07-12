<?php

namespace App\Services\Projeto;

use App\Models\Projeto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProjetoWorkflowService
{
    /**
     * Envia projeto para análise do legislativo
     */
    public function enviarParaAnalise(Projeto $projeto, string $observacoes = null): bool
    {
        if (!$projeto->podeSerEnviado()) {
            throw new Exception('Projeto não pode ser enviado para análise. Status atual: ' . $projeto->status);
        }

        try {
            DB::transaction(function () use ($projeto, $observacoes) {
                // Atualizar status do projeto
                $statusAnterior = $projeto->status;
                $projeto->update(['status' => 'enviado']);

                // Registrar tramitação
                $projeto->adicionarTramitacao(
                    acao: 'enviou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'enviado',
                    observacoes: $observacoes
                );
            });

            Log::info('Projeto enviado para análise', [
                'projeto_id' => $projeto->id,
                'usuario_id' => auth()->id(),
                'observacoes' => $observacoes
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao enviar projeto para análise', [
                'projeto_id' => $projeto->id,
                'usuario_id' => auth()->id(),
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Inicia análise do projeto pelo legislativo
     */
    public function iniciarAnalise(Projeto $projeto, string $observacoes = null): bool
    {
        if ($projeto->status !== 'enviado') {
            throw new Exception('Projeto deve estar com status "enviado" para iniciar análise');
        }

        try {
            DB::transaction(function () use ($projeto, $observacoes) {
                $statusAnterior = $projeto->status;
                $projeto->update(['status' => 'em_analise']);

                $projeto->adicionarTramitacao(
                    acao: 'analisou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'em_analise',
                    observacoes: $observacoes
                );
            });

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao iniciar análise do projeto', [
                'projeto_id' => $projeto->id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Aprova o projeto após análise
     */
    public function aprovarProjeto(Projeto $projeto, string $observacoes = null): bool
    {
        if ($projeto->status !== 'em_analise') {
            throw new Exception('Projeto deve estar em análise para ser aprovado');
        }

        try {
            DB::transaction(function () use ($projeto, $observacoes) {
                $statusAnterior = $projeto->status;
                $projeto->update(['status' => 'aprovado']);

                $projeto->adicionarTramitacao(
                    acao: 'aprovou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'aprovado',
                    observacoes: $observacoes
                );
            });

            Log::info('Projeto aprovado', [
                'projeto_id' => $projeto->id,
                'usuario_id' => auth()->id()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao aprovar projeto', [
                'projeto_id' => $projeto->id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Rejeita o projeto após análise
     */
    public function rejeitarProjeto(Projeto $projeto, string $motivo): bool
    {
        if ($projeto->status !== 'em_analise') {
            throw new Exception('Projeto deve estar em análise para ser rejeitado');
        }

        if (empty($motivo)) {
            throw new Exception('Motivo da rejeição é obrigatório');
        }

        try {
            DB::transaction(function () use ($projeto, $motivo) {
                $statusAnterior = $projeto->status;
                $projeto->update(['status' => 'rejeitado']);

                $projeto->adicionarTramitacao(
                    acao: 'rejeitou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'rejeitado',
                    observacoes: $motivo
                );
            });

            Log::info('Projeto rejeitado', [
                'projeto_id' => $projeto->id,
                'usuario_id' => auth()->id(),
                'motivo' => $motivo
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao rejeitar projeto', [
                'projeto_id' => $projeto->id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Assina o projeto aprovado
     */
    public function assinarProjeto(Projeto $projeto, string $observacoes = null): bool
    {
        if (!$projeto->podeSerAssinado()) {
            throw new Exception('Projeto não pode ser assinado. Status atual: ' . $projeto->status);
        }

        try {
            DB::transaction(function () use ($projeto, $observacoes) {
                $statusAnterior = $projeto->status;
                $projeto->update([
                    'status' => 'assinado',
                    'data_assinatura' => now()
                ]);

                $projeto->adicionarTramitacao(
                    acao: 'assinou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'assinado',
                    observacoes: $observacoes
                );
            });

            Log::info('Projeto assinado', [
                'projeto_id' => $projeto->id,
                'usuario_id' => auth()->id()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao assinar projeto', [
                'projeto_id' => $projeto->id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Protocola o projeto assinado
     */
    public function protocolarProjeto(Projeto $projeto, string $numeroProtocolo, string $observacoes = null): bool
    {
        if (!$projeto->podeSerProtocolado()) {
            throw new Exception('Projeto não pode ser protocolado. Status atual: ' . $projeto->status);
        }

        if (empty($numeroProtocolo)) {
            throw new Exception('Número do protocolo é obrigatório');
        }

        // Verificar se o número já existe
        $existeProtocolo = Projeto::where('numero_protocolo', $numeroProtocolo)
            ->where('id', '!=', $projeto->id)
            ->exists();

        if ($existeProtocolo) {
            throw new Exception('Número de protocolo já existe: ' . $numeroProtocolo);
        }

        try {
            DB::transaction(function () use ($projeto, $numeroProtocolo, $observacoes) {
                $statusAnterior = $projeto->status;
                $projeto->update([
                    'status' => 'protocolado',
                    'numero_protocolo' => $numeroProtocolo,
                    'data_protocolo' => now()
                ]);

                $projeto->adicionarTramitacao(
                    acao: 'protocolou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'protocolado',
                    observacoes: $observacoes ?: "Protocolo nº: {$numeroProtocolo}"
                );
            });

            Log::info('Projeto protocolado', [
                'projeto_id' => $projeto->id,
                'numero_protocolo' => $numeroProtocolo,
                'usuario_id' => auth()->id()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao protocolar projeto', [
                'projeto_id' => $projeto->id,
                'numero_protocolo' => $numeroProtocolo,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Inclui projeto em sessão
     */
    public function incluirEmSessao(Projeto $projeto, string $observacoes = null): bool
    {
        if ($projeto->status !== 'protocolado') {
            throw new Exception('Projeto deve estar protocolado para ser incluído em sessão');
        }

        try {
            DB::transaction(function () use ($projeto, $observacoes) {
                $statusAnterior = $projeto->status;
                $projeto->update(['status' => 'em_sessao']);

                $projeto->adicionarTramitacao(
                    acao: 'incluiu_sessao',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'em_sessao',
                    observacoes: $observacoes
                );
            });

            Log::info('Projeto incluído em sessão', [
                'projeto_id' => $projeto->id,
                'usuario_id' => auth()->id()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao incluir projeto em sessão', [
                'projeto_id' => $projeto->id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Registra votação do projeto
     */
    public function votarProjeto(Projeto $projeto, string $resultadoVotacao, string $observacoes = null): bool
    {
        if ($projeto->status !== 'em_sessao') {
            throw new Exception('Projeto deve estar em sessão para ser votado');
        }

        try {
            DB::transaction(function () use ($projeto, $resultadoVotacao, $observacoes) {
                $statusAnterior = $projeto->status;
                $projeto->update(['status' => 'votado']);

                $observacaoCompleta = "Resultado da votação: {$resultadoVotacao}";
                if ($observacoes) {
                    $observacaoCompleta .= " - {$observacoes}";
                }

                $projeto->adicionarTramitacao(
                    acao: 'votou',
                    statusAnterior: $statusAnterior,
                    statusAtual: 'votado',
                    observacoes: $observacaoCompleta
                );
            });

            Log::info('Projeto votado', [
                'projeto_id' => $projeto->id,
                'resultado' => $resultadoVotacao,
                'usuario_id' => auth()->id()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao votar projeto', [
                'projeto_id' => $projeto->id,
                'erro' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Retorna o histórico completo de tramitação
     */
    public function getHistoricoTramitacao(Projeto $projeto): array
    {
        return $projeto->tramitacao()
            ->with('usuario')
            ->orderBy('created_at')
            ->get()
            ->map(function ($tramitacao) {
                return [
                    'id' => $tramitacao->id,
                    'data' => $tramitacao->data_formatada,
                    'usuario' => $tramitacao->usuario->name,
                    'acao' => $tramitacao->acao_formatada,
                    'status_anterior' => $tramitacao->status_anterior_formatado,
                    'status_atual' => $tramitacao->status_atual_formatado,
                    'observacoes' => $tramitacao->observacoes,
                    'descricao_completa' => $tramitacao->getDescricaoCompleta(),
                ];
            })
            ->toArray();
    }

    /**
     * Obtém próximas ações possíveis para o projeto
     */
    public function getProximasAcoes(Projeto $projeto, User $usuario): array
    {
        $acoes = [];

        switch ($projeto->status) {
            case 'rascunho':
                if ($usuario->can('projeto.edit_own') && $projeto->autor_id === $usuario->id) {
                    $acoes[] = [
                        'acao' => 'enviar_para_analise',
                        'label' => 'Enviar para Análise',
                        'classe' => 'btn-primary',
                        'requer_confirmacao' => true
                    ];
                }
                break;

            case 'enviado':
                if ($usuario->can('projeto.analyze')) {
                    $acoes[] = [
                        'acao' => 'iniciar_analise',
                        'label' => 'Iniciar Análise',
                        'classe' => 'btn-info',
                        'requer_confirmacao' => false
                    ];
                }
                break;

            case 'em_analise':
                if ($usuario->can('projeto.approve')) {
                    $acoes[] = [
                        'acao' => 'aprovar',
                        'label' => 'Aprovar',
                        'classe' => 'btn-success',
                        'requer_confirmacao' => true
                    ];
                }
                if ($usuario->can('projeto.reject')) {
                    $acoes[] = [
                        'acao' => 'rejeitar',
                        'label' => 'Rejeitar',
                        'classe' => 'btn-danger',
                        'requer_confirmacao' => true,
                        'requer_motivo' => true
                    ];
                }
                break;

            case 'aprovado':
                if ($usuario->can('projeto.sign') && $projeto->autor_id === $usuario->id) {
                    $acoes[] = [
                        'acao' => 'assinar',
                        'label' => 'Assinar',
                        'classe' => 'btn-warning',
                        'requer_confirmacao' => true
                    ];
                }
                break;

            case 'assinado':
                if ($usuario->can('projeto.assign_number')) {
                    $acoes[] = [
                        'acao' => 'protocolar',
                        'label' => 'Protocolar',
                        'classe' => 'btn-info',
                        'requer_confirmacao' => true,
                        'requer_numero_protocolo' => true
                    ];
                }
                break;

            case 'protocolado':
                if ($usuario->can('projeto.include_session')) {
                    $acoes[] = [
                        'acao' => 'incluir_sessao',
                        'label' => 'Incluir em Sessão',
                        'classe' => 'btn-primary',
                        'requer_confirmacao' => true
                    ];
                }
                break;

            case 'em_sessao':
                if ($usuario->can('tramitacao.manage')) {
                    $acoes[] = [
                        'acao' => 'votar',
                        'label' => 'Registrar Votação',
                        'classe' => 'btn-success',
                        'requer_confirmacao' => true,
                        'requer_resultado_votacao' => true
                    ];
                }
                break;
        }

        return $acoes;
    }

    /**
     * Gera número de protocolo automático
     */
    public function gerarNumeroProtocolo(): string
    {
        $ano = date('Y');
        $ultimoNumero = Projeto::whereYear('data_protocolo', $ano)
            ->whereNotNull('numero_protocolo')
            ->count();

        $proximoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$proximoNumero}/{$ano}";
    }
}