<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectsController extends Controller
{
    function fetchSubjects(Request $request)
    {

        $perpage = (int)$request->input("per_page", 10);
        $page = (int)$request->input("page", 1);
        if ($perpage < 1) {
            $perpage = 1;
        }
        if ($page < 1) {
            $page = 1;
        }


        $query = $request->user()->Subjects()->orderBy("created_at", "desc");
        $totalSubjects = (clone $query)->count();

        $subjects = $query->skip(($page - 1) * $perpage)->take($perpage)->get();

        $lastPage = ceil($totalSubjects / $perpage);

        $subjects->map(function ($subject) {
            $subject->moduleCount = $subject->Modules()->count();
            return $subject;
        });

        return response()->json([
            "status" => "true",
            "subjects" => $subjects,
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
    public function new(Request $request)
    {
        $data = (object)$request->validate([
            "subject_name" => "required|string|min:1"
        ]);


        try {
            DB::beginTransaction();

            $user = $request->user();

            $user->Subjects()->create([
                "name" => $data->subject_name
            ]);

            DB::commit();

            return response()->json([
                "status" => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false
            ]);
        }
    }

    /**
     * Create update subject.
     * @param Illuminate\Http\Request $request
     */
    public function update(int $subject, Request $request)
    {
        $data = (object)$request->validate([
            "subject_name" => "required|string|min:1"
        ]);


        try {
            DB::beginTransaction();

            $user = $request->user();

            $user->Subjects()->where("id", $subject)->update([
                "name" => $data->subject_name
            ]);

            DB::commit();

            return response()->json([
                "status" => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false
            ]);
        }
    }
    /**
     * Create delete subject.
     * @param Illuminate\Http\Request $request
     */
    public function delete(int $subject, Request $request)
    {

        try {
            DB::beginTransaction();

            $user = $request->user();

            $user->Subjects()->where("id", $subject)->delete();

            DB::commit();

            return response()->json([
                "status" => true
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false
            ]);
        }
    }
}
