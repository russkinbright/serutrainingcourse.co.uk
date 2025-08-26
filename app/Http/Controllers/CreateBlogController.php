<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;

class CreateBlogController extends Controller
{
    /**
     * Display the blog creation form.
     */
    public function create()
    {
        return view('lms.createBlog');
    }

    /**
     * Store a new blog in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'card_image_url' => 'nullable|url',
            'background_image_url' => 'nullable|url',
            'slug' => 'required|string|unique:blogs,slug|max:255',
            'card_title' => 'nullable|string|max:255',
            'card_image' => 'nullable|url',
            'course_details_link' => 'nullable|url',
        ]);

        try {
            Blog::create($validated);
            return response()->json(['success' => true, 'message' => 'Blog created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create blog: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check if a slug is available.
     */
    public function checkSlug(Request $request)
    {
        $request->validate(['slug' => 'required|string|max:255']);
        $exists = Blog::where('slug', $request->slug)->exists();

        return response()->json(['exists' => $exists]);
    }
}