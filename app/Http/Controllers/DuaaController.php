<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DuaaController extends Controller
{
    public function index()
    {
        $categories = $this->getDuaaCategories();
        return view('duaa.index', compact('categories'));
    }

    public function category($slug)
    {
        $categories = $this->getDuaaCategories();

        $category = null;
        foreach ($categories as $cat) {
            if ($cat['slug'] === $slug) {
                $category = $cat;
                break;
            }
        }

        if (!$category) {
            return redirect()->route('duaa.index')
                ->with('error', 'الفئة غير موجودة.');
        }

        $duaas = $this->getDuaasByCategory($slug);

        return view('duaa.category', compact('category', 'duaas'));
    }

    public function show($slug, $id)
    {
        $categories = $this->getDuaaCategories();

        $category = null;
        foreach ($categories as $cat) {
            if ($cat['slug'] === $slug) {
                $category = $cat;
                break;
            }
        }

        if (!$category) {
            return redirect()->route('duaa.index')
                ->with('error', 'الفئة غير موجودة.');
        }

        $duaas = $this->getDuaasByCategory($slug);

        $duaa = null;
        foreach ($duaas as $d) {
            if ($d['id'] == $id) {
                $duaa = $d;
                break;
            }
        }

        if (!$duaa) {
            return redirect()->route('duaa.category', $slug)
                ->with('error', 'الدعاء غير موجود.');
        }

        return view('duaa.show', compact('category', 'duaa'));
    }

    private function getDuaaCategories()
    {
        return [
            [
                'id' => 1,
                'slug' => 'prophetic-duaas',
                'name' => 'أدعية النبوة',
                'name_arabic' => 'أدعية النبوة',
                'description' => 'أدعية أصيلة من النبي محمد ﷺ',
                'description_arabic' => 'أدعية أصيلة من النبي محمد ﷺ',
                'icon' => 'prophet',
                'count' => count($this->getDuaasByCategory('prophetic-duaas'))
            ],
            [
                'id' => 2,
                'slug' => 'quranic-duaas',
                'name' => 'أدعية القرآن',
                'name_arabic' => 'أدعية القرآن',
                'description' => 'أدعية مذكورة في القرآن الكريم',
                'description_arabic' => 'أدعية مذكورة في القرآن الكريم',
                'icon' => 'quran',
                'count' => count($this->getDuaasByCategory('quranic-duaas'))
            ],
            [
                'id' => 3,
                'slug' => 'protection-duaas',
                'name' => 'أدعية الحماية',
                'name_arabic' => 'أدعية الحماية',
                'description' => 'أدعية للحماية والأمان',
                'description_arabic' => 'أدعية للحماية والأمان',
                'icon' => 'shield',
                'count' => count($this->getDuaasByCategory('protection-duaas'))
            ],
            [
                'id' => 4,
                'slug' => 'daily-life',
                'name' => 'أدعية الحياة اليومية',
                'name_arabic' => 'أدعية الحياة اليومية',
                'description' => 'أدعية للمواقف اليومية المتنوعة',
                'description_arabic' => 'أدعية للمواقف اليومية المتنوعة',
                'icon' => 'daily',
                'count' => count($this->getDuaasByCategory('daily-life'))
            ],
            [
                'id' => 5,
                'slug' => 'health-duaas',
                'name' => 'أدعية الصحة والشفاء',
                'name_arabic' => 'أدعية الصحة والشفاء',
                'description' => 'أدعية للصحة والشفاء',
                'description_arabic' => 'أدعية للصحة والشفاء',
                'icon' => 'health',
                'count' => count($this->getDuaasByCategory('health-duaas'))
            ],
            [
                'id' => 6,
                'slug' => 'relief-duaas',
                'name' => 'أدعية الراحة والشدائد',
                'name_arabic' => 'أدعية الراحة والشدائد',
                'description' => 'أدعية لأوقات الصعوبة',
                'description_arabic' => 'أدعية لأوقات الصعوبة',
                'icon' => 'relief',
                'count' => count($this->getDuaasByCategory('relief-duaas'))
            ],
        ];
    }

    private function getDuaasByCategory($slug)
    {
        $allDuaas = [
            'prophetic-duaas' => $this->getPropheticDuaas(),
            'quranic-duaas' => $this->getQuranicDuaas(),
            'protection-duaas' => $this->getProtectionDuaas(),
            'daily-life' => $this->getDailyLifeDuaas(),
            'health-duaas' => $this->getHealthDuaas(),
            'relief-duaas' => $this->getReliefDuaas(),
        ];

        return $allDuaas[$slug] ?? [];
    }

    private function getPropheticDuaas()
    {
        return [
            [
                'id' => 1,
                'title' => 'دعاء الإرشاد',
                'title_arabic' => 'دعاء الإرشاد',
                'arabic' => 'اللَّهُمَّ إِنِّي أَسْأَلُكَ الْهُدَى، وَالتُّقَى، وَالْعَفَافَ، وَالْغِنَى',
                'transliteration' => 'Allahumma inni as\'alukal-huda, wat-tuqa, wal-\'afafa, wal-ghina',
                'translation' => 'يا الله، أسألك الهداية والتقوى والعفاف والغنى.',
                'benefits' => 'هذا الدعاء يطلب الهداية في جميع جوانب الحياة، والتقوى في الأعمال، والعفة في الخلق، والرضا بما رزق الله.',
                'reference' => 'مسلم',
                'count' => 1
            ],
            [
                'id' => 2,
                'title' => 'طلب الحماية صباحًا ومساءً',
                'title_arabic' => 'طلب الحماية صباحًا ومساءً',
                'arabic' => 'اللَّهُمَّ عَالِمَ الْغَيْبِ وَالشَّهَادَةِ فَاطِرَ السَّمَاوَاتِ وَالْأَرْضِ رَبَّ كُلِّ شَيْءٍ وَمَلِيكَهُ أَشْهَدُ أَنْ لَا إِلَهَ إِلَّا أَنْتَ أَعُوذُ بِكَ مِنْ شَرِّ نَفْسِي وَشَرِّ الشَّيْطَانِ وَشِرْكِهِ وَأَنْ أَقْتَرِفَ عَلَى نَفْسِي سُوءًا أَوْ أَجُرَّهُ إِلَى مُسْلِمٍ',
                'transliteration' => 'Allahumma \'Aalimal-ghaybi wash-shahaadati faatiras-samaawaati wal-ardi Rabba kulli shay\'in wa maleekahu, ash-hadu an laa ilaaha illaa anta, a\'oodhu bika min sharri nafsee wa min sharrish-shaytaani wa shirkihi, wa an aqtarifa \'alaa nafsee soo\'an aw ajurrahu ilaa Muslim',
                'translation' => 'يا الله، عالم الغيب والشهادة، خالق السماوات والأرض، رب كل شيء ومليكه، أشهد أن لا إله إلا أنت، أعوذ بك من شر نفسي ومن شر الشيطان وشركه، ومن أن أقترف على نفسي سوءًا أو أجره إلى مسلم.',
                'benefits' => 'هذا الدعاء الشامل يطلب الحماية من شر النفس، الشيطان، والضرر للنفس أو المسلمين.',
                'reference' => 'أبو داود والترمذي',
                'count' => 1
            ],
            [
                'id' => 3,
                'title' => 'دعاء دخول السوق',
                'title_arabic' => 'دعاء دخول السوق',
                'arabic' => 'لَا إِلَهَ إِلَّا اللهُ وَحْدَهُ لَا شَرِيكَ لَهُ، لَهُ الْمُلْكُ وَلَهُ الْحَمْدُ، يُحْيِي وَيُمِيتُ وَهُوَ حَيٌّ لَا يَمُوتُ، بِيَدِهِ الْخَيْرُ، وَهُوَ عَلَى كُلِّ شَيْءٍ قَدِيرٌ',
                'transliteration' => 'Laa ilaaha illallaahu wahdahu laa shareeka lah, lahul-mulku wa lahul-hamdu, yuhyee wa yumeetu wa huwa hayyun laa yamoot, biyadihil-khayru wa huwa \'alaa kulli shay\'in qadeer',
                'translation' => 'لا إله إلا الله وحده لا شريك له، له الملك وله الحمد، يحيي ويميت وهو حي لا يموت، بيده الخير وهو على كل شيء قدير.',
                'benefits' => 'قال النبي ﷺ من قرأ هذا الدعاء عند دخول السوق سُجلت له مليون حسنة، وحُطت عنه مليون سيئة، ورفع له مليون درجة.',
                'reference' => 'الترمذي',
                'count' => 1
            ],
            [
                'id' => 4,
                'title' => 'دعاء الاستخارة',
                'title_arabic' => 'دعاء الاستخارة',
                'arabic' => 'اللَّهُمَّ إِنِّي أَسْتَخِيرُكَ بِعِلْمِكَ وَأَسْتَقْدِرُكَ بِقُدْرَتِكَ، وَأَسْأَلُكَ مِنْ فَضْلِكَ الْعَظِيمِ، فَإِنَّكَ تَقْدِرُ وَلَا أَقْدِرُ وَتَعْلَمُ وَلَا أَعْلَمُ وَأَنْتَ عَلَّامُ الْغُيُوبِ، اللَّهُمَّ إِنْ كُنْتَ تَعْلَمُ أَنَّ هَذَا الْأَمْرَ خَيْرٌ لِي فِي دِينِي وَمَعَاشِي وَعَاقِبَةِ أَمْرِي، فَاقْدُرْهُ لِي وَيَسِّرْهُ لِي ثُمَّ بَارِكْ لِي فِيهِ، وَإِنْ كُنْتَ تَعْلَمُ أَنَّ هَذَا الْأَمْرَ شَرٌّ لِي فِي دِينِي وَمَعَاشِي وَعَاقِبَةِ أَمْرِي، فَاصْرِفْهُ عَنِّي وَاصْرِفْنِي عَنْهُ، وَاقْدُرْ لِي الْخَيْرَ حَيْثُ كَانَ ثُمَّ أَرْضِنِي بِهِ',
                'transliteration' => 'Allahumma inni astakhiroka bi\'ilmika wa astaqdiruka bi-qudratika, wa as\'aluka min fadhlikal-\'azim, fa\'innaka taqdiru wa la aqdiru wa ta\'lamu wa la a\'lamu wa anta \'allamul-ghuyub. Allahumma in kunta ta\'lamu anna hadhal-amra khayrun li fi dini wa ma\'ashi wa\'aqibati amri, faqdurhu li wa yassirhu li thumma barik li fihi, wa in kunta ta\'lamu anna hadhal-amra sharrun li fi dini wa ma\'ashi wa\'aqibati amri, fasrifhu \'anni wasrifni \'anhu waqdur liyal-khayra haythu kana thumma ardinni bihi',
                'translation' => 'O Allah, I seek Your guidance by Your knowledge and I seek Your ability by Your power, and I ask You from Your immense favor, for indeed You are able while I am not, and You know while I do not, and You are the Knower of the unseen. O Allah, if You know this matter is good for me in my religion, my livelihood, and the outcome of my affair, then decree it for me, facilitate it for me, and bless me in it. But if You know this matter is bad for me in my religion, my livelihood, and the outcome of my affair, then turn it away from me and turn me away from it, and decree for me what is good wherever it may be, and then make me satisfied with it.',
                'benefits' => 'النبي ﷺ علم أصحابه هذا الدعاء لاتخاذ القرارات المهمة، يطلب فيه الهداية والخير.',
                'reference' => 'البخاري',
                'count' => 1
            ],
        ];
    }

    private function getQuranicDuaas()
    {
        return [
            [
                'id' => 1,
                'title' => 'دعاء زيادة العلم',
                'title_arabic' => 'دعاء زيادة العلم',
                'arabic' => 'رَبِّ زِدْنِي عِلْمًا',
                'transliteration' => 'Rabbi zidni ilma',
                'translation' => 'ربي زدني علمًا.',
                'benefits' => 'دعاء قصير ولكنه قوي يطلب من الله زيادة العلم النافع.',
                'reference' => 'القرآن 20:114',
                'count' => 1
            ],
            [
                'id' => 2,
                'title' => 'دعاء المغفرة',
                'title_arabic' => 'دعاء المغفرة',
                'arabic' => 'رَبَّنَا اغْفِرْ لَنَا ذُنُوبَنَا وَإِسْرَافَنَا فِي أَمْرِنَا وَثَبِّتْ أَقْدَامَنَا وَانْصُرْنَا عَلَى الْقَوْمِ الْكَافِرِينَ',
                'transliteration' => 'Rabbana-ghfir lana dhunubana wa israfana fi amrina wa thabbit aqdamana wansurna \'alal-qawmil-kafirin',
                'translation' => 'ربنا اغفر لنا ذنوبنا وإسرافنا في أمرنا وثبت أقدامنا وانصرنا على القوم الكافرين.',
                'benefits' => 'دعاء شامل يطلب المغفرة، الثبات، والنصر على التحديات.',
                'reference' => 'القرآن 3:147',
                'count' => 1
            ],
            [
                'id' => 3,
                'title' => 'دعاء للوالدين',
                'title_arabic' => 'دعاء للوالدين',
                'arabic' => 'رَبِّ ارْحَمْهُمَا كَمَا رَبَّيَانِي صَغِيرًا',
                'transliteration' => 'Rabbir hamhuma kama rabbayani saghira',
                'translation' => 'ربي ارحمهما كما ربياني صغيرًا.',
                'benefits' => 'دعاء جميل يطلب من الله رحمة الوالدين كما رحما الابن.',
                'reference' => 'القرآن 17:24',
                'count' => 1
            ],
            [
                'id' => 4,
                'title' => 'دعاء طلب الرزق',
                'title_arabic' => 'دعاء طلب الرزق',
                'arabic' => 'رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً وَفِي الْآخِرَةِ حَسَنَةً وَقِنَا عَذَابَ النَّارِ',
                'transliteration' => 'Rabbana atina fid-dunya hasanatan wa fil-akhirati hasanatan waqina \'adhaban-nar',
                'translation' => 'ربنا آتنا في الدنيا حسنة وفي الآخرة حسنة وقنا عذاب النار.',
                'benefits' => 'دعاء شامل يطلب الخير في الدنيا والآخرة والحماية من عذاب النار.',
                'reference' => 'القرآن 2:201',
                'count' => 1
            ],
        ];
    }

    private function getProtectionDuaas()
    {
        return [
            [
                'id' => 1,
                'title' => 'طلب اللجوء من الشر',
                'title_arabic' => 'طلب اللجوء من الشر',
                'arabic' => 'قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ مِنْ شَرِّ مَا خَلَقَ وَمِنْ شَرِّ غَاسِقٍ إِذَا وَقَبَ وَمِنْ شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ وَمِنْ شَرِّ حَاسِدٍ إِذَا حَسَدَ',
                'transliteration' => 'Qul a\'udhu bi rabbil-falaq min sharri ma khalaq wa min sharri ghasiqin idha waqab wa min sharrin-naffathati fil \'uqad wa min sharri hasidin idha hasad',
                'translation' => 'قل أعوذ برب الفلق من شر ما خلق ومن شر غاسق إذا وقب ومن شر النفاثات في العقد ومن شر حاسد إذا حسد.',
                'benefits' => 'سورة تقدم الحماية من أنواع الشر بما في ذلك القوى الظلامية والسحر وحسد الآخرين.',
                'reference' => 'القرآن، سورة الفلق (113)',
                'count' => 3
            ],
            [
                'id' => 2,
                'title' => 'الحماية من الكوارث',
                'title_arabic' => 'الحماية من الكوارث',
                'arabic' => 'بِسْمِ اللَّهِ الَّذِي لَا يَضُرُّ مَعَ اسْمِهِ شَيْءٌ فِي الْأَرْضِ وَلَا فِي السَّمَاءِ وَهُوَ السَّمِيعُ الْعَلِيمُ',
                'transliteration' => 'Bismillahil-ladhi la yadurru ma\'a ismihi shay\'un fil-ardi wa la fis-sama\' wa huwas-sami\'ul-\'alim',
                'translation' => 'بسم الله الذي لا يضر مع اسمه شيء في الأرض ولا في السماء وهو السميع العليم.',
                'benefits' => 'قال النبي ﷺ من قرأ هذا الدعاء ثلاث مرات في الصباح والمساء لم يصب بكارثة.',
                'reference' => 'أبو داود وابن ماجه والترمذي',
                'count' => 3
            ],
            [
                'id' => 3,
                'title' => 'حصن الحماية',
                'title_arabic' => 'حصن الحماية',
                'arabic' => 'أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّاتِ مِنْ شَرِّ مَا خَلَقَ',
                'transliteration' => 'A\'udhu bikalimatil-lahit-tammati min sharri ma khalaq',
                'translation' => 'أعوذ بكلمات الله التامات من شر ما خلق.',
                'benefits' => 'قال النبي ﷺ من قرأ هذا في المساء لم يضره شيء تلك الليلة.',
                'reference' => 'مسلم',
                'count' => 3
            ],
            [
                'id' => 4,
                'title' => 'دعاء العين',
                'title_arabic' => 'دعاء العين',
                'arabic' => 'أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّاتِ مِنْ كُلِّ شَيْطَانٍ وَهَامَّةٍ وَمِنْ كُلِّ عَيْنٍ لَامَّةٍ',
                'transliteration' => 'A\'udhu bikalimatil-lahit-tammati min kulli shaytanin wa haammatin wa min kulli \'aynin laammatin',
                'translation' => 'أعوذ بكلمات الله التامات من كل شيطان وهامة ومن كل عين لامة.',
                'benefits' => 'دعاء يقال للحماية من العين والسحر والمخلوقات الضارة.',
                'reference' => 'البخاري ومسلم',
                'count' => 3
            ],
        ];
    }

    private function getDailyLifeDuaas()
    {
        return [
            [
                'id' => 1,
                'title' => 'دعاء قبل الأكل',
                'title_arabic' => 'دعاء قبل الأكل',
                'arabic' => 'بِسْمِ اللَّهِ',
                'transliteration' => 'Bismillah',
                'translation' => 'بسم الله.',
                'benefits' => 'قول هذا قبل الأكل يستحضر بركة الله على الطعام.',
                'reference' => 'البخاري ومسلم',
                'count' => 1
            ],
            [
                'id' => 2,
                'title' => 'دعاء بعد الأكل',
                'title_arabic' => 'دعاء بعد الأكل',
                'arabic' => 'الْحَمْدُ لِلَّهِ الَّذِي أَطْعَمَنِي هَذَا وَرَزَقَنِيهِ مِنْ غَيْرِ حَوْلٍ مِنِّي وَلَا قُوَّةٍ',
                'transliteration' => 'Alhamdu lillahil-ladhi at\'amani hadha wa razaqanihi min ghayri hawlin minni wa la quwwatin',
                'translation' => 'الحمد لله الذي أطعمني هذا ورزقنيه من غير حول مني ولا قوة.',
                'benefits' => 'تعبير عن الشكر لله على نعمة الطعام.',
                'reference' => 'أبو داود والترمذي وابن ماجه',
                'count' => 1
            ],
            [
                'id' => 3,
                'title' => 'دعاء دخول المنزل',
                'title_arabic' => 'دعاء دخول المنزل',
                'arabic' => 'بِسْمِ اللَّهِ وَلَجْنَا، وَبِسْمِ اللَّهِ خَرَجْنَا، وَعَلَى رَبِّنَا تَوَكَّلْنَا',
                'transliteration' => 'Bismillahi walajna, wa bismillahi kharajna, wa \'ala Rabbina tawakkalna',
                'translation' => 'بسم الله ولجنا، وبسم الله خرجنا، وعلى ربنا توكلنا.',
                'benefits' => 'طلب بركة الله عند دخول المنزل وخروجه والتوكل عليه.',
                'reference' => 'أبو داود',
                'count' => 1
            ],
            [
                'id' => 4,
                'title' => 'دعاء الخروج من المنزل',
                'title_arabic' => 'دعاء الخروج من المنزل',
                'arabic' => 'بِسْمِ اللَّهِ، تَوَكَّلْتُ عَلَى اللَّهِ، وَلَا حَوْلَ وَلَا قُوَّةَ إِلَّا بِاللَّهِ',
                'transliteration' => 'Bismillah, tawakkaltu \'ala Allah, wa la hawla wa la quwwata illa billah',
                'translation' => 'بسم الله، توكلت على الله، ولا حول ولا قوة إلا بالله.',
                'benefits' => 'يطلب الحماية والتوفيق عند الخروج من المنزل.',
                'reference' => 'أبو داود والترمذي',
                'count' => 1
            ],
        ];
    }

    private function getHealthDuaas()
    {
        return [
            [
                'id' => 1,
                'title' => 'دعاء الشفاء',
                'title_arabic' => 'دعاء الشفاء',
                'arabic' => 'اللَّهُمَّ رَبَّ النَّاسِ، أَذْهِبِ الْبَاسَ، اشْفِ أَنْتَ الشَّافِي، لَا شِفَاءَ إِلَّا شِفَاؤُكَ، شِفَاءً لَا يُغَادِرُ سَقَمًا',
                'transliteration' => 'Allahumma Rabban-nas, adhhibil-ba\'s, ishfi antash-Shafi, la shifa\'a illa shifa\'uk, shifa\'an la yughadiru saqaman',
                'translation' => 'يا الله، رب الناس، أذهب البأس، اشف أنت الشافي، لا شفاء إلا شفاؤك، شفاءً لا يغادر سقماً.',
                'benefits' => 'كان النبي ﷺ يقرأ هذا الدعاء للمرضى طلباً للشفاء التام من الله.',
                'reference' => 'البخاري ومسلم',
                'count' => 3
            ],
            [
                'id' => 2,
                'title' => 'دعاء الحماية من الأمراض',
                'title_arabic' => 'دعاء الحماية من الأمراض',
                'arabic' => 'اللَّهُمَّ إِنِّي أَعُوذُ بِكَ مِنَ الْبَرَصِ، وَالْجُنُونِ، وَالْجُذَامِ، وَمِنْ سَيِّئِ الْأَسْقَامِ',
                'transliteration' => 'Allahumma inni a\'udhu bika minal-barasi, wal-jununi, wal-judhami, wa min sayyi\'il-asqam',
                'translation' => 'يا الله، أعوذ بك من البرص والجنون والجذام ومن سيئ الأسقام.',
                'benefits' => 'هذا الدعاء يطلب الحماية من الأمراض الخطيرة التي تؤثر على الجسم والعقل.',
                'reference' => 'أبو داود والنسائي',
                'count' => 1
            ],
            [
                'id' => 3,
                'title' => 'زيارة المريض',
                'title_arabic' => 'زيارة المريض',
                'arabic' => 'لَا بَأْسَ طَهُورٌ إِنْ شَاءَ اللَّهُ',
                'transliteration' => 'La ba\'sa tahurun in sha Allah',
                'translation' => 'لا بأس، طهور إن شاء الله.',
                'benefits' => 'كان النبي ﷺ يقول هذا لتعزية المريض، مؤكداً أن المرض قد يكون وسيلة للتطهير من الذنوب.',
                'reference' => 'البخاري',
                'count' => 1
            ],
            [
                'id' => 4,
                'title' => 'دعاء الرقية',
                'title_arabic' => 'دعاء الرقية',
                'arabic' => 'أَعُوذُ بِكَلِمَاتِ اللَّهِ التَّامَّاتِ مِنْ شَرِّ مَا خَلَقَ، وَمِنْ شَرِّ كُلِّ دَابَّةٍ أَنْتَ آخِذٌ بِنَاصِيَتِهَا، إِنَّ رَبِّي عَلَى صِرَاطٍ مُسْتَقِيمٍ',
                'transliteration' => 'A\'udhu bikalimatil-lahit-tammati min sharri ma khalaq, wa min sharri kulli dabbatin anta akhidhun bi-naasiyatiha, inna Rabbi \'ala siratim mustaqim',
                'translation' => 'أعوذ بكلمات الله التامات من شر ما خلق، ومن شر كل دابة أنت آخذ بناصيتها، إن ربي على صراط مستقيم.',
                'benefits' => 'دعاء يستخدم كرقية للحماية من الأمراض والمخلوقات الضارة.',
                'reference' => 'الترمذي',
                'count' => 3
            ],
        ];
    }

    private function getReliefDuaas()
    {
        return [
            [
                'id' => 1,
                'title' => 'دعاء الضيق',
                'title_arabic' => 'دعاء الضيق',
                'arabic' => 'لَا إِلَهَ إِلَّا اللَّهُ الْعَظِيمُ الْحَلِيمُ، لَا إِلَهَ إِلَّا اللَّهُ رَبُّ الْعَرْشِ الْعَظِيمِ، لَا إِلَهَ إِلَّا اللَّهُ رَبُّ السَّمَاوَاتِ وَرَبُّ الْأَرْضِ وَرَبُّ الْعَرْشِ الْكَرِيمِ',
                'transliteration' => 'La ilaha illallahul-\'Azimul-Halim, la ilaha illallahu Rabbul-\'Arshil-\'Azim, la ilaha illallahu Rabbus-samawati wa Rabbul-ardi wa Rabbul-\'Arshil-Karim',
                'translation' => 'لا إله إلا الله العظيم الحليم، لا إله إلا الله رب العرش العظيم، لا إله إلا الله رب السماوات ورب الأرض ورب العرش الكريم.',
                'benefits' => 'كان النبي ﷺ يقرأ هذا الدعاء في أوقات الضيق، معترفاً بقدرة الله وسيادته على الخلق.',
                'reference' => 'البخاري ومسلم',
                'count' => 3
            ],
            [
                'id' => 2,
                'title' => 'دعاء القلق والحزن',
                'title_arabic' => 'دعاء القلق والحزن',
                'arabic' => 'اللَّهُمَّ إِنِّي عَبْدُكَ، ابْنُ عَبْدِكَ، ابْنُ أَمَتِكَ، نَاصِيَتِي بِيَدِكَ، مَاضٍ فِيَّ حُكْمُكَ، عَدْلٌ فِيَّ قَضَاؤُكَ، أَسْأَلُكَ بِكُلِّ اسْمٍ هُوَ لَكَ، سَمَّيْتَ بِهِ نَفْسَكَ، أَوْ أَنْزَلْتَهُ فِي كِتَابِكَ، أَوْ عَلَّمْتَهُ أَحَدًا مِنْ خَلْقِكَ، أَوِ اسْتَأْثَرْتَ بِهِ فِي عِلْمِ الْغَيْبِ عِنْدَكَ، أَنْ تَجْعَلَ الْقُرْآنَ رَبِيعَ قَلْبِي، وَنُورَ صَدْرِي، وَجَلَاءَ حُزْنِي، وَذَهَابَ هَمِّي',
                'transliteration' => 'Allahumma inni \'abduka, ibnu \'abdika, ibnu amatika, nasiyati biyadika, madin fiyya hukmuka, \'adlun fiyya qada\'uka, as\'aluka bikulli ismin huwa laka, sammayta bihi nafsaka, aw anzaltahu fi kitabika, aw \'allamtahu ahadan min khalqika, aw ista\'tharta bihi fi \'ilmil-ghaybi \'indaka, an taj\'alal-Qur\'ana rabi\'a qalbi, wa nura sadri, wa jala\'a huzni, wa dhahaba hammi',
                'translation' => 'يا الله، إني عبدك، ابن عبدك، ابن أمتك، ناصيتي بيدك، ماض فيّ حكمك، عدل فيّ قضاؤك، أسألك بكل اسم هو لك، سمّيت به نفسك، أو أنزلته في كتابك، أو علّمته أحدًا من خلقك، أو استأثرت به في علم الغيب عندك، أن تجعل القرآن ربيع قلبي، ونور صدري، وجلاء حزني، وذهاب همي.',
                'benefits' => 'قال النبي ﷺ من قرأ هذا الدعاء عند الإصابة بالقلق أو الحزن، سيرفع الله عنه الضيق ويبدله بالراحة.',
                'reference' => 'أحمد وصححه الألباني',
                'count' => 1
            ],
            [
                'id' => 3,
                'title' => 'دعاء الفرج من الدين',
                'title_arabic' => 'دعاء الفرج من الدين',
                'arabic' => 'اللَّهُمَّ اكْفِنِي بِحَلَالِكَ عَنْ حَرَامِكَ، وَأَغْنِنِي بِفَضْلِكَ عَمَّنْ سِوَاكَ',
                'transliteration' => 'Allahumma-kfini bihalaalika \'an haraamika wa aghnini bifadlika \'amman siwaak',
                'translation' => 'يا الله، اكفني بحلالك عن حرامك، وأغنني بفضلك عمن سواك.',
                'benefits' => 'هذا الدعاء يطلب من الله الرزق من مصادر حلال والاستغناء عن حاجة الآخرين، مما يساعد في تخفيف الديون.',
                'reference' => 'الترمذي',
                'count' => 1
            ],
            [
                'id' => 4,
                'title' => 'دعاء رفع الهم',
                'title_arabic' => 'دعاء رفع الهم',
                'arabic' => 'حَسْبِيَ اللَّهُ لَا إِلَهَ إِلَّا هُوَ عَلَيْهِ تَوَكَّلْتُ وَهُوَ رَبُّ الْعَرْشِ الْعَظِيمِ',
                'transliteration' => 'Hasbiyallahu la ilaha illa huwa, \'alayhi tawakkaltu wa huwa Rabbul-\'Arshil-\'Azim',
                'translation' => 'حسبي الله لا إله إلا هو، عليه توكلت وهو رب العرش العظيم.',
                'benefits' => 'دعاء قوي لرفع الهموم والتوكل على الله.',
                'reference' => 'البخاري',
                'count' => 3
            ],
        ];
    }
}
