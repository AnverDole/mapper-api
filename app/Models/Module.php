<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Subset;

class Module extends Model
{
    use HasFactory;


    public static $HIGH_PRIORITY = 1;
    public static $MEDIUM_PRIORITY = 2;
    public static $LOW_PRIORITY = 3;

    public static $PRIORITIES = [
        1 => "High Priority",
        2 => "Medium Priority",
        3 => "Low Priority",
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject_id', 'title', 'duration', 'priority', 'is_fully_scheduled'
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
    protected $casts = [];


    public function Subject()
    {
        return $this->belongsTo(Subject::class, "subject_id");
    }
    public function Schedule()
    {
        return $this->hasMany(ScheduleSlot::class, "module_id");
    }

    public function getPriorityTextAttribute()
    {
        switch ($this->priority) {
            case self::$LOW_PRIORITY:
                return "Low Priority";
            case self::$MEDIUM_PRIORITY:
                return "Medium Priority";
            case self::$HIGH_PRIORITY:
                return "High Priority";
            default:
                return "None";
        }
    }

}
