<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class PreUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'driver_pre_register';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'gender',
        'mobile_no',
        'password',
        'state',
        'city',
        'vehicle_type_id',
        'latitude',
        'longtitude',
        'vehicle_number',
        'vehicle_manufacture',
        'vehicle_model',
        'vehicle_type',
        'vehicle_fuel_type',
        'vehicle_owner_name',
        'country_operating',
        'vehicle_reg_year',
        'photo1',
        'photo2',
        'certificate1',
        'certificate2',
        'vehicle_audit_photo1',
        'vehicle_audit_photo2',
        'dob',
        'age',
        'licence_number',
        'profile',
        'driving_licence', 
        'driving_licence2',
        'id_proof',
        'id_proof2',
        'bank_ac_holder_name',
        'bank_account',
        'bank_name',
        'branch', 
        'code',
        'api_token',
        'remember_token',
        'created_at',
        'updated_at',
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
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}
