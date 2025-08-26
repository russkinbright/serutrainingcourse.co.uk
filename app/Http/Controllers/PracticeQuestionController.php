<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\PQuestion;
use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PracticeQuestionController extends Controller
{
    public function index()
    {
        return view('lms.practiceQuestion');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $sections = Practice::query()
            ->where('tag', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->get(['id', 'tag', 'unique_id']);

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'practice_unique_id' => 'required|exists:practice,unique_id',
                'questions' => 'required|array|min:1',
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
                while (PQuestion::where('unique_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000, 9999999);
                }

                $question = PQuestion::create([
                    'unique_id' => $uniqueId,
                    'practice_unique_id' => $request->practice_unique_id,
                    'type' => $questionData['type'],
                    'question_text' => $questionData['question_text'],
                    'option_a' => $questionData['option_a'],
                    'option_b' => $questionData['option_b'],
                    'option_c' => $questionData['option_c'],
                    'answer_1' => $questionData['answer_1'],
                    'answer_2' => $questionData['type'] === 'double' ? $questionData['answer_2'] : null,
                    'incorrect' => $questionData['incorrect'],
                ]);

                $createdQuestions[] = $question;
            }

            return response()->json([
                'message' => 'Questions created successfully!',
                'questions' => $createdQuestions,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Question creation failed: ' . $e->getMessage());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Question creation failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to create questions. Please try again.']], 500);
        }
    }

    public function checkQuestionExists(Request $request)
    {
        $questionText = trim($request->input('question_text'));
        $exists = PQuestion::whereRaw('LOWER(question_text) = ?', [strtolower($questionText)])->exists();

        return response()->json(['exists' => $exists]);
    }
}
