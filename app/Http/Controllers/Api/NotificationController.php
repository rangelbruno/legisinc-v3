<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Obter notificações do usuário atual
     */
    public function index(): JsonResponse
    {
        try {
            $notifications = $this->notificationService->getNotificationsForUser();
            $unreadCount = count($notifications);
            $hasUrgent = $this->notificationService->hasUrgentNotifications();

            // Formatar notificações para o frontend
            $formattedNotifications = array_map(function ($notification) {
                return [
                    'id' => $notification['id'],
                    'tipo' => $notification['type'],
                    'titulo' => $notification['title'],
                    'descricao' => $notification['message'],
                    'icone' => 'ki-duotone ' . $notification['icon'],
                    'cor' => $notification['color'],
                    'prioridade' => $notification['priority'],
                    'link' => $notification['url'],
                    'link_acao' => $notification['url'],
                    'acao_texto' => $this->getActionText($notification),
                    'data_formatada' => $this->formatDate($notification['created_at']),
                    'count' => $notification['count'] ?? 1,
                    'ementa' => null // Para compatibilidade com o JS existente
                ];
            }, $notifications);

            return response()->json([
                'success' => true,
                'notificacoes' => $formattedNotifications,
                'nao_lidas' => $unreadCount,
                'tem_urgentes' => $hasUrgent
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar notificações: ' . $e->getMessage(),
                'notificacoes' => [],
                'nao_lidas' => 0,
                'tem_urgentes' => false
            ], 500);
        }
    }

    /**
     * Texto da ação baseado no tipo de notificação
     */
    private function getActionText(array $notification): string
    {
        return match ($notification['id']) {
            'proposicoes_assinatura' => 'Assinar',
            'proposicoes_correcao' => 'Corrigir',
            'proposicoes_revisar' => 'Revisar',
            'proposicoes_urgentes' => 'Revisar',
            'proposicoes_protocolo' => 'Ver Protocolo',
            'proposicoes_abandonadas' => 'Finalizar',
            'proposicoes_atrasadas' => 'Revisar',
            'sistema_estatisticas' => 'Ver Dashboard',
            default => 'Ver Detalhes'
        };
    }

    /**
     * Formatar data para exibição
     */
    private function formatDate(string $dateTime): string
    {
        $date = new \DateTime($dateTime);
        $now = new \DateTime();
        $diff = $now->diff($date);

        if ($diff->days == 0) {
            if ($diff->h == 0) {
                return $diff->i == 0 ? 'Agora' : $diff->i . ' min atrás';
            } else {
                return $diff->h . 'h atrás';
            }
        } elseif ($diff->days == 1) {
            return 'Ontem';
        } elseif ($diff->days < 7) {
            return $diff->days . ' dias atrás';
        } else {
            return $date->format('d/m/Y');
        }
    }
}