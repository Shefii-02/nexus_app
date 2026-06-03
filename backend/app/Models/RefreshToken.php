<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'revoked'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
