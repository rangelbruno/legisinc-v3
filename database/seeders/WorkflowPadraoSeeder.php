<?php

namespace Database\Seeders;

use App\Models\{Workflow, WorkflowEtapa, WorkflowTransicao};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkflowPadraoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 🏛️ Workflow Parlamentar Padrão (preserva fluxo existente)
            $workflowParlamentar = Workflow::create([
                'nome' => 'Fluxo Parlamentar Padrão',
                'descricao' => 'Fluxo tradicional: Parlamentar → Legislativo → Assinatura → Protocolo → Expediente',
                'tipo_documento' => 'proposicao',
                'ativo' => true,
                'is_default' => true,
                'ordem' => 1,
                'configuracao' => [
                    'permite_edicao_legislativo' => true,
                    'requer_aprovacao_autor' => true,
                    'gera_pdf_automatico' => true,
                    'permite_assinatura_digital' => true
                ]
            ]);

            // 🎯 ETAPA 1: Elaboração (Parlamentar)
            $etapaElaboracao = WorkflowEtapa::create([
                'workflow_id' => $workflowParlamentar->id,
                'key' => 'elaboracao',
                'nome' => 'Elaboração',
                'descricao' => 'Criação e edição da proposição pelo parlamentar',
                'role_responsavel' => 'Parlamentar',
                'ordem' => 1,
                'tempo_limite_dias' => null,
                'permite_edicao' => true,
                'permite_assinatura' => false,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['enviar_legislativo', 'salvar_rascunho'],
                'condicoes' => null
            ]);

            // 🎯 ETAPA 2: Análise Legislativa
            $etapaLegislativo = WorkflowEtapa::create([
                'workflow_id' => $workflowParlamentar->id,
                'key' => 'analise_legislativa',
                'nome' => 'Análise Legislativa',
                'descricao' => 'Revisão técnica e jurídica da proposição',
                'role_responsavel' => 'Legislativo',
                'ordem' => 2,
                'tempo_limite_dias' => 5,
                'permite_edicao' => true,
                'permite_assinatura' => false,
                'requer_aprovacao' => true,
                'acoes_possiveis' => ['aprovar', 'devolver', 'solicitar_alteracoes'],
                'condicoes' => null
            ]);

            // 🎯 ETAPA 3: Aguardando Assinatura
            $etapaAssinatura = WorkflowEtapa::create([
                'workflow_id' => $workflowParlamentar->id,
                'key' => 'aguardando_assinatura',
                'nome' => 'Aguardando Assinatura',
                'descricao' => 'Proposição aguardando assinatura do autor',
                'role_responsavel' => 'Parlamentar',
                'ordem' => 3,
                'tempo_limite_dias' => 3,
                'permite_edicao' => false,
                'permite_assinatura' => true,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['assinar', 'devolver_edicao'],
                'condicoes' => [
                    'field' => 'status',
                    'op' => '=',
                    'value' => 'aprovado_assinatura'
                ]
            ]);

            // 🎯 ETAPA 4: Protocolo
            $etapaProtocolo = WorkflowEtapa::create([
                'workflow_id' => $workflowParlamentar->id,
                'key' => 'protocolo',
                'nome' => 'Protocolo',
                'descricao' => 'Atribuição de número oficial e registro',
                'role_responsavel' => 'Protocolo',
                'ordem' => 4,
                'tempo_limite_dias' => 1,
                'permite_edicao' => false,
                'permite_assinatura' => false,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['protocolar', 'devolver'],
                'condicoes' => [
                    'all' => [
                        ['field' => 'assinado', 'op' => '=', 'value' => true],
                        ['field' => 'arquivo_pdf_path', 'op' => 'exists']
                    ]
                ]
            ]);

            // 🎯 ETAPA 5: Expediente (Final)
            $etapaExpediente = WorkflowEtapa::create([
                'workflow_id' => $workflowParlamentar->id,
                'key' => 'expediente',
                'nome' => 'Expediente',
                'descricao' => 'Distribuição e arquivamento da proposição',
                'role_responsavel' => 'Expediente',
                'ordem' => 5,
                'tempo_limite_dias' => 2,
                'permite_edicao' => false,
                'permite_assinatura' => false,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['finalizar', 'arquivar'],
                'condicoes' => [
                    'field' => 'numero_proposicao',
                    'op' => 'exists'
                ]
            ]);

            // 🔄 TRANSIÇÕES DO FLUXO PARLAMENTAR

            // Elaboração → Análise Legislativa
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaElaboracao->id,
                'etapa_destino_id' => $etapaLegislativo->id,
                'acao' => 'enviar_legislativo',
                'condicao' => [
                    'all' => [
                        ['field' => 'ementa', 'op' => 'is_not_empty'],
                        ['field' => 'texto', 'op' => 'is_not_empty']
                    ]
                ],
                'automatica' => false
            ]);

            // Análise Legislativa → Assinatura (Aprovada)
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaLegislativo->id,
                'etapa_destino_id' => $etapaAssinatura->id,
                'acao' => 'aprovar',
                'condicao' => null,
                'automatica' => false
            ]);

            // Análise Legislativa → Elaboração (Devolvida)
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaLegislativo->id,
                'etapa_destino_id' => $etapaElaboracao->id,
                'acao' => 'devolver',
                'condicao' => null,
                'automatica' => false
            ]);

            // Assinatura → Protocolo
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaAssinatura->id,
                'etapa_destino_id' => $etapaProtocolo->id,
                'acao' => 'assinar',
                'condicao' => null,
                'automatica' => false
            ]);

            // Assinatura → Elaboração (Devolver para edição)
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaAssinatura->id,
                'etapa_destino_id' => $etapaElaboracao->id,
                'acao' => 'devolver_edicao',
                'condicao' => null,
                'automatica' => false
            ]);

            // Protocolo → Expediente
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaProtocolo->id,
                'etapa_destino_id' => $etapaExpediente->id,
                'acao' => 'protocolar',
                'condicao' => null,
                'automatica' => false
            ]);

            // Protocolo → Assinatura (Devolver)
            WorkflowTransicao::create([
                'workflow_id' => $workflowParlamentar->id,
                'etapa_origem_id' => $etapaProtocolo->id,
                'etapa_destino_id' => $etapaAssinatura->id,
                'acao' => 'devolver',
                'condicao' => null,
                'automatica' => false
            ]);

            $this->command->info('✅ Workflow Parlamentar Padrão criado com sucesso');

            // 🏛️ Workflow Simplificado (Alternativo)
            $workflowSimplificado = Workflow::create([
                'nome' => 'Fluxo Simplificado',
                'descricao' => 'Fluxo direto: Parlamentar → Protocolo → Expediente',
                'tipo_documento' => 'proposicao',
                'ativo' => true,
                'is_default' => false,
                'ordem' => 2,
                'configuracao' => [
                    'permite_edicao_legislativo' => false,
                    'requer_aprovacao_autor' => false,
                    'gera_pdf_automatico' => true,
                    'permite_assinatura_digital' => true
                ]
            ]);

            // Etapas do Fluxo Simplificado
            $etapaElaboracaoSimp = WorkflowEtapa::create([
                'workflow_id' => $workflowSimplificado->id,
                'key' => 'elaboracao_simp',
                'nome' => 'Elaboração',
                'descricao' => 'Criação da proposição',
                'role_responsavel' => 'Parlamentar',
                'ordem' => 1,
                'tempo_limite_dias' => null,
                'permite_edicao' => true,
                'permite_assinatura' => true,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['enviar_protocolo', 'salvar_rascunho'],
                'condicoes' => null
            ]);

            $etapaProtocoloSimp = WorkflowEtapa::create([
                'workflow_id' => $workflowSimplificado->id,
                'key' => 'protocolo_simp',
                'nome' => 'Protocolo',
                'descricao' => 'Registro oficial',
                'role_responsavel' => 'Protocolo',
                'ordem' => 2,
                'tempo_limite_dias' => 1,
                'permite_edicao' => false,
                'permite_assinatura' => false,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['protocolar'],
                'condicoes' => null
            ]);

            $etapaExpedienteSimp = WorkflowEtapa::create([
                'workflow_id' => $workflowSimplificado->id,
                'key' => 'expediente_simp',
                'nome' => 'Expediente',
                'descricao' => 'Finalização',
                'role_responsavel' => 'Expediente',
                'ordem' => 3,
                'tempo_limite_dias' => 1,
                'permite_edicao' => false,
                'permite_assinatura' => false,
                'requer_aprovacao' => false,
                'acoes_possiveis' => ['finalizar'],
                'condicoes' => null
            ]);

            // Transições do Fluxo Simplificado
            WorkflowTransicao::create([
                'workflow_id' => $workflowSimplificado->id,
                'etapa_origem_id' => $etapaElaboracaoSimp->id,
                'etapa_destino_id' => $etapaProtocoloSimp->id,
                'acao' => 'enviar_protocolo',
                'condicao' => [
                    'all' => [
                        ['field' => 'ementa', 'op' => 'is_not_empty'],
                        ['field' => 'assinado', 'op' => '=', 'value' => true]
                    ]
                ],
                'automatica' => false
            ]);

            WorkflowTransicao::create([
                'workflow_id' => $workflowSimplificado->id,
                'etapa_origem_id' => $etapaProtocoloSimp->id,
                'etapa_destino_id' => $etapaExpedienteSimp->id,
                'acao' => 'protocolar',
                'condicao' => null,
                'automatica' => false
            ]);

            $this->command->info('✅ Workflow Simplificado criado com sucesso');

            // 📊 Estatísticas
            $this->command->info("📊 Workflows criados:");
            $this->command->info("  • Workflow Parlamentar Padrão: {$workflowParlamentar->etapas()->count()} etapas, {$workflowParlamentar->transicoes()->count()} transições");
            $this->command->info("  • Workflow Simplificado: {$workflowSimplificado->etapas()->count()} etapas, {$workflowSimplificado->transicoes()->count()} transições");
        });
    }
}