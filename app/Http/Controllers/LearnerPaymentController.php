<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseDesign;
use App\Models\Learner;
use App\Models\Payment;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LearnerPaymentController extends Controller
{
    public function showPayment(Request $request)
    {
        return view('learner.payment');
    }

    public function getBilling(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            return response()->json([
                'success' => true,
                'billing' => [
                    'fullName' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'address' => $user->address,
                    'city' => $user->city,
                    'postalCode' => $user->postal_code,
                    'message' => $user->message,
                    'whom' => $user->whom,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching billing info: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching billing information'], 500);
        }
    }

    public function saveBilling(Request $request)
    {
        try {
            $data = $request->validate([
                'billing.whom' => 'required|in:personal,company',
                'billing.fullName' => 'required|string',
                'billing.email' => 'required|email',
                'billing.phone' => 'required|string',
                'billing.country' => 'required|string',
                'billing.address' => 'required|string',
                'billing.city' => 'required|string',
                'billing.postalCode' => 'required|string',
                'billing.message' => 'nullable|string'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $user->update([
                'whom' => $data['billing']['whom'],
                'name' => $data['billing']['fullName'],
                'email' => $data['billing']['email'],
                'phone' => $data['billing']['phone'],
                'country' => $data['billing']['country'],
                'address' => $data['billing']['address'],
                'city' => $data['billing']['city'],
                'postal_code' => $data['billing']['postalCode'],
                'message' => $data['billing']['message'],
            ]);

            return response()->json(['success' => true, 'message' => 'Billing information saved successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Billing save error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while saving billing information'], 500);
        }
    }

    public function processPayment(Request $request)
    {
        try {
            $data = $request->validate([
                'cartItems' => 'required|array',
                'cartItems.*.id' => 'required',
                'cartItems.*.name' => 'required|string',
                'cartItems.*.category' => 'required|string',
                'cartItems.*.price' => 'required|numeric|min:0',
                'cartItems.*.quantity' => 'required|integer|min:1',
                'cartItems.*.image' => 'nullable|string',
                'cartItems.*.unique_id' => 'required', // course_design.unique_id
                'paymentMethod' => 'required|in:card,paypal,other',
                'billing' => 'required|array',
                'billing.whom' => 'required|in:personal,company',
                'billing.fullName' => 'required|string',
                'billing.email' => 'required|email',
                'billing.phone' => 'required|string',
                'billing.country' => 'required|string',
                'billing.address' => 'required|string',
                'billing.city' => 'required|string',
                'billing.postalCode' => 'required|string',
                'billing.message' => 'nullable|string',
                'total' => 'required|numeric|min:0'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            if (!$user->country || !$user->address || !$user->city || !$user->postal_code || !$user->whom) {
                return response()->json(['success' => false, 'message' => 'Please complete your billing information'], 400);
            }

            // Generate 8-digit unique ID for this transaction
            $paymentUniqueId = mt_rand(10000000, 99999999);

            foreach ($data['cartItems'] as $item) {
                $course = CourseDesign::find($item['id']);
                if (!$course || $course->price != $item['price'] || $course->unique_id != $item['unique_id']) {
                    return response()->json(['success' => false, 'message' => 'Invalid course data'], 400);
                }

                // Create individual payment record
                $payment = Payment::create([
                    'payment_unique_id' => $paymentUniqueId,
                    'learner_secret_id' => $user->secret_id,
                    'whom' => $data['billing']['whom'],
                    'name' => $data['billing']['fullName'],
                    'email' => $data['billing']['email'],
                    'phone' => $data['billing']['phone'],
                    'city' => $data['billing']['city'],
                    'address' => $data['billing']['address'],
                    'postal_code' => $data['billing']['postalCode'],
                    'country' => $data['billing']['country'],
                    'payment_type' => $data['paymentMethod'],
                    'course_unique_id' => $item['unique_id'],
                    'quantity' => $item['quantity'],
                    'course_title' => $item['name'],
                    'price' => $item['price'],
                    'message' => $data['billing']['message'],
                    'media' => 'Course Cave',
                    // 'status' => $data['paymentMethod'] === 'other' ? 'completed' : 'pending',
                ]);

                // Create course progress if applicable
                if ($data['billing']['whom'] === 'personal') {
                    CourseProgress::create([
                        'learner_secret_id' => $user->secret_id,
                        'course_unique_id' => $item['unique_id'],
                        'progress' => '0',
                    ]);
                }
            }

            // Simulated success response
            if ($data['paymentMethod'] === 'card') {
                return response()->json(['success' => false, 'message' => 'Card payment not implemented yet'], 400);
            } elseif ($data['paymentMethod'] === 'paypal') {
                return response()->json(['success' => false, 'message' => 'PayPal payment not implemented yet'], 400);
            }

            return response()->json(['success' => true, 'message' => 'Payment processed successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
