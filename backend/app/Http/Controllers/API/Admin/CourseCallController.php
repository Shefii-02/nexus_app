<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Conversation;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\Notification\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseCallController extends Controller
{
    // ── GET /course/{id}/students ────────────────────────────────────────
    public function getStudents($id)
    {
        // Get all students enrolled in this course/module
        // $students = User::whereHas('enrollments', fn($q) => $q->where('course_id', $id))
        //     ->where('role', 'student')
        //     ->select('id', 'name', 'email', 'avatar')
        //     ->get();
        $students = [
            [
                'id' => 1,
                'name' => 'Rahul Kumar',
                'email' => 'rahul@example.com',
                'avatar' => 'https://i.pravatar.cc/150?img=1',
            ],
            [
                'id' => 2,
                'name' => 'Anjali Nair',
                'email' => 'anjali@example.com',
                'avatar' => 'https://i.pravatar.cc/150?img=2',
            ],
            [
                'id' => 3,
                'name' => 'Mohammed Ali',
                'email' => 'mohammed@example.com',
                'avatar' => 'https://i.pravatar.cc/150?img=3',
            ],
            [
                'id' => 4,
                'name' => 'Priya Sharma',
                'email' => 'priya@example.com',
                'avatar' => 'https://i.pravatar.cc/150?img=4',
            ],
            [
                'id' => 5,
                'name' => 'Arjun Menon',
                'email' => 'arjun@example.com',
                'avatar' => 'https://i.pravatar.cc/150?img=5',
            ],
        ];

        return response()->json([
            'status'   => 200,
            'students' => $students,
        ]);
    }

    // ── POST /course/{id}/call ───────────────────────────────────────────
    public function sendCall(Request $request, $id)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'module_id'       => 'required|integer',
            'student_ids'     => 'required|array',
            'student_ids.*'   => 'integer|exists:users,id',
        ]);

        Log::info($request->all());

        // $students = User::whereIn('id', $request->student_ids)
        //     ->whereNotNull('fcm_token')
        //     ->get();

        // foreach ($students as $student) {
        //     $this->sendFcmCall($student->fcm_token, [
        //         'type'            => 'incoming_call',
        //         'call_id'         => (string) $request->conversation_id,
        //         'caller_name'     => auth()->user()->name ?? 'Teacher',
        //         'subject'         => $request->subject ?? 'Class',
        //         'conversation_id' => (string) $request->conversation_id,
        //         'module_id'       => (string) $id,
        //     ]);
        // }

        // $studentCount  = $students->count();
        $studentCount = count($request->student_ids);
        return response()->json([
            'status'  => 200,
            'message' => 'Call sent to ' . $studentCount . ' students',
        ]);
    }

    // ── POST /course/{id}/notification ──────────────────────────────────
    public function sendNotification(Request $request, $id)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'module_id'       => 'required|integer',
            'student_ids'     => 'required|array',
            'student_ids.*'   => 'integer|exists:users,id',
            'title'           => 'nullable|string',
            'body'            => 'nullable|string',
        ]);

        Log::info($request->all());

        $studentCount = count($request->student_ids);

        return response()->json([
            'status'  => 200,
            'message' => 'Notification sent to ' . $studentCount . ' students',
        ]);

        $students = User::whereIn('id', $request->student_ids)
            ->whereNotNull('fcm_token')
            ->get();

        foreach ($students as $student) {
            $this->sendFcmNotification($student->fcm_token, [
                'type'            => 'class_alert',
                'conversation_id' => (string) $request->conversation_id,
                'module_id'       => (string) $id,
                'subject'         => $request->subject  ?? 'Class Starting',
                'teacher'         => auth()->user()->name ?? 'Teacher',
                'start_time'      => $request->start_time ?? now()->format('H:i'),
                'title'           => $request->title ?? '📚 Class is starting!',
                'body'            => $request->body  ?? 'Your teacher is ready.',
            ]);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Notification sent to ' . $students->count() . ' students',
        ]);
    }

    // ── FCM helpers ──────────────────────────────────────────────────────
    private function sendFcmCall(string $token, array $data): void
    {
        $this->sendFcm($token, $data, [
            'title' => 'Incoming Class Call',
            'body'  => ($data['caller_name'] ?? 'Teacher') . ' is calling',
        ]);
    }

    private function sendFcmNotification(string $token, array $data): void
    {
        $this->sendFcm($token, $data, [
            'title' => $data['title'] ?? '📚 Class Alert',
            'body'  => $data['body']  ?? 'Your class is starting',
        ]);
    }

    private function sendFcm(string $token, array $data, array $notification): void
    {
        $jsonPath    = storage_path('app/json/fcm-file.json');
        $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $jsonPath
        );
        $authToken   = $credentials->fetchAuthToken();
        $config      = json_decode(file_get_contents($jsonPath), true);

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => $notification,
                'data'         => array_map('strval', $data),
                'android'      => [
                    'priority' => 'high',
                    'ttl'      => '30s',
                    'notification' => [
                        'channel_id'            => 'high_importance_channel',
                        'notification_priority' => 'PRIORITY_MAX',
                        'visibility'            => 'PUBLIC',
                    ],
                ],
            ],
        ];

        (new \GuzzleHttp\Client())->post(
            "https://fcm.googleapis.com/v1/projects/{$config['project_id']}/messages:send",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken['access_token'],
                    'Content-Type'  => 'application/json',
                ],
                'json' => $payload,
            ]
        );
    }
}
