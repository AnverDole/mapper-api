<?php

namespace App\Http\Controllers;

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

        $token = $user->createToken($data["device_name"], ['user:all']);

        return response()->json([
            "status" => true,
            "token" => $token->plainTextToken,
            "user" => $user
        ]);
    }
}
