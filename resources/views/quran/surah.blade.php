@extends('layouts.app')

@section('title', $surahInfo['name'])

@section('content')
<!-- Surah Header -->
<div class="bg-gradient-to-r from-islamic-green-900 via-islamic-green-800 to-islamic-green-900 text-white py-8 shadow-lg">
  <div class="container mx-auto px-6 text-center">
    <h1 class="font-quran text-6xl md:text-7xl mb-4 tracking-wider text-shadow-md">{{ $surahInfo['name'] }}</h1>
    <div class="flex flex-wrap justify-center items-center gap-4 mt-4">
      <div class="flex items-center space-x-2">
        <i class="fas fa-book-open text-islamic-gold-500"> </i>
        <p class="text-lg">
          <span class="opacity-80"> عدد الآيات: </span> {{ count($ayahs) }}
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fas fa-layer-group text-islamic-gold-500"></i>
        <p class="text-lg" id="juz-info">
          <span class="opacity-80">الجزء:</span> <span id="juz-number">-</span>
        </p>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fas fa-bookmark text-islamic-gold-500"></i>
        <p class="text-lg" id="hizb-info">
          <span class="opacity-80"> الحزب: </span> <span id="hizb-number"> - </span>
        </p>
      </div>
      <span class="inline-block px-4 py-1 rounded-full text-sm font-semibold bg-{{ $surahInfo['revelationType'] === 'Meccan' ? 'islamic-gold-500' : 'islamic-green-600' }} shadow-sm">
        {{ $surahInfo['revelationType'] === 'Meccan' ? 'مكية' : 'مدنية' }}
      </span>
    </div>
  </div>
