<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $surah->name_arabic }} - Page {{ $currentPage }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri+Quran&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quran.css') }}">
</head>
<body>
    <nav>
        <div style="max-width: 800px; margin: 0 auto;">
            <a href="{{ url('/') }}">{{ config('app.name', 'Islamic App') }}</a>
            <a href="{{ route('quran.index') }}">{{ __('Quran') }}</a>
            <a href="{{ route('hadith.index') }}">{{ __('Hadith') }}</a>
        </div>
    </nav>
    <div class="quran-page">
        <!-- Surah Header -->
        <div class="surah-header">
            <h1 class="surah-name">{{ $surah->name_arabic }}</h1>
            <span class="juz-info">جزء {{ $surah->juz_number ?? '1' }}</span>
        </div>

        <!-- Quran Text -->
        <div class="quran-text">
            @foreach ($verses as $verse)
                <div class="ayah">
                    <span class="ayah-text">{{ $verse->arabic_text }}</span>
                    <span class="ayah-number">{{ $verse->verse_number }}</span>
                </div>
            @endforeach
        </div>

        <!-- Pagination Controls -->
        <div class="pagination">
            @if ($currentPage > 1)
                <a href="{{ route('quran.show', ['surah' => $surah->id, 'page' => $currentPage - 1]) }}" class="btn">الصفحة السابقة</a>
            @endif
            @if ($hasNextPage)
                <a href="{{ route('quran.show', ['surah' => $surah->id, 'page' => $currentPage + 1]) }}" class="btn">الصفحة التالية</a>
            @endif
        </div>

        <!-- Side Panels -->
        <div class="side-panel left">جزء {{ $surah->juz_number ?? '1' }}</div>
        <div class="side-panel right">صفحة {{ $currentPage }}</div>
    </div>
</body>
</html>
