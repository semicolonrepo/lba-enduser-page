<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProductController;

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

Route::get('/preview/{token}', [CampaignController::class, 'preview'])->name('preview');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google::callback');

Route::prefix('/{brand}/{campaign}')->group(function() {
    Route::get('/', [CampaignController::class, 'index'])->name('index');

    Route::get('/product/{productId}', [ProductController::class, 'show'])->name('product::show');
    Route::get('/product/{productId}/get-voucher', [ProductController::class, 'getVoucher'])->name('product::getVoucher');

    Route::get('/product/{productId}/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google::redirect');
});
