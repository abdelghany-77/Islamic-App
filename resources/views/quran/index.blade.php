@extends('layouts.app')

@section('title', 'Quran')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">The Noble Quran</h1>
        <div class="row">
            @foreach($surahs as $surah)
                <div class="col-md-4 mb-3">
                    <a href="{{ route('quran.show', [$surah->id, 1]) }}" class="text-decoration-none">
                        <div class="card p-3">
                            <div class="card-body text-center">
                                <h5 class="card-title quran-text">{{ $surah->name_arabic }}</h5>
                                <p class="card-text">{{ $surah->name_english }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
