<?php

namespace App\Jobs;

use App\Models\Module;
use App\Models\ScheduleSlot;
use App\Models\User;
use App\Services\ModuleSchedulingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenereateSchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ModuleSchedulingService::schedule([
            "prioritization" => $this->user->Settings->prioritization,
            "max_priority" => Module::$HIGH_PRIORITY,
            "min_priority" => Module::$LOW_PRIORITY,
            "duration_detween_activities" => $this->user->Settings->duration_between_activities,
            "activity_max_duration" => $this->user->Settings->activity_max_duration,
            "day_starts_at" => $this->user->Settings->day_starts_at->format("H:i"),
            "day_ends_at" => $this->user->Settings->day_ends_at->format("H:i"),
            "delay_on_today_start" => 2 * 60 //delay today's tasks by 2 hours
        ], function ($date, $startTime, $endTime) {
            return $this->user->Modules()->where("is_fully_scheduled", false)
                ->orderBy("created_at", "ASC")
                ->get()
                ->groupBy("subject_id")
                ->map(function ($group) {
                    $module = $group->first();
                    return (object)[
                        "module_id" => $module->id,
                        "priority" => $module->priority,
                        "total_duration" => $module->duration,
                        "currently_schedule_duration" => ScheduleSlot::where("module_id", $module->id)->sum("duration")
                    ];
                });
        }, function ($date, $tasks) {
            foreach ($tasks as $task) {

                $module = Module::findorfail($task->module_id);
                ScheduleSlot::create([
                    'user_id' => $this->user->id,
                    "subject_id" => $module->subject_id,
                    'module_id' => $module->id,
                    'duration' => $task->starts_at->diffInMinutes($task->ends_at),
                    'start_at' => $task->starts_at,
                    'end_at' => $task->ends_at
                ]);

                $durationInMinutues = explode( ":", $module->duration);
                $durationInMinutues = $durationInMinutues[0] * 60  + $durationInMinutues[1];
                $module->is_fully_scheduled = ScheduleSlot::where("module_id", $module->id)->sum("duration") >= $durationInMinutues;
                $module->save();
            }
        });
    }
}
