<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ¡Importante!

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code', // Ocultar el hash del OTP
        'otp_expires_at',
    ];

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
