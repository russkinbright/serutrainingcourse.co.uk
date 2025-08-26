<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateSectionController extends Controller
{
    public function index()
    {
        return view('lms.createSection');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sections' => 'required|array|min:1',
                'sections.*.name' => 'required|string|max:255',
                'sections.*.sequence' => 'required|integer|min:1',
            ]);

            $sections = $request->sections;
            $createdSections = [];

            foreach ($sections as $sectionData) {
                $uniqueId = random_int(1000000, 9999999);
                while (Section::where('unique_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000, 9999999);
                }

                $section = Section::create([
                    'unique_id' => $uniqueId,
                    'name' => $sectionData['name'],
                    'sequence' => $sectionData['sequence'],
                ]);

                $createdSections[] = $section;
            }

            return response()->json([
                'message' => 'Sections created successfully!',
                'sections' => $createdSections,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Section creation failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to create sections. Please try again.']], 500);
        }
    }
}