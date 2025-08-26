@extends('home.default')

@section('content')
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-red-600">Payment Failed</h1>
            <p class="mt-4 text-gray-600">There was a problem processing your PayPal payment.</p>
            <a href="{{ route('learner.payment') }}" class="mt-6 inline-block bg-red-600 text-white py-2 px-4 rounded">Try Again</a>
        </div>
    </div>
@endsection
