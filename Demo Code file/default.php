<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seru Training Course</title>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font-awesome --}}
    <script src="https://kit.fontawesome.com/69ba9af9da.js" crossorigin="anonymous"></script>
    <!-- Aipine.js Animation -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script>
       <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
       <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
       <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
       <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
       <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
       <script src="//unpkg.com/alpinejs" defer></script>  --}}


    <!-- Bootstrap CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

    <!-- Tailwind CSS -->
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> --}}

    <!-- lottie -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

    <!-- PDF.js v2.16.105 only -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>


    {{-- Line Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Add Quill.js -->
    {{-- <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
       <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
       <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> --}}



    <!-- Charts links -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <style>
        [x-cloak] {
            display: none !important;
        }
  

        #messageContainer {
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
        }

        #message {
            animation: fadeInOut 2.5s ease-in-out;
            /* Animation for fade in and out */
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        .move {
            transform: translateX(140%);
        }
    </style>
</head>

<body id="top">

    @yield('content')



</body>