<?php

namespace App\Services\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ConditionEvaluator
{
    /**
     * Avalia condições JSON para determinar se uma transição é válida
     * 
     * Formato suportado:
     * {
     *   "all": [condição1, condição2, ...],    // Todas devem ser verdadeiras
     *   "any": [condição1, condição2, ...]     // Pelo menos uma deve ser verdadeira
     * }
     * 
     * Ou condição simples:
     * {
     *   "field": "campo_do_modelo",
     *   "op": "operador",
     *   "value": "valor_esperado"
     * }
     */
    public static function check(array $conditions, Model $documento): bool
    {
        try {
            if (isset($conditions['all'])) {
                return collect($conditions['all'])->every(
                    fn($cond) => self::evaluateSingle($cond, $documento)
                );
            }

            if (isset($conditions['any'])) {
                return collect($conditions['any'])->some(
                    fn($cond) => self::evaluateSingle($cond, $documento)
                );
            }

            return self::evaluateSingle($conditions, $documento);
            
        } catch (\Exception $e) {
            Log::error('Erro ao avaliar condições do workflow', [
                'conditions' => $conditions,
                'documento' => $documento::class . ':' . $documento->id,
                'error' => $e->getMessage()
            ]);
            
            // Em caso de erro, permitir transição (fail-safe)
            return true;
        }
    }

    /**
     * Avalia uma condição simples
     */
    private static function evaluateSingle(array $condition, Model $documento): bool
    {
        $field = $condition['field'] ?? null;
        $op = $condition['op'] ?? '=';
        $value = $condition['value'] ?? null;

        if (!$field) {
            return false;
        }

        // Obter valor do campo (suporta dot notation)
        $actualValue = data_get($documento, $field);

        return match($op) {
            '=' => $actualValue == $value,
            '!=' => $actualValue != $value,
            '>' => $actualValue > $value,
            '>=' => $actualValue >= $value,
            '<' => $actualValue < $value,
            '<=' => $actualValue <= $value,
            'in' => in_array($actualValue, (array)$value),
            'not_in' => !in_array($actualValue, (array)$value),
            'exists' => !is_null($actualValue),
            'not_exists' => is_null($actualValue),
            'contains' => is_string($actualValue) && str_contains($actualValue, $value),
            'not_contains' => is_string($actualValue) && !str_contains($actualValue, $value),
            'starts_with' => is_string($actualValue) && str_starts_with($actualValue, $value),
            'ends_with' => is_string($actualValue) && str_ends_with($actualValue, $value),
            'regex' => is_string($actualValue) && preg_match($value, $actualValue),
            'between' => is_array($value) && count($value) === 2 
                        && $actualValue >= $value[0] && $actualValue <= $value[1],
            'count' => is_countable($actualValue) && count($actualValue) == $value,
            'count_gt' => is_countable($actualValue) && count($actualValue) > $value,
            'count_gte' => is_countable($actualValue) && count($actualValue) >= $value,
            'count_lt' => is_countable($actualValue) && count($actualValue) < $value,
            'count_lte' => is_countable($actualValue) && count($actualValue) <= $value,
            'is_empty' => empty($actualValue),
            'is_not_empty' => !empty($actualValue),
            'is_true' => $actualValue === true,
            'is_false' => $actualValue === false,
            'date_before' => self::compareDates($actualValue, $value, '<'),
            'date_after' => self::compareDates($actualValue, $value, '>'),
            'date_equals' => self::compareDates($actualValue, $value, '='),
            default => false
        };
    }

    /**
     * Compara datas de forma segura
     */
    private static function compareDates($actual, $expected, string $operator): bool
    {
        try {
            $actualDate = $actual instanceof \Carbon\Carbon ? $actual : new \Carbon\Carbon($actual);
            $expectedDate = $expected instanceof \Carbon\Carbon ? $expected : new \Carbon\Carbon($expected);

            return match($operator) {
                '<' => $actualDate->lt($expectedDate),
                '>' => $actualDate->gt($expectedDate),
                '=' => $actualDate->eq($expectedDate),
                default => false
            };
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Valida se as condições estão em formato correto
     */
    public static function validate(array $conditions): array
    {
        $errors = [];

        if (isset($conditions['all']) && !is_array($conditions['all'])) {
            $errors[] = 'Campo "all" deve ser um array';
        }

        if (isset($conditions['any']) && !is_array($conditions['any'])) {
            $errors[] = 'Campo "any" deve ser um array';
        }

        // Se não tem all nem any, deve ser condição simples
        if (!isset($conditions['all']) && !isset($conditions['any'])) {
            if (!isset($conditions['field'])) {
                $errors[] = 'Condição simples deve ter campo "field"';
            }
            
            if (isset($conditions['op'])) {
                $validOps = [
                    '=', '!=', '>', '>=', '<', '<=', 'in', 'not_in', 
                    'exists', 'not_exists', 'contains', 'not_contains',
                    'starts_with', 'ends_with', 'regex', 'between',
                    'count', 'count_gt', 'count_gte', 'count_lt', 'count_lte',
                    'is_empty', 'is_not_empty', 'is_true', 'is_false',
                    'date_before', 'date_after', 'date_equals'
                ];
                
                if (!in_array($conditions['op'], $validOps)) {
                    $errors[] = 'Operador inválido: ' . $conditions['op'];
                }
            }
        }

        return $errors;
    }

    /**
     * Exemplos de condições para documentação
     */
    public static function examples(): array
    {
        return [
            'campo_simples' => [
                'field' => 'status',
                'op' => '=',
                'value' => 'aprovado'
            ],
            'multiplas_condicoes_todas' => [
                'all' => [
                    ['field' => 'valor', 'op' => '>', 'value' => 1000],
                    ['field' => 'aprovado', 'op' => '=', 'value' => true],
                    ['field' => 'anexos', 'op' => 'count_gte', 'value' => 1]
                ]
            ],
            'multiplas_condicoes_qualquer' => [
                'any' => [
                    ['field' => 'urgente', 'op' => '=', 'value' => true],
                    ['field' => 'valor', 'op' => '>', 'value' => 10000],
                    ['field' => 'autor.role', 'op' => '=', 'value' => 'admin']
                ]
            ],
            'condicao_data' => [
                'field' => 'created_at',
                'op' => 'date_after',
                'value' => '2024-01-01'
            ],
            'condicao_array' => [
                'field' => 'tags',
                'op' => 'in',
                'value' => ['importante', 'urgente']
            ],
            'condicao_texto' => [
                'field' => 'titulo',
                'op' => 'contains',
                'value' => 'orçamento'
            ]
        ];
    }
}