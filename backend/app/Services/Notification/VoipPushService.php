<?php

namespace App\Services\Notification;


use Lcobucci\JWT\Configuration;
use GuzzleHttp\Client;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Firebase\JWT\JWT;


class VoipPushService
{

  public function sendVoipPush($deviceToken, $data)
  {
    $teamId = 'PQ6L2R7BV7';
    $keyId = 'R6ZPQ59HUA';
    $bundleId = 'coin.bookmyteacher.app'; // your app bundle

    $privateKeyPath = storage_path('app/json/AuthKey_R6ZPQ59HUA.p8');

    // ✅ Generate JWT
    // $config = Configuration::forSymmetricSigner(
    //     new \Lcobucci\JWT\Signer\Hmac\Sha256(),
    //     \Lcobucci\JWT\Signer\Key\InMemory::plainText(file_get_contents($privateKeyPath))
    // );



    // $config = Configuration::forAsymmetricSigner(
    //   Sha256::create(),
    //   InMemory::file($privateKeyPath),
    //   InMemory::empty()
    // );

    // $now = new \DateTimeImmutable();

    // $token = $config->builder()
    //   ->issuedBy($teamId)
    //   ->issuedAt($now)
    //   ->withHeader('alg', 'ES256')
    //   ->withHeader('kid', $keyId)
    //   ->getToken($config->signer(), $config->signingKey());

    // $jwt = $token->toString();

    // ✅ Payload
    // $payload = json_encode([
    //   'aps' => [
    //     'content-available' => 1,
    //   ],
    //   'caller_name' => $data['caller_name'],
    //   'call_id' => $data['call_id'],
    //   'subject' => $data['subject'],
    // ]);


    $jwt = ApnsService::generateToken();

    $client = new Client();

    $client = new \GuzzleHttp\Client([
      'http_errors' => false,
      'version' => 2.0,
    ]);
// https://api.push.apple.com
// https://api.sandbox.push.apple.com/
    $response = $client->post(
      "https://api.push.apple.com/3/device/$deviceToken",
      [
        'headers' => [
          'authorization' => "bearer $jwt",
          'apns-topic' => $bundleId . '.voip',
          'apns-push-type' => 'voip',
          'apns-priority' => '10',
          'apns-expiration' => '0',
        ],
        'json' => [
          'aps' => [
            'content-available' => 1,
          ],
          'call_id' => uniqid(),
          'caller_name' => $data['caller_name'],
          'subject' => $data['subject'],
        ],
      ]
    );

    return [
      'status_code' => $response->getStatusCode(),
      'body' => $response->getBody()->getContents(),
    ];


    // ✅ Send via HTTP/2
    // $client = new Client([
    //   'base_uri' => 'https://api.push.apple.com',
    // ]);

    // $response = $client->post("/3/device/{$deviceToken}", [
    //   'headers' => [
    //     'apns-topic' => $bundleId . '.voip',
    //     'apns-push-type' => 'voip',
    //     'apns-priority' => '10',
    //     'authorization' => "bearer {$jwt}",
    //   ],
    //   'body' => $payload,
    // ]);

    return $response->getBody()->getContents();
  }
}
