<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'first_name', 'last_name', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function passwordResetOTPs()
    {
        return $this->hasMany(PasswordResetOtp::class, "user_id");
    }
    public function Settings()
    {
        return $this->hasOne(Settings::class, "user_id");
    }
    public function Subjects()
    {
        return $this->hasMany(Subject::class, "user_id");
    }
    public function ScheduleSlots()
    {
        return $this->hasMany(ScheduleSlot::class, "user_id");
    }
    public function Modules(){
        return $this->hasManyThrough(Module::class, Subject::class, "user_id", "subject_id");
    }
}
