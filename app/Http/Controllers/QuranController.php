<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class QuranController extends Controller
{
    protected $apiBaseUrl = 'https://api.alquran.cloud/v1';
    protected $translationEdition = 'en.sahih';
    protected $tafsirEdition = 'en.ibn-kathir';

    public function index()
    {
        // Cache surahs for better performance
        $surahs = Cache::remember('quran.surahs', 86400, function () {
            $response = Http::get("{$this->apiBaseUrl}/surah");
            return $response->json()['data'];
        });

        return view('quran.index', compact('surahs'));
    }

public function surah($surahNumber)
{
    // Get the Arabic text
    $arabicResponse = Cache::remember("quran.surah.{$surahNumber}.arabic", 86400, function () use ($surahNumber) {
        return Http::get("{$this->apiBaseUrl}/surah/{$surahNumber}")->json()['data'];
    });

    // Get the translation
    $translationResponse = Cache::remember("quran.surah.{$surahNumber}.translation", 86400, function () use ($surahNumber) {
        return Http::get("{$this->apiBaseUrl}/surah/{$surahNumber}/{$this->translationEdition}")->json()['data'];
    });

    // Combine Arabic text with translation
    $surahInfo = $arabicResponse;
    $ayahs = [];

    // Skip Bismillah for Surah  Al-Tawbah (9)
    $shouldRemoveBismillah = $surahNumber != 9 && $surahNumber != 1;

    $bismillahVariations = [
        "بِسۡمِ ٱللَّهِ ٱلرَّحۡمَـٰنِ ٱلرَّحِیمِ",
        "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
        "بسم الله الرحمن الرحيم",
        "بِسۡمِ ٱللَّهِ ٱلرَّحۡمَـٰنِ ٱلرَّحِیمِ"
    ];

    // Possible translations
    $bismillahTranslations = [
        'In the name of Allah, the Entirely Merciful, the Especially Merciful',
        'In the name of God, the Most Gracious, the Most Merciful'
    ];

    foreach ($arabicResponse['ayahs'] as $key => $ayah) {
        $arabicText = $ayah['text'];
        $translationText = $translationResponse['ayahs'][$key]['text'] ?? '';

        // Remove Bismillah from first verse if it exists (except for surah 1 and 9)
        if ($shouldRemoveBismillah && $key === 0 && $ayah['numberInSurah'] === 1) {
            // Try each variation of Bismillah
            foreach ($bismillahVariations as $bismillah) {
                $arabicText = str_replace($bismillah, '', $arabicText);
            }

            // Try each translation variation
            foreach ($bismillahTranslations as $bismillahTranslation) {
                $translationText = str_replace($bismillahTranslation, '', $translationText);
            }

            // Trim any extra spaces
            $arabicText = trim($arabicText);
            $translationText = trim($translationText);
        }
        $ayahs[] = [
            'number' => $ayah['numberInSurah'],
            'arabic_text' => $arabicText,
            'translation' => $translationText
        ];
    }

    return view('quran.surah', compact('surahInfo', 'ayahs'));
}
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return redirect()->route('quran.index');
        }

        try {
            // Get all surahs first
            $allSurahsResponse = Http::get("{$this->apiBaseUrl}/surah");

            if (!$allSurahsResponse->successful()) {
                return view('quran.search', [
                    'results' => [],
                    'query' => $query,
                    'error' => 'Unable to fetch surahs from API'
                ]);
            }

            $allSurahs = $allSurahsResponse->json()['data'] ?? [];

            // Filter surahs by name matching the query
            $results = [];
            foreach ($allSurahs as $surah) {
                // Convert query and surah names to lowercase
                $lowercaseQuery = mb_strtolower($query, 'UTF-8');
                // Check various name fields that might exist in API
                $nameMatches = (isset($surah['name']) && mb_stripos($surah['name'], $query) !== false);
                $arabicNameMatches = (isset($surah['name_arabic']) && mb_stripos($surah['name_arabic'], $query) !== false);
                $englishNameMatches = (isset($surah['englishName']) && mb_stripos($surah['englishName'], $query) !== false);
                $translationMatches = (isset($surah['englishNameTranslation']) && mb_stripos($surah['englishNameTranslation'], $query) !== false);

                if ($nameMatches || $arabicNameMatches || $englishNameMatches || $translationMatches) {
                    $results[] = $surah;
                }
            }

            // timestamp for debugging
            $timestamp = date('Y-m-d H:i:s');

            return view('quran.search', compact('results', 'query', 'timestamp'));
        } catch (\Exception $e) {
            Log::error('Error in Quran search: ' . $e->getMessage());
            return view('quran.search', [
                'results' => [],
                'query' => $query,
                'error' => 'An error occurred while searching'
            ]);
        }
    }


    // public function tafsir($number)
    // {
    //     try {
    //         // Get the surah information
    //         $surahResponse = Http::get("{$this->apiBaseUrl}/surah/{$number}");
    //         if (!$surahResponse->successful()) {
    //             Log::error("Failed to fetch surah: " . $surahResponse->status());
    //             return redirect()->route('quran.index')->with('error', 'Unable to fetch surah information');
    //         }
    //         $surahInfo = $surahResponse->json()['data'];

    //         // Get the Arabic text
    //         $arabicResponse = Cache::remember("quran.surah.{$number}.arabic", 86400, function () use ($number) {
    //             return Http::get("{$this->apiBaseUrl}/surah/{$number}")->json()['data'];
    //         });

    //         // Get the translation
    //         $translationResponse = Cache::remember("quran.surah.{$number}.translation", 86400, function () use ($number) {
    //             return Http::get("{$this->apiBaseUrl}/surah/{$number}/{$this->translationEdition}")->json()['data'];
    //         });

    //         // Fetch tafsir from Quran.com API
    //         $tafsirId = 161; // Al-Muyassar tafsir
    //         $tafsirApiUrl = "https://api.quran.com/api/v4/tafsirs/{$tafsirId}/by_ayah";

    //         // Prepare ayahs array to store all data
    //         $ayahs = [];

    //         foreach ($arabicResponse['ayahs'] as $key => $ayah) {
    //             $arabicText = $ayah['text'];
    //             $ayahNumber = $ayah['numberInSurah'];
    //             $verseKey = "{$number}:{$ayahNumber}"; // Format: surah:ayah

    //             // Remove bismillah from first ayah if needed (except for Al-Fatiha and At-Tawba)
    //             if ($key === 0 && $number !== 1 && $number !== 9) {
    //                 if (strpos($arabicText, 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ') === 0) {
    //                     $arabicText = str_replace('بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', '', $arabicText);
    //                     $arabicText = trim($arabicText);
    //                 }
    //             }

    //             // Fetch tafsir for this specific ayah
    //             $tafsirText = Cache::remember("tafsir.{$tafsirId}.{$verseKey}", 86400, function () use ($tafsirApiUrl, $verseKey) {
    //                 try {
    //                     $response = Http::get($tafsirApiUrl, [
    //                         'verse_key' => $verseKey
    //                     ]);

    //                     if ($response->successful() && !empty($response->json()['tafsirs'])) {
    //                         return $response->json()['tafsirs'][0]['text'] ?? null;
    //                     }

    //                     return null;
    //                 } catch (\Exception $e) {
    //                     Log::error("Error fetching tafsir for {$verseKey}: " . $e->getMessage());
    //                     return null;
    //                 }
    //             });

    //             // If no tafsir was found, provide some fallback tafsirs for demonstration
    //             if (empty($tafsirText)) {
    //                 // Some sample tafsirs for Al-Fatiha to demonstrate functionality
    //                 if ($number == 1) {
    //                     $fallbackTafsirs = [
    //                         1 => "الحمد: الثناء على الله تعالى بصفات الكمال، وأفعال الإكرام والجلال. والله: اسم الرب المعبود بحق، المنفرد بالألوهية. ورب: مالك ومدبر، والعالمين: جمع عالم، وهو كل ما سوى الله.",
    //                         2 => "الرحمن: ذو الرحمة العامة لجميع الخلائق. الرحيم: ذو الرحمة الخاصة بالمؤمنين.",
    //                         3 => "مالك يوم الدين: أي يوم الجزاء والحساب، وهو يوم القيامة.",
    //                         4 => "معناه: نخصك وحدك -يا الله- بالعبادة، ونستعين بك وحدك في جميع أمورنا.",
    //                         5 => "أرشدنا وثبتنا على الصراط المستقيم، وهو الإسلام الذي ارتضيته لنا ديناً.",
    //                         6 => "أي أنعمت عليهم بالهداية والتوفيق، وهم الأنبياء والصديقون والشهداء والصالحون.",
    //                         7 => "أي غير طريق اليهود المغضوب عليهم بسبب عنادهم وكفرهم، وغير طريق النصارى الضالين عن الحق بسبب جهلهم وضلالتهم."
    //                     ];

    //                     $tafsirText = $fallbackTafsirs[$ayahNumber] ?? "التفسير غير متوفر لهذه الآية";
    //                 } else if ($number == 2 && $ayahNumber <= 5) {
    //                     $fallbackTafsirs = [
    //                         1 => "الم: هذه الحروف المقطعة في أوائل بعض السور من المتشابه الذي استأثر الله بعلمه، ونؤمن أن لها حكمة ومعنى يليق بعظمة القرآن.",
    //                         2 => "ذلك القرآن العظيم لا شك أنه منزل من عند الله، وأنه حق وصدق، هدى للمتقين الذين يخافون الله ويجتنبون معاصيه.",
    //                         3 => "الذين يصدقون بما أنزل الله على رسله من الغيب، ويقيمون الصلاة على وجهها الصحيح، وينفقون مما رزقهم الله في وجوه الخير.",
    //                         4 => "والذين يؤمنون بما أنزل الله عليك -أيها الرسول- وما أنزل على الرسل من قبلك، وهم موقنون بالآخرة، لا يشكون فيها.",
    //                         5 => "أولئك المتصفون بهذه الصفات على هدى عظيم من ربهم، وأولئك هم الفائزون بخير الدنيا والآخرة."
    //                     ];

    //                     $tafsirText = $fallbackTafsirs[$ayahNumber] ?? "التفسير غير متوفر لهذه الآية";
    //                 } else {
    //                     $tafsirText = "التفسير غير متوفر لهذه الآية";
    //                 }
    //             }

    //             // Add to ayahs array
    //             $ayahs[] = [
    //                 'number' => $ayahNumber,
    //                 'arabic_text' => $arabicText,
    //                 'translation' => $translationResponse['ayahs'][$key]['text'] ?? '',
    //                 'tafsir' => $tafsirText,
    //                 'verse_key' => $verseKey
    //             ];
    //         }

    //         // Add tafsir source info
    //         $tafsirInfo = [
    //             'id' => $tafsirId,
    //             'name' => 'تفسير الميسر',
    //             'source' => 'Quran.com API'
    //         ];

    //         // Add debug info if in development
    //         $debugMode = config('app.debug', false);
    //         $debugInfo = null;

    //         if ($debugMode) {
    //             $debugInfo = [
    //                 'timestamp' => date('Y-m-d H:i:s'),
    //                 'api_url' => $tafsirApiUrl,
    //                 'sample_verse_key' => $number . ':1',
    //                 'tafsir_id' => $tafsirId
    //             ];
    //         }

    //         return view('quran.tafsir', compact('surahInfo', 'ayahs', 'tafsirInfo', 'debugInfo'));
    //     } catch (\Exception $e) {
    //         Log::error("Error in tafsir method: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    //         return view('quran.tafsir-error', [
    //             'error' => 'حدث خطأ أثناء جلب التفسير. يرجى المحاولة مرة أخرى لاحقا.',
    //             'details' => $e->getMessage(),
    //             'surahNumber' => $number
    //         ]);
    //     }
    // }
}
