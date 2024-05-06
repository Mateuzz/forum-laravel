<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{category}', [CategoryController::class, 'show']);
Route::post('/category', [CategoryController::class, 'store']);

Route::get('/post/{post:slug}', [PostController::class, 'show']);
Route::get('/post', [PostController::class, 'index']);
Route::post('/post', [PostController::class, 'store'])->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::get('/login', [LoginController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/login', [LoginController::class, 'destroy'])->middleware('auth:sanctum');

Route::post('/user', [RegisterController::class, 'store'])->middleware('guest');

Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{user:id}', [UserController::class, 'show']);
