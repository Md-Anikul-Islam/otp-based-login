<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'sendOtp'])->name('login.sendOtp');
Route::get('otp/verify', [LoginController::class, 'showOtpVerificationForm'])->name('otp.verify');
Route::post('otp/verify', [LoginController::class, 'verifyOtp'])->name('otp.verify.submit');
