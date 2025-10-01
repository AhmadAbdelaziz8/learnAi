<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deck;
use Spatie\PdfToText\Pdf; 
use App\Services\GeminiService;

class DeckController extends Controller
{
    // Store a newly created deck in storage.
    public function store(Request $request)
    {
        // Validate and store the deck
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);
        // get the PDF path from the request
        $pdfPath = $request->file('file')->getRealPath();
        $textContent = Pdf::getText($pdfPath);
        $topic = $validated['topic'];
        // Extract text from the PDF
        $flashcards = $geminiService->generateFlashcardsFromText($textContent);

        deck::create([
            'user_id' => $request->user()->id,
            'topic' => $topic,
        ]);
        $deck = Deck::latest()->first();
        return response()->json(['deck' => $deck], 201);
    }
    
}
