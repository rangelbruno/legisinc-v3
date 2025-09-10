# Sistema de Fluxos Modulares - Adendo Técnico

## 1. Correções Críticas do Documento Principal

### 1.1. Adaptação para PostgreSQL

**❌ Problema**: Schema original com sintaxe MySQL (ENUM, AUTO_INCREMENT)
**✅ Solução**: Migrations Laravel corrigidas para PostgreSQL

```php
// Migration: create_workflows_table.php
Schema::create('workflows', function (Blueprint $t) {
    $t->bigIncrements('id');
    $t->string('nome');
    $t->text('descricao')->nullable();
    $t->string('tipo_documento'); // Lookup table recomendado
    $t->boolean('ativo')->default(true);
    $t->boolean('is_default')->default(false);
    $t->integer('ordem')->default(0);
    $t->jsonb('configuracao')->nullable();
    $t->timestamps();

    // Índice parcial: apenas um default por tipo
    $t->index(['tipo_documento'], 'idx_workflows_tipo');
});

// Constraint única para default por tipo (PostgreSQL)
DB::statement('CREATE UNIQUE INDEX uniq_default_workflow 
               ON workflows (tipo_documento) 
               WHERE is_default = true');
```

### 1.2. Integridade de Transições

```php
// Migration: create_workflow_transicoes_table.php
Schema::create('workflow_transicoes', function (Blueprint $t) {
    $t->bigIncrements('id');
    $t->foreignId('workflow_id')->constrained('workflows')->onDelete('cascade');
    $t->foreignId('etapa_origem_id')->constrained('workflow_etapas')->onDelete('cascade');
    $t->foreignId('etapa_destino_id')->constrained('workflow_etapas')->onDelete('cascade');
    $t->string('acao');
    $t->jsonb('condicao')->nullable();
    $t->boolean('automatica')->default(false);
    $t->timestamps();

    // Evita fan-out confuso: uma ação por etapa origem
    $t->unique(['workflow_id','etapa_origem_id','acao'], 'uniq_transicao_acao');
    
    // Índices de performance
    $t->index(['etapa_origem_id','acao'], 'idx_transicao_busca');
});

// Constraint: evita auto-loops
DB::statement('ALTER TABLE workflow_transicoes 
               ADD CONSTRAINT chk_no_self_loop 
               CHECK (etapa_origem_id != etapa_destino_id)');
```

### 1.3. Lock Otimista e Concorrência

```php
// Migration: create_documento_workflow_status_table.php
Schema::create('documento_workflow_status', function (Blueprint $t) {
    $t->bigIncrements('id');
    $t->morphs('documento'); // documento_type, documento_id + índice automático
    $t->foreignId('workflow_id')->constrained('workflows');
    $t->foreignId('etapa_atual_id')->constrained('workflow_etapas');
    $t->string('status')->default('em_andamento');
    $t->timestamp('prazo_atual')->nullable();
    $t->timestamp('iniciado_em')->useCurrent();
    $t->timestamp('finalizado_em')->nullable();
    $t->jsonb('dados_workflow')->nullable();
    $t->unsignedInteger('version')->default(0); // Lock otimista
    $t->timestamps();

    $t->unique(['documento_id','documento_type','workflow_id'], 'uniq_doc_workflow');
    
    // Índices de performance
    $t->index(['status','prazo_atual'], 'idx_status_prazo_vencido');
    $t->index(['workflow_id','status'], 'idx_workflow_ativo');
});
```

## 2. WorkflowService Corrigido com Transações

