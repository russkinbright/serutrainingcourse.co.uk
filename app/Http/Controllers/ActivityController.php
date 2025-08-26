<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function getActivities(Request $request)
    {
        $lastId = $request->query('last_id', 0);
        $activities = Activity::where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($activities);
    }


    public function pollActivities(Request $request)
    {
        $lastId  = $request->query('last_id', 0);
        $timeout = 1;
        $start   = microtime(true);

        while (microtime(true) - $start < $timeout) {
            $new = Activity::where('id', '>', $lastId)->orderBy('id')->get();

            if ($new->isNotEmpty()) {
                return response()->json($new);
            }
            usleep(200_000);         // sleep 0.2 s  (→ average latency ≈0.1 s)
        }
        return response()->json([]);
    }
}
