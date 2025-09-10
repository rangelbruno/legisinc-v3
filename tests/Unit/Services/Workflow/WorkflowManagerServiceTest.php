<?php

namespace Tests\Unit\Services\Workflow;

use Tests\TestCase;
use App\Models\{Workflow, WorkflowEtapa, WorkflowTransicao, User, Proposicao, Parlamentar};
use App\Services\Workflow\WorkflowManagerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class WorkflowManagerServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkflowManagerService $workflowManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->workflowManager = app(WorkflowManagerService::class);

        // Criar roles básicos
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Parlamentar', 'guard_name' => 'web']);
        Role::create(['name' => 'Legislativo', 'guard_name' => 'web']);
    }

    /** @test */
    public function pode_criar_workflow_completo()
    {
        // Arrange
        $dadosWorkflow = [
            'nome' => 'Workflow de Teste',
            'descricao' => 'Workflow criado para testes automatizados',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'etapas' => [
                [
                    'key' => 'inicio',
                    'nome' => 'Início',
                    'descricao' => 'Etapa inicial',
                    'role_responsavel' => 'Parlamentar',
                    'ordem' => 1,
                    'acoes_possiveis' => ['enviar'],
                    'permite_edicao' => true
                ],
                [
                    'key' => 'analise',
                    'nome' => 'Análise',
                    'descricao' => 'Etapa de análise',
                    'role_responsavel' => 'Legislativo',
                    'ordem' => 2,
                    'acoes_possiveis' => ['aprovar', 'devolver'],
                    'permite_edicao' => false
                ]
            ],
            'transicoes' => [
                [
                    'from' => 'inicio',
                    'to' => 'analise',
                    'acao' => 'enviar',
                    'automatica' => false
                ]
            ]
        ];

        // Act
        $workflow = $this->workflowManager->criarWorkflow($dadosWorkflow);

        // Assert
        $this->assertInstanceOf(Workflow::class, $workflow);
        $this->assertEquals('Workflow de Teste', $workflow->nome);
        $this->assertEquals('proposicao', $workflow->tipo_documento);
        $this->assertTrue($workflow->ativo);

        // Verificar etapas
        $this->assertEquals(2, $workflow->etapas()->count());
        
        $etapaInicio = $workflow->etapas()->where('key', 'inicio')->first();
        $this->assertNotNull($etapaInicio);
        $this->assertEquals('Início', $etapaInicio->nome);
        $this->assertEquals('Parlamentar', $etapaInicio->role_responsavel);
        $this->assertTrue($etapaInicio->permite_edicao);

        // Verificar transições
        $this->assertEquals(1, $workflow->transicoes()->count());
        
        $transicao = $workflow->transicoes()->first();
        $this->assertEquals($etapaInicio->id, $transicao->etapa_origem_id);
        $this->assertEquals('enviar', $transicao->acao);
        $this->assertFalse($transicao->automatica);
    }

    /** @test */
    public function ignora_transicoes_com_keys_invalidas()
    {
        // Arrange
        $dadosWorkflow = [
            'nome' => 'Workflow com Transição Inválida',
            'tipo_documento' => 'proposicao',
            'etapas' => [
                ['key' => 'etapa1', 'nome' => 'Etapa 1']
            ],
            'transicoes' => [
                [
                    'from' => 'etapa_inexistente',
                    'to' => 'etapa1',
                    'acao' => 'teste'
                ],
                [
                    'from' => 'etapa1',
                    'to' => 'outra_inexistente',
                    'acao' => 'teste2'
                ]
            ]
        ];

        Log::shouldReceive('warning')->twice();

        // Act
        $workflow = $this->workflowManager->criarWorkflow($dadosWorkflow);

        // Assert
        $this->assertEquals(0, $workflow->transicoes()->count());
    }

    /** @test */
    public function pode_duplicar_workflow()
    {
        // Arrange
        $workflowOriginal = Workflow::create([
            'nome' => 'Workflow Original',
            'descricao' => 'Descrição original',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'is_default' => true
        ]);

        $etapaOriginal = WorkflowEtapa::create([
            'workflow_id' => $workflowOriginal->id,
            'key' => 'etapa_teste',
            'nome' => 'Etapa Teste',
            'ordem' => 1
        ]);

        WorkflowTransicao::create([
            'workflow_id' => $workflowOriginal->id,
            'etapa_origem_id' => $etapaOriginal->id,
            'etapa_destino_id' => $etapaOriginal->id,
            'acao' => 'loop'
        ]);

        // Act
        $workflowDuplicado = $this->workflowManager->duplicarWorkflow(
            $workflowOriginal->id, 
            'Workflow Duplicado'
        );

        // Assert
        $this->assertEquals('Workflow Duplicado', $workflowDuplicado->nome);
        $this->assertEquals($workflowOriginal->descricao, $workflowDuplicado->descricao);
        $this->assertEquals($workflowOriginal->tipo_documento, $workflowDuplicado->tipo_documento);
        $this->assertFalse($workflowDuplicado->is_default); // Nunca herda padrão
        $this->assertFalse($workflowDuplicado->ativo); // Inativo até ser publicado

        // Verificar etapas duplicadas
        $this->assertEquals($workflowOriginal->etapas()->count(), $workflowDuplicado->etapas()->count());
        
        $etapaDuplicada = $workflowDuplicado->etapas()->where('key', 'etapa_teste')->first();
        $this->assertNotNull($etapaDuplicada);
        $this->assertEquals($etapaOriginal->nome, $etapaDuplicada->nome);

        // Verificar transições duplicadas
        $this->assertEquals($workflowOriginal->transicoes()->count(), $workflowDuplicado->transicoes()->count());
    }

    /** @test */
    public function pode_ativar_desativar_workflow()
    {
        // Arrange
        $workflow = Workflow::create([
            'nome' => 'Workflow Teste',
            'tipo_documento' => 'proposicao',
            'ativo' => false
        ]);

        // Act - Ativar
        $this->workflowManager->ativarDesativarWorkflow($workflow->id, true);

        // Assert
        $workflow->refresh();
        $this->assertTrue($workflow->ativo);

        // Act - Desativar
        $this->workflowManager->ativarDesativarWorkflow($workflow->id, false);

        // Assert
        $workflow->refresh();
        $this->assertFalse($workflow->ativo);
    }

    /** @test */
    public function nao_pode_desativar_workflow_em_uso()
    {
        // Arrange
        $workflow = Workflow::create([
            'nome' => 'Workflow em Uso',
            'tipo_documento' => 'proposicao',
            'ativo' => true
        ]);

        // Mock do método temDocumentosEmUso
        $workflow = \Mockery::mock($workflow)->makePartial();
        $workflow->shouldReceive('temDocumentosEmUso')->andReturn(true);

        // Mock do Workflow::findOrFail
        Workflow::shouldReceive('findOrFail')
            ->with($workflow->id)
            ->andReturn($workflow);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível desativar workflow com documentos em andamento');

        $this->workflowManager->ativarDesativarWorkflow($workflow->id, false);
    }

    /** @test */
    public function pode_definir_workflow_padrao()
    {
        // Arrange
        $workflow1 = Workflow::create([
            'nome' => 'Workflow 1',
            'tipo_documento' => 'proposicao',
            'ativo' => false,
            'is_default' => true
        ]);

        $workflow2 = Workflow::create([
            'nome' => 'Workflow 2',
            'tipo_documento' => 'proposicao',
            'ativo' => false,
            'is_default' => false
        ]);

        // Act
        $this->workflowManager->definirWorkflowPadrao($workflow2->id, 'proposicao');

        // Assert
        $workflow1->refresh();
        $workflow2->refresh();

        $this->assertFalse($workflow1->is_default);
        $this->assertTrue($workflow2->is_default);
        $this->assertTrue($workflow2->ativo); // Workflow padrão deve estar ativo
    }

    /** @test */
    public function pode_atualizar_workflow()
    {
        // Arrange
        $workflow = Workflow::create([
            'nome' => 'Workflow Original',
            'descricao' => 'Descrição original',
            'tipo_documento' => 'proposicao'
        ]);

        $etapaOriginal = WorkflowEtapa::create([
            'workflow_id' => $workflow->id,
            'key' => 'etapa_original',
            'nome' => 'Etapa Original',
            'ordem' => 1
        ]);

        $dadosAtualizacao = [
            'nome' => 'Workflow Atualizado',
            'descricao' => 'Nova descrição',
            'etapas' => [
                [
                    'key' => 'nova_etapa',
                    'nome' => 'Nova Etapa',
                    'ordem' => 1
                ]
            ],
            'transicoes' => []
        ];

        // Act
        $workflowAtualizado = $this->workflowManager->atualizarWorkflow($workflow->id, $dadosAtualizacao);

        // Assert
        $this->assertEquals('Workflow Atualizado', $workflowAtualizado->nome);
        $this->assertEquals('Nova descrição', $workflowAtualizado->descricao);

        // Verificar se etapas antigas foram removidas e novas criadas
        $this->assertEquals(1, $workflowAtualizado->etapas()->count());
        $this->assertNull($workflowAtualizado->etapas()->where('key', 'etapa_original')->first());
        $this->assertNotNull($workflowAtualizado->etapas()->where('key', 'nova_etapa')->first());
    }

    /** @test */
    public function nao_pode_atualizar_workflow_em_uso()
    {
        // Arrange
        $workflow = Workflow::create([
            'nome' => 'Workflow em Uso',
            'tipo_documento' => 'proposicao'
        ]);

        // Mock do método temDocumentosEmUso
        $workflow = \Mockery::mock($workflow)->makePartial();
        $workflow->shouldReceive('temDocumentosEmUso')->andReturn(true);

        Workflow::shouldReceive('with')->andReturn(
            \Mockery::mock()->shouldReceive('findOrFail')->andReturn($workflow)->getMock()
        );

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível editar workflow com documentos em andamento');

        $this->workflowManager->atualizarWorkflow($workflow->id, ['nome' => 'Novo Nome']);
    }

    /** @test */
    public function pode_remover_workflow_sem_historico()
    {
        // Arrange
        $workflow = Workflow::create([
            'nome' => 'Workflow para Remover',
            'tipo_documento' => 'proposicao'
        ]);

        $etapa = WorkflowEtapa::create([
            'workflow_id' => $workflow->id,
            'key' => 'etapa_teste',
            'nome' => 'Etapa Teste',
            'ordem' => 1
        ]);

        $transicao = WorkflowTransicao::create([
            'workflow_id' => $workflow->id,
            'etapa_origem_id' => $etapa->id,
            'etapa_destino_id' => $etapa->id,
            'acao' => 'loop'
        ]);

        // Mock sem histórico
        $workflow = \Mockery::mock($workflow)->makePartial();
        $workflow->shouldReceive('temDocumentosEmUso')->andReturn(false);
        $workflow->shouldReceive('historico->exists')->andReturn(false);

        Workflow::shouldReceive('findOrFail')
            ->with($workflow->id)
            ->andReturn($workflow);

        // Act
        $this->workflowManager->removerWorkflow($workflow->id);

        // Assert
        $this->assertDatabaseMissing('workflows', ['id' => $workflow->id]);
        $this->assertDatabaseMissing('workflow_etapas', ['id' => $etapa->id]);
        $this->assertDatabaseMissing('workflow_transicoes', ['id' => $transicao->id]);
    }

    /** @test */
    public function arquiva_workflow_com_historico()
    {
        // Arrange
        $workflow = Workflow::create([
            'nome' => 'Workflow com Histórico',
            'tipo_documento' => 'proposicao',
            'ativo' => true
        ]);

        // Mock com histórico
        $workflow = \Mockery::mock($workflow)->makePartial();
        $workflow->shouldReceive('temDocumentosEmUso')->andReturn(false);
        $workflow->shouldReceive('historico->exists')->andReturn(true);

        Workflow::shouldReceive('findOrFail')
            ->with($workflow->id)
            ->andReturn($workflow);

        // Act
        $this->workflowManager->removerWorkflow($workflow->id);

        // Assert - Workflow deve ser arquivado, não removido
        $workflow->refresh();
        $this->assertFalse($workflow->ativo);
        $this->assertStringContains('(Arquivado)', $workflow->nome);
    }

    /** @test */
    public function lista_workflows_por_tipo()
    {
        // Arrange
        Workflow::create([
            'nome' => 'Workflow Proposição Ativo',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'is_default' => true,
            'ordem' => 1
        ]);

        Workflow::create([
            'nome' => 'Workflow Proposição Inativo',
            'tipo_documento' => 'proposicao',
            'ativo' => false
        ]);

        Workflow::create([
            'nome' => 'Workflow Outro Tipo',
            'tipo_documento' => 'requerimento',
            'ativo' => true
        ]);

        // Act
        $workflows = $this->workflowManager->listarWorkflowsPorTipo('proposicao');

        // Assert
        $this->assertCount(1, $workflows); // Apenas ativo
        $this->assertEquals('Workflow Proposição Ativo', $workflows->first()->nome);
    }

    /** @test */
    public function obtem_workflow_padrao()
    {
        // Arrange
        $workflowPadrao = Workflow::create([
            'nome' => 'Workflow Padrão',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'is_default' => true
        ]);

        Workflow::create([
            'nome' => 'Outro Workflow',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'is_default' => false
        ]);

        // Act
        $workflow = $this->workflowManager->obterWorkflowPadrao('proposicao');

        // Assert
        $this->assertNotNull($workflow);
        $this->assertEquals($workflowPadrao->id, $workflow->id);
        $this->assertTrue($workflow->is_default);
    }

    /** @test */
    public function retorna_null_se_nao_ha_workflow_padrao()
    {
        // Arrange
        Workflow::create([
            'nome' => 'Workflow Sem Padrão',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'is_default' => false
        ]);

        // Act
        $workflow = $this->workflowManager->obterWorkflowPadrao('proposicao');

        // Assert
        $this->assertNull($workflow);
    }

    /** @test */
    public function valida_workflow_bem_formado()
    {
        // Arrange
        $workflow = Workflow::create(['nome' => 'Teste', 'tipo_documento' => 'proposicao']);
        
        $etapa1 = WorkflowEtapa::create([
            'workflow_id' => $workflow->id,
            'key' => 'etapa1',
            'nome' => 'Etapa 1',
            'ordem' => 1
        ]);

        $etapa2 = WorkflowEtapa::create([
            'workflow_id' => $workflow->id,
            'key' => 'etapa2', 
            'nome' => 'Etapa 2',
            'ordem' => 2
        ]);

        WorkflowTransicao::create([
            'workflow_id' => $workflow->id,
            'etapa_origem_id' => $etapa1->id,
            'etapa_destino_id' => $etapa2->id,
            'acao' => 'avancar'
        ]);

        // Act
        $erros = $this->workflowManager->validarWorkflow($workflow);

        // Assert
        $this->assertEmpty($erros);
    }

    /** @test */
    public function detecta_workflow_sem_etapas()
    {
        // Arrange
        $workflow = Workflow::create(['nome' => 'Teste', 'tipo_documento' => 'proposicao']);

        // Act
        $erros = $this->workflowManager->validarWorkflow($workflow);

        // Assert
        $this->assertContains('Workflow deve ter pelo menos uma etapa', $erros);
    }

    /** @test */
    public function detecta_ordens_duplicadas()
    {
        // Arrange
        $workflow = Workflow::create(['nome' => 'Teste', 'tipo_documento' => 'proposicao']);
        
        WorkflowEtapa::create([
            'workflow_id' => $workflow->id,
            'key' => 'etapa1',
            'nome' => 'Etapa 1',
            'ordem' => 1
        ]);

        WorkflowEtapa::create([
            'workflow_id' => $workflow->id,
            'key' => 'etapa2',
            'nome' => 'Etapa 2',
            'ordem' => 1 // Ordem duplicada
        ]);

        // Act
        $erros = $this->workflowManager->validarWorkflow($workflow);

        // Assert
        $this->assertContains('Etapas devem ter ordens únicas', $erros);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}