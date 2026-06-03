<?php

namespace App\Services\Notification;

use App\Models\NotificationLog;

class NotificationLogService
{
    public static function create(
        ?int $notificationId,
        ?int $userId,
        string $action,
        array $payload = []
    ): void {

        NotificationLog::create([
            'notification_id' => $notificationId,
            'user_id' => $userId,
            'action' => $action,
            'payload' => $payload
        ]);
    }
}
