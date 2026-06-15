<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{

    protected $fillable = [
       'email' , 'phone', 'otp_code', 'type', 'device_id', 'is_used', 'expires_at','verified_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_used'    => 'boolean',
    ];

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function isValid(string $otp, string $device_id): bool
    {
        return !$this->is_used
            && !$this->isExpired()
            && $this->otp === $otp
            && $this->device_id === $device_id;
    }
}
