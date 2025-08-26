<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MainCourseController extends Controller
{
    /**
     * Display the main course page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('course.main-course');
    }

    /**
     * Search courses with pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $search = trim($request->input('search', '')); 
            $perPage = 6; // Number of courses per page
            $query = Course::query();

            if (!empty($search)) {
                $query->where('title', 'LIKE', "%{$search}%");
            }

            $courses = $query->paginate($perPage)
                ->through(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $course->slug,
                        'description' => $course->description,
                        'image' => $course->image,
                        'duration' => $course->duration,
                        'enroll' => $course->enroll,
                        'price' => $course->price,
                        'footer_price' => $course->footer_price,
                        'rating' => $course->rating,
                    ];
                });

            return response()->json(['courses' => $courses]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'An error occurred while searching courses'], 500);
        }
    }

    /**
     * Update course rating.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rate(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:course,id',
                'rating' => 'required|numeric|min:0|max:5|regex:/^\d(\.\d)?$/'
            ]);

            $course = Course::findOrFail($request->course_id);
            $course->rating = (string) $request->rating;
            $course->save();

            return response()->json(['message' => 'Rating updated successfully']);
        } catch (\Exception $e) {
            Log::error('Rating update error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to update rating'], 500);
        }
    }
}