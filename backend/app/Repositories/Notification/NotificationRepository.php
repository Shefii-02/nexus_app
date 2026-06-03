<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use App\Repositories\BaseRepository;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(
        Notification $model
    ) {
        parent::__construct($model);
    }

    public function listByUser(
        int $userId,
        int $page = 1,
        int $perPage = 15,
        array $filters = []
    ) {
        $query = Notification::query();
        //     where(
        //     'created_by',
        //     $userId
        // );

        if (!empty($filters['type'])) {
            $query->where(
                'type',
                $filters['type']
            );
        }

        if (!empty($filters['priority'])) {
            $query->where(
                'priority',
                $filters['priority']
            );
        }

        return $query
            ->latest()
            ->paginate(
                $perPage,
                ['*'],
                'page',
                $page
            );
    }

    public function getUnreadByUser(
        int $userId
    ) {
        return Notification::
        //     where(
        //     'created_by',
        //     $userId
        // )->
            whereNull('read_at')
            ->latest()
            ->get();
    }

    public function getUnreadCount(
        int $userId
    ): int {
        return Notification::
        //     where(
        //     'created_by',
        //     $userId
        // )
        //     ->
            whereNull('read_at')
            ->count();
    }

    public function markAsRead(
        int $id
    ): bool {
        return Notification::where(
            'id',
            $id
        )->update([
            'read_at' => now(),
        ]) > 0;
    }

    public function markAsUnread(
        int $id
    ): bool {
        return Notification::where(
            'id',
            $id
        )->update([
            'read_at' => null,
        ]) > 0;
    }

    public function markAllAsRead(
        int $userId
    ): bool {
        Notification::
        //     where(
        //     'created_by',
        //     $userId
        // )
            // ->
            whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return true;
    }
}
