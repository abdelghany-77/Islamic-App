@extends('layouts.app')

@section('title', $category['name_arabic'] . ' - أدعية إسلامية')

@section('content')
  <div class="bg-islamic-green-800 text-white">
    <div class="container mx-auto px-4 py-12">
      <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4 font-arabic">{{ $category['name_arabic'] }}</h1>
        @if ($category['description_arabic'])
          <p class="font-arabic text-2xl mb-2">{{ $category['description_arabic'] }}</p>
        @endif
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8">
    <div class="mb-6">
      <a href="{{ route('duaa.index') }}"
        class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center font-arabic">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
        </svg>
        العودة إلى جميع الأدعية
      </a>
    </div>

    @if (session('error'))
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p>{{ session('error') }}</p>
      </div>
    @endif

    @if (count($duaas) > 0)
      @foreach ($duaas as $index => $duaa)
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
          <div class="p-6">
            <p dir="rtl" class="font-arabic text-2xl mb-4 text-right leading-loose">{{ $duaa['arabic'] }}</p>
            {{-- @if (isset($duaa['transliteration']) && !empty($duaa['transliteration']))
              <p class="text-gray-700 italic mb-3">{{ $duaa['transliteration'] }}</p>
            @endif
            <p class="text-gray-800 mb-4">{{ $duaa['translation'] }}</p> --}}

            <div class="mt-4 flex flex-wrap items-center gap-4 pt-3 border-t border-gray-100">
              @if (isset($duaa['reference']) && !empty($duaa['reference']))
                <div class="text-gray-600 text-sm font-arabic">
                  <span class="font-medium">المرجع:</span> {{ $duaa['reference'] }}
                </div>
              @endif
            </div>

            <!-- Counter -->
            @if (isset($duaa['count']) && $duaa['count'] > 1)
              <div class="mt-4">
                <div class="flex items-center justify-between">
                  <!-- Numerical Counter -->
                  <span class="text-gray-700 whitespace-nowrap font-arabic">التقدم: <span id="counter-{{ $index }}">0</span> / {{ $duaa['count'] }}</span>
                  <!-- Recite Button -->
                  <button id="recite-button-{{ $index }}"
                    onclick="incrementCounter({{ $index }}, {{ $duaa['count'] }})"
                    class="bg-islamic-green-600 text-white px-4 py-2 rounded-lg hover:bg-islamic-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed ml-4 font-arabic">
                    +
                  </button>
                </div>
              </div>
            @endif

            @if (isset($duaa['benefits']) && !empty($duaa['benefits']))
              <div class="mt-4 bg-islamic-gold-50 p-3 rounded-md">
                <p dir="rtl" class="text-gray-700 font-arabic">{{ $duaa['benefits'] }}</p>
              </div>
            @endif
          </div>
          <div class="bg-gray-50 px-6 py-3 border-t flex justify-between items-center">
            <div class="flex space-x-4">
              <button class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center text-sm font-arabic"
                onclick="copyDua({{ json_encode($duaa['arabic']) }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                نسخ
              </button>
              <button class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center text-sm font-arabic"
                onclick="shareDua({{ json_encode($duaa) }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                مشاركة
              </button>
            </div>

            <div>
              <button class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center font-arabic"
                onclick="playAudio({{ json_encode($duaa) }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-islamic-green-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-xl font-bold text-gray-700 mb-2 font-arabic">لا يوجد أدعية متاحة</h3>
        <p class="text-gray-600 font-arabic">لم نتمكن من العثور على أدعية لـ {{ $category['name_arabic'] }}.</p>
        <p class="text-gray-600 mt-2 font-arabic">يرجى اختيار فئة أخرى أو المحاولة لاحقًا.</p>
      </div>
    @endif
  </div>

  <script>
    let counters = {};

    function incrementCounter(index, maxCount) {
      if (!counters[index]) {
        counters[index] = 0;
      }
      if (counters[index] < maxCount) {
        counters[index]++;
        document.getElementById(`counter-${index}`).textContent = counters[index];
        if (counters[index] === maxCount) {
          //   alert('لقد أكملت تلاوة الدعاء!');
        }
      }
    }

    function copyDua(text) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
          .then(() => {
            alert('تم نسخ الدعاء إلى الحافظة');
          })
          .catch(err => {
            console.error('لم يمكن نسخ النص: ', err);
            alert('فشل النسخ. حاول مجددًا.');
          });
      } else {
        alert('نسخ إلى الحافظة غير مدعوم في متصفحك');
      }
    }

    function shareDua(duaa) {
      alert('سيتم فتح خيارات المشاركة في تنفيذ حقيقي.');
    }

    function playAudio(duaa) {
      alert('سيتم تشغيل التلاوة الصوتية لهذا الدعاء في تنفيذ حقيقي.');
    }
  </script>
@endsection
