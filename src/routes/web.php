<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\CorrectionAttendanceController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [LoginController::class, 'select']);

Route::get('/login', [LoginController::class, 'getLogin'])->name('login');
Route::post('/login', [LoginController::class, 'postLogin']);

Route::get('/logout', [LoginController::class, 'logout']);

Route::get('/register', [LoginController::class, 'register']);
Route::post('/register/store', [LoginController::class, 'store']);

Route::get('/verification/{email}', [LoginController::class, 'verification'])->name('verification');

Route::get('/resend/{email}', [LoginController::class, 'resend'])->name('resend');

Route::group(['prefix' => 'attendance'], function()
{
    Route::get('/', [AttendanceController::class, 'index'])->middleware('auth');

    Route::get('/start', [AttendanceController::class, 'start']);

    Route::get('/end', [AttendanceController::class, 'end']);

    Route::get('/list/{month?}', [AttendanceController::class, 'list'])->name('attendance_list');

    Route::get('/{id}', [AttendanceController::class, 'detail'])->name('attendance_detail');

    Route::post('/request/{id}', [AttendanceController::class, 'request'])->name('attendance_request');
});

Route::get('/rest/start', [RestController::class, 'start']);

Route::get('/rest/end', [RestController::class, 'end']);

Route::group(['prefix' => 'stamp_correction_request'], function()
{
    Route::post('/{id}', [CorrectionAttendanceController::class, 'correction'])->name('correction');

    Route::get('/list', [CorrectionAttendanceController::class, 'list'])->name('request_list');
});

Route::group(['prefix' => 'admin'], function()
{
    Route::get('/login', [AdminController::class, 'getLogin']);

    Route::post('/login', [AdminController::class, 'postLogin']);

    Route::get('/attendance/list/{day?}', [AdminController::class, 'list'])->name('admin_attendance_list');

    Route::get('/staff/list', [AdminController::class, 'staff_list']);

    Route::get('/attendance/staff/{id}/{month?}', [AdminController::class, 'staff_attendance_list'])->name('staff_attendance_list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AdminController::class, 'approval_detail'])->name('approval_detail');

    Route::post('/approve/{id?}', [AdminController::class, 'approve'])->name('approve');

    Route::post('/correction{id}', [AdminController::class, 'correction'])->name('admin_correction');

});

