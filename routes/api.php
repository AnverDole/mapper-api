<?php

use App\Notifications\PasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('guest')->post('/register', 'RegisterController@register')->name("register");

Route::middleware('guest')->post('/login', 'LoginController@login')->name("login");

Route::middleware('guest')->post('/forgot-password/step-1', 'ForgotPasswordController@step1')->name("forgot.password.step-1");
Route::middleware('guest')->post('/forgot-password/step-2', 'ForgotPasswordController@step2')->name("forgot.password.step-2");
Route::middleware('guest')->post('/forgot-password/step-3', 'ForgotPasswordController@step3')->name("forgot.password.step-3");

