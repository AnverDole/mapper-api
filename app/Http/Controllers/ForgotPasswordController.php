<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Notifications\PasswordResetOtp as NotificationsPasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Forgot password controller
 *
 * This is the controller that handles the account recovery process. Which is a three
 * step process.
 *
 * 1) Step 1 - The user provides the email address associated with the account.
 *             Then the application will check whether it is a valid email and it
 *             will send an OTP which allows the user to proceed to the next step.
 *             Otherwise, the user will have to request again with the correct email address.
 *
 * 2) Step 2 - The user should send the OTP with the email address. which then validate.
 *             Then the user can proceed to the next step.
 *             if the OTP is not valid then the user will have to request again with the correct email address.
 *
 * 3) Step 3 - The user should choose a secure password and the process is finished.
 *
 *
 */
class ForgotPasswordController extends Controller
{
    /**
     * Forgot password step 1 (Validate email & send OTP)
     * @param \Illuminate\Http\Request $request
     */
    public function step1(Request $request)
    {
        $data = (object)$request->validate([
            "email" => "required|email"
        ]);

        //even if the given user is wrong, send a success message. this will block the user on step 2.
        //it provides extra security by preventing showing the existing status of other persons' emails.
        $user = User::where("email", $data->email)->first();
        if (!$user) {
            return response()->json([
                "success" => true
            ]);
        }

        $user->passwordResetOTPs()->delete();

        $otp = rand(10000, 99999);

        $user->passwordResetOTPs()->create([
            "user_id" => $user->id,
            "ip" => $request->ip(),
            "otp" => Hash::make($otp)
        ]);

        $user->notify(new NotificationsPasswordResetOtp($user->first_name, $otp, $user->email));

        return response()->json([
            "success" => true
        ]);
    }

    /**
     * Forgot password step 2 (Validate OTP)
     * @param \Illuminate\Http\Request $request
     */
    public function step2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:users,email",
            "otp" => "required|string|size:5"
        ]);


        $validator->after(function ($v) {
            $data = (object)$v->getData();
            if($v->errors()->count() > 0) {
                return;
            }

            // check whether the given OTP is valid
            if (!$this->isValidOTP($data->email, $data->otp)) {
                $v->errors()->add("otp", "Invalid one time password, Please try again.");
            }
        });

        if ($validator->fails()) {
            return response()->json([
                "message" => "The given data was invalid.",
                "errors" => [
                    "otp" => "Invalid one time password, Please try again."
                ]
            ], 400);
        }

        return response()->json([
            "success" => true
        ]);
    }

    /**
     * Forgot password step 3 (Validate OTP & update the password)
     * @param \Illuminate\Http\Request $request
     */
    public function step3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:users,email",
            "otp" => "required|string|size:5",
            "password" => "required|string|min:8",
        ]);


        $validator->after(function ($v) {
            $data = (object)$v->getData();
            if ($v->errors()->count() > 0) {
                return;
            }
            // check whether the given OTP is valid
            if (!$this->isValidOTP($data->email, $data->otp)) {
                $v->errors()->add("otp", "Invalid OTP, Please try again.");
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

        //remove old OTPs
        $user->passwordResetOTPs()->delete();

        //assign new password
        $user->password = Hash::make($data->password);

        $user->save();


        return response()->json([
            "success" => true
        ]);
    }


    /**
     * Check whether the given OTP is valid.
     * @param string $email
     * @param string $otp
     * @return bool
     */
    private function isValidOTP($email, $otp)
    {
        $user = User::where("email", $email)->first();
        if (!$user) return false;

        $pOTPs = $user->passwordResetOTPs;


        foreach ($pOTPs as $pOTP) {
            if (Hash::check($otp, $pOTP->otp)) {
                return true;
            }
        }

        return false;
    }
}
