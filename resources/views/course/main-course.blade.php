@extends('home.default')


<style>
    [x-cloak] {
            display: none !important;
        }
    .glassmorphic {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .truncate-3-lines {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .animate-pulse-slow {
        animation: pulse 2.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .card-hover {
        transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .card-hover:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
        background: rgba(255, 255, 255, 0.2);
    }

    .card-hover::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: 0.5s;
    }

    .card-hover:hover::before {
        left: 100%;
    }

    .pagination-button {
        transition: all 0.3s ease;
    }

    .pagination-button:hover:not(:disabled) {
        background-color: #7c3aed;
        transform: scale(1.1);
    }

    .pagination-button:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .search-input {
        transition: all 0.3s ease;
    }

    .search-input:focus {
        transform: scale(1.02);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
    }

    .badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(45deg, #facc15, #f472b6);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: bold;
    }

    .draw {
        stroke-dasharray: 10;
        stroke-dashoffset: 10;
    }

    input:not(:placeholder-shown)~.search-dots {
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .search-input:focus {
        box-shadow: 0 0 15px rgba(167, 139, 250, 0.6);
        border-color: rgba(167, 139, 250, 0.8);
    }

    .relative:hover svg {
        transform: translateY(-50%) scale(1.1);
    }

    .star-rating {
        display: flex;
        align-items: center;
    }

    .star-rating i {
        font-size: 1rem;
        margin-right: 0.2rem;
        transition: color 0.3s ease, transform 0.2s ease;
        cursor: pointer;
    }

    .star-rating i:hover {
        transform: scale(1.2);
    }

    .star-rating .fas.fa-star {
        color: #facc15;
    }

    .star-rating .fas.fa-star-half-alt {
        color: #facc15;
    }

    .star-rating .far.fa-star {
        color: #d1d5db;
    }

    .star-rating .hover-star {
        color: #facc15 !important;
    }
</style>

<div class="bg-gray-100 font-sans min-h-screen" x-data="{
    courses: [],
    currentPage: 1,
    lastPage: 1,
    search: '',
    isLoading: false,
    errorMessage: '',
    ratingError: '',
    async fetchCourses(page = 1) {
        this.isLoading = true;
        this.errorMessage = '';
        this.currentPage = page;
        try {
            const url = '{{ route('main.course.search') }}?search=' + encodeURIComponent(this.search || '') + '&page=' + page;
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            const data = await response.json();
            this.courses = (data.courses?.data || []).map(course => ({
                ...course,
                hoverRating: null
            }));
            this.currentPage = data.courses?.current_page || 1;
            this.lastPage = data.courses?.last_page || 1;
        } catch (error) {
            console.error('Fetch error:', error);
            this.errorMessage = 'Failed to load courses. Please try again.';
            this.courses = [];
        } finally {
            this.isLoading = false;
        }
    },
    getStarRating(rating, courseId) {
        const numRating = parseFloat(rating) || 0;
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            const isHalf = numRating >= i - 0.5 && numRating < i;
            const isFull = numRating >= i;
            stars += `<i 
                class='${isFull ? 'fas fa-star' : isHalf ? 'fas fa-star-half-alt' : 'far fa-star'}'
                @click='setRating(${courseId}, ${i - 0.5})'
                @mouseover='hoverStar(${courseId}, ${i - 0.5})'
                @mouseout='clearHover(${courseId})'
                x-bind:class='hoverRating[${courseId}] >= ${i - 0.5} ? \'hover-star\' : \'\''
            ></i>`;
        }
        return stars;
    },
    hoverStar(courseId, rating) {
        this.courses = this.courses.map(course =>
            course.id === courseId ? { ...course, hoverRating: rating } : course
        );
    },
    clearHover(courseId) {
        this.courses = this.courses.map(course =>
            course.id === courseId ? { ...course, hoverRating: null } : course
        );
    },
    async setRating(courseId, rating) {
        this.ratingError = '';
        try {
            const response = await fetch('{{ route('main.course.rate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ course_id: courseId, rating })
            });
            if (!response.ok) {
                throw new Error('Failed to update rating: ' + response.status);
            }
            const data = await response.json();
            this.courses = this.courses.map(course =>
                course.id === courseId ? { ...course, rating: rating.toString(), hoverRating: null } : course
            );
        } catch (error) {
            console.error('Rating error:', error);
            this.ratingError = 'Failed to update rating. Please try again.';
        }
    }
}" x-init="fetchCourses(1)">

    <header
        class="sticky top-0 left-0 w-full bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg z-20 transition-all duration-300 "
        data-header>
        @include('main.navbar')
    </header>
    <!-- Header Section -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white py-16">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="text-center md:text-left">
                    <h1 class="text-5xl font-extrabold tracking-tight animate-pulse-slow">
                        Discover Your Learning Journey
                    </h1>
                    <p class="mt-4 text-xl text-gray-200">
                        Unlock your potential with our diverse range of courses designed for success.
                    </p>
                </div>
                <div class="mt-6 md:mt-0 w-full md:w-auto">
                    <div class="relative">
                        <input x-model.debounce.500ms="search" @input.debounce.500ms="fetchCourses(1)"
                            placeholder="Search courses by title..."
                            class="search-input w-full md:w-80 px-6 py-3 rounded-full bg-white/90 border-2 border-white/30 text-black placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-purple-300/50 transition-all duration-300 shadow-lg hover:shadow-purple-200/50 focus:shadow-purple-300/50">
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-500 hover:text-purple-600 transition-colors duration-300"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" class="animate-pulse"
                                style="animation-duration: 2s;">
                                <animate attributeName="r" values="6;8;6" dur="3s" repeatCount="indefinite" />
                            </circle>
                            <path d="M21 21l-4.35-4.35" class="draw">
                                <animate attributeName="stroke-dashoffset" values="10;0" dur="0.5s" begin="0.5s"
                                    fill="freeze" />
                                <animate attributeName="opacity" values="0;1" dur="0.5s" begin="0.5s"
                                    fill="freeze" />
                            </path>
                        </svg>
                        <div
                            class="absolute right-14 top-1/2 transform -translate-y-1/2 flex space-x-1 opacity-0 search-dots">
                            <div class="h-2 w-2 bg-purple-500 rounded-full animate-bounce"
                                style="animation-delay: 0.1s"></div>
                            <div class="h-2 w-2 bg-purple-500 rounded-full animate-bounce"
                                style="animation-delay: 0.2s"></div>
                            <div class="h-2 w-2 bg-purple-500 rounded-full animate-bounce"
                                style="animation-delay: 0.3s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Error Message -->
        <div x-show="errorMessage" class="text-center py-8 text-red-600">
            <span x-text="errorMessage"></span>
        </div>
        <div x-show="ratingError" class="text-center py-8 text-red-600">
            <span x-text="ratingError"></span>
        </div>

        <!-- Loading Indicator -->
        <div x-show="isLoading" class="text-center py-8">
            <svg class="w-8 h-8 mx-auto animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-600">Loading courses...</span>
        </div>

        <!-- Course Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" x-show="!isLoading && !errorMessage">
            <template x-for="course in courses" :key="course.id">
                <div class="bg-white glassmorphic rounded-xl shadow-lg p-6 card-hover relative">
                    <span class="badge">Popular</span>
                    <img :src="course.image || 'https://via.placeholder.com/300x200'" alt="Course Image"
                        class="w-full h-48 object-cover rounded-lg mb-4 transition-transform duration-300 hover:scale-105">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="course.title"></h2>
                    <div class="text-gray-600 mb-4 truncate-3-lines" x-html="course.description"></div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-users text-blue-500 mr-2"></i>
                        <span class="text-gray-700" x-text="course.enroll + ' students'"></span>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-purple-500 mr-2"></i>
                        <span class="text-gray-700" x-text="course.duration"></span>
                    </div>
                    <div class="flex items-center mb-2 star-rating" x-html="getStarRating(course.rating, course.id)">
                    </div>
                    <div class="flex items-center mb-4">
                        <template x-if="course.footer_price">
                            <div class="flex items-center">
                                <span class="text-green-600 font-bold text-lg" x-text="'£' + course.price"></span>
                                <span class="text-gray-500 line-through ml-2" x-text="'£' + course.footer_price"></span>
                            </div>
                        </template>
                        <template x-if="!course.footer_price">
                            <span class="text-green-600 font-bold text-lg" x-text="'£' + course.price"></span>
                        </template>
                    </div>
                    <a :href="'/course-details/' + course.slug"
                        class="block text-center bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2 rounded-lg hover:bg-purple-700 transition-all duration-300 transform hover:scale-105">
                        View More
                    </a>
                </div>
            </template>
        </div>

        <div x-show="!isLoading && courses.length === 0 && !errorMessage" class="text-center text-gray-600 mt-8">
            No courses found matching your search.
        </div>

        <!-- Pagination Controls -->
        <div x-show="!isLoading && courses.length > 0 && !errorMessage"
            class="mt-8 flex justify-center items-center space-x-4">
            <button @click="fetchCourses(currentPage - 1)" :disabled="currentPage === 1"
                class="pagination-button px-4 py-2 bg-purple-500 text-white rounded-lg disabled:bg-gray-300">
                Previous
            </button>
            <template x-for="page in Array.from({ length: lastPage }, (_, i) => i + 1)" :key="page">
                <button @click="fetchCourses(page)" class="pagination-button px-4 py-2 rounded-lg"
                    :class="currentPage === page ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700'">
                    <span x-text="page"></span>
                </button>
            </template>
            <button @click="fetchCourses(currentPage + 1)" :disabled="currentPage === lastPage"
                class="pagination-button px-4 py-2 bg-purple-500 text-white rounded-lg disabled:bg-gray-300">
                Next
            </button>
        </div>
    </div>
    @include('main.footer')
</div>

{{-- @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/69ba9af9da.js" crossorigin="anonymous"></script>
@endsection --}}
