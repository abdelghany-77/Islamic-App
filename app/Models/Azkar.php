<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Azkar extends Model
{
    protected $table = 'azkar';

    protected $fillable = [
        'category',
        'arabic_text',
        'transliteration',
        'english_translation',
        'repeat_count',
        'reference',
        'benefits',
    ];

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
