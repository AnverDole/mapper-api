<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Create new user account.
     * @param Illuminate\Http\Request $request
     */
    public function register(Request $request)
    {

        $data = $request->validate([
            "email" => "required|email|unique:users,email",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "password" => "required|string|min:8",
            "device_name" => "required|string|max:100",
        ]);

        $data["password"] = Hash::make($data["password"]);

        $user = User::create($data);
        $user->Settings()->create([
            "prioritization" => Settings::HIGH_PRIORITY_CATEGORY, //Settings::$priorityCategories["1"]
            "duration_between_activities" => 30, //30 minitues
            "activity_max_duration" => 60 * 3, //3 hours
            "day_starts_at" => "08:00",
            "day_ends_at" => "20:00",
        ]);


        $token = $user->createToken($data["device_name"], ['user:all']);

        return response()->json([
            "status" => true,
            "token" => $token->plainTextToken,
            "user" => $user
        ]);
    }
}
