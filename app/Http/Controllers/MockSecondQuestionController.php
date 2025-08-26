<?php

namespace App\Http\Controllers;

use App\Models\M2Question;
use App\Models\Practice;
use App\Models\Section;
use App\Models\Mock;
use App\Models\PQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MockSecondQuestionController extends Controller
{
    public function index()
    {
        return view('lms.mockSecondQuestion');
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query'));
        if (empty($query)) {
            return response()->json([]);
        }

        $mocks = Mock::join('practice', 'practice.tag', '=', 'mock.tag')
        ->where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('mock.tag', 'LIKE', "%{$query}%")
                        ->orWhere('mock.unique_id', 'LIKE', "%{$query}%");
        })
        ->select(
            'mock.unique_id as mock_unique_id',
            'mock.tag',
            'mock.name',
            'practice.unique_id as practice_unique_id'
        )
        ->take(10)
        ->get();

        Log::info('Search Query Result:', $mocks->toArray());
        return response()->json($mocks);
    }


   public function getQuestions(Request $request)
        {
            $practice_unique_id = $request->input('practice_unique_id');
            if (empty($practice_unique_id)) {
                return response()->json([]);
            }

            $questions = DB::table('p_question')
                ->join('practice', 'p_question.practice_unique_id', '=', 'practice.unique_id')
                ->where('p_question.practice_unique_id', $practice_unique_id)
                ->select(
                    'p_question.unique_id',
                    'p_question.type',
                    'p_question.question_text',
                    'p_question.option_a',
                    'p_question.option_b',
                    'p_question.option_c',
                    'p_question.answer_1',
                    'p_question.answer_2',
                    'p_question.incorrect'
                )
                ->orderByRaw("FIELD(p_question.type, 'single', 'double')")
                ->get()
                ->map(function ($question) {
                    return [
                        'unique_id' => $question->unique_id,
                        'type' => $question->type,
                        'question_text' => $question->question_text,
                        'option_a' => $question->option_a,
                        'option_b' => $question->option_b,
                        'option_c' => $question->option_c,
                        'answer_1' => $question->answer_1,
                        'answer_2' => $question->answer_2,
                        'incorrect' => $question->incorrect,
                    ];
                });

            return response()->json($questions);
        }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mock_unique_id' => 'required|exists:mock,unique_id',
                'questions' => 'required|array|min:1',
                'questions.*.unique_id' => 'required',
                'questions.*.type' => 'required|in:single,double',
                'questions.*.question_text' => 'required|string|max:1000',
                'questions.*.option_a' => 'required|string|max:255',
                'questions.*.option_b' => 'required|string|max:255',
                'questions.*.option_c' => 'required|string|max:255',
                'questions.*.answer_1' => 'required|string|in:a,b,c',
                'questions.*.answer_2' => 'nullable|string|in:a,b,c',
                'questions.*.incorrect' => 'required|string|max:1000',
            ]);

            $createdQuestions = [];

            foreach ($request->questions as $questionData) {
                $uniqueId = random_int(1000000, 9999999);
                while (M2Question::where('unique_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000, 9999999);
                }

                $question = M2Question::create([
                    'unique_id' => $uniqueId,
                    'mock_unique_id' => $request->mock_unique_id,
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'option_a' => $questionData['option_a'],
                    'option_b' => $questionData['option_b'],
                    'option_c' => $questionData['option_c'],
                    'answer_1' => $questionData['answer_1'],
                    'answer_2' => $questionData['answer_2'],
                    'incorrect' => $questionData['incorrect'],
                ]);

                $createdQuestions[] = $question;
            }

            return response()->json([
                'message' => 'Questions saved successfully!',
                'questions' => $createdQuestions,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Question saving failed: ' . $e->getMessage());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Question saving failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to save questions. Please try again.']], 500);
        }
    }
}
