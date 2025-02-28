@extends('layouts.app')

@section('title', 'Hadith ' . $hadith->reference_number)

@section('content')
<div class="book-container">
    <div class="p-4" style="background: #fffef5; border: 1px solid var(--gold);">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 style="font-family: 'Lateef', cursive; color: var(--green);">{{ $hadith->book->name }}: {{ $hadith->reference_number }}</h3>
            @if($hadith->authenticity)
            <span style="background: #d4edda; color: #155724; padding: 0.5rem 1rem; border-radius: 20px;">{{ $hadith->authenticity }}</span>
            @endif
        </div>
        <div class="quran-text mb-4">{{ $hadith->arabic_text }}</div>
        <p style="font-size: 1.2rem; line-height: 1.6;">{{ $hadith->translation }}</p>
        <p style="font-style: italic; color: #555; margin-top: 1rem;">Narrated by: {{ $hadith->narrator }}</p>
        @if($hadith->explanation)
        <div class="mt-4 p-3" style="background: #f8f2e0; border-radius: 5px;">
            <h5 style="color: var(--green);">Explanation</h5>
            <p>{{ $hadith->explanation }}</p>
        </div>
        @endif
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('hadith.book', $hadith->book) }}" class="btn btn-quran">Back</a>
            <button class="btn btn-quran" onclick="navigator.clipboard.writeText('{{ $hadith->book->name }} {{ $hadith->reference_number }}: {{ $hadith->translation }}')">
                <i class="bi bi-clipboard"></i> Copy
            </button>
        </div>
    </div>
</div>
@endsection
