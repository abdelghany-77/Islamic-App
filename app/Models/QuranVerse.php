<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranVerse extends Model
{
    protected $fillable = [
        'surah_id',
        'verse_number',
        'arabic_text',
        'translation',
        'transliteration',
        'tafsir'
    ];

    public function surah()
    {
        return $this->belongsTo(QuranSurah::class);
    }
}
