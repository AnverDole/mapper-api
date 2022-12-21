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
Route::middleware('auth')->post('/logout', 'LoginController@logout')->name("logout");
Route::middleware('auth')->get('/fetch-user', 'LoginController@fetchUser')->name("login.fetch.user");

Route::middleware('guest')->post('/forgot-password/step-1', 'ForgotPasswordController@step1')->name("forgot.password.step-1");
Route::middleware('guest')->post('/forgot-password/step-2', 'ForgotPasswordController@step2')->name("forgot.password.step-2");
Route::middleware('guest')->post('/forgot-password/step-3', 'ForgotPasswordController@step3')->name("forgot.password.step-3");

Route::middleware('auth')->get('/account/settings', 'SettingsController@get')->name("account.settings");
Route::middleware('auth')->post('/account/settings', 'SettingsController@update');
Route::middleware('auth')->post('/account/new-password', 'ChangePasswordController@update')->name("account.password.new");

Route::middleware('auth')->get('/account/management', 'AccountManagement@get')->name("account.management");
Route::middleware('auth')->post('/account/management', 'AccountManagement@update');


Route::middleware('auth')->get('/subjects', 'SubjectsController@fetchSubjects')->name("subjects");
Route::middleware('auth')->post('/subjects/new', 'SubjectsController@new')->name("subjects.new");
Route::middleware('auth')->post('/subjects/{subject}/update', 'SubjectsController@update')->name("subjects.update");
Route::middleware('auth')->post('/subjects/{subject}/delete', 'SubjectsController@delete')->name("subjects.delete");

Route::middleware('auth')->get('/subjects/{subject}/modules', 'ModulesController@fetchModules')->name("subjects.modules");
Route::middleware('auth')->post('/subjects/{subject}/modules/new', 'ModulesController@new')->name("subjects.modules.new");
Route::middleware('auth')->post('/subjects/{subject}/modules/{module}/update', 'ModulesController@update')->name("subjects.modules.update");
Route::middleware('auth')->post('/subjects/{subject}/modules/{module}/delete', 'ModulesController@delete')->name("subjects.modules.delete");

Route::middleware('auth')->get('/modules/{module}/schedule', 'ScheduleController@fetchScheduleInModule');
Route::middleware('auth')->get('/schedule', 'ScheduleController@fetchSchedule');
Route::middleware('auth')->post('/schedule/generate-new', 'ScheduleController@generateSchedule');
Route::middleware('auth')->post('/schedule/{schedule}', 'ScheduleController@getScheduleSlot')->name("schedule.slot");
Route::middleware('auth')->post('/schedule/{schedule}/toggle-finish', 'ScheduleController@toggleFinished');

Route::middleware('auth')->get('/schedule/today-activities', 'ScheduleController@todaySchedule');
