<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\QuranVerse;

class QuranSurah extends Model
{
    protected $fillable = [
        'number',
        'name_arabic',
        'name_english',
        'name_transliteration',
        'total_verses',
        'revelation_type'
    ];
    public function verses()
    {
        return $this->hasMany(QuranVerse::class, 'surah_id');
    }
}
