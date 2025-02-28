<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Islamic App') }} - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri+Quran&family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9f5e9;
            color: #333;
            font-family: 'Open Sans', sans-serif;
        }
        .navbar {
            background: #1f4a38;
        }
        .navbar a, .navbar .btn {
            color: #f9f5e9;
        }
        .navbar a:hover, .navbar .btn:hover {
            color: #d4a017;
        }
        .quran-text {
            font-family: 'Amiri Quran', serif;
            font-size: 2rem;
            line-height: 3rem;
            text-align: right;
        }
        .translation {
            font-size: 1.2rem;
            line-height: 1.8rem;
            text-align: left;
        }
        .verse {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
        }
        .highlighted {
            background: #ffffcc;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('Islamic App') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('quran.index') }}">Quran</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('hadith.index') }}">Hadith</a></li>
                </ul>
                <form class="d-flex ms-auto" action="{{ route('quran.search') }}" method="GET">
                    <input class="form-control me-2" type="text" name="query" placeholder="Search Surah or Ayah (e.g., 1:1)" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p>© {{ date('Y') }} Islamic App</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
