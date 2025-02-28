<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HadithBook;
use App\Models\Hadith;

class HadithController extends Controller
{
    public function index()
    {
        $books = HadithBook::all();
        return view('hadith.index', compact('books'));
    }

    public function showBook(HadithBook $book)
    {
        $hadiths = $book->hadiths()->paginate(20);
        return view('hadith.book', compact('book', 'hadiths'));
    }

    public function show(Hadith $hadith)
    {
        return view('hadith.show', compact('hadith'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $hadiths = Hadith::where('arabic_text', 'like', "%{$query}%")
        ->orWhere('translation', 'like', "%{$query}%")
        ->orWhere('narrator', 'like', "%{$query}%")
        ->paginate(15);

        return view('hadith.search', compact('hadiths', 'query'));
    }
}
