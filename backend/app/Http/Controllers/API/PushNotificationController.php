<?php

namespace App\Http\Controllers\API;

use App\Services\Notification\PushNotificationService;
use App\Services\FcmNotificationService;
use Exception;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Or inject LoggerInterface directly
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class PushNotificationController extends Controller
{
    protected $notificationService;
    protected $fcmTokenIos;
    protected $fcmTokenAndroid;

    use ApiResponse;
    public function __construct(PushNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->fcmTokenIos = 'dPvyIPaG1k4nj_Em7zzUtG:APA91bF_PiiheEMlagDMFAQRBH36JPdT5GKc7IyhT73KEGQMagqasqiiT1zial_2qX4Da2kP3R8bEjZ0falXOYwsL_c_a_xD87r2omOmrj3YKjNn-AdW4Tk'; // Get this from your DB
        $this->fcmTokenAndroid = "fDUUh-BKRVSkmOPjNORIKT:APA91bH6SHuhjJzXiVa8wUQ0Nb4TMY2H-aJAA8BdW5nZdx6cn4zNZ_CR7G0AXQgNgpsz_prmX6agiA12uS_lX_RUc1m9Dk_Pp5ae-NFj2QsMk2Q2IwLZ3xk";
        //
    }

    public function sendPush(Request $request, $fcmToken)
    {

        $jsonPath = storage_path('app/json/fcm-file.json');

        // FORCE a fresh token for testing
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials($scopes, $jsonPath);
        $tokenArray = $credentials->fetchAuthToken();
        $accessToken = $tokenArray['access_token'];

        // LOG THIS: If this is empty, your JSON file is invalid
        if (!$accessToken) {
            return response()->json(['error' => 'Could not generate access token. Check JSON file.'], 500);
        }


        $client = new \GuzzleHttp\Client();
        $config = json_decode(file_get_contents($jsonPath), true);
        $url = "https://fcm.googleapis.com/v1/projects/{$config['project_id']}/messages:send";


        $response = $client->post($url, [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => 'Test',
                        'body' => 'It works!'
                    ]
                ]
            ]
        ]);

        try {
                return response()->json([
                'status' => 200,
                'message' => 'Notification sent successfully',
                'response' => json_decode($response->getBody(), true)
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // This will show the EXACT reason Google is rejecting you
            return response()->json([
                'status' => 'error',
                'google_says' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ], 401);
        }
    }

  // public function sendPush(Request $request)
  //   {
  //         $fcmToken = 'dPvyIPaG1k4nj_Em7zzUtG:APA91bF_PiiheEMlagDMFAQRBH36JPdT5GKc7IyhT73KEGQMagqasqiiT1zial_2qX4Da2kP3R8bEjZ0falXOYwsL_c_a_xD87r2omOmrj3YKjNn-AdW4Tk'; // Get this from your DB

  //       try {
  //           // 1. Path to your Service Account JSON (Ensure this path is correct!)
  //           $jsonPath = storage_path('app/json/fcm-file.json');

  //           if (!file_exists($jsonPath)) {
  //               return response()->json(['error' => 'Service account file not found.'], 500);
  //           }

  //           // 2. Get/Cache the Access Token (Valid for 1 hour)
  //           $accessToken = $this->getValidAccessToken($jsonPath);

  //           // 3. Get Project ID from JSON
  //           $config = json_decode(file_get_contents($jsonPath), true);
  //           $projectId = env('FIREBASE_PROJECT_ID');

  //           // 4. Build the Payload
  //           $payload = [
  //               'message' => [
  //                   'token' => $fcmToken, // From your request
  //                   'notification' => [
  //                       'title' => $request->title ?? 'Default Title',
  //                       'body'  => $request->body ?? 'Default Message',
  //                   ],
  //                   // Fix for the 400 error: Ensure 'data' is a Map (object), not a List
  //                   'data' => (object) [
  //                       'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
  //                       'id' => '1' // Values MUST be strings
  //                   ],
  //                   'android' => ['priority' => 'high'],
  //                   'apns' => ['payload' => ['aps' => ['content-available' => 1]]]
  //               ]
  //           ];

  //           // 5. Send Request
  //           $client = new Client();
  //           $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

  //           $response = $client->post($url, [
  //               'headers' => [
  //                   'Authorization' => "Bearer $accessToken",
  //                   'Content-Type'  => 'application/json',
  //               ],
  //               'json' => $payload,
  //           ]);

  //           return response()->json(json_decode($response->getBody(), true));

  //       } catch (\Exception $e) {
  //           // This will catch the 401 and log the exact reason from Google
  //           Log::error('FCM Error: ' . $e->getMessage());

  //           $errorBody = $e instanceof \GuzzleHttp\Exception\RequestException && $e->hasResponse()
  //               ? json_decode($e->getResponse()->getBody()->getContents(), true)
  //               : $e->getMessage();

  //           return response()->json([
  //               'status' => 'error',
  //               'details' => $errorBody
  //           ], 401);
  //       }
  //   }

    /**
     * Handles Token Generation and Caching
     */
    private function getValidAccessToken($jsonPath)
    {
        return Cache::remember('fcm_access_token', 3500, function () use ($jsonPath) {
            $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
            $credentials = new ServiceAccountCredentials($scopes, $jsonPath);
            $tokenArray = $credentials->fetchAuthToken();

            if (!isset($tokenArray['access_token'])) {
                throw new \Exception('Failed to fetch access token from Google.');
            }

            return $tokenArray['access_token'];
        });
    }


    public function sendToUser(Request $request)
    {
        // $fcmToken = 'dPvyIPaG1k4nj_Em7zzUtG:APA91bF_PiiheEMlagDMFAQRBH36JPdT5GKc7IyhT73KEGQMagqasqiiT1zial_2qX4Da2kP3R8bEjZ0falXOYwsL_c_a_xD87r2omOmrj3YKjNn-AdW4Tk'; // Get this from your DB
        $fcmToken = $request->token ?? 'eSVlggMDQr-VWkwtR3Trv3:APA91bHv59_hzLpzuHOBnDJpJIinnu3InG7_Yu2sfMYs0x16CMSBQMUTZ4GksrBZ2WTOHoBpgOr_kCdrKFLmYLuyb9bdD0YD9MmI9-qSdCACLGMlpJ0lVmE';

        // 1. Setup Configuration
        $serviceAccountPath = storage_path('app/json/fcm-file.json'); // Ensure file exists here
        $config = json_decode(file_get_contents($serviceAccountPath), true);
        $projectId = env('FIREBASE_PROJECT_ID');

        // 2. Get OAuth2 Access Token
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
        $authToken = $credentials->fetchAuthToken();
        $accessToken = $authToken['access_token'];

        // 3. Prepare the Data Payload (The fix for your 400 error)
        // FCM v1 'data' MUST be a flat associative array of strings.
        $extraData = [
            'order_id' => "12345", // Must be a string
            'status'   => "shipped",
            'click_action' => "FLUTTER_NOTIFICATION_CLICK"
        ];

        // Ensure we don't send an indexed list.
        // If $extraData is empty, we use an empty object.
        $formattedData = empty($extraData) ? new \stdClass() : array_map('strval', $extraData);

        // 4. Build the JSON Payload
        $payload = [
            'message' => [
                'token' => $fcmToken, // Replace with actual token
                'notification' => [
                    'title' => 'Order Update',
                    'body'  => 'Your package is on the way!'
                ],
                'data' => $formattedData,
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'high_importance_channel'
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default'
                        ]
                    ]
                ]
            ]
        ];

        // 5. Execute Request
        $client = new Client();
        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Notification sent successfully',
                'response' => json_decode($response->getBody(), true)
            ]);
        } catch (GuzzleException $e) {
            return response()->json([
                'status' => $e->getCode(),
                'error' => json_decode($e->getResponse()->getBody()->getContents(), true)
            ], $e->getCode() ?: 500);
        }
    }

    // public function sendToUser(Request $request)
    // {
    //   $fcmToken = 'dPvyIPaG1k4nj_Em7zzUtG:APA91bF_PiiheEMlagDMFAQRBH36JPdT5GKc7IyhT73KEGQMagqasqiiT1zial_2qX4Da2kP3R8bEjZ0falXOYwsL_c_a_xD87r2omOmrj3YKjNn-AdW4Tk'; // Get this from your DB

    //   $fcm = new FcmNotificationService();
    //   $result = $fcm->send(
    //     $fcmToken,
    //     'Hello World',
    //     'This is a v1 notification!'
    //   );

    //   dd($result);


    //   // Assume you have a user and their FCM token from your database
    //   $userFcmToken = 'dPvyIPaG1k4nj_Em7zzUtG:APA91bF_PiiheEMlagDMFAQRBH36JPdT5GKc7IyhT73KEGQMagqasqiiT1zial_2qX4Da2kP3R8bEjZ0falXOYwsL_c_a_xD87r2omOmrj3YKjNn-AdW4Tk'; // Get this from your DB
    //   $notificationTitle = 'New Message!';
    //   $notificationBody = 'You have a new message from a friend.';
    //   $customData = ['type' => 'chat', 'chat_id' => '123'];
    //   try {
    //     $success = $this->notificationService->sendPushNotification(
    //       $userFcmToken,
    //       $notificationTitle,
    //       $notificationBody,
    //       $customData
    //     );

    //     dd($success);

    //     if ($success) {
    //       return response()->json(['message' => 'Notification sent successfully!']);
    //     } else {
    //       return response()->json(['message' => 'Failed to send notification.'], 500);
    //     }
    //   } catch (Exception $e) {
    //     dd($e->getMessage());
    //   }
    // }

    public function sendToTopic(Request $request)
    {
        $topic = 'general_announcements'; // Your predefined topic
        $notificationTitle = 'App Update!';
        $notificationBody = 'A new version of the app is available.';
        $customData = ['version' => '2.0.0'];

        $success = $this->notificationService->sendTopicNotification(
            $topic,
            $notificationTitle,
            $notificationBody,
            $customData
        );

        if ($success) {
            return response()->json(['message' => 'Topic notification sent successfully!']);
        } else {
            return response()->json(['message' => 'Failed to send topic notification.'], 500);
        }
    }


    ///////////////////////////////////////////////////////

    public function sendClassNotification(string $token, $platform = 'android')
    {

        if ($platform === 'ios') {
            $data = [
                'caller_name' => 'Math Teacher',
                'call_id' => '123456',
                'subject' => 'Physics',
            ];

            $service = new \App\Services\Notification\VoipPushService();
            return  $service->sendVoipPush($token, $data);

            return response()->json(['status' => 'sent']);
        }



        $jsonPath = storage_path('app/json/fcm-file.json');

        // 1. Get Access Token
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $jsonPath);
        $authToken = $credentials->fetchAuthToken();
        $accessToken = $authToken['access_token'];

        $config = json_decode(file_get_contents($jsonPath), true);
        $projectId = $config['project_id'];

        // 2. Payload (v1 format)
        $payload = [
            'message' => [
                'token' => $token,
                'data' => [
                    'type' => 'incoming_call',
                    'call_id' => '123456',
                    'caller_name' => 'Math Teacher',
                    'subject' => 'Physics',
                ],
                'android' => [
                    'priority' => 'high',
                    'ttl' => '30s',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'content-available' => 1,
                            'sound' => 'default',
                        ]
                    ]
                ],
            ]
            // 'message' => [
            //   'token' => $token,

            //   'notification' => [
            //     'title' => '📚 Class in 5 minutes!',
            //     'body'  => 'Mathematics with Prof. Test at 10:00',
            //   ],

            //   'data' => [
            //     'type'       => 'class_alert',
            //     'subject'    => 'Mathematics',
            //     'teacher'    => 'Prof. Test',
            //     'start_time' => '10:00',
            //     'class_id'   => '999',
            //     'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            //   ],

            //   'android' => [
            //     'priority' => 'high',
            //     'notification' => [
            //       'channel_id' => 'class_alert_channel'
            //     ]
            //   ],

            //   'apns' => [
            //     'payload' => [
            //       'aps' => [
            //         'sound' => 'default'
            //       ]
            //     ]
            //   ]
            // ]
        ];

        // 3. Send
        $client = new Client();

        try {
            $response = $client->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            return response()->json([
                'status' => 200,
                'message' => 'Notification sent successfully',
                'response' => json_decode($response->getBody(), true)
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {

            return response()->json([
                'status' => 'error',
                'details' => json_decode(
                    $e->getResponse()->getBody()->getContents(),
                    true
                )
            ], 500);
        }
    }
    public function sendClassAlertTest(string $fcmToken)
    {
      $response =   $this->sendClassAlert($fcmToken, [
            'id'            => '101',
            'course_id'     => 1,
            'subject'       => 'Mathematics',
            'teacher'       => 'Mr. Rahul',
            'class_name'    => 'Class 10-A',
            'time'          => '10:00 AM',
            'message'       => 'Class is starting now. Join immediately!',
            'alarm_enabled' => 'true',
        ]);

        return $response;
    }


    public function sendClassAlert(string $token, array $alertData, string $platform = 'android')
    {
        $jsonPath    = storage_path('app/json/fcm-file.json');
        $scopes      = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $jsonPath);
        $authToken   = $credentials->fetchAuthToken();
        $accessToken = $authToken['access_token'];

        if (!$accessToken) {
            return response()->json(['error' => 'Could not generate access token.'], 500);
        }

        $config    = json_decode(file_get_contents($jsonPath), true);
        $projectId = $config['project_id'];

        // ── All values must be strings in FCM data payload ─────────────────────
        $payload = [
            'message' => [
                'token' => $token,

                // ✅ NO 'notification' key — data-only so background handler fires
                'data' => [
                    'type'          => 'class_alert',
                    'id'            => (string) ($alertData['id']         ?? uniqid()),
                    'subject'       => (string) ($alertData['subject']    ?? ''),
                    'teacher'       => (string) ($alertData['teacher']    ?? ''),
                    'class_name'    => (string) ($alertData['class_name'] ?? ''),
                    'time'          => (string) ($alertData['time']       ?? ''),
                    'message'       => (string) ($alertData['message']    ?? ''),
                    'alarm_enabled' => (string) ($alertData['alarm_enabled'] ?? 'true'),
                    'sent_at'       => now()->toIso8601String(),
                ],

                'android' => [
                    'priority' => 'high',   // ← wakes device from Doze
                    'ttl'      => '60s',
                ],

                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'content-available' => 1,
                            'sound'             => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post(
                "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send",
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => $payload,
                ]
            );

            return response()->json([
                'status'   => 200,
                'message'  => 'Class alert sent successfully',
                'response' => json_decode($response->getBody(), true),
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'status'  => 'error',
                'details' => json_decode(
                    $e->getResponse()->getBody()->getContents(),
                    true
                ),
            ], 500);
        }
    }
}
