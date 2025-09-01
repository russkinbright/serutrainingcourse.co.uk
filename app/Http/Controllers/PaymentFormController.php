<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException; // ✅ import this

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
                // Was numeric; allow string or int IDs
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
                'total' => 'required|numeric|min:0'
            ]);

            // === Stripe path (unchanged) ===
            if ($data['paymentMethod'] === 'card') {
                Session::put('stripe_cart', $data['cartItems']);
                Session::put('stripe_billing', $data['billing']);
                Session::put('stripe_total', $data['total']);

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('stripe.payment')
                ]);
            }

            // === PayPal path (unchanged) ===
            // if ($data['paymentMethod'] === 'paypal') {
            //     Session::put('paypal_cart', $data['cartItems']);
            //     Session::put('paypal_billing', $data['billing']);
            //     Session::put('paypal_total', $data['total']);

            //     return response()->json([
            //         'success' => true,
            //         'redirect_url' => route('paypal.create', ['amount' => $data['total']])
            //     ]);
            // }

            // === OTHER payment: auto-complete locally ===
            if ($data['paymentMethod'] === 'paypal') {
                // 1) Validate against DB & recompute total
                $calcTotal = 0;
                foreach ($data['cartItems'] as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Course not found: '.$item['name']
                        ], 422);
                    }
                    if ((float)$course->price !== (float)$item['price']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Course price mismatch for '.$item['name']
                        ], 422);
                    }
                    $calcTotal += ((float)$item['price']) * (int)$item['quantity'];
                }
                if (abs($calcTotal - (float)$data['total']) > 0.01) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Total amount mismatch.'
                    ], 422);
                }

                DB::beginTransaction();
                try {
                    // 2) Find or create learner
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
                            'payment_type' => 'other',
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

                    // 3) Create payments + course progress + activity
                    $paymentUniqueId = $this->generateUniquePaymentId();
                    $txnId = 'OTHER-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6));

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
                            'payment_type'      => 'other',
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

                        // expiry from course duration like "8 weeks"
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

                    // 4) Email confirmation (best-effort)
                    try {
                        Mail::to($data['billing']['email'])
                            ->send(new PaymentConfirmationMail($data['billing'], $data['cartItems'], $plainPassword));
                    } catch (\Throwable $mailEx) {
                        Log::error('Other payment email failed: '.$mailEx->getMessage());
                    }

                    DB::commit();

                    // frontend will show “Purchase successfully completed!” and clear cart
                 return response()->json([
                    'success' => true,
                    'html' => view('emails.successPayment', [
                        'gtmPurchase' => [
                            'transaction_id' => $paymentUniqueId, // your order id (stable, not PI id)
                            'value'         => (float) $total,    // final paid £
                            'currency'      => 'GBP',
                            'affiliation'   => 'Seru Training Course',
                            'coupon'        => $couponCode ?? null,
                            'items' => collect($cartItems)->map(fn($i) => [
                                'item_id'      => (string)($i['unique_id'] ?? $i['id']),
                                'item_name'    => $i['name'] ?? $i['title'] ?? 'Course',
                                'price'        => (float)$i['price'],
                                'quantity'     => (int)$i['quantity'],
                                'item_brand'   => 'serutrainingcourse',
                                'item_category'=> $i['category'] ?? 'Courses',
                                'item_variant' => $i['level'] ?? 'Default',
                            ])->values(),
                            'customer' => [
                                'full_name'  => $billing['fullName'] ?? null,
                                'email'      => $billing['email'] ?? null,
                                'phone'      => $billing['phone'] ?? null,
                                'country'    => $billing['country'] ?? null,
                                'city'       => $billing['city'] ?? null,
                                'postalCode' => $billing['postalCode'] ?? null,
                                'address'    => $billing['address'] ?? null,
                                'whom'       => $billing['whom'] ?? null,
                            ]
                        ]
                    ])->render()
                ]);

                } catch (\Throwable $tx) {
                    DB::rollBack();
                    Log::error('Other payment tx error: '.$tx->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not complete purchase.'
                    ], 500);
                }
            }

            // Fallback
            return response()->json(['success' => true, 'message' => 'Payment processed successfully']);

        } catch (ValidationException $e) { // ✅ this now resolves
            Log::error('Validation failed:', ['errors' => $e->errors(), 'input' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
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
