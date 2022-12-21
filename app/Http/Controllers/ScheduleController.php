<?php

namespace App\Http\Controllers;

use App\Jobs\GenereateSchedules;
use App\Models\Module;
use App\Models\ScheduleSlot;
use App\Services\ModuleSchedulingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    function fetchSchedule(Request $request)
    {

        $perpage = (int)$request->input("per_page", 10);
        $page = (int)$request->input("page", 1);
        if ($perpage < 1) {
            $perpage = 1;
        }
        if ($page < 1) {
            $page = 1;
        }


        $query = $request->user()
            ->ScheduleSlots()
            ->orderBy("start_at", "asc");

        $totalSlots = (clone $query)->count();

        $slots = $query->skip(($page - 1) * $perpage)->take($perpage)->get();

        $lastPage = ceil($totalSlots / $perpage);
        $slots->load("Module");
        $slots->load("Module.Subject");

        $slots = $slots->map(function ($slot) {

            $durationInMinutues = explode(":", $slot->module->duration);
            $durationInMinutues = $durationInMinutues[0] * 60  + $durationInMinutues[1];

            $slot->module->durationInMinitues = $durationInMinutues;
            $slot->module->subjectName = $slot->module->Subject->name;
            $slot->module->subjectId = $slot->module->Subject->id;

            $slt = (object)[];
            $slt->id = $slot->id;
            $slt->user_id = $slot->user_id;
            $slt->module_id = $slot->module_id;

            $slt->date = $slot->start_at->format("Y-m-d");
            $slt->start_at_time = $slot->start_at->format("H:i A");
            $slt->end_at_time = $slot->end_at->format("H:i A");

            $slt->is_finished = $slot->is_finished;

            $slt->module = $slot->module;



            return $slt;
        });

        return response()->json([
            "status" => "true",
            "slots" => $slots,
            "pagination" => [
                "has_previous" => $page >= 2,
                "previous_page" => $page >= 2 ? $page - 1 : null,
                "current_page" => $page,
                "next_page" => $page < $lastPage ? $page + 1 : null,
                "has_next" => $page < $lastPage,
                "total_pages" => $lastPage
            ]
        ]);
    }
    function fetchScheduleInModule(int $module, Request $request)
    {

        $perpage = (int)$request->input("per_page", 10);
        $page = (int)$request->input("page", 1);
        if ($perpage < 1) {
            $perpage = 1;
        }
        if ($page < 1) {
            $page = 1;
        }


        $query = $request->user()
            ->ScheduleSlots()
            ->Where("module_id", $module)
            ->orderBy("start_at", "asc");

        $totalSlots = (clone $query)->count();


        $slots = $query->skip(($page - 1) * $perpage)->take($perpage)->get();

        $lastPage = ceil($totalSlots / $perpage);
        $slots->load("Module");
        $slots->load("Module.Subject");


        $slots = $slots->map(function ($slot) {
            $durationInMinutues = explode(":", $slot->module->duration);
            $durationInMinutues = $durationInMinutues[0] * 60  + $durationInMinutues[1];

            $slot->module->durationInMinitues = $durationInMinutues;
            $slot->module->subjectName = $slot->module->Subject->name;
            $slot->module->subjectId = $slot->module->Subject->id;

            $slt = (object)[];
            $slt->id = $slot->id;
            $slt->user_id = $slot->user_id;
            $slt->module_id = $slot->module_id;

            $slt->date = $slot->start_at->format("Y-m-d");
            $slt->start_at_time = $slot->start_at->format("H:i A");
            $slt->end_at_time = $slot->end_at->format("H:i A");


            $slt->is_finished = $slot->is_finished;
            // $slt->created_at =   $slot->created_at;
            // $slt->updated_at =      $slot->updated_at;

            $slt->module = $slot->module;



            return $slt;
        });

        return response()->json([
            "status" => "true",
            "slots" => $slots,
            "pagination" => [
                "has_previous" => $page >= 2,
                "previous_page" => $page >= 2 ? $page - 1 : null,
                "current_page" => $page,
                "next_page" => $page < $lastPage ? $page + 1 : null,
                "has_next" => $page < $lastPage,
                "total_pages" => $lastPage
            ]
        ]);
    }
    function todaySchedule(Request $request)
    {

        $perpage = (int)$request->input("per_page", 10);
        $page = (int)$request->input("page", 1);
        if ($perpage < 1) {
            $perpage = 1;
        }
        if ($page < 1) {
            $page = 1;
        }

        $query = $request->user()
            ->ScheduleSlots()
            ->whereDate("start_at", Carbon::today());

        if($request->input("filter") == 1){
            $query->where("is_finished", 0);
        }else if($request->input("filter") == 2){
            $query->where("is_finished", 1);
        }

        $query->orderBy("start_at", "asc");

        $totalSlots = (clone $query)->count();

        $slots = $query->skip(($page - 1) * $perpage)->take($perpage)->get();

        $lastPage = ceil($totalSlots / $perpage);
        $slots->load("Module");
        $slots->load("Module.Subject");

        $slots = $slots->map(function ($slot) {
            $durationInMinutues = explode(":", $slot->module->duration);
            $durationInMinutues = $durationInMinutues[0] * 60  + $durationInMinutues[1];

            $slot->module->durationInMinitues = $durationInMinutues;
            $slot->module->subjectName = $slot->module->Subject->name;
            $slot->module->subjectId = $slot->module->Subject->id;

            $slt = (object)[];
            $slt->id = $slot->id;
            $slt->user_id = $slot->user_id;
            $slt->module_id = $slot->module_id;

            $slt->date = $slot->start_at->format("Y-m-d");
            $slt->start_at_time = $slot->start_at->format("H:i A");
            $slt->end_at_time = $slot->end_at->format("H:i A");

            $slt->is_finished = $slot->is_finished;

            $slt->module = $slot->module;



            return $slt;
        });

        return response()->json([
            "status" => "true",
            "slots" => $slots,
            "pagination" => [
                "has_previous" => $page >= 2,
                "previous_page" => $page >= 2 ? $page - 1 : null,
                "current_page" => $page,
                "next_page" => $page < $lastPage ? $page + 1 : null,
                "has_next" => $page < $lastPage,
                "total_pages" => $lastPage
            ]
        ]);
    }

    function generateSchedule(Request $request)
    {
        $request->user()
            ->ScheduleSlots()
            ->where("is_finished", false)
            ->get()
            ->each(function ($slot) {
                $module = $slot->Module;
                $module->is_fully_scheduled = false;
                $module->save();

                $slot->delete();
            });

        // generate the schedule as a background job
        GenereateSchedules::dispatch($request->user());
        return response()->json([
            "status" => "true",
            "message" => "Your schedule generation has been initiated, it will take couple of minutes to finish."
        ], 201);

        //generate the schedule immediately
        // (new GenereateSchedules($request->user()))->handle();

        return response()->json([
            "status" => "true",
            "message" => "Your schedule has been generated."
        ], 201);
    }



    function getScheduleSlot(int $schedule,  Request $request)
    {

        $slot = Auth::user()->ScheduleSlots()->where("id", $schedule)->first();

        $durationInMinutues = explode(":", $slot->module->duration);
        $durationInMinutues = $durationInMinutues[0] * 60  + $durationInMinutues[1];

        $slot->module->durationInMinitues = $durationInMinutues;
        $slot->module->subjectName = $slot->module->Subject->name;
        $slot->module->subjectId = $slot->module->Subject->id;

        $slt = (object)[];
        $slt->id = $slot->id;
        $slt->user_id = $slot->user_id;
        $slt->module_id = $slot->module_id;

        $slt->date = $slot->start_at->format("Y-m-d");
        $slt->start_at_time = $slot->start_at->format("H:i A");
        $slt->end_at_time = $slot->end_at->format("H:i A");

        $slt->is_finished = $slot->is_finished;

        $slt->module = $slot->module;


        return response()->json([
            "status" => true,
            "slot" => $slt
        ]);
    }
    function toggleFinished(int $schedule,  Request $request)
    {

        $slot = Auth::user()->ScheduleSlots()->where("id", $schedule)->first();

        $slot->is_finished = $request->input("is_finished", 0) == "1";
        $slot->save();


        return response()->json([
            "status" => true
        ]);
    }
}
