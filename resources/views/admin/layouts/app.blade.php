<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم')</title>

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Arabic Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Almarai:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --islamic-green: #1a472a;
            --islamic-gold: #d4af37;
            --parchment: #f8f3e6;
        }

        body {
            font-family: 'Amiri', serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .islamic-sidebar {
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            right: 0;
            background: linear-gradient(180deg, var(--islamic-green), #0d2e1c);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .islamic-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/images/arabesque-pattern-light.png') repeat;
            opacity: 0.05;
            pointer-events: none;
        }

        .islamic-sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            margin-bottom: 1rem;
        }

        .islamic-sidebar-header h4 {
            font-family: 'Amiri', serif;
            font-weight: 700;
            text-align: center;
            color: white;
            margin: 0;
        }

        .islamic-sidebar-header h4 i {
            color: var(--islamic-gold);
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            font-family: 'Amiri', serif;
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0;
            border-right: 3px solid transparent;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(212, 175, 55, 0.2);
            border-right-color: var(--islamic-gold);
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            margin-left: 0.5rem;
        }

        .main-content {
            margin-right: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        .alert {
            border-right: 5px solid;
            font-family: 'Amiri', serif;
        }

        @media (max-width: 992px) {
            .islamic-sidebar {
                width: 70px;
                overflow: hidden;
            }

            .islamic-sidebar-header h4 span {
                display: none;
            }

            .nav-link span {
                display: none;
            }

            .nav-link i {
                margin-left: 0;
                font-size: 1.2rem;
            }

            .main-content {
                margin-right: 70px;
            }
        }

        @media (max-width: 768px) {
            .islamic-sidebar {
                width: 100%;
                height: 60px;
                min-height: auto;
                bottom: 0;
                top: auto;
            }

            .sidebar-nav {
                display: flex;
                flex-direction: row;
            }

            .nav-item {
                flex: 1;
                text-align: center;
            }

            .nav-link {
                padding: 0.5rem;
                border-right: none;
                border-top: 3px solid transparent;
            }

            .nav-link:hover, .nav-link.active {
                border-right: none;
                border-top-color: var(--islamic-gold);
            }

            .islamic-sidebar-header {
                display: none;
            }

            .main-content {
                margin-right: 0;
                padding-bottom: 80px;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Islamic Sidebar -->
    <nav class="islamic-sidebar">
        <div class="islamic-sidebar-header">
            <h4>
                <i class="fas fa-mosque"></i>
                <span>لوحة التحكم</span>
            </h4>
        </div>

        <ul class="nav flex-column sidebar-nav">
            {{-- <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
            </li> --}}

            <li class="nav-item">
                <a href="{{ route('admin.stories.index') }}" class="nav-link {{ request()->routeIs('admin.stories.*') ? 'active' : '' }}">
                    <i class="fas fa-book-quran"></i>
                    <span>القصص الإسلامية</span>
                </a>
            </li>
{{--
            <li class="nav-item mt-auto">
                <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li> --}}
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-right-color: var(--islamic-green);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-right-color: #dc3545;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>
                    <h6 class="mb-1">حدثت الأخطاء التالية:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
