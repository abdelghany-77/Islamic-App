@extends('layouts.app')

@section('title', 'خطأ في التفسير')

@section('content')
<div class="container mx-auto px-4 py-16">
  <div class="max-w-lg mx-auto bg-white rounded-lg shadow-lg p-8">
    <div class="text-center mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <h1 class="text-2xl font-bold text-gray-800 mt-4">خطأ في التفسير</h1>
    </div>

    <p class="mb-6 text-center text-gray-600">{{ $error }}</p>

    <div class="flex justify-center space-x-4">
      <a href="{{ route('quran.surah', $surahNumber) }}" class="bg-islamic-green-600 text-white px-4 py-2 rounded-lg hover:bg-islamic-green-700 transition">
        العودة للقراءة
      </a>
      <a href="{{ route('quran.tafsir', $surahNumber) }}" class="bg-islamic-gold-500 text-white px-4 py-2 rounded-lg hover:bg-islamic-gold-600 transition">
        حاول مرة أخرى
      </a>
    </div>

    @if(config('app.debug'))
    <div class="mt-8 p-4 bg-gray-100 rounded-lg">
      <h3 class="font-bold text-sm mb-2">تفاصيل الخطأ (للمطورين فقط):</h3>
      <pre class="text-xs overflow-auto">{{ $details }}</pre>
    </div>
    @endif
  </div>
</div>
@endsection
