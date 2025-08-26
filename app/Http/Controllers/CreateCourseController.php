<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateCourseController extends Controller
{
    /**
     * Display the course creation form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('lms.createCourse');
    }

    /**
     * Store a new course in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255|unique:course,title',
                'meta_title' => 'nullable',
                'meta_keywords' => 'nullable',
                'meta_description' => 'nullable',
                'slug' => 'required|string|max:255|unique:course,slug|regex:/^[a-z0-9-]+$/',
                'canonical_url' => 'nullable|url|max:255',
                'robots_meta' => 'nullable|string',
                'schema_markup' => 'nullable|json',
                'description' => 'required',
                'duration' => 'required',
                'enroll' => 'required',
                'price' => 'required',
                'footer_price' => 'nullable',
                'rating' => 'required',
                'image_url' => 'nullable',
            ]);

            $data = $request->only([
                'title',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'slug',
                'canonical_url',
                'robots_meta',
                'schema_markup',
                'description',
                'duration',
                'enroll',
                'price',
                'footer_price',
                'rating',
            ]);

            if ($request->filled('image_url')) {
                $data['image'] = $request->image_url;
            }

            // Generate unique 7-digit ID
            $data['unique_id'] = $this->generateUniqueCourseId();

            Course::create($data);

            return response()->json([
                'message' => 'Course created successfully!',
                'redirect_url' => route('course.page') // Adjust route name if needed
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Course creation failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['errors' => ['general' => 'Failed to create course. Please try again.']], 500);
        }
    }

    /**
     * Check if a course title or slug is available.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTitle(Request $request)
    {
        $title = trim($request->input('title'));
        if (empty($title)) {
            return response()->json([
                'exists' => false,
                'message' => 'Title cannot be empty.',
                'suggested_slug' => '',
            ], 400);
        }

        $exists = Course::where('title', $title)->exists();
        $slug = Str::slug($title);
        $slugExists = Course::where('slug', $slug)->exists();

        // If slug exists, append a number to make it unique
        $baseSlug = $slug;
        $counter = 1;
        while ($slugExists) {
            $slug = $baseSlug . '-' . $counter++;
            $slugExists = Course::where('slug', $slug)->exists();
        }

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This title is already taken.' : 'This title is available!',
            'suggested_slug' => $slug,
        ]);
    }

    /**
     * Generate a unique 7-digit course ID.
     *
     * @return string
     */
    private function generateUniqueCourseId()
    {
        do {
            $id = mt_rand(1000000, 9999999); // 7-digit random number
        } while (Course::where('unique_id', $id)->exists());

        return (string) $id;
    }
}