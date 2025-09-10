<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\WorkflowEtapa;
use App\Models\WorkflowTransicao;
use App\Models\DocumentoWorkflowStatus;
use App\Models\DocumentoWorkflowHistorico;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class WorkflowTransitionService
{
    /**
     * Executa uma transição de workflow para um documento
     */
    public function executarTransicao(
        Model $documento,
        WorkflowTransicao $transicao,
        array $dadosAdicionais = []
    ): bool {
        try {
            DB::beginTransaction();

            // Validar se a transição pode ser executada
            if (!$this->podeExecutarTransicao($documento, $transicao)) {
                throw new Exception('Transição não pode ser executada no estado atual do documento');
            }

            // Obter status atual do documento
            $statusAtual = $this->obterStatusDocumento($documento);
            
            // Executar validação de condições
            if (!$this->validarCondicoes($documento, $transicao, $dadosAdicionais)) {
                throw new Exception('Condições da transição não foram atendidas');
            }

            // Executar a transição
            $novoStatus = $this->criarNovoStatus($documento, $transicao, $statusAtual);
            
            // Registrar no histórico
            $this->registrarHistorico($documento, $transicao, $statusAtual, $novoStatus, $dadosAdicionais);

            // Executar ações pós-transição
            $this->executarAcoesPosTransicao($documento, $transicao, $dadosAdicionais);

            DB::commit();
            
            Log::info("Transição executada com sucesso", [
                'documento_id' => $documento->id,
                'documento_type' => get_class($documento),
                'transicao_id' => $transicao->id,
                'etapa_origem' => $statusAtual->etapa->nome,
                'etapa_destino' => $novoStatus->etapa->nome,
                'usuario_id' => Auth::id()
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error("Erro ao executar transição", [
                'documento_id' => $documento->id,
                'documento_type' => get_class($documento),
                'transicao_id' => $transicao->id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);

            throw $e;
        }
    }

    /**
     * Verifica se uma transição pode ser executada
     */
    public function podeExecutarTransicao(Model $documento, WorkflowTransicao $transicao): bool
    {
        $statusAtual = $this->obterStatusDocumento($documento);
        
        if (!$statusAtual) {
            return false;
        }

        // Verificar se a etapa origem da transição corresponde à etapa atual
        if ($statusAtual->workflow_etapa_id !== $transicao->etapa_origem_id) {
            return false;
        }

        // Verificar permissões do usuário
        if (!$this->temPermissaoParaTransicao($documento, $transicao)) {
            return false;
        }

        // Verificar se o documento não está bloqueado
        if ($statusAtual->bloqueado) {
            return false;
        }

        return true;
    }

    /**
     * Obtém todas as transições disponíveis para um documento no estado atual
     */
    public function obterTransicoesDisponiveis(Model $documento): array
    {
        $statusAtual = $this->obterStatusDocumento($documento);
        
        if (!$statusAtual) {
            return [];
        }

        $transicoes = WorkflowTransicao::where('workflow_id', $statusAtual->workflow_id)
            ->where('etapa_origem_id', $statusAtual->workflow_etapa_id)
            ->with(['etapaOrigem', 'etapaDestino'])
            ->get();

        return $transicoes->filter(function ($transicao) use ($documento) {
            return $this->podeExecutarTransicao($documento, $transicao);
        })->toArray();
    }

    /**
     * Executa transições automáticas para um documento
     */
    public function processarTransicoesAutomaticas(Model $documento): bool
    {
        $statusAtual = $this->obterStatusDocumento($documento);
        
        if (!$statusAtual) {
            return false;
        }

        $transicoes = WorkflowTransicao::where('workflow_id', $statusAtual->workflow_id)
            ->where('etapa_origem_id', $statusAtual->workflow_etapa_id)
            ->where('automatica', true)
            ->with(['etapaOrigem', 'etapaDestino'])
            ->get();

        $executouAlguma = false;

        foreach ($transicoes as $transicao) {
            if ($this->podeExecutarTransicao($documento, $transicao)) {
                try {
                    $this->executarTransicao($documento, $transicao);
                    $executouAlguma = true;
                    
                    // Processar recursivamente para transições automáticas em cadeia
                    $this->processarTransicoesAutomaticas($documento);
                    break; // Executar apenas uma transição automática por vez
                    
                } catch (Exception $e) {
                    Log::warning("Falha ao executar transição automática", [
                        'documento_id' => $documento->id,
                        'transicao_id' => $transicao->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $executouAlguma;
    }

    /**
     * Valida as condições de uma transição
     */
    protected function validarCondicoes(
        Model $documento,
        WorkflowTransicao $transicao,
        array $dadosAdicionais = []
    ): bool {
        $condicoes = $transicao->condicao ?? [];
        
        if (empty($condicoes)) {
            return true;
        }

        foreach ($condicoes as $chave => $valorEsperado) {
            $valorAtual = $this->obterValorCondicao($documento, $chave, $dadosAdicionais);
            
            if (!$this->avaliarCondicao($valorAtual, $valorEsperado)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtém o valor de uma condição específica
     */
    protected function obterValorCondicao(Model $documento, string $chave, array $dadosAdicionais = [])
    {
        // Verificar nos dados adicionais primeiro
        if (array_key_exists($chave, $dadosAdicionais)) {
            return $dadosAdicionais[$chave];
        }

        // Verificar nos atributos do documento
        if ($documento->hasAttribute($chave)) {
            return $documento->getAttribute($chave);
        }

        // Verificar em relacionamentos
        if (method_exists($documento, $chave)) {
            return $documento->$chave;
        }

        return null;
    }

    /**
     * Avalia uma condição específica
     */
    protected function avaliarCondicao($valorAtual, $valorEsperado): bool
    {
        if (is_array($valorEsperado)) {
            // Condição complexa com operador
            $operador = $valorEsperado['operador'] ?? '=';
            $valor = $valorEsperado['valor'];

            switch ($operador) {
                case '=':
                case '==':
                    return $valorAtual == $valor;
                case '!=':
                    return $valorAtual != $valor;
                case '>':
                    return $valorAtual > $valor;
                case '>=':
                    return $valorAtual >= $valor;
                case '<':
                    return $valorAtual < $valor;
                case '<=':
                    return $valorAtual <= $valor;
                case 'in':
                    return in_array($valorAtual, (array) $valor);
                case 'not_in':
                    return !in_array($valorAtual, (array) $valor);
                case 'regex':
                    return preg_match($valor, $valorAtual);
                default:
                    return false;
            }
        }

        // Comparação simples
        return $valorAtual == $valorEsperado;
    }

    /**
     * Verifica se o usuário tem permissão para executar a transição
     */
    protected function temPermissaoParaTransicao(Model $documento, WorkflowTransicao $transicao): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Usar a policy para verificar permissões
        return $user->can('executar', [$transicao, $documento]);
    }

    /**
     * Obtém o status atual do documento
     */
    protected function obterStatusDocumento(Model $documento): ?DocumentoWorkflowStatus
    {
        return DocumentoWorkflowStatus::where('documento_type', get_class($documento))
            ->where('documento_id', $documento->id)
            ->where('ativo', true)
            ->with(['workflow', 'etapa'])
            ->first();
    }

    /**
     * Cria um novo status para o documento
     */
    protected function criarNovoStatus(
        Model $documento,
        WorkflowTransicao $transicao,
        DocumentoWorkflowStatus $statusAtual
    ): DocumentoWorkflowStatus {
        // Desativar status atual
        $statusAtual->update(['ativo' => false, 'finalizado_em' => now()]);

        // Criar novo status
        return DocumentoWorkflowStatus::create([
            'documento_type' => get_class($documento),
            'documento_id' => $documento->id,
            'workflow_id' => $transicao->workflow_id,
            'workflow_etapa_id' => $transicao->etapa_destino_id,
            'status' => 'em_andamento',
            'ativo' => true,
            'iniciado_em' => now(),
            'iniciado_por' => Auth::id()
        ]);
    }

    /**
     * Registra a transição no histórico
     */
    protected function registrarHistorico(
        Model $documento,
        WorkflowTransicao $transicao,
        DocumentoWorkflowStatus $statusAnterior,
        DocumentoWorkflowStatus $novoStatus,
        array $dadosAdicionais = []
    ): void {
        DocumentoWorkflowHistorico::create([
            'documento_type' => get_class($documento),
            'documento_id' => $documento->id,
            'workflow_id' => $transicao->workflow_id,
            'workflow_transicao_id' => $transicao->id,
            'etapa_origem_id' => $transicao->etapa_origem_id,
            'etapa_destino_id' => $transicao->etapa_destino_id,
            'acao' => $transicao->acao,
            'dados_transicao' => array_merge($dadosAdicionais, [
                'automatica' => $transicao->automatica,
                'condicoes' => $transicao->condicao
            ]),
            'executado_por' => Auth::id(),
            'executado_em' => now()
        ]);
    }

    /**
     * Executa ações pós-transição
     */
    protected function executarAcoesPosTransicao(
        Model $documento,
        WorkflowTransicao $transicao,
        array $dadosAdicionais = []
    ): void {
        // Disparar eventos
        event('documento.transicao.executada', [
            'documento' => $documento,
            'transicao' => $transicao,
            'dados' => $dadosAdicionais
        ]);

        // TODO: Implementar notificações automáticas
        // TODO: Implementar webhooks para integrações externas
    }

    /**
     * Inicializa o workflow para um documento
     */
    public function inicializarWorkflow(Model $documento, Workflow $workflow): DocumentoWorkflowStatus
    {
        $primeiraEtapa = $workflow->primeiraEtapa();
        
        if (!$primeiraEtapa) {
            throw new Exception("Workflow '{$workflow->nome}' não possui etapas configuradas");
        }

        return DocumentoWorkflowStatus::create([
            'documento_type' => get_class($documento),
            'documento_id' => $documento->id,
            'workflow_id' => $workflow->id,
            'workflow_etapa_id' => $primeiraEtapa->id,
            'status' => 'em_andamento',
            'ativo' => true,
            'iniciado_em' => now(),
            'iniciado_por' => Auth::id()
        ]);
    }

    /**
     * Finaliza o workflow de um documento
     */
    public function finalizarWorkflow(Model $documento, string $statusFinal = 'concluido'): bool
    {
        $statusAtual = $this->obterStatusDocumento($documento);
        
        if (!$statusAtual) {
            return false;
        }

        $statusAtual->update([
            'ativo' => false,
            'status' => $statusFinal,
            'finalizado_em' => now(),
            'finalizado_por' => Auth::id()
        ]);

        return true;
    }
}