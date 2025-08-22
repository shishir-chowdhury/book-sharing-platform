<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\AdminController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    Route::post('books',        [BookController::class, 'store']);
    Route::get('books/nearby',  [BookController::class, 'nearby']);
});

Route::prefix('admin')->middleware(['auth:api','is_admin'])->group(function () {
    Route::get('users',          [AdminController::class, 'users']);
    Route::get('books',          [AdminController::class, 'books']);
    Route::delete('books/{id}',  [AdminController::class, 'deleteBook']);
});