</div>


  <!-- Quran Page Container -->
  <div class="container mx-auto px-4 py-8">
    <!-- Mushaf Frame with decorative header -->
    <div class="mushaf-container">
      <div class="mushaf-header"></div>

      <div class="quran-page">
        <div class="quran-content">
          @if ($surahInfo['number'] != 9)
            <div class="bismillah">
              بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
            </div>
          @endif

          <div class="ayahs-text" dir="rtl">
            @foreach ($ayahs as $ayah)
              <span class="ayah-text" data-ayah="{{ $ayah['number'] }}" id="ayah-{{ $ayah['number'] }}">
                {{ $ayah['arabic_text'] }}
                <span class="ayah-number">{{ $ayah['number'] }}</span>
              </span>
            @endforeach
          </div>
        </div>
      </div>

      <div class="mushaf-footer"></div>
    </div>
  </div>

  <!-- Pagination -->
  <div class="container mx-auto px-4 py-6 flex justify-between items-center">
    @if ($surahInfo['number'] > 1)
      <a href="{{ route('quran.surah', $surahInfo['number'] - 1) }}"
        class="btn-navigation">
        السورة السابقة
      </a>
    @else
      <div></div>
    @endif

    <a href="{{ route('quran.index') }}" class="btn-main">فهرس السور</a>

    @if ($surahInfo['number'] < 114)
      <a href="{{ route('quran.surah', $surahInfo['number'] + 1) }}"
        class="btn-navigation">
        السورة التالية
      </a>
    @else
      <div></div>
    @endif
  </div>

  <style>
    /* 🔹 Enhanced Quranic Typography */
    @import url('https://fonts.googleapis.com/css2?family=Amiri&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Scheherazade+New&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic&display=swap');

    :root {
      --mushaf-bg-color: #f8f3e6;
      --mushaf-border-color: #0a512a;
      --mushaf-text-color: #101810;
      --bismillah-color: #0a512a;
      --ayah-number-color: #0a512a;
      --gold-accent: #d4af37;
    }

    .font-quran {
      font-family: 'Noto Naskh Arabic', 'Scheherazade New', 'Amiri Quran', serif;
      letter-spacing: 0.01em;
    }

    /* 🔹 Mushaf Container Styling */
    .mushaf-container {
      position: relative;
      max-width: 800px;
      margin: 0 auto;
    }

    .mushaf-header {
      height: 40px;
      background-image: url('/images/mushaf-header.png');
      background-size: 100% 100%;
      background-repeat: no-repeat;
    }

    .mushaf-footer {
      height: 40px;
      background-image: url('/images/mushaf-footer.png');
      background-size: 100% 100%;
      background-repeat: no-repeat;
    }

    /* 🔹 Quran Page Styling - Exact match to traditional pages */
    .quran-page {
      position: relative;
      background-color: var(--mushaf-bg-color);
      background-image: url('/images/paper-texture.png');
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      padding: 0;
      overflow: hidden;
    }

      .quran-page::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border: 20px solid transparent;
      border-image: url('/images/quran-border.png') 40 round;
      pointer-events: none;
    }

    .quran-content {
      position: relative;
      padding: 20px 30px;
      z-index: 5;
    }

    /* 🔹 Bismillah Styling */
    .bismillah {
      margin: 15px 0 30px;
      color: var(--bismillah-color);
      font-family: 'Noto Naskh Arabic', 'Scheherazade New', serif;
      font-size: 2.3rem;
      line-height: 1.7;
      text-align: center;
      letter-spacing: 0.01em;
    }

    /* 🔹 Ayah Text Styling to match traditional Quran */
    .ayahs-text {
      font-family: 'Noto Naskh Arabic', 'Scheherazade New', serif;
      font-size: 24px;
      line-height: 2.7;
      text-align: justify;
      color: var(--mushaf-text-color);
      padding: 0;
      word-spacing: 0.1em;
    }

    .ayah-text {
      position: relative;
      cursor: pointer;
      transition: background-color 0.3s ease;
      border-radius: 4px;
      padding: 3px 0;
    }

    .ayah-text:hover {
      background-color: rgba(10, 81, 42, 0.05);
    }

    .ayah-text.highlight {
      background-color: rgba(212, 175, 55, 0.15);
    }

    /* 🔹 Ayah Number Styling - circle similar to traditional Quran */
    .ayah-number {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      width: 24px;
      height: 24px;
      background-image: url('/images/ayah-circle.svg');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      font-size: 12px;
      color: var(--ayah-number-color);
      margin: 0 3px;
      vertical-align: middle;
      position: relative;
      top: -2px;
    }

    /* Fallback for ayah number */
    .ayah-number::before {
      content: "";
      position: absolute;
      width: 24px;
      height: 24px;
      border: 1px solid var(--ayah-number-color);
      border-radius: 50%;
      z-index: -1;
    }

    /* 🔹 Translation Section */
    .translations-container {
      background-color: rgba(250, 250, 250, 0.7);
      border-radius: 6px;
      padding: 15px;
    }

    .translation-item {
      margin-bottom: 12px;
    }

    /* 🔹 Navigation Buttons */
    .btn-navigation {
      background: var(--gold-accent);
      color: white;
      padding: 8px 16px;
      border-radius: 5px;
      transition: all 0.3s;
      font-weight: bold;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-navigation:hover {
      background: #b8860b;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .btn-main {
      background: var(--mushaf-border-color);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      transition: all 0.3s;
      font-weight: bold;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .btn-main:hover {
      background: #074020;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* 🔹 Responsive Design */
    @media (max-width: 768px) {
      .ayahs-text {
        font-size: 20px;
        line-height: 2.4;
      }

      .bismillah {
        font-size: 1.8rem;
      }

      .quran-content {
        padding: 15px;
      }
    }
  </style>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Save and highlight last read ayah
      const ayahs = document.querySelectorAll(".ayah-text");
      const lastRead = localStorage.getItem("lastReadAyah");

      // Function to highlight and scroll to ayah
      function highlightAyah(ayahElement) {
        // Remove highlight from all ayahs
        ayahs.forEach(a => a.classList.remove("highlight"));

        // Add highlight to clicked ayah
        ayahElement.classList.add("highlight");

        // Save to localStorage
        localStorage.setItem("lastReadAyah", ayahElement.dataset.ayah);

        // Scroll to the ayah and its translation
        ayahElement.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Also highlight corresponding translation
        const transId = "trans-" + ayahElement.dataset.ayah;
        const transElement = document.getElementById(transId);
        if (transElement) {
          setTimeout(() => {
            transElement.classList.add("bg-islamic-green-50");
            setTimeout(() => {
              transElement.classList.remove("bg-islamic-green-50");
            }, 2000);
          }, 800);
        }
      }

      // Add click event to each ayah
      ayahs.forEach(ayah => {
        ayah.addEventListener("click", function() {
          highlightAyah(ayah);
        });
      });

      // Highlight last read ayah if available
      if (lastRead) {
        const lastAyah = document.querySelector(`[data-ayah="${lastRead}"]`);
        if (lastAyah) {
          // Delay to ensure page is fully loaded
          setTimeout(() => {
            highlightAyah(lastAyah);
          }, 500);
        }
      }

      // Add page turning visual effect (optional)
      document.querySelectorAll('.btn-navigation, .btn-main').forEach(btn => {
        btn.addEventListener('click', function(e) {
          // We're not preventing default here to allow normal navigation
          const pageElement = document.querySelector('.quran-page');
          pageElement.classList.add('page-turn');

          // Remove the class after animation completes
          setTimeout(() => {
            pageElement.classList.remove('page-turn');
          }, 500);
        });
      });
    });
    
    document.addEventListener('DOMContentLoaded', function() {
  // Surah to Juz and Hizb mapping
  const surahJuzHizbMap = {
    1: {juz: "1", hizb: "1"},
    2: {juz: "1-3", hizb: "1-6"},
    3: {juz: "3-4", hizb: "6-8"},
    4: {juz: "4-6", hizb: "8-11"},
    5: {juz: "6-7", hizb: "11-14"},
    6: {juz: "7-8", hizb: "14-16"},
    7: {juz: "8-9", hizb: "16-18"},
    8: {juz: "9-10", hizb: "18-19"},
    9: {juz: "10-11", hizb: "19-22"},
    10: {juz: "11", hizb: "22"},
    11: {juz: "11-12", hizb: "22-24"},
    12: {juz: "12-13", hizb: "24-26"},
    13: {juz: "13", hizb: "26"},
    14: {juz: "13", hizb: "26-27"},
    15: {juz: "13-14", hizb: "27-28"},
    16: {juz: "14", hizb: "28"},
    17: {juz: "15", hizb: "29-30"},
    18: {juz: "15-16", hizb: "30-32"},
    19: {juz: "16", hizb: "32"},
    20: {juz: "16", hizb: "32-33"},
    21: {juz: "17", hizb: "33-34"},
    22: {juz: "17", hizb: "34-35"},
    23: {juz: "18", hizb: "35-36"},
    24: {juz: "18", hizb: "36-37"},
    25: {juz: "18-19", hizb: "37-38"},
    26: {juz: "19", hizb: "38-39"},
    27: {juz: "19-20", hizb: "39-40"},
    28: {juz: "20", hizb: "40-41"},
    29: {juz: "20-21", hizb: "41-42"},
    30: {juz: "21", hizb: "42"},
    31: {juz: "21", hizb: "42-43"},
    32: {juz: "21", hizb: "43"},
    33: {juz: "21-22", hizb: "43-44"},
    34: {juz: "22", hizb: "44-45"},
    35: {juz: "22", hizb: "45"},
    36: {juz: "22-23", hizb: "45-46"},
    37: {juz: "23", hizb: "46-47"},
    38: {juz: "23", hizb: "47"},
    39: {juz: "23-24", hizb: "47-48"},
    40: {juz: "24", hizb: "48-49"},
    41: {juz: "24-25", hizb: "49-50"},
    42: {juz: "25", hizb: "50-51"},
    43: {juz: "25", hizb: "51"},
    44: {juz: "25", hizb: "51-52"},
    45: {juz: "25", hizb: "52"},
    46: {juz: "26", hizb: "52-53"},
    47: {juz: "26", hizb: "53"},
    48: {juz: "26", hizb: "53-54"},
    49: {juz: "26", hizb: "54"},
    50: {juz: "26", hizb: "54"},
    51: {juz: "26-27", hizb: "54-55"},
    52: {juz: "27", hizb: "55"},
    53: {juz: "27", hizb: "55-56"},
    54: {juz: "27", hizb: "56"},
    55: {juz: "27", hizb: "56"},
    56: {juz: "27", hizb: "56-57"},
    57: {juz: "27", hizb: "57"},
    58: {juz: "28", hizb: "57-58"},
    59: {juz: "28", hizb: "58"},
    60: {juz: "28", hizb: "58"},
    61: {juz: "28", hizb: "58-59"},
    62: {juz: "28", hizb: "59"},
    63: {juz: "28", hizb: "59"},
    64: {juz: "28", hizb: "59-60"},
    65: {juz: "28", hizb: "60"},
    66: {juz: "28", hizb: "60"},
    67: {juz: "29", hizb: "60-61"},
    68: {juz: "29", hizb: "61"},
    69: {juz: "29", hizb: "61-62"},
    70: {juz: "29", hizb: "62"},
    71: {juz: "29", hizb: "62"},
    72: {juz: "29", hizb: "62-63"},
    73: {juz: "29", hizb: "63"},
    74: {juz: "29", hizb: "63"},
    75: {juz: "29", hizb: "63-64"},
    76: {juz: "29", hizb: "64"},
    77: {juz: "29", hizb: "64"},
    78: {juz: "30", hizb: "64-65"},
    79: {juz: "30", hizb: "65"},
    80: {juz: "30", hizb: "65"},
    81: {juz: "30", hizb: "65"},
    82: {juz: "30", hizb: "65"},
    83: {juz: "30", hizb: "65-66"},
    84: {juz: "30", hizb: "66"},
    85: {juz: "30", hizb: "66"},
    86: {juz: "30", hizb: "66"},
    87: {juz: "30", hizb: "66"},
    88: {juz: "30", hizb: "66"},
    89: {juz: "30", hizb: "66-67"},
    90: {juz: "30", hizb: "67"},
    91: {juz: "30", hizb: "67"},
    92: {juz: "30", hizb: "67"},
    93: {juz: "30", hizb: "67"},
    94: {juz: "30", hizb: "67"},
    95: {juz: "30", hizb: "67"},
    96: {juz: "30", hizb: "67"},
    97: {juz: "30", hizb: "67"},
    98: {juz: "30", hizb: "67-68"},
    99: {juz: "30", hizb: "68"},
    100: {juz: "30", hizb: "68"},
    101: {juz: "30", hizb: "68"},
    102: {juz: "30", hizb: "68"},
    103: {juz: "30", hizb: "68"},
    104: {juz: "30", hizb: "68"},
    105: {juz: "30", hizb: "68"},
    106: {juz: "30", hizb: "68"},
    107: {juz: "30", hizb: "68"},
    108: {juz: "30", hizb: "68"},
    109: {juz: "30", hizb: "68"},
    110: {juz: "30", hizb: "68"},
    111: {juz: "30", hizb: "68"},
    112: {juz: "30", hizb: "68"},
    113: {juz: "30", hizb: "68"},
    114: {juz: "30", hizb: "68"}
  };

  const surahNumber = {{ $surahInfo['number'] }};

  if (surahJuzHizbMap[surahNumber]) {
    document.getElementById('juz-number').textContent = surahJuzHizbMap[surahNumber].juz;
    document.getElementById('hizb-number').textContent = surahJuzHizbMap[surahNumber].hizb;
  }
});
  </script>
@endsection
