<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

// NEW: models & mailable you already use in StripeController
use App\Models\Course;
use App\Models\Payment;
use App\Models\Learner;
use App\Models\Activity;
use App\Models\CourseProgress;
use App\Mail\PaymentConfirmationMail;

class PaymentFormController extends Controller
{
    public function showPayment(Request $request)
    {
        return view('course.paymentForm');
    }

    public function processPayment(Request $request)
    {
        try {
            $data = $request->validate([
                'cartItems' => 'required|array',
                'cartItems.*.name' => 'required|string',
                'cartItems.*.price' => 'required|numeric|min:0',
                'cartItems.*.quantity' => 'required|integer|min:1',
                'cartItems.*.unique_id' => 'required|numeric',
                'paymentMethod' => 'required|in:card,paypal,other', // note: 'other'
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

            // ------ CARD → Stripe redirect (unchanged) ------
            if ($data['paymentMethod'] === 'card') {
                Session::put('stripe_cart', $data['cartItems']);
                Session::put('stripe_billing', $data['billing']);
                Session::put('stripe_total', $data['total']);

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('stripe.payment')
                ]);
            }

            // ------ PayPal redirect (unchanged) ------
            if ($data['paymentMethod'] === 'paypal') {
                Session::put('paypal_cart', $data['cartItems']);
                Session::put('paypal_billing', $data['billing']);
                Session::put('paypal_total', $data['total']);

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('paypal.create', ['amount' => $data['total']])
                ]);
            }

            // ------ OTHER: instant success, write DB ------
            if ($data['paymentMethod'] === 'other') {

                // 1) Recompute total & validate courses to avoid tampering
                $calcTotal = 0.0;
                foreach ($data['cartItems'] as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) {
                        return response()->json(['success' => false, 'message' => 'Course not found.'], 404);
                    }
                    if ((float)$course->price !== (float)$item['price']) {
                        return response()->json(['success' => false, 'message' => 'Course price mismatch.'], 422);
                    }
                    $calcTotal += ((float)$item['price']) * (int)$item['quantity'];
                }
                if (abs($calcTotal - (float)$data['total']) > 0.01) {
                    return response()->json(['success' => false, 'message' => 'Total amount mismatch.'], 422);
                }

                // 2) Create or fetch learner
                $billing = $data['billing'];
                $existingLearner = Learner::where('email', $billing['email'])->first();
                $plainPassword = null;

                if ($existingLearner) {
                    $learnerSecretId = $existingLearner->secret_id;
                } else {
                    do {
                        $generatedSecretId = mt_rand(1000000, 9999999);
                    } while (Learner::where('secret_id', $generatedSecretId)->exists());

                    $plainPassword = Str::random(8);
                    $newLearner = Learner::create([
                        'secret_id'    => $generatedSecretId,
                        'name'         => $billing['fullName'] ?? 'Learner',
                        'email'        => $billing['email'],
                        'payment_type' => 'other',
                        'whom'         => $billing['whom'] ?? null,
                        'phone'        => $billing['phone'] ?? null,
                        'message'      => $billing['message'] ?? null,
                        'password'     => Hash::make($plainPassword),
                        'country'      => $billing['country'] ?? null,
                        'city'         => $billing['city'] ?? null,
                        'address'      => $billing['address'] ?? null,
                        'postal_code'  => $billing['postalCode'] ?? null,
                    ]);
                    $learnerSecretId = $newLearner->secret_id;
                }

                // 3) Write payments + course progress
                $paymentUniqueId = $this->generateUniquePaymentId();
                $fakeTxnId = 'OTHER-' . Str::upper(Str::random(10));

                foreach ($data['cartItems'] as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) continue;

                    Payment::create([
                        'payment_unique_id' => $paymentUniqueId,
                        'learner_secret_id' => $learnerSecretId,
                        'name'              => $billing['fullName'] ?? 'Learner',
                        'email'             => $billing['email'],
                        'phone'             => $billing['phone'] ?? null,
                        'city'              => $billing['city'] ?? null,
                        'address'           => $billing['address'] ?? null,
                        'postal_code'       => $billing['postalCode'] ?? null,
                        'country'           => $billing['country'] ?? null,
                        'payment_type'      => 'other',
                        'course_unique_id'  => $item['unique_id'],
                        'whom'              => $billing['whom'] ?? null,
                        'quantity'          => (int)$item['quantity'],
                        'course_title'      => $item['name'],
                        'price'             => (float)$item['price'],
                        'media'             => Session::get('ref_source', 'Unknown'),
                        'message'           => $billing['message'] ?? null,
                        'status'            => 'completed',
                        'transaction_id'    => $fakeTxnId,
                        'account_id'        => null,
                        'stripe_email'      => null,
                    ]);

                    $expireAt = now();
                    if ($course && preg_match('/(\d+)\s*week/i', $course->duration, $m)) {
                        $expireAt = now()->addWeeks((int) $m[1]);
                    }

                    CourseProgress::create([
                        'learner_secret_id' => $learnerSecretId,
                        'course_unique_id'  => $item['unique_id'],
                        'permodule'         => 0,
                        'perquestion'       => 0,
                        'is_completed'      => false,
                        'progress'          => 0,
                        'mark'              => 0,
                        'total_mark'        => 0,
                        'expire_at'         => $expireAt,
                    ]);

                    $lineTotal = (float)$item['price'] * (int)$item['quantity'];
                    Activity::create([
                        'message' => sprintf(
                            '<strong>%s</strong> (%s) enrolled in <strong>%s</strong> x %d. Transaction ID: <code>%s</code>, Amount: <strong>£%s</strong>.',
                            e($billing['fullName']),
                            e($billing['email']),
                            e($item['name']),
                            (int)$item['quantity'],
                            e($fakeTxnId),
                            e(number_format($lineTotal, 2))
                        ),
                    ]);
                }

                // 4) Email
                try {
                    Mail::to($billing['email'])->send(new PaymentConfirmationMail($billing, $data['cartItems'], $plainPassword));
                } catch (\Throwable $e) {
                    Log::error('Other payment email failed: '.$e->getMessage());
                }

                // 5) Done
                Session::forget(['ref_source']);

                return response()->json([
                    'success' => true,
                    'message' => 'Purchase successfully completed.'
                    // no redirect_url → frontend shows success inline
                ]);
            }

            // default
            return response()->json(['success' => true, 'message' => 'Payment processed successfully']);

        } catch (ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors(), 'input' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /** Generate unique payment id (copied pattern from StripeController) */
    protected function generateUniquePaymentId()
    {
        do {
            $id = mt_rand(10000000, 99999999);
        } while (Payment::where('payment_unique_id', $id)->exists());
        return $id;
    }
}
