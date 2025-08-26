<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Practice;
use App\Models\PQuestion;
use App\Models\Mock;
use App\Models\M1Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FinalMockController extends Controller
{
    /**
     * Fetch 37 random questions for the final mock test, ensuring at least one question per section.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchFinalMockQuestions()
    {
        try {
            // Step 1: Get all unique section tags from the practice table
            $sectionTags = Practice::select('tag')->distinct()->pluck('tag')->toArray();
            if (empty($sectionTags)) {
                Log::warning('No section tags found in practice table');
                return response()->json(['success' => false, 'message' => 'No sections available'], 404);
            }

            Log::info('Found section tags', ['tags' => $sectionTags]);

            $questions = [];
            $totalQuestionsNeeded = 37;
            $remainingQuestions = $totalQuestionsNeeded;

            // Step 2: Fetch at least one question per section
            foreach ($sectionTags as $tag) {
                // Get practice questions for this tag
                $practice = Practice::where('tag', $tag)->first();
                $practiceQuestions = $practice ? PQuestion::where('practice_unique_id', $practice->unique_id)
                    ->select('unique_id', 'type', 'question_text', 'option_a', 'option_b', 'option_c', 'answer_1', 'answer_2', 'incorrect')
                    ->inRandomOrder()
                    ->take(1)
                    ->get()
                    ->map(function ($q) use ($tag) {
                        return [
                            'unique_id' => $q->unique_id ?? 'missing_unique_id_' . $tag,
                            'type' => $q->type ?? 'single',
                            'question_text' => $q->question_text ?? 'No question text',
                            'option_a' => $q->option_a ?? '',
                            'option_b' => $q->option_b ?? '',
                            'option_c' => $q->option_c ?? '',
                            'answer_1' => $q->answer_1 ?? '',
                            'answer_2' => $q->answer_2,
                            'incorrect' => $q->incorrect ?? '',
                            'source' => 'practice'
                        ];
                    })->toArray() : [];

                // Get Mock Test 1 questions for this tag
                $mock1 = Mock::where('tag', $tag)->where('mock_number', 'mock 1')->first();
                $mock1Questions = $mock1 ? M1Question::where('mock_unique_id', $mock1->unique_id)
                    ->select('unique_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'option_f', 'answer_1', 'answer_2', 'answer_3')
                    ->inRandomOrder()
                    ->take(1)
                    ->get()
                    ->map(function ($q) use ($tag) {
                        return [
                            'unique_id' => $q->unique_id ?? 'missing_unique_id_' . $tag,
                            'type' => 'mock1',
                            'question_text' => $q->question_text ?? 'No question text',
                            'option_a' => $q->option_a ?? '',
                            'option_b' => $q->option_b ?? '',
                            'option_c' => $q->option_c ?? '',
                            'option_d' => $q->option_d ?? '',
                            'option_e' => $q->option_e ?? '',
                            'option_f' => $q->option_f ?? '',
                            'answer_1' => $q->answer_1 ?? '',
                            'answer_2' => $q->answer_2,
                            'answer_3' => $q->answer_3,
                            'source' => 'mock1'
                        ];
                    })->toArray() : [];

                // Randomly choose one question from either practice or mock1 for this section
                $availableQuestions = array_merge($practiceQuestions, $mock1Questions);
                if (!empty($availableQuestions)) {
                    $selectedQuestion = $availableQuestions[array_rand($availableQuestions)];
                    $questions[] = $selectedQuestion;
                    $remainingQuestions--;
                    Log::info('Selected question for tag', ['tag' => $tag, 'question' => $selectedQuestion]);
                } else {
                    Log::warning('No questions available for tag', ['tag' => $tag]);
                }
            }

            // Step 3: Fill remaining questions (up to 37) with random questions from all sections
            if ($remainingQuestions > 0) {
                // Get all practice questions
                $allPracticeQuestions = Practice::join('p_question', 'practice.unique_id', '=', 'p_question.practice_unique_id')
                    ->select(
                        'p_question.unique_id',
                        'p_question.type',
                        'p_question.question_text',
                        'p_question.option_a',
                        'p_question.option_b',
                        'p_question.option_c',
                        'p_question.answer_1',
                        'p_question.answer_2',
                        'p_question.incorrect',
                        DB::raw("'practice' as source")
                    )
                    ->inRandomOrder()
                    ->get()
                    ->map(function ($q) {
                        return [
                            'unique_id' => $q->unique_id ?? 'missing_unique_id_' . uniqid(),
                            'type' => $q->type ?? 'single',
                            'question_text' => $q->question_text ?? 'No question text',
                            'option_a' => $q->option_a ?? '',
                            'option_b' => $q->option_b ?? '',
                            'option_c' => $q->option_c ?? '',
                            'answer_1' => $q->answer_1 ?? '',
                            'answer_2' => $q->answer_2,
                            'incorrect' => $q->incorrect ?? '',
                            'source' => 'practice'
                        ];
                    })->toArray();

                // Get all Mock Test 1 questions
                $allMock1Questions = Mock::where('mock_number', 'mock 1')
                    ->join('m1_question', 'mock.unique_id', '=', 'm1_question.mock_unique_id')
                    ->select(
                        'm1_question.unique_id',
                        DB::raw("'mock1' as type"),
                        'm1_question.question_text',
                        'm1_question.option_a',
                        'm1_question.option_b',
                        'm1_question.option_c',
                        'm1_question.option_d',
                        'm1_question.option_e',
                        'm1_question.option_f',
                        'm1_question.answer_1',
                        'm1_question.answer_2',
                        'm1_question.answer_3',
                        DB::raw("'mock1' as source")
                    )
                    ->inRandomOrder()
                    ->get()
                    ->map(function ($q) {
                        return [
                            'unique_id' => $q->unique_id ?? 'missing_unique_id_' . uniqid(),
                            'type' => 'mock1',
                            'question_text' => $q->question_text ?? 'No question text',
                            'option_a' => $q->option_a ?? '',
                            'option_b' => $q->option_b ?? '',
                            'option_c' => $q->option_c ?? '',
                            'option_d' => $q->option_d ?? '',
                            'option_e' => $q->option_e ?? '',
                            'option_f' => $q->option_f ?? '',
                            'answer_1' => $q->answer_1 ?? '',
                            'answer_2' => $q->answer_2,
                            'answer_3' => $q->answer_3,
                            'source' => 'mock1'
                        ];
                    })->toArray();

                // Combine and filter out already selected questions
                $allRemainingQuestions = array_merge($allPracticeQuestions, $allMock1Questions);
                $usedQuestionIds = array_column($questions, 'unique_id');
                $allRemainingQuestions = array_values(array_filter($allRemainingQuestions, function ($q) use ($usedQuestionIds) {
                    return isset($q['unique_id']) && !in_array($q['unique_id'], $usedQuestionIds);
                }));

                // Check if enough questions are available
                if (count($allRemainingQuestions) < $remainingQuestions) {
                    Log::warning('Not enough remaining questions available', [
                        'available' => count($allRemainingQuestions),
                        'needed' => $remainingQuestions
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough questions available to complete the final mock test'
                    ], 422);
                }

                // Take remaining questions
                $additionalQuestions = array_slice($this->shuffleArray($allRemainingQuestions), 0, $remainingQuestions);
                $questions = array_merge($questions, $additionalQuestions);
                Log::info('Added remaining questions', ['count' => count($additionalQuestions)]);
            }

            // Step 4: Shuffle the final set of questions
            $questions = $this->shuffleArray(array_values($questions));

            // Step 5: Ensure exactly 37 questions
            if (count($questions) > $totalQuestionsNeeded) {
                $questions = array_slice($questions, 0, $totalQuestionsNeeded);
            } elseif (count($questions) < $totalQuestionsNeeded) {
                Log::warning('Not enough questions available for final mock test', [
                    'available' => count($questions),
                    'needed' => $totalQuestionsNeeded
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough questions available to create the final mock test'
                ], 422);
            }

            Log::info('Final questions selected', ['count' => count($questions)]);

            return response()->json([
                'success' => true,
                'questions' => $questions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching final mock questions: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'questions_count' => count($questions ?? []),
                'section_tags' => $sectionTags ?? []
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load final mock questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to shuffle an array
     *
     * @param array $array
     * @return array
     */
    private function shuffleArray($array)
    {
        // Reindex array to ensure sequential numeric keys
        $array = array_values($array);
        $count = count($array);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            if (isset($array[$i]) && isset($array[$j])) {
                [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
            } else {
                Log::warning('Invalid array index in shuffleArray', ['i' => $i, 'j' => $j, 'array_count' => $count]);
            }
        }
        return $array;
    }
}