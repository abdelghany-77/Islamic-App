@extends('layouts.app')

@section('title', 'Hadith Collections')

@section('header')
<div class="main-header">
    <div class="container text-center">
        <h1 style="font-family: 'Lateef', cursive; font-size: 2.5rem;">Hadith Collections</h1>
    </div>
</div>
@endsection

@section('content')
<div class="book-container">
    <h3 style="font-family: 'Lateef', cursive; color: var(--green); text-align: center; margin-bottom: 2rem;">Collections</h3>
    <div class="row g-4">
        @foreach($books as $book)
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('hadith.book', $book) }}" class="text-decoration-none">
                <div class="card p-3" style="background: #fffef5; border: 1px solid var(--gold);">
                    <h5 style="color: var(--green);">{{ $book->name }}</h5>
                    <p style="color: var(--dark);">{{ $book->author }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
