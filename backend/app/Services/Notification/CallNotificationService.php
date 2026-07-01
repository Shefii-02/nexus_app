<?php
namespace App\Services\Notification;

use App\Models\UserPlatform;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CallNotificationService
{
    private string $projectId;
    private string $accessToken;
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    // ─────────────────────────────────────────────────────────────────────
    //  PUBLIC — send to a single user (auto-detects platform)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Push an incoming-call notification to every active device
     * belonging to $userId. Handles iOS (VoIP) and Android (FCM) automatically.
     */
    public function notifyUser(int $userId, array $callData): void
    {
        $platforms = UserPlatform::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get();

        foreach ($platforms as $platform) {
            try {
                if ($platform->platform === 'ios') {
                    if ($platform->voip_token) {
                        $this->sendVoip($platform->voip_token, $callData);
                    }
                } else {
                    // android (or any other platform)
                    if ($platform->fcm_token) {
                        $this->sendFcm($platform->fcm_token, $callData);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('CallNotificationService: failed for user '
                    . $userId . ' platform ' . $platform->platform, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Push to multiple users — used by BroadcastCallController.
     */
    public function notifyUsers(array $userIds, array $callData): void
    {
        $platforms = UserPlatform::whereIn('user_id', $userIds)
            ->whereNull('deleted_at')
            ->get();

        foreach ($platforms as $platform) {
            try {
                if ($platform->platform === 'ios') {
                    if ($platform->voip_token) {
                        $this->sendVoip($platform->voip_token, $callData);
                    }
                } else {
                    if ($platform->fcm_token) {
                        $this->sendFcm($platform->fcm_token, $callData);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('CallNotificationService: failed for user '
                    . $platform->user_id . ' platform ' . $platform->platform, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    //  ANDROID — FCM v1
    // ─────────────────────────────────────────────────────────────────────
    private function sendFcm(string $token, array $callData): void
    {
        $payload = [
            'message' => [
                'token' => $token,
                'data'  => array_map('strval', $callData), // FCM data must be strings
                'android' => [
                    'priority' => 'high',
                    'ttl'      => '30s',
                ],
                'apns' => [
                    'headers' => ['apns-priority' => '10'],
                    'payload' => [
                        'aps' => [
                            'content-available' => 1,
                            'sound'             => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $this->client->post(
            'https://fcm.googleapis.com/v1/projects/'
                . $this->getProjectId() . '/messages:send',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    //  iOS — VoIP push
    // ─────────────────────────────────────────────────────────────────────
    private function sendVoip(string $token, array $callData): void
    {
        $service = new VoipPushService();
        $service->sendVoipPush($token, $callData);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  Google auth — lazy-loaded + cached per request
    // ─────────────────────────────────────────────────────────────────────
    private function getAccessToken(): string
    {
        if (!isset($this->accessToken)) {
            $jsonPath    = storage_path('app/json/fcm-file.json');
            $scopes      = ['https://www.googleapis.com/auth/firebase.messaging'];
            $credentials = new ServiceAccountCredentials($scopes, $jsonPath);
            $auth        = $credentials->fetchAuthToken();
            $this->accessToken = $auth['access_token'];
        }
        return $this->accessToken;
    }

    private function getProjectId(): string
    {
        if (!isset($this->projectId)) {
            $jsonPath        = storage_path('app/json/fcm-file.json');
            $config          = json_decode(file_get_contents($jsonPath), true);
            $this->projectId = $config['project_id'];
        }
        return $this->projectId;
    }
}
