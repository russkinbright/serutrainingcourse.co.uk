@extends('home.default')
@section('content')
    <style>
        [x-cloak] {
            display: none
        }

        .glassmorphic {
            background: rgba(255, 255, 255, .1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, .3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .1)
        }

        .card-hover {
            transition: transform .3s ease, box-shadow .3s ease
        }

        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0, 0, 0, .2)
        }

        .btn-pulse {
            position: relative;
            overflow: hidden
        }

        .btn-pulse::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, .3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width .5s ease, height .5s ease
        }

        .btn-pulse:hover::after {
            width: 300px;
            height: 300px
        }

        .review-slider {
            position: relative;
            overflow: hidden;
            width: 100%
        }

        .review-container {
            display: flex;
            width: max-content;
            animation: scroll-left 30s linear infinite
        }

        .review-container:hover {
            animation-play-state: paused
        }

        .review-card {
            flex: 0 0 auto;
            width: 300px;
            margin-right: 20px
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(0)
            }

            100% {
                transform: translateX(-50%)
            }
        }

        .star-rating .fa-star {
            color: #e5e7eb
        }

        .star-rating .fa-star.checked {
            color: #f59e0b
        }

        .sticky-cta {
            position: sticky;
            top: 20px;
            z-index: 10
        }

        @media (max-width:767px) {
            .sticky-cta {
                position: static
            }
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            background: rgba(255, 255, 255, .1);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, .3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, .1);
            padding: 2rem
        }

        .loading-text {
            margin-top: 1rem;
            color: #4b5563;
            font-size: 1.25rem;
            font-weight: 500;
            text-align: center
        }
    </style>

    <header
        class="fixed top-0 left-0 w-full bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg z-20 transition-all duration-300"
        data-header>
        @include('main.navbar')
    </header>

    <main id="top" class="relative bg-gray-100 min-h-screen pt-20">
        <div class="mx-auto max-w-[1500px] relative z-10 px-4 py-8">
            <div x-data="{
                course: null,
                messages: [],
                async init() {
                    const slug = window.location.pathname.split('/').pop();
            
                    try {
                        const response = await fetch(`/api/course-details/${slug}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const result = await response.json();
            
                        if (result.success) {
                            this.course = result.course;
            
                            // Meta & title (guard if tags don't exist)
                            const md = document.querySelector('meta[name=description]');
                            if (md) md.setAttribute('content', this.course.meta_description || '');
                            const mk = document.querySelector('meta[name=keywords]');
                            if (mk) mk.setAttribute('content', this.course.meta_keywords || '');
                            const mr = document.querySelector('meta[name=robots]');
                            if (mr) mr.setAttribute('content', this.course.robots_meta || '');
                            const can = document.querySelector('link[rel=canonical]');
                            if (can) can.setAttribute('href', this.course.canonical_url || ('/course-details/' + this.course.slug));
                            document.title = this.course.title || 'Course Details';
            
                            // Schema markup
                            if (this.course.schema_markup) {
                                const s = document.createElement('script');
                                s.type = 'application/ld+json';
                                s.text = this.course.schema_markup;
                                document.head.appendChild(s);
                            }
            
                            // Put description into iframe
                            const iframeEl = document.getElementById('dbHtml');
                            const html = this.course?.description ?? '';
                            const styledHtml = `
                                                <style>
                                                  body{font-size:18px;line-height:1.6;font-family:Arial,sans-serif;color:#333}
                                                  h1,h2,h3{color:#ff9900}
                                                  ul,ol{padding-left:1.5rem}
                                                </style>
                                                ${html}
                                            `;
                            iframeEl.srcdoc = styledHtml;
            
                            const resize = () => {
                                try {
                                    iframeEl.style.height = iframeEl.contentWindow.document.documentElement.scrollHeight + 'px';
                                } catch (_) {}
                            };
                            iframeEl.addEventListener('load', () => {
                                resize();
                                try {
                                    const ro = new ResizeObserver(resize);
                                    ro.observe(iframeEl.contentDocument.documentElement);
                                } catch (_) {}
                            });
            
                            // Fire GA4 view_item event
                            this.pushViewItem(this.course);
            
                        } else {
                            this.addMessage(result.message || 'Course not found.', 'error');
                        }
                    } catch (error) {
                        console.error('Error fetching course:', error);
                        this.addMessage('Failed to load course details. Please try again.', 'error');
                    }
            
                    if (window.AOS) AOS.init({ duration: 800, easing: 'ease-in-out', once: true });
            
                    if (!this.course && window.lottie) {
                        lottie.loadAnimation({
                            container: document.getElementById('loading-animation'),
                            renderer: 'svg',
                            loop: true,
                            autoplay: true,
                            path: 'https://assets4.lottiefiles.com/packages/lf20_44rltw5h.json'
                        });
                    }
                },
                // ✅ define as an Alpine method (no 'function' keyword)
                // replace your pushViewItem with this
                pushViewItem(course) {
                    if (!course) return;
            
                    const id = String(course.unique_id ?? course.id ?? '');
                    // Guard: never push twice for the same course on this page load
                    if (window.__viSentId === id) {
                        return; // already sent
                    }
                    window.__viSentId = id;
            
                    window.dataLayer = window.dataLayer || [];
            
                    // Best practice: clear any previous ecommerce object to avoid carry-over
                    window.dataLayer.push({ ecommerce: null });
            
                    window.dataLayer.push({
                        event: 'view_item',
                        // Optional event_id you can also use for GA/Ads dedup if needed
                        event_id: 'vi-' + id,
                        ecommerce: {
                            currency: 'GBP',
                            value: Number(course.price) || 0,
                            items: [{
                                item_id: id,
                                item_name: course.title,
                                price: Number(course.price) || 0,
                                currency: 'GBP',
                                item_category: course.category ?? 'Courses',
                                item_brand: 'Serutrainingcourse',
                                item_variant: course.level ?? 'Default',
                                quantity: 1
                            }]
                        }
                    });
                    // console.log('GA4 view_item pushed once', id);
                },
            
                addMessage(text, type) {
                    const id = Date.now();
                    this.messages.push({ id, text, type });
                    if (type === 'success') {
                        setTimeout(() => {
                            this.messages = this.messages.filter(msg => msg.id !== id);
                        }, 3000);
                    }
                },
                addToCart() {
                    if (!this.course) {
                        this.addMessage('No course selected.', 'error');
                        return;
                    }
                    const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
                    const newItem = {
                        unique_id: this.course.unique_id ?? this.course.id,
                        title: this.course.title,
                        price: isNaN(parseFloat(this.course.price)) ? 0 : parseFloat(this.course.price),
                        image: this.course.image ? this.course.image : '/assets/images/default-course.jpg',
                        quantity: 1
                    };
                    cartItems.push(newItem);
                    localStorage.setItem('cartItems', JSON.stringify(cartItems));
                    this.addMessage('Course added to cart!', 'success');
                    window.location.href = '{{ route('cart') }}';
                }
            }" x-init="init()">
                <!-- Messages -->
                <div class="mb-6" data-aos="fade-up">
                    <template x-for="message in messages" :key="message.id">
                        <div :class="message.type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
                            'bg-red-100 border-red-500 text-red-700'"
                            class="border-l-4 p-4 mb-2 rounded-r-lg flex items-center justify-between animate-fade-in glassmorphic">
                            <div class="flex items-center">
                                <i :class="message.type === 'success' ? 'fas fa-check-circle text-green-500' :
                                    'fas fa-exclamation-circle text-red-500'"
                                    class="mr-2"></i>
                                <span x-text="message.text"></span>
                            </div>
                            <button @click="messages = messages.filter(m => m.id !== message.id)"
                                class="text-xl font-bold text-gray-700 hover:text-gray-900">×</button>
                        </div>
                    </template>
                </div>

                <!-- Hero -->
                <div x-show="course" class="relative h-96 rounded-2xl overflow-hidden mb-8 group shadow-2xl"
                    data-aos="fade-up">
                    <img :src="'https://thestudyportal.online/storage/Seru Website/1755511868_vZONlQCS_3d066e8f-86dd-4b51-b27f-905e0d414583.jpg'"
                        :alt="course?.title || 'Course image'"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-black/30"></div> <!-- fixed 'bg-black/30t' -->
                    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6">
                        <div class="px-6 py-4 bg-black/30 rounded-lg shadow-lg">
                            <h1 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow-xl leading-tight"
                                x-text="course?.title"></h1>
                            <p
                                class="mt-3 flex items-center justify-center text-lg md:text-xl font-medium text-orange-300 drop-shadow-md">
                                <i class="fas fa-clock mr-2 text-orange-400"></i>
                                <span x-text="course?.duration"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Loading -->
                <div x-show="!course" class="loading-container" data-aos="fade-up">
                    <div id="loading-animation" style="width:150px; height:150px;"></div>
                    <p class="loading-text">Loading course details...</p>
                </div>

                <!-- Two columns -->
                <div x-show="course" class="flex flex-col md:flex-row md:items-start gap-8 mb-8">
                    <!-- Left -->
                    <div class="flex-1 md:max-w-[65%]">
                        <div class="bg-white glassmorphic rounded-2xl p-6 card-hover h-full" data-aos="fade-up">
                            <h2 class="text-2xl font-extrabold text-orange-600 mb-4 flex items-center">
                                <i class="fas fa-book mr-2 text-orange-500"></i> Course Details
                            </h2>

                            <div class="text-gray-700 mb-4">
                                <p>
                                    <i class="fas fa-graduation-cap mr-2 text-orange-500"></i>
                                    <span x-text="course?.title"></span>
                                </p>
                            </div>

                            <iframe id="dbHtml" class="w-full border-0 des" referrerpolicy="no-referrer"></iframe>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="md:w-[32%] sticky-cta">
                        <div class="bg-white glassmorphic rounded-2xl p-6 card-hover" data-aos="fade-up"
                            data-aos-delay="100">
                            <img :src="course?.image || '{{ asset('assets/images/default-course.jpg') }}'"
                                class="w-full h-64 object-cover rounded-lg mb-4 border border-pink-200 transform hover:scale-105 transition"
                                :alt="course?.title || 'Course image'">
                            <div class="text-2xl font-bold text-green-600 mb-4">
                                <span x-text="'£' + (parseFloat(course?.price || 0)).toFixed(2)"></span>
                            </div>
                            <button @click="addToCart()"
                                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white cursor-pointer py-3 font-semibold rounded-lg btn-pulse transition transform hover:scale-95 flex items-center justify-center">
                                <i class="fas fa-rocket mr-2"></i> Take This Course
                            </button>
                            <a href="{{ route('course.page') }}"
                                class="mt-2 w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 cursor-pointer font-semibold rounded-lg btn-pulse transition transform hover:scale-95 flex items-center justify-center text-center">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Courses
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div x-show="course" class="mb-8" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="text-2xl font-extrabold text-orange-600 mb-4 flex items-center">
                        <i class="fas fa-star mr-2 text-orange-500"></i> Student Reviews
                    </h2>
                    <div class="review-slider">
                        <div class="review-container">
                            <div class="review-card bg-white glassmorphic rounded-2xl p-6 card-hover" data-aos="fade-right">
                                <div class="flex items-center mb-2">
                                    <div class="star-rating flex">
                                        <i class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star"></i>
                                    </div>
                                    <span class="ml-2 text-gray-600">4.0</span>
                                </div>
                                <p class="text-gray-700">"This course was a game-changer! The content was engaging and
                                    well-structured."</p>
                                <p class="mt-2 text-gray-500 font-semibold">— Sarah J.</p>
                            </div>

                            <div class="review-card bg-white glassmorphic rounded-2xl p-6 card-hover" data-aos="fade-right"
                                data-aos-delay="100">
                                <div class="flex items-center mb-2">
                                    <div class="star-rating flex">
                                        <i class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i>
                                    </div>
                                    <span class="ml-2 text-gray-600">5.0</span>
                                </div>
                                <p class="text-gray-700">"Absolutely loved the practical approach. Highly recommend!"</p>
                                <p class="mt-2 text-gray-500 font-semibold">— Michael T.</p>
                            </div>

                            <div class="review-card bg-white glassmorphic rounded-2xl p-6 card-hover" data-aos="fade-right"
                                data-aos-delay="200">
                                <div class="flex items-center mb-2">
                                    <div class="star-rating flex">
                                        <i class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star-half-alt checked"></i>
                                    </div>
                                    <span class="ml-2 text-gray-600">4.5</span>
                                </div>
                                <p class="text-gray-700">"Great course, but could use more interactive elements."</p>
                                <p class="mt-2 text-gray-500 font-semibold">— Emily R.</p>
                            </div>

                            <!-- Duplicates for loop effect -->
                            <div class="review-card bg-white glassmorphic rounded-2xl p-6 card-hover"
                                data-aos="fade-right">
                                <div class="flex items-center mb-2">
                                    <div class="star-rating flex">
                                        <i class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star"></i>
                                    </div>
                                    <span class="ml-2 text-gray-600">4.0</span>
                                </div>
                                <p class="text-gray-700">"This course was a game-changer! The content was engaging and
                                    well-structured."</p>
                                <p class="mt-2 text-gray-500 font-semibold">— Sarah J.</p>
                            </div>

                            <div class="review-card bg-white glassmorphic rounded-2xl p-6 card-hover"
                                data-aos="fade-right" data-aos-delay="100">
                                <div class="flex items-center mb-2">
                                    <div class="star-rating flex">
                                        <i class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i>
                                    </div>
                                    <span class="ml-2 text-gray-600">5.0</span>
                                </div>
                                <p class="text-gray-700">"Absolutely loved the practical approach. Highly recommend!"</p>
                                <p class="mt-2 text-gray-500 font-semibold">— Michael T.</p>
                            </div>

                            <div class="review-card bg-white glassmorphic rounded-2xl p-6 card-hover"
                                data-aos="fade-right" data-aos-delay="200">
                                <div class="flex items-center mb-2">
                                    <div class="star-rating flex">
                                        <i class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star checked"></i><i class="fas fa-star checked"></i><i
                                            class="fas fa-star-half-alt checked"></i>
                                    </div>
                                    <span class="ml-2 text-gray-600">4.5</span>
                                </div>
                                <p class="text-gray-700">"Great course, but could use more interactive elements."</p>
                                <p class="mt-2 text-gray-500 font-semibold">— Emily R.</p>
                            </div>
                        </div>
                    </div>
                </div> <!-- /x-data wrapper -->
            </div>
        </div>
    </main>

    @include('main.footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const backTopBtn = document.getElementById('backToTopBtn');
            if (!backTopBtn) return;
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) backTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                else backTopBtn.classList.add('opacity-0', 'pointer-events-none');
            });
            backTopBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <a href="#top" id="backToTopBtn"
        class="fixed bottom-6 right-6 bg-gradient-to-r from-pink-600 to-orange-600 text-white p-4 rounded-full shadow-lg z-50 transition-all duration-300 opacity-0 pointer-events-none hover:scale-110"
        aria-label="Back to Top" data-aos="fade-up">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
        </svg>
    </a>
@endsection
