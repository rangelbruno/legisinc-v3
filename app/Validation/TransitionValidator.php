<?php

namespace App\Validation;

use App\Models\WorkflowTransicao;
use App\Models\DocumentoWorkflowStatus;
use App\Services\WorkflowTransitionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransitionValidator
{
    protected WorkflowTransitionService $transitionService;

    public function __construct(WorkflowTransitionService $transitionService)
    {
        $this->transitionService = $transitionService;
    }

    /**
     * Valida se uma transição pode ser executada
     */
    public function validarExecucao(
        Model $documento, 
        WorkflowTransicao $transicao, 
        array $dadosAdicionais = []
    ): array {
        $erros = [];

        // 1. Validar estado do documento
        $statusAtual = $this->obterStatusDocumento($documento);
        if (!$statusAtual) {
            $erros[] = 'Documento não possui workflow ativo.';
            return $erros;
        }

        // 2. Validar se a transição é aplicável ao estado atual
        if ($statusAtual->workflow_etapa_id !== $transicao->etapa_origem_id) {
            $erros[] = 'Transição não é válida para o estado atual do documento.';
        }

        // 3. Validar se os workflows coincidem
        if ($statusAtual->workflow_id !== $transicao->workflow_id) {
            $erros[] = 'Transição não pertence ao workflow ativo do documento.';
        }

        // 4. Validar se o documento não está bloqueado
        if ($statusAtual->bloqueado) {
            $erros[] = 'Documento está bloqueado e não pode ter transições executadas.';
        }

        // 5. Validar permissões do usuário
        if (!$this->validarPermissoes($documento, $transicao)) {
            $erros[] = 'Usuário não possui permissão para executar esta transição.';
        }

        // 6. Validar condições específicas da transição
        $condicoesErrors = $this->validarCondicoes($documento, $transicao, $dadosAdicionais);
        $erros = array_merge($erros, $condicoesErrors);

        // 7. Validar integridade dos dados
        $dadosErrors = $this->validarDados($documento, $transicao, $dadosAdicionais);
        $erros = array_merge($erros, $dadosErrors);

        return $erros;
    }

    /**
     * Valida condições específicas da transição
     */
    public function validarCondicoes(
        Model $documento,
        WorkflowTransicao $transicao,
        array $dadosAdicionais = []
    ): array {
        $erros = [];
        $condicoes = $transicao->condicao ?? [];

        if (empty($condicoes)) {
            return $erros;
        }

        foreach ($condicoes as $chave => $valorEsperado) {
            $valorAtual = $this->obterValorCondicao($documento, $chave, $dadosAdicionais);
            
            if (!$this->avaliarCondicao($valorAtual, $valorEsperado)) {
                $erros[] = "Condição '{$chave}' não foi atendida. Valor atual: " . 
                          $this->formatarValor($valorAtual) . ", esperado: " . 
                          $this->formatarValor($valorEsperado);
            }
        }

        return $erros;
    }

    /**
     * Valida permissões do usuário para a transição
     */
    public function validarPermissoes(Model $documento, WorkflowTransicao $transicao): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Usar a policy para verificar permissões
        return $user->can('executar', [$transicao, $documento]);
    }

    /**
     * Valida dados adicionais fornecidos
     */
    public function validarDados(
        Model $documento,
        WorkflowTransicao $transicao,
        array $dadosAdicionais = []
    ): array {
        $erros = [];

        // Validar estrutura básica dos dados
        $validator = Validator::make($dadosAdicionais, [
            'observacoes' => 'sometimes|string|max:1000',
            'data_execucao' => 'sometimes|date|after_or_equal:now',
            'prioridade' => 'sometimes|integer|between:1,5',
            'metadata' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            $erros = array_merge($erros, $validator->errors()->all());
        }

        // Validações específicas por tipo de transição
        $tipoErrors = $this->validarPorTipoTransicao($documento, $transicao, $dadosAdicionais);
        $erros = array_merge($erros, $tipoErrors);

        return $erros;
    }

    /**
     * Validações específicas por tipo de transição
     */
    protected function validarPorTipoTransicao(
        Model $documento,
        WorkflowTransicao $transicao,
        array $dadosAdicionais
    ): array {
        $erros = [];

        switch ($transicao->acao) {
            case 'aprovar':
                if (empty($dadosAdicionais['observacoes'])) {
                    $erros[] = 'Observações são obrigatórias para aprovação.';
                }
                break;

            case 'rejeitar':
                if (empty($dadosAdicionais['motivo_rejeicao'])) {
                    $erros[] = 'Motivo da rejeição é obrigatório.';
                }
                break;

            case 'solicitar_alteracao':
                if (empty($dadosAdicionais['alteracoes_solicitadas'])) {
                    $erros[] = 'Especificação das alterações solicitadas é obrigatória.';
                }
                break;

            case 'encaminhar':
                if (empty($dadosAdicionais['destino'])) {
                    $erros[] = 'Destino do encaminhamento é obrigatório.';
                }
                break;
        }

        return $erros;
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

            return match($operador) {
                '=', '==' => $valorAtual == $valor,
                '!=' => $valorAtual != $valor,
                '>' => $valorAtual > $valor,
                '>=' => $valorAtual >= $valor,
                '<' => $valorAtual < $valor,
                '<=' => $valorAtual <= $valor,
                'in' => in_array($valorAtual, (array) $valor),
                'not_in' => !in_array($valorAtual, (array) $valor),
                'regex' => preg_match($valor, (string) $valorAtual),
                default => false
            };
        }

        // Comparação simples
        return $valorAtual == $valorEsperado;
    }

    /**
     * Formata um valor para exibição
     */
    protected function formatarValor($valor): string
    {
        if (is_null($valor)) {
            return 'null';
        }
        if (is_bool($valor)) {
            return $valor ? 'true' : 'false';
        }
        if (is_array($valor)) {
            return '[' . implode(', ', $valor) . ']';
        }
        
        return (string) $valor;
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
}