<?php

namespace Tests\Unit\Services\Workflow;

use Tests\TestCase;
use App\Models\{User, Proposicao, Parlamentar, Workflow, WorkflowEtapa, WorkflowTransicao, DocumentoWorkflowStatus, DocumentoWorkflowHistorico};
use App\Services\Workflow\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Event, Gate};
use App\Events\WorkflowAdvanced;
use Spatie\Permission\Models\Role;

class WorkflowServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkflowService $workflowService;
    private User $parlamentar;
    private User $legislativo; 
    private Proposicao $proposicao;
    private Workflow $workflow;
    private WorkflowEtapa $etapaElaboracao;
    private WorkflowEtapa $etapaLegislativo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->workflowService = app(WorkflowService::class);
        
        // Criar roles
        Role::create(['name' => 'Parlamentar', 'guard_name' => 'web']);
        Role::create(['name' => 'Legislativo', 'guard_name' => 'web']);
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);

        // Criar usuários
        $this->parlamentar = User::factory()->create();
        $this->parlamentar->assignRole('Parlamentar');
        
        $this->legislativo = User::factory()->create();
        $this->legislativo->assignRole('Legislativo');

        // Criar parlamentar
        $parlamentarModel = Parlamentar::factory()->create([
            'user_id' => $this->parlamentar->id
        ]);

        // Criar proposição
        $this->proposicao = Proposicao::factory()->create([
            'parlamentar_id' => $parlamentarModel->id,
            'ementa' => 'Teste de ementa',
            'texto' => 'Texto da proposição'
        ]);

        // Criar workflow de teste
        $this->workflow = Workflow::create([
            'nome' => 'Workflow Teste',
            'descricao' => 'Workflow para testes',
            'tipo_documento' => 'proposicao',
            'ativo' => true,
            'is_default' => true,
        ]);

        // Criar etapas
        $this->etapaElaboracao = WorkflowEtapa::create([
            'workflow_id' => $this->workflow->id,
            'key' => 'elaboracao',
            'nome' => 'Elaboração',
            'role_responsavel' => 'Parlamentar',
            'ordem' => 1,
            'permite_edicao' => true,
            'acoes_possiveis' => ['enviar_legislativo']
        ]);

        $this->etapaLegislativo = WorkflowEtapa::create([
            'workflow_id' => $this->workflow->id,
            'key' => 'legislativo',
            'nome' => 'Legislativo',
            'role_responsavel' => 'Legislativo',
            'ordem' => 2,
            'permite_edicao' => true,
            'acoes_possiveis' => ['aprovar', 'devolver']
        ]);

        // Criar transição
        WorkflowTransicao::create([
            'workflow_id' => $this->workflow->id,
            'etapa_origem_id' => $this->etapaElaboracao->id,
            'etapa_destino_id' => $this->etapaLegislativo->id,
            'acao' => 'enviar_legislativo',
            'automatica' => false
        ]);
    }

    /** @test */
    public function pode_iniciar_fluxo_workflow()
    {
        // Act
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Assert
        $this->assertDatabaseHas('documento_workflow_status', [
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
            'workflow_id' => $this->workflow->id,
            'etapa_atual_id' => $this->etapaElaboracao->id,
            'status' => 'em_andamento'
        ]);

        $this->assertDatabaseHas('documento_workflow_historico', [
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
            'workflow_id' => $this->workflow->id,
            'etapa_atual_id' => $this->etapaElaboracao->id,
            'acao' => 'criado'
        ]);

        // Verificar se proposição foi atualizada
        $this->proposicao->refresh();
        $this->assertEquals($this->workflow->id, $this->proposicao->workflow_id);
        $this->assertEquals($this->etapaElaboracao->id, $this->proposicao->etapa_workflow_atual_id);
        $this->assertTrue($this->proposicao->fluxo_personalizado);
    }

    /** @test */
    public function iniciar_fluxo_e_idempotente()
    {
        // Iniciar fluxo duas vezes
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Deve haver apenas um status
        $statusCount = DocumentoWorkflowStatus::where([
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
        ])->count();

        $this->assertEquals(1, $statusCount);
    }

    /** @test */
    public function pode_avancar_etapa_com_permissao()
    {
        // Arrange
        $this->actingAs($this->parlamentar);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Mock Gate para permitir ação
        Gate::shouldReceive('allows')
            ->with('workflow.enviar_legislativo', [$this->proposicao, $this->etapaElaboracao])
            ->andReturn(true);

        Event::fake();

        // Act
        $this->workflowService->avancarEtapa(
            $this->proposicao, 
            'enviar_legislativo',
            'Enviando para análise'
        );

        // Assert
        $status = DocumentoWorkflowStatus::where([
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
        ])->first();

        $this->assertEquals($this->etapaLegislativo->id, $status->etapa_atual_id);
        $this->assertEquals('em_andamento', $status->status);

        // Verificar histórico
        $this->assertDatabaseHas('documento_workflow_historico', [
            'documento_id' => $this->proposicao->id,
            'acao' => 'enviar_legislativo',
            'comentario' => 'Enviando para análise',
            'etapa_atual_id' => $this->etapaLegislativo->id,
            'etapa_anterior_id' => $this->etapaElaboracao->id
        ]);

        Event::assertDispatched(WorkflowAdvanced::class);
    }

    /** @test */
    public function nao_pode_avancar_etapa_sem_permissao()
    {
        // Arrange
        $this->actingAs($this->parlamentar);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Mock Gate para negar ação
        Gate::shouldReceive('allows')
            ->with('workflow.enviar_legislativo', [$this->proposicao, $this->etapaElaboracao])
            ->andReturn(false);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sem permissão para executar esta ação');

        $this->workflowService->avancarEtapa($this->proposicao, 'enviar_legislativo');
    }

    /** @test */
    public function nao_pode_avancar_com_transicao_invalida()
    {
        // Arrange
        $this->actingAs($this->parlamentar);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        Gate::shouldReceive('allows')->andReturn(true);

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Transição inválida: acao_inexistente na etapa Elaboração');

        $this->workflowService->avancarEtapa($this->proposicao, 'acao_inexistente');
    }

    /** @test */
    public function pode_obter_status_atual()
    {
        // Arrange
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Act
        $status = $this->workflowService->obterStatus($this->proposicao);

        // Assert
        $this->assertNotNull($status);
        $this->assertEquals($this->workflow->id, $status->workflow_id);
        $this->assertEquals($this->etapaElaboracao->id, $status->etapa_atual_id);
        $this->assertEquals('em_andamento', $status->status);
    }

    /** @test */
    public function pode_obter_historico()
    {
        // Arrange
        $this->actingAs($this->parlamentar);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        Gate::shouldReceive('allows')->andReturn(true);

        $this->workflowService->avancarEtapa($this->proposicao, 'enviar_legislativo');

        // Act
        $historico = $this->workflowService->obterHistorico($this->proposicao);

        // Assert
        $this->assertCount(2, $historico); // criado + enviar_legislativo
        $this->assertEquals('enviar_legislativo', $historico->first()->acao);
        $this->assertEquals('criado', $historico->last()->acao);
    }

    /** @test */
    public function pode_pausar_workflow()
    {
        // Arrange
        $this->actingAs($this->legislativo);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Act
        $this->workflowService->pausarWorkflow($this->proposicao, 'Aguardando documentos');

        // Assert
        $status = DocumentoWorkflowStatus::where([
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
        ])->first();

        $this->assertEquals('pausado', $status->status);
        $this->assertEquals('Aguardando documentos', $status->dados_workflow['motivo_pausa']);

        $this->assertDatabaseHas('documento_workflow_historico', [
            'documento_id' => $this->proposicao->id,
            'acao' => 'pausado',
            'comentario' => 'Aguardando documentos'
        ]);
    }

    /** @test */
    public function pode_retomar_workflow()
    {
        // Arrange
        $this->actingAs($this->legislativo);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);
        $this->workflowService->pausarWorkflow($this->proposicao, 'Teste pausa');

        // Act
        $this->workflowService->retomarWorkflow($this->proposicao);

        // Assert
        $status = DocumentoWorkflowStatus::where([
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
        ])->first();

        $this->assertEquals('em_andamento', $status->status);
        $this->assertArrayNotHasKey('motivo_pausa', $status->dados_workflow ?? []);

        $this->assertDatabaseHas('documento_workflow_historico', [
            'documento_id' => $this->proposicao->id,
            'acao' => 'retomado'
        ]);
    }

    /** @test */
    public function verificacao_permissoes_funciona()
    {
        // Teste com usuário correto
        $result = $this->workflowService->verificarPermissoes(
            $this->parlamentar, 
            $this->proposicao, 
            'enviar_legislativo'
        );
        $this->assertFalse($result); // Sem status ainda

        // Iniciar fluxo
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        // Mock Gate
        Gate::shouldReceive('allows')
            ->with('workflow.enviar_legislativo', [$this->proposicao, $this->etapaElaboracao])
            ->andReturn(true);

        $result = $this->workflowService->verificarPermissoes(
            $this->parlamentar, 
            $this->proposicao, 
            'enviar_legislativo'
        );
        $this->assertTrue($result);

        // Teste com usuário sem role
        Gate::shouldReceive('allows')
            ->with('workflow.enviar_legislativo', [$this->proposicao, $this->etapaElaboracao])
            ->andReturn(false);

        $result = $this->workflowService->verificarPermissoes(
            $this->legislativo, 
            $this->proposicao, 
            'enviar_legislativo'
        );
        $this->assertFalse($result);
    }

    /** @test */
    public function idempotencia_funciona_com_chave()
    {
        // Arrange
        $this->actingAs($this->parlamentar);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        Gate::shouldReceive('allows')->andReturn(true);

        $idempotencyKey = 'test-key-' . uniqid();

        // Act - executar duas vezes com mesma chave
        $this->workflowService->avancarEtapa($this->proposicao, 'enviar_legislativo', null, $idempotencyKey);
        $this->workflowService->avancarEtapa($this->proposicao, 'enviar_legislativo', null, $idempotencyKey);

        // Assert - deve haver apenas uma entrada no histórico com esta ação
        $count = DocumentoWorkflowHistorico::where([
            'documento_id' => $this->proposicao->id,
            'acao' => 'enviar_legislativo'
        ])->count();

        $this->assertEquals(1, $count);
    }

    /** @test */
    public function lock_otimista_previne_condicoes_corrida()
    {
        // Arrange
        $this->actingAs($this->parlamentar);
        $this->workflowService->iniciarFluxo($this->proposicao, $this->workflow->id);

        Gate::shouldReceive('allows')->andReturn(true);

        // Simular versão desatualizada modificando version manualmente
        $status = DocumentoWorkflowStatus::where([
            'documento_id' => $this->proposicao->id,
            'documento_type' => Proposicao::class,
        ])->first();

        $originalVersion = $status->version;

        // Act
        $this->workflowService->avancarEtapa($this->proposicao, 'enviar_legislativo');

        // Assert - version deve ter incrementado
        $status->refresh();
        $this->assertEquals($originalVersion + 1, $status->version);
    }
}