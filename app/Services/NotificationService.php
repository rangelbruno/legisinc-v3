<?php

namespace App\Services;

use App\Models\User;
use App\Models\Proposicao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Obter notificações do usuário baseado no seu perfil
     */
    public function getNotificationsForUser(?User $user = null): array
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return [];
        }

        // Cache de notificações por 30 segundos para evitar múltiplas queries em requisições próximas
        $cacheKey = 'user_notifications_' . $user->id;

        return Cache::remember($cacheKey, 30, function () use ($user) {
            $notifications = [];

            // Notificações para Parlamentar
            if ($user->isParlamentar()) {
                $notifications = array_merge($notifications, $this->getParlamentarNotifications($user));
            }

            // Notificações para Legislativo
            if ($user->isLegislativo()) {
                $notifications = array_merge($notifications, $this->getLegislativoNotifications($user));
            }

            // Notificações para Protocolo
            if ($user->isProtocolo()) {
                $notifications = array_merge($notifications, $this->getProtocoloNotifications($user));
            }

            // Notificações para Admin
            if ($user->isAdmin()) {
                $notifications = array_merge($notifications, $this->getAdminNotifications($user));
            }

            // Ordenar por prioridade e data
            usort($notifications, function ($a, $b) {
                if ($a['priority'] === $b['priority']) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                }
                return $this->getPriorityWeight($b['priority']) - $this->getPriorityWeight($a['priority']);
            });

            return $notifications;
        });
    }

    /**
     * Contar total de notificações não lidas
     */
    public function getUnreadCount(?User $user = null): int
    {
        return count($this->getNotificationsForUser($user));
    }

    /**
     * Verificar se há notificações urgentes
     */
    public function hasUrgentNotifications(?User $user = null): bool
    {
        $notifications = $this->getNotificationsForUser($user);

        foreach ($notifications as $notification) {
            if ($notification['priority'] === 'urgent') {
                return true;
            }
        }

        return false;
    }

    /**
     * Notificações específicas para Parlamentar
     * OTIMIZADO: Reduz queries usando uma única query com múltiplos filtros
     */
    private function getParlamentarNotifications(User $user): array
    {
        $notifications = [];

        // OTIMIZAÇÃO: Fazer uma única query com group by para contar múltiplos status
        $statusCounts = Proposicao::where('autor_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->whereIn('status', ['retornado_legislativo', 'salvando'])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Proposições retornadas do legislativo
        $proposicoesRetornadas = $statusCounts['retornado_legislativo'] ?? 0;
        if ($proposicoesRetornadas > 0) {
            $notifications[] = [
                'id' => 'proposicoes_retornadas',
                'type' => 'proposicao',
                'title' => 'Proposições Retornadas',
                'message' => "Você tem {$proposicoesRetornadas} proposição(ões) retornada(s) do legislativo",
                'icon' => 'ki-information',
                'color' => 'warning',
                'priority' => 'urgent',
                'url' => route('proposicoes.minhas-proposicoes'),
                'created_at' => now()->toDateTimeString(),
                'count' => $proposicoesRetornadas
            ];
        }

        // Proposições em salvando há mais de 1 dia (use cache adicional para query complexa)
        $cacheKeySalvando = 'proposicoes_salvando_antigas_' . $user->id;
        $proposicoesSalvando = Cache::remember($cacheKeySalvando, 60, function () use ($user) {
            return Proposicao::where('autor_id', $user->id)
                ->where('status', 'salvando')
                ->where('updated_at', '<', now()->subDays(1))
                ->count();
        });

        if ($proposicoesSalvando > 0) {
            $notifications[] = [
                'id' => 'proposicoes_salvando',
                'type' => 'proposicao',
                'title' => 'Proposições em Salvamento',
                'message' => "Você tem {$proposicoesSalvando} proposição(ões) em processo de salvamento",
                'icon' => 'ki-time',
                'color' => 'info',
                'priority' => 'low',
                'url' => route('proposicoes.minhas-proposicoes'),
                'created_at' => now()->toDateTimeString(),
                'count' => $proposicoesSalvando
            ];
        }

        return $notifications;
    }

    /**
     * Notificações específicas para Legislativo
     * OTIMIZADO: Usando cache e queries agrupadas
     */
    private function getLegislativoNotifications(User $user): array
    {
        $notifications = [];

        // Cache para queries do legislativo (60 segundos)
        $cacheLegislativo = 'legislativo_notifications_data';

        $data = Cache::remember($cacheLegislativo, 60, function () {
            // Uma única query para obter dados agregados
            return [
                'para_revisar' => Proposicao::where('status', 'enviado_legislativo')->count(),
                'atrasadas' => Proposicao::where('status', 'enviado_legislativo')
                    ->where('updated_at', '<', now()->subDays(3))
                    ->count()
            ];
        });

        // Proposições para revisão
        if ($data['para_revisar'] > 0) {
            $notifications[] = [
                'id' => 'proposicoes_revisar',
                'type' => 'proposicao',
                'title' => 'Proposições para Revisão',
                'message' => "Existem {$data['para_revisar']} proposição(ões) aguardando revisão legislativa",
                'icon' => 'ki-document',
                'color' => 'primary',
                'priority' => 'high',
                'url' => route('proposicoes.legislativo.index'),
                'created_at' => now()->toDateTimeString(),
                'count' => $data['para_revisar']
            ];
        }

        // Proposições atrasadas
        if ($data['atrasadas'] > 0) {
            $notifications[] = [
                'id' => 'proposicoes_atrasadas',
                'type' => 'proposicao',
                'title' => 'Revisões Atrasadas',
                'message' => "Existem {$data['atrasadas']} proposição(ões) aguardando revisão há mais de 3 dias",
                'icon' => 'ki-time',
                'color' => 'warning',
                'priority' => 'medium',
                'url' => route('proposicoes.legislativo.index'),
                'created_at' => now()->toDateTimeString(),
                'count' => $data['atrasadas']
            ];
        }

        return $notifications;
    }

    /**
     * Notificações específicas para Protocolo
     * OTIMIZADO: Cache global para estatísticas
     */
    private function getProtocoloNotifications(User $user): array
    {
        $notifications = [];

        // Cache global de estatísticas (5 minutos)
        $totalProposicoes = Cache::remember('total_proposicoes_sistema', 300, function () {
            return Proposicao::count();
        });

        if ($totalProposicoes > 0) {
            $notifications[] = [
                'id' => 'protocolo_estatisticas',
                'type' => 'system',
                'title' => 'Visão Geral - Protocolo',
                'message' => "Sistema com {$totalProposicoes} proposição(ões) registrada(s) para controle",
                'icon' => 'ki-folder',
                'color' => 'info',
                'priority' => 'low',
                'url' => route('dashboard'),
                'created_at' => now()->toDateTimeString(),
                'count' => $totalProposicoes
            ];
        }

        return $notifications;
    }

    /**
     * Notificações específicas para Admin
     * OTIMIZADO: Uma única query com cache
     */
    private function getAdminNotifications(User $user): array
    {
        $notifications = [];

        // Cache de estatísticas do admin (2 minutos)
        $cacheAdminStats = 'admin_system_stats';

        $stats = Cache::remember($cacheAdminStats, 120, function () {
            // Uma única query para obter estatísticas
            $total = Proposicao::count();
            $pendentes = Proposicao::whereIn('status', ['salvando', 'enviado_legislativo', 'retornado_legislativo'])->count();

            return [
                'total' => $total,
                'pendentes' => $pendentes
            ];
        });

        if ($stats['pendentes'] > 0) {
            $notifications[] = [
                'id' => 'sistema_estatisticas',
                'type' => 'system',
                'title' => 'Visão Geral do Sistema',
                'message' => "Sistema com {$stats['total']} proposições, sendo {$stats['pendentes']} pendentes",
                'icon' => 'ki-chart-line',
                'color' => 'primary',
                'priority' => 'low',
                'url' => route('dashboard'),
                'created_at' => now()->toDateTimeString(),
                'count' => $stats['pendentes']
            ];
        }

        return $notifications;
    }

    /**
     * Obter peso da prioridade para ordenação
     */
    private function getPriorityWeight(string $priority): int
    {
        return match ($priority) {
            'urgent' => 4,
            'high' => 3,
            'medium' => 2,
            'low' => 1,
            default => 0
        };
    }
}