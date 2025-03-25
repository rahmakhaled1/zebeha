<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $table = 'user_otps';

    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
        'type',
        'phone',
        'is_verified',
        'verified_at'
    ];

    protected $casts = [
        'otp_expire_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
