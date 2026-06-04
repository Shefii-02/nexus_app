<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Traits\JsonResponseTrait;
use Exception;
use Google\Service\CloudSourceRepositories\Repo;
use Google_Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
  use AuthorizesRequests, ValidatesRequests, JsonResponseTrait;

  public function userExistNot($mobileNo = null, $company_id = null)
  {
    $user =  User::where('mobile', $mobileNo)->where('company_id', $company_id)->first();
    if ($user) {
      return true;
    } else {
      return false;
    }
  }

  public function googleLoginCheck(Request $request)
  {

    try {
      $authUser = $request->user(); // may be null
      $idToken  = $request->input('idToken');
      $emailID  = $request->input('email');

      if (!$idToken && !$emailID) {
        return response()->json([
          'status' => false,
          'message' => 'Missing idToken or email',
        ], 400);
      }

      $email = null;
      $payload = null;

      // ✅ Verify Google ID Token (ONLY if token exists)
      if ($idToken) {


        $client = new \Google_Client([
          'client_id' => config('services.google.client_id'), // WEB CLIENT ID
        ]);

        $payload = $client->verifyIdToken($idToken);

        // if (!$payload) {
        //   Log::warning('Invalid Google ID token');
        //   return response()->json([
        //     'status' => false,
        //     'message' => 'Invalid Google token',
        //   ], 401);

        // }
        if ($payload) {
          $email = $payload['email'] ?? null;
        }
      }

      // ✅ Fallback email (optional)
      if (!$email && $emailID) {
        $email = $emailID;
      }

      if (!$email) {
        return response()->json([
          'status' => false,
          'message' => 'Email not found',
        ], 400);
      }


      // ✅ If already authenticated user (link Google)
      if ($authUser) {
        $authUser->email = $email;
        $authUser->email_verified_at = now();
        $authUser->save();

        return response()->json([
          'success' => true,
          'user' => $authUser,
        ], 200);
      }

      // ✅ Find existing user
      $user = User::where('email', $email)
        ->where('company_id', 1)
        ->first();

      if (!$user) {
        return response()->json([
          'status' => false,
          'message' => 'Account not found. Please sign up normally.',
        ], 404);
      }

      // ✅ Login existing user
      // $user->tokens()->delete();
      $token = $user->createToken('auth_token')->plainTextToken;

      $user->email_verified_at = now();
      $user->last_login = now();
      $user->save();

      $this->LoginActivityStore($user, $request);

      return response()->json([
        'success' => true,
        'message' => 'Login successfully',
        'user'    => $user,
        'token'   => $token,
      ], 200);
    } catch (\Throwable $e) {
      Log::error('Google login failed', [
        'error' => $e->getMessage(),
      ]);

      return response()->json([
        'status' => false,
        'message' => 'Google login failed',
      ], 500);
    }
  }

  public function AppleLoginCheck(Request $request)
  {
    Log::info($request->all());
    Log::info('Apple Details');
    try {
      $authUser = $request->user(); // may be null
      $idToken  = $request->input('idToken');
      $emailID  = $request->input('email');

      if (!$emailID) {
        return response()->json([
          'status' => false,
          'message' => 'Missing email',
        ], 400);
      }

      $email = $emailID;


      if (!$email) {
        return response()->json([
          'status' => false,
          'message' => 'Email not found',
        ], 400);
      }


      // ✅ Find existing user
      $user = User::where('email', $email)
        ->where('company_id', 1)
        ->first();

      if (!$user) {
        return response()->json([
          'status' => false,
          'message' => 'Account not found. Please sign up normally.',
        ], 200);
      }

      // ✅ Login existing user
      // $user->tokens()->delete();
      $token = $user->createToken('auth_token')->plainTextToken;

      $user->email_verified_at = now();
      $user->last_login = now();
      $user->save();

      $this->LoginActivityStore($user, $request, 'apple');

      return response()->json([
        'success' => true,
        'message' => 'Login successfully',
        'user'    => $user,
        'token'   => $token,
      ], 200);
    } catch (\Throwable $e) {
      Log::error('Apple login failed', [
        'error' => $e->getMessage(),
      ]);

      return response()->json([
        'status' => false,
        'message' => 'Apple login failed',
      ], 500);
    }
  }


  // public function googleLoginCheck(Request $request)
  // {
  //   try {

  //     $user = $request->user();


  //     $idToken = $request->input('idToken');
  //     $emialID = $request->input('email');

  //     if (!$idToken && !$emialID) {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Missing idToken'
  //       ]);
  //     }

  //     // ✅ Verify token using Google API client
  //     $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
  //     $payload = $client->verifyIdToken($idToken);



  //     if (!$payload && !$emialID) {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Invalid Google token'
  //       ]);
  //     }


  //     $email = $payload['email'] ?? $emialID;

  //     if (!$email) {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Email not found in token'
  //       ]);
  //     }


  //     // ✅ Check if email exists in your users table
  //     $userEx = User::where('email', $email)->where('company_id', 1)->first();
  //     if ($user) {
  //       $user->email = $email;
  //       $user->email_verified_at = now();
  //       $user->save();
  //       return response()->json([
  //         'status' => 'success',
  //         'user' => $user
  //       ]);
  //     } else if ($userEx) {
  //       //login user
  //       // Revoke all existing tokens
  //       $userEx->tokens()->delete();

  //       // Generate token if using Sanctum
  //       $token = $userEx->createToken('auth_token')->plainTextToken;
  //       $userEx->email_verified_at = now();
  //       $userEx->save();
  //       $userEx->refresh();
  //       return response()->json([
  //         'success' => true,
  //         'message' => 'Login successfully',
  //         'user'    => $userEx,
  //         'token'   => $token,
  //       ], 200);
  //     } else {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Account not found. Please sign up normally.',
  //       ]);
  //     }
  //   } catch (\Exception $e) {
  //     return response()->json([
  //       'status' => 'error',
  //       'message' => $e->getMessage(),
  //     ]);
  //   }
  // }

  public function  userDataRetrieve(Request $request)
  {
    try {
      $user = $request->user();
      return response()->json([
        'success' => true,
        'message' => 'User data fetched successfully',
        'user'    => $user,
        'referral_code' => 'BMT-9834',
      ], 200);
    } catch (Exception $e) {
      Log::error('User data getting  failed: ' . $e->getMessage());
      return response()->json([
        'status' => false,
        'message' => $e->getMessage(),
      ]);
    }
  }

  public function userVerifyEmail(Request $request)
  {
    try {
      $user = $request->user();

      if ($user->email_verified_at) {
        return response()->json([
          'success' => true,
          'message' => 'Email already verified.',
        ], 200);
      }

      $idToken = $request->input('idToken');
      $emailID = $request->input('email');

      if (!$idToken && !$emailID) {
        return response()->json([
          'status' => false,
          'message' => 'Missing idToken or email',
        ], 400);
      }

      $email = null;
      $payload = null;

      // ✅ Verify Google ID token if provided
      if ($idToken) {

        $client = new \Google_Client([
          'client_id' => config('services.google.client_id'), // WEB CLIENT ID
        ]);

        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
          return response()->json([
            'status' => false,
            'message' => 'Invalid Google token',
          ], 401);
        }



        $email = $payload['email'] ?? null;
      }

      // ✅ Fallback to email (optional)
      if (!$email && $emailID) {
        $email = $emailID;
      }

      if (!$email) {
        return response()->json([
          'status' => false,
          'message' => 'Email not found',
        ], 400);
      }

      // ✅ Update user
      $user->email = $email;
      $user->email_verified_at = now();
      $user->save();



      return response()->json([
        'success' => true,
        'message' => 'User email verified successfully',
      ], 200);
    } catch (\Throwable $e) {
      Log::error('User email verification failed', [
        'error' => $e->getMessage(),
      ]);

      return response()->json([
        'status' => false,
        'message' => 'Email verification failed',
      ], 500);
    }
  }


  private function LoginActivityStore($user, Request $request, $provider = 'google')
  {
    LoginActivity::create([
      'company_id'   => $user->company_id,
      'user_id'      => $user->id,
      'provider'     => $provider,
      'source'       => $request->header('X-APP-SOURCE', 'android'),
      'email'        => $user->email,
      'ip_address'   => $request->ip(),
      'user_agent'   => $request->userAgent(),
      'logged_in_at' => now(),
    ]);
  }


  public function appleCallback(Request $request)
  {
    $appleUser = Socialite::driver('apple')->stateless()->user();

    // Apple sometimes hides email after first login
    $email = $appleUser->email;

    $user = User::where('apple_id', $appleUser->id)
      ->orWhere('email', $email)
      ->first();

    if (!$user) {
      $user = User::create([
        'name'     => $appleUser->name ?? 'Apple User',
        'email'    => $email,
        'apple_id' => $appleUser->id,
      ]);
    } else {
      // attach apple id if user existed
      if (!$user->apple_id) {
        $user->apple_id = $appleUser->id;
        $user->save();
      }
    }

    // ✅ STORE LOGIN ACTIVITY
    LoginActivity::create([
      'user_id'      => $user->id,
      'provider'     => 'apple',
      'source'       => $request->header('X-APP-SOURCE', 'web'),
      'email'        => $email,
      'ip_address'   => $request->ip(),
      'user_agent'   => $request->userAgent(),
      'logged_in_at' => now(),
    ]);

    return redirect()->route('dashboard');
  }


  // public function userVerifyEmail(Request $request)
  // {
  //   try {
  //     $user = $request->user();
  //     if ($user->email_verified_at) {
  //       return response()->json([
  //         'success' => true,
  //         'message' => 'Email already verified.',
  //       ], 200);
  //     }

  //     $idToken = $request->input('idToken');
  //     $emailID = $request->input('email');

  //     if (!$idToken && !$emailID) {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Missing idToken'
  //       ]);
  //     }

  //     // ✅ Verify token using Google API client
  //     $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
  //     $payload = $client->verifyIdToken($idToken);

  //     if (!$payload && !$emailID) {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Invalid Google token'
  //       ]);
  //     }

  //     $email = $payload['email'] ?? $emailID;

  //     if (!$email) {
  //       return response()->json([
  //         'status' => 'error',
  //         'message' => 'Email not found in token'
  //       ]);
  //     }


  //     $user->email = $email;
  //     $user->email_verified_at = now();

  //     $user->save();
  //     Log::error('User email verification successfully: ' . $user->email);
  //     return response()->json([
  //       'success' => true,
  //       'message' => 'User Email Verification successfully',
  //     ], 200);
  //   } catch (Exception $e) {
  //     Log::error('User email verification failed: ' . $e->getMessage());

  //     return response()->json([
  //       'status' => false,
  //       'message' => $e->getMessage(),
  //     ]);
  //   }
  // }
}
