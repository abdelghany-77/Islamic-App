@extends('layouts.app')

@section('title', $book->name)

@section('content')
<div class="container">
    <h2 style="text-align: center; color: #1f4a38; font-size: 2rem; margin-bottom: 2rem;">
        {{ $book->name }} by {{ $book->author }}
    </h2>
    @foreach($hadiths as $hadith)
    <div style="margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px dashed #d4a017;">
        <h3 style="font-size: 1.5rem; color: #1f4a38;">{{ $hadith->reference_number }}</h3>
        <div class="quran-text" style="margin-top: 1rem;">{{ $hadith->arabic_text }}</div>
        <p style="font-size: 1.2rem; line-height: 2rem; margin-top: 1rem;">{{ $hadith->translation }}</p>
        <p style="font-size: 1.1rem; font-style: italic; color: #555; margin-top: 0.5rem;">
            Narrated by: {{ $hadith->narrator }}
        </p>
    </div>
    @endforeach
</div>
@endsection
