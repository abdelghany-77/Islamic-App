@extends('layouts.app')

@section('title', 'الأذكاراليومية')

@section('content')
  <div class="bg-islamic-green-800 text-white">
    <div class="container mx-auto px-4 py-12">
      <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-2">الأذكار اليومية</h1>
        <p class="text-xl md:text-xl"> اجعل ذكر الله جزءًا من يومك مع أذكار الصباح والمساء، وأدعية متنوعة لكل مناسبة تجلب لك السكينة والبركة.

</p>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8">
    @if (session('error'))
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p>{{ session('error') }}</p>
      </div>
    @endif

    @if (session('warning'))
      <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
        <p>{{ session('warning') }}</p>
      </div>
    @endif

    <div dir="rtl" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      @forelse($categories as $category)
        <a href="{{ route('azkar.category', $category['slug']) }}"
          class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 flex flex-col items-center justify-center text-center">
          <div
            class="w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-islamic-green-100 text-islamic-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              @if ($category['icon'] == 'sun')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              @elseif($category['icon'] == 'moon')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              @elseif($category['icon'] == 'prayer')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              @elseif($category['icon'] == 'bed')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              @elseif($category['icon'] == 'wake')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              @elseif($category['icon'] == 'home')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              @elseif($category['icon'] == 'door')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
              @elseif($category['icon'] == 'food')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              @endif
            </svg>
          </div>
          <h3 class="text-xl font-medium text-gray-800">{{ $category['arabic_name'] }}</h3>
          @if (isset($category['arabic_name']))
            <p dir="rtl" class="font-arabic text-lg mt-1 text-gray-600"></p>
          @endif
          @if (isset($category['count']) && $category['count'] > 0)
            <div class="mt-3">
              <span class="bg-islamic-green-100 text-islamic-green-800 px-2 py-1 rounded-full text-xs font-medium">
                {{ $category['count'] }} Azkar
              </span>
            </div>
          @endif
        </a>
      @empty
        <div class="col-span-full text-center py-8">
          <div class="text-gray-400 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-xl font-medium text-gray-500">No azkar categories available</h3>
          <p class="mt-2 text-gray-500">Please check back later or try refreshing the page.</p>
        </div>
      @endforelse
    </div>

    <div class="mt-12 bg-islamic-green-50 rounded-lg p-6">
  {{-- <!-- English Section -->
  <h2 class="text-2xl font-bold text-islamic-green-800 mb-4">About Islamic Azkar</h2>
  <p class="text-gray-700">
    Azkar (أذكار) is the plural of Zikr (ذِكر), which means remembrance or mention. In Islamic context, Azkar refers
    to the remembrance of Allah through specific phrases and supplications taught by the Prophet Muhammad (peace be
    upon him). Regular recitation of these Azkar is known to bring peace to the heart, protection from harm, and closeness to
    Allah.
  </p>
  <div class="mt-4">
    <h3 class="text-xl font-medium text-islamic-green-700 mb-2">Benefits of Reciting Azkar</h3>
    <ul class="list-disc pl-5 text-gray-700 space-y-1">
      <li>Protection from evil and harm</li>
      <li>Increased blessings in daily life</li>
      <li>Peace of mind and relief from anxiety</li>
      <li>Strengthening of faith and connection with Allah</li>
      <li>Purification of the heart and soul</li>
    </ul>
  </div>
  <div class="mt-4">
    <blockquote class="italic border-l-4 border-islamic-green-400 pl-4 py-2 text-gray-600">
      "Those who believe, and whose hearts find satisfaction in the remembrance of Allah: for without doubt in the
      remembrance of Allah do hearts find satisfaction." - Quran 13:28
    </blockquote>
  </div> --}}

  <!-- Arabic Section -->
  <div class="mt-6" dir="rtl">
    <h2 class="text-2xl font-bold text-islamic-green-800 mb-4"> الأذكار الإسلامية</h2>
    <p class="text-gray-700">
      الأذكار (جمع ذكر) تعني الذكر أو الذكر، وفي السياق الإسلامي تشير الأذكار إلى تذكر الله من خلال عبارات وأدعية معينة علّمها النبي محمد (صلى الله عليه وسلم). يُعرف تلاوة هذه الأذكار بانتظام بأنها تجلب السكينة للقلب، الحماية من الأذى، والتقرب إلى الله.
    </p>
    <div class="mt-4">
      <h3 class="text-xl font-medium text-islamic-green-700 mb-2">فوائد تلاوة الأذكار</h3>
      <ul class="list-disc pr-5 text-gray-700 space-y-1">
        <li>الحماية من الشر والأذى</li>
        <li>زيادة البركات في الحياة اليومية</li>
        <li>سكينة البال وتخفيف القلق</li>
        <li>تقوية الإيمان والارتباط بالله</li>
        <li>تطهير القلب والروح</li>
      </ul>
    </div>
    <div class="mt-4">
      <blockquote class="italic border-r-4 border-islamic-green-400 pr-4 py-2 text-gray-600">
        "الَّذِينَ آمَنُوا وَتَطْمَئِنُّ قُلُوبُهُم بِذِكْرِ اللَّهِ ۗ أَلَا بِذِكْرِ اللَّهِ تَطْمَئِنُّ الْقُلُوبُ"
      </blockquote>
    </div>
  </div>
</div>
  </div>
@endsection
