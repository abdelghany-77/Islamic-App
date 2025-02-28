@extends('layouts.app')

@section('title', 'Hadith Search Results')

@section('header')
<div class="main-header">
    <div class="container text-center">
        <h1 style="font-family: 'Lateef', cursive; font-size: 2.5rem;">Hadith Search</h1>
        <p style="font-size: 1.2rem;">Results for: "{{ $query }}"</p>
    </div>
</div>
@endsection

@section('content')
<div class="book-container">
    <h3 style="font-family: 'Lateef', cursive; color: var(--green); text-align: center; margin-bottom: 2rem;">{{ $hadiths->total() }} Results</h3>
    @if($hadiths->count() > 0)
        @foreach($hadiths as $hadith)
        <div class="mb-4 p-3" style="border-bottom: 1px dashed var(--gold);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 style="color: var(--green);">{{ $hadith->book->name }}: {{ $hadith->reference_number }}</h5>
                <a href="{{ route('hadith.show', $hadith) }}" class="btn btn-sm btn-quran">Details</a>
            </div>
            <div class="quran-text">{{ $hadith->arabic_text }}</div>
            <p style="font-size: 1.1rem; margin-top: 1rem;">{{ $hadith->translation }}</p>
            <p style="font-style: italic; color: #555;">Narrated by: {{ $hadith->narrator }}</p>
        </div>
        @endforeach
        <div class="text-center mt-4">{{ $hadiths->appends(['query' => $query])->links('pagination::bootstrap-5') }}</div>
    @else
        <p style="text-align: center; color: var(--dark);">No results found for "{{ $query }}".</p>
    @endif
</div>
@endsection
