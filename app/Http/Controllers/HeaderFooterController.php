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
            $pixel = Pixel::findOrFail($id);
            Log::info('Fetching pixel data', [
                'pixel_id' => $id,
                'header_length' => strlen($pixel->header ?? ''),
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
                'message' => 'Pixel data not found.',
            ], 404);
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

        try {
            $pixel = Pixel::findOrFail($id);
            $pixel->update([
                'header' => $request->header,
                'footer' => $request->footer,
            ]);

            Log::info('Pixel data updated successfully', [
                'pixel_id' => $pixel->id,
                'header_length' => strlen($pixel->header ?? ''),
                'footer_length' => strlen($pixel->footer ?? ''),
            ]);

            return response()->json([
                'message' => 'Pixel data updated successfully!',
                'pixel' => $pixel,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error occurred while updating pixel data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while updating the pixel data.',
            ], 500);
        }
    }
}