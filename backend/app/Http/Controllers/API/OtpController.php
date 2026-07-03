<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\Otp;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Traits\JsonResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Response;

class OtpController extends Controller
{
  use JsonResponseTrait;

  /**
   * Send OTP for Sign In
   */
  public function sendOtpSignIn(Request $request)
  {
    $request->validate([
      'mobile' => 'required|digits:10',
    ]);

    $mobile = '91' . $request->mobile;
    $expTime = 20;

    // check existing unverified otp
    $existingOtp = OtpVerification::where('mobile', $mobile)
      ->where('verified', 0) // unverified
      ->orderBy('created_at', 'DESC')
      ->first();

    if ($existingOtp) {
      // Reuse old otp → update expiry + attempt
      $existingOtp->update([
        'attempt'    => $existingOtp->attempt + 1,
        'expires_at' => Carbon::now()->addMinutes($expTime),
      ]);

      $otp = $existingOtp->otp;
    } else {
      // Generate new OTP
      $otp = rand(1000, 9999);

      Otp::create([
        'mobile'     => $mobile,
        'otp'        => $otp,
        'expires_at' => Carbon::now()->addMinutes($expTime),
        'company_id' => $company_id,
        'type'       => 'mobile',
        'attempt'    => 1
      ]);
    }

    if ($mobile == env('TEST_MOBILE') ||  $mobile == "918075261300") {
      return $this->success('OTP sent successfully', ['mobile' => $mobile]);
    }

    // send otp
    $response = $this->SmsApiFunction($mobile, $otp, $expTime);



    if ($response && $response->successful()) {
      return $this->success('OTP sent successfully', ['mobile' => $mobile]);
    } else if (!env('SMSOTP', false)) {
      return $this->success('OTP sent successfully (Debug Mode)', ['mobile' => $mobile]);
    }

    return $this->error('Failed to send OTP', Response::HTTP_BAD_REQUEST);
  }



  /**
   * Send OTP for Sign Up
   */
  public function sendOtpSignUp(Request $request)
  {
    $request->validate([
      'mobile' => 'required|digits:10',
    ]);

    $company_id = 1;
    $mobile = '91' . $request->mobile;
    $expTime = 20;

    // Check if user already exists
    $userr = User::where('mobile', $mobile)
      ->where('company_id', $company_id)
      ->where('profile_fill', 1)
      ->exists();

    if ($userr) {
      return $this->error('User already registered, Please Sign In', Response::HTTP_CONFLICT);
    }

    // Check if there is already an unverified OTP
    $existingOtp = Otp::where('mobile', $mobile)
      ->where('company_id', $company_id)
      ->where('verified', 0) // not verified
      ->orderBy('created_at', 'DESC')
      ->first();

    if ($existingOtp) {
      // Reuse existing OTP and update attempt count & expiry
      $existingOtp->update([
        'attempt'    => $existingOtp->attempt + 1,
        'expires_at' => Carbon::now()->addMinutes($expTime),
      ]);

      $otp = $existingOtp->otp;
    } else {
      // Generate new OTP
      $otp = rand(1000, 9999);

      Otp::create([
        'mobile'     => $mobile,
        'otp'        => $otp,
        'expires_at' => Carbon::now()->addMinutes($expTime),
        'company_id' => $company_id,
        'type'       => $request->has('type') ? $request->type : 'mobile',
        'attempt'    => 1
      ]);
    }

    // Send OTP
    $response = $this->SmsApiFunction($mobile, $otp, $expTime);

    if ($response && $response->successful()) {
      return $this->success('OTP sent successfully', ['mobile' => $mobile]);
    } else if (!env('SMSOTP', true)) {
      return $this->success('OTP sent successfully (Debug Mode)', ['mobile' => $mobile]);
    }

    return $this->error('Failed to send OTP', Response::HTTP_BAD_REQUEST);
  }



  /**
   * Re Send OTP
   */

  public function reSendOtp(Request $request)
  {
    $request->validate([
      'mobile' => 'required|digits:10',
    ]);

    $company_id = 1;
    $mobile = '91' . $request->mobile; // add country code

    // Get last unverified OTP
    $otpData = Otp::where('mobile', $mobile)
      ->where('company_id', $company_id)
      ->where('verified', 0)
      ->orderBy('created_at', 'desc')
      ->first();

    if (!$otpData) {
      return $this->error('Resending Failed. Please close the app and retry.', Response::HTTP_NOT_FOUND);
    }

    $otp     = $otpData->otp;
    $expTime = 20;

    // ✅ Update existing OTP row
    $otpData->update([
      'expires_at' => Carbon::now()->addMinutes($expTime),
      'attempt'    => $otpData->attempt + 1, // increase attempt count
    ]);

    // Send SMS
    $response = $this->SmsApiFunction($mobile, $otp, $expTime);

    if ($response && $response->successful()) {
      return $this->success('Re-send OTP sent successfully', [
        'mobile' => $mobile,
        'otp_id' => $otpData->id,
      ]);
    } elseif (!env('SMSOTP', false)) {
      // ✅ Allow bypass in dev mode
      return $this->success('Re-send OTP sent successfully (DEV MODE)', [
        'mobile' => $mobile,
        'otp_id' => $otpData->id,
      ]);
    }

    return $this->error('Failed to send OTP', Response::HTTP_BAD_REQUEST);
  }

