<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Learner;
use App\Models\Activity;
use App\Models\CourseProgress;
use App\Mail\PaymentConfirmationMail;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    public function payment(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // ---------- Phase B: returned from Stripe with session_id ----------
            if ($request->has('session_id')) {
                $sessionId = $request->query('session_id');

                // 1) Get data you saved before redirect
                $cartItems = Session::get('stripe_cart', []);
                $billing   = Session::get('stripe_billing', []);
                $total     = (float) Session::get('stripe_total', 0);

                if (empty($cartItems) || empty($billing) || $total <= 0) {
                    return redirect()->route('checkout.cancel')->with('error', 'Session data missing.');
                }

                // 2) Retrieve Checkout Session & verify paid
                $session = \Stripe\Checkout\Session::retrieve([
                    'id' => $sessionId,
                    'expand' => ['payment_intent', 'customer', 'customer_details'],
                ]);

                if (!$session || $session->mode !== 'payment') {
                    return redirect()->route('checkout.cancel')->with('error', 'Invalid session.');
                }

                $paid = $session->payment_status === 'paid';
                $paymentIntentId = is_string($session->payment_intent) ? $session->payment_intent : ($session->payment_intent->id ?? null);
                if (!$paid && !is_string($session->payment_intent) && ($session->payment_intent->status ?? null) === 'succeeded') {
                    $paid = true;
                }
                if (!$paid) {
                    return redirect()->route('checkout.cancel')->with('error', 'Payment not completed.');
                }

                // 3) Recompute total & validate courses
                $calcTotal = 0;
                foreach ($cartItems as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) {
                        Log::error('Course not found for unique_id: '.$item['unique_id']);
                        continue;
                    }
                    if ((float)$course->price !== (float)$item['price']) {
                        return redirect()->route('checkout.cancel')->with('error', 'Course price mismatch for '.$item['name']);
                    }
                    $calcTotal += ((float)$item['price']) * (int)$item['quantity'];
                }
                if (abs($calcTotal - $total) > 0.01) {
                    return redirect()->route('checkout.cancel')->with('error', 'Total amount mismatch.');
                }

                // Idempotency: skip if already processed
                if ($paymentIntentId && Payment::where('transaction_id', $paymentIntentId)->exists()) {
                    return view('emails.successPayment');
                }

                $paymentUniqueId = $this->generateUniquePaymentId();
                $payerEmail = $session->customer_details->email ?? $session->customer_email ?? ($billing['email'] ?? null);

                // 4) Create/fetch Learner (same as PayPal)
                $existingLearner = Learner::where('email', $billing['email'])->first();
                $plainPassword = null;

                if ($existingLearner) {
                    $learnerSecretId = $existingLearner->secret_id;
                    $plainPassword   = 'Use your existing password to log in.';
                } else {
                    do {
                        $generatedSecretId = mt_rand(1000000, 9999999);
                    } while (Learner::where('secret_id', $generatedSecretId)->exists());

                    $plainPassword = Str::random(8);
                    $hashedPassword = Hash::make($plainPassword);

                    $newLearner = Learner::create([
                        'secret_id'    => $generatedSecretId,
                        'name'         => $billing['fullName'] ?? 'Learner',
                        'email'        => $billing['email'],
                        'payment_type' => 'stripe',
                        'whom'         => $billing['whom'] ?? null,
                        'phone'        => $billing['phone'] ?? null,
                        'message'      => $billing['message'] ?? null,
                        'password'     => $hashedPassword,
                        'country'      => $billing['country'] ?? null,
                        'city'         => $billing['city'] ?? null,
                        'address'      => $billing['address'] ?? null,
                        'postal_code'  => $billing['postalCode'] ?? null,
                    ]);
                    $learnerSecretId = $newLearner->secret_id;
                }

                // 5) Payments + CourseProgress
                foreach ($cartItems as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) {
                        Log::error('Course not found for unique_id: '.$item['unique_id']);
                        continue;
                    }

                    Payment::create([
                        'payment_unique_id' => $paymentUniqueId,
                        'learner_secret_id' => $learnerSecretId,
                        'name'              => $billing['fullName'] ?? 'Learner',
                        'email'             => $billing['email'] ?? $payerEmail,
                        'phone'             => $billing['phone'] ?? null,
                        'city'              => $billing['city'] ?? null,
                        'address'           => $billing['address'] ?? null,
                        'postal_code'       => $billing['postalCode'] ?? null,
                        'country'           => $billing['country'] ?? null,
                        'payment_type'      => 'stripe',
                        'course_unique_id'  => $item['unique_id'],
                        'whom'              => $billing['whom'] ?? null,
                        'quantity'          => (int) $item['quantity'],
                        'course_title'      => $item['name'],
                        'price'             => (float) $item['price'],
                        'media'             => Session::get('ref_source', 'Unknown'),
                        'message'           => $billing['message'] ?? null,
                        'status'            => 'completed',
                        'transaction_id'    => $paymentIntentId,
                        'account_id'        => null,
                        'stripe_email'      => $session->customer_details->email ?? $session->customer_email ?? ($billing['email'] ?? null),

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
                                e($paymentIntentId),
                                e(number_format($lineTotal, 2))
                            ),
                        ]);
                }

                // 6) Email
                try {
                    Mail::to($billing['email'] ?? $payerEmail)
                        ->send(new PaymentConfirmationMail($billing, $cartItems, $plainPassword));
                } catch (\Throwable $e) {
                    Log::error('Stripe email sending failed: '.$e->getMessage());
                }

              

                // 7) Cleanup
                Session::forget(['stripe_cart','stripe_billing','stripe_total','ref_source']);
                echo '<script>localStorage.removeItem("cartItems"); localStorage.removeItem("cartDiscount");</script>';

                try {
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    Artisan::call('cache:clear');
                    Artisan::call('clear-compiled');
                    Artisan::call('config:cache');
                } catch (\Throwable $t) {}

                return view('emails.successPayment');
            }

            // ---------- Phase A: first visit → create Checkout Session & redirect ----------
            $cartItems = Session::get('stripe_cart', []);
            $billing   = Session::get('stripe_billing', []);
            $total     = (float) Session::get('stripe_total', 0);

            if (empty($cartItems) || empty($billing) || $total <= 0) {
                throw new \Exception('Missing cart, billing info, or invalid amount.');
            }

            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'gbp',
                        'product_data' => ['name' => 'Course Payment'],
                        'unit_amount'  => (int) round($total * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'          => 'payment',
                'success_url'   => route('stripe.payment') . '?session_id={CHECKOUT_SESSION_ID}', // <— same URL
                'cancel_url'    => route('checkout.cancel'),
                'metadata'      => ['email' => $billing['email'] ?? ''],
                'customer_email'=> $billing['email'] ?? null,
            ]);

            return redirect($checkoutSession->url);

        } catch (\Throwable $e) {
            Log::error('Stripe unified payment() error: '.$e->getMessage());
            return back()->with('error', 'Payment failed: '.$e->getMessage());
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
