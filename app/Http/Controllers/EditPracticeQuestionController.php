<?php
namespace App\Http\Controllers;
use App\Models\PQuestion;
use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EditpracticeQuestionController extends Controller
{
    public function index()
    {
        Log::info('Accessed edit practice question index page');
        return view('lms.editpracticeQuestion');
    }

    public function searchSections(Request $request)
    {
        $query = trim($request->input('query'));
        Log::info("Searching sections with query: {$query}");

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $sections = Practice::where('tag', 'LIKE', "%{$query}%")
                ->orWhere('unique_id', 'LIKE', "%{$query}%")
                ->select('unique_id', 'tag')
                ->take(10)
                ->get();

            Log::info("Found sections: " . $sections->toJson());
            return response()->json($sections);
        } catch (\Exception $e) {
            Log::error("Error searching sections: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to search sections: ' . $e->getMessage()]], 500);
        }
    }

    public function getQuestions(Request $request)
    {
        $section_unique_id = $request->input('section_unique_id');
        $query = $request->input('query', '');

        Log::info("Fetching questions for section: {$section_unique_id} with search query: {$query}");

        try {
            $questions = PQuestion::where('practice_unique_id', $section_unique_id)
                ->when($query, function ($q) use ($query) {
                    return $q->where('question_text', 'LIKE', "%{$query}%");
                })
                ->select('unique_id', 'type', 'question_text', 'option_a', 'option_b', 'option_c', 'answer_1', 'answer_2', 'incorrect')
                ->get()
                ->map(function ($question) {
                    $question->answers = array_filter([$question->answer_1, $question->answer_2]);
                    return $question;
                });

            Log::info("Fetched questions: " . $questions->toJson());
            return response()->json($questions);
        } catch (\Exception $e) {
            Log::error("Error fetching questions for section {$section_unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to fetch questions: ' . $e->getMessage()]], 500);
        }
    }

    public function checkQuestionExists(Request $request)
    {
        $question_text = trim($request->input('question_text'));
        $unique_id = $request->input('unique_id');

        Log::info("Checking if question exists. Text: {$question_text}, Unique ID: {$unique_id}");

        if (empty($question_text)) {
            return response()->json(['exists' => false]);
        }

        try {
            $exists = PQuestion::whereRaw('LOWER(question_text) = ?', [strtolower($question_text)])
                ->where('unique_id', '!=', $unique_id)
                ->exists();

            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error("Error checking question existence: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to check question existence: ' . $e->getMessage()]], 500);
        }
    }

    public function updateQuestion(Request $request)
    {
        $request->validate([
            'unique_id' => 'required|exists:p_question,unique_id',
            'section_unique_id' => 'required|exists:practice,unique_id',
            'type' => 'required|in:single,double',
            'question_text' => 'required|string|max:255',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'answer_1' => 'required|string|in:a,b,c',
            'answer_2' => 'nullable|string|in:a,b,c',
            'incorrect' => 'nullable|string',
        ]);

        try {
            $question = PQuestion::where('unique_id', $request->unique_id)->firstOrFail();

            Log::info("Updating question", [
                'unique_id' => $request->unique_id,
                'updated_data' => $request->only([
                    'section_unique_id', 'type', 'question_text',
                    'option_a', 'option_b', 'option_c',
                    'answer_1', 'answer_2', 'incorrect'
                ])
            ]);

            $question->update([
                'practice_unique_id' => $request->section_unique_id,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'option_a' => $request->option_a,
                'option_b' => $request->option_b,
                'option_c' => $request->option_c,
                'answer_1' => $request->answer_1,
                'answer_2' => $request->answer_2,
                'incorrect' => $request->incorrect,
            ]);

            return response()->json(['message' => 'Question updated successfully']);
        } catch (\Exception $e) {
            Log::error("Error updating question {$request->unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to update question: ' . $e->getMessage()]], 500);
        }
    }

    public function deleteQuestion(Request $request)
    {
        $request->validate([
            'unique_id' => 'required|exists:p_question,unique_id',
        ]);

        try {
            Log::warning("Deleting question with unique_id: {$request->unique_id}");
            PQuestion::where('unique_id', $request->unique_id)->delete();
            return response()->json(['message' => 'Question deleted successfully']);
        } catch (\Exception $e) {
            Log::error("Error deleting question {$request->unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to delete question: ' . $e->getMessage()]], 500);
        }
    }

    public function deleteAllQuestions(Request $request)
    {
        $request->validate([
            'section_unique_id' => 'required|exists:practice,unique_id',
        ]);

        try {
            Log::warning("Deleting all questions for section: {$request->section_unique_id}");
            PQuestion::where('practice_unique_id', $request->section_unique_id)->delete();
            return response()->json(['message' => 'All questions for the section deleted successfully']);
        } catch (\Exception $e) {
            Log::error("Error deleting all questions for section {$request->section_unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to delete all questions: ' . $e->getMessage()]], 500);
        }
    }
}