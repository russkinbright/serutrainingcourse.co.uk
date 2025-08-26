<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            margin: 0;
        }
        .certificate-container {
            aspect-ratio: 794 / 1122;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-100 via-white to-purple-200 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-white rounded-xl shadow-2xl overflow-hidden border border-purple-300">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-700 to-purple-800 text-white text-center py-6 px-4">
            <h1 class="text-3xl font-extrabold tracking-wide">🎓 Verified Certificate</h1>
            <p class="mt-1 text-sm text-purple-100">This document confirms a successful course completion.</p>
        </div>
        <!-- Certificate Content -->
        <div class="relative w-full certificate-container">
            <img src="{{ asset('certificates/y.png') }}" alt="Certificate Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 sm:px-12 text-gray-800 ">
                <div class="text-xl font-semibold mb-2 uppercase tracking-wide text-purple-700">
                    THIS IS TO CERTIFY THAT
                </div>
                <div class="text-4xl sm:text-5xl font-bold font-serif text-purple-900 mt-2">
                    {{ $learner }}
                </div>
                <div class="text-lg sm:text-xl mt-6 font-medium text-gray-700">
                    has successfully completed the course of
                </div>
                <div class="text-2xl sm:text-3xl font-bold mt-3 text-purple-800 max-w-xl">
                    {{ $courseTitle }}
                </div>
                <div class="text-lg sm:text-xl mt-4 font-medium text-gray-700">
                    and has been awarded with this certificate on
                </div>
                <div class="text-lg mt-2 text-gray-600">
                    {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                </div>
                <div class="mt-4 text-sm text-gray-500 italic">
                    Certificate Code: <span class="font-semibold text-purple-600">{{ $code }}</span>
                </div>
            </div>
        </div>
        <!-- Footer Actions -->
        <div class="bg-purple-50 px-6 py-4 flex justify-center sm:justify-between flex-wrap gap-4 border-t border-purple-200">
            <p class="text-sm text-red-500 text-center sm:text-left ">
                If this certificate looks valid, you may download or verify it.
            </p>
        </div>
    </div>


</body>
</html>