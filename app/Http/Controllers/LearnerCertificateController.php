<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\CertificateOrder;
use App\Models\CourseDesign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LearnerCertificateController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Fetch certificate details for a completed course
     */
    public function getCertificateDetails($unique_id)
    {
        try {
            $learner = Auth::user();
            if (!$learner || !$learner->secret_id) {
                return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
            }

            // Verify course exists and is completed
            $course = CourseDesign::where('unique_id', $unique_id)->firstOrFail();
            $progress = CourseProgress::where('learner_secret_id', $learner->secret_id)
                ->where('course_unique_id', $unique_id)
                ->first();

            if (!$progress || !$progress->is_completed) {
                return response()->json(['success' => false, 'message' => 'Course not completed.'], 403);
            }

            // Define certificate options (replace image paths with actual asset URLs)
            $certificateOptions = [
                [
                    'type' => 'digital',
                    'title' => 'Digital Certificate',
                    'description' => 'Receive a PDF version of your certificate via email.',
                    'price' => 0.00,
                    'image' => asset('image/certificates.png')
                ],
                [
                    'type' => 'printed',
                    'title' => 'Printed Certificate',
                    'description' => 'Get a high-quality printed certificate delivered to your address.',
                    'price' => 19.99,
                    'image' => asset('image/transcript.png')
                ]
            ];

            Log::info('Fetched certificate details:', [
                'course_id' => $unique_id,
                'learner_id' => $learner->secret_id
            ]);

            return response()->json([
                'success' => true,
                'certificate' => [
                    'course_unique_id' => $course->unique_id,
                    'course_title' => $course->course_title,
                    'learner_name' => $learner->name
                ],
                'options' => $certificateOptions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching certificate details: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load certificate details.'], 404);
        }
    }

    /**
     * Process certificate order
     */
    // public function placeCertificateOrder(Request $request, $unique_id)
    // {
    //     try {
    //         $learner = Auth::user();
    //         if (!$learner || !$learner->secret_id) {
    //             return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    //         }

    //         // Validate request
    //         $validated = $request->validate([
    //             'type' => 'required|in:digital,printed',
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|email|max:255',
    //             'address' => 'required_if:type,printed|string|max:500',
    //             'payment_method' => 'required_if:type,printed|in:card,paypal'
    //         ]);

    //         // Verify course completion
    //         $course = Course::where('unique_id', $unique_id)->firstOrFail();
    //         $progress = CourseProgress::where('learner_secret_id', $learner->secret_id)
    //             ->where('course_unique_id', $unique_id)
    //             ->first();

    //         if (!$progress || !$progress->is_completed) {
    //             return response()->json(['success' => false, 'message' => 'Course not completed.'], 403);
    //         }

    //         // Check for existing order
    //         $existingOrder = CertificateOrder::where('learner_secret_id', $learner->secret_id)
    //             ->where('course_unique_id', $unique_id)
    //             ->where('type', $validated['type'])
    //             ->first();

    //         if ($existingOrder) {
    //             return response()->json(['success' => false, 'message' => 'Certificate already ordered.'], 400);
    //         }

    //         // Create order
    //         $order = CertificateOrder::create([
    //             'learner_secret_id' => $learner->secret_id,
    //             'course_unique_id' => $unique_id,
    //             'type' => $validated['type'],
    //             'name' => $validated['name'],
    //             'email' => $validated['email'],
    //             'address' => $validated['address'] ?? null,
    //             'payment_method' => $validated['payment_method'] ?? null,
    //             'price' => $validated['type'] === 'digital' ? 0.00 : 19.99,
    //             'status' => 'pending'
    //         ]);

    //         // TODO: Integrate payment gateway for printed certificates
    //         // For now, assume payment is processed successfully

    //         Log::info('Certificate order placed:', [
    //             'order_id' => $order->id,
    //             'course_id' => $unique_id,
    //             'learner_id' => $learner->secret_id,
    //             'type' => $validated['type']
    //         ]);

    //         return response()->json(['success' => true, 'message' => 'Certificate order placed successfully.']);
    //     } catch (\Exception $e) {
    //         Log::error('Error placing certificate order: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'message' => 'Failed to place order.']);
    //     }
    // }
}