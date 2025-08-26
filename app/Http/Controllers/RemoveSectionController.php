<?php
namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveSectionController extends Controller
{
    public function index()
    {
        return view('lms.removeSection');
    }

    public function searchCourses(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json([]);
        }

        $courses = Course::where('title', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->get(['id', 'unique_id', 'title', 'price', 'rating', 'footer_price', 'image']);

        Log::info("Course search query: {$query}, found: " . $courses->count());
        return response()->json($courses);
    }

    public function getSections($course_unique_id)
    {
        try {
            $course = Course::where('unique_id', $course_unique_id)->first();
            if (!$course) {
                Log::warning("Course not found for unique_id: {$course_unique_id}");
                return response()->json(['errors' => ['Course not found']], 404);
            }

            $sections = DB::table('course_section')
                ->join('course', 'course_section.course_unique_id', '=', 'course.unique_id')
                ->join('section', 'course_section.section_unique_id', '=', 'section.unique_id')
                ->where('course.unique_id', $course_unique_id)
                ->select('section.id', 'section.unique_id', 'section.name')
                ->get();

            Log::info("Fetched sections for course {$course_unique_id}: " . $sections->toJson());
            return response()->json($sections);
        } catch (\Exception $e) {
            Log::error("Error fetching sections for course {$course_unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to fetch sections: ' . $e->getMessage()]], 500);
        }
    }

    public function removeSection($course_unique_id, $section_unique_id)
    {
        try {
            $course = Course::where('unique_id', $course_unique_id)->first();
            if (!$course) {
                Log::warning("Course not found for unique_id: {$course_unique_id}");
                return response()->json(['errors' => ['Course not found']], 404);
            }

            $section = Section::where('unique_id', $section_unique_id)->first();
            if (!$section) {
                Log::warning("Section not found for unique_id: {$section_unique_id}");
                return response()->json(['errors' => ['Section not found']], 404);
            }

            $deleted = DB::table('course_section')
                ->where('course_unique_id', $course->unique_id)
                ->where('section_unique_id', $section->unique_id)
                ->delete();

            if ($deleted) {
                Log::info("Section {$section_unique_id} removed from course {$course_unique_id}");
                return response()->json(['message' => 'Section removed from course successfully']);
            } else {
                Log::warning("No course_section record found for course {$course_unique_id} and section {$section_unique_id}");
                return response()->json(['errors' => ['Section not found in course']], 422);
            }
        } catch (\Exception $e) {
            Log::error("Error removing section {$section_unique_id} from course {$course_unique_id}: " . $e->getMessage());
            return response()->json(['errors' => ['Failed to remove section: ' . $e->getMessage()]], 500);
        }
    }
}