```php
<?php
namespace App\Services\Workflow;

use App\Events\WorkflowAdvanced;
use App\Models\{Workflow, WorkflowEtapa, DocumentoWorkflowStatus, DocumentoWorkflowHistorico};
use App\Services\Workflow\ConditionEvaluator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{DB, Gate};
use Illuminate\Support\Str;

class WorkflowService
{
    public function iniciarFluxo(Model $documento, int $workflowId): void
    {
        DB::transaction(function () use ($documento, $workflowId) {
            $workflow = Workflow::with(['etapas' => fn($q) => $q->orderBy('ordem')])
                              ->findOrFail($workflowId);
            
            $primeiraEtapa = $workflow->etapas->firstOrFail();

            // Criar ou atualizar status (idempotente)
            $status = DocumentoWorkflowStatus::updateOrCreate(
                [
                    'documento_id' => $documento->id,
                    'documento_type' => $documento::class,
                    'workflow_id' => $workflow->id
                ],
                [
                    'etapa_atual_id' => $primeiraEtapa->id,
                    'status' => 'em_andamento',
                    'iniciado_em' => now(),
                    'version' => DB::raw('version + 1')
                ]
            );

            // Histórico inicial
            DocumentoWorkflowHistorico::create([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
                'workflow_id' => $workflow->id,
                'etapa_atual_id' => $primeiraEtapa->id,
                'usuario_id' => auth()->id(),
                'acao' => 'criado',
                'dados_contexto' => ['workflow_iniciado' => true]
            ]);

            // Atualizar campos de acesso rápido no documento
            $documento->update([
                'workflow_id' => $workflow->id,
                'etapa_workflow_atual_id' => $primeiraEtapa->id
            ]);

            event(new WorkflowAdvanced($documento, null, $primeiraEtapa, 'criado'));
        });
    }

    public function avancarEtapa(
        Model $documento, 
        string $acao, 
        ?string $comentario = null,
        ?string $idempotencyKey = null
    ): void {
        DB::transaction(function () use ($documento, $acao, $comentario, $idempotencyKey) {
            // Lock otimista
            $status = DocumentoWorkflowStatus::where([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
            ])->lockForUpdate()->firstOrFail();

            // Verificar idempotência se fornecida
            if ($idempotencyKey && $this->jaProcessado($idempotencyKey)) {
                return; // Já foi processado
            }

            $etapaAtual = WorkflowEtapa::findOrFail($status->etapa_atual_id);

            // Verificar permissões
            if (!$this->verificarPermissoes(auth()->user(), $documento, $acao)) {
                abort(403, 'Sem permissão para executar esta ação');
            }

            // Determinar próxima etapa
            $proximaEtapa = $this->obterProximaEtapa($etapaAtual, $acao, $documento);
            if (!$proximaEtapa) {
                throw new \RuntimeException("Transição inválida: {$acao} na etapa {$etapaAtual->nome}");
            }

            // Registrar no histórico
            DocumentoWorkflowHistorico::create([
                'documento_id' => $documento->id,
                'documento_type' => $documento::class,
                'workflow_id' => $status->workflow_id,
                'etapa_atual_id' => $proximaEtapa->id,
                'etapa_anterior_id' => $etapaAtual->id,
                'usuario_id' => auth()->id(),
                'acao' => $acao,
                'comentario' => $comentario,
                'dados_contexto' => [
                    'idempotency_key' => $idempotencyKey,
                    'transicao_automatica' => false
                ]
            ]);

            // Atualizar status (com versioning)
            $novoStatus = $this->isEtapaFinal($proximaEtapa) ? 'finalizado' : 'em_andamento';
            $status->update([
                'etapa_atual_id' => $proximaEtapa->id,
                'status' => $novoStatus,
                'finalizado_em' => $novoStatus === 'finalizado' ? now() : null,
                'prazo_atual' => $this->calcularPrazo($proximaEtapa),
                'version' => $status->version + 1
            ]);

            // Atualizar documento para acesso rápido
            $documento->update([
                'etapa_workflow_atual_id' => $proximaEtapa->id
            ]);

            // Marcar idempotência se fornecida
            if ($idempotencyKey) {
                $this->marcarProcessado($idempotencyKey);
            }

            event(new WorkflowAdvanced($documento, $etapaAtual, $proximaEtapa, $acao));
        });
    }

    public function verificarPermissoes($usuario, Model $documento, string $acao): bool
    {
        $status = DocumentoWorkflowStatus::where([
            'documento_id' => $documento->id,
            'documento_type' => $documento::class,
        ])->first();

        if (!$status) return false;

        $etapaAtual = WorkflowEtapa::find($status->etapa_atual_id);
        if (!$etapaAtual) return false;

        // 1. Verificar role da etapa
        if ($etapaAtual->role_responsavel && !$usuario->hasRole($etapaAtual->role_responsavel)) {
            return false;
        }

        // 2. Verificar ação permitida na etapa
        $acoesPermitidas = $etapaAtual->acoes_possiveis ?? [];
        if (!in_array($acao, $acoesPermitidas)) {
            return false;
        }

        // 3. Gate/Policy específica
        return Gate::allows('workflow.' . $acao, [$documento, $etapaAtual]);
    }

    public function obterProximaEtapa(WorkflowEtapa $etapaAtual, string $acao, Model $documento): ?WorkflowEtapa
    {
        $transicao = WorkflowTransicao::where([
            'workflow_id' => $etapaAtual->workflow_id,
            'etapa_origem_id' => $etapaAtual->id,
            'acao' => $acao,
        ])->first();

        if (!$transicao) return null;

        // Avaliar condições JSON
        if ($transicao->condicao && !ConditionEvaluator::check($transicao->condicao, $documento)) {
            return null;
        }

        return WorkflowEtapa::find($transicao->etapa_destino_id);
    }

    private function isEtapaFinal(WorkflowEtapa $etapa): bool
    {
        return !WorkflowTransicao::where('etapa_origem_id', $etapa->id)->exists();
    }

    private function calcularPrazo(WorkflowEtapa $etapa): ?Carbon
    {
        return $etapa->tempo_limite_dias 
            ? now()->addDays($etapa->tempo_limite_dias)
            : null;
    }

    private function jaProcessado(string $key): bool
    {
        return cache()->has("workflow_idempotency:{$key}");
    }

    private function marcarProcessado(string $key): void
    {
        cache()->put("workflow_idempotency:{$key}", true, now()->addHours(24));
    }
}
```

