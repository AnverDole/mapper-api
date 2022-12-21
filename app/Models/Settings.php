<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    public const HIGH_PRIORITY_CATEGORY = 100;
    public const LOW_PRIORITY_CATEGORY = 200;
    public const RANDOM_PRIORITY_CATEGORY = 300;
    public static $priorityCategories  = [
        self::HIGH_PRIORITY_CATEGORY => "Highest Priority First",
        self::LOW_PRIORITY_CATEGORY => "Lowest Priority First",
        self::RANDOM_PRIORITY_CATEGORY => "Random"
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        "prioritization",
        "duration_between_activities",
        "activity_max_duration",
        "day_starts_at",
        "day_ends_at"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "day_starts_at" => 'datetime:H:i:s',
        "day_ends_at" => 'datetime:H:i:s',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
