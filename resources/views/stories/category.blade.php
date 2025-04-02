
@extends('layouts.app')

@section('title',$category)

@section('content')
    <!-- Page Header -->
    <div class="bg-islamic-green-900 text-white py-10">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-3"> {{ $category }}</h1>
            <p class="text-xl opacity-90">استكشف القصص الإسلامية من {{ $category }}</p>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="container mx-auto px-4 py-10">
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">التصنيفات</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('stories.index') }}"
                   class="px-4 py-2 rounded-full bg-islamic-green-100 text-islamic-green-800 hover:bg-islamic-green-200 transition">
                    الكل
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('stories.category', $cat) }}"
                       class="px-4 py-2 rounded-full {{ $category === $cat ? 'bg-islamic-green-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' }} transition">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Stories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($stories as $story)
                <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition duration-300">
                    @if($story->image_url)
                        <div class="h-40 overflow-hidden">
                            <img src="{{ $story->image_url }}"
                                 alt="{{ $story->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="bg-islamic-gold-100 text-islamic-gold-800 text-xs px-2 py-1 rounded">
                                {{ $story->category }}
                            </span>
                            @if($story->video_url)
                                <span class="text-islamic-green-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-islamic-green-900 mb-2">{{ $story->title }}</h3>
                        <p class="text-gray-700 mb-4">{{ Str::limit($story->description, 80) }}</p>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('stories.show', $story->id) }}"
                               class="text-islamic-green-700 font-semibold hover:text-islamic-green-800">
                                اقرأ المزيد
                            </a>
                            <span class="text-sm text-gray-500">
                                <span class="inline-block mr-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                {{ $story->view_count }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-600">لا توجد قصص في هذا التصنيف حالياً.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $stories->links() }}
        </div>
    </div>
@endsection
