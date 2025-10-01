<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;
    // Define relationships

    // each deck belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // each deck has many flashcards
    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }
}
