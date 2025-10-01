<?php

use App\Http\Controllers\Api\DeckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// handle upload and text extraction
Route::post('/decks', [DeckController::class, 'store']);

// get the specific deck with it's flashcards
Route::get('/decks/{id}', [DeckController::class, 'show']);