<?php

use App\Http\Controllers\CsrfController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;

Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::get('/login', [LoginController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/login', [LoginController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/csrf-token', CsrfController::class);
