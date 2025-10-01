<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'user_id',
    ];
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
