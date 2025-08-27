<?php

namespace App\Http\Controllers;

use App\Models\Pixel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HeaderFooterController extends Controller
{
    public function index()
    {
        return view('lms.headerFooter');
    }

    public function show($id)
    {
        try {
            $pixel = Pixel::find($id);
            if (!$pixel) {
                // Create a default record if none exists
                $pixel = Pixel::create([
                    'id' => $id,
                    'header' => '',
                    'body' => '',
                    'footer' => '',
                ]);
                Log::info('Created default pixel record', ['pixel_id' => $id]);
            }

            Log::info('Fetching pixel data', [
                'pixel_id' => $id,
                'header_length' => strlen($pixel->header ?? ''),
                'body_length' => strlen($pixel->body ?? ''),
                'footer_length' => strlen($pixel->footer ?? ''),
            ]);

            return response()->json([
                'pixel' => $pixel,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching pixel data', [
                'pixel_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Error fetching pixel data.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Incoming pixel update request', [
            'data' => $request->all(),
            'ip' => $request->ip(),
            'user_id' => auth()->check() ? auth()->id() : null,
            'pixel_id' => $id,
        ]);

        $validator = Validator::make($request->all(), [
            'header' => 'nullable|string',
            'body' => 'nullable|string',
            'footer' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed during pixel update', [
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if at least one field is provided
        if (!$request->filled('header') && !$request->filled('body') && !$request->filled('footer')) {
            Log::warning('No pixel data provided for update', ['pixel_id' => $id]);

            return response()->json([
                'errors' => ['form' => ['Please provide at least one pixel code (header, body, or footer).']],
            ], 422);
        }

        try {
            $pixel = Pixel::find($id);
            if (!$pixel) {
                // Create a new record if none exists
                $pixel = Pixel::create([
                    'id' => $id,
                    'header' => $request->header ?? '',
                    'body' => $request->body ?? '',
                    'footer' => $request->footer ?? '',
                ]);
                Log::info('Created new pixel record during update', ['pixel_id' => $id]);
            } else {
                $pixel->update([
                    'header' => $request->header ?? '',
                    'body' => $request->body ?? '',
                    'footer' => $request->footer ?? '',
                ]);
                Log::info('Pixel data updated successfully', ['pixel_id' => $pixel->id]);
            }

            return response()->json([
                'message' => 'Pixel data saved successfully!',
                'pixel' => $pixel,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error occurred while saving pixel data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while saving the pixel data.',
            ], 500);
        }
    }
}