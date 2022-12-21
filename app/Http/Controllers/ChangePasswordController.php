<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Notifications\PasswordResetOtp as NotificationsPasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    /**
     * Change password
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            "current_password" => "required|string",
            "new_password" => "required|string|min:8|confirmed"
        ]);

        $validator->after(function ($v) use ($user) {
            $data = (object)$v->getData();
            if ($v->errors()->count() > 0) {
                return;
            }

            if (!Hash::check($data->current_password, $user->password)) {
                $v->errors()->add("current_password", "Given password is wrong! please try again.");
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $data  = (object)$validator->validated();
        $user->password = Hash::make($data->new_password);
        $user->save();

        return response()->json([
            "success" => true
        ]);
    }
}
