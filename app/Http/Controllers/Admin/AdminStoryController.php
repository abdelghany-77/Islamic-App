<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IslamicStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminStoryController extends Controller
{
    public function index()
    {
        $stories = IslamicStory::latest()->paginate(10);
        return view('admin.stories.index', compact('stories'));
    }

    public function create()
    {
        return view('admin.stories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'content' => 'required',
            'source' => 'nullable|max:255',
            'category' => 'required|max:100',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'featured' => 'boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('stories', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        IslamicStory::create($validated);
        return redirect()->route('admin.stories.index')->with('success', 'تم إضافة القصة بنجاح');
    }

    public function edit(IslamicStory $story)
    {
        return view('admin.stories.edit', compact('story'));
    }

    public function update(Request $request, IslamicStory $story)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'content' => 'required',
            'source' => 'nullable|max:255',
            'category' => 'required|max:100',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'featured' => 'boolean'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Remove old image if exists
            if ($story->image_url && Storage::exists('public/' . str_replace('/storage/', '', $story->image_url))) {
                Storage::delete('public/' . str_replace('/storage/', '', $story->image_url));
            }

            $path = $request->file('image')->store('stories', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $story->update($validated);
        return redirect()->route('admin.stories.index')->with('success', 'تم تحديث القصة بنجاح');
    }

    public function destroy(IslamicStory $story)
    {
        // Remove image if exists
        if ($story->image_url && Storage::exists('public/' . str_replace('/storage/', '', $story->image_url))) {
            Storage::delete('public/' . str_replace('/storage/', '', $story->image_url));
        }

        $story->delete();
        return redirect()->route('admin.stories.index')->with('success', 'تم حذف القصة بنجاح');
    }
}
