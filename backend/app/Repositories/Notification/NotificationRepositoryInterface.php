<?php

namespace App\Repositories\Notification;

use App\Repositories\BaseRepositoryInterface;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function listByUser(
        int $userId,
        int $page = 1,
        int $perPage = 15,
        array $filters = []
    );

    public function getUnreadByUser(
        int $userId
    );

    public function getUnreadCount(
        int $userId
    ): int;

    public function markAsRead(
        int $id
    ): bool;

    public function markAsUnread(
        int $id
    ): bool;

    public function markAllAsRead(
        int $userId
    ): bool;
}
