<?php

namespace App\Services\Auth;

use App\Models\OtpVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private string $token;
    private string $phoneId;

    public function __construct()
    {
        $this->token   = config('whatsapp.token');
        $this->phoneId = config('whatsapp.phone_id');
    }

    // ─── Generate & Send OTP ────────────────────────────────────────────────
    public function sendOtp(string $phone, string $deviceId): array
    {


     // Dummy account for app store review / testing
        if ($phone === env('dummyNumber','+91 9846366783')) {
            return ['success' => true, 'message' => 'OTP sent successfully'];
        }


        // Check for an existing unused OTP
        $existing = OtpVerification::where('phone', $phone)
            ->where('is_used', false)
            ->latest()
            ->first();

        if ($existing) {
            // Resend the same OTP, just refresh expiry
            $existing->update(['expires_at' => now()->addMinutes(15)]);
            $otp = $existing->otp_code;
        } else {
            // Invalidate any old used OTPs and generate a fresh one
            OtpVerification::where('phone', $phone)
                ->update(['is_used' => true]);

            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            OtpVerification::create([
                'phone'      => $phone,
                'otp_code'   => $otp,
                'device_id'  => $deviceId,
                'type'       => 'phone',
                'is_used'    => false,
                'expires_at' => now()->addMinutes(15),
            ]);
        }

        // Send via WhatsApp
        $sent = $this->sendWhatsAppOtp($phone, $otp);

        if (!$sent) {
            return ['success' => false, 'message' => 'Failed to send OTP via WhatsApp'];
        }

        return ['success' => true, 'message' => 'OTP sent successfully'];
    }

    // ─── Verify OTP ─────────────────────────────────────────────────────────

    public function verifyOtp(string $phone, string $otp, string $deviceId): array
    {
        // Dummy account for app store review / testing
        if ($phone === env('dummyNumber') && $otp === env('dummyOtp')) {
            return ['success' => true, 'message' => 'OTP verified successfully'];
        }

        $record = OtpVerification::where('phone', $phone)
            ->where('is_used', false)
            ->latest()
            ->first();


        if (!$record) {
            return ['success' => false, 'message' => 'OTP not found. Please request a new one.'];
        }

        if ($record->isExpired()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        if ($record->device_id !== $deviceId) {
            return ['success' => false, 'message' => 'Device mismatch. Please request OTP again on this device.'];
        }

        if ($record->otp_code !== $otp) {
            return ['success' => false, 'message' => 'Invalid OTP. Please try again.'];
        }

        // Mark as used
        $record->update(['is_used' => true, 'verified_at' => now()]);

        return ['success' => true, 'message' => 'OTP verified successfully'];
    }

    // ─── Send WhatsApp Message ───────────────────────────────────────────────

    private function sendWhatsAppOtp(string $phone, string $otp): bool
    {
        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v25.0/{$this->phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'                => $phone,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => 'auth',
                        'language' => ['code' => 'en'],
                        'components' => [
                            [
                                'type'       => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $otp]
                                ]
                            ],
                            [
                                'type'       => 'button',
                                'sub_type'   => 'url',
                                'index'      => '0',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $otp]
                                ]
                            ]
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('WhatsApp OTP Error', $response->json());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp OTP Exception: ' . $e->getMessage());
            return false;
        }
    }
}
