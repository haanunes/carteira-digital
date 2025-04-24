<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth.api')->group(function () {
    Route::get('/user',           [AuthController::class, 'user']);
    Route::post('/logout',        [AuthController::class, 'logout']);
    Route::post('/deposit',       [TransactionController::class, 'deposit']);
    Route::post('/transfer',      [TransactionController::class, 'transfer']);
    Route::post('/reverse/{transaction}', [TransactionController::class, 'reverse']);
    Route::get('/transactions',   [TransactionController::class, 'index']);
    Route::get('/users/{user}',   [UserController::class, 'show']);
});
