<?php
namespace App\Http\Controllers;

use App\Models\M1Question;
use App\Models\Mock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EditMockOneQuestionController extends Controller
{
    public function index()
    {
        Log::info('Accessed edit mock one question index page');
        return view('lms.editMockOneQuestion');
    }

    public function searchSections(Request $request)
    {
        $query = trim($request->input('query'));
        Log::info("Searching mock sections with query: {$query}");

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $sections = Mock::where('tag', 'LIKE', "%{$query}%")
                ->orWhere('name', 'LIKE', "%{$query}%")
                ->orWhere('unique_id', 'LIKE', "%{$query}%")
                ->select('unique_id', 'tag', 'name')
                ->take(10)
                ->get();

            Log::info("Found mock sections: " . $sections->toJson());
            return response()->json($sections);
        } catch (\Exception $e) {
            Log::error("Error searching mock sections: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to search sections: ' . $e->getMessage()]], 500);
        }
    }

    public function getQuestions(Request $request)
    {
        $section_unique_id = $request->input('section_unique_id');
        Log::info("Fetching questions for mock section: {$section_unique_id}");

        if (empty($section_unique_id)) {
            Log::warning("No section_unique_id provided for fetching questions");
            return response()->json([]);
        }

        try {
            $questions = M1Question::where('mock_unique_id', $section_unique_id)
                ->select(
                    'unique_id',
                    'question_text',
                    'option_a',
                    'option_b',
                    'option_c',
                    'option_d',
                    'option_e',
                    'option_f',
                    'answer_1',
                    'answer_2',
                    'answer_3'
                )
                ->get();

            Log::info("Fetched questions for mock section: {$section_unique_id}", ['count' => $questions->count()]);
            return response()->json($questions);
        } catch (\Exception $e) {
            Log::error("Error fetching questions for mock section {$section_unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to fetch questions: ' . $e->getMessage()]], 500);
        }
    }

    public function updateQuestion(Request $request)
    {
        try {
            Log::info('Received update request', ['payload' => $request->all()]);

            $validated = $request->validate([
                'unique_id' => 'required|exists:m1_question,unique_id',
                'question_text' => 'required|string|max:1000',
                'option_a' => 'required|string|max:255',
                'option_b' => 'required|string|max:255',
                'option_c' => 'required|string|max:255',
                'option_d' => 'required|string|max:255',
                'option_e' => 'required|string|max:255',
                'option_f' => 'required|string|max:255',
                'answer_1' => 'required|string|in:a,b,c,d,e,f',
                'answer_2' => 'required|string|in:a,b,c,d,e,f',
                'answer_3' => 'required|string|in:a,b,c,d,e,f',
            ]);

            DB::beginTransaction();

            $question = M1Question::where('unique_id', $validated['unique_id'])->firstOrFail();
            $question->update([
                'question_text' => $validated['question_text'],
                'option_a' => $validated['option_a'],
                'option_b' => $validated['option_b'],
                'option_c' => $validated['option_c'],
                'option_d' => $validated['option_d'],
                'option_e' => $validated['option_e'],
                'option_f' => $validated['option_f'],
                'answer_1' => $validated['answer_1'],
                'answer_2' => $validated['answer_2'],
                'answer_3' => $validated['answer_3'],
            ]);

            DB::commit();

            Log::info('Question updated successfully', ['unique_id' => $validated['unique_id']]);
            return response()->json(['message' => 'Question updated successfully!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed', ['errors' => $e->errors(), 'payload' => $request->all()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Question not found', ['unique_id' => $request->input('unique_id'), 'trace' => $e->getTraceAsString()]);
            return response()->json(['errors' => ['general' => 'Question not found.']], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Question update failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'payload' => $request->all()]);
            return response()->json(['errors' => ['general' => 'Failed to update question. Please try again.']], 500);
        }
    }

    public function deleteQuestion(Request $request)
    {
        try {
            Log::info('Received delete request', ['payload' => $request->all()]);

            $validated = $request->validate([
                'unique_id' => 'required|exists:m1_question,unique_id',
            ]);

            $question = M1Question::where('unique_id', $validated['unique_id'])->firstOrFail();
            $question->delete();

            Log::info('Question deleted successfully', ['unique_id' => $validated['unique_id']]);
            return response()->json(['message' => 'Question deleted successfully!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors(), 'payload' => $request->all()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Question not found', ['unique_id' => $request->input('unique_id'), 'trace' => $e->getTraceAsString()]);
            return response()->json(['errors' => ['general' => 'Question not found.']], 404);
        } catch (\Exception $e) {
            Log::error('Question deletion failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'payload' => $request->all()]);
            return response()->json(['errors' => ['general' => 'Failed to delete question. Please try again.']], 500);
        }
    }

    public function deleteAllQuestions(Request $request)
    {
        try {
            Log::info('Received delete all request', ['payload' => $request->all()]);

            $validated = $request->validate([
                'section_unique_id' => 'required|exists:mock,unique_id',
            ]);

            $deletedCount = M1Question::where('mock_unique_id', $validated['section_unique_id'])->delete();

            Log::info('All questions deleted for mock section', ['section_unique_id' => $validated['section_unique_id'], 'deleted_count' => $deletedCount]);
            return response()->json(['message' => "$deletedCount question(s) deleted successfully!"], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors(), 'payload' => $request->all()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('All questions deletion failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'payload' => $request->all()]);
            return response()->json(['errors' => ['general' => 'Failed to delete questions. Please try again.']], 500);
        }
    }
}