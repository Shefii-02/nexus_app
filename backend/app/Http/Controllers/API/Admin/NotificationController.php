<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\NotificationDTO;
use App\Http\Controllers\API\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Services\Notification\FcmNotificationService;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(): JsonResponse
    {
        return $this->successResponse(
            NotificationResource::collection(
                $this->notificationService
                    ->listByUser(
                        auth()->id()
                    )
            ),
            'Notifications retrieved successfully'
        );
    }

    public function store(
        NotificationRequest $request
    ): JsonResponse {

        $dto =
            NotificationDTO::fromArray(
                $request->validated()
            );

        $notification =
            $this->notificationService
            ->create(
                $dto
            );


        $targetUserId = $notification->user_id ?? null;
        if ($targetUserId) {
            (new FcmNotificationService())->sendCustom(
                [$targetUserId],
                $notification->title ?? 'New Notification',
                $notification->message ?? $notification->body ?? '',
                [
                    'type'            => 'notification',
                    'notification_id' => (string) $notification->id,
                ]
            );
        }

        return $this->successResponse(
            NotificationResource::make(
                $notification
            ),
            'Notification created successfully',
            201
        );
    }

    public function show(
        int $id
    ): JsonResponse {

        $notification =
            $this->notificationService
            ->find(
                $id
            );

        return $this->successResponse(
            NotificationResource::make(
                $notification
            ),
            'Notification retrieved successfully'
        );
    }

    public function update(
        NotificationRequest $request,
        int $id
    ): JsonResponse {

        $dto =
            NotificationDTO::fromArray(
                $request->validated()
            );

        $this->notificationService
            ->update(
                $id,
                $dto
            );

        return $this->successResponse(
            null,
            'Notification updated successfully'
        );
    }

    public function destroy(
        int $id
    ): JsonResponse {

        $this->notificationService
            ->delete(
                $id
            );

        return $this->successResponse(
            null,
            'Notification deleted successfully'
        );
    }

    public function unread(): JsonResponse
    {
        return $this->successResponse(
            NotificationResource::collection(
                $this->notificationService
                    ->getUnreadByUser(
                        auth()->id()
                    )
            ),
            'Unread notifications'
        );
    }

    public function unreadCount(): JsonResponse
    {
        return $this->successResponse([
            'count' =>
            $this->notificationService
                ->getUnreadCount(
                    auth()->id()
                )
        ]);
    }

    public function markRead(
        int $id
    ): JsonResponse {

        $this->notificationService
            ->markAsRead(
                $id,
                auth()->id()
            );

        return $this->successResponse(
            null,
            'Notification marked as read'
        );
    }

    public function markUnread(
        int $id
    ): JsonResponse {

        $this->notificationService
            ->markAsUnread(
                $id,
                auth()->id()
            );

        return $this->successResponse(
            null,
            'Notification marked as unread'
        );
    }

    public function markAllRead(): JsonResponse
    {
        $this->notificationService
            ->markAllAsRead(
                auth()->id()
            );

        return $this->successResponse(
            null,
            'All notifications marked as read'
        );
    }
}
