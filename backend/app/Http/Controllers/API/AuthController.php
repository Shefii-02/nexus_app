<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\RefreshToken;
use App\Models\Student;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Services\Media\MediaService;
use App\Services\Notification\FcmNotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;


    public function __construct(
        private OtpService $otpService,

        private MediaService $mediaService
    ) {}


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
            'password' => 'required'
        ]);


        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // if (!$token = auth()->attempt($credentials)) {
        //     return response()->json(['error' => 'Invalid credentials'], 401);
        // }
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }


        $user = auth('api')->user();

        // Revoke existing refresh tokens for this user
        RefreshToken::where('user_id', $user->id)
            ->where('revoked', false)
            ->update(['revoked' => true]);

        $refreshToken = Str::random(64);
        $accessTokenExpiry = now()->addMinutes(config('jwt.ttl', 60)); // Default 60 minutes

        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => Hash::make($refreshToken),
            'expires_at' => now()->addDays(7), // Refresh token expires in 7 days
            'access_token_expires_at' => $accessTokenExpiry,
        ]);



        return response()->json([
            'status' => true,
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'access_token_expires_at' => config('jwt.ttl', 60) * 60, // Convert to seconds
            'refresh_token_expires_at' => config('jwt.refresh_ttl', 43200), // 7 days in seconds

            'user' => new UserResource($user),
            // 'user' => [
            //     ...$user->toArray(),
            //     'roles' => $user->getRoleNames(),
            //     'permissions' => $user->getAllPermissions()->pluck('name'),
            // ],

        ]);

        $request->validate([
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function setupProfile(Request $request)
    {
        try {

            $user = $request->user();

            $data = $request->only([
                'name',
                'email',
                'parent_name',
                'password',
            ]);

            // Hash password if provided
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            // Upload avatar
            if ($request->hasFile('avatar')) {
                $media = $this->mediaService->upload(
                    $request->file('avatar'),
                    $user->id,
                    'avatar'
                );

                $data['avatar'] = $media->id;
            }

            // Mark profile completed
            $data['profile_completed'] = 1;

            // Update user
            $user->update($data);
            $user->refresh();

            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                $student = new Student();
                $student->user_id = $user->id;
                $student->roll_number = rand(11111, 99999);
            }

            $student->guardian_name = $request->parent_name;
            $student->save();

            /*
        |--------------------------------------------------------------------------
        | Create Direct Chat with First Super Admin
        |--------------------------------------------------------------------------
        */
            $admin = User::where('acc_type', 'admin')
                ->where('status', 1)
                ->orderBy('id')
                ->first();

            if ($admin && $admin->id != $user->id) {

                $conversation = Conversation::where('type', 'single')
                    ->whereHas('participants', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->whereHas('participants', function ($q) use ($admin) {
                        $q->where('user_id', $admin->id);
                    })
                    ->withCount('participants')
                    ->having('participants_count', 2)
                    ->first();

                if (!$conversation) {

                    DB::transaction(function () use ($admin, $user) {

                        $conversation = Conversation::create([
                            'type'       => 'single',
                            'title'      => null,
                            'created_by' => $admin->id,
                            'status'     => "active",
                        ]);

                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id'         => $admin->id,
                            'created_by'      => $admin->id,
                            'status'          => "active",
                        ]);

                        ConversationParticipant::create([
                            'conversation_id' => $conversation->id,
                            'user_id'         => $user->id,
                            'created_by'      => $admin->id,
                            'status'          => "active",
                        ]);
                    });
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Profile setup completed successfully',
                'user'    => new UserResource($user->load('avatar')),
            ]);
        } catch (\Exception $e) {

            Log::error('Setup Profile Error', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        return response()->json([
            'status' => true,
            'user' => auth('api')->user()->load('roles', 'permissions')
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status' => true,
            'message' => 'Logged out'
        ]);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        // Find the refresh token
        $refreshToken = RefreshToken::where('revoked', false)
            ->where('expires_at', '>', now())
            ->get()
            ->first(function ($token) use ($request) {
                return Hash::check($request->refresh_token, $token->token_hash);
            });

        if (!$refreshToken) {
            return response()->json(['error' => 'Invalid or expired refresh token'], 401);
        }

        $user = $refreshToken->user;


        // Generate new access token
        $newAccessToken = auth('api')->login($user);
        $accessTokenExpiry = now()->addMinutes(config('jwt.ttl', 60));

        // Revoke the used refresh token and create a new one (rotation)
        $refreshToken->update(['revoked' => true]);

        $newRefreshToken = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => Hash::make($newRefreshToken),
            'expires_at' => now()->addMonths(6),
            'access_token_expires_at' => $accessTokenExpiry,
        ]);

        return response()->json([
            'status' => true,
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'access_token_expires_at' => config('jwt.ttl', 43200) * 60,
            'refresh_token_expires_at' => config('jwt.refresh_ttl', 259200) * 60,
            'user' => [
                ...$user->toArray(),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }


    protected function respondWithToken($token)
    {
        $auth = auth('api');

        // Create refresh token with longer TTL (7 days)
        $originalTTL = config('jwt.ttl');
        config(['jwt.ttl' => 10080]); // 7 days in minutes
        $refreshToken = $auth->refresh();
        config(['jwt.ttl' => $originalTTL]); // Restore original TTL

        return response()->json([
            'status' => true,
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60,
            'user' => $auth->user()->load('roles', 'permissions')
        ]);
    }


    public function sendOtp(Request $request)
    {
        // Log::info('Received OTP request', ['phone' => $request->phone, 'device_id' => $request->device_id]);
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'device_id' => 'required|string',
        ]);

        $phone    = $request->phone;     // e.g. 918086544828
        $deviceId = $request->device_id; // e.g. unique device fingerprint

        $result = $this->otpService->sendOtp($phone, $deviceId);

        return response()->json($result, $result['success'] ? 200 : 500);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'OTP sent successfully',
        //     'otp' => '123456', // dev only
        // ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone'     => 'required|string',
            'otp'       => 'required|string|size:4',
            'device_id' => 'required|string',
        ]);

        try {

            $result = $this->otpService->verifyOtp(
                $request->phone,
                $request->otp,
                $request->device_id
            );




            if (!$result['success']) {
                Log::info('Failed');
                return response()->json($result, 422);
            }
            //   Log::info('Received OTP Subimt', ['phone' => $request->phone,'otp' => $request->otp, 'device_id' => $request->device_id]);

            $dummyName = 'User_' . substr($request->phone, -4);
            // Find or create user
            $user = User::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'name'      => $dummyName,
                    'device_id' => $request->device_id,
                    'password'  => Hash::make(str()->random(16)),
                ]
            );

            // $user->update(['device_id' => $request->device_id]);


            // $user = User::where('phone', $request->phone)->first();
            // // $user = User::first();
            // if (!$user) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'User not found'
            //     ], 404);
            // }

            $accessToken = auth('api')->login($user);

            $refreshToken = Str::random(128);

            RefreshToken::create([
                'user_id' => $user->id,
                'token_hash' => Hash::make($refreshToken),
                'expires_at' => now()->addMonths(6),
                'access_token_expires_at' => now()->addDays(30),
            ]);

            // (new FcmNotificationService())->welcomeMessage($user->id);

            $resposne = [
                'status' => true,

                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,

                'token_type' => 'Bearer',

                'expires_in' => now()->addDays(30)->timestamp,

                'refresh_expires_in' => now()->addMonths(6)->timestamp,

                'is_new_user' => $user->email == '' || $user->email == null ? true : false,

                'user' => new UserResource($user),
                // 'user' => [
                //     ...$user->toArray(),
                //     'role'  => $user->acc_type,
                //     'roles' => $user->getRoleNames(),
                //     'permissions' => $user->getAllPermissions()->pluck('name'),
                // ]
            ];

            return response()->json($resposne);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }








    // public function verifyOtp(Request $request)
    // {
    //     Log::info('Received OTP verification request', [
    //         'phone' => $request->phone,
    //         'code' => $request->code
    //     ]);

    //     // Clean phone number format
    //     // $phone = str_replace(' ', '', $request->phone);
    //     // $request->merge(['phone' => $phone]);

    //     // $request->validate([
    //     //     'phone' => 'required|string',
    //     //     'code' => 'required|string',
    //     // ]);

    //     try {
    //         // OTP check (dev logic)
    //         if ($request->code !== '1234') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid OTP',
    //             ], 422);
    //         }

    //         // 🔥 FIX: Find the SPECIFIC user by phone number instead of the first user
    //         $user = User::first();

    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'User not found with this phone number'
    //             ], 404);
    //         }

    //         // 🔥 FIX: Log the user in via the 'api' guard to get the JWT token
    //         $token = auth('api')->login($user);

    //         Log::info($token);

    //         // Generate refresh token logic
    //         $refreshToken = Str::random(64);

    //         RefreshToken::create([
    //             'user_id' => $user->id,
    //             'token_hash' => Hash::make($refreshToken),
    //             'expires_at' => now()->addDays(7),
    //             'access_token_expires_at' => now()->addMinutes(config('jwt.ttl', 60)),
    //         ]);

    //         // Return standardized token response
    //         return response()->json([
    //             'status' => true,
    //             'access_token' => $token,
    //             'refresh_token' => $refreshToken,
    //             'is_new_user' => false,
    //             'token_type' => 'Bearer',
    //             'expires_in' => config('jwt.ttl', 60) * 60,
    //             'refresh_expires_in' => config('jwt.refresh_ttl', 43200),
    //             'user' => [
    //                 ...$user->toArray(),
    //                 'roles' => $user->getRoleNames(),
    //                 'permissions' => $user->getAllPermissions()->pluck('name'),
    //             ],
    //         ]);
    //     } catch (Exception $e) {
    //         Log::info('OTP Verification Error: ' . $e->getMessage());

    //         // 🔥 FIX: Corrected response syntax array nesting error
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred during verification',
    //             'error' => $e->getMessage()
    //         ], 500); // Changed from 404 to 500 server error
    //     }
    // }
}
