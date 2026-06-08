<?php

namespace App\Services\Notification;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Psr\Log\LoggerInterface;

class PushNotificationService
{
  protected $messaging;
  protected $logger;

  public function __construct(LoggerInterface $logger = null)
  {
    // Path to your service account JSON file
    $path = storage_path('app/json/fcm-file.json');


    // Initialize the Factory and create the Messaging service directly
    $factory = (new Factory)->withServiceAccount($path);

    // FIX: In SDK v5+, v6+, and v7+, use createMessaging()
    $this->messaging = $factory->createMessaging();

    $this->logger = $logger;
  }

  /**
   * Sends a push notification to a specific FCM device token.
   */
  public function sendPushNotification(string $fcmToken, string $title, string $body, array $data = []): bool
  {
    try {
      // CRITICAL: All values in the 'data' array MUST be strings for FCM
      $stringData = array_map('strval', $data);

      $message = CloudMessage::new()
        // FIX: Use withToken() for specific device tokens
        ->withToken($fcmToken)
        ->withNotification(Notification::create($title, $body))
        ->withData($stringData);

      $this->messaging->send($message);

      if ($this->logger) {
        $this->logger->info("FCM notification sent successfully to token: {$fcmToken}");
      }
      return true;
    } catch (MessagingException $e) {
      dd($e->getMessage());
      // Specifically check if the token is invalid or unregistered
      if ($this->logger) {
        $this->logger->error("FCM Messaging Error: " . $e->getMessage());
      }

      // Note: If $e is an instance of NotFound, the token is dead and should be deleted from your DB
      return false;
    } catch (FirebaseException $e) {
      dd(3);
      if ($this->logger) {
        $this->logger->error("Firebase General Error: " . $e->getMessage());
      }
      return false;
    } catch (\Throwable $e) {
      dd($e->getMessage());
      if ($this->logger) {
        $this->logger->critical("System Error: " . $e->getMessage());
      }
      return false;
    }
  }

  /**
   * Sends a push notification to a specific FCM topic.
   */
  public function sendTopicNotification(string $topic, string $title, string $body, array $data = []): bool
  {
    try {
      $stringData = array_map('strval', $data);

      $message = CloudMessage::new()
        ->withTopic($topic)
        ->withNotification(Notification::create($title, $body))
        ->withData($stringData);

      $this->messaging->send($message);

      return true;
    } catch (\Throwable $e) {
      if ($this->logger) {
        $this->logger->error("Topic Notification Error: " . $e->getMessage());
      }
      return false;
    }
  }
}
