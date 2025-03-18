@extends('layouts.app')

@section('title', 'أدعية وصلوات إسلامية')

@section('content')
  <div class="bg-islamic-green-800 text-white">
    <div class="container mx-auto px-4 py-12">
      <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-2 font-arabic">أدعية وصلوات إسلامية</h1>
        <p class="text-xl max-w-3xl mx-auto font-arabic">تقرب إلى الله بالأدعية اليومية التي تغطي كل احتياجاتك، من طلب
          الهداية والرزق إلى الحماية والشفاء.

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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($categories as $category)
        <a href="{{ route('duaa.category', $category['slug']) }}"
          class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 flex flex-col items-center justify-center text-center">
          <div
            class="w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-islamic-green-100 text-islamic-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              @if ($category['icon'] == 'prophet')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
              @elseif($category['icon'] == 'quran')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              @elseif($category['icon'] == 'shield')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              @elseif($category['icon'] == 'daily')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              @elseif($category['icon'] == 'health')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              @elseif($category['icon'] == 'relief')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
              @endif
            </svg>
          </div>
          <h2 class="text-xl font-bold text-islamic-green-800 mb-2 font-arabic">{{ $category['name'] }}</h2>
          <p class="text-gray-600 font-arabic">{{ $category['description'] }}</p>
{{--
          @if (isset($category['count']) && $category['count'] > 0)
            <div class="mt-3">
              <span
                class="bg-islamic-green-100 text-islamic-green-800 px-3 py-1 rounded-full text-xs font-medium flex items-center justify-center font-arabic">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                {{ $category['count'] }} أدعية
              </span>
            </div>
          @endif --}}
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
          <h3 class="text-xl font-medium text-gray-500 font-arabic">لا توجد فئات أدعية متاحة</h3>
          <p class="mt-2 text-gray-500 font-arabic">يرجى المحاولة لاحقًا</p>
        </div>
      @endforelse
    </div>

    <div dir="rtl" class="mt-12 bg-islamic-green-50 rounded-lg p-6">
      <h3 class="text-xl font-bold text-islamic-green-800 mb-4 font-arabic">حول الأدعية والصلوات الإسلامية</h3>
      <div class="text-gray-700 font-arabic">
        <p>الدعاء هو الكلمة العربية للابتهال أو الصلاة. وهو فعل الدعاء إلى الله بخصوصية وتواضع، طلبًا لمساعدته، هدايته،
          رحمته، وبركاته.</p>
        <p class="mt-2">علمنا النبي محمد صلى الله عليه وسلم أن الدعاء جوهر العبادة، وشجع المسلمين على الابتهال في جميع
          المواقف. يقول الله في القرآن: "وَقَالَ رَبُّكُمُ ادْعُونِي أَسْتَجِبْ لَكُمْ" (40:60).</p>
        <p class="mt-2">تم جمع الأدعية هنا من مصادر أصيلة تشمل القرآن والحديث. وتساعد القراءة المنتظمة لهذه الصلوات على
          تعزيز الارتباط بالله وتجلب السلام للقلب.</p>
      </div>
    </div>
  </div>
@endsection
