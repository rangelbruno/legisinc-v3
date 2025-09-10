<?php

namespace App\Services\Workflow;

use App\Models\{Workflow, WorkflowEtapa, WorkflowTransicao};
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Str;

class WorkflowManagerService
{
    /**
     * Cria um novo workflow a partir de dados JSON (Designer)
     */
    public function criarWorkflow(array $dados): Workflow
    {
        return DB::transaction(function () use ($dados) {
            $wf = Workflow::create([
                'nome' => $dados['nome'],
                'descricao' => $dados['descricao'] ?? null,
                'tipo_documento' => $dados['tipo_documento'],
                'ativo' => $dados['ativo'] ?? false, // Inativo por padrão até ser publicado
                'configuracao' => $dados['configuracao'] ?? []
            ]);

            // 🎯 FASE 1: Criar etapas e indexar por key
            $map = []; // key => id
            foreach ($dados['etapas'] as $i => $e) {
                $etapa = WorkflowEtapa::create([
                    'workflow_id' => $wf->id,
                    'key' => $e['key'] ?? Str::slug($e['label'] ?? $e['nome'] ?? 'etapa-'.$i),
                    'nome' => $e['label'] ?? $e['nome'] ?? ('Etapa '.($i+1)),
                    'descricao' => $e['descricao'] ?? null,
                    'role_responsavel' => $e['role_responsavel'] ?? null,
                    'ordem' => $e['ordem'] ?? ($i+1),
                    'tempo_limite_dias' => $e['tempo_limite_dias'] ?? null,
                    'permite_edicao' => (bool)($e['permite_edicao'] ?? false),
                    'permite_assinatura' => (bool)($e['permite_assinatura'] ?? false),
                    'requer_aprovacao' => (bool)($e['requer_aprovacao'] ?? false),
                    'acoes_possiveis' => $e['acoes_possiveis'] ?? [],
                    'condicoes' => $e['condicoes'] ?? null,
                ]);
                $map[$etapa->key] = $etapa->id;
            }

            // 🎯 FASE 2: Criar transições usando as keys mapeadas
            foreach ($dados['transicoes'] ?? [] as $t) {
                $from = $map[$t['from']] ?? null;
                $to   = $map[$t['to']] ?? null;
                if (!$from || !$to) {
                    Log::warning('Transição ignorada por keys inválidas', [
                        'workflow' => $wf->nome,
                        'transicao' => $t
                    ]);
                    continue; // Skip invalid transitions
                }

                WorkflowTransicao::create([
                    'workflow_id' => $wf->id,
                    'etapa_origem_id' => $from,
                    'etapa_destino_id' => $to,
                    'acao' => $t['acao'],
                    'condicao' => $t['condicao'] ?? null,
                    'automatica' => (bool)($t['automatica'] ?? false),
                ]);
            }

            Log::info('Workflow criado', [
                'workflow' => $wf->nome,
                'etapas' => count($dados['etapas']),
                'transicoes' => count($dados['transicoes'] ?? [])
            ]);

            return $wf;
        });
    }

    /**
     * Duplica um workflow existente
     */
    public function duplicarWorkflow(int $workflowId, string $novoNome): Workflow
    {
        return DB::transaction(function () use ($workflowId, $novoNome) {
            $workflowOriginal = Workflow::with(['etapas', 'transicoes'])
                                      ->findOrFail($workflowId);
            
            // Duplicar workflow principal
            $novoWorkflow = $workflowOriginal->replicate();
            $novoWorkflow->nome = $novoNome;
            $novoWorkflow->is_default = false; // Nunca herda padrão
            $novoWorkflow->ativo = false; // Inativo até ser publicado
            $novoWorkflow->save();

            // Duplicar etapas e mapear IDs
            $mapeamentoEtapas = [];
            foreach ($workflowOriginal->etapas as $etapaOriginal) {
                $novaEtapa = $etapaOriginal->replicate();
                $novaEtapa->workflow_id = $novoWorkflow->id;
                $novaEtapa->save();
                
                $mapeamentoEtapas[$etapaOriginal->id] = $novaEtapa->id;
            }

            // Duplicar transições com novos IDs
            foreach ($workflowOriginal->transicoes as $transicaoOriginal) {
                $novaTransicao = $transicaoOriginal->replicate();
                $novaTransicao->workflow_id = $novoWorkflow->id;
                $novaTransicao->etapa_origem_id = $mapeamentoEtapas[$transicaoOriginal->etapa_origem_id];
                $novaTransicao->etapa_destino_id = $mapeamentoEtapas[$transicaoOriginal->etapa_destino_id];
                $novaTransicao->save();
            }

            Log::info('Workflow duplicado', [
                'original' => $workflowOriginal->nome,
                'novo' => $novoNome,
                'etapas' => count($mapeamentoEtapas),
                'transicoes' => $workflowOriginal->transicoes->count()
            ]);

            return $novoWorkflow;
        });
    }