## 3. ConditionEvaluator para Condições JSON

```php
<?php
namespace App\Services\Workflow;

use Illuminate\Database\Eloquent\Model;

class ConditionEvaluator
{
    public static function check(array $conditions, Model $documento): bool
    {
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
    }

    private static function evaluateSingle(array $condition, Model $documento): bool
    {
        $field = $condition['field'] ?? null;
        $op = $condition['op'] ?? '=';
        $value = $condition['value'] ?? null;

        if (!$field) return false;

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
            'contains' => str_contains($actualValue, $value),
            default => false
        };
    }
}
```

## 4. Middleware Integrado

```php
<?php
namespace App\Http\Middleware;

class CheckWorkflowPermission
{
    public function handle($request, Closure $next, $acao = null)
    {
        $documento = $this->extrairDocumento($request);
        
        if (!$documento) {
            return $next($request);
        }

        // Verificar se documento usa workflow
        if ($documento->workflow_id) {
            $workflowService = app(WorkflowService::class);
            
            if (!$workflowService->verificarPermissoes(auth()->user(), $documento, $acao)) {
                abort(403, 'Sem permissão para esta ação no fluxo atual');
            }
        } else {
            // Sistema atual: usar middleware existente
            // Mantém total compatibilidade
            return app('middleware.check.permission')->handle($request, $next, $acao);
        }
        
        return $next($request);
    }

    private function extrairDocumento($request): ?Model
    {
        // Buscar documento nos parâmetros de rota
        if ($request->route('proposicao')) {
            return $request->route('proposicao');
        }

        if ($request->route('documento')) {
            return $request->route('documento');
        }

        return null;
    }
}
```

## 5. Seeder para Workflow Padrão

