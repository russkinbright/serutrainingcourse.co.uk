@include('home.default')
<!-- Video.js CSS -->
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />

<!-- Video.js JS -->
<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

<style>
    .video-container {
        position: relative;
        width: 100%;
        max-width: 1300px;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        background-color: #000;
    }

    /* Style the Video.js player */
    .video-js {
        width: 100%;
        height: 100%;
        border-radius: 1rem;
        overflow: hidden;
        font-size: clamp(14px, 2.5vw, 16px);
    }

    /* Prevent download */
    video::-internal-media-controls-download-button {
        display: none;
    }

    video::-webkit-media-controls-enclosure {
        overflow: hidden;
    }

    /* Optional: center play button and style */
    .vjs-big-play-button {
        background-color: rgba(147, 51, 234, 0.8);
        border: none;
        border-radius: 50%;
        width: clamp(60px, 8vw, 72px);
        height: clamp(60px, 8vw, 72px);
        margin-left: calc(clamp(60px, 8vw, 72px) / -2);
        margin-top: calc(clamp(60px, 8vw, 72px) / -2);
        font-size: clamp(1.5rem, 3vw, 2rem);
        transition: background 0.3s;
    }

    .vjs-big-play-button:hover {
        background-color: rgba(147, 51, 234, 1);
    }

    ul {
        list-style: disc;
    }

    ol {
        list-style: decimal;
    }

    li {
        margin: 0.3em 0;
        margin-left: 1.5rem;
    }

    /* Responsive carousel adjustments */
    .carousel-item {
        width: 100%;
        padding: 0 0.5rem;
    }

    @media (min-width: 640px) {
        .carousel-item {
            width: 50%;
        }
    }

    @media (min-width: 1024px) {
        .carousel-item {
            width: 33.33%;
        }
    }

    .course-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .course-card img {
        height: clamp(180px, 25vw, 240px);
        object-fit: cover;
    }

    .course-card h3 {
        font-size: clamp(1rem, 2.5vw, 1.125rem);
    }

    .course-card .prose {
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    .course-card a {
        width: clamp(120px, 20vw, 140px);
        font-size: clamp(0.875rem, 2vw, 1rem);
    }
</style>

<div class="container-fluid" x-data="{
    courses: [],
    otherCourses: [],
    currentPage: 1,
    lastPage: 1,
    search: '',
    isLoading: false,
    errorMessage: '',
    currentCarouselIndex: 0,
    error: '',
    showModal: false,
    showExpireModal: false,
    selectedCourseId: null,
    expiredToday: false,
    videoError: false,
    posterUrl: 'https://thestudyportal.online/storage/Seru Website/1755580204_21usBw6d_ChatGPT Image Aug 19, 2025, 11_09_05 AM.png',

    async fetchCourses() {
        try {
            const res = await fetch('/api/learner/courses', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) {
                const today = new Date().toISOString().slice(0, 10);
                this.courses = data.courses.map(course => {
                    let expire_at = course.expire_at ? new Date(course.expire_at).toISOString().slice(0, 10) : null;
                    course.expireFormatted = expire_at;
                    course.isExpiringToday = expire_at === today;
                    return course;
                });
            } else {
                this.error = data.message || 'Unable to load courses.';
            }
        } catch (err) {
            this.error = 'Something went wrong.';
        }
    },

    async fetchOtherCourses(page = 1) {
        this.isLoading = true;
        this.errorMessage = '';
        this.currentPage = page;
        try {
            const url = '{{ route('main.course.search') }}?search=' + encodeURIComponent(this.search || '') + '&page=' + page;
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            const data = await response.json();
            this.otherCourses = (data.courses?.data || []).map(course => ({
                ...course,
                id: course.id || course.unique_id,
                description: course.description || 'Explore this course to gain valuable skills and knowledge tailored to your learning needs.',
                image: course.image || 'https://via.placeholder.com/300x200?text=Course+Image'
            }));
            this.currentPage = data.courses?.current_page || 1;
            this.lastPage = data.courses?.last_page || 1;
        } catch (error) {
            console.error('Fetch error:', error);
            this.errorMessage = 'Failed to load courses. Please try again.';
            this.otherCourses = [];
        } finally {
            this.isLoading = false;
        }
    },

    openModal(unique_id) {
        const course = this.courses.find(c => c.unique_id === unique_id);
        this.selectedCourseId = unique_id;
        if (course && course.isExpiringToday) {
            this.showExpireModal = true;
        } else {
            this.showModal = true;
        }
    },

    closeModal() {
        this.showModal = false;
        this.showExpireModal = false;
        this.selectedCourseId = null;
    },

    navigateToSectionTest() {
        if (this.selectedCourseId) {
            window.location.href = '/learner/course/' + this.selectedCourseId;
        }
        this.closeModal();
    },

    navigateToFinalMock() {
        document.getElementById('finalMockForm').submit(); // POST (URL not shown), then 302 -> /learner/final-mock
        this.closeModal();
    },

    showCourseDetails(id) {
        window.location.href = '/learner/course-details/' + id;
    },

    showCourseCertificate(unique_id) {
        window.location.href = '/learner/certificate/' + unique_id;
    },

    async confirmExpire() {
        if (!this.selectedCourseId) return;
        try {
            const res = await fetch(`/api/learner/course-progress/${this.selectedCourseId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.courses = this.courses.filter(course => course.unique_id !== this.selectedCourseId);
                this.closeModal();
                window.location.href = 'https://serutrainingcourse.co.uk/';
            } else {
                alert('Failed to delete course progress.');
            }
        } catch (err) {
            alert('Error deleting course progress.');
        }
    },

    nextCarousel() {
        this.currentCarouselIndex = (this.currentCarouselIndex + 1) % Math.max(1, this.otherCourses.length);
    },

    prevCarousel() {
        this.currentCarouselIndex = (this.currentCarouselIndex - 1 + Math.max(1, this.otherCourses.length)) % Math.max(1, this.otherCourses.length);
    },

    handleVideoError() {
        this.videoError = true;
    }
}" x-init="fetchCourses();
fetchOtherCourses();
$nextTick(() => {
    console.log('Initializing Video.js player');
    const player = videojs('tutorial-video', {
        autoplay: false,
        preload: 'none',
        controls: true
    });
    player.on('error', () => {
        console.log('Video.js error');
        $data.videoError = true;
    });
    player.on('ready', () => {
        console.log('Video.js player ready');
    });
});">
    <div class="flex-1 p-4 sm:p-6 gradient-bg">
        <!-- My Courses Section -->
        <div class="mb-8 sm:mb-10" x-show="courses.length > 0">
            <div class="flex justify-between items-center">
                <h2 class="text-lg sm:text-xl font-bold text-white">My Courses</h2>
            </div>
            <div class="bg-white/90 rounded-2xl shadow-xl overflow-hidden border border-purple-100">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700">
                            <th class="p-3 sm:p-4 text-left font-semibold">Course Name</th>
                            <th class="p-3 sm:p-4 text-center font-semibold">Expire At</th>
                            <th class="p-3 sm:p-4 text-center font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="course in courses" :key="course.unique_id">
                            <tr
                                class="border-t border-purple-200 hover:bg-purple-50/50 transition-colors duration-300 course-card">
                                <td class="p-3 sm:p-4 align-middle">
                                    <h2 class="text-base sm:text-lg font-semibold text-purple-800 line-clamp-2"
                                        x-text="course.title"></h2>
                                </td>
                                <td class="p-3 sm:p-4 align-middle text-center">
                                    <div
                                        class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-md border border-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                                        <span x-text="course.expireFormatted"></span>
                                    </div>
                                </td>
                                <td class="p-3 sm:p-4 align-middle text-center">
                                    <button @click="openModal(course.unique_id)"
                                        class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-md border border-white hover:shadow-lg hover:scale-105 transition-all duration-300 continue-button hover:cursor-pointer">
                                        Continue
                                        <svg class="w-4 h-4 ml-2 svg-icon" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- No Courses Message -->
        <div x-show="courses.length === 0" class="text-center mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-xl font-bold text-white mb-4">No Courses Enrolled</h2>
            <p class="text-purple-200 text-sm sm:text-base">Explore our available courses below to start your SERU
                training!</p>
        </div>

        <!-- Tutorial Video Section -->
        <div class="mt-8 sm:mt-10 mb-8 sm:mb-10">
            <h2 class="text-xl sm:text-2xl font-bold text-purple-800 mb-4 text-center">How to Practice Your Course</h2>
            <div class="video-container" x-show="!videoError">
                <video id="tutorial-video" class="video-js vjs-big-play-centered vjs-theme-city" controls
                    preload="metadata" playsinline controlsList="nodownload" oncontextmenu="return false"
                    data-setup='{}' :poster="posterUrl">
                    <source
                        src="https://thestudyportal.online/storage/Seru%20Website/1754645736_LOmQnoJj_istockphoto-2210801206-640_adpp_is.mp4"
                        type="video/mp4" />
                    <source src="https://thestudyportal.online/storage/Seru%20Website/fallback-video.webm"
                        type="video/webm" />
                    Your browser does not support the video tag.
                </video>
            </div>

            <div x-show="videoError" class="video-error text-center">
                <p class="text-sm sm:text-base">Sorry, the tutorial video could not be loaded. Please check your
                    connection or try again later.</p>
                <a href="https://thestudyportal.online/storage/Seru%20Website/1754645736_LOmQnoJj_istockphoto-2210801206-640_adpp_is.mp4"
                    target="_blank" class="text-purple-200 underline text-sm sm:text-base">Try opening the video
                    directly</a>
            </div>
        </div>

        <!-- Explore Other Courses Carousel -->
        <div>
            <!-- Header -->
            <h2 class="text-xl sm:text-2xl font-bold text-center text-purple-800 mb-4">
                Explore All Course
            </h2>

            <!-- Carousel -->
            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-500"
                    :style="'transform: translateX(-' + (currentCarouselIndex * (100 / Math.min(otherCourses.length, 3))) + '%)'">
                    <template x-for="(course, index) in otherCourses" :key="course.id">
                        <div class="carousel-item flex-shrink-0 px-2">
                            <div
                                class="course-card h-full flex flex-col bg-white rounded-2xl shadow-xl p-4 border border-purple-200 transform hover:-translate-y-2 hover:shadow-purple-500/30 transition-all duration-300">
                                <div class="overflow-hidden rounded-xl mb-4">
                                    <img :src="course.image" alt="Course Image"
                                        class="w-full object-cover rounded-xl transform hover:scale-105 transition-transform duration-500">
                                </div>
                                <h3 class="text-lg font-semibold text-purple-800 mb-2 line-clamp-2"
                                    x-text="course.title"></h3>
                                <div class="relative flex-grow">
                                    <div
                                        class="prose prose-sm text-gray-600 max-h-[4.5em] overflow-hidden leading-relaxed line-clamp-3">
                                        <div x-html="course.description"></div>
                                    </div>
                                    <div
                                        class="pointer-events-none absolute inset-x-0 bottom-0 h-6 bg-gradient-to-b from-transparent to-white">
                                    </div>
                                </div>
                                <a :href="'/course-details/' + course.slug"
                                    class="mt-auto inline-flex items-center justify-center py-2 bg-gradient-to-r from-purple-600 to-indigo-600 
                                    text-white font-semibold rounded-lg shadow-md hover:shadow-purple-500/40 hover:scale-105 
                                    transition-all duration-300">
                                    Learn More
                                    <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Navigation Buttons Centered Below -->
            <div class="flex justify-center space-x-3 mt-6">
                <button @click="prevCarousel"
                    class="p-2 sm:p-3 bg-gradient-to-br from-purple-600 to-indigo-600 text-white rounded-full shadow-lg hover:shadow-purple-500/40 transition-all duration-300 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="nextCarousel"
                    class="p-2 sm:p-3 bg-gradient-to-br from-purple-600 to-indigo-600 text-white rounded-full shadow-lg hover:shadow-purple-500/40 transition-all duration-300 group">
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Loading and Error States -->
            <div x-show="isLoading" class="text-center text-purple-400 mt-4 text-sm sm:text-base">Loading...</div>
            <div x-show="errorMessage" class="text-center text-red-400 mt-4 text-sm sm:text-base"
                x-text="errorMessage"></div>
        </div>

        <!-- Modal -->
        <div x-show="showModal" x-cloak
            class="fixed inset-0 backdrop-blur-sm bg-black/30 flex items-center justify-center z-50 p-4"
            @click="closeModal">
            <div class="bg-gradient-to-br from-blue-900 to-indigo-800 rounded-2xl p-6 max-w-md w-full transform transition-all duration-300 scale-100 hover:scale-[1.02] shadow-2xl border border-indigo-300/20"
                @click.stop>
                <div class="relative">
                    <button @click="closeModal"
                        class="absolute top-2 right-2 text-indigo-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="text-center mb-6">
                        <h2
                            class="text-2xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-300 mb-2">
                            Practice Mode</h2>
                        <p class="text-white font-semibold text-sm sm:text-base">Choose how you want to practice for
                            your SERU
                            training</p>
                    </div>
                    <form id="finalMockForm" action="{{ route('learner.finalMock.start') }}" method="POST"
                        class="hidden">
                        @csrf
                        <input type="hidden" name="start" value="1">
                    </form>
                    <div class="flex flex-col space-y-4">
                        <button @click="navigateToSectionTest"
                            class="group flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-cyan-600/80 to-blue-600/80 hover:from-cyan-500 hover:to-blue-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-cyan-500/20 transition-all duration-300 cursor-pointer border border-cyan-400/20 relative overflow-hidden">
                            <span class="relative z-10">Section Test</span>
                            <span class="relative z-10 flex items-center">
                                <span class="mr-2 text-cyan-200 group-hover:text-white">Practice by section</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </button>

                        <form id="finalMockForm" action="{{ route('learner.finalMock.start') }}" method="POST"
                            class="hidden">
                            @csrf
                            <input type="hidden" name="start" value="1">
                        </form>
                        <button @click="document.getElementById('finalMockForm').submit(); closeModal();"
                            class="group flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-indigo-600/80 to-purple-600/80 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-purple-500/20 transition-all duration-300 cursor-pointer border border-indigo-400/20 relative overflow-hidden">
                            <span class="relative z-10">Final MOCK</span>
                            <span class="relative z-10 flex items-center">
                                <span class="mr-2 text-indigo-200 group-hover:text-white">Full 37-question test</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        </button>
                    </div>
                    <div class="mt-6 text-center">
                        <p class="text-xs sm:text-sm text-white font-semibold">SERU Training Course - Master your
                            knowledge</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expire Modal -->
        <div x-show="showExpireModal" x-cloak
            class="fixed inset-0 backdrop-blur-sm bg-black/50 flex items-center justify-center z-50 p-4"
            @click="closeModal">
            <div @click.stop
                class="relative bg-white p-6 sm:p-8 rounded-xl shadow-2xl max-w-lg sm:max-w-xl w-full border border-purple-700 overflow-hidden">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-500/20 rounded-full filter blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-500/20 rounded-full filter blur-2xl">
                </div>
                <div class="relative z-10 text-center space-y-5">
                    <div
                        class="mx-auto w-16 h-16 bg-purple-500/30 rounded-full border-purple-700 border-2 flex items-center justify-center shadow-lg shadow-purple-500/20">
                        <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="space-y-2 mb-3">
                        <h2 class="text-xl sm:text-2xl font-bold text-rose-600">Course Access Expired</h2>
                        <p class="text-black font-bold text-sm sm:text-base">Continue your SERU training by renewing
                            your access</p>
                    </div>
                    <button @click="confirmExpire"
                        class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white rounded-xl font-semibold text-base sm:text-lg transition-all duration-300 shadow-lg hover:shadow-purple-500/30 group transform hover:scale-[1.02]">
                        <span class="relative flex items-center justify-center space-x-2">
                            <span>Buy Again</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const player = videojs('tutorial-video', {
                autoplay: false,
                preload: 'none',
                controls: true,
            });

            player.on('ready', () => {
                console.log('Video.js ready');
            });

            player.on('play', () => {
                console.log('Video started');
            });

            player.on('error', () => {
                console.log('Video error');
            });
        });
    </script>
