<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'mobile_no',
        'company_name',
        'profile',
        'gender',
        'status',
        'dob',
        'otp',
        'address',
        'state',
        'city',
        'pincode',
        'is_live',
        'login_status',
        'is_deleted',
        'about_us',
        'experence_id',
        'latitude',
        'longtitude',
        'device_id',
        'device_type',
        'vehicle_type_id',
        'vehicle_number',
        'device_name',
        'device_token',
        'remember_token',
        'api_token', 
        'referral_code',
        'notification_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
