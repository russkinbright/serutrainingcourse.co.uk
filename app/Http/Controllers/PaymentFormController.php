<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
                'cartItems' => 'required|array|min:1',
                'cartItems.*.name' => 'required|string',
                'cartItems.*.price' => 'required|numeric|min:0',
                'cartItems.*.quantity' => 'required|integer|min:1',
                'cartItems.*.unique_id' => 'required',
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
                'couponCode' => 'nullable|string',
                'total' => 'required|numeric|min:0'
            ]);

            // === Stripe path ===
            if ($data['paymentMethod'] === 'card') {
                Session::put('stripe_cart', $data['cartItems']);
                Session::put('stripe_billing', $data['billing']);
                Session::put('stripe_total', $data['total']);

                return redirect()->route('stripe.payment');
            }

            // === PayPal path (custom handling) ===
            if ($data['paymentMethod'] === 'paypal' || $data['paymentMethod'] === 'other') {
                // Validate & recompute total
                $calcTotal = 0;
                foreach ($data['cartItems'] as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) {
                        return back()->withErrors(['Course not found: '.$item['name']]);
                    }
                    if ((float)$course->price !== (float)$item['price']) {
                        return back()->withErrors(['Course price mismatch for '.$item['name']]);
                    }
                    $calcTotal += ((float)$item['price']) * (int)$item['quantity'];
                }
                if (abs($calcTotal - (float)$data['total']) > 0.01) {
                    return back()->withErrors(['Total amount mismatch.']);
                }

                DB::beginTransaction();
                try {
                    // Find or create learner
                    $existing = Learner::where('email', $data['billing']['email'])->first();
                    $plainPassword = null;

                    if ($existing) {
                        $learnerSecretId = $existing->secret_id;
                    } else {
                        do {
                            $secret = mt_rand(1000000, 9999999);
                        } while (Learner::where('secret_id', $secret)->exists());

                        $plainPassword = Str::random(8);

                        $newLearner = Learner::create([
                            'secret_id'    => $secret,
                            'name'         => $data['billing']['fullName'],
                            'email'        => $data['billing']['email'],
                            'payment_type' => $data['paymentMethod'],
                            'whom'         => $data['billing']['whom'],
                            'phone'        => $data['billing']['phone'],
                            'message'      => $data['billing']['message'] ?? null,
                            'password'     => Hash::make($plainPassword),
                            'country'      => $data['billing']['country'],
                            'city'         => $data['billing']['city'],
                            'address'      => $data['billing']['address'],
                            'postal_code'  => $data['billing']['postalCode'],
                        ]);

                        $learnerSecretId = $newLearner->secret_id;
                    }

                    // Payment + CourseProgress + Activity
                    $paymentUniqueId = $this->generateUniquePaymentId();
                    $txnId = strtoupper($data['paymentMethod']).'-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

                    foreach ($data['cartItems'] as $item) {
                        $course = Course::where('unique_id', $item['unique_id'])->first();

                        Payment::create([
                            'payment_unique_id' => $paymentUniqueId,
                            'learner_secret_id' => $learnerSecretId,
                            'name'              => $data['billing']['fullName'],
                            'email'             => $data['billing']['email'],
                            'phone'             => $data['billing']['phone'],
                            'city'              => $data['billing']['city'],
                            'address'           => $data['billing']['address'],
                            'postal_code'       => $data['billing']['postalCode'],
                            'country'           => $data['billing']['country'],
                            'payment_type'      => $data['paymentMethod'],
                            'course_unique_id'  => $item['unique_id'],
                            'whom'              => $data['billing']['whom'],
                            'quantity'          => (int) $item['quantity'],
                            'course_title'      => $item['name'],
                            'price'             => (float) $item['price'],
                            'media'             => Session::get('ref_source', 'Unknown'),
                            'message'           => $data['billing']['message'] ?? null,
                            'status'            => 'completed',
                            'transaction_id'    => $txnId,
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
                                e($data['billing']['fullName']),
                                e($data['billing']['email']),
                                e($item['name']),
                                (int)$item['quantity'],
                                e($txnId),
                                e(number_format($lineTotal, 2))
                            ),
                        ]);
                    }

                    // Email confirmation
                    try {
                        Mail::to($data['billing']['email'])
                            ->send(new PaymentConfirmationMail($data['billing'], $data['cartItems'], $plainPassword));
                    } catch (\Throwable $mailEx) {
                        Log::error('Payment email failed: '.$mailEx->getMessage());
                    }

                    DB::commit();

                    // Return the success view
                    return view('emails.successPayment', [
                        'gtmPurchase' => [
                            'transaction_id' => $paymentUniqueId,
                            'value'         => (float) $data['total'],
                            'currency'      => 'GBP',
                            'affiliation'   => 'Seru Training Course',
                            'coupon'        => $data['couponCode'] ?? null,
                            'items' => collect($data['cartItems'])->map(fn($i) => [
                                'item_id'      => (string)($i['unique_id'] ?? $i['id']),
                                'item_name'    => $i['name'] ?? $i['title'] ?? 'Course',
                                'price'        => (float)$i['price'],
                                'quantity'     => (int)$i['quantity'],
                                'item_brand'   => 'serutrainingcourse',
                                'item_category'=> $i['category'] ?? 'Courses',
                                'item_variant' => $i['level'] ?? 'Default',
                            ])->values(),
                            'customer' => [
                                'full_name'  => $data['billing']['fullName'] ?? null,
                                'email'      => $data['billing']['email'] ?? null,
                                'phone'      => $data['billing']['phone'] ?? null,
                                'country'    => $data['billing']['country'] ?? null,
                                'city'       => $data['billing']['city'] ?? null,
                                'postalCode' => $data['billing']['postalCode'] ?? null,
                                'address'    => $data['billing']['address'] ?? null,
                                'whom'       => $data['billing']['whom'] ?? null,
                            ]
                        ]
                    ]);

                } catch (\Throwable $tx) {
                    DB::rollBack();
                    Log::error('Payment tx error: '.$tx->getMessage());
                    return back()->withErrors(['Could not complete purchase.']);
                }
            }

            return back()->with('message', 'Payment processed successfully');

        } catch (ValidationException $e) {
            Log::error('Validation failed:', ['errors' => $e->errors(), 'input' => $request->all()]);
            return back()->withErrors(['Validation failed.']);
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return back()->withErrors(['An error occurred: ' . $e->getMessage()]);
        }
    }

    private function generateUniquePaymentId()
    {
        do {
            $id = mt_rand(10000000, 99999999);
        } while (Payment::where('payment_unique_id', $id)->exists());
        return $id;
    }
}