```php
<?php
namespace Database\Seeders;

class WorkflowPadraoSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Workflow padrão parlamentar
            $workflow = Workflow::create([
                'nome' => 'Fluxo Parlamentar Padrão',
                'descricao' => 'Fluxo tradicional: Parlamentar → Legislativo → Assinatura → Protocolo → Expediente',
                'tipo_documento' => 'proposicao',
                'ativo' => true,
                'is_default' => true,
                'ordem' => 1,
                'configuracao' => [
                    'preservar_sistema_atual' => true,
                    'migrado_automaticamente' => true
                ]
            ]);

            // Etapas do fluxo
            $etapas = [
                [
                    'nome' => 'Criação pelo Parlamentar',
                    'role_responsavel' => 'parlamentar',
                    'ordem' => 1,
                    'permite_edicao' => true,
                    'acoes_possiveis' => ['enviar_para_legislativo', 'salvar_rascunho']
                ],
                [
                    'nome' => 'Análise Legislativa',
                    'role_responsavel' => 'legislativo', 
                    'ordem' => 2,
                    'permite_edicao' => true,
                    'acoes_possiveis' => ['aprovar', 'devolver', 'reprovar']
                ],
                [
                    'nome' => 'Assinatura do Autor',
                    'role_responsavel' => 'parlamentar',
                    'ordem' => 3,
                    'permite_assinatura' => true,
                    'acoes_possiveis' => ['assinar_documento']
                ],
                [
                    'nome' => 'Protocolo',
                    'role_responsavel' => 'protocolo',
                    'ordem' => 4,
                    'acoes_possiveis' => ['protocolar_documento']
                ],
                [
                    'nome' => 'Expediente',
                    'role_responsavel' => 'expediente',
                    'ordem' => 5,
                    'acoes_possiveis' => ['arquivar', 'encaminhar']
                ]
            ];

            foreach ($etapas as $etapaData) {
                $etapa = WorkflowEtapa::create(array_merge($etapaData, [
                    'workflow_id' => $workflow->id
                ]));

                // Criar transições
                if ($etapaData['ordem'] < count($etapas)) {
                    $proximaEtapa = WorkflowEtapa::where('workflow_id', $workflow->id)
                                                 ->where('ordem', $etapaData['ordem'] + 1)
                                                 ->first();
                    
                    // Transições principais
                    foreach ($etapaData['acoes_possiveis'] as $acao) {
                        if (in_array($acao, ['enviar_para_legislativo', 'aprovar', 'assinar_documento', 'protocolar_documento'])) {
                            WorkflowTransicao::create([
                                'workflow_id' => $workflow->id,
                                'etapa_origem_id' => $etapa->id,
                                'etapa_destino_id' => $proximaEtapa->id,
                                'acao' => $acao
                            ]);
                        }
                    }
                }

                // Transição de devolução (volta para parlamentar)
                if ($etapaData['role_responsavel'] === 'legislativo') {
                    $etapaParlamentar = WorkflowEtapa::where('workflow_id', $workflow->id)
                                                     ->where('ordem', 1)
                                                     ->first();
                    
                    WorkflowTransicao::create([
                        'workflow_id' => $workflow->id,
                        'etapa_origem_id' => $etapa->id,
                        'etapa_destino_id' => $etapaParlamentar->id,
                        'acao' => 'devolver'
                    ]);
                }
            }
        });

        $this->command->info('✅ Workflow padrão parlamentar criado com sucesso');
    }
}
```

## 6. Políticas de Autorização

```php
<?php
namespace App\Policies;

class WorkflowProposicaoPolicy
{
    public function workflowEnviarParaLegislativo(User $user, Proposicao $proposicao, WorkflowEtapa $etapa)
    {
        return $user->id === $proposicao->autor_id 
            && $proposicao->status === 'rascunho';
    }

    public function workflowAprovar(User $user, Proposicao $proposicao, WorkflowEtapa $etapa)
    {
        return $user->hasRole('legislativo') 
            && $proposicao->status === 'em_analise_legislativa';
    }

    public function workflowAssinarDocumento(User $user, Proposicao $proposicao, WorkflowEtapa $etapa)
    {
        return $user->id === $proposicao->autor_id 
            && $proposicao->status === 'aprovado_legislativo'
            && !$proposicao->assinado;
    }

    public function workflowProtocolarDocumento(User $user, Proposicao $proposicao, WorkflowEtapa $etapa)
    {
        return $user->hasRole('protocolo') 
            && $proposicao->assinado 
            && !$proposicao->numero_protocolo;
    }
}
```

