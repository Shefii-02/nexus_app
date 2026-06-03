<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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
            'expires_in' => config('jwt.ttl', 60) * 60, // Convert to seconds
            'refresh_expires_in' => config('jwt.refresh_ttl', 43200), // 7 days in seconds

            'user' => [
                ...$user->toArray(),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],

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
            'expires_at' => now()->addMonth(),
            'access_token_expires_at' => $accessTokenExpiry,
        ]);

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 60) * 60, // Convert to seconds
            'refresh_expires_in' => config('jwt.refresh_ttl', 43200), // 7 days in seconds
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
        Log::info('Received OTP request', ['phone' => $request->phone]);
        $request->validate([
            'phone' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => '123456', // dev only
        ]);
    }

    public function verifyOtp(Request $request)
    {
        Log::info('Received OTP verification request', [
            'phone' => $request->phone,
            'code' => $request->code
        ]);
        $phone = str_replace(' ', '', $request->phone);
        $request->merge([
            'phone' => $phone
        ]);

        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        try{
        // OTP check (dev logic)
        if ($request->code !== '1234') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP',
            ], 422);
        }

        $user = \App\Models\User::first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'No users found'
            ], 404);
        }

        $token = auth('api')->fromUser($user);

        // 🔥 STEP 1: Get FIRST user (as you requested)


        // if (!$user) {
        //     Log::info('Not found User');
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No users found in system',
        //     ], 404);
        // }

        // // 🔥 STEP 2: Generate JWT token using api guard
        // $token = auth('api')->login($user);

        // 🔥 STEP 3: Refresh token logic (reuse your system)
        $refreshToken = Str::random(64);

        \App\Models\RefreshToken::create([
            'user_id' => $user->id,
            'token_hash' => Hash::make($refreshToken),
            'expires_at' => now()->addDays(7),
            'access_token_expires_at' => now()->addMinutes(config('jwt.ttl', 60)),
        ]);

        // 🔥 STEP 4: Return SAME response format as login()
        return response()->json([
            'status' => true,
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 60) * 60,
            'refresh_expires_in' => config('jwt.refresh_ttl', 43200),

            'user' => [
                ...$user->toArray(),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
        }
        catch(Exception $e){
               Log::info('Error :',$e->getMessage());
        }
    }
}
