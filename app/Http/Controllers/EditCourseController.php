<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EditCourseController extends Controller
{
    /**
     * Display the course edit form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('lms.editCourse');
    }

    /**
     * Search courses by title or unique ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $courses = Course::query()
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->get(['id', 'title', 'unique_id']);

        return response()->json($courses);
    }

    /**
     * Show a specific course's details.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $course = Course::findOrFail($id);
            return response()->json([
                'id' => $course->id,
                'unique_id' => $course->unique_id,
                'title' => $course->title,
                'meta_title' => $course->meta_title,
                'meta_keywords' => $course->meta_keywords,
                'meta_description' => $course->meta_description,
                'slug' => $course->slug,
                'canonical_url' => $course->canonical_url,
                'robots_meta' => $course->robots_meta,
                'schema_markup' => $course->schema_markup,
                'description' => $course->description,
                'duration' => $course->duration,
                'enroll' => $course->enroll,
                'price' => $course->price,
                'footer_price' => $course->footer_price,
                'rating' => $course->rating,
                'image' => $course->image,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Course not found: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Course not found.']], 404);
        }
    }

    /**
     * Update a specific course.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255|unique:course,title,' . $id,
                'meta_title' => 'nullable|string|max:255',
                'meta_keywords' => 'nullable|string|max:1000',
                'meta_description' => 'nullable|string|max:1000',
                'slug' => 'required|string|max:255|unique:course,slug,' . $id . '|regex:/^[a-z0-9-]+$/',
                'canonical_url' => 'nullable|url|max:255',
                'robots_meta' => 'nullable|string',
                'schema_markup' => 'nullable|json',
                'description' => 'required|string',
                'duration' => 'required|string',
                'enroll' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'footer_price' => 'nullable|numeric|min:0',
                'rating' => 'required|in:0,1,1.5,2,2.5,3,3.5,4,4.5,5',
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

            $course->update($data);

            return response()->json([
                'message' => 'Course updated successfully!'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Course not found for update: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Course not found.']], 404);
        } catch (\Exception $e) {
            Log::error('Course update failed: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['errors' => ['general' => 'Failed to update course. Please try again.']], 500);
        }
    }

    /**
     * Check if a course title is available (excluding the current course).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTitle(Request $request, $id)
    {
        $title = trim($request->input('title'));
        if (empty($title)) {
            return response()->json([
                'exists' => false,
                'message' => 'Title cannot be empty.',
                'suggested_slug' => '',
            ], 400);
        }

        $exists = Course::where('title', $title)->where('id', '!=', $id)->exists();
        $slug = Str::slug($title);
        $slugExists = Course::where('slug', $slug)->where('id', '!=', $id)->exists();

        // If slug exists, append a number to make it unique
        $baseSlug = $slug;
        $counter = 1;
        while ($slugExists) {
            $slug = $baseSlug . '-' . $counter++;
            $slugExists = Course::where('slug', $slug)->where('id', '!=', $id)->exists();
        }

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This title is already taken.' : 'This title is available!',
            'suggested_slug' => $slug,
        ]);
    }

    /**
     * Check if a meta title is available (excluding the current course).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMetaTitle(Request $request, $id)
    {
        $meta_title = trim($request->input('meta_title'));
        if (empty($meta_title)) {
            return response()->json([
                'exists' => false,
                'message' => 'Meta title cannot be empty.',
            ], 400);
        }

        $exists = Course::where('meta_title', $meta_title)->where('id', '!=', $id)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This meta title is already taken.' : 'This meta title is available!',
        ]);
    }

    /**
     * Delete a specific course.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();

            return response()->json([
                'message' => 'Course deleted successfully!',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Course not found for deletion: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Course not found.']], 404);
        } catch (\Exception $e) {
            Log::error('Course deletion failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to delete course. Please try again.']], 500);
        }
    }
}