## 7. Observabilidade e Jobs

```php
<?php
namespace App\Jobs;

class VerificarPrazosWorkflow implements ShouldQueue
{
    public function handle()
    {
        $atrasados = DocumentoWorkflowStatus::where('status', 'em_andamento')
            ->whereNotNull('prazo_atual')
            ->where('prazo_atual', '<', now())
            ->with(['documento', 'etapaAtual'])
            ->get();

        foreach ($atrasados as $status) {
            // Marcar como atrasado
            $dadosWorkflow = $status->dados_workflow ?? [];
            $dadosWorkflow['atrasado'] = true;
            $dadosWorkflow['atrasado_desde'] = now();

            $status->update(['dados_workflow' => $dadosWorkflow]);

            // Notificar responsáveis
            event(new WorkflowAtrasado($status->documento, $status->etapaAtual));
        }
    }
}
```

## 8. Contrato JSON do Designer Vue.js

```typescript
// types/workflow.ts
export interface WorkflowDefinition {
  nome: string;
  descricao: string;
  tipo_documento: string;
  etapas: EtapaDefinition[];
  transicoes: TransicaoDefinition[];
}

export interface EtapaDefinition {
  key: string;
  label: string;
  role_responsavel: string;
  ordem: number;
  tempo_limite_dias?: number;
  permite_edicao: boolean;
  permite_assinatura: boolean;
  acoes_possiveis: string[];
  posicao?: { x: number; y: number }; // Para designer visual
}

export interface TransicaoDefinition {
  from: string;
  to: string;
  acao: string;
  condicao?: Record<string, any>;
  automatica?: boolean;
}
```

## 9. Performance e Índices Otimizados

```sql
-- Índices de performance críticos

-- Busca rápida de documentos por workflow/status
CREATE INDEX idx_doc_workflow_status_ativo 
ON documento_workflow_status (workflow_id, status) 
WHERE status IN ('em_andamento', 'pausado');

-- Busca de documentos atrasados
CREATE INDEX idx_doc_workflow_prazo_vencido 
ON documento_workflow_status (prazo_atual, status) 
WHERE status = 'em_andamento' AND prazo_atual IS NOT NULL;

-- Histórico por documento (ordenado por data)
CREATE INDEX idx_historico_documento_data 
ON documento_workflow_historico (documento_type, documento_id, created_at DESC);

-- Transições por etapa origem (busca comum)
CREATE INDEX idx_transicoes_origem_acao 
ON workflow_transicoes (etapa_origem_id, acao);

-- Etapas por workflow ordenadas
CREATE INDEX idx_etapas_workflow_ordem 
ON workflow_etapas (workflow_id, ordem);
```

## 10. Resumo das Melhorias Implementadas

✅ **PostgreSQL Nativo**: Schema corrigido com jsonb, constraints e índices  
✅ **Lock Otimista**: Previne condições de corrida com campo `version`  
✅ **Transações ACID**: Toda operação crítica em transação  
✅ **Idempotência**: Suporte a chaves de idempotência  
✅ **Políticas Granulares**: Gates específicos por ação  
✅ **Avaliador de Condições**: DSL JSON para regras complexas  
✅ **Observabilidade**: Jobs para monitorar prazos  
✅ **Performance**: Índices otimizados para consultas comuns  
✅ **Compatibilidade**: 100% preservação do sistema atual  
✅ **Auditoria Completa**: Histórico detalhado de todas as ações  

**Próximo Passo**: Validar este adendo e implementar a Fase 1 com as correções aplicadas.