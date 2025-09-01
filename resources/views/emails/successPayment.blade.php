@extends('home.default')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.8/lottie.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        .svg-icon { transition: transform 0.3s ease; }
        .svg-icon:hover { transform: scale(1.2); }
        .confetti { pointer-events: none; }
        /* Optimize animations for mobile */
        @media (max-width: 640px) {
            .animate__animated { animation-duration: 0.6s !important; }
            .confetti { animation-duration: 2s !important; }
        }
    </style>

<body class="bg-gradient-to-b from-gray-50 to-blue-100 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 relative overflow-hidden">

    <!-- Confetti Container -->
    <div id="confetti-container" class="absolute inset-0 pointer-events-none z-0"></div>

    <!-- Lottie Animation Container -->
    <div id="lottie-greeting" class="absolute top-0 left-0 w-full h-full opacity-20 pointer-events-none z-0 hidden sm:block"></div>

    <div class="relative z-10 bg-white w-full max-w-md sm:max-w-lg lg:max-w-xl rounded-3xl shadow-2xl overflow-hidden text-center transform transition-all duration-500 hover:shadow-3xl">
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-900 to-blue-700 text-white px-4 sm:px-6 py-6 sm:py-8 lg:py-10">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold animate-pulse">Payment Successful!</h1>
        </div>

        <!-- Body -->
        <div class="px-4 sm:px-6 py-6 sm:py-8 lg:py-10">
            <div class="w-20 sm:w-24 h-20 sm:h-24 mx-auto bg-green-600 rounded-full flex items-center justify-center mb-4 sm:mb-6 animate-bounce">
                <svg class="w-10 sm:w-12 h-10 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-green-700 text-xl sm:text-2xl font-semibold mb-3 sm:mb-4 animate__animated animate__fadeInUp">Welcome to Seru Training Course!</h2>
            <p class="text-gray-700 text-sm sm:text-base lg:text-lg mb-4 sm:mb-6 animate__animated animate__fadeInUp animate__delay-1s">
                Your training journey begins now. We've sent all the details to your email inbox.
            </p>

            <!-- Course Details -->
            <div class="bg-gray-50 rounded-xl p-4 sm:p-6 text-left text-sm sm:text-base lg:text-lg mb-4 sm:mb-6 animate__animated animate__fadeInUp animate__delay-2s">
                <h3 class="text-gray-900 font-semibold mb-3 sm:mb-4 flex items-center">
                    <svg class="w-5 sm:w-6 h-5 sm:h-6 mr-2 text-blue-600 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    What's Next?
                </h3>
                <ul class="space-y-3 sm:space-y-4">
                    <li class="flex items-center group">
                        <svg class="w-5 sm:w-6 h-5 sm:h-6 mr-2 sm:mr-3 text-blue-500 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Check your mailbox for payment confirmation
                    </li>
                    <li class="flex items-center group">
                        <svg class="w-5 sm:w-6 h-5 sm:h-6 mr-2 sm:mr-3 text-blue-500 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Check Your Login Details
                    </li>
                    <li class="flex items-center group">
                        <svg class="w-5 sm:w-6 h-5 sm:h-6 mr-2 sm:mr-3 text-blue-500 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Login to our portal and start training
                    </li>
                </ul>
            </div>

            <p class="text-gray-700 text-sm sm:text-base lg:text-lg mb-4 sm:mb-6 animate__animated animate__fadeInUp animate__delay-3s">
                Our team will contact you shortly with additional preparation materials.
            </p>

            <a href="/" class="inline-block bg-blue-700 text-white px-6 sm:px-8 py-2 sm:py-3 rounded-full font-semibold hover:bg-blue-800 transition transform hover:scale-105 text-sm sm:text-base">
                Return to Homepage
            </a>
        </div>
    </div>

    <!-- Animation Scripts -->
    <script>

         // Clear localStorage cart
        localStorage.removeItem('cartItems');
        localStorage.removeItem('cartDiscount');

        // Optional: Clear sessionStorage if used
        sessionStorage.clear();

        // Enhanced Confetti Animation
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            const shapes = ['square', 'circle', 'triangle'];
            const colors = ['#ff4d4f', '#40c4ff', '#52c41a', '#faad14', '#722ed1'];
            const count = window.innerWidth < 640 ? 50 : 100; // Reduce confetti on mobile

            for (let i = 0; i < count; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti', 'absolute');
                const shape = shapes[Math.floor(Math.random() * shapes.length)];
                confetti.style.width = shape === 'triangle' ? '10px' : '8px';
                confetti.style.height = shape === 'triangle' ? '8px' : '8px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-10px';
                confetti.style.opacity = Math.random() * 0.5 + 0.5;
                if (shape === 'circle') confetti.style.borderRadius = '50%';
                if (shape === 'triangle') {
                    confetti.style.clipPath = 'polygon(50% 0%, 0% 100%, 100% 100%)';
                }
                confetti.style.animation = `fall ${Math.random() * 3 + 1.5}s ease-in-out forwards`;
                container.appendChild(confetti);

                setTimeout(() => confetti.remove(), 4000);
            }
        }

        // Lottie Greeting Animation (disabled on mobile for performance)
        if (window.innerWidth >= 640) {
            const lottieContainer = document.getElementById('lottie-greeting');
            lottie.loadAnimation({
                container: lottieContainer,
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: 'https://assets.lottiefiles.com/packages/lf20_j3k7t3.json' // Celebration animation
            });
        }

        // GSAP Animations
        gsap.from('.animate__animated', {
            opacity: 0,
            y: 30,
            stagger: 0.2,
            duration: window.innerWidth < 640 ? 0.6 : 1,
            ease: 'power3.out'
        });

        // Trigger Confetti
        window.onload = () => {
            createConfetti();
            setInterval(createConfetti, 2000);
        };

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth < 640) {
                document.getElementById('lottie-greeting').style.display = 'none';
            } else {
                document.getElementById('lottie-greeting').style.display = 'block';
            }
        });
    </script>
</body>
</html>