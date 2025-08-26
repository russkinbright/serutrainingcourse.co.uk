<?php

namespace App\Http\Controllers;

use App\Models\Mock;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateMockController extends Controller
{
    public function index()
    {
        return view('lms.createMock');
    }

    public function search(Request $request)
    {
        $query = trim($request->input('query'));
        if (empty($query)) {
            return response()->json([]);
        }

        $sections = Section::where('name', 'LIKE', "%{$query}%")
            ->orWhere('unique_id', 'LIKE', "%{$query}%")
            ->select('unique_id', 'name')
            ->take(10)
            ->get();

        return response()->json($sections);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'mocks' => 'required|array|min:1',
                'mocks.*.mock_number' => 'required|in:mock 1,mock 2',
                'mocks.*.tag' => 'required|string|max:255',
                'mocks.*.name' => 'required|string|max:255',
            ]);

            $createdMocks = [];

            foreach ($request->mocks as $mockData) {
                // Generate unique_id
                $uniqueId = random_int(1000000, 9999999);
                while (Mock::where('unique_id', $uniqueId)->exists()) {
                    $uniqueId = random_int(1000000, 9999999);
                }

                $mock = Mock::create([
                    'unique_id' => $uniqueId,
                    'mock_number' => $mockData['mock_number'],
                    'tag' => $mockData['tag'],
                    'name' => $mockData['name'],
                ]);

                $createdMocks[] = $mock;
            }

            return response()->json([
                'message' => 'Mock tests created successfully!',
                'mocks' => $createdMocks,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Mock test creation failed: ' . $e->getMessage());
            return response()->json(['errors' => ['general' => 'Failed to create mock tests. Please try again.']], 500);
        }
    }
}