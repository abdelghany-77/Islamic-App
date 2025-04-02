<?php

namespace App\Http\Controllers;

use App\Models\IslamicStory;
use Illuminate\Http\Request;

class IslamicStoryController extends Controller
{
    /**
     * Display a listing of the stories.
     */
    public function index()
    {
        $featuredStories = IslamicStory::where('featured', true)
            ->latest()
            ->take(3)
            ->get();

        $categories = IslamicStory::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        $stories = IslamicStory::latest()->paginate(9);

        return view('stories.index', compact('stories', 'featuredStories', 'categories'));
    }

    /**
     * Display stories by category.
     */
    public function category($category)
    {
        $stories = IslamicStory::where('category', $category)
            ->latest()
            ->paginate(9);

        $categories = IslamicStory::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('stories.category', compact('stories', 'category', 'categories'));
    }

    /**
     * Display the specified story.
     */
    public function show($id)
    {
        $story = IslamicStory::findOrFail($id);
        $story->incrementViewCount();

        // Get related stories from the same category
        $relatedStories = IslamicStory::where('category', $story->category)
            ->where('id', '!=', $story->id)
            ->take(3)
            ->get();

        return view('stories.show', compact('story', 'relatedStories'));
    }

    /**
     * Search for stories
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $stories = IslamicStory::where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->paginate(9);

        return view('stories.search', compact('stories', 'query'));
    }
}
