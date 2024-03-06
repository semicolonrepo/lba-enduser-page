<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GoogleAuthRatingController;
use App\Http\Controllers\OneTimePasswordController;
use App\Http\Controllers\OneTimePasswordRatingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\TermConditionController;

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
    return view('welcome_custom', ['message' => 'Campaign not found']);
});

Route::get('/preview/{token}', [CampaignController::class, 'preview'])->name('preview');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google::callback');
Route::get('/term-condition', [TermConditionController::class, 'index'])->name('term-condition');

Route::middleware('validate.campaign')->prefix('/{brand}/{campaign}')->group(function() {
    Route::get('/', [CampaignController::class, 'index'])->name('index')->middleware('activity.log');
    Route::get('/cover', [CampaignController::class, 'cover'])->name('cover')->middleware('activity.log');

    Route::get('/product/{productId}', [ProductController::class, 'show'])->name('product::show')->middleware('activity.log');

    Route::get('/product/{productId}/voucher/view/{voucherCode}', [VoucherController::class, 'show'])->name('voucher::show')->middleware('activity.log');
    Route::post('/product/{productId}/voucher/claim', [VoucherController::class, 'claim'])->name('voucher::claim')->middleware('voucherAuth');
    Route::get('/product/{productId}/voucher/claim', [VoucherController::class, 'claim'])->name('voucher::claim')->middleware('voucherAuth');

    Route::get('/rating/{voucherCode}', [RatingController::class, 'show'])->name('rating::show')->middleware(
        'activity.log',
        'rating.auth',
        'verify.campaign.has.rating',
        'verify.voucher.claimed',
        'verify.voucher.permission',
        'verify.voucher.has.rating',
    );
    Route::post('/rating/{voucherCode}', [RatingController::class, 'store'])->name('rating::store')->middleware(
        'activity.log',
        'rating.auth',
        'verify.campaign.has.rating',
        'verify.voucher.claimed',
        'verify.voucher.permission',
        'verify.voucher.has.rating',
    );

    Route::get('/product/{productId}/otp/login', [OneTimePasswordController::class, 'login'])->name('otp::login');
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/product/{productId}/otp/send', [OneTimePasswordController::class, 'send'])->name('otp::send');
        Route::get('/product/{productId}/otp/resend/{phoneNumber}', [OneTimePasswordController::class, 'resend'])->name('otp::resend');
    });
    Route::get('/product/{productId}/otp/send/{phoneNumber}', [OneTimePasswordController::class, 'validateGet'])->name('otp::validate::get');
    Route::post('/product/{productId}/otp/send/{phoneNumber}', [OneTimePasswordController::class, 'validatePost'])->name('otp::validate::post');

    Route::get('/rating/{voucherCode}/otp/login', [OneTimePasswordRatingController::class, 'login'])->name('otp::login::rating');
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/rating/{voucherCode}/otp/send', [OneTimePasswordRatingController::class, 'send'])->name('otp::send::rating');
        Route::get('/rating/{voucherCode}/otp/resend/{phoneNumber}', [OneTimePasswordRatingController::class, 'resend'])->name('otp::resend::rating');
    });
    Route::get('/rating/{voucherCode}/otp/send/{phoneNumber}', [OneTimePasswordRatingController::class, 'validateGet'])->name('otp::validate::get::rating');
    Route::post('/rating/{voucherCode}/otp/send/{phoneNumber}', [OneTimePasswordRatingController::class, 'validatePost'])->name('otp::validate::post::rating');

    Route::get('/rating/{voucherCode}/google/login', [GoogleAuthRatingController::class, 'login'])->name('google::login::rating');
    Route::get('/rating/{voucherCode}/google/redirect', [GoogleAuthRatingController::class, 'redirect'])->name('google::redirect::rating');

    Route::get('/product/{productId}/google/login', [GoogleAuthController::class, 'login'])->name('google::login');
    Route::post('/product/{productId}/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google::redirect');
});

