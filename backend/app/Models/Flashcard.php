<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;
    // each flashcard belongs to a deck
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }
}
