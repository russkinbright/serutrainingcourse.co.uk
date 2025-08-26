<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Practice;
use App\Models\PQuestion;
use App\Models\Mock;
use App\Models\M1Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Exception;

class FinalMockController extends Controller
{
public function start(Request $request)
{
    try {
        $learner = Auth::guard('learner')->user();
        if (!$learner) {
            return redirect()->route('learner.learnerLogin');
        }

        // ✅ Check if learner has any course progress
        $hasCourse = DB::table('course_progress')
            ->where('learner_secret_id', $learner->secret_id)
            ->exists();

        if (!$hasCourse) {
            return redirect()
                ->route('learner.page')
                ->with('error', 'No course assigned to your account. Please enroll first.');
        }

        // continue as before
        $questions = $this->buildFinalMockQuestions();

        if (count($questions) < 37) {
            return back()->with('error', 'Not enough questions to create the final mock test.');
        }

        Session::put('final_mock_questions', $questions);
        return redirect()->route('learner.finalMock');

    } catch (\Throwable $e) {
        Log::error('FinalMock start failed: '.$e->getMessage());
        return back()->with('error', 'Failed to start final mock: '.$e->getMessage());
    }
}

    // JSON API used by the page as a fallback
    public function fetchFinalMockQuestions(Request $request)
            {
                try {
                    $learner = Auth::guard('learner')->user();
                    if (!$learner) {
                        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
                    }

                    // ✅ Same check here
                    $hasCourse = DB::table('course_progress')
                        ->where('learner_secret_id', $learner->secret_id)
                        ->exists();

                    if (!$hasCourse) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No course assigned to your account. Please enroll first.'
                        ], 403);
                    }

                    $questions = $this->buildFinalMockQuestions();

                    if (count($questions) < 37) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Not enough questions available to create the final mock test'
                        ], 422);
                    }

                    return response()->json(['success' => true, 'questions' => $questions]);

                } catch (\Throwable $e) {
                    Log::error('fetchFinalMockQuestions error: '.$e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to load final mock questions: '.$e->getMessage()
                    ], 500);
                }
            }


    /**
     * Build and return the final mock questions as a plain array.
     * DO NOT return JsonResponse/View from here.
     *
     * @return array
     * @throws \Exception on failure
     */
    private function buildFinalMockQuestions(): array
    {
        // Step 1: section tags
        $sectionTags = Practice::select('tag')->distinct()->pluck('tag')->toArray();
        if (empty($sectionTags)) {
            Log::warning('No section tags found in practice table');
            throw new Exception('No sections available');
        }

        $questions = [];
        $totalQuestionsNeeded = 37;
        $remainingQuestions    = $totalQuestionsNeeded;

        // Step 2: at least one per section (randomly from practice or mock1)
        foreach ($sectionTags as $tag) {
            $practice = Practice::where('tag', $tag)->first();

            $practiceQuestions = $practice ? PQuestion::where('practice_unique_id', $practice->unique_id)
                ->select('unique_id','type','question_text','option_a','option_b','option_c','answer_1','answer_2','incorrect')
                ->inRandomOrder()->take(1)->get()->map(function ($q) use ($tag) {
                    return [
                        'unique_id'     => $q->unique_id ?? 'missing_unique_id_'.$tag,
                        'type'          => $q->type ?? 'single',
                        'question_text' => $q->question_text ?? 'No question text',
                        'option_a'      => $q->option_a ?? '',
                        'option_b'      => $q->option_b ?? '',
                        'option_c'      => $q->option_c ?? '',
                        'answer_1'      => $q->answer_1 ?? '',
                        'answer_2'      => $q->answer_2,
                        'incorrect'     => $q->incorrect ?? '',
                        'source'        => 'practice',
                    ];
                })->toArray() : [];

            $mock1 = Mock::where('tag', $tag)->where('mock_number', 'mock 1')->first();

            $mock1Questions = $mock1 ? M1Question::where('mock_unique_id', $mock1->unique_id)
                ->select('unique_id','question_text','option_a','option_b','option_c','option_d','option_e','option_f','answer_1','answer_2','answer_3')
                ->inRandomOrder()->take(1)->get()->map(function ($q) use ($tag) {
                    return [
                        'unique_id'     => $q->unique_id ?? 'missing_unique_id_'.$tag,
                        'type'          => 'mock1',
                        'question_text' => $q->question_text ?? 'No question text',
                        'option_a'      => $q->option_a ?? '',
                        'option_b'      => $q->option_b ?? '',
                        'option_c'      => $q->option_c ?? '',
                        'option_d'      => $q->option_d ?? '',
                        'option_e'      => $q->option_e ?? '',
                        'option_f'      => $q->option_f ?? '',
                        'answer_1'      => $q->answer_1 ?? '',
                        'answer_2'      => $q->answer_2,
                        'answer_3'      => $q->answer_3,
                        'source'        => 'mock1',
                    ];
                })->toArray() : [];

            $available = array_merge($practiceQuestions, $mock1Questions);
            if (!empty($available)) {
                $selected     = $available[array_rand($available)];
                $questions[]  = $selected;
                $remainingQuestions--;
            } else {
                Log::warning('No questions available for tag', ['tag' => $tag]);
            }
        }

        // Step 3: fill to 37 from all sections
        if ($remainingQuestions > 0) {
            $allPractice = Practice::join('p_question', 'practice.unique_id', '=', 'p_question.practice_unique_id')
                ->select(
                    'p_question.unique_id','p_question.type','p_question.question_text',
                    'p_question.option_a','p_question.option_b','p_question.option_c',
                    'p_question.answer_1','p_question.answer_2','p_question.incorrect',
                    DB::raw("'practice' as source")
                )->inRandomOrder()->get()->map(function ($q) {
                    return [
                        'unique_id'     => $q->unique_id ?? 'missing_unique_id_'.uniqid(),
                        'type'          => $q->type ?? 'single',
                        'question_text' => $q->question_text ?? 'No question text',
                        'option_a'      => $q->option_a ?? '',
                        'option_b'      => $q->option_b ?? '',
                        'option_c'      => $q->option_c ?? '',
                        'answer_1'      => $q->answer_1 ?? '',
                        'answer_2'      => $q->answer_2,
                        'incorrect'     => $q->incorrect ?? '',
                        'source'        => 'practice',
                    ];
                })->toArray();

            $allMock1 = Mock::where('mock_number', 'mock 1')
                ->join('m1_question', 'mock.unique_id', '=', 'm1_question.mock_unique_id')
                ->select(
                    'm1_question.unique_id',
                    DB::raw("'mock1' as type"),
                    'm1_question.question_text',
                    'm1_question.option_a','m1_question.option_b','m1_question.option_c',
                    'm1_question.option_d','m1_question.option_e','m1_question.option_f',
                    'm1_question.answer_1','m1_question.answer_2','m1_question.answer_3',
                    DB::raw("'mock1' as source")
                )->inRandomOrder()->get()->map(function ($q) {
                    return [
                        'unique_id'     => $q->unique_id ?? 'missing_unique_id_'.uniqid(),
                        'type'          => 'mock1',
                        'question_text' => $q->question_text ?? 'No question text',
                        'option_a'      => $q->option_a ?? '',
                        'option_b'      => $q->option_b ?? '',
                        'option_c'      => $q->option_c ?? '',
                        'option_d'      => $q->option_d ?? '',
                        'option_e'      => $q->option_e ?? '',
                        'option_f'      => $q->option_f ?? '',
                        'answer_1'      => $q->answer_1 ?? '',
                        'answer_2'      => $q->answer_2,
                        'answer_3'      => $q->answer_3,
                        'source'        => 'mock1',
                    ];
                })->toArray();

            $pool = array_merge($allPractice, $allMock1);

            // remove already used IDs
            $usedIds = array_column($questions, 'unique_id');
            $pool = array_values(array_filter($pool, fn($q) => isset($q['unique_id']) && !in_array($q['unique_id'], $usedIds)));

            if (count($pool) < $remainingQuestions) {
                Log::warning('Not enough remaining questions', ['available' => count($pool), 'needed' => $remainingQuestions]);
                throw new Exception('Not enough questions available to complete the final mock test');
            }

            shuffle($pool);
            $questions = array_merge($questions, array_slice($pool, 0, $remainingQuestions));
        }

        // Step 4/5: shuffle & enforce exactly 37
        $questions = array_values($questions);
        shuffle($questions);

        if (count($questions) > $totalQuestionsNeeded) {
            $questions = array_slice($questions, 0, $totalQuestionsNeeded);
        }
        if (count($questions) < $totalQuestionsNeeded) {
            throw new Exception('Not enough questions available to create the final mock test');
        }

        return $questions; // <-- array only
    }

    private function shuffleArray($array)
    {
        $array = array_values($array);
        for ($i = count($array) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
        }
        return $array;
    }
}
