<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Create new access token.
     * @param Illuminate\Http\Request $request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:users,email",
            "password" => "required|string",
            "device_name" => "required|string|max:100",
        ]);

        $validator->after(function ($v) {
            $data = (object)$v->getData();
            if ($v->errors()->count() > 0) return;

            $user = User::where("email", $data->email)->first();
            if (!$user) return;

            if (!Hash::check($data->password, $user->password)) {
                $v->errors()->add("email", "Your email and password do not match.");
            }
        });

        if ($validator->fails()) {
            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => $validator->errors()
            ], 400);
        }

        $data = (object)$validator->validated();

        $user = User::where("email", $data->email)->first();

        $user->tokens()->where('name', $data->device_name)->delete();

        $token = $user->createToken($data->device_name, ['user:all']);

        return response()->json([
            "status" => true,
            "token" => $token->plainTextToken,
            "user" => $user
        ]);
    }
    /**
     * Unauthenticate the user
     * @param Illuminate\Http\Request $request
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        $isLoggedOut = $user->tokens()
            ->where('id', $user->currentAccessToken()->id)
            ->delete();

        return response()->json([
            "status" => (bool)$isLoggedOut
        ]);
    }
    /**
     * Fetch authenticated user details
     * @param Illuminate\Http\Request $request
     */
    public function fetchUser(Request $request)
    {

        return response()->json([
            "status" => true,
            "user" => $request->user()
        ]);
    }
}