    /**
     * Ativa ou desativa um workflow
     */
    public function ativarDesativarWorkflow(int $workflowId, bool $ativo): void
    {
        DB::transaction(function () use ($workflowId, $ativo) {
            $workflow = Workflow::findOrFail($workflowId);
            
            // Se desativando, verificar se não há documentos em andamento
            if (!$ativo && $workflow->temDocumentosEmUso()) {
                throw new \Exception('Não é possível desativar workflow com documentos em andamento');
            }
            
            $workflow->update(['ativo' => $ativo]);

            Log::info('Workflow ' . ($ativo ? 'ativado' : 'desativado'), [
                'workflow' => $workflow->nome
            ]);
        });
    }

    /**
     * Define um workflow como padrão para um tipo de documento
     */
    public function definirWorkflowPadrao(int $workflowId, string $tipoDocumento): void
    {
        DB::transaction(function () use ($workflowId, $tipoDocumento) {
            // Remover padrão atual do tipo
            Workflow::where('tipo_documento', $tipoDocumento)
                   ->update(['is_default' => false]);
            
            // Definir novo padrão
            $workflow = Workflow::findOrFail($workflowId);
            $workflow->update([
                'is_default' => true,
                'ativo' => true // Workflow padrão deve estar ativo
            ]);

            Log::info('Workflow definido como padrão', [
                'workflow' => $workflow->nome,
                'tipo_documento' => $tipoDocumento
            ]);
        });
    }

    /**
     * Atualiza um workflow existente
     */
    public function atualizarWorkflow(int $workflowId, array $dados): Workflow
    {
        return DB::transaction(function () use ($workflowId, $dados) {
            $workflow = Workflow::with(['etapas', 'transicoes'])->findOrFail($workflowId);

            // Verificar se pode ser editado
            if ($workflow->temDocumentosEmUso()) {
                throw new \Exception('Não é possível editar workflow com documentos em andamento. Duplique o workflow para criar uma nova versão.');
            }

            // Atualizar dados básicos
            $workflow->update([
                'nome' => $dados['nome'] ?? $workflow->nome,
                'descricao' => $dados['descricao'] ?? $workflow->descricao,
                'configuracao' => $dados['configuracao'] ?? $workflow->configuracao
            ]);

            // Se fornecidas etapas/transições, recriar completamente
            if (isset($dados['etapas'])) {
                // Remover etapas e transições existentes
                $workflow->transicoes()->delete();
                $workflow->etapas()->delete();

                // Recriar usando mesmo processo do criarWorkflow
                $map = [];
                foreach ($dados['etapas'] as $i => $e) {
                    $etapa = WorkflowEtapa::create([
                        'workflow_id' => $workflow->id,
                        'key' => $e['key'] ?? Str::slug($e['nome'] ?? 'etapa-'.$i),
                        'nome' => $e['nome'] ?? ('Etapa '.($i+1)),
                        'descricao' => $e['descricao'] ?? null,
                        'role_responsavel' => $e['role_responsavel'] ?? null,
                        'ordem' => $e['ordem'] ?? ($i+1),
                        'tempo_limite_dias' => $e['tempo_limite_dias'] ?? null,
                        'permite_edicao' => (bool)($e['permite_edicao'] ?? false),
                        'permite_assinatura' => (bool)($e['permite_assinatura'] ?? false),
                        'requer_aprovacao' => (bool)($e['requer_aprovacao'] ?? false),
                        'acoes_possiveis' => $e['acoes_possiveis'] ?? [],
                        'condicoes' => $e['condicoes'] ?? null,
                    ]);
                    $map[$etapa->key] = $etapa->id;
                }

                // Recriar transições
                foreach ($dados['transicoes'] ?? [] as $t) {
                    $from = $map[$t['from']] ?? null;
                    $to   = $map[$t['to']] ?? null;
                    if ($from && $to) {
                        WorkflowTransicao::create([
                            'workflow_id' => $workflow->id,
                            'etapa_origem_id' => $from,
                            'etapa_destino_id' => $to,
                            'acao' => $t['acao'],
                            'condicao' => $t['condicao'] ?? null,
                            'automatica' => (bool)($t['automatica'] ?? false),
                        ]);
                    }
                }
            }

            Log::info('Workflow atualizado', [
                'workflow' => $workflow->nome
            ]);

            return $workflow->fresh(['etapas', 'transicoes']);
        });
    }

