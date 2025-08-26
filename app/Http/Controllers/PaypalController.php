<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Support\Facades\Artisan;
use App\Models\Learner;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class PaypalController extends Controller
{
    public function createTransaction(Request $request)
    {
        $amount = $request->query('amount') ?? 10.00;

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));

        $token = $provider->getAccessToken();

        if (!is_array($token)) {
            Log::error('PayPal token response was not an array', ['token' => $token]);
            return redirect()->route('paypal.error')->with('error', 'PayPal authentication failed.');
        }

        $provider->setAccessToken($token);

        $cartItems = Session::get('paypal_cart', []);
        $billing = Session::get('paypal_billing', []);
        $items = [];

        if (empty($cartItems)) {
            Log::error('No cart items found in session');
            return redirect()->route('paypal.error')->with('error', 'Cart is empty.');
        }

        $paymentUniqueId = $this->generateUniquePaymentId();
        Session::put('paypal_payment_unique_id', $paymentUniqueId);

        foreach ($cartItems as $item) {
            $course = Course::where('unique_id', $item['unique_id'])->first();

            if (!$course) {
                Log::error('Invalid course unique_id: ' . $item['unique_id']);
                return redirect()->route('paypal.error')->with('error', 'Invalid course selected.');
            }

            if ($course->price != $item['price']) {
                Log::error('Course price mismatch for ' . $item['name']);
                return redirect()->route('paypal.error')->with('error', 'Course price mismatch.');
            }

            $items[] = [
                'name' => $item['name'],
                'description' => 'Course ID: ' . $item['unique_id'] . ' | Quantity: ' . $item['quantity'],
                'quantity' => (int) $item['quantity'],
                'unit_amount' => [
                    'currency_code' => 'GBP',
                    'value' => number_format((float) $item['price'], 2, '.', ''),
                ],
                'sku' => $paymentUniqueId,
            ];
        }

        $totalAmount = array_reduce($items, function ($sum, $item) {
            return $sum + ($item['unit_amount']['value'] * $item['quantity']);
        }, 0);

        if (abs($totalAmount - $amount) > 0.01) {
            Log::error('Total amount mismatch', ['calculated' => $totalAmount, 'provided' => $amount]);
            return redirect()->route('paypal.error')->with('error', 'Total amount mismatch.');
        }

        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'application_context' => [
                'return_url' => route('paypal.success'),
                'cancel_url' => route('paypal.cancel'),
            ],
            'purchase_units' => [[
                'description' => 'Course Purchase from Seru Training',
                'amount' => [
                    'currency_code' => 'GBP',
                    'value' => number_format($totalAmount, 2, '.', ''),
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => 'GBP',
                            'value' => number_format($totalAmount, 2, '.', '')
                        ]
                    ]
                ],
                'items' => $items
            ]]
        ]);

        if (isset($response['id']) && $response['status'] === 'CREATED') {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        Log::error('PayPal order creation failed', ['response' => $response]);
        return redirect()->route('paypal.error')->with('error', 'Something went wrong with PayPal.');
    }

   public function successTransaction(Request $request)
        {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));

            $token = $provider->getAccessToken();
            $provider->setAccessToken($token);

            $response = $provider->capturePaymentOrder($request->query('token'));

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $capture = $response['purchase_units'][0]['payments']['captures'][0];
                $transactionId = $capture['id'];
                $payerId = $response['payer']['payer_id'] ?? null;
                $payerEmail = $response['payer']['email_address'] ?? null;
                $amount = $capture['amount']['value'] ?? 0;

                if (Payment::where('transaction_id', $transactionId)->exists()) {
                    return redirect()->route('paypal.error')->with('error', 'This transaction has already been processed.');
                }

                $cartItems = Session::get('paypal_cart', []);
                $billing = Session::get('paypal_billing', []);
                $paymentUniqueId = Session::get('paypal_payment_unique_id');

                if (empty($cartItems) || empty($billing) || !$paymentUniqueId) {
                    Log::error('Session data missing', [
                        'cartItems' => $cartItems,
                        'billing' => $billing,
                        'paymentUniqueId' => $paymentUniqueId
                    ]);
                    return redirect()->route('paypal.error')->with('error', 'Session data missing.');
                }

                // Check if learner exists
                $existingLearner = Learner::where('email', $billing['email'])->first();
                $plainPassword = null;

                if ($existingLearner) {
                    $learnerSecretId = $existingLearner->secret_id;
                    $plainPassword = 'Use your existing password to log in.';
                } else {
                    // Generate a unique 7-digit secret ID
                    do {
                        $generatedSecretId = mt_rand(1000000, 9999999);
                    } while (Learner::where('secret_id', $generatedSecretId)->exists());

                    $plainPassword = Str::random(8);
                    $hashedPassword = Hash::make($plainPassword);

                    $newLearner = Learner::create([
                        'secret_id' => $generatedSecretId,
                        'name' => $billing['fullName'],
                        'email' => $billing['email'],
                        'payment_type' => 'paypal',
                        'whom' => $billing['whom'],
                        'card' => null,
                        'card_expiry' => null,
                        'card_code' => null,
                        'phone' => $billing['phone'],
                        'message' => $billing['message'] ?? null,
                        'password' => $hashedPassword,
                        'country' => $billing['country'],
                        'city' => $billing['city'],
                        'address' => $billing['address'],
                        'postal_code' => $billing['postalCode'],
                        'question' => null,
                        'answer' => null,
                    ]);

                    $learnerSecretId = $newLearner->secret_id;
                }

                foreach ($cartItems as $item) {
                    $course = Course::where('unique_id', $item['unique_id'])->first();
                    if (!$course) {
                        Log::error('Course not found for unique_id: ' . $item['unique_id']);
                        continue;
                    }

                    Payment::create([
                        'payment_unique_id' => $paymentUniqueId,
                        'learner_secret_id' => $learnerSecretId,
                        'name' => $billing['fullName'],
                        'email' => $billing['email'],
                        'phone' => $billing['phone'],
                        'city' => $billing['city'],
                        'address' => $billing['address'],
                        'postal_code' => $billing['postalCode'],
                        'country' => $billing['country'],
                        'payment_type' => 'paypal',
                        'course_unique_id' => $item['unique_id'],
                        'whom' => $billing['whom'],
                        'quantity' => $item['quantity'],
                        'course_title' => $item['name'],
                        'price' => $item['price'],
                        'media' => Session::get('ref_source', 'Unknown'),
                        'message' => $billing['message'] ?? null,
                        'status' => 'completed',
                        'transaction_id' => $transactionId,
                        'account_id' => $payerId,
                        'paypal_email' => $payerEmail,
                    ]);

                   

                    // Get the course record
                    $course = Course::where('unique_id', $item['unique_id'])->first();

                    // Default expiration: today (no access)
                    $expireAt = now();

                    // Parse duration (e.g., "2 Weeks", "4 Weeks")
                    if ($course && preg_match('/(\d+)\s*week/i', $course->duration, $matches)) {
                        $expireAt = now()->addWeeks((int) $matches[1]);
                       }


                    CourseProgress::create([
                        'learner_secret_id' => $learnerSecretId,
                        'course_unique_id' => $item['unique_id'],
                        'permodule' => 0,
                        'perquestion' => 0,
                        'is_completed' => false,
                        'progress' => 0,
                        'mark' => 0,
                        'total_mark' => 0,
                        'expire_at' => $expireAt,
                    ]);

                        $lineTotal = (float)$item['price'] * (int)$item['quantity'];

                        Activity::create([
                            'message' => sprintf(
                                '<strong>%s</strong> (%s) enrolled in <strong>%s</strong> x %d. ' .
                                'Transaction ID: <code>%s</code>, Amount: <strong>£%s</strong>.',
                                e($billing['fullName']),
                                e($billing['email']),
                                e($item['name']),
                                (int)$item['quantity'],
                                e($transactionId),
                                e(number_format($lineTotal, 2))
                            ),
                        ]);


                }

                // Send email
                try {
                    Mail::to($billing['email'])->send(new PaymentConfirmationMail($billing, $cartItems, $plainPassword));
                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage());
                }


                // Clear session and cache
                Session::forget([
                    'paypal_cart',
                    'paypal_billing',
                    'paypal_total',
                    'ref_source',
                    'paypal_payment_unique_id'
                ]);

                echo '<script>localStorage.removeItem("cartItems"); localStorage.removeItem("cartDiscount");</script>';

                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('cache:clear');
                Artisan::call('clear-compiled');
                Artisan::call('config:cache');

                return view('emails.successPayment');
            }

            Log::error('PayPal payment capture failed', ['response' => $response]);
            return redirect()->route('paypal.error')->with('error', 'Payment failed.');
        }



    public function cancelTransaction()
    {
        Session::forget([
            'paypal_cart',
            'paypal_billing',
            'paypal_total',
            'ref_source',
            'paypal_payment_unique_id'
        ]);

        return redirect()->route('learner.payment')->with('error', 'Payment canceled.');
    }

    public function errorTransaction()
    {
        return view('course.paypalError');
    }

    private function generateUniquePaymentId()
    {
        do {
            $id = mt_rand(10000000, 99999999);
        } while (Payment::where('payment_unique_id', $id)->exists());

        return $id;
    }
}
