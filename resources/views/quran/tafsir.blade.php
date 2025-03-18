@extends('layouts.app')

@section('title', 'تفسير ' . $surahInfo['name'])

@section('content')
  <!-- Surah Header -->
  <div class="bg-islamic-green-800 text-white">
    <div class="container mx-auto px-4 py-12">
      <div class="text-center">
        <h1 class="font-arabic text-4xl md:text-5xl mb-2">تفسير {{ $surahInfo['name'] }}</h1>
        <p class="mt-2 text-lg">عدد الآيات: {{ count($ayahs) }}</p>
        <p class="mt-1">{{ $tafsirInfo['name'] }}</p>
        <span class="inline-block mt-3 px-4 py-1 rounded-full text-sm font-semibold bg-{{ $surahInfo['revelationType'] === 'Meccan' ? 'islamic-gold-500' : 'islamic-green-600' }}">
          {{ $surahInfo['revelationType'] === 'Meccan' ? 'مكية' : 'مدنية' }}
        </span>
      </div>

      <div class="flex justify-center mt-6 space-x-4">
        <a href="{{ route('quran.surah', $surahInfo['number']) }}"
          class="bg-white text-islamic-green-800 px-4 py-2 rounded-lg flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          عرض القراءة
        </a>
      </div>
    </div>
  </div>
  {{-- tafisr resource --}}
<div class="container mx-auto px-4 py-4 mt-4 mb-8">
    <div class="text-center text-sm text-gray-500">
      <p>{{ $tafsirInfo['name'] }} </p>
    </div>
  </div>
  <!-- Surah Content with Tafsir -->
  <div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
      @if ($surahInfo['number'] != 9)
        {{-- Surah At-Tawbah doesn't begin with Bismillah --}}
        <div class="text-center font-arabic text-2xl mb-6">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
      @endif

      @foreach ($ayahs as $ayah)
        <div class="mb-12 pb-8 border-b border-gray-200 last:border-b-0">
          <div class="flex items-start mb-4">
            <div class="flex-grow">
              <p dir="rtl" class="font-arabic text-2xl mb-3 text-right leading-loose">
                {{ $ayah['arabic_text'] }}</p>
              <p class="text-gray-700" dir="rtl">{{ $ayah['translation'] }}</p>
            </div>
            <div
              class="w-10 h-10 rounded-full bg-islamic-gold-500 text-white flex items-center justify-center mr-4 flex-shrink-0">
              <span class="font-bold">{{ $ayah['number'] }}</span>
            </div>
          </div>

          <div class="mt-6">
            <h4 class="font-bold text-lg text-islamic-green-700 mb-3" dir="rtl">التفسير:</h4>
            <div class="bg-gray-50 p-4 rounded-lg border-r-4 border-islamic-gold-500" dir="rtl">
              <p class="text-gray-800">{{ $ayah['tafsir'] }}</p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Pagination -->
  <div class="container mx-auto px-4 py-4">
    <div class="flex justify-between items-center">
      @if ($surahInfo['number'] > 1)
        <a href="{{ route('quran.tafsir', $surahInfo['number'] - 1) }}"
          class="bg-islamic-green-100 text-islamic-green-800 px-4 py-2 rounded-lg hover:bg-islamic-green-200 transition flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          السورة السابقة
        </a>
      @else
        <div></div>
      @endif

      <a href="{{ route('quran.index') }}"
        class="bg-islamic-green-600 text-white px-4 py-2 rounded-lg hover:bg-islamic-green-700 transition">
        قائمة السور
      </a>

      @if ($surahInfo['number'] < 114)
        <a href="{{ route('quran.tafsir', $surahInfo['number'] + 1) }}"
          class="bg-islamic-green-100 text-islamic-green-800 px-4 py-2 rounded-lg hover:bg-islamic-green-200 transition flex items-center">
          السورة التالية
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      @else
        <div></div>
      @endif
    </div>
  </div>
@endsection
