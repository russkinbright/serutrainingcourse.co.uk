<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LearnerController extends Controller
{
    public function showLearnerPage()
    {
        $learner = Auth::guard('learner')->user();

        if (!$learner) {
            return redirect()->route('learner.learnerLogin')->with('message', 'Please log in to access your dashboard.');
        }

        $courses = [
            [
                'name' => 'Graphic Design Bootcamp: Advanced Training Using Adobe CCas',
                'total_enrollment' => 10,
                'rating' => 4.0,
            ],
        ];

        return view('learner.learnerPage', compact('learner', 'courses'));
    }
}
