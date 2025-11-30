<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CategoriesController;
use Illuminate\Http\Request;

/**
 * ==========1===========
 * unprotected routes for user registration and login
 */
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/**
 * =========2===========
 * protected routes, only accessible with valid token
 */
Route::middleware('auth:sanctum')->group(function () {
    /**
     * =========3===========
     * User logout route
     */
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    

    /**
     * =========4===========
     * Books CRUD routes
     */
    Route::apiResource('books', BooksController::class);

    /**
     * =========5===========
     * Return or borrow books
     */
    Route::put('books/{id}/borrow-return', [BooksController::class, 'borrowReturn']);
});


