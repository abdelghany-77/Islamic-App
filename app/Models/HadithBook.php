<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hadith;

class HadithBook extends Model
{
    protected $fillable = ['name', 'author', 'description'];

    public function hadiths()
    {
        return $this->hasMany(Hadith::class, 'book_id');
    }
}
