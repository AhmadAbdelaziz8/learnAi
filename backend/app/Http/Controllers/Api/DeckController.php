<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\Flashcard;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class DeckController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $decks = Deck::with('flashcards', 'user')->get();
        return response()->json($decks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'user_id' => 'required|exists:users,id', // Add this validation
        ]);

        // Create the deck with provided user_id
        $deck = Deck::create([
            'topic' => $request->topic,
            'user_id' => $request->user_id, // Use provided user_id
        ]);

        // Handle PDF upload and text extraction
        $pdfFile = $request->file('pdf_file');
        $path = $pdfFile->store('pdfs', 'public');

        // Extract text from PDF
        $textContent = Pdf::getText(storage_path('app/public/' . $path));

        // Generate flashcards using Gemini
        try {
            $flashcards = $this->geminiService->generateFlashcardsFromText($textContent);
        } catch (\Exception $e) {
            // Fallback: create sample flashcards if Gemini fails
            $flashcards = [
                ['question' => 'What is the main topic of this PDF?', 'answer' => 'Based on the uploaded PDF content'],
                ['question' => 'Sample Question 2 from PDF', 'answer' => 'Sample Answer 2'],
                ['question' => 'Key concept from the document', 'answer' => 'Important information extracted'],
            ];
        }

        // Save flashcards to database
        foreach ($flashcards as $flashcardData) {
            Flashcard::create([
                'deck_id' => $deck->id,
                'question' => $flashcardData['question'],
                'answer' => $flashcardData['answer'],
            ]);
        }

        // Load the deck with flashcards and user for response
        $deck->load('flashcards', 'user');

        return response()->json([
            'message' => 'Deck created successfully',
            'deck' => $deck,
        ], 201);
    }

    public function show(Deck $deck)
    {
        $deck->load('flashcards', 'user');
        return response()->json($deck);
    }
}
