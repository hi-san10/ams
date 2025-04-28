<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RestController;


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

Route::get('/', function () {
    return view('login');
});

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
});

Route::get('/rest/start', [RestController::class, 'start']);

Route::get('/rest/end', [RestController::class, 'end']);


