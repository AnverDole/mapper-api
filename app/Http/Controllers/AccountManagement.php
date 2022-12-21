<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountManagement extends Controller
{
    /**
     * Create new user account.
     * @param Illuminate\Http\Request $request
     */
    public function get(Request $request)
    {

        $user = $request->user();


        return response()->json([
            "status" => true,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
        ]);
    }

    /**
     * Create new user account.
     * @param Illuminate\Http\Request $request
     */
    public function update(Request $request)
    {
        $data = (object)$request->validate([
            "first_name" => "required|string",
            "last_name" => "required|string"
        ]);


        try {
            DB::beginTransaction();

            $user = $request->user();

            $user->first_name = $data->first_name;
            $user->last_name = $data->last_name;
            $user->save();

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
