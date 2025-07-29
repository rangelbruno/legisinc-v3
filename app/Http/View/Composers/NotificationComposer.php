<?php

namespace App\Http\View\Composers;

use App\Services\NotificationService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (Auth::check()) {
            $notifications = $this->notificationService->getNotificationsForUser();
            $unreadCount = count($notifications);
            $hasUrgent = $this->notificationService->hasUrgentNotifications();

            $view->with([
                'userNotifications' => $notifications,
                'notificationCount' => $unreadCount,
                'hasUrgentNotifications' => $hasUrgent
            ]);
        } else {
            $view->with([
                'userNotifications' => [],
                'notificationCount' => 0,
                'hasUrgentNotifications' => false
            ]);
        }
    }
}