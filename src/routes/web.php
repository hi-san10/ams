<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('login', [LoginController::class, 'login']);

Route::get('register', [LoginController::class, 'register']);

Route::post('register/store', [LoginController::class, 'store']);

Route::get('verification/{email}', [LoginController::class, 'verification'])->name('verification');

Route::get('resend/{email}', [LoginController::class, 'resend'])->name('resend');
