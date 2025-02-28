<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    protected $fillable = [
        'book_id',
        'reference_number',
        'arabic_text',
        'translation',
        'narrator',
        'authenticity',
        'explanation'
    ];

    public function book()
    {
        return $this->belongsTo(HadithBook::class);
    }
}
