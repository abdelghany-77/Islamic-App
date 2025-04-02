<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', 'تذكير') }} - @yield('title', 'الرئيسية')</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font for Arabic text -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Amiri+Quran&display=swap"
    rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'islamic-green': {
              '50': '#f1f8f4',
              '100': '#dcefe3',
              '200': '#bde0cb',
              '300': '#94c8aa',
              '400': '#68aa84',
              '500': '#4b8e67',
              '600': '#3a7253',
              '700': '#305c45',
              '800': '#294a39',
              '900': '#243e31',
              '950': '#0f2119',
            },
            'islamic-gold': {
              '50': '#fef8ec',
              '100': '#fcedcf',
              '200': '#f9d89e',
              '300': '#f5bb62',
              '400': '#f1a035',
              '500': '#ec8216',
              '600': '#d86711',
              '700': '#b44e12',
              '800': '#933e16',
              '900': '#793516',
              '950': '#411a08',
            }
          },
          fontFamily: {
            'arabic': ['Amiri', 'serif']
          }
        }
      }
    }
  </script>

  <style>
    .font-arabic {
      font-family: 'Amiri', serif;
    }

    [dir="rtl"] {
      text-align: right;
    }
  </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-50">
  <!-- Header -->
  <header class="bg-islamic-green-800 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3">
      <div class="flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-2xl font-bold flex items-center font-arabic">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 ml-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          تذكير
        </a>

        <!-- Mobile menu button -->
        <button id="mobile-menu-button" class="md:hidden focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-6">
          <a href="{{ route('home') }}"
            class="hover:text-islamic-gold-200 transition duration-300 font-arabic">الرئيسية</a>
          <pre></pre>
          <a href="{{ route('quran.index') }}"
            class="hover:text-islamic-gold-200 transition duration-300 font-arabic">القرآن الكريم</a>
          <a href="{{ route('azkar.index') }}"
            class="hover:text-islamic-gold-200 transition duration-300 font-arabic">الأذكار</a>
          <a href="{{ route('duaa.index') }}"
            class="hover:text-islamic-gold-200 transition duration-300 font-arabic">الأدعية</a>
          <a href="{{ route('stories.index') }}"
            class="hover:text-islamic-gold-200 transition duration-300 font-arabic">القصص الإسلامية</a>

        </nav>
      </div>
    </div>

    <!-- Mobile Navigation -->
    <div id="mobile-menu" class="md:hidden hidden bg-islamic-green-900 pb-4">
      <div class="container mx-auto px-4">
        <a href="{{ route('home') }}"
          class="block py-2 hover:bg-islamic-green-700 px-3 rounded my-1 font-arabic">الرئيسية</a>
        <a href="{{ route('quran.index') }}"
          class="block py-2 hover:bg-islamic-green-700 px-3 rounded my-1 font-arabic">القرآن الكريم</a>
        <a href="{{ route('azkar.index') }}"
          class="block py-2 hover:bg-islamic-green-700 px-3 rounded my-1 font-arabic">الأذكار</a>
        <a href="{{ route('duaa.index') }}"
          class="block py-2 hover:bg-islamic-green-700 px-3 rounded my-1 font-arabic">الأدعية</a>
        <a href="{{ route('stories.index') }}"
          class="hover:text-islamic-gold-200 transition duration-300 font-arabic">القصص الإسلامية</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow">
    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="bg-islamic-green-900 text-white py-8">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4 text-islamic-gold-300 font-arabic">تذكير</h3>
          <p class="mb-4 font-arabic">مصدر شامل للمسلمين الباحثين عن المعرفة والإرشاد.</p>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-4 text-islamic-gold-300 font-arabic">روابط سريعة</h3>
          <ul class="space-y-2">
            <li><a href="{{ route('quran.index') }}" class="hover:text-islamic-gold-200 transition font-arabic"> القرآن
                الكريم</a></li>
            <li><a href="{{ route('azkar.index') }}"
                class="hover:text-islamic-gold-200 transition font-arabic">الأذكار</a></li>
            <li><a href="{{ route('duaa.index') }}"
                class="hover:text-islamic-gold-200 transition font-arabic">الأدعية</a></li>
            <li><a href="{{ route('stories.index') }}" class="hover:text-islamic-gold-200 transition font-arabic">القصص
                الإسلامية</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-4 text-islamic-gold-300 font-arabic">اتصل بنا</h3>
          <p class="font-arabic">لأي أسئلة أو تعليقات:</p>
          <p class="mt-2 font-arabic">tazkeer@gmail.com</p>
        </div>
      </div>
      <div class="border-t border-islamic-green-800 mt-8 pt-4 text-center text-sm font-arabic">
        © {{ date('Y') }} تذكير . جميع الحقوق محفوظة.
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      document.getElementById('mobile-menu').classList.toggle('hidden');
    });
  </script>
</body>

</html>
