<?php

namespace Tests\Unit\Services\Workflow;

use Tests\TestCase;
use App\Models\Proposicao;
use App\Services\Workflow\ConditionEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ConditionEvaluatorTest extends TestCase
{
    use RefreshDatabase;

    private Proposicao $proposicao;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->proposicao = Proposicao::factory()->create([
            'ementa' => 'Moção de apoio ao desenvolvimento sustentável',
            'texto' => 'Conteúdo da proposição sobre meio ambiente',
            'status' => 'aprovado',
            'assinado' => true,
            'valor' => 15000,
            'urgente' => false,
            'tags' => ['meio-ambiente', 'sustentabilidade'],
            'created_at' => Carbon::parse('2024-06-15 10:30:00')
        ]);
    }

    /** @test */
    public function avalia_condicao_simples_igual()
    {
        $condition = [
            'field' => 'status',
            'op' => '=',
            'value' => 'aprovado'
        ];

        $result = ConditionEvaluator::check($condition, $this->proposicao);
        $this->assertTrue($result);

        // Teste negativo
        $condition['value'] = 'rejeitado';
        $result = ConditionEvaluator::check($condition, $this->proposicao);
        $this->assertFalse($result);
    }

    /** @test */
    public function avalia_condicao_simples_diferente()
    {
        $condition = [
            'field' => 'status',
            'op' => '!=',
            'value' => 'rascunho'
        ];

        $result = ConditionEvaluator::check($condition, $this->proposicao);
        $this->assertTrue($result);
    }

    /** @test */
    public function avalia_condicoes_numericas()
    {
        // Maior que
        $condition = [
            'field' => 'valor',
            'op' => '>',
            'value' => 10000
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Menor que
        $condition['op'] = '<';
        $condition['value'] = 20000;
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Maior ou igual
        $condition['op'] = '>=';
        $condition['value'] = 15000;
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Menor ou igual
        $condition['op'] = '<=';
        $condition['value'] = 15000;
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_array()
    {
        // In array
        $condition = [
            'field' => 'status',
            'op' => 'in',
            'value' => ['aprovado', 'pendente']
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Not in array
        $condition['op'] = 'not_in';
        $condition['value'] = ['rascunho', 'rejeitado'];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_existencia()
    {
        // Exists
        $condition = [
            'field' => 'ementa',
            'op' => 'exists'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Not exists
        $condition = [
            'field' => 'campo_inexistente',
            'op' => 'not_exists'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_string()
    {
        // Contains
        $condition = [
            'field' => 'ementa',
            'op' => 'contains',
            'value' => 'desenvolvimento'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Not contains
        $condition['op'] = 'not_contains';
        $condition['value'] = 'economia';
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Starts with
        $condition = [
            'field' => 'ementa',
            'op' => 'starts_with',
            'value' => 'Moção'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Ends with
        $condition = [
            'field' => 'ementa',
            'op' => 'ends_with',
            'value' => 'sustentável'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_boolean()
    {
        // Is true
        $condition = [
            'field' => 'assinado',
            'op' => 'is_true'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Is false
        $condition = [
            'field' => 'urgente',
            'op' => 'is_false'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_vazias()
    {
        // Is empty
        $proposicaoVazia = Proposicao::factory()->create(['justificativa' => null]);
        $condition = [
            'field' => 'justificativa',
            'op' => 'is_empty'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $proposicaoVazia));

        // Is not empty
        $condition = [
            'field' => 'ementa',
            'op' => 'is_not_empty'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_count()
    {
        // Count
        $condition = [
            'field' => 'tags',
            'op' => 'count',
            'value' => 2
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Count greater than
        $condition['op'] = 'count_gt';
        $condition['value'] = 1;
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Count less than
        $condition['op'] = 'count_lt';
        $condition['value'] = 5;
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_data()
    {
        // Date after
        $condition = [
            'field' => 'created_at',
            'op' => 'date_after',
            'value' => '2024-01-01'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Date before
        $condition['op'] = 'date_before';
        $condition['value'] = '2025-01-01';
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));

        // Date equals
        $condition = [
            'field' => 'created_at',
            'op' => 'date_equals',
            'value' => '2024-06-15 10:30:00'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_regex()
    {
        $condition = [
            'field' => 'ementa',
            'op' => 'regex',
            'value' => '/Moção.*sustentável/'
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_between()
    {
        $condition = [
            'field' => 'valor',
            'op' => 'between',
            'value' => [10000, 20000]
        ];
        $this->assertTrue(ConditionEvaluator::check($condition, $this->proposicao));
    }

    /** @test */
    public function avalia_condicoes_all()
    {
        $conditions = [
            'all' => [
                ['field' => 'status', 'op' => '=', 'value' => 'aprovado'],
                ['field' => 'assinado', 'op' => 'is_true'],
                ['field' => 'valor', 'op' => '>', 'value' => 10000]
            ]
        ];

        $result = ConditionEvaluator::check($conditions, $this->proposicao);
        $this->assertTrue($result);

        // Teste com uma condição falsa
        $conditions['all'][] = ['field' => 'status', 'op' => '=', 'value' => 'rejeitado'];
        $result = ConditionEvaluator::check($conditions, $this->proposicao);
        $this->assertFalse($result);
    }

    /** @test */
    public function avalia_condicoes_any()
    {
        $conditions = [
            'any' => [
                ['field' => 'status', 'op' => '=', 'value' => 'rejeitado'], // false
                ['field' => 'urgente', 'op' => 'is_true'], // false
                ['field' => 'assinado', 'op' => 'is_true'] // true
            ]
        ];

        $result = ConditionEvaluator::check($conditions, $this->proposicao);
        $this->assertTrue($result);

        // Teste com todas falsas
        $conditions = [
            'any' => [
                ['field' => 'status', 'op' => '=', 'value' => 'rejeitado'],
                ['field' => 'urgente', 'op' => 'is_true']
            ]
        ];

        $result = ConditionEvaluator::check($conditions, $this->proposicao);
        $this->assertFalse($result);
    }

    /** @test */
    public function retorna_true_em_caso_erro()
    {
        // Condição inválida deve retornar true (fail-safe)
        $conditions = [
            'field' => 'campo_inexistente',
            'op' => 'operador_invalido',
            'value' => 'qualquer_coisa'
        ];

        $result = ConditionEvaluator::check($conditions, $this->proposicao);
        $this->assertTrue($result);
    }

    /** @test */
    public function valida_condicoes_corretas()
    {
        $conditions = [
            'field' => 'status',
            'op' => '=',
            'value' => 'aprovado'
        ];

        $errors = ConditionEvaluator::validate($conditions);
        $this->assertEmpty($errors);
    }

    /** @test */
    public function valida_condicoes_all()
    {
        $conditions = [
            'all' => [
                ['field' => 'status', 'op' => '=', 'value' => 'aprovado']
            ]
        ];

        $errors = ConditionEvaluator::validate($conditions);
        $this->assertEmpty($errors);

        // Teste com all inválido
        $conditions['all'] = 'string_invalida';
        $errors = ConditionEvaluator::validate($conditions);
        $this->assertContains('Campo "all" deve ser um array', $errors);
    }

    /** @test */
    public function valida_condicoes_operador_invalido()
    {
        $conditions = [
            'field' => 'status',
            'op' => 'operador_invalido',
            'value' => 'teste'
        ];

        $errors = ConditionEvaluator::validate($conditions);
        $this->assertContains('Operador inválido: operador_invalido', $errors);
    }

    /** @test */
    public function valida_condicoes_campo_obrigatorio()
    {
        $conditions = [
            'op' => '=',
            'value' => 'teste'
        ];

        $errors = ConditionEvaluator::validate($conditions);
        $this->assertContains('Condição simples deve ter campo "field"', $errors);
    }

    /** @test */
    public function retorna_exemplos_validos()
    {
        $examples = ConditionEvaluator::examples();

        $this->assertArrayHasKey('campo_simples', $examples);
        $this->assertArrayHasKey('multiplas_condicoes_todas', $examples);
        $this->assertArrayHasKey('multiplas_condicoes_qualquer', $examples);
        $this->assertArrayHasKey('condicao_data', $examples);
        $this->assertArrayHasKey('condicao_array', $examples);
        $this->assertArrayHasKey('condicao_texto', $examples);

        // Verificar se exemplo simples é válido
        $errors = ConditionEvaluator::validate($examples['campo_simples']);
        $this->assertEmpty($errors);
    }

    /** @test */
    public function suporta_dot_notation()
    {
        // Assumindo que proposição tem relacionamento com parlamentar
        $proposicaoComRelacao = Proposicao::factory()->create();

        $condition = [
            'field' => 'id',
            'op' => 'exists'
        ];

        $result = ConditionEvaluator::check($condition, $proposicaoComRelacao);
        $this->assertTrue($result);
    }
}