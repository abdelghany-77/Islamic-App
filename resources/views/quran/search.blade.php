@extends('layouts.app')

@section('content')
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold mb-6 text-center">نتائج البحث: {{ $query }}</h1>
      <!-- Search Form - Using your existing form structure -->
      <div class="mt-8 max-w-md mx-auto mb-8">
        <form action="{{ route('quran.search') }}" method="GET" class="flex">
          <input type="text" name="query" value="{{ $query }}" placeholder="ابحث عن سورة..."
            class="w-full px-4 py-2 rounded-l text-gray-800 focus:outline-none">
          <button type="submit" class="bg-islamic-gold-500 hover:bg-islamic-gold-600 px-4 py-2 rounded-r">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </form>
      </div>

      <!-- Error Message -->
      @if (isset($error))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
          <p>{{ $error }}</p>
        </div>
      @endif

      <!-- Results -->
      @if (count($results) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach ($results as $surah)
            <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
              <div class="flex justify-between items-center mb-4">
                <div
                  class="bg-islamic-gold-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-medium">
                  {{ $surah['number'] ?? 'N/A' }}
                </div>
                <div class="text-gray-500 text-sm">
                  {{ $surah['numberOfAyahs'] ?? 'N/A' }} آيات
                </div>
              </div>

              <h3 class="text-xl font-bold text-right mb-1" dir="rtl">
                {{ $surah['name'] ?? ($surah['name_arabic'] ?? 'N/A') }}
              </h3>

              <div class="text-gray-600 mb-4 text-sm">
                <p>{{ $surah['englishName'] ?? 'N/A' }}</p>
                <p class="mt-1 text-xs">
                  <span class="inline-block px-2 py-1 rounded bg-gray-100">
                    {{ $surah['revelationType'] ?? 'N/A' }}
                  </span>
                </p>
              </div>

              <a href="{{ route('quran.surah', $surah['number']) }}"
                class="block w-full text-center bg-islamic-green-500 hover:bg-islamic-green-600 text-white py-2 rounded transition-colors">
                قراءة السورة
              </a>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center p-10 bg-gray-50 rounded-lg">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-2 text-xl font-semibold text-gray-900">لم يتم العثور على نتائج</h3>
          <p class="mt-1 text-gray-500">لم نتمكن من العثور على أي سورة تطابق بحثك "{{ $query }}". حاول بكلمة أخرى.
          </p>
          <div class="mt-6">
            <a href="{{ route('quran.index') }}" class="text-islamic-green-500 hover:text-islamic-green-600">
              العودة إلى القرآن الكريم
            </a>
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
