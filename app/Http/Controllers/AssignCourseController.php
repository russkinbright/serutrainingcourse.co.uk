<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Models\Course;
use App\Models\Payment;
use App\Models\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AssignCourseController extends Controller
{
    public function index()
    {
        Log::info('AssignCourseController@index called');
        return view('lms.assignCourse');
    }

    public function searchLearners(Request $request)
    {
        $query = $request->query('q');
        Log::info('Searching learners with query:', ['query' => $query]);

        if (empty($query) || strlen($query) < 2) {
            Log::warning('Query too short or empty for learner search.', ['query' => $query]);
            return response()->json([]);
        }

        $learners = Learner::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('secret_id', 'LIKE', "%{$query}%")
            ->select('secret_id', 'name', 'email')
            ->take(10)
            ->get();

        Log::info('Learner search results:', ['count' => $learners->count()]);
        return response()->json($learners);
    }

    public function searchCourses(Request $request)
    {
        $query = $request->query('q');
        Log::info('Searching courses with query:', ['query' => $query]);

        if (empty($query) || strlen($query) < 2) {
            Log::warning('Query too short or empty for course search.', ['query' => $query]);
            return response()->json([]);
        }

        $courses = Course::where('title', 'LIKE', "%{$query}%")
            ->select('unique_id', 'title')
            ->take(10)
            ->get();

        Log::info('Course search results:', ['count' => $courses->count()]);
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        Log::info('Assigning courses to learner:', [
            'learner_secret_id' => $request->learner_secret_id,
            'name' => $request->name,
            'email' => $request->email,
            'courses' => $request->courses
        ]);

        $request->validate([
            'learner_secret_id' => 'required|exists:learner,secret_id',
            'name' => 'required|string',
            'email' => 'required|email',
            'courses' => 'required|array|min:1',
            'courses.*.course_unique_id' => 'required|exists:course,unique_id',
            'courses.*.course_title' => 'required|string',
        ]);

        try {
            foreach ($request->courses as $course) {
                $paymentUniqueId = mt_rand(10000000, 99999999);

                Payment::create([
                    'payment_unique_id' => $paymentUniqueId,
                    'learner_secret_id' => $request->learner_secret_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'payment_type' => 'manual',
                    'course_unique_id' => $course['course_unique_id'],
                    'course_title' => $course['course_title'],
                ]);

                Log::info('Payment record created:', [
                    'payment_unique_id' => $paymentUniqueId,
                    'course_unique_id' => $course['course_unique_id']
                ]);

                CourseProgress::create([
                    'learner_secret_id' => $request->learner_secret_id,
                    'course_unique_id' => $course['course_unique_id'],
                ]);

                Log::info('CourseProgress record created for learner:', [
                    'learner_secret_id' => $request->learner_secret_id,
                    'course_unique_id' => $course['course_unique_id']
                ]);
            }

            Log::info('All courses assigned successfully to learner:', ['learner_secret_id' => $request->learner_secret_id]);
            return response()->json(['message' => 'Courses assigned successfully!'], 200);
        } catch (\Exception $e) {
            Log::error('Error assigning courses:', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to assign courses: ' . $e->getMessage()], 500);
        }
    }
}
