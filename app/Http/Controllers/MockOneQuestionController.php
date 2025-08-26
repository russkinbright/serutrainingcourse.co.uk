<?php

namespace App\Http\Controllers;

use App\Models\M1Question;
use App\Models\Mock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MockOneQuestionController extends Controller
{
    public function index()
    {
        return view('lms.mockOneQuestion');
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query'));
        if (empty($query)) {
            return response()->json([]);
        }

        $mocks = Mock::where('tag', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->select('unique_id', 'tag', 'name')
            ->take(10)
            ->get();

        return response()->json($mocks);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mock_unique_id' => 'required|exists:mock,unique_id',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string|max:1000',
                'questions.*.option_a' => 'required|string|max:255',
                'questions.*.option_b' => 'required|string|max:255',
                'questions.*.option_c' => 'required|string|max:255',
                'questions.*.option_d' => 'required|string|max:255',
                'questions.*.option_e' => 'required|string|max:255',
                'questions.*.option_f' => 'required|string|max:255',
                'questions.*.answer_1' => 'required|string|in:a,b,c,d,e,f',
                'questions.*.answer_2' => 'required|string|in:a,b,c,d,e,f',
                'questions.*.answer_3' => 'required|string|in:a,b,c,d,e,f',
            ]);

            $createdQuestions = [];

            foreach ($request->questions as $questionData) {
                $uniqueId = random_int(1000000, 9999999);
                while (M1Question::where('unique_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000, 9999999);
                }

                $question = M1Question::create([
                    'unique_id' => $uniqueId,
                    'mock_unique_id' => $request->mock_unique_id,
                    'question_text' => $questionData['question_text'],
                    'option_a' => $questionData['option_a'],
                    'option_b' => $questionData['option_b'],
                    'option_c' => $questionData['option_c'],
                    'option_d' => $questionData['option_d'],
                    'option_e' => $questionData['option_e'],
                    'option_f' => $questionData['option_f'],
                    'answer_1' => $questionData['answer_1'],
                    'answer_2' => $questionData['answer_2'],
                    'answer_3' => $questionData['answer_3'],
                ]);

                $createdQuestions[] = $question;
            }

            return response()->json([
                'message' => 'Questions created successfully!',
                'questions' => $createdQuestions,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Question creation failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to create questions. Please try again.']], 500);
        }
    }

    public function checkQuestionExists(Request $request)
    {
        $questionText = trim($request->input('question_text'));
        $exists = M1Question::whereRaw('LOWER(question_text) = ?', [strtolower($questionText)])->exists();

        return response()->json(['exists' => $exists]);
    }
}
