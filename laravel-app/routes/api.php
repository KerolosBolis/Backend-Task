<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CheckoutController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/plans', [PlanController::class, 'index']);
Route::post('/checkout/stripe', [CheckoutController::class, 'stripe']);
Route::post('/checkout/paypal', [CheckoutController::class, 'paypal']);
Route::middleware('auth:sanctum')->get('/my-transactions', [CheckoutController::class, 'myTransactions']);
