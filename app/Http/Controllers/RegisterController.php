<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {

        $data = $request->validate([
            "email" => "required|email|unique:users,email",
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "password" => "required|string|min:8",
        ]);

        $data["password"] = Hash::make($data["password"]);

        $user = User::create($data);

        return response()->json([
            "status" => true,
            "token" => $user->createToken('auth-token', ['user:all'])->plainTextToken,
            "user" => $user
        ]);
    }
}
