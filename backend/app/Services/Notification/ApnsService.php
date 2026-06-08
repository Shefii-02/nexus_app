<?php
namespace App\Services\Notification;

use Firebase\JWT\JWT;

class ApnsService
{
    public static function generateToken()
    {
        $teamId = env('APNS_TEAM_ID');
        $keyId = env('APNS_KEY_ID');

    $privateKeyPath = storage_path('app/json/AuthKey_R6ZPQ59HUA.p8');
        $payload = [
            'iss' => $teamId,
            'iat' => time(),
        ];

        return JWT::encode($payload, file_get_contents($privateKeyPath), 'ES256', $keyId);
    }
}
