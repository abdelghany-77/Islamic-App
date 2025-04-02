@extends('layouts.app')

@section('title', $story->title)

@section('content')
  <!-- Enhanced Story Header with Islamic Geometric Pattern -->
  <div class="relative bg-gradient-to-b from-islamic-green-900 to-islamic-green-800 text-white py-12 md:py-16 overflow-hidden">
    <!-- Islamic Geometric Pattern Overlay -->
    <div class="absolute inset-0 opacity-10">
      <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 1000 1000">
        <pattern id="islamic-pattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
          <path fill="white" d="M50,0 L100,25 L100,75 L50,100 L0,75 L0,25 Z"></path>
          <path fill="none" stroke="white" stroke-width="1" d="M50,0 L100,25 L100,75 L50,100 L0,75 L0,25 Z"></path>
          <path fill="none" stroke="white" stroke-width="1" d="M50,25 L50,75"></path>
          <path fill="none" stroke="white" stroke-width="1" d="M25,12.5 L75,87.5"></path>
          <path fill="none" stroke="white" stroke-width="1" d="M25,87.5 L75,12.5"></path>
        </pattern>
      </svg>
    </div>
    <div class="container mx-auto px-4 relative z-10">
      <div class="max-w-3xl mx-auto text-center">
        <!-- Decorative Islamic Divider -->
        <div class="flex justify-center mb-6">
          <svg width="120" height="30" viewBox="0 0 120 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M60 0L67 10H53L60 0Z" fill="#d4af37"/>
            <path d="M40 10L47 20H33L40 10Z" fill="#d4af37"/>
            <path d="M80 10L87 20H73L80 10Z" fill="#d4af37"/>
            <path d="M20 20L27 30H13L20 20Z" fill="#d4af37"/>
            <path d="M60 20L67 30H53L60 20Z" fill="#d4af37"/>
            <path d="M100 20L107 30H93L100 20Z" fill="#d4af37"/>
            <path d="M0 30L7 20H-7L0 30Z" fill="#d4af37"/>
            <path d="M120 30L127 20H113L120 30Z" fill="#d4af37"/>
          </svg>
        </div>

        <span
          class="inline-block bg-islamic-gold-500 text-islamic-green-900 px-5 py-2 rounded-full text-sm mb-6 shadow-lg font-arabic font-medium border-2 border-islamic-gold-300">
          {{ $story->category }}
        </span>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 font-arabic leading-tight animate-fade-in">
          {{ $story->title }}
        </h1>
        <p class="text-lg md:text-xl opacity-90 mb-8 font-arabic leading-relaxed max-w-2xl mx-auto">
          {{ $story->description }}
        </p>
        <div class="flex justify-center items-center text-sm opacity-80 space-x-6 space-x-reverse">
          <span class="font-arabic flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
            المصدر: {{ $story->source ?? 'غير محدد' }}
          </span>
          <span class="text-islamic-gold-200">•</span>
          <span class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-2" viewBox="0 0 20 20" fill="currentColor">
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
              <path fill-rule="evenodd"
                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                clip-rule="evenodd" />
            </svg>
            {{ $story->view_count }} مشاهدة
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Story Content with Enhanced Typography -->
  <div class="container mx-auto px-4 py-8 md:py-16">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-6 md:p-8 space-y-12 border-t-4 border-islamic-gold-500">
      <!-- Image Section with Enhanced Presentation -->
      @if ($story->image_url)
        <div class="relative rounded-lg overflow-hidden shadow-md mb-8 border border-islamic-gold-200">
          <div class="absolute top-0 right-0 bg-islamic-green-800 text-white px-4 py-2 text-sm font-arabic rounded-bl-lg z-10 opacity-90">
            قصة إسلامية
          </div>
          <img src="{{ $story->image_url }}" alt="{{ $story->title }}" class="w-full h-auto object-cover">
          <!-- Optional Islamic Frame Overlay -->
          <div class="absolute inset-0 pointer-events-none border-8 border-islamic-gold-500 opacity-10 rounded-lg"></div>
        </div>
      @endif

      <!-- Decorative Islamic Divider -->
      <div class="flex justify-center my-6">
        <svg width="200" height="20" viewBox="0 0 200 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M100 0L105 10H95L100 0Z" fill="#164E38"/>
          <line x1="10" y1="10" x2="90" y2="10" stroke="#164E38" stroke-width="1" stroke-dasharray="2 2"/>
          <line x1="110" y1="10" x2="190" y2="10" stroke="#164E38" stroke-width="1" stroke-dasharray="2 2"/>
          <circle cx="10" cy="10" r="3" fill="#d4af37"/>
          <circle cx="190" cy="10" r="3" fill="#d4af37"/>
        </svg>
      </div>

      <!-- Description Section with Enhanced Typography -->
      <div class="prose prose-lg max-w-none font-arabic text-gray-800 leading-relaxed text-right">
        <!-- Optional verse/hadith highlight at the beginning -->
        @if (isset($story->highlightedVerse))
          <div class="bg-islamic-green-50 border-r-4 border-islamic-green-700 p-4 my-6 rounded-lg">
            <p class="text-islamic-green-800 text-xl">{{ $story->highlightedVerse }}</p>
          </div>
        @endif

        <!-- Main content -->
        <div class="leading-loose">
          {!! $story->content !!}
        </div>
      <!-- Video Section  -->
      @if ($story->getEmbeddedVideoHtml())
        <div class="my-12">
          <div class="bg-white rounded-lg shadow-md overflow-hidden border border-islamic-green-100">
            <!-- Title with Minaret Icon -->
            <h3 class="bg-islamic-green-700 text-white text-xl md:text-2xl font-bold py-3 px-4 font-arabic flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              شاهد الفيديو
            </h3>
            <div class="relative border border-gray-200">
              <div class="aspect-w-16 aspect-h-9">
                {!! $story->getEmbeddedVideoHtml() !!}
              </div>
            </div>
          </div>
        </div>
      @endif

      <!-- Share Buttons-->
      <div class="mt-12 pt-8 border-t border-gray-200">
        <h4 class="text-xl font-bold mb-6 font-arabic text-islamic-green-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
          </svg>
          شارك هذه القصة
        </h4>
        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" rel="noopener"
            class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                clip-rule="evenodd"></path>
            </svg>
            <span class="font-arabic">فيسبوك</span>
          </a>
          <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $story->title }}" target="_blank" rel="noopener"
            class="flex items-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg hover:bg-sky-600 transition duration-300 shadow-md">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84">
              </path>
            </svg>
            <span class="font-arabic">تويتر</span>
          </a>
          <a href="https://api.whatsapp.com/send?text={{ $story->title }} {{ url()->current() }}" target="_blank" rel="noopener"
            class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 shadow-md">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M21.105 4.893c-1.216-1.217-2.828-1.888-4.546-1.888-3.542 0-6.425 2.883-6.425 6.425 0 1.126.295 2.225.854 3.193l-0.907 3.32 3.393-.889c0.933.508 1.98.776 3.044.776h.003c3.54 0 6.425-2.885 6.425-6.428 0-1.717-.671-3.33-1.888-4.545l.047.036zM16.558 19.146h-.002c-.96 0-1.902-.258-2.723-.744l-.195-.116-2.027.532.54-1.975-.127-.202c-.54-.858-.825-1.848-.825-2.868 0-2.972 2.42-5.392 5.394-5.392 1.44 0 2.793.561 3.812 1.58 1.018 1.019 1.58 2.37 1.58 3.811 0 2.973-2.42 5.394-5.393 5.394l-.035-.02zm2.962-4.045c-.162-.081-0.96-.475-1.108-.529-.149-.054-.258-.081-.366.081-.108.163-.42.529-.514.637-.095.108-.19.121-.351.041-.162-.081-.683-.252-1.3-.803-.48-.428-.805-.957-.9-1.12-.095-.162-.01-.25.071-.331.073-.073.163-.19.244-.285.081-.095.108-.163.162-.271.054-.108.027-.202-.013-.285-.041-.081-.366-.882-.501-1.207-.132-.317-.266-.274-.366-.279-.095-.005-.204-.005-.312-.005s-.285.041-.433.203c-.149.163-.57.557-.57 1.36 0 .801.585 1.574.667 1.682.081.108 1.146 1.749 2.775 2.454.388.167.69.267.925.343.389.124.743.106 1.023.064.312-.046.96-.392 1.095-.771.135-.379.135-.704.095-.771-.04-.067-.149-.107-.311-.188h.01z"
                clip-rule="evenodd"></path>
            </svg>
            <span class="font-arabic">واتساب</span>
          </a>
        </div>
      </div>

      <!-- Related Stories -->
      @if ($relatedStories->count() > 0)
        <div class="mt-16 pt-8 border-t border-gray-200">
          <h3 class="text-2xl font-bold mb-8 font-arabic text-islamic-green-800 text-center md:text-right flex items-center justify-end">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            قصص ذات صلة
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($relatedStories as $relatedStory)
              <a href="{{ route('stories.show', $relatedStory->id) }}"
                class="block transition duration-300 hover:shadow-xl transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-islamic-green-500 rounded-lg">
                <div class="bg-white rounded-lg overflow-hidden shadow border border-gray-100 h-full relative">
                  <!-- Islamic pattern corner accent -->
                  <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden opacity-20 pointer-events-none">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                      <path fill="#164E38" d="M0,0 L100,0 L100,100 Z"></path>
                    </svg>
                  </div>

                  @if ($relatedStory->image_url)
                    <div class="h-44 overflow-hidden">
                      <img src="{{ $relatedStory->image_url }}" alt="{{ $relatedStory->title }}"
                        class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                    </div>
                  @endif
                  <div class="p-5">
                    <h3 class="text-lg font-bold text-islamic-green-900 mb-3 font-arabic leading-tight">
                      {{ $relatedStory->title }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                      {{ Str::limit($relatedStory->description, 80) }}
                    </p>
                    <span class="text-islamic-green-700 font-semibold text-sm font-arabic hover:underline inline-flex items-center">
                      اقرأ المزيد
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                      </svg>
                    </span>
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @endif

      <!--  Call-to-Action Section -->
      <div class="mt-16 pt-8 flex flex-col md:flex-row gap-6 items-center justify-center border-t border-gray-200">
        <a href="{{ route('stories.index') }}" class="group bg-islamic-green-700 hover:bg-islamic-green-800 text-white px-6 py-3 rounded-lg flex items-center gap-3 font-arabic text-lg font-medium transition-all transform hover:-translate-y-1 hover:shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          اكتشف المزيد من القصص
        </a>
      </div>
    </div>
  </div>
@endsection

<style>
  @font-face {
    font-family: 'Mishafi';
    src: url('/fonts/MishafiGold.ttf') format('truetype');
    font-display: swap;
  }

  @font-face {
    font-family: 'Scheherazade';
    src: url('/fonts/ScheherazadeNew-Regular.ttf') format('truetype');
    font-display: swap;
  }

  :root {
    --primary-color: #164E38;
    --primary-light: #20724f;
    --primary-dark: #0d3929;
    --gold-color: #d4af37;
    --gold-light: #e6c860;
    --gold-dark: #b59429;
    --text-color: #333333;
    --bg-color: #f8f9fa;
  }

  body {
    background-color: var(--bg-color);
    color: var(--text-color);
    line-height: 1.6;
  }

  /* Enhanced Islamic Typography */
  .font-arabic {
    font-family: 'Scheherazade', 'Amiri', 'Traditional Arabic', serif;
  }

  .prose {
    font-family: 'Scheherazade', 'Amiri', 'Traditional Arabic', serif;
    font-size: 1.25rem;
    line-height: 1.9;
    color: #333;
    text-align: right;
    direction: rtl;
  }

  .prose p {
    margin-bottom: 1.5rem;
    text-align: justify;
    text-align-last: right;
  }

  .prose p:first-of-type::first-letter {
    font-size: 1.5em;
    font-weight: bold;
    color: var(--primary-color);
  }

  .prose strong {
    color: var(--primary-color);
    font-weight: 700;
  }

  /* Verse styling */
  .prose blockquote {
    border-right: 4px solid var(--gold-color);
    padding-right: 1rem;
    margin-right: 0;
    font-style: italic;
    color: #555;
    background-color: rgba(212, 175, 55, 0.1);
    padding: 1rem;
    border-radius: 0.5rem;
  }

  /* Decorative Islamic page elements */
  .islamic-divider {
    height: 2px;
    background: linear-gradient(to right, transparent, var(--gold-color), transparent);
    margin: 2rem 0;
  }

  img {
    max-width: 100%;
    height: auto;
  }

  .animate-fade-in {
    animation: fadeIn 1s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Improved responsive adjustments */
  @media (max-width: 768px) {
    .prose {
      font-size: 1.1rem;
      line-height: 1.8;
    }

    .container {
      padding-left: 16px;
      padding-right: 16px;
    }
  }
  @media (prefers-color-scheme: dark) {
    body {
      background-color: #1a1a1a;
      color: #f0f0f0;
    }

    .prose {
      color: #f0f0f0;
    }

    .bg-white {
      background-color: #2a2a2a;
    }

    .text-islamic-green-900 {
      color: #d4af37;
    }
  }
</style>
