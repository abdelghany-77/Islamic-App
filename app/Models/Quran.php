<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quran extends Model
{
    protected $table = 'quran';

    protected $fillable = [
        'surah_number',
        'surah_name_ar',
        'surah_name_en',
        'ayah_number',
        'ayah_text',
        'translation',
    ];

    public function scopeSurah($query, $surahNumber)
    {
        return $query->where('surah_number', $surahNumber);
    }

    public function scopeAyah($query, $surahNumber, $ayahNumber)
    {
        return $query->where('surah_number', $surahNumber)
            ->where('ayah_number', $ayahNumber);
    }
}
