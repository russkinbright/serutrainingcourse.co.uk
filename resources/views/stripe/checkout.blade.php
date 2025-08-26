<!DOCTYPE html>
<html>
<head>
    <title>Stripe Checkout</title>
</head>
<body>

@if (session('error'))
    <p style="color:red;">{{ session('error') }}</p>
@endif

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<h2>Total: ${{ number_format($total, 2) }}</h2>

<form action="{{ route('stripe.payment') }}" method="POST">
    @csrf
    <button type="submit">Pay Now</button>
</form>

</body>
</html>
