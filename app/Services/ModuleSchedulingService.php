<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ScheduleSlot;
use App\Models\Settings;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;
use SplPriorityQueue;

class ModuleSchedulingService
{

    public const ALGO_HIGH_PRIORITY_FIRST = 30;
    public const ALGO_LOW_PRIORITY_FIRST = 10;

    public static function schedule(array $config, Closure $fetchTasks, Closure $taskForDay)
    {

        $config = (object)$config;
        $date = Carbon::today();

        $dayStart = Carbon::createFromFormat("H:i", $config->day_starts_at)
            ->setDate($date->year, $date->month, $date->day);
        $dayEnd = Carbon::createFromFormat("H:i", $config->day_ends_at)
            ->setDate($date->year, $date->month, $date->day);

        // if (Carbon::now()->isAfter($dayStart) && Carbon::now()->isBefore($dayEnd) && Carbon::now() != $dayStart->format("Y-m-d H")) {
        //     $start = Carbon::now()->addHours($config->delay_on_today_start / 60)->setMinutes(0);
        //     $dayStart->setTime($start->hour, $start->minute);
        // } else if (Carbon::now()->isAfter($dayEnd)) {
        //     $date = Carbon::tomorrow();
        //     $dayStart->setDate($date->year, $date->month, $date->day);
        //     $dayEnd->setDate($date->year, $date->month, $date->day);
        // }

        $days = 7;
        while (true) {

            $pQueue = new SplPriorityQueue();


            $timeCursor = (clone $dayStart);

            $tasks = $fetchTasks($dayStart->format("Y-m-d"), $dayStart->hour, $dayStart->minute);

            if (count($tasks) < 1) break;

            foreach ($tasks as $task) {
                $pQueue->insert($task, self::transformPrioraty($config->prioritization, $config->min_priority, $config->max_priority, $task->priority));
            }

            $slots = [];
            while ($pQueue->valid()) {

                $task = $pQueue->extract();

                $module = Module::find($task->module_id);

                $start = (clone $timeCursor);

                $duration = min([
                    $config->activity_max_duration,
                    // $module->duration
                ]);

                $end = $timeCursor->addMinutes($duration);

                if ($end->isAfter($dayEnd)) {
                    break;
                }

                $slots[] = (object)[
                    "module_id" => $module->id,
                    "starts_at" => (clone $start),
                    "ends_at" => (clone $end)
                ];


                $timeCursor->addMinutes($config->duration_detween_activities);
            }

            $taskForDay($date, $slots);

            $date->addDay();
            $dayStart = Carbon::createFromFormat("H:i", $config->day_starts_at)
                ->setDate($date->year, $date->month, $date->day);
            $dayEnd = Carbon::createFromFormat("H:i", $config->day_ends_at)
                ->setDate($date->year, $date->month, $date->day);

            if ($days  <= 1) {
                break;
            }
        }
    }

    private static function transformPrioraty($prioritization, $minPriority, $maxPriority, $from)
    {
        $new = $from;

        if ($prioritization == Settings::LOW_PRIORITY_CATEGORY) {
            $new = abs($from - $maxPriority) + 1;
        }

        if ($prioritization == Settings::RANDOM_PRIORITY_CATEGORY){
            $new = random_int($minPriority, $maxPriority);
        }



        return $new;
    }
}
