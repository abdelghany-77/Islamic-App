@extends('layouts.app')

@section('title', 'القرآن الكريم')

@section('content')
  <!-- Hero Section -->
  <div class="bg-gradient-to-br from-islamic-green-900 to-islamic-green-700 text-white">
    <div class="container mx-auto px-4 py-12 md:py-16">
      <div class="text-center">
        <h1 class="font-quran text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-islamic-gold-200 leading-tight">القرآن
          الكريم</h1>
        <p class="font-arabic text-xl md:text-2xl max-w-3xl mx-auto leading-relaxed">
          استمتع بقراءة وسماع القرآن الكريم بتلاوات متنوعة، مع تفاسير وترجمات تساعدك على فهم كلام الله وتدبره بعمق.
        </p>

        <div class="mt-10 md:mt-12 max-w-lg mx-auto">
          <form action="{{ route('quran.search') }}" method="GET"
            class="flex items-center bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Search Input -->
            <input type="text" name="query" placeholder="ابحث عن سورة  .."
              class="w-full px-5 py-3 text-gray-800 bg-white border-none focus:ring-2 focus:ring-islamic-gold-500 focus:outline-none transition duration-300"
              aria-label="    ">
            <!-- Search Button -->
            <button type="submit"
              class="bg-islamic-gold-500 hover:bg-islamic-gold-600 text-white px-5 py-3 transition duration-300 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <!-- Surah List -->
  <div dir="rtl" class="container mx-auto px-4 py-12 md:py-16">
    <h2 class="font-arabic text-2xl md:text-3xl font-bold text-center text-islamic-green-800 mb-8">قائمة السور</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      @foreach ($surahs as $surah)
        <a href="{{ route('quran.surah', $surah['number']) }}"
          class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 flex items-center group relative overflow-hidden transform hover:-translate-y-2 hover:bg-islamic-green-50">
          <!-- Number Circle -->
          <div
            class="w-16 h-16 rounded-full bg-gradient-to-br from-islamic-green-500 to-islamic-green-700 text-white flex items-center justify-center ml-5 shadow-lg group-hover:from-islamic-green-600 group-hover:to-islamic-green-800 transition-all duration-300">
            <span class="font-bold text-xl md:text-2xl">{{ $surah['number'] }}</span>
          </div>
          <!-- Surah Info -->
          <div class="flex-1 text-right">
            <h3
              class="font-arabic text-xl md:text-2xl font-semibold text-gray-800 group-hover:text-islamic-green-700 transition-colors duration-300">
              {{ $surah['name'] }}
            </h3>
            <p class="font-arabic text-gray-500 text-sm md:text-base mt-1">
              {{ $surah['englishName'] }}
            </p>
          </div>
          <!-- Decorative Overlay -->
          <div
            class="absolute inset-0 bg-islamic-green-100 opacity-0 group-hover:opacity-10 transition-opacity duration-300 rounded-xl">
          </div>
        </a>
      @endforeach
    </div>
  </div>
@endsection
