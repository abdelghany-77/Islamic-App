<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AzkarController extends Controller
{
    protected $apiUrl = 'https://raw.githubusercontent.com/nawafalqari/azkar-api/main/azkar.json';

    // Backup API
    protected $backupApiUrl = 'https://www.hisnmuslim.com/api/ar';

    // Cache duration in seconds (24 hours)
    protected $cacheDuration = 86400;

    public function index()
    {
        try {
            // Get categories from API or cache
            $categories = Cache::remember('azkar.categories', $this->cacheDuration, function () {
                // Fetch azkar data from API
                $response = Http::get($this->apiUrl);
                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch azkar data from API');
                }

                $azkarData = $response->json();

                if (!is_array($azkarData)) {
                    throw new \Exception('Invalid azkar data format');
                }

                // Extract unique categories
                $uniqueCategories = [];
                foreach ($azkarData as $zikr) {
                    if (isset($zikr['category']) && !isset($uniqueCategories[$zikr['category']])) {
                        $uniqueCategories[$zikr['category']] = [
                            'name' => $this->translateCategoryName($zikr['category']),
                            'arabic_name' => $zikr['category'],
                            'slug' => $this->createSlug($zikr['category']),
                            'icon' => $this->getCategoryIcon($zikr['category']),
                            'count' => 1
                        ];
                    } elseif (isset($zikr['category'])) {
                        $uniqueCategories[$zikr['category']]['count']++;
                    }
                }

                return array_values($uniqueCategories);
            });

            // If API fails, fall back to predefined categories
            if (empty($categories)) {
                $categories = $this->getFallbackCategories();
            }

            return view('azkar.index', compact('categories'));

        } catch (\Exception $e) {
            // Log error
            Log::error('Azkar API error: ' . $e->getMessage());

            // Use fallback categories
            $categories = $this->getFallbackCategories();

            return view('azkar.index', compact('categories'))
                ->with('warning', 'Using local data as the API is currently unavailable.');
        }
    }

    public function category($slug)
    {
        try {
            // Normalize slug
            $slug = Str::lower($slug);

            // Handle special cases like "Morning" vs "morning"
            if ($slug === "morning" || $slug === "evening") {
                $slug = strtolower($slug);
            }
            // Get azkar for this category
            $categoryData = Cache::remember("azkar.category.{$slug}", $this->cacheDuration, function () use ($slug) {
                // Get all azkar
                $response = Http::get($this->apiUrl);

                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch azkar data from API');
                }
                $azkarData = $response->json();
                if (!is_array($azkarData)) {
                    throw new \Exception('Invalid azkar data format');
                }

                // Find the Arabic category name from the slug
                $arabicCategoryName = null;
                foreach ($azkarData as $zikr) {
                    if (isset($zikr['category']) && $this->createSlug($zikr['category']) === $slug) {
                        $arabicCategoryName = $zikr['category'];
                        break;
                    }
                }

                if (!$arabicCategoryName) {
                    // Try to match by approximate slug
                    foreach ($azkarData as $zikr) {
                        if (isset($zikr['category']) && Str::contains($this->createSlug($zikr['category']), $slug)) {
                            $arabicCategoryName = $zikr['category'];
                            break;
                        }
                    }
                }

                if (!$arabicCategoryName) {
                    throw new \Exception('Category not found: ' . $slug);
                }

                // Filter azkar for this category
                $categoryAzkar = [];
                foreach ($azkarData as $zikr) {
                    if (isset($zikr['category']) && $zikr['category'] === $arabicCategoryName) {
                        $categoryAzkar[] = [
                            'id' => $zikr['id'] ?? count($categoryAzkar) + 1,
                            'arabic_text' => $zikr['zekr'] ?? '',
                            'transliteration' => $zikr['read'] ?? '',
                            'translation' => $zikr['en'] ?? '',
                            'reference' => $zikr['source'] ?? '',
                            'count' => $zikr['repeat'] ?? 1,
                            'benefits' => $zikr['fadl'] ?? ''
                        ];
                    }
                }

                return [
                    'name' => $this->translateCategoryName($arabicCategoryName),
                    'arabic_name' => $arabicCategoryName,
                    'azkar' => $categoryAzkar
                ];
            });

            // If no azkar found, try fallback data
            if (empty($categoryData['azkar'])) {
                $fallbackData = $this->getFallbackAzkar($slug);
                if (!empty($fallbackData)) {
                    $categoryData = $fallbackData;
                }
            }

            $displayName = $categoryData['name'];
            $arabicName = $categoryData['arabic_name'];
            $azkar = $categoryData['azkar'];

            return view('azkar.category', compact('azkar', 'displayName', 'arabicName', 'slug'));

        } catch (\Exception $e) {
            // Log error
            Log::error('Azkar category error: ' . $e->getMessage());

            // Try fallback data
            $fallbackData = $this->getFallbackAzkar($slug);

            if (!empty($fallbackData)) {
                $displayName = $fallbackData['name'];
                $arabicName = $fallbackData['arabic_name'];
                $azkar = $fallbackData['azkar'];

                return view('azkar.category', compact('azkar', 'displayName', 'arabicName', 'slug'))
                    ->with('warning', 'Using local data as the API is currently unavailable.');
            }

            // If all fails, return empty with error
            $displayName = ucfirst(str_replace('-', ' ', $slug));
            $arabicName = '';
            $azkar = [];

            return view('azkar.category', compact('azkar', 'displayName', 'arabicName', 'slug'))
                ->with('error', 'Sorry, we encountered an issue loading the azkar. Please try again later.');
        }
    }

    /**
     * Create a URL-friendly slug from Arabic text
     */
    private function createSlug($text)
    {
        $arabicToEnglish = [
            'أذكار الصباح' => 'morning',
            'أذكار المساء' => 'evening',
            'أذكار النوم' => 'sleep',
            'أذكار الاستيقاظ من النوم' => 'wake-up',
            'أذكار بعد الصلاة' => 'after-prayer',
            'أذكار دخول المنزل' => 'home-entry',
            'أذكار الخروج من المنزل' => 'home-exit',
            'أذكار الطعام' => 'food',
            'أذكار السفر' => 'travel',
            'أذكار الهم والحزن' => 'distress',
            'أذكار الاستغفار' => 'forgiveness'
        ];

        if (isset($arabicToEnglish[$text])) {
            return $arabicToEnglish[$text];
        }

        // Fallback for other categories
        // Replace non-letter or digits with -
        $slug = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Convert to lowercase
        $slug = strtolower($slug);

        // Remove unwanted characters
        $slug = preg_replace('~[^-\w]+~', '', $slug);

        // Trim
        $slug = trim($slug, '-');

        // Remove duplicate -
        $slug = preg_replace('~-+~', '-', $slug);

        // If empty, use generic
        if (empty($slug)) {
            return 'zikr';
        }

        return $slug;
    }

    /**
     * Translate Arabic category names to English
     */
    private function translateCategoryName($arabicName)
    {
        $translations = [
            'أذكار الصباح' => 'Morning Azkar',
            'أذكار المساء' => 'Evening Azkar',
            'أذكار النوم' => 'Before Sleep Azkar',
            'أذكار الاستيقاظ من النوم' => 'Waking Up Azkar',
            'أذكار بعد الصلاة' => 'After Prayer Azkar',
            'أذكار دخول المنزل' => 'Entering Home Azkar',
            'أذكار الخروج من المنزل' => 'Leaving Home Azkar',
            'أذكار الطعام' => 'Food & Drink Azkar',
            'أذكار السفر' => 'Travel Azkar',
            'أذكار الهم والحزن' => 'Distress Azkar',
            'أذكار الاستغفار' => 'Forgiveness Azkar'
        ];

        return $translations[$arabicName] ?? ucfirst(str_replace('-', ' ', $this->createSlug($arabicName)));
    }

    /**
     * Get an icon name for a category
     */
    private function getCategoryIcon($categoryName)
    {
        $icons = [
            'morning' => 'sun',
            'evening' => 'moon',
            'sleep' => 'bed',
            'wake-up' => 'wake',
            'after-prayer' => 'prayer',
            'home-entry' => 'home',
            'home-exit' => 'door',
            'food' => 'food',
            'travel' => 'car',
            'distress' => 'heart',
            'forgiveness' => 'hands'
        ];

        $slug = $this->createSlug($categoryName);

        foreach ($icons as $key => $icon) {
            if (Str::contains($slug, $key)) {
                return $icon;
            }
        }

        return 'star'; // default icon
    }

    /**
     * Get fallback categories when API fails
     */
    private function getFallbackCategories()
    {
        return [
            [
                'name' => 'Morning Azkar',
                'arabic_name' => 'أذكار الصباح',
                'slug' => 'morning',
                'icon' => 'sun',
                'count' => 15
            ],
            [
                'name' => 'Evening Azkar',
                'arabic_name' => 'أذكار المساء',
                'slug' => 'evening',
                'icon' => 'moon',
                'count' => 15
            ],
            [
                'name' => 'After Prayer Azkar',
                'arabic_name' => 'أذكار بعد الصلاة',
                'slug' => 'after-prayer',
                'icon' => 'prayer',
                'count' => 10
            ],
            [
                'name' => 'Before Sleep Azkar',
                'arabic_name' => 'أذكار النوم',
                'slug' => 'sleep',
                'icon' => 'bed',
                'count' => 12
            ],
            [
                'name' => 'Waking Up Azkar',
                'arabic_name' => 'أذكار الاستيقاظ',
                'slug' => 'wake-up',
                'icon' => 'wake',
                'count' => 5
            ],
            [
                'name' => 'Entering Home Azkar',
                'arabic_name' => 'أذكار دخول المنزل',
                'slug' => 'home-entry',
                'icon' => 'home',
                'count' => 3
            ],
            [
                'name' => 'Leaving Home Azkar',
                'arabic_name' => 'أذكار الخروج من المنزل',
                'slug' => 'home-exit',
                'icon' => 'door',
                'count' => 3
            ],
            [
                'name' => 'Food & Drink Azkar',
                'arabic_name' => 'أذكار الطعام والشراب',
                'slug' => 'food',
                'icon' => 'food',
                'count' => 8
            ]
        ];
    }

    /**
     * Get fallback azkar data when API fails
     */
        /**
     * Get fallback azkar data when API fails
     */
    private function getFallbackAzkar($slug)
    {
        $fallbackData = [
            'morning' => [
                'name' => 'Morning Azkar',
                'arabic_name' => 'أذكار الصباح',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'اللَّهُ لاَ إِلَهَ إِلاَّ هُوَ الْحَيُّ الْقَيُّومُ لاَ تَأْخُذُهُ سِنَةٌ وَلاَ نَوْمٌ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الأَرْضِ مَن ذَا الَّذِي يَشْفَعُ عِنْدَهُ إِلاَّ بِإِذْنِهِ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ وَلاَ يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلاَّ بِمَا شَاء وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالأَرْضَ وَلاَ يَؤُودُهُ حِفْظُهُمَا وَهُوَ الْعَلِيُّ الْعَظِيمُ',
                        'transliteration' => 'Allahu la ilaha illa Huwal-Hayyul-Qayyum, la ta\'khuthuhu sinatun wa la nawm, lahu ma fis-samawati wa ma fil-\'ard, man thal-lathee yashfa\'u \'indahu illa bi\'ithnih, ya\'lamu ma bayna aydeehim wa ma khalfahum, wa la yuheetuna bishay\'im-min \'ilmihi illa bima sha\', wasi\'a kursiyyuhus-samawati wal-\'ard, wa la ya\'uduhu hifthuhuma, wa Huwal-\'Aliyyul-\'Atheem',
                        'translation' => 'Allah! There is no god but He - the Living, The Self-subsisting, Eternal. No slumber can seize Him nor sleep. His are all things in the heavens and on earth. Who is there can intercede in His presence except as he permitteth? He knoweth what (appeareth to His creatures as) before or after or behind them. Nor shall they compass aught of His knowledge except as He willeth. His Throne doth extend over the heavens and the earth, and He feeleth no fatigue in guarding and preserving them for He is the Most High, the Supreme (in glory).',
                        'reference' => '[آية الكرسى - سورة البقرة 255].',
                        'benefits' => 'من قالها حين يصبح أجير من الجن حتى يمسى ومن قالها حين يمسى أجير من الجن حتى يصبح.',
                        'count' => 1
                    ],
                    [
                        'id' => 2,
                        'arabic_text' => ' قُلۡ هُوَ ٱللَّهُ أَحَدٌ ﴿١﴾ ٱللَّهُ ٱلصَّمَدُ ﴿٢﴾ لَمۡ يَلِدۡ وَلَمۡ يُولَدۡ ﴿٣﴾ وَلَمۡ يَكُن لَّهُۥ كُفُوًا أَحَدُۢ ﴿٤﴾',
                        'transliteration' => 'Qul huwa Allahu ahad, Allahus-samad, lam yalid wa lam yulad, wa lam yakul-lahu kufuwan ahad',
                        'translation' => 'Say: He is Allah, the One; Allah, the Eternal, Absolute; He begetteth not, nor is He begotten; And there is none like unto Him.',
                        'reference' => 'سورة الإخلاص',
                        'benefits' => 'من قالها حين يصبح وحين يمسى كفته من كل شىء (الإخلاص والمعوذتين).',
                        'count' => 3
                    ],
                    [
                        'id' => 3,
                        'arabic_text' => ' قُلۡ أَعُوذُ بِرَبِّ ٱلۡفَلَقِ ﴿١﴾ مِن شَرِّ مَا خَلَقَ ﴿٢﴾ وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ ﴿٣﴾ وَمِن شَرِّ ٱلنَّفَّٰثَٰتِ فِي ٱلۡعُقَدِ ﴿٤﴾ وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ ﴿٥﴾',
                        'transliteration' => 'Qul a\'uthu bi rabbil-falaq, min sharri ma khalaq, wa min sharri ghasiqin itha waqab, wa min sharrin-naffathati fil-\'uqad, wa min sharri hasidin itha hasad',
                        'translation' => 'Say: I seek refuge with the Lord of the Dawn, from the mischief of created things; from the mischief of Darkness as it overspreads; from the mischief of those who practice Secret Arts; and from the mischief of the envious one as he practices envy.',
                        'reference' => 'سورة الفلق',
                        'benefits' => 'من قالها حين يصبح وحين يمسى كفته من كل شىء (الإخلاص والمعوذتين).',
                        'count' => 3
                    ],
                    [
                        'id' => 4,
                        'arabic_text' => 'قُلۡ أَعُوذُ بِرَبِّ ٱلنَّاسِ ﴿١﴾ مَلِكِ ٱلنَّاسِ ﴿٢﴾ إِلَٰهِ ٱلنَّاسِ ﴿٣﴾ مِن شَرِّ ٱلۡوَسۡوَاسِ ٱلۡخَنَّاسِ ﴿٤﴾ ٱلَّذِي يُوَسۡوِسُ فِي صُدُورِ ٱلنَّاسِ ﴿٥﴾ مِنَ ٱلۡجِنَّةِ وَٱلنَّاسِ ﴿٦﴾',
                        'transliteration' => 'Qul a\'uthu bi rabbin-nas, malikin-nas, ilahin-nas, min sharril-waswasil-khannas, allathee yuwaswisu fee sudurin-nas, minal-jinnati wan-nas',
                        'translation' => 'Say: I seek refuge with the Lord and Cherisher of Mankind, the King (or Ruler) of Mankind, the God (or Judge) of Mankind, from the mischief of the Whisperer (of Evil), who withdraws (after his whisper), (the same) who whispers into the hearts of Mankind, among Jinns and among Men.',
                        'reference' => 'سورة الناس',
                        'benefits' => 'من قالها حين يصبح وحين يمسى كفته من كل شىء (الإخلاص والمعوذتين).',
                        'count' => 3
                    ],
                    [
                        'id' => 5,
                        'arabic_text' => 'أَصْبَحْنَا وَأَصْبَحَ الْمُلْكُ لِلَّهِ، وَالْحَمْدُ لِلَّهِ، لاَ إِلَـهَ إِلاَّ اللهُ وَحْدَهُ لاَ شَرِيْكَ لَهُ، لَهُ الْمُلْكُ وَلَهُ الْحَمْدُ وَهُوَ عَلَى كُلِّ شَيْءٍ قَدِيْرُ. رَبِّ أَسْأَلُكَ خَيْرَ مَا فِيْ هَذَا الْيَوْمِ وَخَيْرَ مَا بَعْدَهُ، وَأَعُوْذُ بِكَ مِنْ شَرِّ مَا فِيْ هَذَا الْيَوْمِ وَشَرِّ مَا بَعْدَهُ، رَبِّ أَعُوْذُ بِكَ مِنَ الْكَسَلِ وَسُوْءِ الْكِبَرِ، رَبِّ أَعُوْذُ بِكَ مِنْ عَذَابٍ فِي النَّارِ وَعَذَابٍ فِي الْقَبْرِ',
                        'transliteration' => 'Asbahna wa asbahal-mulku lillah, walhamdu lillah, la ilaha illallahu wahdahu la shareeka lah, lahul-mulku walahul-hamd, wa huwa \'ala kulli shay\'in qadeer. Rabbi as\'aluka khayra ma fee hatha-lyawmi wa khayra ma ba\'dahu, wa a\'uthu bika min sharri ma fee hatha-lyawmi wa sharri ma ba\'dahu, Rabbi a\'uthu bika minal-kasal, wa su\'il-kibar, Rabbi a\'uthu bika min \'athabin fin-nar, wa \'athabin fil-qabr',
                        'translation' => 'We have reached the morning and at this very time all sovereignty belongs to Allah, and all praise is for Allah. None has the right to be worshipped except Allah, alone, without partner, to Him belongs all sovereignty and praise and He is over all things omnipotent. My Lord, I ask You for the good of this day and the good of what follows it and I take refuge in You from the evil of this day and the evil of what follows it. My Lord, I take refuge in You from laziness and senility. My Lord, I take refuge in You from torment in the Fire and punishment in the grave.',
                        'reference' => 'رواه مسلم',
                        'count' => 1
                    ],
                    [
                        'id' => 6,
                        'arabic_text' => 'اللّهمَّ أَنْتَ رَبِّي لا إلهَ إلاّ أَنْتَ ، خَلَقْتَني وَأَنا عَبْدُك ، وَأَنا عَلى عَهْدِكَ وَوَعْدِكَ ما اسْتَطَعْت ، أَعوذُبِكَ مِنْ شَرِّ ما صَنَعْت ، أَبوءُ لَكَ بِنِعْمَتِكَ عَلَيَّ وَأَبوءُ بِذَنْبي فَاغْفِرْ لي فَإِنَّهُ لا يَغْفِرُ الذُّنوبَ إِلاّ أَنْتَ',
                        'transliteration' => 'Allahumma anta Rabbi la ilaha illa anta, khalaqtani wa ana ‘abduka, wa ana ‘ala ‘ahdika wa wa‘dika ma istata‘tu, a‘udhu bika min sharri ma sana‘tu, abu’u laka bini‘matika ‘alayya wa abu’u bidhanbi faghfir li fa innahu la yaghfiru al-dhunuba illa anta',
                        'translation' => 'O Allah, You are my Lord, there is no god but You. You created me and I am Your servant, and I am upon Your covenant and promise as much as I am able. I seek refuge in You from the evil of what I have done. I acknowledge Your blessings upon me and I acknowledge my sin. So forgive me, for indeed, none forgives sins except You.',
                        'reference' => 'رواه البخاري',
                        'benefits' => 'من قالها موقنا بها حين يمسى ومات من ليلته دخل الجنة وكذلك حين يصبح.',
                        'count' => 1
                    ],
                    [
                        'id' => 7,
                        'arabic_text' => 'اللّهُمَّ إِنِّي أَصْبَحْتُ أُشْهِدُك ، وَأُشْهِدُ حَمَلَةَ عَرْشِك ، وَمَلَائِكَتَكَ ، وَجَميعَ خَلْقِك ، أَنَّكَ أَنْتَ اللهُ لا إلهَ إلاّ أَنْتَ وَحْدَكَ لا شَريكَ لَك ، وَأَنَّ مُحَمّداً عَبْدُكَ وَرَسولُك',
                        'transliteration' => 'Allahumma inni asbahtu ushhiduka, wa ushhidu hamalata ‘arshika, wa mala’ikataka, wa jami‘a khalqika, annaka anta Allahu la ilaha illa anta wahdaka la shareeka laka, wa anna Muhammadan ‘abduka wa rasuluka',
                        'translation' => 'O Allah, I have reached the morning and I call You to witness, and I call the bearers of Your throne, Your angels, and all Your creation to witness that You are Allah, there is no god but You, alone, without partner, and that Muhammad is Your servant and Messenger.',
                        'reference' => 'رواه أبو داود',
                        'benefits' => 'من قالها أعتقه الله من النار.',
                        'count' => 4
                    ],
                    [
                        'id' => 8,
                        'arabic_text' => 'اللّهُمَّ ما أَصْبََحَ بي مِنْ نِعْمَةٍ أَو بِأَحَدٍ مِنْ خَلْقِك ، فَمِنْكَ وَحْدَكَ لا شريكَ لَك ، فَلَكَ الْحَمْدُ وَلَكَ الشُّكْر',
                        'transliteration' => 'Allahumma ma asbaha bi min ni‘matin aw bi ahadin min khalqika, faminka wahdaka la shareeka laka, falakal-hamdu wa lakash-shukru',
                        'translation' => 'O Allah, whatever blessing has come to me or to any of Your creation this morning is from You alone, without partner, so to You is all praise and to You is all thanks.',
                        'reference' => 'رواه أبو داود',
                        'benefits' => 'من قالها حين يصبح أدى شكر يومه.',
                        'count' => 1
                    ],
                    [
                        'id' => 9,
                        'arabic_text' => 'حَسْبِيَ اللّهُ لا إلهَ إلاّ هُوَ عَلَيهِ تَوَكَّلتُ وَهُوَ رَبُّ العَرْشِ العَظيم',
                        'transliteration' => 'Hasbiyallahu la ilaha illa huwa, ‘alayhi tawakkaltu wa huwa Rabbul-‘arshil-‘azim',
                        'translation' => 'Allah is sufficient for me; there is no god but He. In Him I put my trust, and He is the Lord of the Mighty Throne.',
                        'reference' => 'رواه السني',
                        'benefits' => 'من قالها كفاه الله ما أهمه من أمر الدنيا والأخرة.',
                        'count' => 7
                    ],
                    [
                        'id' => 10,
                        'arabic_text' => 'بِسمِ اللهِ الذي لا يَضُرُّ مَعَ اسمِهِ شَيءٌ في الأرْضِ وَلا في السّماءِ وَهوَ السّميعُ العَليم',
                        'transliteration' => 'Bismillahilladhi la yadurru ma‘a ismihi shay’un fil-ardi wa la fis-sama’i wa huwas-Sami‘ul-‘Alim',
                        'translation' => 'In the name of Allah, with whose name nothing on earth or in the heavens can cause harm, and He is the All-Hearing, the All-Knowing.',
                        'reference' => 'رواه ابن ماجة',
                        'benefits' => 'لم يضره من الله شيء.',
                        'count' => 3
                    ],
                    [
                        'id' => 11,
                        'arabic_text' => 'اللّهُمَّ بِكَ أَصْبَحْنا وَبِكَ أَمْسَينا ، وَبِكَ نَحْيا وَبِكَ نَمُوتُ وَإِلَيْكَ النُّشُور',
                        'transliteration' => 'Allahumma bika asbahna wa bika amsayna, wa bika nahya wa bika namutu wa ilaykan-nushur',
                        'translation' => 'O Allah, by You we have reached the morning and by You we have reached the evening, by You we live and by You we die, and to You is the resurrection.',
                        'reference' => 'رواه الترمذي',
                        'count' => 1
                    ],
                    [
                        'id' => 12,
                        'arabic_text' => 'أَصْبَحْنا عَلَى فِطْرَةِ الإسْلاَمِ، وَعَلَى كَلِمَةِ الإِخْلاَصِ، وَعَلَى دِينِ نَبِيِّنَا مُحَمَّدٍ صَلَّى اللهُ عَلَيْهِ وَسَلَّمَ، وَعَلَى مِلَّةِ أَبِينَا إبْرَاهِيمَ حَنِيفاً مُسْلِماً وَمَا كَانَ مِنَ المُشْرِكِينَ',
                        'transliteration' => 'Asbahna ‘ala fitratil-Islam, wa ‘ala kalimatil-ikhlas, wa ‘ala deeni nabiyyina Muhammadin sallallahu ‘alayhi wa sallam, wa ‘ala millati abina Ibrahima hanifan musliman wa ma kana minal-mushrikeen',
                        'translation' => 'We have reached the morning upon the natural disposition of Islam, and upon the word of sincerity, and upon the religion of our Prophet Muhammad (peace be upon him), and upon the way of our father Abraham, inclining toward truth, a Muslim, and he was not of the polytheists.',
                        'reference' => 'رواه أحمد',
                        'count' => 1
                    ],
                    [
                        'id' => 13,
                        'arabic_text' => 'سُبْحانَ اللهِ وَبِحَمْدِهِ عَدَدَ خَلْقِه ، وَرِضا نَفْسِه ، وَزِنَةَ عَرْشِه ، وَمِدادَ كَلِماتِه',
                        'transliteration' => 'Subhanallahi wa bihamdihi ‘adada khalqihi, wa rida nafsihi, wa zinata ‘arshihi, wa midada kalimatihi',
                        'translation' => 'Glory be to Allah and praise be to Him, as much as the number of His creation, as much as pleases Him, as much as the weight of His Throne, and as much as the ink of His words.',
                        'reference' => 'رواه مسلم',
                        'count' => 3
                    ],
                    [
                        'id' => 14,
                        'arabic_text' => 'اللّهُمَّ عافِني في بَدَني ، اللّهُمَّ عافِني في سَمْعي ، اللّهُمَّ عافِني في بَصَري ، لا إلهَ إلاّ أَنْتَ',
                        'transliteration' => 'Allahumma ‘afini fi badani, Allahumma ‘afini fi sam‘i, Allahumma ‘afini fi basari, la ilaha illa anta',
                        'translation' => 'O Allah, grant me well-being in my body, O Allah, grant me well-being in my hearing, O Allah, grant me well-being in my sight. There is no god but You.',
                        'reference' => 'رواه أحمد',
                        'count' => 3
                    ],
                    [
                        'id' => 15,
                        'arabic_text' => 'اللّهُمَّ إِنّي أَعوذُ بِكَ مِنَ الْكُفر ، وَالفَقْر ، وَأَعوذُ بِكَ مِنْ عَذابِ القَبْر ، لا إلهَ إلاّ أَنْتَ',
                        'transliteration' => 'Allahumma inni a‘udhu bika minal-kufri, wal-faqri, wa a‘udhu bika min ‘adhabíl-qabri, la ilaha illa anta',
                        'translation' => 'O Allah, I seek refuge in You from disbelief and poverty, and I seek refuge in You from the punishment of the grave. There is no god but You.',
                        'reference' => 'رواه أحمد',
                        'count' => 3
                    ],
                    [
                        'id' => 16,
                        'arabic_text' => 'اللّهُمَّ إِنِّي أسْأَلُكَ العَفْوَ وَالعافِيةَ في الدُّنْيا وَالآخِرَة ، اللّهُمَّ إِنِّي أسْأَلُكَ العَفْوَ وَالعافِيةَ في ديني وَدُنْيايَ وَأهْلي وَمالي ، اللّهُمَّ اسْتُرْ عوْراتي وَآمِنْ رَوْعاتي ، اللّهُمَّ احْفَظْني مِن بَينِ يَدَيَّ وَمِن خَلْفي وَعَن يَميني وَعَن شِمالي ، وَمِن فَوْقي ، وَأَعوذُ بِعَظَمَتِكَ أَن أُغْتالَ مِن تَحْتي',
                        'transliteration' => 'Allahumma inni as’aluka al-‘afwa wal-‘afiyata fid-dunya wal-akhirah, Allahumma inni as’aluka al-‘afwa wal-‘afiyata fi deeni wa dunyaya wa ahli wa mali, Allahumma ustur ‘awrati wa amin raw‘ati, Allahumma ihfazni min bayni yadayya wa min khalfi wa ‘an yamini wa ‘an shimali, wa min fawqi, wa a‘udhu bi‘azamatika an ughtala min tahti',
                        'translation' => 'O Allah, I ask You for pardon and well-being in this world and the Hereafter. O Allah, I ask You for pardon and well-being in my religion, my worldly affairs, my family, and my wealth. O Allah, conceal my faults and calm my fears. O Allah, protect me from in front of me, from behind me, from my right, from my left, and from above me, and I seek refuge in Your greatness from being unexpectedly destroyed from beneath me.',
                        'reference' => 'رواه أبو داود',
                        'count' => 1
                    ],
                    [
                        'id' => 17,
                        'arabic_text' => 'يَا حَيُّ يَا قيُّومُ بِرَحْمَتِكَ أسْتَغِيثُ أصْلِحْ لِي شَأنِي كُلَّهُ وَلاَ تَكِلْنِي إلَى نَفْسِي طَرْفَةَ عَيْنٍ',
                        'transliteration' => 'Ya Hayyu ya Qayyumu birahmatika astagheethu aslih li sha’ni kullahu wa la takilni ila nafsi tarfata ‘aynin',
                        'translation' => 'O Ever-Living, O Sustainer, by Your mercy I seek help. Rectify for me all of my affairs and do not leave me to myself even for the blink of an eye.',
                        'reference' => 'رواه الترمذي',
                        'count' => 3
                    ],
                    [
                        'id' => 18,
                        'arabic_text' => 'أَصْبَحْنا وَأَصْبَحْ المُلكُ للهِ رَبِّ العالَمين ، اللّهُمَّ إِنِّي أسْأَلُكَ خَيْرَ هذا اليَوْم ، فَتْحَهُ ، وَنَصْرَهُ ، وَنورَهُ وَبَرَكَتَهُ ، وَهُداهُ ، وَأَعوذُ بِكَ مِنْ شَرِّ ما فيهِ وَشَرِّ ما بَعْدَه',
                        'transliteration' => 'Asbahna wa asbahal-mulku lillahi Rabbil-‘alamin, Allahumma inni as’aluka khayra hadha-lyawmi, fathahu, wa nasrahu, wa nurahu wa barakatahu, wa hudahu, wa a‘udhu bika min sharri ma fihi wa sharri ma ba‘dahu',
                        'translation' => 'We have reached the morning and the dominion belongs to Allah, Lord of the worlds. O Allah, I ask You for the good of this day, its openings, its victories, its light, its blessings, and its guidance, and I seek refuge in You from the evil of what is in it and the evil of what follows it.',
                        'reference' => 'رواه أبو داود',
                        'count' => 1
                    ],
                    [
                        'id' => 19,
                        'arabic_text' => 'اللّهُمَّ عالِمَ الغَيْبِ وَالشّهادَةِ فاطِرَ السّماواتِ وَالأرْضِ رَبَّ كلِّ شَيءٍ وَمَليكَه ، أَشْهَدُ أَنْ لا إِلهَ إِلاّ أَنْت ، أَعوذُ بِكَ مِن شَرِّ نَفْسي وَمِن شَرِّ الشَّيْطانِ وَشِرْكِهِ ، وَأَنْ أَقْتَرِفَ عَلى نَفْسي سوءاً أَوْ أَجُرَّهُ إِلى مُسْلِم',
                        'transliteration' => 'Allahumma ‘alimal-ghaybi wash-shahadati fatiras-samawati wal-ardi Rabba kulli shay’in wa malikahu, ash-hadu an la ilaha illa anta, a‘udhu bika min sharri nafsi wa min sharrish-shaytani wa shirkihi, wa an aqtarifa ‘ala nafsi su’an aw ajurrahu ila muslim',
                        'translation' => 'O Allah, Knower of the unseen and the witnessed, Creator of the heavens and the earth, Lord and Sovereign of all things, I bear witness that there is no god but You. I seek refuge in You from the evil of my soul and from the evil of Satan and his association, and from committing wrong against myself or bringing it upon a Muslim.',
                        'reference' => 'رواه الترمذي',
                        'count' => 1
                    ],
                    [
                        'id' => 20,
                        'arabic_text' => 'أَعوذُ بِكَلِماتِ اللّهِ التّامّاتِ مِنْ شَرِّ ما خَلَق',
                        'transliteration' => 'A‘udhu bikalimatillahit-tammati min sharri ma khalaq',
                        'translation' => 'I seek refuge in the perfect words of Allah from the evil of what He has created.',
                        'reference' => 'رواه مسلم',
                        'count' => 3
                    ],
                    [
                        'id' => 21,
                        'arabic_text' => 'اللَّهُمَّ صَلِّ وَسَلِّمْ وَبَارِكْ على نَبِيِّنَا مُحمَّد',
                        'transliteration' => 'Allahumma salli wa sallim wa barik ‘ala nabiyyina Muhammad',
                        'translation' => 'O Allah, send blessings, peace, and mercy upon our Prophet Muhammad.',
                        'reference' => 'رواه الطبراني',
                        'benfits' => 'من صلى على حين يصبح وحين يمسى ادركته شفاعتى يوم القيامة.',
                        'count' => 10
                    ],
                    [
                        'id' => 22,
                        'arabic_text' => 'اللَّهُمَّ إِنَّا نَعُوذُ بِكَ مِنْ أَنْ نُشْرِكَ بِكَ شَيْئًا نَعْلَمُهُ ، وَنَسْتَغْفِرُكَ لِمَا لَا نَعْلَمُهُ',
                        'transliteration' => 'Allahumma inna na‘udhu bika min an nushrika bika shay’an na‘lamuhu, wa nastaghfiruka lima la na‘lamuhu',
                        'translation' => 'O Allah, we seek refuge in You from associating anything with You that we know, and we ask Your forgiveness for what we do not know.',
                        'reference' => 'رواه أحمد',
                        'count' => 3
                    ],
                    [
                        'id' => 23,
                        'arabic_text' => 'اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنْ الْهَمِّ وَالْحَزَنِ، وَأَعُوذُ بِكَ مِنْ الْعَجْزِ وَالْكَسَلِ، وَأَعُوذُ بِكَ مِنْ الْجُبْنِ وَالْبُخْلِ، وَأَعُوذُ بِكَ مِنْ غَلَبَةِ الدَّيْنِ، وَقَهْرِ الرِّجَالِ',
                        'transliteration' => 'Allahumma inni a‘udhu bika minal-hammi wal-hazan, wa a‘udhu bika minal-‘ajzi wal-kasal, wa a‘udhu bika minal-jubni wal-bukhl, wa a‘udhu bika min ghalabatid-dayn, wa qahrir-rijal',
                        'translation' => 'O Allah, I seek refuge in You from anxiety and sorrow, from weakness and laziness, from cowardice and miserliness, and from being overburdened by debt and from the oppression of men.',
                        'reference' => 'رواه أبو داود',
                        'count' => 3
                    ],
                    [
                        'id' => 24,
                        'arabic_text' => 'أسْتَغْفِرُ اللهَ العَظِيمَ الَّذِي لاَ إلَهَ إلاَّ هُوَ، الحَيُّ القَيُّومُ، وَأتُوبُ إلَيهِ',
                        'transliteration' => 'Astaghfirullahal-‘azimalladhi la ilaha illa huwa, al-Hayyul-Qayyum, wa atubu ilayhi',
                        'translation' => 'I seek forgiveness from Allah, the Most Great, there is no god but He, the Ever-Living, the Sustainer, and I repent to Him.',
                        'reference' => 'رواه الترمذي',
                        'count' => 3
                    ],
                    [
                        'id' => 25,
                        'arabic_text' => 'يَا رَبِّ , لَكَ الْحَمْدُ كَمَا يَنْبَغِي لِجَلَالِ وَجْهِكَ , وَلِعَظِيمِ سُلْطَانِكَ',
                        'transliteration' => 'Ya Rabbi, lakal-hamdu kama yanbaghi lijalali wajhika, wa li‘azimi sultanika',
                        'translation' => 'O my Lord, to You is all praise as befits the majesty of Your countenance and the greatness of Your authority.',
                        'reference' => 'رواه ابن ماجة',
                        'count' => 3
                    ],
                    [
                        'id' => 26,
                        'arabic_text' => 'اللَّهُمَّ إِنِّي أَسْأَلُكَ عِلْمًا نَافِعًا، وَرِزْقًا طَيِّبًا، وَعَمَلًا مُتَقَبَّلًا',
                        'transliteration' => 'Allahumma inni as’aluka ‘ilman nafi‘an, wa rizqan tayyiban, wa ‘amalan mutaqabbalan',
                        'translation' => 'O Allah, I ask You for beneficial knowledge, good provision, and accepted deeds.',
                        'reference' => 'رواه ابن ماجة',
                        'count' => 1
                    ],
                    [
                        'id' => 27,
                        'arabic_text' => 'اللَّهُمَّ أَنْتَ رَبِّي لا إِلَهَ إِلا أَنْتَ ، عَلَيْكَ تَوَكَّلْتُ ، وَأَنْتَ رَبُّ الْعَرْشِ الْعَظِيمِ , مَا شَاءَ اللَّهُ كَانَ ، وَمَا لَمْ يَشَأْ لَمْ يَكُنْ ، وَلا حَوْلَ وَلا قُوَّةَ إِلا بِاللَّهِ الْعَلِيِّ الْعَظِيمِ , أَعْلَمُ أَنَّ اللَّهَ عَلَى كُلِّ شَيْءٍ قَدِيرٌ ، وَأَنَّ اللَّهَ قَدْ أَحَاطَ بِكُلِّ شَيْءٍ عِلْمًا , اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنْ شَرِّ نَفْسِي ، وَمِنْ شَرِّ كُلِّ دَابَّةٍ أَنْتَ آخِذٌ بِنَاصِيَتِهَا ، إِنَّ رَبِّي عَلَى صِرَاطٍ مُسْتَقِيمٍ',
                        'transliteration' => 'Allahumma anta Rabbi la ilaha illa anta, ‘alayka tawakkaltu, wa anta Rabbul-‘arshil-‘azim, ma sha’allahu kana, wa ma lam yasha’ lam yakun, wa la hawla wa la quwwata illa billahil-‘Aliyyil-‘Azim, a‘lamu annallaha ‘ala kulli shay’in qadeer, wa annallaha qad ahata bikulli shay’in ‘ilma, Allahumma inni a‘udhu bika min sharri nafsi, wa min sharri kulli dabbatin anta akhidhun binasiyatiha, inna Rabbi ‘ala siratin mustaqim',
                        'translation' => 'O Allah, You are my Lord, there is no god but You. I rely upon You, and You are the Lord of the Mighty Throne. Whatever Allah wills happens, and whatever He does not will does not happen. There is no power or strength except with Allah, the Most High, the Most Great. I know that Allah is capable of all things and that Allah has encompassed everything in knowledge. O Allah, I seek refuge in You from the evil of my soul and from the evil of every creature that You seize by its forelock. Indeed, my Lord is on a straight path.',
                        'reference' => 'رواه أحمد',
                        'count' => 1
                    ],
                    [
                        'id' => 28,
                        'arabic_text' => 'لَا إلَه إلّا اللهُ وَحْدَهُ لَا شَرِيكَ لَهُ، لَهُ الْمُلْكُ وَلَهُ الْحَمْدُ وَهُوَ عَلَى كُلِّ شَيْءِ قَدِيرِ',
                        'transliteration' => 'La ilaha illallahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu wa huwa ‘ala kulli shay’in qadeer',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, and He is over all things competent.',
                        'reference' => 'رواه الترمذي',
                        'benefits' => 'من قالها 100 مرة في يوم كانت له عدل عشر رقاب، وكتبت له مئة حسنة، ومحيت عنه مئة سيئة، وكانت له حرزا من الشيطان في يومه ذلك حتى يمسي ولم يأتي أحد بأفضل مما جاء به إلا احد عمل أكثر من ذلك',
                        'count' => 100
                    ],
                    [
                        'id' => 29,
                        'arabic_text' => 'سُبْحانَ اللهِ وَبِحَمْدِهِ',
                        'transliteration' => 'Subhanallahi wa bihamdihi',
                        'translation' => 'Glory be to Allah and praise be to Him.',
                        'reference' => 'رواه مسلم',
                        'benefits' => 'من قال : سبحان اللهِ وبحمدِه مائةَ مرةٍ غُفرَتْ له ذنوبُه وإنْ كانتْ مثلَ زبَدِ البحرِ',
                        'count' => 100
                    ],
                    [
                        'id' => 30,
                        'arabic_text' => 'أسْتَغْفِرُ اللهَ وَأتُوبُ إلَيْهِ',
                        'transliteration' => 'Astaghfirullaha wa atubu ilayhi',
                        'translation' => 'I seek forgiveness from Allah and repent to Him.',
                        'reference' => 'رواه البخاري',
                        'count' => 100
                    ]
                ]
            ],
            'evening' => [
                'name' => 'Evening Azkar',
                'arabic_name' => 'أذكار المساء',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'اللّهُ لاَ إِلَهَ إِلاَّ هُوَ الْحَيُّ الْقَيُّومُ لاَ تَأْخُذُهُ سِنَةٌ وَلاَ نَوْمٌ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الأَرْضِ مَن ذَا الَّذِي يَشْفَعُ عِنْدَهُ إِلاَّ بِإِذْنِهِ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ وَلاَ يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلاَّ بِمَا شَاء وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالأَرْضَ وَلاَ يَؤُودُهُ حِفْظُهُمَا وَهُوَ الْعَلِيُّ الْعَظِيمُ',
                        'transliteration' => 'Allahu la ilaha illa huwa alhayyu alqayyumu la takhuthuhu sinatun wala nawmun lahu ma fee alssamawati wama fee alardi man tha allathee yashfaAAu AAindahu illa biithnihi yaAAlamu ma bayna aydeehim wama khalfahum wala yuheetoona bishayin min AAilmihi illa bima shaa wasiAAa kursiyyuhu alssamawati waalarda wala yaooduhu hifthuhuma wahuwa alAAaliyyu alAAatheemu',
                        'translation' => 'Allah - there is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth. Who is it that can intercede with Him except by His permission? He knows what is [presently] before them and what will be after them, and they encompass not a thing of His knowledge except for what He wills. His Kursi extends over the heavens and the earth, and their preservation tires Him not. And He is the Most High, the Most Great.',
                        'reference' => '[آية الكرسى - سورة البقرة 255]',
                        'benefits' => 'من قالها حين يصبح أجير من الجن حتى يمسى ومن قالها حين يمسى أجير من الجن حتى يصبح',
                        'count' => 1
                    ],
                    [
                        'id' => 2,
                        'arabic_text' => ' قُلۡ هُوَ ٱللَّهُ أَحَدٌ ﴿١﴾ ٱللَّهُ ٱلصَّمَدُ ﴿٢﴾ لَمۡ يَلِدۡ وَلَمۡ يُولَدۡ ﴿٣﴾ وَلَمۡ يَكُن لَّهُۥ كُفُوًا أَحَدُۢ ﴿٤﴾',
                        'transliteration' => 'Bismillahi alrrahmani alrraheemi qul huwa Allahu ahadun Allahu alssamadu lam yalid walam yooladu walam yakun lahu kufuwan ahadun',
                        'translation' => 'In the name of Allah, the Most Gracious, the Most Merciful. Say, "He is Allah, [who is] One, Allah, the Eternal Refuge. He neither begets nor is born, Nor is there to Him any equivalent."',
                        'reference' => 'سورة الإخلاص',
                        'benefits' => 'من قالها حين يصبح وحين يمسى كفته من كل شىء (الإخلاص والمعوذتين)',
                        'count' => 3
                    ],
                    [
                        'id' => 3,
                        'arabic_text' => ' قُلۡ أَعُوذُ بِرَبِّ ٱلۡفَلَقِ ﴿١﴾ مِن شَرِّ مَا خَلَقَ ﴿٢﴾ وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ ﴿٣﴾ وَمِن شَرِّ ٱلنَّفَّٰثَٰتِ فِي ٱلۡعُقَدِ ﴿٤﴾ وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ ﴿٥﴾',
                        'transliteration' => 'Bismillahi alrrahmani alrraheemi qul aAAoothu birabbi alfalaqi min sharri ma khalaqa wamin sharri ghasiqin itha waqaba wamin sharri alnnaffathati fee alAAuqadi wamin sharri hasidin itha hasada',
                        'translation' => 'In the name of Allah, the Most Gracious, the Most Merciful. Say, "I seek refuge in the Lord of daybreak From the evil of that which He created And from the evil of darkness when it settles And from the evil of the blowers in knots And from the evil of an envier when he envies."',
                        'reference' => 'سورة الفلق',
                        'count' => 3
                    ],
                    [
                        'id' => 4,
                        'arabic_text' => '    قُلۡ أَعُوذُ بِرَبِّ ٱلنَّاسِ ﴿١﴾ مَلِكِ ٱلنَّاسِ ﴿٢﴾ إِلَٰهِ ٱلنَّاسِ ﴿٣﴾ مِن شَرِّ ٱلۡوَسۡوَاسِ ٱلۡخَنَّاسِ ﴿٤﴾ ٱلَّذِي يُوَسۡوِسُ فِي صُدُورِ ٱلنَّاسِ ﴿٥﴾ مِنَ ٱلۡجِنَّةِ وَٱلنَّاسِ ﴿٦﴾',
                        'transliteration' => 'Bismillahi alrrahmani alrraheemi qul aAAoothu birabbi alnnasi maliki alnnasi ilahi alnnasi min sharri alwaswasi alkhannasi allathee yuwaswisu fee sudoori alnnasi mina aljinnati waalnnasi',
                        'translation' => 'In the name of Allah, the Most Gracious, the Most Merciful. Say, "I seek refuge in the Lord of mankind, The Sovereign of mankind, The God of mankind, From the evil of the retreating whisperer - Who whispers [evil] into the breasts of mankind - From among the jinn and mankind."',
                        'reference' => 'سورة الناس',
                        'count' => 3
                    ],
                    [
                        'id' => 5,
                        'arabic_text' => 'أَمْسَيْنا وَأَمْسى الملكُ لله وَالحَمدُ لله ، لا إلهَ إلاّ اللّهُ وَحدَهُ لا شَريكَ لهُ، لهُ المُلكُ ولهُ الحَمْد، وهُوَ على كلّ شَيءٍ قدير ، رَبِّ أسْأَلُكَ خَيرَ ما في هذهِ اللَّيْلَةِ وَخَيرَ ما بَعْدَها ، وَأَعوذُ بِكَ مِنْ شَرِّ ما في هذهِ اللَّيْلةِ وَشَرِّ ما بَعْدَها ، رَبِّ أَعوذُبِكَ مِنَ الْكَسَلِ وَسوءِ الْكِبَر ، رَبِّ أَعوذُ بِكَ مِنْ عَذابٍ في النّارِ وَعَذابٍ في القَبْر',
                        'transliteration' => 'Amsayna wa amsa almulku lillahi walhamdu lillahi, la ilaha illa Allahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu, wa huwa ‘ala kulli shay’in qadeer, Rabbi as’aluka khayra ma fee hathihi allaylati wa khayra ma ba‘daha, wa a‘udhu bika min sharri ma fee hathihi allaylati wa sharri ma ba‘daha, Rabbi a‘udhu bika minal-kasali wa su’il-kibari, Rabbi a‘udhu bika min ‘adhabin fin-nari wa ‘adhabin fil-qabri',
                        'translation' => 'We have reached the evening and at this very time all sovereignty belongs to Allah, and all praise is for Allah. None has the right to be worshipped except Allah, alone, without partner, to Him belongs all sovereignty and praise and He is over all things omnipotent. My Lord, I ask You for the good of this night and the good of what follows it and I take refuge in You from the evil of this night and the evil of what follows it. My Lord, I take refuge in You from laziness and senility. My Lord, I take refuge in You from torment in the Fire and punishment in the grave.',
                        'reference' => 'رواه مسلم',
                        'count' => 1
                    ],
                    [
                        'id' => 6,
                        'arabic_text' => 'اللّهمَّ أَنْتَ رَبِّي لا إلهَ إلاّ أَنْتَ ، خَلَقْتَني وَأَنا عَبْدُك ، وَأَنا عَلى عَهْدِكَ وَوَعْدِكَ ما اسْتَطَعْت ، أَعوذُبِكَ مِنْ شَرِّ ما صَنَعْت ، أَبوءُ لَكَ بِنِعْمَتِكَ عَلَيَّ وَأَبوءُ بِذَنْبي فَاغْفِرْ لي فَإِنَّهُ لا يَغْفِرُ الذُّنوبَ إِلاّ أَنْتَ',
                        'transliteration' => 'Allahumma anta Rabbi la ilaha illa anta, khalaqtani wa ana ‘abduka, wa ana ‘ala ‘ahdika wa wa‘dika ma istata‘tu, a‘udhu bika min sharri ma sana‘tu, abu’u laka bini‘matika ‘alayya wa abu’u bidhanbi faghfir li fa innahu la yaghfiru al-dhunuba illa anta',
                        'translation' => 'O Allah, You are my Lord, there is no god but You. You created me and I am Your servant, and I am upon Your covenant and promise as much as I am able. I seek refuge in You from the evil of what I have done. I acknowledge Your blessings upon me and I acknowledge my sin. So forgive me, for indeed, none forgives sins except You.',
                        'reference' => 'رواه البخاري',
                        'benefits' => 'من قالها موقنا بها حين يمسى ومات من ليلته دخل الجنة وكذلك حين يصبح',
                        'count' => 1
                    ],
                    [
                        'id' => 7,
                        'arabic_text' => 'اللّهُمَّ إِنِّي أَمسيتُ أُشْهِدُك ، وَأُشْهِدُ حَمَلَةَ عَرْشِك ، وَمَلَائِكَتَكَ ، وَجَميعَ خَلْقِك ، أَنَّكَ أَنْتَ اللهُ لا إلهَ إلاّ أَنْتَ وَحْدَكَ لا شَريكَ لَك ، وَأَنَّ مُحَمّداً عَبْدُكَ وَرَسولُك',
                        'transliteration' => 'Allahumma inni amsaytu ushhiduka, wa ushhidu hamalata ‘arshika, wa mala’ikataka, wa jami‘a khalqika, annaka anta Allahu la ilaha illa anta wahdaka la shareeka laka, wa anna Muhammadan ‘abduka wa rasuluka',
                        'translation' => 'O Allah, I have reached the evening and I call You to witness, and I call the bearers of Your throne, Your angels, and all Your creation to witness that You are Allah, there is no god but You, alone, without partner, and that Muhammad is Your servant and Messenger.',
                        'reference' => 'رواه أبو داود',
                        'benefits' => 'من قالها أعتقه الله من النار',
                        'count' => 4
                    ],
                    [
                        'id' => 8,
                        'arabic_text' => 'اللّهُمَّ ما أَمسى بي مِنْ نِعْمَةٍ أَو بِأَحَدٍ مِنْ خَلْقِك ، فَمِنْكَ وَحْدَكَ لا شريكَ لَك ، فَلَكَ الْحَمْدُ وَلَكَ الشُّكْر',
                        'transliteration' => 'Allahumma ma amsa bi min ni‘matin aw bi ahadin min khalqika, faminka wahdaka la shareeka laka, falakal-hamdu wa lakash-shukru',
                        'translation' => 'O Allah, whatever blessing has come to me or to any of Your creation this evening is from You alone, without partner, so to You is all praise and to You is all thanks.',
                        'reference' => 'رواه أبو داود',
                        'benefits' => 'من قالها حين يمسى أدى شكر يومه',
                        'count' => 1
                    ],
                    [
                        'id' => 9,
                        'arabic_text' => 'حَسْبِيَ اللّهُ لا إلهَ إلاّ هُوَ عَلَيهِ تَوَكَّلتُ وَهُوَ رَبُّ العَرْشِ العَظيم',
                        'transliteration' => 'Hasbiyallahu la ilaha illa huwa, ‘alayhi tawakkaltu wa huwa Rabbul-‘arshil-‘azim',
                        'translation' => 'Allah is sufficient for me; there is no god but He. In Him I put my trust, and He is the Lord of the Mighty Throne.',
                        'reference' => 'رواه السني',
                        'benefits' => 'من قالها كفاه الله ما أهمه من أمر الدنيا والأخرة',
                        'count' => 7
                    ],
                    [
                        'id' => 10,
                        'arabic_text' => 'بِسمِ اللهِ الذي لا يَضُرُّ مَعَ اسمِهِ شَيءٌ في الأرْضِ وَلا في السّماءِ وَهوَ السّميعُ العَليم',
                        'transliteration' => 'Bismillahilladhi la yadurru ma‘a ismihi shay’un fil-ardi wa la fis-sama’i wa huwas-Sami‘ul-‘Alim',
                        'translation' => 'In the name of Allah, with whose name nothing on earth or in the heavens can cause harm, and He is the All-Hearing, the All-Knowing.',
                        'reference' => 'رواه ابن ماجة',
                        'benefits' => 'لم يضره من الله شيء',
                        'count' => 3
                    ],
                    [
                        'id' => 11,
                        'arabic_text' => 'اللّهُمَّ بِكَ أَمْسَينا وَبِكَ أَصْبَحْنا، وَبِكَ نَحْيا وَبِكَ نَمُوتُ وَإِلَيْكَ الْمَصِيرُ',
                        'transliteration' => 'Allahumma bika amsayna wa bika asbahna, wa bika nahya wa bika namutu wa ilaykal-maseeru',
                        'translation' => 'O Allah, by You we have reached the evening and by You we have reached the morning, by You we live and by You we die, and to You is the final return.',
                        'reference' => 'رواه الترمذي',
                        'count' => 1
                    ],
                    [
                        'id' => 12,
                        'arabic_text' => 'أَمْسَيْنَا عَلَى فِطْرَةِ الإسْلاَمِ، وَعَلَى كَلِمَةِ الإِخْلاَصِ، وَعَلَى دِينِ نَبِيِّنَا مُحَمَّدٍ صَلَّى اللهُ عَلَيْهِ وَسَلَّمَ، وَعَلَى مِلَّةِ أَبِينَا إبْرَاهِيمَ حَنِيفاً مُسْلِماً وَمَا كَانَ مِنَ المُشْرِكِينَ',
                        'transliteration' => 'Amsayna ‘ala fitratil-Islam, wa ‘ala kalimatil-ikhlas, wa ‘ala deeni nabiyyina Muhammadin sallallahu ‘alayhi wa sallam, wa ‘ala millati abina Ibrahima hanifan musliman wa ma kana minal-mushrikeen',
                        'translation' => 'We have reached the evening upon the natural disposition of Islam, and upon the word of sincerity, and upon the religion of our Prophet Muhammad (peace be upon him), and upon the way of our father Abraham, inclining toward truth, a Muslim, and he was not of the polytheists.',
                        'reference' => 'رواه أحمد',
                        'count' => 1
                    ],
                    [
                        'id' => 13,
                        'arabic_text' => 'سُبْحانَ اللهِ وَبِحَمْدِهِ عَدَدَ خَلْقِه ، وَرِضا نَفْسِه ، وَزِنَةَ عَرْشِه ، وَمِدادَ كَلِماتِه',
                        'transliteration' => 'Subhanallahi wa bihamdihi ‘adada khalqihi, wa rida nafsihi, wa zinata ‘arshihi, wa midada kalimatihi',
                        'translation' => 'Glory be to Allah and praise be to Him, as much as the number of His creation, as much as pleases Him, as much as the weight of His Throne, and as much as the ink of His words.',
                        'reference' => 'رواه مسلم',
                        'count' => 3
                    ],
                    [
                        'id' => 14,
                        'arabic_text' => 'اللّهُمَّ عافِني في بَدَني ، اللّهُمَّ عافِني في سَمْعي ، اللّهُمَّ عافِني في بَصَري ، لا إلهَ إلاّ أَنْتَ',
                        'transliteration' => 'Allahumma ‘afini fi badani, Allahumma ‘afini fi sam‘i, Allahumma ‘afini fi basari, la ilaha illa anta',
                        'translation' => 'O Allah, grant me well-being in my body, O Allah, grant me well-being in my hearing, O Allah, grant me well-being in my sight. There is no god but You.',
                        'reference' => 'رواه أحمد',
                        'count' => 3
                    ],
                    [
                        'id' => 15,
                        'arabic_text' => 'اللّهُمَّ إِنّي أَعوذُ بِكَ مِنَ الْكُفر ، وَالفَقْر ، وَأَعوذُ بِكَ مِنْ عَذابِ القَبْر ، لا إلهَ إلاّ أَنْتَ',
                        'transliteration' => 'Allahumma inni a‘udhu bika minal-kufri, wal-faqri, wa a‘udhu bika min ‘adhabíl-qabri, la ilaha illa anta',
                        'translation' => 'O Allah, I seek refuge in You from disbelief and poverty, and I seek refuge in You from the punishment of the grave. There is no god but You.',
                        'reference' => 'رواه أحمد',
                        'count' => 3
                    ],
                    [
                        'id' => 16,
                        'arabic_text' => 'اللّهُمَّ إِنِّي أسْأَلُكَ العَفْوَ وَالعافِيةَ في الدُّنْيا وَالآخِرَة ، اللّهُمَّ إِنِّي أسْأَلُكَ العَفْوَ وَالعافِيةَ في ديني وَدُنْيايَ وَأهْلي وَمالي ، اللّهُمَّ اسْتُرْ عوْراتي وَآمِنْ رَوْعاتي ، اللّهُمَّ احْفَظْني مِن بَينِ يَدَيَّ وَمِن خَلْفي وَعَن يَميني وَعَن شِمالي ، وَمِن فَوْقي ، وَأَعوذُ بِعَظَمَتِكَ أَن أُغْتالَ مِن تَحْتي',
                        'transliteration' => 'Allahumma inni as’aluka al-‘afwa wal-‘afiyata fid-dunya wal-akhirah, Allahumma inni as’aluka al-‘afwa wal-‘afiyata fi deeni wa dunyaya wa ahli wa mali, Allahumma ustur ‘awrati wa amin raw‘ati, Allahumma ihfazni min bayni yadayya wa min khalfi wa ‘an yamini wa ‘an shimali, wa min fawqi, wa a‘udhu bi‘azamatika an ughtala min tahti',
                        'translation' => 'O Allah, I ask You for pardon and well-being in this world and the Hereafter. O Allah, I ask You for pardon and well-being in my religion, my worldly affairs, my family, and my wealth. O Allah, conceal my faults and calm my fears. O Allah, protect me from in front of me, from behind me, from my right, from my left, and from above me, and I seek refuge in Your greatness from being unexpectedly destroyed from beneath me.',
                        'reference' => 'رواه أبو داود',
                        'count' => 1
                    ],
                    [
                        'id' => 17,
                        'arabic_text' => 'يَا حَيُّ يَا قيُّومُ بِرَحْمَتِكَ أسْتَغِيثُ أصْلِحْ لِي شَأنِي كُلَّهُ وَلاَ تَكِلْنِي إلَى نَفْسِي طَرْفَةَ عَيْنٍ',
                        'transliteration' => 'Ya Hayyu ya Qayyumu birahmatika astagheethu aslih li sha’ni kullahu wa la takilni ila nafsi tarfata ‘aynin',
                        'translation' => 'O Ever-Living, O Sustainer, by Your mercy I seek help. Rectify for me all of my affairs and do not leave me to myself even for the blink of an eye.',
                        'reference' => 'رواه الترمذي',
                        'count' => 3
                    ],
                    [
                        'id' => 18,
                        'arabic_text' => 'اللّهُمَّ عالِمَ الغَيْبِ وَالشّهادَةِ فاطِرَ السّماواتِ وَالأرْضِ رَبَّ كلِّ شَيءٍ وَمَليكَه ، أَشْهَدُ أَنْ لا إِلهَ إِلاّ أَنْت ، أَعوذُ بِكَ مِن شَرِّ نَفْسي وَمِن شَرِّ الشَّيْطانِ وَشِرْكِهِ ، وَأَنْ أَقْتَرِفَ عَلى نَفْسي سوءاً أَوْ أَجُرَّهُ إِلى مُسْلِم',
                        'transliteration' => 'Allahumma ‘alimal-ghaybi wash-shahadati fatiras-samawati wal-ardi Rabba kulli shay’in wa malikahu, ash-hadu an la ilaha illa anta, a‘udhu bika min sharri nafsi wa min sharrish-shaytani wa shirkihi, wa an aqtarifa ‘ala nafsi su’an aw ajurrahu ila muslim',
                        'translation' => 'O Allah, Knower of the unseen and the witnessed, Creator of the heavens and the earth, Lord and Sovereign of all things, I bear witness that there is no god but You. I seek refuge in You from the evil of my soul and from the evil of Satan and his association, and from committing wrong against myself or bringing it upon a Muslim.',
                        'reference' => 'رواه الترمذي',
                        'count' => 1
                    ],
                    [
                        'id' => 19,
                        'arabic_text' => 'أَعوذُ بِكَلِماتِ اللّهِ التّامّاتِ مِنْ شَرِّ ما خَلَق',
                        'transliteration' => 'A‘udhu bikalimatillahit-tammati min sharri ma khalaq',
                        'translation' => 'I seek refuge in the perfect words of Allah from the evil of what He has created.',
                        'reference' => 'رواه مسلم',
                        'count' => 3
                    ],
                    [
                        'id' => 20,
                        'arabic_text' => 'اللَّهُمَّ صَلِّ وَسَلِّمْ وَبَارِكْ على نَبِيِّنَا مُحمَّد',
                        'transliteration' => 'Allahumma salli wa sallim wa barik ‘ala nabiyyina Muhammad',
                        'translation' => 'O Allah, send blessings, peace, and mercy upon our Prophet Muhammad.',
                        'reference' => 'رواه الطبراني',
                        'benefits' => 'من صلى على حين يصبح وحين يمسى ادركته شفاعتى يوم القيامة',
                        'count' => 10
                    ],
                    [
                        'id' => 21,
                        'arabic_text' => 'اللَّهُمَّ إِنَّا نَعُوذُ بِكَ مِنْ أَنْ نُشْرِكَ بِكَ شَيْئًا نَعْلَمُهُ ، وَنَسْتَغْفِرُكَ لِمَا لَا نَعْلَمُهُ',
                        'transliteration' => 'Allahumma inna na‘udhu bika min an nushrika bika shay’an na‘lamuhu, wa nastaghfiruka lima la na‘lamuhu',
                        'translation' => 'O Allah, we seek refuge in You from associating anything with You that we know, and we ask Your forgiveness for what we do not know.',
                        'reference' => 'رواه أحمد',
                        'count' => 3
                    ],
                    [
                        'id' => 22,
                        'arabic_text' => 'اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنْ الْهَمِّ وَالْحَزَنِ، وَأَعُوذُ بِكَ مِنْ الْعَجْزِ وَالْكَسَلِ، وَأَعُوذُ بِكَ مِنْ الْجُبْنِ وَالْبُخْلِ، وَأَعُوذُ بِكَ مِنْ غَلَبَةِ الدَّيْنِ، وَقَهْرِ الرِّجَالِ',
                        'transliteration' => 'Allahumma inni a‘udhu bika minal-hammi wal-hazan, wa a‘udhu bika minal-‘ajzi wal-kasal, wa a‘udhu bika minal-jubni wal-bukhl, wa a‘udhu bika min ghalabatid-dayn, wa qahrir-rijal',
                        'translation' => 'O Allah, I seek refuge in You from anxiety and sorrow, from weakness and laziness, from cowardice and miserliness, and from being overburdened by debt and from the oppression of men.',
                        'reference' => 'رواه أبو داود',
                        'count' => 3
                    ],
                    [
                        'id' => 23,
                        'arabic_text' => 'أسْتَغْفِرُ اللهَ العَظِيمَ الَّذِي لاَ إلَهَ إلاَّ هُوَ، الحَيُّ القَيُّومُ، وَأتُوبُ إلَيهِ',
                        'transliteration' => 'Astaghfirullahal-‘azimalladhi la ilaha illa huwa, al-Hayyul-Qayyum, wa atubu ilayhi',
                        'translation' => 'I seek forgiveness from Allah, the Most Great, there is no god but He, the Ever-Living, the Sustainer, and I repent to Him.',
                        'reference' => 'رواه الترمذي',
                        'count' => 3
                    ],
                    [
                        'id' => 24,
                        'arabic_text' => 'يَا رَبِّ , لَكَ الْحَمْدُ كَمَا يَنْبَغِي لِجَلَالِ وَجْهِكَ , وَلِعَظِيمِ سُلْطَانِكَ',
                        'transliteration' => 'Ya Rabbi, lakal-hamdu kama yanbaghi lijalali wajhika, wa li‘azimi sultanika',
                        'translation' => 'O my Lord, to You is all praise as befits the majesty of Your countenance and the greatness of Your authority.',
                        'reference' => 'رواه ابن ماجة',
                        'count' => 3
                    ],
                    [
                        'id' => 25,
                        'arabic_text' => 'لَا إلَه إلّا اللهُ وَحْدَهُ لَا شَرِيكَ لَهُ، لَهُ الْمُلْكُ وَلَهُ الْحَمْدُ وَهُوَ عَلَى كُلِّ شَيْءِ قَدِيرِ',
                        'transliteration' => 'La ilaha illallahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu wa huwa ‘ala kulli shay’in qadeer',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, and He is over all things competent.',
                        'reference' => 'رواه الترمذي',
                        'benefits' => 'كانت له عدل عشر رقاب، وكتبت له مئة حسنة، ومحيت عنه مئة سيئة، وكانت له حرزا من الشيطان',
                        'count' => 100
                    ],
                    [
                        'id' => 26,
                        'arabic_text' => 'اللَّهُمَّ أَنْتَ رَبِّي لا إِلَهَ إِلا أَنْتَ ، عَلَيْكَ تَوَكَّلْتُ ، وَأَنْتَ رَبُّ الْعَرْشِ الْعَظِيمِ , مَا شَاءَ اللَّهُ كَانَ ، وَمَا لَمْ يَشَأْ لَمْ يَكُنْ ، وَلا حَوْلَ وَلا قُوَّةَ إِلا بِاللَّهِ الْعَلِيِّ الْعَظِيمِ , أَعْلَمُ أَنَّ اللَّهَ عَلَى كُلِّ شَيْءٍ قَدِيرٌ ، وَأَنَّ اللَّهَ قَدْ أَحَاطَ بِكُلِّ شَيْءٍ عِلْمًا , اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنْ شَرِّ نَفْسِي ، وَمِنْ شَرِّ كُلِّ دَابَّةٍ أَنْتَ آخِذٌ بِنَاصِيَتِهَا ، إِنَّ رَبِّي عَلَى صِرَاطٍ مُسْتَقِيمٍ',
                        'transliteration' => 'Allahumma anta Rabbi la ilaha illa anta, ‘alayka tawakkaltu, wa anta Rabbul-‘arshil-‘azim, ma sha’allahu kana, wa ma lam yasha’ lam yakun, wa la hawla wa la quwwata illa billahil-‘Aliyyil-‘Azim, a‘lamu annallaha ‘ala kulli shay’in qadeer, wa annallaha qad ahata bikulli shay’in ‘ilma, Allahumma inni a‘udhu bika min sharri nafsi, wa min sharri kulli dabbatin anta akhidhun binasiyatiha, inna Rabbi ‘ala siratin mustaqim',
                        'translation' => 'O Allah, You are my Lord, there is no god but You. I rely upon You, and You are the Lord of the Mighty Throne. Whatever Allah wills happens, and whatever He does not will does not happen. There is no power or strength except with Allah, the Most High, the Most Great. I know that Allah is capable of all things and that Allah has encompassed everything in knowledge. O Allah, I seek refuge in You from the evil of my soul and from the evil of every creature that You seize by its forelock. Indeed, my Lord is on a straight path.',
                        'reference' => 'رواه أحمد',
                        'count' => 1
                    ],
                    [
                        'id' => 27,
                        'arabic_text' => 'سُبْحانَ اللهِ وَبِحَمْدِهِ',
                        'transliteration' => 'Subhanallahi wa bihamdihi',
                        'translation' => 'Glory be to Allah and praise be to Him.',
                        'reference' => 'رواه مسلم',
                        'benefits' => 'من قال : سبحان اللهِ وبحمدِه مائةَ مرةٍ غُفرَتْ له ذنوبُه وإنْ كانتْ مثلَ زبَدِ البحرِ',
                        'count' => 100
                    ]
                ]
            ],
            'sleep' => [
                'name' => 'Before Sleep Azkar',
                'arabic_name' => 'أذكار النوم',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'اللَّهُ لاَ إِلَهَ إِلاَّ هُوَ الْحَيُّ الْقَيُّومُ لاَ تَأْخُذُهُ سِنَةٌ وَلاَ نَوْمٌ لَّهُ مَا فِي السَّمَوَاتِ وَمَا فِي الأَرْضِ مَن ذَا الَّذِي يَشْفَعُ عِنْدَهُ إِلاَّ بِإِذْنِهِ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ وَلاَ يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلاَّ بِمَا شَاء وَسِعَ كُرْسِيُّهُ السَّمَوَاتِ وَالأَرْضَ وَلاَ يَؤُودُهُ حِفْظُهُمَا وَهُوَ الْعَلِيُّ الْعَظِيمُ',
                        'transliteration' => 'Allahu la ilaha illa huwa alhayyu alqayyumu la takhuthuhu sinatun wala nawmun lahu ma fee alssamawati wama fee alardi man tha allathee yashfaAAu AAindahu illa biithnihi yaAAlamu ma bayna aydeehim wama khalfahum wala yuheetoona bishayin min AAilmihi illa bima shaa wasiAAa kursiyyuhu alssamawati waalarda wala yaooduhu hifthuhuma wahuwa alAAaliyyu alAAatheemu',
                        'translation' => 'Allah - there is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth. Who is it that can intercede with Him except by His permission? He knows what is [presently] before them and what will be after them, and they encompass not a thing of His knowledge except for what He wills. His Kursi extends over the heavens and the earth, and their preservation tires Him not. And He is the Most High, the Most Great.',
                        'reference' => '[آية الكرسى - سورة البقرة 255]',
                        'count' => 1
                    ],
                    [
                        'id' => 2,
                        'arabic_text' => 'بِسْمِ اللهِ الرَّحْمنِ الرَّحِيم آمَنَ الرَّسُولُ بِمَا أُنزِلَ إِلَيْهِ مِن رَّبِّهِ وَالْمُؤْمِنُونَ كُلٌّ آمَنَ بِاللَّهِ وَمَلآئِكَتِهِ وَكُتُبِهِ وَرُسُلِهِ لاَ نُفَرِّقُ بَيْنَ أَحَدٍ مِّن رُّسُلِهِ وَقَالُواْ سَمِعْنَا وَأَطَعْنَا غُفْرَانَكَ رَبَّنَا وَإِلَيْكَ الْمَصِيرُ* لاَ يُكَلِّفُ اللَّهُ نَفْساً إِلاَّ وُسْعَهَا لَهَا مَا كَسَبَتْ وَعَلَيْهَا مَا اكْتَسَبَتْ رَبَّنَا لاَ تُؤَاخِذْنَا إِن نَّسِينَا أَوْ أَخْطَأْنَا رَبَّنَا وَلاَ تَحْمِلْ عَلَيْنَا إِصْراً كَمَا حَمَلْتَهُ عَلَى الَّذِينَ مِن قَبْلِنَا رَبَّنَا وَلاَ تُحَمِّلْنَا مَا لاَ طَاقَةَ لَنَا بِهِ وَاعْفُ عَنَّا وَاغْفِرْ لَنَا وَارْحَمْنَآ أَنتَ مَوْلاَنَا فَانصُرْنَا عَلَى الْقَوْمِ الْكَافِرِينَ',
                        'transliteration' => 'Bismillahi alrrahmani alrraheemi amana alrrasoolu bima onzila ilayhi min rabbihi waalmuminoona kullun amana billahi wamalaikatihi wakutubihi warusulihi la nufarriqu bayna ahadin min rusulihi waqaloo samiAAna waataAAna ghufranaka rabbana wailayka almaseeru* La yukallifu Allahu nafsan illa wusAAaha laha ma kasabat waAAalayha ma iktasabat rabbana la tuakhithna in naseena aw akhtana rabbana wala tahmil AAalayna isran kama hamaltahu AAala allatheena min qablina rabbana wala tuhammilna ma la taqata lana bihi waooAAfu AAanna waghfir lana wairhamna anta mawlana faonsurna AAala alqawmi alkafireena',
                        'translation' => 'In the name of Allah, the Most Gracious, the Most Merciful. The Messenger has believed in what was revealed to him from his Lord, and [so have] the believers. All of them have believed in Allah and His angels and His books and His messengers, [saying], "We make no distinction between any of His messengers." And they say, "We hear and we obey. [We seek] Your forgiveness, our Lord, and to You is the [final] destination." Allah does not charge a soul except [with that within] its capacity. It will have [the consequence of] what [good] it has gained, and it will bear [the consequence of] what [evil] it has earned. "Our Lord, do not impose blame upon us if we have forgotten or erred. Our Lord, and lay not upon us a burden like that which You laid upon those before us. Our Lord, and burden us not with that which we have no ability to bear. And pardon us; and forgive us; and have mercy upon us. You are our protector, so give us victory over the disbelieving people."',
                        'reference' => '[سورة البقرة 285 - 286]',
                        'count' => 1
                    ],
                    [
                        'id' => 3,
                        'arabic_text' => '    قُلۡ يَٰٓأَيُّهَا ٱلۡكَٰفِرُونَ ﴿١﴾ لَآ أَعۡبُدُ مَا تَعۡبُدُونَ ﴿٢﴾ وَلَآ أَنتُمۡ عَٰبِدُونَ مَآ أَعۡبُدُ ﴿٣﴾ وَلَآ أَنَا۠ عَابِدٞ مَّا عَبَدتُّمۡ ﴿٤﴾ وَلَآ أَنتُمۡ عَٰبِدُونَ مَآ أَعۡبُدُ ﴿٥﴾ لَكُمۡ دِينُكُمۡ وَلِيَ دِينِ ﴿٦﴾',
                        'transliteration' => 'Bismillahi alrrahmani alrraheemi qul ya ayyuha alkafiroona la aAAbudu ma taAAbudoona wala antum AAabidoona ma aAAbudu wala ana AAabidun ma AAabadtum wala antum AAabidoona ma aAAbudu lakum deenukum waliya deeni',
                        'translation' => 'In the name of Allah, the Most Gracious, the Most Merciful. Say, "O disbelievers, I do not worship what you worship. Nor are you worshippers of what I worship. Nor will I be a worshipper of what you worship. Nor will you be worshippers of what I worship. For you is your religion, and for me is my religion."',
                        'reference' => 'سورة الكافرون',
                        'count' => 1
                    ],
                    [
                        'id' => 4,
                        'arabic_text' => 'بِاسْمِكَ رَبِّي وَضَعْتُ جَنْبِي، وَبِكَ أَرْفَعُهُ، فَإِن أَمْسَكْتَ نَفْسِي فارْحَمْهَا، وَإِنْ أَرْسَلْتَهَا فَاحْفَظْهَا، بِمَا تَحْفَظُ بِهِ عِبَادَكَ الصَّالِحِينَ',
                        'transliteration' => 'Bismika Rabbi wada‘tu janbi, wa bika arfa‘uhu, fa in amsakta nafsi farhamha, wa in arsaltaha fahfazha, bima tahfazu bihi ‘ibadakas-saliheen',
                        'translation' => 'In Your name, my Lord, I lay my side, and in Your name, I raise it. If You should take my soul, then have mercy on it, and if You should return it, then protect it with that which You protect Your righteous servants.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 5,
                        'arabic_text' => 'اللَّهُمَّ إِنَّكَ خَلَقْتَ نَفْسِي وَأَنْتَ تَوَفَّاهَا، لَكَ مَمَاتُهَا وَمَحْياهَا، إِنْ أَحْيَيْتَهَا فَاحْفَظْهَا، وَإِنْ أَمَتَّهَا فَاغْفِرْ لَهَا. اللَّهُمَّ إِنِّي أَسْأَلُكَ العَافِيَةَ',
                        'transliteration' => 'Allahumma innaka khalaqta nafsi wa anta tawaffaha, laka mamatuha wa mahyaha, in ahyaytaha fahfazha, wa in amattaha faghfir laha. Allahumma inni as’alukal-‘afiyata',
                        'translation' => 'O Allah, indeed You have created my soul, and You will take it. To You belongs its death and its life. If You keep it alive, then protect it, and if You cause it to die, then forgive it. O Allah, I ask You for well-being.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 6,
                        'arabic_text' => 'اللَّهُمَّ قِنِي عَذَابَكَ يَوْمَ تَبْعَثُ عِبَادَكَ',
                        'transliteration' => 'Allahumma qini ‘adhabaka yawma tab‘athu ‘ibadaka',
                        'translation' => 'O Allah, protect me from Your punishment on the day You resurrect Your servants.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 7,
                        'arabic_text' => 'بِاسْمِكَ اللَّهُمَّ أَمُوتُ وَأَحْيَا',
                        'transliteration' => 'Bismika Allahumma amutu wa ahya',
                        'translation' => 'In Your name, O Allah, I die and I live.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 8,
                        'arabic_text' => 'سُبْحَانَ اللَّهِ (ثلاثاً وثلاثين) وَالْحَمْدُ لِلَّهِ (ثلاثاً وثلاثين) وَاللَّهُ أَكْبَرُ (ثلاثًا وثلاثين)',
                        'transliteration' => 'Subhanallah (33 times), Alhamdulillah (33 times), Allahu Akbar (33 times)',
                        'translation' => 'Glory be to Allah (33 times), Praise be to Allah (33 times), Allah is the Greatest (33 times).',
                        'reference' => null,
                        'count' => 33
                    ],
                    [
                        'id' => 9,
                        'arabic_text' => 'اللَّهُمَّ رَبَّ السَّمَوَاتِ السَّبْعِ وَرَبَّ الأَرْضِ، وَرَبَّ الْعَرْشِ الْعَظِيمِ، رَبَّنَا وَرَبَّ كُلِّ شَيْءٍ، فَالِقَ الْحَبِّ وَالنَّوَى، وَمُنْزِلَ التَّوْرَاةِ وَالْإِنْجِيلِ، وَالْفُرْقَانِ، أَعُوذُ بِكَ مِنْ شَرِّ كُلِّ شَيْءٍ أَنْتَ آخِذٌ بِنَاصِيَتِهِ. اللَّهُمَّ أَنْتَ الأَوَّلُ فَلَيْسَ قَبْلَكَ شَيْءٌ، وَأَنْتَ الآخِرُ فَلَيسَ بَعْدَكَ شَيْءٌ، وَأَنْتَ الظَّاهِرُ فَلَيسَ فَوْقَكَ شَيْءٌ، وَأَنْتَ الْبَاطِنُ فَلَيسَ دُونَكَ شَيْءٌ، اقْضِ عَنَّا الدَّيْنَ وَأَغْنِنَا مِنَ الْفَقْرِ',
                        'transliteration' => 'Allahumma Rabba alssamawati alssab‘i wa Rabba alardi, wa Rabba al‘arshil-‘azimi, Rabbana wa Rabba kulli shay’in, faliqa alhabbi wannawa, wa munzila attawrati wal-injeeli, wal-furqani, a‘udhu bika min sharri kulli shay’in anta akhidhun binasiyatihi. Allahumma anta al-awwalu falaysa qablaka shay’un, wa anta al-akhiru falaysa ba‘daka shay’un, wa anta alzzahiru falaysa fawqaka shay’un, wa anta albatinu falaysa doonaka shay’un, iqdi ‘anna aldayna wa aghnina minal-faqri',
                        'translation' => 'O Allah, Lord of the seven heavens and Lord of the earth, and Lord of the Great Throne, our Lord and the Lord of everything, the Splitter of the grain and the date seed, the Revealer of the Torah, the Gospel, and the Criterion, I seek refuge in You from the evil of everything You seize by its forelock. O Allah, You are the First, so there is nothing before You, and You are the Last, so there is nothing after You, and You are the Manifest, so there is nothing above You, and You are the Hidden, so there is nothing below You. Settle our debt for us and spare us from poverty.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 10,
                        'arabic_text' => 'الْحَمْدُ لِلَّهِ الَّذِي أَطْعَمَنَا وَسَقَانَا، وَكَفَانَا، وَآوَانَا، فَكَمْ مِمَّنْ لاَ كَافِيَ لَهُ وَلاَ مُؤْوِيَ',
                        'transliteration' => 'Alhamdu lillahi allathee at‘amana wa saqana, wa kafana, wa awana, fakam mimman la kafiya lahu wala mu’wiya',
                        'translation' => 'Praise be to Allah who has fed us and given us drink, and sufficed us, and sheltered us. How many are those who have neither a sustainer nor a shelterer!',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 11,
                        'arabic_text' => 'اللَّهُمَّ عَالِمَ الغَيْبِ وَالشَّهَادَةِ فَاطِرَ السَّمَوَاتِ وَالْأَرْضِ، رَبَّ كُلِّ شَيْءٍ وَمَلِيكَهُ، أَشْهَدُ أَنْ لاَ إِلَهَ إِلاَّ أَنْتَ، أَعُوذُ بِكَ مِنْ شَرِّ نَفْسِي، وَمِنْ شَرِّ الشَّيْطانِ وَشِرْكِهِ، وَأَنْ أَقْتَرِفَ عَلَى نَفْسِي سُوءاً، أَوْ أَجُرَّهُ إِلَى مُسْلِمٍ',
                        'transliteration' => 'Allahumma ‘alimal-ghaybi wash-shahadati fatiras-samawati wal-ardi Rabba kulli shay’in wa malikahu, ash-hadu an la ilaha illa anta, a‘udhu bika min sharri nafsi wa min sharrish-shaytani wa shirkihi, wa an aqtarifa ‘ala nafsi su’an aw ajurrahu ila muslim',
                        'translation' => 'O Allah, Knower of the unseen and the witnessed, Creator of the heavens and the earth, Lord and Sovereign of all things, I bear witness that there is no god but You. I seek refuge in You from the evil of my soul and from the evil of Satan and his association, and from committing wrong against myself or bringing it upon a Muslim.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 12,
                        'arabic_text' => 'اللَّهُمَّ أَسْلَمْتُ نَفْسِي إِلَيْكَ، وَفَوَّضْتُ أَمْرِي إِلَيْكَ، وَوَجَّهْتُ وَجْهِي إِلَيْكَ، وَأَلْجَأْتُ ظَهْرِي إِلَيْكَ، رَغْبَةً وَرَهْبَةً إِلَيْكَ، لاَ مَلْجَأَ وَلاَ مَنْجَا مِنْكَ إِلاَّ إِلَيْكَ، آمَنْتُ بِكِتَابِكَ الَّذِي أَنْزَلْتَ، وَبِنَبِيِّكَ الَّذِي أَرْسَلْتَ',
                        'transliteration' => 'Allahumma aslamtu nafsi ilayka, wa fawwadtu amri ilayka, wa wajjahtu wajhi ilayka, wa alja’tu zahri ilayka, raghbatan wa rahbatan ilayka, la malja’a wala manja minka illa ilayka, amantu bikitabika allathee anzalta, wa binabiyyika allathee arsalta',
                        'translation' => 'O Allah, I have submitted myself to You, and I have entrusted my affairs to You, and I have directed my face to You, and I have relied upon You, out of desire and fear of You. There is no refuge and no escape from You except to You. I believe in Your Book which You have revealed and in Your Prophet whom You have sent.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 13,
                        'arabic_text' => 'اللَّهمَّ إِنِّي أَعُوُذُ بِكَ مِنَ الْبرَصِ، وَالجُنُونِ، والجُذَامِ، وسّيءِ الأَسْقامِ',
                        'transliteration' => 'Allahumma inni a‘udhu bika minal-barasi, wal-junooni, wal-judhami, wa sayyi’il-asqami',
                        'translation' => 'O Allah, I seek refuge in You from leprosy, insanity, mutilation, and from evil diseases.',
                        'reference' => 'رواه أنس بن مالك',
                        'count' => 1
                    ]
                ]
            ],
            'after-prayer' => [
                'name' => 'After Prayer Azkar',
                'arabic_name' => 'أذكار بعد الصلاة',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'أَسْتَغْفِرُ الله، أَسْتَغْفِرُ الله، أَسْتَغْفِرُ الله. اللّهُمَّ أَنْتَ السَّلامُ، وَمِنْكَ السَّلام، تَبارَكْتَ يا ذا الجَـلالِ وَالإِكْرام',
                        'transliteration' => 'Astaghfirullah, Astaghfirullah, Astaghfirullah. Allahumma anta as-Salam, wa minka as-Salam, tabarakta ya dhal-Jalali wal-Ikram',
                        'translation' => 'I seek forgiveness from Allah, I seek forgiveness from Allah, I seek forgiveness from Allah. O Allah, You are Peace, and from You is peace, blessed are You, O Possessor of Majesty and Honor.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 2,
                        'arabic_text' => 'لا إلهَ إلاّ اللّهُ وحدَهُ لا شريكَ لهُ، لهُ المُـلْكُ ولهُ الحَمْد، وهوَ على كلّ شَيءٍ قَدير، اللّهُـمَّ لا مانِعَ لِما أَعْطَـيْت، وَلا مُعْطِـيَ لِما مَنَـعْت، وَلا يَنْفَـعُ ذا الجَـدِّ مِنْـكَ الجَـد',
                        'transliteration' => 'La ilaha illa Allahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu, wa huwa ‘ala kulli shay’in qadeer, Allahumma la mani‘a lima a‘tayta, wa la mu‘tiya lima mana‘ta, wa la yanfa‘u dhal-jaddi minka al-jaddu',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, and He is over all things competent. O Allah, there is no preventer of what You give, and no giver of what You prevent, and the might of the mighty does not avail against You.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 3,
                        'arabic_text' => 'لا إلهَ إلاّ اللّه, وحدَهُ لا شريكَ لهُ، لهُ الملكُ ولهُ الحَمد، وهوَ على كلّ شيءٍ قدير، لا حَـوْلَ وَلا قـوَّةَ إِلاّ بِاللهِ، لا إلهَ إلاّ اللّـه، وَلا نَعْـبُـدُ إِلاّ إيّـاه, لَهُ النِّعْـمَةُ وَلَهُ الفَضْل وَلَهُ الثَّـناءُ الحَـسَن، لا إلهَ إلاّ اللّهُ مخْلِصـينَ لَـهُ الدِّينَ وَلَوْ كَـرِهَ الكـافِرون',
                        'transliteration' => 'La ilaha illa Allah, wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu, wa huwa ‘ala kulli shay’in qadeer, la hawla wa la quwwata illa billahi, la ilaha illa Allah, wa la na‘budu illa iyyahu, lahun-ni‘matu wa lahul-fadlu wa lahuth-thana’ul-hasan, la ilaha illa Allahu mukhliseena lahud-deena wa law kariha al-kafiroon',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, and He is over all things competent. There is no power and no strength except with Allah. There is no god but Allah, and we worship none but Him. To Him belongs the blessing, and to Him belongs the favor, and to Him belongs the good praise. There is no god but Allah, sincerely dedicating our religion to Him, even if the disbelievers dislike it.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 4,
                        'arabic_text' => 'سُـبْحانَ اللهِ، والحَمْـدُ لله، واللهُ أكْـبَر',
                        'transliteration' => 'Subhanallah (33 times), Alhamdulillah (33 times), Allahu Akbar (33 times)',
                        'translation' => 'Glory be to Allah (33 times), Praise be to Allah (33 times), Allah is the Greatest (33 times).',
                        'reference' => null,
                        'count' => 33
                    ],
                    [
                        'id' => 5,
                        'arabic_text' => 'لا إلهَ إلاّ اللّهُ وَحْـدَهُ لا شريكَ لهُ، لهُ الملكُ ولهُ الحَمْد، وهُوَ على كُلّ شَيءٍ قَـدير',
                        'transliteration' => 'La ilaha illa Allahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu, wa huwa ‘ala kulli shay’in qadeer',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, and He is over all things competent.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 6,
                        'arabic_text' => 'بِسْمِ اللهِ الرَّحْمنِ الرَّحِيم قُلْ هُوَ ٱللَّهُ أَحَدٌ، ٱللَّهُ ٱلصَّمَدُ، لَمْ يَلِدْ وَلَمْ يُولَدْ، وَلَمْ يَكُن لَّهُۥ كُفُوًا أَحَدٌۢ. بِسْمِ اللهِ الرَّحْمنِ الرَّحِيم قُلْ أَعُوذُ بِرَبِّ ٱلْفَلَقِ، مِن شَرِّ مَا خَلَقَ، وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ، وَمِن شَرِّ ٱلنَّفَّٰثَٰتِ فِى ٱلْعُقَدِ، وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ. بِسْمِ اللهِ الرَّحْمنِ الرَّحِيم قُلْ أَعُوذُ بِرَبِّ ٱلنَّاسِ، مَلِكِ ٱلنَّاسِ، إِلَٰهِ ٱلنَّاسِ، مِن شَرِّ ٱلْوَسْوَاسِ ٱلْخَنَّاسِ، ٱلَّذِى يُوَسْوِسُ فِى صُدُورِ ٱلنَّاسِ، مِنَ ٱلْجِنَّةِ وَٱلنَّاسِ',
                        'transliteration' => 'Bismillahi alrrahmani alrraheemi qul huwa Allahu ahadun, Allahu alssamadu, lam yalid walam yooladu, walam yakun lahu kufuwan ahadun. Bismillahi alrrahmani alrraheemi qul aAAoothu birabbi alfalaqi, min sharri ma khalaqa, wamin sharri ghasiqin itha waqaba, wamin sharri alnnaffathati fee alAAuqadi, wamin sharri hasidin itha hasada. Bismillahi alrrahmani alrraheemi qul aAAoothu birabbi alnnasi, maliki alnnasi, ilahi alnnasi, min sharri alwaswasi alkhannasi, allathee yuwaswisu fee sudoori alnnasi, mina aljinnati waalnnasi',
                        'translation' => 'In the name of Allah, the Most Gracious, the Most Merciful. Say, "He is Allah, [who is] One, Allah, the Eternal Refuge. He neither begets nor is born, Nor is there to Him any equivalent." In the name of Allah, the Most Gracious, the Most Merciful. Say, "I seek refuge in the Lord of daybreak From the evil of that which He created And from the evil of darkness when it settles And from the evil of the blowers in knots And from the evil of an envier when he envies." In the name of Allah, the Most Gracious, the Most Merciful. Say, "I seek refuge in the Lord of mankind, The Sovereign of mankind, The God of mankind, From the evil of the retreating whisperer - Who whispers [evil] into the breasts of mankind - From among the jinn and mankind."',
                        'reference' => null,
                        'benefits' => 'ثلاث مرات بعد صلاتي الفجر والمغرب. ومرة بعد الصلوات الأخرى',
                        'count' => 3
                    ],
                    [
                        'id' => 7,
                        'arabic_text' => 'أَعُوذُ بِاللهِ مِنْ الشَّيْطَانِ الرَّجِيمِ اللّهُ لاَ إِلَـهَ إِلاَّ هُوَ الْحَيُّ الْقَيُّومُ لاَ تَأْخُذُهُ سِنَةٌ وَلاَ نَوْمٌ لَّهُ مَا فِي السَّمَاوَاتِ وَمَا فِي الأَرْضِ مَن ذَا الَّذِي يَشْفَعُ عِنْدَهُ إِلاَّ بِإِذْنِهِ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ وَلاَ يُحِيطُونَ بِشَيْءٍ مِّنْ عِلْمِهِ إِلاَّ بِمَا شَاء وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالأَرْضَ وَلاَ يَؤُودُهُ حِفْظُهُمَا وَهُوَ الْعَلِيُّ الْعَظِيمُ',
                        'transliteration' => 'A‘udhu billahi minash-shaytanir-rajeem. Allahu la ilaha illa huwa alhayyu alqayyumu la takhuthuhu sinatun wala nawmun lahu ma fee alssamawati wama fee alardi man tha allathee yashfaAAu AAindahu illa biithnihi yaAAlamu ma bayna aydeehim wama khalfahum wala yuheetoona bishayin min AAilmihi illa bima shaa wasiAAa kursiyyuhu alssamawati waalarda wala yaooduhu hifthuhuma wahuwa alAAaliyyu alAAatheemu',
                        'translation' => 'I seek refuge in Allah from the accursed Satan. Allah - there is no deity except Him, the Ever-Living, the Sustainer of [all] existence. Neither drowsiness overtakes Him nor sleep. To Him belongs whatever is in the heavens and whatever is on the earth. Who is it that can intercede with Him except by His permission? He knows what is [presently] before them and what will be after them, and they encompass not a thing of His knowledge except for what He wills. His Kursi extends over the heavens and the earth, and their preservation tires Him not. And He is the Most High, the Most Great.',
                        'reference' => '[آية الكرسى - البقرة 255]',
                        'count' => 1
                    ],
                    [
                        'id' => 8,
                        'arabic_text' => 'لا إلهَ إلاّ اللّهُ وحْـدَهُ لا شريكَ لهُ، لهُ المُلكُ ولهُ الحَمْد، يُحيـي وَيُمـيتُ وهُوَ على كُلّ شيءٍ قدير',
                        'transliteration' => 'La ilaha illa Allahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu, yuhyi wa yumeetu wa huwa ‘ala kulli shay’in qadeer',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, He gives life and causes death, and He is over all things competent.',
                        'reference' => null,
                        'benefits' => 'عَشْر مَرّات بَعْدَ المَغْرِب وَالصّـبْح',
                        'count' => 10
                    ],
                    [
                        'id' => 9,
                        'arabic_text' => 'اللّهُـمَّ إِنِّـي أَسْأَلُـكَ عِلْمـاً نافِعـاً وَرِزْقـاً طَيِّـباً، وَعَمَـلاً مُتَقَـبَّلاً',
                        'transliteration' => 'Allahumma inni as’aluka ‘ilman nafi‘an wa rizqan tayyiban, wa ‘amalan mutaqabbalan',
                        'translation' => 'O Allah, I ask You for beneficial knowledge, good provision, and accepted deeds.',
                        'reference' => null,
                        'benefits' => 'بَعْد السّلامِ من صَلاةِ الفَجْر',
                        'count' => 1
                    ],
                    [
                        'id' => 10,
                        'arabic_text' => 'اللَّهُمَّ أَجِرْنِي مِنْ النَّار',
                        'transliteration' => 'Allahumma ajirni minan-nar',
                        'translation' => 'O Allah, save me from the Fire.',
                        'reference' => null,
                        'benefits' => 'بعد صلاة الصبح والمغرب',
                        'count' => 7
                    ],
                    [
                        'id' => 11,
                        'arabic_text' => 'اللَّهُمَّ أَعِنِّي عَلَى ذِكْرِك Hawkins يَوْمَ الجُمُعَةِ وَيَوْمَ السَّبْتِ',
                        'transliteration' => 'Allahumma a‘inni ‘ala dhikrika wa shukrika wa husni ‘ibadatik',
                        'translation' => 'O Allah, help me to remember You, to thank You, and to worship You in the best manner.',
                        'reference' => null,
                        'count' => 1
                    ]
                ]
            ],
            'wake-up' => [
                'name' => 'Waking Up Azkar',
                'arabic_name' => 'أذكار الاستيقاظ',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'الْحَمْدُ للهِ الَّذِي أَحْيَانَا بَعْدَ مَا أَمَاتَنَا وَإِلَيْهِ النُّشُورُ',
                        'transliteration' => 'Alhamdu lillaahil-lathee ahyaanaa ba\'da maa amaatanaa wa ilayhin-nushoor',
                        'translation' => 'All praise is for Allah who gave us life after having taken it from us and unto Him is the resurrection.',
                        'reference' => 'Bukhari',
                        'count' => 1
                    ],
                    [
                        'id' => 2,
                        'arabic_text' => 'الحمدُ للهِ الذي عافاني في جَسَدي وَرَدّ عَليّ روحي وَأَذِنَ لي بِذِكْرِه',
                        'transliteration' => 'Alhamdu lillahilladhi ‘aafani fi jasadi wa radd ‘alayya roohi wa adhina li bidhikrihi',
                        'translation' => 'Praise be to Allah who has granted me well-being in my body, returned my soul to me, and permitted me to remember Him.',
                        'reference' => null,
                        'count' => 1
                    ],
                    [
                        'id' => 3,
                        'arabic_text' => 'لا إلهَ إلاّ اللّهُ وَحْـدَهُ لا شَـريكَ له، لهُ المُلـكُ ولهُ الحَمـد، وهوَ على كلّ شيءٍ قدير، سُـبْحانَ اللهِ، والحمْـدُ لله، ولا إلهَ إلاّ اللهُ واللهُ أكبَر، وَلا حَولَ وَلا قوّة إلاّ باللّهِ العليّ العظيم. رَبِّ اغْفرْ لي',
                        'transliteration' => 'La ilaha illa Allahu wahdahu la shareeka lahu, lahul-mulku wa lahul-hamdu, wa huwa ‘ala kulli shay’in qadeer, Subhanallah, walhamdu lillah, wa la ilaha illa Allahu wallahu Akbar, wa la hawla wa la quwwata illa billahil-‘Aliyyil-‘Azim. Rabbi ghfir li',
                        'translation' => 'There is no god but Allah, alone, without partner. To Him belongs the dominion, and to Him belongs all praise, and He is over all things competent. Glory be to Allah, praise be to Allah, there is no god but Allah, and Allah is the Greatest, and there is no power and no strength except with Allah, the Most High, the Most Great. My Lord, forgive me.',
                        'reference' => null,
                        'count' => 1
                    ]
                ]
            ],
            'home-entry' => [
                'name' => 'Entering Home Azkar',
                'arabic_name' => 'أذكار دخول المنزل',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'بِسْمِ اللَّهِ وَلَجْنَا، وَبِسْمِ اللَّهِ خَرَجْنَا، وَعَلَى رَبِّنَا تَوَكَّلْنَا',
                        'transliteration' => 'Bismillaahi walajnaa, wa bismillaahi kharajnaa, wa \'alaa Rabbinaa tawakkalnaa',
                        'translation' => 'In the name of Allah we enter, in the name of Allah we leave, and upon our Lord we depend.',
                        'reference' => 'Abu Dawud',
                        'count' => 1
                    ]
                ]
            ],
            'home-exit' => [
                'name' => 'Leaving Home Azkar',
                'arabic_name' => 'أذكار الخروج من المنزل',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'بِسْمِ اللَّهِ، تَوَكَّلْتُ عَلَى اللَّهِ، وَلَا حَوْلَ وَلَا قُوَّةَ إِلَّا بِاللَّهِ',
                        'transliteration' => 'Bismillaah, tawakkaltu \'alallaah, wa laa hawla wa laa quwwata illaa billaah',
                        'translation' => 'In the name of Allah, I place my trust in Allah, and there is no might nor power except with Allah.',
                        'reference' => 'Abu Dawud, At-Tirmidhi',
                        'count' => 1
                    ]
                ]
            ],
            'food' => [
                'name' => 'Food & Drink Azkar',
                'arabic_name' => 'أذكار الطعام والشراب',
                'azkar' => [
                    [
                        'id' => 1,
                        'arabic_text' => 'بِسْمِ اللَّهِ',
                        'transliteration' => 'Bismillaah',
                        'translation' => 'In the name of Allah.',
                        'reference' => 'Abu Dawud, At-Tirmidhi',
                        'count' => 1
                    ],
                    [
                        'id' => 2,
                        'arabic_text' => 'الْحَمْدُ لِلَّهِ الَّذِي أَطْعَمَنِي هَذَا، وَرَزَقَنِيهِ، مِنْ غَيْرِ حَوْلٍ مِنِّي وَلاَ قُوَّةٍ',
                        'transliteration' => 'Alhamdu lillaahil-lathee at\'amanee haathaa, wa razaqaneehi, min ghayri hawlin minnee wa laa quwwatin',
                        'translation' => 'All praise is for Allah who fed me this and provided it for me without any might nor power from myself.',
                        'reference' => 'Abu Dawud, At-Tirmidhi, Ibn Majah',
                        'count' => 1
                    ]
                ]
            ]
        ];
        return $fallbackData[$slug] ?? null;
    }
}
