@extends('layouts.app')

@section('title', $displayName . ' Azkar')

@section('content')
  <div class="bg-islamic-green-800 text-white">
    <div class="container mx-auto px-4 py-12">
      <div class="text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $arabicName }} </h1>
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 py-8">
    <div class="mb-6">
      <a href="{{ route('azkar.index') }}"
        class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
        </svg>
        الرجوع الى قائمة الأذكار
      </a>
    </div>

    @if (session('error'))
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p>{{ session('error') }}</p>
      </div>
    @endif

    @if (count($azkar) > 0)
      @foreach ($azkar as $index => $zikr)
        <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
          <div class="p-6">
            <p dir="rtl" class="font-arabic text-2xl mb-4 text-right leading-loose">
              {{ $zikr['zekr'] ?? ($zikr['arabic_text'] ?? 'Arabic text not available') }}</p>
            {{-- @if (isset($zikr['transliteration']) && !empty($zikr['transliteration']))
              <p class="text-gray-700 italic mb-3">{{ $zikr['transliteration'] }}</p>
            @endif
            <p class="text-gray-800 mb-4">
              {{ $zikr['translation'] ?? ($zikr['english_translation'] ?? ($zikr['description'] ?? 'Translation not available')) }}
            </p> --}}

            <div class="mt-4 flex flex-wrap items-center gap-4 pt-3 border-t border-gray-100">
              {{-- @if (isset($zikr['count']) && $zikr['count'] > 1)
                <div class="bg-islamic-green-50 px-3 py-1 rounded-full text-islamic-green-800 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  Repeat {{ $zikr['count'] }} times
                </div>
              @endif --}}

              @if (isset($zikr['reference']) && !empty($zikr['reference']))
                <div class="text-gray-600 text-sm">
                  <span class="font-medium"></span> {{ $zikr['reference'] }}
                </div>
              @endif
            </div>

            <!-- Counter -->
            @if (isset($zikr['count']) && $zikr['count'] > 1)
              <div class="mt-4">
                <div class="flex items-center justify-between">
                  <!-- Numerical Counter -->
                  <span class="text-gray-700 whitespace-nowrap">Progress: <span id="counter-{{ $index }}">0</span>
                    / {{ $zikr['count'] }}</span>
                  <!-- Recite Button -->
                  <button id="recite-button-{{ $index }}"
                    onclick="incrementCounter({{ $index }}, {{ $zikr['count'] }})"
                    class="bg-islamic-green-600 text-white px-4 py-2 rounded-lg hover:bg-islamic-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed ml-4">
                    +
                  </button>
                </div>
              </div>
            @endif

            @if (isset($zikr['benefits']) && !empty($zikr['benefits']))
              <div class="mt-4 bg-islamic-gold-50 p-3 rounded-md">
                <p dir="rtl" class="text-gray-700">{{ $zikr['benefits'] }}</p>
              </div>
            @endif
          </div>
          <div class="bg-gray-50 px-6 py-3 border-t flex justify-between items-center">
            <div class="flex space-x-4">
              <button class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center text-sm"
                onclick="copyZikr({{ json_encode($zikr['zekr'] ?? ($zikr['arabic_text'] ?? '')) }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                نسخ
              </button>
              <button class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center text-sm"
                onclick="shareZikr({{ json_encode($zikr) }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                مشاركة
              </button>
            </div>

            <div>
              <button class="text-islamic-green-700 hover:text-islamic-green-800 inline-flex items-center"
                onclick="playAudio({{ json_encode($zikr) }})">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-islamic-green-300 mb-4" fill="none"
          viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-xl font-bold text-gray-700 mb-2">No Azkar Available</h3>
        <p class="text-gray-600">We couldn't find any azkar for {{ $displayName }}.</p>
        <p class="text-gray-600 mt-2">Please try selecting a different category or check back later.</p>
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
          //   alert('You have completed the recitation!');
        }
      }
    }

    function copyZikr(text) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text)
          .then(() => {
            alert('Zikr copied to clipboard');
          })
          .catch(err => {
            console.error('Could not copy text: ', err);
            alert('Failed to copy. Please try again.');
          });
      } else {
        alert('Copy to clipboard is not supported in your browser');
      }
    }

    function shareZikr(zikr) {
      alert('This would open sharing options in a real implementation.');
    }

    function playAudio(zikr) {
      alert('This would play the audio recitation of this zikr in a real implementation.');
    }
  </script>
@endsection
