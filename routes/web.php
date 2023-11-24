<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleAuthController;

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
    return view('welcome_custom');
});

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google::callback');

Route::prefix('/{brand}/{campaign}')->group(function() {
    Route::get('/', [Controller::class, 'index'])->name('lba-1::index');
    Route::get('/product', [Controller::class, 'product'])->name('lba-1::product');

    Route::prefix('auth')->group(function() {
        Route::get('/', [Controller::class, 'login'])->name('login');
        Route::get('/redirect', [GoogleAuthController::class, 'redirect'])->name('google::redirect');
        Route::get('/phone-number', [Controller::class, 'phoneNumber'])->name('lba-1::login::phone-number');
        Route::get('/otp', [Controller::class, 'otp'])->name('lba-1::login::otp');
    });

    Route::get('/voucher-redeem', [Controller::class, 'voucherRedeem'])->name('voucher-redeem');
});
