@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Search Results for: "{{ $query }}"</h1>
        @if ($verses->count() > 0)
            <div class="verses">
                @foreach ($verses as $verse)
                    <div class="verse row mb-3">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('quran.show', [$verse->surah_id, ceil($verse->verse_number / 15), 'ayah' => $verse->id]) }}" class="text-decoration-none">
                                <span class="quran-text">{{ $verse->arabic_text }}</span>
                                <span class="ayah-number badge bg-warning text-dark ms-2">{{ $verse->verse_number }}</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3 translation">
                            {{ $verse->translation }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination text-center mt-4">
                {{ $verses->appends(['query' => $query])->links() }}
            </div>
        @else
            <p class="text-center">No results found for "{{ $query }}".</p>
        @endif
    </div>
@endsection
