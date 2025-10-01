<?php

use App\Http\Controllers\Api\DeckController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// No auth middleware - public routes
Route::apiResource('users', UserController::class)->only(['index', 'store', 'show']);
Route::apiResource('decks', DeckController::class)->only(['index', 'store', 'show']);