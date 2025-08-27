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
            $pixel = Pixel::firstOrCreate(['id' => $id], ['header'=>'','body'=>'','footer'=>'']);
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
                'message' => 'Error fetching pixel data: ' . $e->getMessage(),
            ], 500);
        }
    }

   public function update(Request $request, $id)
        {
            Log::info('Incoming pixel update request', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
                'pixel_id' => $id,
            ]);

            $validator = Validator::make($request->all(), [
                'header' => 'nullable|string',
                'body'   => 'nullable|string',
                'footer' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // decode base64 sent from client
            $header = base64_decode((string)$request->input('header', ''), true);
            $body   = base64_decode((string)$request->input('body', ''), true);
            $footer = base64_decode((string)$request->input('footer', ''), true);

            $header = $header === false ? '' : $header;
            $body   = $body   === false ? '' : $body;
            $footer = $footer === false ? '' : $footer;

            if (trim($header.$body.$footer) === '') {
                return response()->json([
                    'errors' => ['form' => ['Please provide at least one pixel code (header, body, or footer).']]
                ], 422);
            }

            $data = compact('header','body','footer');

            // ensure the same record is created/updated by id
            $pixel = Pixel::updateOrCreate(['id' => $id], $data);

            Log::info('Pixel data saved', [
                'pixel_id' => $pixel->id,
                'header_length' => strlen($header),
                'body_length'   => strlen($body),
                'footer_length' => strlen($footer),
            ]);

            return response()->json([
                'message' => 'Pixel data saved successfully!',
                'pixel'   => $pixel,
            ], 200);
        }

}