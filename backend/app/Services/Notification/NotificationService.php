<?php

namespace App\Services\Notification;

use App\DTOs\NotificationDTO;
use App\Models\NotificationTargetUser;
use App\Models\User;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Services\BaseService;
use App\Services\Notification\NotificationLogService;
use Illuminate\Support\Facades\DB;

class NotificationService extends BaseService
{
    public function __construct(
        NotificationRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }

    public function create(
        NotificationDTO $dto
    ): object {

        return DB::transaction(
            function () use ($dto) {

                $users = $this->resolveUsers(
                    $dto
                );

                $notification =
                    $this->repository->create([
                        'title' => $dto->title,
                        'message' => $dto->message,
                        'type' => $dto->type,
                        'priority' => $dto->priority,
                        'action_url' => $dto->action_url,
                        'related_model' => $dto->related_model,
                        'related_id' => $dto->related_id,
                        'created_by' => $dto->created_by,
                        'total_receivers' => $users->count(),
                    ]);

                foreach ($users as $user) {

                    NotificationTargetUser::create([
                        'notification_id' =>
                            $notification->id,

                        'receiver_id' =>
                            $user->id,

                        'fcm_status' =>
                            'pending',
                    ]);
                }

                NotificationLogService::create(
                    $notification->id,
                    auth()->id(),
                    'notification_created',
                    [
                        'target_type' =>
                            $dto->target_type,

                        'receivers' =>
                            $users->count(),
                    ]
                );

                return $notification;
            }
        );
    }

    public function update(
        int $id,
        NotificationDTO $dto
    ): bool {

        return DB::transaction(
            function () use (
                $id,
                $dto
            ) {

                $result =
                    $this->repository->update(
                        $id,
                        [
                            'title' =>
                                $dto->title,

                            'message' =>
                                $dto->message,

                            'type' =>
                                $dto->type,

                            'priority' =>
                                $dto->priority,

                            'action_url' =>
                                $dto->action_url,

                            'related_model' =>
                                $dto->related_model,

                            'related_id' =>
                                $dto->related_id,
                        ]
                    );

                NotificationLogService::create(
                    $id,
                    auth()->id(),
                    'notification_updated'
                );

                return $result;
            }
        );
    }

    public function listByUser(
        int $userId
    ) {
        return $this->repository
            ->listByUser(
                $userId
            );
    }

    public function getUnreadByUser(
        int $userId
    ) {
        return $this->repository
            ->getUnreadByUser(
                $userId
            );
    }

    public function getUnreadCount(
        int $userId
    ) {
        return $this->repository
            ->getUnreadCount(
                $userId
            );
    }

    public function markAsRead(
        int $notificationId,
        int $userId
    ): bool {

        $result =
            $this->repository
                ->markAsRead(
                    $notificationId,
                    $userId
                );

        NotificationLogService::create(
            $notificationId,
            $userId,
            'notification_read'
        );

        return $result;
    }

    public function markAsUnread(
        int $notificationId,
        int $userId
    ): bool {

        return $this->repository
            ->markAsUnread(
                $notificationId,
                $userId
            );
    }

    public function markAllAsRead(
        int $userId
    ): bool {

        return $this->repository
            ->markAllAsRead(
                $userId
            );
    }

    public function delete(
        int $id
    ): bool {

        return DB::transaction(
            function () use ($id) {

                NotificationTargetUser::where(
                    'notification_id',
                    $id
                )->delete();

                NotificationLogService::create(
                    $id,
                    auth()->id(),
                    'notification_deleted'
                );

                return $this->repository
                    ->delete($id);
            }
        );
    }

    private function resolveUsers(
        NotificationDTO $dto
    ) {

        switch (
            $dto->target_type
        ) {

            case 'single':

                return User::where(
                    'id',
                    $dto->user_id
                )->get();

            case 'multiple':

                return User::whereIn(
                    'id',
                    $dto->user_ids
                )->get();

            case 'students':

                return User::where(
                    'acc_type',
                    'student'
                )->get();

            case 'teachers':

                return User::where(
                    'acc_type',
                    'teacher'
                )->get();

            case 'staff':

                return User::where(
                    'acc_type',
                    'staff'
                )->get();

            case 'all':

                return User::query()->get();

            default:

                return collect();
        }
    }
}
