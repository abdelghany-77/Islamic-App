@extends('layouts.app')

@section('title', 'تذكير')

@section('content')
  <!-- Hero Section -->
  <div class="bg-islamic-green-900 text-white">
    <div class="container mx-auto px-4 py-16 md:py-24">
      <div class="text-center">
        <h1 class="font-arabic text-4xl md:text-5xl mb-4">بِسْمِ اللهِ الرَّحْمنِ الرَّحِيمِ</h1>
        <br>
        <p class="text-lg md:text-xl mb-6 font-arabic">
          تذكير هو مصدر شامل وملهم للمسلمين، يقدم تجربة روحانية من خلال القرآن الكريم، الأذكار اليومية، والأدعية
          المتنوعة. يهدف إلى تعزيز الصلة بالله من خلال توفير أوقات الصلاة بدقة، تفاسير، تلاوات، ومحتوى تعليمي يناسب
          احتياجاتك اليومية، مما يجعله رفيقك المثالي في يومك.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
          <a href="{{ route('quran.index') }}"
            class="bg-islamic-gold-600 hover:bg-islamic-gold-700 text-white px-6 py-2 rounded-full font-medium transition duration-300 shadow-md font-arabic">
            اقرأ القرآن
          </a>
          <a href="{{ route('azkar.index') }}"
            class="bg-islamic-green-600 hover:bg-islamic-green-700 text-white px-6 py-2 rounded-full font-medium transition duration-300 shadow-md font-arabic">
            الأذكار اليومية
          </a>
          <a href="{{ route('duaa.index') }}"
            class="bg-islamic-green-600 hover:bg-islamic-green-700 text-white px-6 py-2 rounded-full font-medium transition duration-300 shadow-md font-arabic">
            الأدعية اليومية
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Prayer Times Section -->
  <!-- Prayer Times Section -->
  <div class="container mx-auto px-4 py-12">
    <div class="bg-gray-800 rounded-xl shadow-lg p-6 border-t-4 border-islamic-green-600">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl md:text-3xl font-bold text-white text-center font-quran">أوقات الصلاة</h2>
        @if (isset($locationData))
          <div class="flex items-center text-white text-sm">
            <i class="fas fa-map-marker-alt text-islamic-green-500 ml-2"></i>
            <span class="font-arabic">{{ $locationData['city'] }}, {{ $locationData['country'] }}</span>
          </div>
        @endif
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        @foreach ([
          'Fajr' => ['name' => 'الفجر', 'name_arabic' => 'الفجر', 'time' => $timings['Fajr'], 'icon' => 'fa-sun', 'active' => false],
          'Dhuhr' => ['name' => 'الظهر', 'name_arabic' => 'الظهر', 'time' => $timings['Dhuhr'], 'icon' => 'fa-cloud-sun', 'active' => false],
          'Asr' => ['name' => 'العصر', 'name_arabic' => 'العصر', 'time' => $timings['Asr'], 'icon' => 'fa-cloud', 'active' => false],
          'Maghrib' => ['name' => 'المغرب', 'name_arabic' => 'المغرب', 'time' => $timings['Maghrib'], 'icon' => 'fa-cloud-moon', 'active' => false],
          'Isha' => ['name' => 'العشاء', 'name_arabic' => 'العشاء', 'time' => $timings['Isha'], 'icon' => 'fa-moon', 'active' => false],
      ] as $english => $data)
          <div
            class="{{ $data['active'] ? 'bg-islamic-green-700' : 'bg-gray-700' }} rounded-lg p-4 text-center transition duration-300 hover:bg-islamic-green-600">
            <i class="fas {{ $data['icon'] }} text-islamic-green-400 text-2xl mb-2"></i>
            <h3 class="text-lg font-semibold text-white uppercase font-arabic">{{ $data['name_arabic'] }}</h3>
            <p class="text-xl text-white font-arabic">{{ $data['time'] }}</p>
          </div>
        @endforeach
      </div>
      @if ($errors->has('msg'))
        <p class="text-center mt-6 text-red-400 font-arabic">{{ $errors->first('msg') }}</p>
      @endif
    </div>
  </div>
  <!-- Features Section -->
  <!-- Features Section -->
  <div class="container mx-auto px-4 py-16">
    <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 text-islamic-green-800 font-arabic">
      استمتع بتجربة إيمانية متكاملة
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Quran Section -->
      <div
        class="relative bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300">
        <div class="relative bg-gradient-to-b from-islamic-green-200 to-islamic-green-100 p-6 text-center">

          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-islamic-green-700 relative z-10"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
        </div>
        <div class="p-6 text-center">
          <h3 class="text-xl md:text-2xl font-bold mb-3 text-islamic-green-800 font-arabic">القرآن الكريم</h3>
          <p class="text-gray-600 mb-4 font-arabic leading-relaxed">
            استمتع بقراءة وسماع القرآن الكريم بتلاوات متنوعة، مع تفاسير وترجمات تساعدك على فهم كلام الله وتدبره بعمق.
          </p>
          <a href="{{ route('quran.index') }}"
            class="inline-flex items-center px-4 py-2 bg-islamic-green-600 text-white rounded-full font-medium hover:bg-islamic-green-700 transition duration-300 shadow-md font-arabic">
            اقرأ القرآن
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </a>
        </div>
      </div>

      <!-- Azkar Section -->
      <div
        class="relative bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300">
        <div class="relative bg-gradient-to-b from-islamic-green-200 to-islamic-green-100 p-6 text-center">

          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-islamic-green-700 relative z-10"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
        </div>
        <div class="p-6 text-center">
          <h3 class="text-xl md:text-2xl font-bold mb-3 text-islamic-green-800 font-arabic">الأذكار اليومية</h3>
          <p class="text-gray-600 mb-4 font-arabic leading-relaxed">
            اجعل ذكر الله جزءًا من يومك مع أذكار الصباح والمساء، وأدعية متنوعة لكل مناسبة تجلب لك السكينة والبركة.
          </p>
          <a href="{{ route('azkar.index') }}"
            class="inline-flex items-center px-4 py-2 bg-islamic-green-600 text-white rounded-full font-medium hover:bg-islamic-green-700 transition duration-300 shadow-md font-arabic">
            تصفح الأذكار
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </a>
        </div>
      </div>

      <!-- Duas Section -->
      <div
        class="relative bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300">
        <div class="relative bg-gradient-to-b from-islamic-green-200 to-islamic-green-100 p-6 text-center">

          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-islamic-green-700 relative z-10"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 9a4 4 0 015-3.873M18 9a4 4 0 00-5-3.873M11 6h2M6 9l5 6m7-6l-5 6m-5 6h10" />
          </svg>
        </div>
        <div class="p-6 text-center">
          <h3 class="text-xl md:text-2xl font-bold mb-3 text-islamic-green-800 font-arabic">الأدعية اليومية</h3>
          <p class="text-gray-600 mb-4 font-arabic leading-relaxed">
            تقرب إلى الله بالأدعية اليومية التي تغطي كل احتياجاتك، من طلب الهداية والرزق إلى الحماية والشفاء.
          </p>
          <a href="{{ route('duaa.index') }}"
            class="inline-flex items-center px-4 py-2 bg-islamic-green-600 text-white rounded-full font-medium hover:bg-islamic-green-700 transition duration-300 shadow-md font-arabic">
            استكشف الأدعية
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection

<style>
  .font-arabic {
    font-family: 'Amiri', serif;
  }

  .font-quran {
    font-family: 'Amiri Quran', 'UthmanicHafs', serif;
  }
</style>
