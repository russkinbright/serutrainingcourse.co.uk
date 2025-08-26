@include('home.default')
<div class="bg-gradient-to-b from-purple-50 to-white text-gray-900 font-inter overflow-x-hidden">
    <header
        class="sticky top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-lg z-20 transition-all duration-500 ease-in-out"
        data-header>
        @include('main.navbar')
    </header>

    <!-- Hero Section -->
    <section class="py-16 sm:py-20 md:py-24" id="main-content">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-center gap-10">
            <div class="lg:w-1/2 text-center lg:text-left animate-slide-in-left">
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-10 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600 leading-tight">
                    Pass Your SERU Test First Time</h1>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 mb-8 max-w-lg mx-auto lg:mx-0">Master the SERU
                    test with bite-sized lessons, practice questions, and realistic mock exams.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="/learner/login"
                        class="relative bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 text-lg font-semibold overflow-hidden group">
                        <span class="relative z-10">Start Learning</span>
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-purple-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                    </a>
                    <a href="/course"
                        class="relative bg-white border-2 border-purple-300 hover:bg-purple-50 text-purple-600 px-8 py-4 rounded-full shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-lg font-semibold">View
                        Courses</a>
                </div>
            </div>
            <div class="lg:w-1/2 mt-10 lg:mt-0 relative">
                <img src="/image/Seru-hero.png" alt="SERU training illustration"
                    class="w-full max-w-md md:max-w-md lg:max-w-lg mx-auto animate-float rounded-xl relative z-10">
                <div class="absolute inset-0 bg-purple-200/30 rounded-full blur-3xl transform scale-125 -z-10"></div>
            </div>
        </div>
        <!-- Trust Row -->
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            <div class="flex items-center justify-center gap-3 text-gray-700 animate-slide-up" data-delay="0.1">
                <i
                    class="fa-solid fa-check-double text-purple-600 text-2xl transform group-hover:scale-110 transition-transform"></i>
                <span class="text-base sm:text-lg font-medium">Updated for SERU Standards</span>
            </div>
            <div class="flex items-center justify-center gap-3 text-gray-700 animate-slide-up" data-delay="0.2">
                <i
                    class="fa-solid fa-mobile-screen text-purple-600 text-2xl transform group-hover:scale-110 transition-transform"></i>
                <span class="text-base sm:text-lg font-medium">Mobile Friendly</span>
            </div>
            <div class="flex items-center justify-center gap-3 text-gray-700 animate-slide-up" data-delay="0.3">
                <i
                    class="fa-solid fa-clock text-purple-600 text-2xl transform group-hover:scale-110 transition-transform"></i>
                <span class="text-base sm:text-lg font-medium">High Pass Rates</span>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 sm:py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-center mb-12 
             bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">
                Why Choose SERU Training?
            </h2>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Point 1 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-graduation-cap text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Mapped to TfL SERU Syllabus</h3>
                        <p class="text-gray-600">Our courses align with the latest TfL requirements.</p>
                    </div>
                </div>

                <!-- Point 2 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-clipboard-check text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Realistic Mock Exams</h3>
                        <p class="text-gray-600">Practice with timed exams that mirror the real test.</p>
                    </div>
                </div>

                <!-- Point 3 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-user-tie text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Expert Support</h3>
                        <p class="text-gray-600">Get help from UK-based instructors.</p>
                    </div>
                </div>

                <!-- Point 4 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-book-open text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Practice Questions Library</h3>
                        <p class="text-gray-600">Access hundreds of practice questions covering all SERU topics.</p>
                    </div>
                </div>

                <!-- Point 5 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-clipboard-list text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Section-wise Mock Tests</h3>
                        <p class="text-gray-600">Focus on specific areas like Equality, Safety, or Regulations with
                            section-based practice.</p>
                    </div>
                </div>

                <!-- Point 6 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-pen-to-square text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Final Mock Test (37 Questions)</h3>
                        <p class="text-gray-600">Experience the full SERU exam simulation with the same number of
                            questions and timing as the real test.</p>
                    </div>
                </div>

                <!-- Point 7 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-stopwatch text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Timed Assessments</h3>
                        <p class="text-gray-600">Build confidence under real exam conditions with strict timers.</p>
                    </div>
                </div>

                <!-- Point 8 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-chart-line text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Progress Tracking</h3>
                        <p class="text-gray-600">Review your scores and track improvement with each practice attempt.
                        </p>
                    </div>
                </div>

                <!-- Point 9 -->
                <div
                    class="flex items-start gap-5 group bg-purple-50 hover:bg-purple-100 p-6 rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i
                        class="fa-solid fa-headset text-3xl text-purple-600 transform group-hover:scale-110 transition-transform"></i>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Learner Support</h3>
                        <p class="text-gray-600">Guidance and help for any questions during your SERU preparation.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <section class="py-16 sm:py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-center mb-12 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">
                Our Results</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                <div class="p-6 bg-purple-50 rounded-2xl shadow-sm hover:shadow-lg transition">
                    <i class="fa-solid fa-users text-4xl text-purple-600 mb-3"></i>
                    <h3 class="text-3xl font-bold text-gray-900">10k+</h3>
                    <p class="text-gray-600">Learners Trained</p>
                </div>
                <div class="p-6 bg-purple-50 rounded-2xl shadow-sm hover:shadow-lg transition">
                    <i class="fa-solid fa-star text-4xl text-purple-600 mb-3"></i>
                    <h3 class="text-3xl font-bold text-gray-900">92%</h3>
                    <p class="text-gray-600">First-Time Pass Rate</p>
                </div>
                <div class="p-6 bg-purple-50 rounded-2xl shadow-sm hover:shadow-lg transition">
                    <i class="fa-solid fa-clipboard-list text-4xl text-purple-600 mb-3"></i>
                    <h3 class="text-3xl font-bold text-gray-900">500+</h3>
                    <p class="text-gray-600">Practice Questions</p>
                </div>
                <div class="p-6 bg-purple-50 rounded-2xl shadow-sm hover:shadow-lg transition">
                    <i class="fa-solid fa-clock text-4xl text-purple-600 mb-3"></i>
                    <h3 class="text-3xl font-bold text-gray-900">24/7</h3>
                    <p class="text-gray-600">Access Anytime</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Testimonials -->
    <section class="py-16 sm:py-20 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2
                class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-center mb-12 
             bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600 animate-fade-in">
                What Our Students Say
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Review 1 -->
                <div class="bg-purple-50 border border-gray-200 p-8 rounded-3xl shadow-lg hover:shadow-2xl 
                  transition-all duration-300 transform hover:-translate-y-2 animate-slide-up"
                    data-delay="0.1" aria-label="Testimonial by James from London">
                    <div class="flex items-center mb-4">
                        <img src="https://thestudyportal.online/storage/Seru Website/1755510232_IxTJmqhg_istockphoto-1230749818-612x612.jpg" alt="James's photo"
                            class="w-24 h-20 rounded-full mr-4 border-2 border-purple-200">
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">James, London</p>
                            <div class="flex text-purple-600">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">“The final 37-question mock felt just like the real SERU exam — I passed
                        first time!”</p>
                </div>

                <!-- Review 2 -->
                <div class="bg-purple-50 border border-gray-200 p-8 rounded-3xl shadow-lg hover:shadow-2xl 
                  transition-all duration-300 transform hover:-translate-y-2 animate-slide-up"
                    data-delay="0.2" aria-label="Testimonial by Aisha from Manchester">
                    <div class="flex items-center mb-4">
                        <img src="https://thestudyportal.online/storage/Seru Website/1755510196_uXKQy9aG_pexels-olly-733872.jpg" alt="Aisha's photo"
                            class="w-24 h-20 rounded-full mr-4 border-2 border-purple-200">
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">Aisha, London</p>
                            <div class="flex text-purple-600">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">“I loved the section-wise practice — it helped me master my weak areas
                        quickly.”</p>
                </div>

                <!-- Review 3 -->
                <div class="bg-purple-50 border border-gray-200 p-8 rounded-3xl shadow-lg hover:shadow-2xl 
                  transition-all duration-300 transform hover:-translate-y-2 animate-slide-up"
                    data-delay="0.3" aria-label="Testimonial by Haroon from Birmingham">
                    <div class="flex items-center mb-4">
                        <img src="https://thestudyportal.online/storage/Seru Website/1755510225_5uKwLtwd_pexels-italo-melo-881954-2379004.jpg" alt="Haroon's photo"
                            class="w-24 h-20 rounded-full mr-4 border-2 border-purple-200">
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">Haroon, London</p>
                            <div class="flex text-purple-600">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">“The timed mocks boosted my confidence — exam pressure felt so much
                        easier.”</p>
                </div>

                <!-- Review 4 -->
                <div class="bg-purple-50 border border-gray-200 p-8 rounded-3xl shadow-lg hover:shadow-2xl 
                  transition-all duration-300 transform hover:-translate-y-2 animate-slide-up"
                    data-delay="0.4" aria-label="Testimonial by Priya from Leicester">
                    <div class="flex items-center mb-4">
                        <img src="https://thestudyportal.online/storage/Seru Website/1755510209_Bea6Aq8c_istockphoto-1135381120-612x612.jpg" alt="Priya's photo"
                            class="w-24 h-20 rounded-full mr-4 border-2 border-purple-200">
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">Priya, London</p>
                            <div class="flex text-purple-600">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">“Super easy to use on my phone — I practiced while commuting.”</p>
                </div>

                <!-- Review 5 -->
                <div class="bg-purple-50 border border-gray-200 p-8 rounded-3xl shadow-lg hover:shadow-2xl 
                  transition-all duration-300 transform hover:-translate-y-2 animate-slide-up"
                    data-delay="0.5" aria-label="Testimonial by Daniel from Glasgow">
                    <div class="flex items-center mb-4">
                        <img src="https://thestudyportal.online/storage/Seru Website/1755510217_A3lEr7gT_photo-1557862921-37829c790f19.jpeg" alt="Daniel's photo"
                            class="w-24 h-20 rounded-full mr-4 border-2 border-purple-200">
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">Daniel, London</p>
                            <div class="flex text-purple-600">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">“Honestly the best prep for SERU — I went into the test relaxed and
                        ready.”</p>
                </div>

                <!-- Review 6 -->
                <div class="bg-purple-50 border border-gray-200 p-8 rounded-3xl shadow-lg hover:shadow-2xl 
                  transition-all duration-300 transform hover:-translate-y-2 animate-slide-up"
                    data-delay="0.6" aria-label="Testimonial by Fatima from Leeds">
                    <div class="flex items-center mb-4">
                        <img src="https://thestudyportal.online/storage/Seru Website/1755510202_JhnQnY9N_istockphoto-1459333105-612x612.jpg" alt="Fatima's photo"
                            class="w-24 h-20 rounded-full mr-4 border-2 border-purple-200">
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">Fatima, London</p>
                            <div class="flex text-purple-600">
                                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i
                                    class="fa-solid fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">“The mock exams felt exactly like the TfL test. Couldn’t have asked for
                        better prep.”</p>
                </div>

            </div>
        </div>
    </section>


    <!-- Newsletter -->
    <section class="py-16 sm:py-20 bg-gradient-to-b from-purple-50 to-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center animate-fade-in">
            <h2
                class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">
                Get Free SERU Tips & Mock Exam</h2>
            <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                <input type="email" placeholder="Enter your email"
                    class="px-5 py-3 bg-white border border-gray-300 text-gray-900 placeholder-gray-400 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-400 text-sm sm:text-base"
                    aria-label="Email for newsletter">
                <button type="button"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-full text-sm sm:text-base font-semibold">Mail
                    Us</button>
            </form>
            <p class="text-gray-600 mt-4 text-base">We respect your privacy. Unsubscribe anytime.</p>
        </div>
    </section>

    <!-- Footer -->
    @include('main.footer')

    <!-- Back to Top -->
    <button id="back-to-top"
        class="fixed bottom-6 right-6 bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-full shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 hidden"
        aria-label="Back to top">
        <i class="fa-solid fa-arrow-up text-lg"></i>
    </button>

    <!-- Inline JS -->
    <script>
        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);

        // Hero Section Animations
        gsap.from(".animate-slide-in-left", {
            x: -100,
            opacity: 0,
            duration: 1,
            ease: "power3.out",
            scrollTrigger: {
                trigger: "#main-content",
                start: "top 80%",
            }
        });

        // Slide Up Animations
        document.querySelectorAll(".animate-slide-up").forEach((el, index) => {
            gsap.from(el, {
                y: 50,
                opacity: 0,
                duration: 0.8,
                delay: el.dataset.delay || index * 0.2,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: el,
                    start: "top 85%",
                }
            });
        });

        // Fade In Animations
        gsap.from(".animate-fade-in", {
            opacity: 0,
            duration: 1,
            ease: "power3.out",
            scrollTrigger: {
                trigger: ".animate-fade-in",
                start: "top 80%",
            }
        });

        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuToggle?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            gsap.to(mobileMenu, {
                height: mobileMenu.classList.contains('hidden') ? 0 : 'auto',
                duration: 0.5,
                ease: "power3.inOut"
            });
        });

        // Back to Top
        const backToTop = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove('hidden');
                gsap.to(backToTop, {
                    opacity: 1,
                    duration: 0.3
                });
            } else {
                gsap.to(backToTop, {
                    opacity: 0,
                    duration: 0.3,
                    onComplete: () => backToTop.classList.add('hidden')
                });
            }
        });
        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

    <!-- Animation Styles -->
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(2deg);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</div>
