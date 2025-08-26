<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseDesign;
use Illuminate\Support\Facades\Log;

class LearnerCartController extends Controller
{
    public function showCart(Request $request)
{
    $uniqueId = $request->input('unique_id');

    // Guard against missing unique ID
   

    // Get course metadata
    $lmsCourse = Course::where('unique_id', $uniqueId)->firstOrFail();

    // Get course display design info
    $course = CourseDesign::where('unique_id', $uniqueId)->firstOrFail();

    // Inject necessary LMS data
    $course->category = $lmsCourse->category ?? 'Uncategorized';
    $course->title = $lmsCourse->title ?? 'Untitled';

    return view('learner.learnerCart', compact('course'));
}


    public function showCartFromLogin(Request $request)
    {
        $decodedTitle = str_replace('-', ' ', $request->title);

        // Find the course by the decoded title
        $course = Course::where('title', 'like', '%' . $decodedTitle . '%')->firstOrFail();
        $course->rating = 4.0;

        return view('course.cart', compact('course'));
    }
}
