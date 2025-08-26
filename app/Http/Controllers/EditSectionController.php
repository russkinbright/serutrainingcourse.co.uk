<?php
namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\CourseSection;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EditSectionController extends Controller
{
    public function index()
    {
        Log::info('Accessed edit section index page');
        return view('lms.editSection');
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query'));
        Log::info("Searching sections with query: {$query}");

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            $sections = Section::query()
                ->where('section.name', 'LIKE', "%{$query}%")
                ->orWhere('section.unique_id', 'LIKE', "%{$query}%")
                ->leftJoin('course_section', 'section.unique_id', '=', 'course_section.section_unique_id')
                ->leftJoin('course', 'course_section.course_unique_id', '=', 'course.unique_id')
                ->select(
                    'section.id',
                    'section.name',
                    'section.sequence',
                    'section.unique_id'
                )
                ->groupBy('section.id', 'section.name', 'section.sequence', 'section.unique_id')
                ->with(['courseSections.course' => function ($query) {
                    $query->select('course.unique_id', 'course.title');
                }])
                ->take(10)
                ->get()
                ->map(function ($section) {
                    $courseTitles = $section->courseSections->pluck('course.title')->filter()->implode(', ');
                    return [
                        'id' => $section->id,
                        'name' => $section->name,
                        'sequence' => $section->sequence,
                        'unique_id' => $section->unique_id,
                        'course_titles' => $courseTitles ?: 'None'
                    ];
                });

            Log::info("Found sections: " . $sections->toJson());
            return response()->json($sections);
        } catch (\Exception $e) {
            Log::error("Error searching sections: " . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to search sections: ' . $e->getMessage()]], 500);
        }
    }

    public function show($id)
    {
        try {
            $section = Section::query()
                ->where('section.id', $id)
                ->select('section.id', 'section.name', 'section.sequence', 'section.unique_id')
                ->with(['courseSections.course' => function ($query) {
                    $query->select('course.unique_id', 'course.title');
                }])
                ->firstOrFail();

            $courseTitles = $section->courseSections->pluck('course.title')->filter()->implode(', ');
            $response = [
                'id' => $section->id,
                'name' => $section->name,
                'sequence' => $section->sequence,
                'unique_id' => $section->unique_id,
                'course_titles' => $courseTitles ?: 'None'
            ];

            Log::info("Fetched section details: " . json_encode($response));
            return response()->json($response);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Section not found: ID {$id}");
            return response()->json(['errors' => ['general' => 'Section not found.']], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching section: " . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to load section details: ' . $e->getMessage()]], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Received update request for section ID: ' . $id, ['payload' => $request->all()]);

            $section = Section::findOrFail($id);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sequence' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $section->update([
                'name' => $validated['name'],
                'sequence' => $validated['sequence'],
            ]);

            DB::commit();

            Log::info('Section updated successfully', ['id' => $id, 'name' => $validated['name']]);
            return response()->json([
                'message' => 'Section updated successfully!',
                'section' => [
                    'id' => $section->id,
                    'name' => $section->name,
                    'sequence' => $section->sequence,
                    'unique_id' => $section->unique_id,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation failed for section update', ['errors' => $e->errors(), 'payload' => $request->all()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Section not found for update: ID ' . $id);
            return response()->json(['errors' => ['general' => 'Section not found.']], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Section update failed: ' . $e->getMessage(), ['payload' => $request->all()]);
            return response()->json(['errors' => ['general' => 'Failed to update section: ' . $e->getMessage()]], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Log::info('Received delete request for section ID: ' . $id);

            $section = Section::findOrFail($id);
            $section->delete();

            Log::info('Section deleted successfully', ['id' => $id]);
            return response()->json(['message' => 'Section deleted successfully!'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Section not found for deletion: ID ' . $id);
            return response()->json(['errors' => ['general' => 'Section not found.']], 404);
        } catch (\Exception $e) {
            Log::error('Section deletion failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to delete section: ' . $e->getMessage()]], 500);
        }
    }
}