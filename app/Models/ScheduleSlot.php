<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSlot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'module_id',
        'duration',
        'start_at',
        'end_at',
        'is_finished'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "start_at" => "datetime",
        "end_at" => "datetime",
    ];

    public function User()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function Module()
    {
        return $this->belongsTo(Module::class, "module_id");
    }
}
