<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Illuminate\Support\Facades\Log;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Exception;

class GeminiService
{
    public function generateFlashcardsFromText(string $textContent): array
    {
        // we want array of objects with term and definition
        $responseSchema = new Schema(
            type: DataType::ARRAY,
            items: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'question' => new Schema(type: DataType::STRING),
                    'answer' => new Schema(type: DataType::STRING),
                ],
                required: ['question', 'answer']
            )
        );

        $prompt = "Generate 10 flashcards (question and answer) based on the following text:\n\n---\n\n{$textContent}";

        try {
            $result = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->withGenerationConfig(
                    new GenerationConfig(
                        responseMimeType: ResponseMimeType::APPLICATION_JSON,
                        responseSchema: $responseSchema
                    )
                )
                ->generateContent($prompt);

            // Since we force a schema, we can trust the output and simplify the return.
            return json_decode($result->text(), true) ?? [];

        } catch (Exception $e) {
            Log::error('Gemini API call with schema failed: ' . $e->getMessage());
            return [];
        }
    }
}