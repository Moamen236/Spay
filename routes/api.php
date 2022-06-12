<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\RechargeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ReciptsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceCodeController;

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

Route::post('/test', [TestController::class, 'store'])->name('test.store');

//CLIENTS
Route::get('/clients', [ClientsController::class, 'index']);
Route::get('/clients/{id}', [ClientsController::class, 'show']);
Route::post('/clients', [ClientsController::class, 'store']);
Route::post('/clients/{id}', [ClientsController::class, 'update']);
Route::get('/destroy_clients', [ClientsController::class, 'destroy']);
Route::get('/clients/{id}/destroy', [ClientsController::class, 'destroyClient']);
// Route::get('/clients/payments', [ClientsController::class, 'payments']);
// Route::get('/clients/{id}/wallet', [ClientsController::class, 'wallet']);


//PAYMENT
Route::get('/payments', [PaymentsController::class, 'index']);
Route::get('/payments/{id}', [PaymentsController::class, 'show']);
Route::post('/payments', [PaymentsController::class, 'store']);


//OTP
Route::get('/otps', [OtpController::class, 'index']);
Route::get('/otps/{id}', [OtpController::class, 'show']);
Route::post('/otps', [OtpController::class, 'store']);
Route::get('/otps/{id}/destroy', [OtpController::class, 'deleteOtpById']);
Route::post('/checkOtp', [OtpController::class, 'check']);
Route::post('/resend', [OtpController::class, 'resend']);
Route::post('/check_reset_password_otp', [OtpController::class, 'checkOtpFromResetpassword']);


//RECIPTS
Route::get('/recipts', [ReciptsController::class, 'index']);
Route::get('/recipts/{id}', [ReciptsController::class, 'show']);
Route::post('/recipts', [ReciptsController::class, 'store']);


//COMPANY
Route::get('/companies', [CompaniesController::class, 'index']);
Route::get('/companies/{id}', [CompaniesController::class, 'show']);
// Route::get('/companies/{id}/payments', [CompaniesController::class, 'payments']);


//WALLET
Route::get('/wallets', [WalletController::class, 'index']);
Route::get('/wallets/{id}', [WalletController::class, 'show']);
Route::post('/wallets', [WalletController::class, 'store']);
Route::post('/WalletPay', [WalletController::class, 'payWithWallet']);


//RECHARGE INFO
Route::get('/recharge', [RechargeController::class, 'index']);
Route::get('/recharge/{id}', [RechargeController::class, 'show']);
Route::post('/recharge', [RechargeController::class, 'store']);


//AuthintcTION
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/check_user', [AuthController::class, 'checkUserAndSendOtp']);
Route::post('/reset_password', [AuthController::class, 'updatePassword']);

// Codes
Route::get('/codes', [ServiceCodeController::class, 'index']);
Route::get('/codes/{id}', [ServiceCodeController::class, 'show']);
Route::post('/codes', [ServiceCodeController::class, 'store']);
