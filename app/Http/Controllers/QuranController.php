<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuranSurah;
use App\Models\QuranVerse;

class QuranController extends Controller
{
    public function index()
    {
        $surahs = QuranSurah::all();
        return view('quran.index', compact('surahs'));
    }

    public function show($surahId, $page = 1)
    {
        $surah = QuranSurah::findOrFail($surahId);
        $verses = QuranVerse::where('surah_id', $surahId)
            ->offset(($page - 1) * 15)
            ->limit(15)
            ->get();
        $totalVerses = QuranVerse::where('surah_id', $surahId)->count();
        $hasNextPage = ($page * 15) < $totalVerses;
        $currentPage = $page;
        return view('quran.show', compact('surah', 'verses', 'currentPage', 'hasNextPage'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        // Case 1: Surah:Ayah format (e.g., "1:1")
        if (preg_match('/^(\d+):(\d+)$/', $query, $matches)) {
            $surahNumber = $matches[1];
            $ayahNumber = $matches[2];
            $surah = QuranSurah::where('number', $surahNumber)->firstOrFail();
            $verse = QuranVerse::where('surah_id', $surah->id)
                ->where('verse_number', $ayahNumber)
                ->firstOrFail();
            // Calculate the page containing this Ayah
            $versePosition = QuranVerse::where('surah_id', $surah->id)
                ->where('verse_number', '<=', $ayahNumber)
                ->count();
            $page = ceil($versePosition / 15);
            return redirect()->route('quran.show', [$surah->id, $page, 'ayah' => $verse->id]);
        }
        // Case 2: Surah number (e.g., "1")
        elseif (is_numeric($query)) {
            $surah = QuranSurah::where('number', $query)->firstOrFail();
            return redirect()->route('quran.show', [$surah->id, 1]);
        }
        // Case 3: Surah name (e.g., "Al-Fatiha" or "الفاتحة")
        else {
            $surah = QuranSurah::where('name_english', 'like', "%{$query}%")
                ->orWhere('name_arabic', 'like', "%{$query}%")
                ->first();
            if ($surah) {
                return redirect()->route('quran.show', [$surah->id, 1]);
            }
            // Case 4: Search within verses (Arabic or Translation)
            $verses = QuranVerse::where('arabic_text', 'like', "%{$query}%")
                ->orWhere('translation', 'like', "%{$query}%")
                ->paginate(15);
            return view('quran.search', compact('verses', 'query'));
        }
    }
}
