<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProposicaoApiController extends Controller
{
    /**
     * Get proposição with real-time data and optimized cache
     */
    public function show($id): JsonResponse
    {
        try {
            // Cache key baseado no ID da proposição e timestamp da última atualização
            $proposicao = $this->getProposicaoWithCache($id);

            // Verificar permissões
            if (!$this->canViewProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Formatar dados para resposta otimizada
            $data = $this->formatProposicaoResponse($proposicao);

            return response()->json([
                'success' => true,
                'proposicao' => $data,
                'timestamp' => now()->toISOString(),
                'cache_hit' => $this->wasCacheHit($id)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar proposição via API', [
                'proposicao_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar proposição',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Get proposição with intelligent cache
     */
    private function getProposicaoWithCache($id)
    {
        // Primeiro, verificar se existe no cache baseado na última modificação
        $lastModified = Cache::remember("proposicao_last_modified_{$id}", 60, function () use ($id) {
            return Proposicao::where('id', $id)
                ->value('updated_at');
        });

        $cacheKey = "proposicao_api_{$id}_{$lastModified}";
        
        return Cache::remember($cacheKey, 300, function () use ($id) { // Cache por 5 minutos
            return Proposicao::with([
                'autor:id,name,email'
            ])
            ->select([
                'id', 'tipo', 'ementa', 'conteudo', 'status', 
                'autor_id', 'template_id',
                'arquivo_path', 'arquivo_pdf_path', 'numero_protocolo',
                'created_at', 'updated_at', 'ultima_modificacao'
            ])
            ->findOrFail($id);
        });
    }

    /**
     * Format proposição response with optimized data
     */
    private function formatProposicaoResponse($proposicao): array
    {
        // Otimizar conteúdo - só enviar preview se muito grande
        $conteudo = $proposicao->conteudo;
        $conteudoPreview = null;
        
        if ($conteudo && strlen($conteudo) > 2000) {
            $conteudoPreview = substr($conteudo, 0, 500) . '...';
        }

        return [
            'id' => $proposicao->id,
            'tipo' => $proposicao->tipo,
            'ementa' => $proposicao->ementa,
            'conteudo' => $conteudo,
            'conteudo_preview' => $conteudoPreview,
            'conteudo_length' => strlen($conteudo ?? ''),
            'status' => $proposicao->status,
            'numero_protocolo' => $proposicao->numero_protocolo,
            'created_at' => $proposicao->created_at?->toISOString(),
            'updated_at' => $proposicao->updated_at?->toISOString(),
            'ultima_modificacao' => $proposicao->ultima_modificacao?->toISOString(),
            'autor' => [
                'id' => $proposicao->autor?->id,
                'name' => $proposicao->autor?->name,
                'email' => $proposicao->autor?->email
            ],
            'template_info' => [
                'template_id' => $proposicao->template_id,
                'tipo' => $proposicao->tipo
            ],
            'has_arquivo' => !empty($proposicao->arquivo_path),
            'has_pdf' => !empty($proposicao->arquivo_pdf_path),
            'permissions' => [
                'can_edit' => $this->canEditProposicao($proposicao),
                'can_sign' => $this->canSignProposicao($proposicao),
                'can_view_content' => $this->canViewContent($proposicao),
                'can_send_legislative' => $this->canSendToLegislative($proposicao),
                'can_update_status' => $this->canUpdateStatusAPI($proposicao)
            ],
            'meta' => [
                'word_count' => str_word_count(strip_tags($conteudo ?? '')),
                'char_count' => strlen($conteudo ?? ''),
                'has_content' => !empty($conteudo),
                'is_complete' => !empty($proposicao->ementa) && !empty($conteudo)
            ]
        ];
    }

    /**
     * Check if response came from cache
     */
    private function wasCacheHit($id): bool
    {
        // Verificar se existe no cache baseado na última modificação
        $lastModified = Cache::get("proposicao_last_modified_{$id}");
        if ($lastModified) {
            $cacheKey = "proposicao_api_{$id}_{$lastModified}";
            return Cache::has($cacheKey);
        }
        return false;
    }

    /**
     * Update proposição status
     */
    public function updateStatus($id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|string|in:rascunho,em_edicao,enviado_legislativo,em_revisao,aguardando_aprovacao_autor,devolvido_edicao,retornado_legislativo,aprovado,reprovado'
            ]);

            $proposicao = Proposicao::findOrFail($id);

            // Verificar permissões para alterar status
            if (!$this->canUpdateStatus($proposicao, $request->status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para alterar este status'
                ], 403);
            }

            $oldStatus = $proposicao->status;
            $proposicao->update([
                'status' => $request->status,
                'ultima_modificacao' => now()
            ]);

            // Limpar cache
            Cache::forget("proposicao_api_{$id}");

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get proposição status history/changes
     */
    public function statusHistory($id): JsonResponse
    {
        try {
            $proposicao = Proposicao::findOrFail($id);

            if (!$this->canViewProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Simulação do histórico de status (poderia vir de uma tabela de log)
            $history = [
                [
                    'status' => 'rascunho',
                    'timestamp' => $proposicao->created_at->toISOString(),
                    'user' => $proposicao->autor?->name,
                    'description' => 'Proposição criada'
                ]
            ];

            if ($proposicao->status !== 'rascunho') {
                $history[] = [
                    'status' => $proposicao->status,
                    'timestamp' => $proposicao->updated_at->toISOString(),
                    'user' => Auth::user()->name ?? 'Sistema',
                    'description' => 'Status alterado para ' . $this->getStatusLabel($proposicao->status)
                ];
            }

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar histórico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Real-time updates endpoint
     */
    public function updates($id, Request $request): JsonResponse
    {
        try {
            $lastUpdate = $request->get('last_update');
            $proposicao = Proposicao::findOrFail($id);

            if (!$this->canViewProposicao($proposicao)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso negado'
                ], 403);
            }

            // Verificar se houve atualizações desde o último check
            $hasUpdates = false;
            if ($lastUpdate) {
                $lastUpdateTime = \Carbon\Carbon::parse($lastUpdate);
                $hasUpdates = $proposicao->updated_at > $lastUpdateTime;
            }

            return response()->json([
                'success' => true,
                'has_updates' => $hasUpdates,
                'current_status' => $proposicao->status,
                'last_modified' => $proposicao->updated_at->toISOString(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar atualizações',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can view proposição
     */
    private function canViewProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Simplificado: usuário logado pode ver suas próprias proposições
        // e também pode ver outras se for admin ou legislativo (baseado no email)
        
        // Autor pode ver sua proposição
        if ($proposicao->autor_id === $user->id) return true;

        // Admin pode ver tudo (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode ver proposições enviadas para análise (baseado no email/nome)
        if ((str_contains($user->email, 'legislativo') || str_contains($user->name, 'legislativo') || str_contains($user->email, 'joao')) && 
            in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'aprovado', 'reprovado'])) {
            return true;
        }

        // Para desenvolvimento: permitir acesso geral para usuários logados
        return true;
    }

    /**
     * Check if user can edit proposição
     */
    private function canEditProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Admin pode editar tudo (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Autor pode editar se status permitir
        if ($proposicao->autor_id === $user->id && in_array($proposicao->status, [
            'rascunho', 'em_edicao', 'devolvido_edicao'
        ])) {
            return true;
        }

        // Legislativo pode editar durante revisão (baseado no email)
        if ((str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) && 
            $proposicao->status === 'em_revisao') {
            return true;
        }

        return false;
    }

    /**
     * Check if user can sign proposição
     */
    private function canSignProposicao(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Apenas proposições aprovadas podem ser assinadas
        if ($proposicao->status !== 'aprovado') return false;

        // Parlamentares e admin podem assinar (baseado no email)
        return str_contains($user->email, 'parlamentar') || 
               str_contains($user->email, 'admin') || 
               str_contains($user->email, 'jessica') ||
               str_contains($user->email, 'bruno') ||
               $proposicao->autor_id === $user->id;
    }

    /**
     * Check if user can view full content
     */
    private function canViewContent(Proposicao $proposicao): bool
    {
        // Por enquanto, mesma regra do canViewProposicao
        return $this->canViewProposicao($proposicao);
    }

    /**
     * Check if user can update status to specific value
     */
    private function canUpdateStatus(Proposicao $proposicao, string $newStatus): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Admin pode alterar qualquer status (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode aprovar/reprovar/devolver (baseado no email)
        if (str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) {
            $allowedStatuses = ['em_revisao', 'aprovado', 'reprovado', 'devolvido_edicao'];
            return in_array($newStatus, $allowedStatuses);
        }

        // Autor pode alterar para em_edicao ou enviado_legislativo
        if ($proposicao->autor_id === $user->id) {
            $allowedStatuses = ['em_edicao', 'enviado_legislativo'];
            return in_array($newStatus, $allowedStatuses);
        }

        return false;
    }

    /**
     * Get status label in Portuguese
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'rascunho' => 'Rascunho',
            'em_edicao' => 'Em Edição',
            'enviado_legislativo' => 'Enviado ao Legislativo',
            'em_revisao' => 'Em Revisão',
            'aguardando_aprovacao_autor' => 'Aguardando Aprovação do Autor',
            'devolvido_edicao' => 'Devolvido para Edição',
            'retornado_legislativo' => 'Retornado do Legislativo',
            'aprovado' => 'Aprovado',
            'reprovado' => 'Reprovado'
        ];

        return $labels[$status] ?? 'Status Desconhecido';
    }

    /**
     * Check if user can send to legislative
     */
    private function canSendToLegislative(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Autor pode enviar se status permitir e tiver conteúdo mínimo
        if ($proposicao->autor_id === $user->id) {
            return in_array($proposicao->status, ['rascunho', 'em_edicao', 'devolvido_edicao']) &&
                   !empty($proposicao->ementa) && !empty($proposicao->conteudo);
        }

        // Admin sempre pode (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can update status via API
     */
    private function canUpdateStatusAPI(Proposicao $proposicao): bool
    {
        $user = Auth::user();
        
        if (!$user) return false;

        // Admin pode alterar qualquer status (baseado no email)
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'bruno')) {
            return true;
        }

        // Legislativo pode alterar status durante revisão (baseado no email)
        if ((str_contains($user->email, 'legislativo') || str_contains($user->email, 'joao')) && 
            $proposicao->status === 'em_revisao') {
            return true;
        }

        return false;
    }

    /**
     * Clear proposição cache when updated
     */
    public static function clearProposicaoCache($proposicaoId)
    {
        $patterns = [
            "proposicao_api_{$proposicaoId}",
            "proposicao_api_{$proposicaoId}_*", 
            "proposicao_last_modified_{$proposicaoId}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        // Se tiver implementação de cache com tags, usar aqui
        // Cache::tags(['proposicao', "proposicao_{$proposicaoId}"])->flush();
    }
}