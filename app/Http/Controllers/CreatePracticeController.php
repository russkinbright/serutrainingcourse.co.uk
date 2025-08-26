<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Practice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreatePracticeController extends Controller
{
    public function index()
    {
        return view('lms.createPractice');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $sections = Section::query()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->get(['id', 'name', 'unique_id']);

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'practices' => 'required|array|min:1',
                'practices.*.name' => 'required|string|max:255',
                'practices.*.tag' => 'required|string|max:255',
            ]);

            $createdPractices = [];

            foreach ($request->practices as $practiceData) {
                $uniqueId = random_int(1000000, 9999999);
                while (Practice::where('unique_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000, 9999999);
                }

                $practice = Practice::create([
                    'unique_id' => $uniqueId,
                    'tag' => $practiceData['tag'],
                    'name' => $practiceData['name'],
                ]);

                $createdPractices[] = $practice;
            }

            return response()->json([
                'message' => 'Practices created successfully!',
                'practices' => $createdPractices,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Practice creation failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to create practices. Please try again.']], 500);
        }
    }
}
