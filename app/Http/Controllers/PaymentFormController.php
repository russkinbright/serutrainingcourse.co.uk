<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

        if ($data['paymentMethod'] === 'card') {
            // Save data in session to use in StripeController
            Session::put('stripe_cart', $data['cartItems']);
            Session::put('stripe_billing', $data['billing']);
            Session::put('stripe_total', $data['total']);

            return response()->json([
                'success' => true,
                'redirect_url' => route('stripe.payment')
            ]);
        }


        if ($data['paymentMethod'] === 'paypal') {
            Session::put('paypal_cart', $data['cartItems']);
            Session::put('paypal_billing', $data['billing']);
            Session::put('paypal_total', $data['total']);

            return response()->json([
                'success' => true,
                'redirect_url' => route('paypal.create', ['amount' => $data['total']])
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Payment processed successfully']);

    } catch (ValidationException $e) {
        // Log details of the failed validation
        Log::error('Validation failed:', [
            'errors' => $e->errors(),
            'input' => $request->all()
        ]);

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

}