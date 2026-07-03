<?php
namespace App\Services\Notification;

use App\Models\UserPlatform;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    private Client $client;
    private ?string $accessToken = null;
    private ?string $projectId   = null;

    public function __construct()
    {
        $this->client = new Client();
    }

    // =========================================================================
    //  PUBLIC — typed notification senders
    // =========================================================================

    public function welcomeMessage(int $userId): void
    {
        $this->toUser($userId, [
            'title' => 'Thanks for Joining!',
            'body'  => 'Explore features, personalize your experience, and enjoy using the app.',
        ], [
            'type'              => 'general'
        ]);
    }


    /** New chat message arrived in a conversation */
    public function sendNewMessage(int $userId, array $data): void
    {
        // $data: conversation_id, sender_name, preview, conversation_name
        $this->toUser($userId, [
            'title' => $data['sender_name'],
            'body'  => $data['preview'],
        ], [
            'type'              => 'new_message',
            'conversation_id'   => (string) $data['conversation_id'],
            'conversation_name' => $data['conversation_name'] ?? '',
            'sender_name'       => $data['sender_name'],
        ]);
    }

    /** Student admitted / admission status updated */
    public function sendAdmissionNotification(int $userId, array $data): void
    {
        // $data: student_name, course_name, status ('approved'|'rejected'|'pending')
        $statusLabel = match ($data['status'] ?? 'approved') {
            'approved' => '✅ Admission Approved',
            'rejected' => '❌ Admission Rejected',
            default    => '📋 Admission Update',
        };

        $this->toUser($userId, [
            'title' => $statusLabel,
            'body'  => 'Your admission for ' . $data['course_name'] . ' has been ' . ($data['status'] ?? 'updated') . '.',
        ], [
            'type'        => 'admission',
            'course_name' => $data['course_name'] ?? '',
            'status'      => $data['status'] ?? '',
            'admission_id'=> (string) ($data['admission_id'] ?? ''),
        ]);
    }

    /** New announcement posted */
    public function sendAnnouncement(array $userIds, array $data): void
    {
        // $data: title, body, announcement_id, course_name
        $this->toUsers($userIds, [
            'title' => '📢 ' . $data['title'],
            'body'  => $data['body'],
        ], [
            'type'            => 'announcement',
            'announcement_id' => (string) $data['announcement_id'],
            'course_name'     => $data['course_name'] ?? '',
        ]);
    }

    /** New material uploaded (pdf, video, doc etc.) */
    public function sendMaterialUploaded(array $userIds, array $data): void
    {
        // $data: course_name, material_title, material_type, material_id, course_id
        $icon = match ($data['material_type'] ?? 'file') {
            'video'     => '🎬',
            'pdf'       => '📄',
            'audio'     => '🎙️',
            'image'     => '🖼️',
            default     => '📎',
        };

        $this->toUsers($userIds, [
            'title' => $icon . ' New Material: ' . $data['course_name'],
            'body'  => $data['material_title'] . ' has been uploaded.',
        ], [
            'type'          => 'material_uploaded',
            'material_id'   => (string) $data['material_id'],
            'material_type' => $data['material_type'] ?? 'file',
            'course_id'     => (string) ($data['course_id'] ?? ''),
            'course_name'   => $data['course_name'] ?? '',
        ]);
    }

    /** Recorded class uploaded */
    public function sendRecordedClassUploaded(array $userIds, array $data): void
    {
        // $data: course_name, class_title, record_id, course_id
        $this->toUsers($userIds, [
            'title' => '🎥 Recorded Class Available',
            'body'  => $data['class_title'] . ' (' . $data['course_name'] . ') is now available.',
        ], [
            'type'        => 'recorded_class',
            'record_id'   => (string) $data['record_id'],
            'course_id'   => (string) ($data['course_id'] ?? ''),
            'course_name' => $data['course_name'] ?? '',
        ]);
    }

    /** Upcoming class reminder (scheduled alert) */
    public function sendClassScheduleReminder(array $userIds, array $data): void
    {
        // $data: course_name, teacher_name, start_time, class_id, minutes_before
        $this->toUsers($userIds, [
            'title' => '⏰ Class in ' . ($data['minutes_before'] ?? 5) . ' minutes',
            'body'  => $data['course_name'] . ' with ' . $data['teacher_name'] . ' at ' . $data['start_time'],
        ], [
            'type'         => 'class_reminder',
            'class_id'     => (string) $data['class_id'],
            'course_name'  => $data['course_name'] ?? '',
            'teacher_name' => $data['teacher_name'] ?? '',
            'start_time'   => $data['start_time'] ?? '',
        ]);
    }

    /** Class has started right now */
    public function sendClassStarted(array $userIds, array $data): void
    {
        // $data: course_name, teacher_name, class_id, course_id
        $this->toUsers($userIds, [
            'title' => '🔴 Class Started: ' . $data['course_name'],
            'body'  => 'Your class with ' . $data['teacher_name'] . ' has started. Join now!',
        ], [
            'type'         => 'class_started',
            'class_id'     => (string) $data['class_id'],
            'course_id'    => (string) ($data['course_id'] ?? ''),
            'course_name'  => $data['course_name'] ?? '',
            'teacher_name' => $data['teacher_name'] ?? '',
        ]);
    }

    /** General / custom push (escape hatch for one-off notifications) */
    public function sendCustom(array $userIds, string $title, string $body, array $extra = []): void
    {
        $this->toUsers($userIds, compact('title', 'body'), array_merge(['type' => 'custom'], $extra));
    }

    // =========================================================================
    //  PRIVATE — resolve users → tokens → dispatch
    // =========================================================================

    /**
     * Send to a single user across all their devices.
     */
    private function toUser(int $userId, array $notification, array $data): void
    {
        $this->toUsers([$userId], $notification, $data);
    }

    /**
     * Send to multiple users. Loads all platforms in one query,
     * then dispatches FCM/VoIP per device.
     */
    private function toUsers(array $userIds, array $notification, array $data): void
    {
        if (empty($userIds)) return;

        $platforms = UserPlatform::whereIn('user_id', $userIds)
            ->whereNull('deleted_at')
            ->get();

        foreach ($platforms as $platform) {
            try {
                if ($platform->platform === 'ios') {
                    // For non-call notifications on iOS, use FCM APNs (not VoIP).
                    // VoIP is reserved for incoming-call pushes only.
                    if ($platform->fcm_token) {
                        $this->dispatchFcm($platform->fcm_token, $notification, $data);
                    }
                } else {
                    if ($platform->fcm_token) {
                        $this->dispatchFcm($platform->fcm_token, $notification, $data);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('FcmNotificationService: dispatch failed', [
                    'user_id'  => $platform->user_id,
                    'platform' => $platform->platform,
                    'type'     => $data['type'] ?? 'unknown',
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Build and POST one FCM v1 message.
     *
     * $notification = ['title' => '...', 'body' => '...']
     * $data         = ['type' => '...', ...string values...]
     */
    private function dispatchFcm(string $token, array $notification, array $data): void
    {
        $stringData = array_map('strval', $data);

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $notification['title'],
                    'body'  => $notification['body'],
                ],
                'data'    => $stringData,
                'android' => [
                    'priority'     => 'high',
                    'ttl'          => '60s',
                    'notification' => [
                        'channel_id' => $this->channelForType($data['type'] ?? ''),
                        'sound'      => 'default',
                    ],
                ],
                'apns' => [
                    'headers' => ['apns-priority' => '10'],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $notification['title'],
                                'body'  => $notification['body'],
                            ],
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->client->post(
            'https://fcm.googleapis.com/v1/projects/' . $this->projectId() . '/messages:send',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]
        );

        Log::info('FcmNotificationService: sent', [
            'type'   => $data['type'] ?? '?',
            'status' => $response->getStatusCode(),
        ]);
    }

    /**
     * Map notification type to Android channel id.
     * These must match the channels registered in your Flutter app.
     */
    private function channelForType(string $type): string
    {
        return match ($type) {
            'new_message'                  => 'chat_channel',
            'incoming_call'                => 'call_channel',
            'class_started', 'class_reminder' => 'class_alert_channel',
            'announcement'                 => 'announcement_channel',
            'material_uploaded',
            'recorded_class'               => 'material_channel',
            'admission'                    => 'admission_channel',
            default                        => 'general_channel',
        };
    }

    // =========================================================================
    //  Google auth — lazy, cached per instance lifetime (one request)
    // =========================================================================

    private function accessToken(): string
    {
        if (!$this->accessToken) {
            $jsonPath          = storage_path('app/json/fcm-file.json');
            $credentials       = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $jsonPath
            );
            $this->accessToken = $credentials->fetchAuthToken()['access_token'];
        }
        return $this->accessToken;
    }

    private function projectId(): string
    {
        if (!$this->projectId) {
            $jsonPath        = storage_path('app/json/fcm-file.json');
            $config          = json_decode(file_get_contents($jsonPath), true);
            $this->projectId = $config['project_id'];
        }
        return $this->projectId;
    }
}
