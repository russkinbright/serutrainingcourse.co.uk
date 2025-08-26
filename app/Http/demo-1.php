<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Section;
use App\Models\Practice;
use App\Models\PQuestion;
use App\Models\Mock;
use App\Models\M1Question;
use App\Models\M2Question;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LearnerCourseController extends Controller
{
    /**
     * Fetch all courses for the authenticated learner.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchCourses()
    {
        try {
            $learner = Auth::user();
            if (!$learner || !$learner->secret_id) {
                Log::warning('User not authenticated or missing secret_id', ['user_id' => Auth::id()]);
                return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
            }

            $progressRecords = CourseProgress::where('learner_secret_id', $learner->secret_id)->get();
            $courseIds = $progressRecords->pluck('course_unique_id')->toArray();

            $courses = Course::whereIn('unique_id', $courseIds)->get()->map(function ($course) use ($progressRecords) {
                $progress = $progressRecords->firstWhere('course_unique_id', $course->unique_id);
                return [
                    'title' => $course->title,
                    'unique_id' => $course->unique_id,
                    'progress' => $progress ? $progress->progress : 0,
                    'is_completed' => $progress ? $progress->is_completed : false,
                ];
            });

            return response()->json(['success' => true, 'courses' => $courses]);
        } catch (\Exception $e) {
            Log::error('Error fetching learner courses: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to load courses'], 500);
        }
    }

    /**
     * Fetch details for a specific course, including sections and quizzes.
     *
     * @param string $unique_id
     * @return \Illuminate\Http\JsonResponse
     */
   public function getCourseDetails($unique_id)
    {
        try {
            $learner = Auth::user();
            if (!$learner || !$learner->secret_id) {
                Log::warning('User not authenticated or missing secret_id', ['user_id' => Auth::id(), 'course_id' => $unique_id]);
                return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
            }

            $course = Course::where('unique_id', $unique_id)->first();
            if (!$course) {
                Log::warning('Course not found', ['unique_id' => $unique_id]);
                return response()->json(['success' => false, 'message' => 'Course not found'], 404);
            }

            $sections = Section::where('course_unique_id', $unique_id)
                ->orderBy('sequence')
                ->get()
                ->map(function ($section) {
                    $practice = Practice::where('tag', $section->name)->first();
                    $practiceQuestions = $practice ? PQuestion::where('practice_unique_id', $practice->unique_id)
                        ->select('unique_id', 'type', 'question_text', 'option_a', 'option_b', 'option_c', 'answer_1', 'answer_2', 'incorrect')
                        ->get()
                        ->map(function ($q) {
                            return [
                                'unique_id' => $q->unique_id,
                                'type' => $q->type,
                                'question_text' => $q->question_text,
                                'option_a' => $q->option_a,
                                'option_b' => $q->option_b,
                                'option_c' => $q->option_c,
                                'answer_1' => $q->answer_1,
                                'answer_2' => $q->answer_2,
                                'incorrect' => $q->incorrect
                            ];
                        })->toArray() : [];

                    $mock1 = Mock::where('tag', $section->name)->where('mock_number', 'mock 1')->first();
                    $mock1Questions = $mock1 ? M1Question::where('mock_unique_id', $mock1->unique_id)
                        ->select('unique_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'option_f', 'answer_1', 'answer_2', 'answer_3')
                        ->get()
                        ->map(function ($q) {
                            return [
                                'unique_id' => $q->unique_id,
                                'question_text' => $q->question_text,
                                'option_a' => $q->option_a,
                                'option_b' => $q->option_b,
                                'option_c' => $q->option_c,
                                'option_d' => $q->option_d,
                                'option_e' => $q->option_e,
                                'option_f' => $q->option_f,
                                'answer_1' => $q->answer_1,
                                'answer_2' => $q->answer_2,
                                'answer_3' => $q->answer_3
                            ];
                        })->toArray() : [];

                    $mock2 = Mock::where('tag', $section->name)->where('mock_number', 'mock 2')->first();
                    $mock2Questions = $mock2 ? M2Question::where('mock_unique_id', $mock2->unique_id)
                        ->select('unique_id', 'type', 'question_text', 'option_a', 'option_b', 'option_c', 'answer_1', 'answer_2', 'incorrect')
                        ->get()
                        ->map(function ($q) {
                            return [
                                'unique_id' => $q->unique_id,
                                'type' => $q->type,
                                'question_text' => $q->question_text,
                                'option_a' => $q->option_a,
                                'option_b' => $q->option_b,
                                'option_c' => $q->option_c,
                                'answer_1' => $q->answer_1,
                                'answer_2' => $q->answer_2,
                                'incorrect' => $q->incorrect
                            ];
                        })->toArray() : [];

                    return [
                        'unique_id' => $section->unique_id,
                        'name' => $section->name,
                        'sequence' => $section->sequence,
                        'text_description' => $section->text_description ?? null,
                        'video_file' => $section->video_file ?? null,
                        'text_file' => $section->text_file ?? null,
                        'quizzes' => array_filter([
                            $practice ? [
                                'type' => 'practice',
                                'title' => 'Practice Quiz - ' . $section->name,
                                'questions' => $practiceQuestions
                            ] : null,
                            $mock1 ? [
                                'type' => 'mock1',
                                'title' => 'Mock Test 1 - ' . $section->name,
                                'questions' => $mock1Questions
                            ] : null,
                            $mock2 ? [
                                'type' => 'mock2',
                                'title' => 'Mock Test 2 - ' . $section->name,
                                'questions' => $mock2Questions
                            ] : null
                        ])
                    ];
                })->toArray();

            return response()->json([
                'success' => true,
                'course' => [
                    'title' => $course->title,
                    'unique_id' => $course->unique_id,
                    'description' => $course->description
                ],
                'sections' => $sections
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching course details: ' . $e->getMessage(), ['unique_id' => $unique_id, 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to load course details'], 500);
        }
    }
}