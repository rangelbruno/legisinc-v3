<?php

namespace App\Services;

use App\Models\User;
use App\Models\Proposicao;
use Illuminate\Support\Facades\Auth;

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
     */
    private function getParlamentarNotifications(User $user): array
    {
        $notifications = [];

        // Proposições retornadas do legislativo (equivale a "para correção")
        $proposicoesRetornadas = Proposicao::where('autor_id', $user->id)
            ->where('status', 'retornado_legislativo')
            ->count();

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

        // Proposições em processo de salvamento há mais de 1 dia
        $proposicoesSalvando = Proposicao::where('autor_id', $user->id)
            ->where('status', 'salvando')
            ->where('updated_at', '<', now()->subDays(1))
            ->count();

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
     */
    private function getLegislativoNotifications(User $user): array
    {
        $notifications = [];

        // Proposições enviadas ao legislativo (aguardando revisão)
        $proposicoesParaRevisar = Proposicao::where('status', 'enviado_legislativo')
            ->count();

        if ($proposicoesParaRevisar > 0) {
            $notifications[] = [
                'id' => 'proposicoes_revisar',
                'type' => 'proposicao',
                'title' => 'Proposições para Revisão',
                'message' => "Existem {$proposicoesParaRevisar} proposição(ões) aguardando revisão legislativa",
                'icon' => 'ki-document',
                'color' => 'primary',
                'priority' => 'high',
                'url' => route('proposicoes.legislativo.index'),
                'created_at' => now()->toDateTimeString(),
                'count' => $proposicoesParaRevisar
            ];
        }

        // Proposições enviadas há mais de 3 dias (atrasadas)
        $proposicoesAtrasadas = Proposicao::where('status', 'enviado_legislativo')
            ->where('updated_at', '<', now()->subDays(3))
            ->count();

        if ($proposicoesAtrasadas > 0) {
            $notifications[] = [
                'id' => 'proposicoes_atrasadas',
                'type' => 'proposicao',
                'title' => 'Revisões Atrasadas',
                'message' => "Existem {$proposicoesAtrasadas} proposição(ões) aguardando revisão há mais de 3 dias",
                'icon' => 'ki-time',
                'color' => 'warning',
                'priority' => 'medium',
                'url' => route('proposicoes.legislativo.index'),
                'created_at' => now()->toDateTimeString(),
                'count' => $proposicoesAtrasadas
            ];
        }

        return $notifications;
    }

    /**
     * Notificações específicas para Protocolo
     */
    private function getProtocoloNotifications(User $user): array
    {
        $notifications = [];

        // Para protocolo, vamos mostrar estatísticas gerais
        $totalProposicoes = Proposicao::count();

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
     */
    private function getAdminNotifications(User $user): array
    {
        $notifications = [];

        // Estatísticas gerais do sistema
        $totalProposicoes = Proposicao::count();
        $proposicoesPendentes = Proposicao::whereIn('status', ['salvando', 'enviado_legislativo', 'retornado_legislativo'])->count();

        if ($proposicoesPendentes > 0) {
            $notifications[] = [
                'id' => 'sistema_estatisticas',
                'type' => 'system',
                'title' => 'Visão Geral do Sistema',
                'message' => "Sistema com {$totalProposicoes} proposições, sendo {$proposicoesPendentes} pendentes",
                'icon' => 'ki-chart-line',
                'color' => 'primary',
                'priority' => 'low',
                'url' => route('dashboard'),
                'created_at' => now()->toDateTimeString(),
                'count' => $proposicoesPendentes
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