    /**
     * Remove um workflow (soft delete se houver histórico)
     */
    public function removerWorkflow(int $workflowId): void
    {
        DB::transaction(function () use ($workflowId) {
            $workflow = Workflow::findOrFail($workflowId);

            // Verificar se pode ser removido
            if ($workflow->temDocumentosEmUso()) {
                throw new \Exception('Não é possível remover workflow com documentos em andamento');
            }

            // Se há histórico, apenas desativar
            $temHistorico = $workflow->historico()->exists();
            
            if ($temHistorico) {
                $workflow->update([
                    'ativo' => false,
                    'nome' => $workflow->nome . ' (Arquivado)'
                ]);
                
                Log::info('Workflow arquivado (tem histórico)', [
                    'workflow' => $workflow->nome
                ]);
            } else {
                // Sem histórico, pode remover completamente
                $workflow->transicoes()->delete();
                $workflow->etapas()->delete();
                $workflow->delete();

                Log::info('Workflow removido completamente', [
                    'workflow' => $workflow->nome
                ]);
            }
        });
    }

    /**
     * Lista workflows disponíveis para um tipo de documento
     */
    public function listarWorkflowsPorTipo(string $tipoDocumento): \Illuminate\Database\Eloquent\Collection
    {
        return Workflow::tipoDocumento($tipoDocumento)
            ->ativo()
            ->with(['etapas'])
            ->orderBy('is_default', 'desc')
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();
    }

    /**
     * Obtém workflow padrão para um tipo de documento
     */
    public function obterWorkflowPadrao(string $tipoDocumento): ?Workflow
    {
        return Workflow::tipoDocumento($tipoDocumento)
            ->padrao()
            ->ativo()
            ->with(['etapas'])
            ->first();
    }

    /**
     * Valida se um workflow está bem formado
     */
    public function validarWorkflow(Workflow $workflow): array
    {
        $erros = [];

        // Deve ter pelo menos uma etapa
        if ($workflow->etapas->count() === 0) {
            $erros[] = 'Workflow deve ter pelo menos uma etapa';
        }

        // Todas as etapas devem ter ordem única
        $ordens = $workflow->etapas->pluck('ordem')->toArray();
        if (count($ordens) !== count(array_unique($ordens))) {
            $erros[] = 'Etapas devem ter ordens únicas';
        }

        // Todas as transições devem ter etapas válidas
        foreach ($workflow->transicoes as $transicao) {
            $origemValida = $workflow->etapas->contains('id', $transicao->etapa_origem_id);
            $destinoValido = $workflow->etapas->contains('id', $transicao->etapa_destino_id);
            
            if (!$origemValida || !$destinoValido) {
                $erros[] = "Transição inválida: {$transicao->acao}";
            }
        }

        // Deve existir pelo menos um caminho da primeira para última etapa
        if ($workflow->etapas->count() > 1 && !$this->existeCaminhoCompleto($workflow)) {
            $erros[] = 'Não existe caminho completo entre primeira e última etapa';
        }

        return $erros;
    }

    /**
     * Verifica se existe caminho completo no workflow
     */
    private function existeCaminhoCompleto(Workflow $workflow): bool
    {
        $primeira = $workflow->etapas->sortBy('ordem')->first();
        $ultima = $workflow->etapas->sortBy('ordem')->last();

        if (!$primeira || !$ultima || $primeira->id === $ultima->id) {
            return true;
        }

        // BFS para verificar conectividade
        $visitados = [];
        $fila = [$primeira->id];
        
        while (!empty($fila)) {
            $atual = array_shift($fila);
            
            if ($atual === $ultima->id) {
                return true;
            }
            
            if (isset($visitados[$atual])) {
                continue;
            }
            
            $visitados[$atual] = true;
            
            // Adicionar próximas etapas
            $proximas = $workflow->transicoes
                ->where('etapa_origem_id', $atual)
                ->pluck('etapa_destino_id');
                
            foreach ($proximas as $proxima) {
                if (!isset($visitados[$proxima])) {
                    $fila[] = $proxima;
                }
            }
        }

        return false;
    }
}