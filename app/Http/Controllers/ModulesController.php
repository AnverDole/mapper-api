<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Subject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ModulesController extends Controller
{
    function fetchModules(int $subject, Request $request)
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
            ->Subjects()
            ->findorfail($subject)
            ->Modules()
            ->orderBy("created_at", "desc");

        $totalModules = (clone $query)->count();

        $modules = $query->skip(($page - 1) * $perpage)->take($perpage)->get();

        $lastPage = ceil($totalModules / $perpage);
        $modules->load("Subject");
        $modules->map(function ($module) {
            $module->subjectName = $module->Subject->name;
            $module->subjectId = $module->Subject->id;
            // $module->priority = (object)[
            //     "text" => $module->priorityText,
            //     "index" => $module->priority
            // ];
            return $module;
        });

        return response()->json([
            "status" => "true",
            "modules" => $modules,
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


    /**
     * Create new subject.
     * @param Illuminate\Http\Request $request
     */
    public function new(int $subject, Request $request)
    {
        $data = (object)$request->validate([
            "title" => "required|string|min:1",
            "duration" => ["required", "string", "regex:/^(0[0-9]|[0-9]{2,}):(0[0-9]|[0-9]{2,})$/i"], //format: HH:mm -> 32:23
            "priority" => ["required", "string",  Rule::in(array_keys(Module::$PRIORITIES))]
        ]);

        Log::emergency("Sdsd", (array)$data);

        try {
            DB::beginTransaction();

            $user = $request->user();

            $isInserted = $user->Subjects()->findorfail($subject)
                ->Modules()->create([
                    "title" =>  $data->title,
                    "duration" =>  $data->duration,
                    "priority" =>  $data->priority
                ]);
            Log::info($isInserted);

            DB::commit();

            return response()->json([
                "status" => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return response()->json([
                "status" => false
            ]);
        }
    }


    /**
     * Update module
     * @param Illuminate\Http\Request $request
     */
    public function update(int $subject, int $module, Request $request)
    {
        Log::error($request->all());
        $data = (object)$request->validate([
            "title" => "required|string|min:1",
            "duration" => ["required", "string", "regex:/^(0[0-9]|[0-9]{2,}):(0[0-9]|[0-9]{2,})$/i"], //format: HH:mm -> 32:23
            "priority" => ["required", "string",  Rule::in(array_keys(Module::$PRIORITIES))]
        ]);


        try {
            DB::beginTransaction();

            $user = $request->user();


            $subject = $user->Subjects()->where("id", $subject)->first();

            if (!$subject) {
                return response()->json([
                    "status" => false
                ]);
            }
            $module = $subject->Modules()->where("id", $module)->first();

            if (!$module) {
                return response()->json([
                    "status" => false
                ]);
            }


            $module->title =  $data->title;
            $module->duration =  $data->duration;
            $module->priority =  $data->priority;
            $module->save();


            DB::commit();

            return response()->json([
                "status" => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::alert($e->getMessage());

            return response()->json([
                "status" => false
            ]);
        }
    }


    /**
     * delete module.
     * @param Illuminate\Http\Request $request
     */
    public function delete(int $subject, int $module, Request $request)
    {

        try {
            DB::beginTransaction();

            $user = $request->user();

            $subject = $user->Subjects()->where("id", $subject)->first() ?? null;

            $module = $subject->Modules()->where("id", $module)->first() ?? null;

            if (!($subject && $module)) {
                return response()->json([
                    "status" => false
                ]);
            }


            $module->delete();

            DB::commit();

            return response()->json([
                "status" => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::emergency($e->getMessage());
            return response()->json([
                "status" => false
            ]);
        }
    }
}
