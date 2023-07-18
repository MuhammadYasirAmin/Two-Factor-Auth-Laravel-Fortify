<?php

use App\Http\Controllers\AuthController;
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

Route::get('/', static function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('home', 'home')->name('home');
    Route::view('password/update', 'auth.passwords.update')->name('passwords.update');

    Route::post('Phone-Verify', [AuthController::class, 'phoneVerification'])->name('Phone.Verify');
    Route::get('Verify-Phone/{User_ID}', [AuthController::class, 'codeVerifyView'])->name('Code.Verify');
    Route::post('Code-Verify', [AuthController::class, 'codeVerification'])->name('OTP.Verify');

    Route::post('Email-Verify', [AuthController::class, 'emailVerification'])->name('Email.Verify');
    Route::get('Verify-Email/{User_ID}', [AuthController::class, 'codeEmailVerifyView'])->name('Email.Code.Verify');
    Route::post('Email-Code-Verify', [AuthController::class, 'alternateEmailVerification'])->name('Email.OTP.Verify');
});