  /**
   * Verify OTP for Sign In
   */
  // public function verifyOtpSignIn(Request $request)
  // {
  //   $request->validate([
  //     'mobile' => 'required',
  //     'otp'    => 'required',
  //   ]);

  //   $mobile = '91' . $request->mobile;
  //   $otpInput = $request->otp;

  //   $otpRecord = Otp::where('mobile', $mobile)
  //     ->where('otp', $otpInput)
  //     ->where('verified', 0)
  //     ->where('expires_at', '>=', now())
  //     ->latest()
  //     ->first();

  //   if (!$otpRecord) {
  //     return $this->error('Invalid or expired OTP', Response::HTTP_UNAUTHORIZED);
  //   }
  //   User::where('mobile', $mobile)->where('company_id', 1)->update(['mobile_verified' => 1]);
  //   if ($mobile == '919846366783' && $otpInput == '7878') {
  //     $otpRecord->update(['verified' => false]);
  //   } else {
  //     $otpRecord->update(['verified' => true]);
  //   }

  //   $user = User::where('mobile', $mobile)->where('company_id', 1)->first();

  //   if (!$user) {
  //     $user = new User();
  //     $user->mobile          = $mobile;
  //     $user->mobile_verified = true;
  //     $user->company_id      = 1;
  //     $user->profile_fill    = 0;
  //     $user->save();
  //   } else {
  //     $user->update(['mobile_verified' => true]);
  //   }

  //   // return $this->success('OTP verified successfully');
  //   return response()->json([
  //     'success' => true,
  //     'message' => 'OTP verified successfully',
  //     'user'    => $user,
  //   ], 200);
  // }

