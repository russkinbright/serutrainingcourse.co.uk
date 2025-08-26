<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Section;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConnectSectionController extends Controller
{
    public function index()
    {
        return view('lms.connectSection');
    }

    public function searchCourses(Request $request)
    {
        $query = $request->input('query');
        $courses = Course::query()
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->get(['id', 'title', 'unique_id', 'price', 'rating', 'image'])
            ->toArray();

        return response()->json($courses);
    }

    public function searchSections(Request $request)
    {
        $query = $request->input('query');
        $sections = Section::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->get(['id', 'name', 'unique_id'])
            ->toArray();

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_unique_id' => 'required|exists:course,unique_id',
                'section_unique_ids' => 'required|array|min:1',
                'section_unique_ids.*' => 'exists:section,unique_id',
            ]);

            DB::beginTransaction();

            $courseUniqueId = $validated['course_unique_id'];
            $sectionUniqueIds = $validated['section_unique_ids'];

            $createdConnections = [];

            foreach ($sectionUniqueIds as $sectionUniqueId) {
                // Check if the connection already exists to avoid duplicates
                $exists = CourseSection::where('course_unique_id', $courseUniqueId)
                    ->where('section_unique_id', $sectionUniqueId)
                    ->exists();

                if (!$exists) {
                    $connection = CourseSection::create([
                        'course_unique_id' => $courseUniqueId,
                        'section_unique_id' => $sectionUniqueId,
                    ]);
                    $createdConnections[] = $connection;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Sections connected to course successfully!',
                'connections' => $createdConnections,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Section connection failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to connect sections. Please try again.']], 500);
        }
    }
}