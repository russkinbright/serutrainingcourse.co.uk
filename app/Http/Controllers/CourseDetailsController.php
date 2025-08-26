<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseDetailsController extends Controller
{
    /**
     * Render the course details view.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        return view('course.course-details');
    }

    /**
     * Fetch course details by slug for API.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiShow($slug)
    {
        try {
            $course = Course::where('slug', $slug)->first();
            if (!$course) {
                return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
            }
            return response()->json([
                'success' => true,
                'course' => [
                    'id' => $course->id,
                    'unique_id' => $course->unique_id,
                    'title' => $course->title,
                    'meta_description' => $course->meta_description,
                    'meta_keywords' => $course->meta_keywords,
                    'robots_meta' => $course->robots_meta,
                    'canonical_url' => $course->canonical_url,
                    'schema_markup' => $course->schema_markup,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'duration' => $course->duration,
                    'enroll' => $course->enroll,
                    'price' => $course->price,
                    'image' => $course->image,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching course details: ' . $e->getMessage(), ['slug' => $slug, 'exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to load course details.'], 500);
        }
    }
}