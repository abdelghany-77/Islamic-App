@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

<div class="container">
    <div style="text-align: center;">
        <h1 class="quran-text" style="font-size: 3rem; margin-bottom: 1rem;">
            بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيمِ
        </h1>
        <a href="{{ route('quran.index') }}" class="btn">Read Quran</a>
        <a href="{{ route('hadith.index') }}" class="btn" style="margin-left: 1rem;">Explore Hadith</a>
    </div>
</div>
@endsection
