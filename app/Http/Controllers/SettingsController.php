<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Get settings of the user account
     * @param Illuminate\Http\Request $request
     */
    public function get(Request $request)
    {

        $user = $request->user();
        $settings = $user->Settings;

        return response()->json([
            "status" => true,
            "prioritization" => [
                "categories" => Settings::$priorityCategories,
                "value" => $settings->prioritization ?? 1
            ],
            "duration_between_activities" => $settings->duration_between_activities ?? null,
            "activity_max_duration" => $settings->activity_max_duration ?? null,
            "day_starts_at" => $settings->day_starts_at == null ? null : $settings->day_starts_at->format("H:i"),
            "day_ends_at" => $settings->day_ends_at == null ? null : $settings->day_ends_at->format("H:i"),
        ]);
    }

    /**
     * Update settings of the user account.
     * @param Illuminate\Http\Request $request
     */
    public function update(Request $request)
    {
        $data = (object)$request->validate([
            "prioritization" => ["required", "int", Rule::in(array_keys(Settings::$priorityCategories))],
            "duration_between_activities" => "required|int|min:1", // duration between activities in minitues
            "activity_max_duration" => "required|int|min:0",
            "day_starts_at" => "required|date_format:H:i",
            "day_ends_at" => "required|after:day_starts_at|date_format:H:i",
        ]);


        try {
            DB::beginTransaction();

            $user = $request->user();

            $user->Settings()->delete();
            $user->Settings()->create([
                "prioritization" => $data->prioritization,
                "duration_between_activities" => $data->duration_between_activities,
                "activity_max_duration" => $data->activity_max_duration,
                "day_starts_at" => $data->day_starts_at,
                "day_ends_at" => $data->day_ends_at,
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
}
