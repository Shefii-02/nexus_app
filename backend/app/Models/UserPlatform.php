<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlatform extends Model
{
  //

  protected $fillable = ['user_id', 'company_id', 'platform', 'ip_address', 'device_info', 'app_version', 'last_active_at', 'fcm_token', 'status', 'device_id', 'latitude', 'longitude', 'district', 'city','voip_token'];
}