  public function verifyOtpSignIn(Request $request)
  {
    $request->validate([
      'mobile' => 'required|digits:10',
      'otp'    => 'required|digits:4',
    ]);

    $mobile = '91' . $request->mobile;

    $otpRecord = Otp::where('mobile', $mobile)
      ->where('otp', $request->otp)
      ->where('verified', 0)
      ->where('expires_at', '>=', now())
      ->latest()
      ->first();

    if (!$otpRecord) {
      return $this->error('Invalid or expired OTP', Response::HTTP_UNAUTHORIZED);
    }

    // Mark OTP as verified (skip for test number)
    if (($mobile == env('TEST_MOBILE') && $request->otp == env('TEST_OTP')) || ($mobile == "918075261300" && $request->otp == '7878')) {
      $otpRecord->update(['verified' => 0]); // keep unverified for testing
    } else {
      $otpRecord->update(['verified' => 1]);
    }

    // Fetch existing user
    $user = User::where('mobile', $mobile)->where('company_id', 1)->first();


    if (!$user) {
      $user = new User();
      $user->mobile          = $mobile;
      $user->mobile_verified = true;
      $user->company_id      = 1;
      $user->profile_fill    = 0;
      $user->last_login = now();
      $user->save();
    } else {
      $user->update(['mobile_verified' => true, 'last_login' => now()]);
    }

    // Revoke all existing tokens
    // $user->tokens()->delete();

    // Generate token if using Sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
      'success' => true,
      'message' => 'OTP verified successfully',
      'user'    => $user,
      'token'   => $token,
    ], 200);
  }


  /**
   * Verify OTP for Sign Up
   */
  public function verifyOtpSignUp(Request $request)
  {
    $request->validate([
      'mobile' => 'required|digits:10',
      'otp'    => 'required|digits:4',
    ]);

    $mobile = '91' . $request->mobile;
    $otpInput = $request->otp;
    $company_id = 1;

    $otpRecord = Otp::where('mobile', $mobile)
      ->where('otp', $otpInput)
      ->where('verified', 0)
      ->where('expires_at', '>=', now())
      ->latest()
      ->first();

    if (!$otpRecord) {
      return $this->error('Invalid or expired OTP', Response::HTTP_UNAUTHORIZED);
    }

    $otpRecord->update(['verified' => true]);

    $user = User::firstOrCreate(
      ['mobile' => $mobile, 'company_id' => $company_id],
      [
        'mobile_verified' => true,
        'profile_fill'    => 0,
        'last_login'      => now(),
      ]
    );

    // Revoke all existing tokens
    $user->tokens()->delete();

    $token = $user->createToken('auth_token')->plainTextToken;

    return $this->success('OTP verified successfully', [
      'token' => $token,
      'mobile' => $mobile
    ]);
  }

  // public function verifyOtpSignUp(Request $request)
  // {
  //   $request->validate([
  //     'mobile' => 'required',
  //     'otp'    => 'required',
  //   ]);

  //   $mobile = '91' . $request->mobile;
  //   $otpInput  = $request->otp;
  //   $company_id = 1;

  //   $otpRecord = Otp::where('mobile', $mobile)
  //     ->where('otp', $otpInput)
  //     ->where('verified', 0)
  //     ->where('expires_at', '>=', now())
  //     ->latest()
  //     ->first();

  //   if (!$otpRecord) {
  //     return $this->error('Invalid or expired OTP', Response::HTTP_UNAUTHORIZED);
  //   }

  //   $otpRecord->update(['verified' => true]);

  //   $user = new User();
  //   $user->mobile          = $mobile;
  //   $user->mobile_verified = true;
  //   $user->company_id      = $company_id;
  //   $user->profile_fill    = 0;
  //   $user->save();

  //   return $this->success('OTP verified successfully');
  // }


  /**
   * Verify OTP for Guest Sign Up
   */

  public function verifyGuestOtp(Request $request)
  {
    $request->validate([
      'mobile' => 'required|digits:10',
      'otp'    => 'required|digits:4',
    ]);

    $mobile = '91' . $request->mobile;
    $company_id = 1;

    $otpRecord = Otp::where('mobile', $mobile)
      ->where('company_id', $company_id)
      ->where('otp', $request->otp)
      ->where('expires_at', '>=', now())
      ->where('verified', 0)
      ->first();

    if (!$otpRecord) {
      return $this->error('Invalid or expired OTP', Response::HTTP_UNAUTHORIZED);
    }

    // mark otp as used
    $otpRecord->update(['verified' => 1]);


    // check if user exists

    $user = User::firstOrCreate(
      ['mobile' => $mobile, 'company_id' => $company_id],
      [
        'mobile_verified' => true,
        'acc_type'       => 'guest',
        'profile_fill'   => 0,
      ]
    );

    // $user = User::where('mobile', $mobile)->where('company_id', $company_id)->first();

    // if (!$user) {
    //   // create guest account
    //   $user = User::create([
    //     'mobile'     => $mobile,
    //     'company_id' => $company_id,
    //     'mobile_verified' => true,
    //     'acc_type'       => 'guest',
    //     'profile_fill' => 0,
    //   ]);
    // }


    $user->tokens()->delete();
    // generate token / login
    $token = $user->createToken('auth_token')->plainTextToken;

    return $this->success('OTP Verified successfully', [
      'token' => $token,
      'mobile' => $mobile,
      'acc_type'  => $user->acc_type,
    ]);
  }


  /**
   * Send OTP via Email
   */
  public function sendEmailOtp(Request $request)
  {
    $request->validate([
      'email' => 'required|email'
    ]);

    $email      = $request->email;
    $company_id = 1;
    $otp        = rand(1000, 9999);
    $expTime    = 20;

    Otp::create([
      'mobile'     => $email, // ⚠️ could rename to 'email' in DB for clarity
      'otp'        => $otp,
      'expires_at' => Carbon::now()->addMinutes($expTime),
      'company_id' => $company_id,
      'type'       => 'email'
    ]);

    try {
      Mail::to($email)->send(new SendOtpMail($otp, $expTime));
      return $this->success('OTP sent successfully', ['email' => $email]);
    } catch (\Exception $e) {
      Log::error("Email OTP failed: " . $e->getMessage());
      return $this->error('Failed to send OTP: ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
    }
  }

  /**
   * Verify Email OTP
   */
  public function verifyEmailOtp(Request $request)
  {
    $request->validate([
      'email' => 'required',
      'otp'   => 'required',
    ]);

    $email    = $request->email;
    $otpInput = $request->otp;

    $otpRecord = Otp::where('mobile', $email)
      ->where('otp', $otpInput)
      ->where('verified', 0)
      ->where('expires_at', '>=', now())
      ->where('type', 'email')
      ->latest()
      ->first();

    if (!$otpRecord) {
      return $this->error('Invalid or expired OTP', Response::HTTP_UNAUTHORIZED);
    }

    $otpRecord->update(['verified' => true]);
    User::where('email', $email)->where('company_id', 1)->update(['email_verified_at' => date('Y-m-d H:i:s')]);

    return $this->success('OTP verified successfully');
  }




  /**
   * Reusable SMS Function
   */
  private function SmsApiFunction($mobile = null, $otp = null, $expTime = 20)
  {
    if ($mobile && $otp && env('SMSOTP', true)) {
      $response =  Http::get("https://www.smsgatewayhub.com/api/mt/SendSMS", [
        'APIKey'        => config('services.smsgatewayhub.key'),
        'senderid'      => config('services.smsgatewayhub.senderid'),
        'channel'       => 2,
        'DCS'           => 0,
        'flashsms'      => 0,
        'number'        => $mobile,
        'text'          => "{$otp} is your One Time Password (OTP) for login/signup at BookMyTeacher By Pachavellam Education.This OTP will only be valid for {$expTime} minutes. Do not share anyone",
        'route'         => 54,
        'EntityId'      => config('services.smsgatewayhub.entity_id'),
        'dlttemplateid' => config('services.smsgatewayhub.template_id'),
      ]);

      return $response;
    }
    return false;
  }

  /**
   * Check if user exists
   */
  public function userExistNot($mobileNo = null, $company_id = null)
  {
    return User::where('mobile', $mobileNo)->where('company_id', $company_id)->exists();
  }
}
