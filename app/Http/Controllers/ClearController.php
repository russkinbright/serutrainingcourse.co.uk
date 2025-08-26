<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ClearController extends Controller
{
    public function clearCache(Request $request)
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            Log::info('Application cache cleared successfully.');

            // Clear configuration cache
            Artisan::call('config:clear');
            Log::info('Configuration cache cleared successfully.');

            // Clear route cache
            Artisan::call('route:clear');
            Log::info('Route cache cleared successfully.');

            // Clear view cache
            Artisan::call('view:clear');
            Log::info('View cache cleared successfully.');

            // Clear compiled classes
            Artisan::call('clear-compiled');
            Log::info('Compiled classes cleared successfully.');

            // Clear event cache
            Artisan::call('event:clear');
            Log::info('Event cache cleared successfully.');

            return response()->json([
                'status' => 'success',
                'message' => 'All caches cleared successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error clearing caches: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear caches: ' . $e->getMessage()
            ], 500);
        }
    }
}