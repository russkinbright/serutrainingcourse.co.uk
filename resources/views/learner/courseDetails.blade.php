<div class="mx-auto px-4 py-6">
    <!-- Course Header -->
    <div
        class="bg-gradient-to-r from-purple-800 to-purple-600 text-white shadow-xl rounded-2xl p-8 mb-8 transform transition hover:shadow-2xl">
        <div class="flex items-center">
            <button onclick="showPanel('learnerCoursePanel')"
                class="flex items-center bg-gradient-to-r from-purple-500 to-indigo-600 text-purple-100 border-4 border-white rounded-lg w-8 h-8 justify-center mr-4 transition-transform hover:scale-110 hover:shadow-md">
                ←
            </button>
            <h1 class="text-4xl font-extrabold tracking-tight" x-text="selectedCourse?.title"></h1>
        </div>
        <p class="mt-2 text-sm opacity-80 mx-12"
            x-text="'Category: ' + selectedCourse?.category + ' | Level: ' + selectedCourse?.level"></p>
    </div>


    <!-- Navigation Tabs -->
    <div x-data="{ tab: 'overview' }">
        <div class="flex space-x-6 border-b-2 border-purple-200 mb-6 bg-white rounded-t-xl p-4">
            <button @click="tab = 'overview'"
                :class="tab === 'overview' ? 'border-purple-700 text-purple-700 font-bold bg-purple-100' :
                    'text-gray-500 hover:text-purple-600'"
                class="pb-3 px-6 border-b-4 transition-all duration-300 rounded-t-lg">
                Overview
            </button>
            <button @click="tab = 'curriculum'"
                :class="tab === 'curriculum' ? 'border-purple-700 text-purple-700 font-bold bg-purple-100' :
                    'text-gray-500 hover:text-purple-600'"
                class="pb-3 px-6 border-b-4 transition-all duration-300 rounded-t-lg">
                Curriculum
            </button>
        </div>

        <!-- Tab Contents -->
        <div x-show="tab === 'overview'" class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-purple-800 mb-4">Course Overview</h2>
            <p class="text-gray-700 leading-relaxed" x-html="selectedCourseDetails?.description"></p>
        </div>

        <div x-show="tab === 'curriculum'" class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-purple-800 mb-6">Course Curriculum</h2>

            <template x-for="module in selectedModules" :key="module.unique_id">
                <div x-data="{ open: false }" class="border border-purple-100 rounded-lg overflow-hidden mb-4">
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4 flex justify-between items-center cursor-pointer"
                        @click="open = !open">
                        <div class="flex items-center">
                            <i class="fas fa-bookmark text-purple-500 mr-3"></i>
                            <h4 class="font-semibold text-gray-800" x-text="module?.name"></h4>
                        </div>
                        <i class="fas fa-chevron-down text-purple-500 transition-transform duration-300"
                            :class="{ 'rotate-180': open }"></i>
                    </div>

                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="px-6 py-4 bg-white border-t border-purple-100">
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-700 flex items-center">
                                    <i class="fas fa-info-circle text-purple-400 mr-2"></i>
                                    <span x-text="`Module — ${module.name}`"></span>
                                </h4>
                                <span class="text-sm text-purple-600 bg-purple-100 font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-clock text-purple-400 mr-1"></i>
                                    <span x-text="`${module.time} min`"></span>
                                </span>
                            </div>
                        </div>

                        <template x-if="module.quizzes.length > 0">
                            <ul class="space-y-3">
                                <template x-for="quiz in module.quizzes" :key="quiz.unique_id">
                                    <li
                                        class="flex justify-between items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                                        <div class="flex items-center">
                                            <i class="fas fa-question-circle text-purple-400 mr-3"></i>
                                            <span class="font-medium text-gray-700">Quiz — </span>
                                            <span class="text-gray-600 mx-2 font-medium" x-text="quiz?.title"></span>
                                        </div>
                                        <span
                                            class="text-sm text-purple-600 bg-purple-100 font-bold px-3 py-1 rounded-full">
                                            <i class="fas fa-clock text-purple-400 ml-2"></i> 10 min
                                        </span>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="module.quizzes.length === 0">
                            <p class="text-sm text-gray-500 italic">No quizzes found for this module.</p>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function learnerApp() {
        return {
            courses: [],
            selectedCourse: null,
            selectedCourseDetails: null,
            selectedModules: [],
            error: '',

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
                        this.courses = data.courses;
                    } else {
                        this.error = data.message || 'Unable to load courses.';
                    }
                } catch (err) {
                    this.error = 'Something went wrong.';
                }
            },

            async showCourseDetails(unique_id) {
                try {
                    const res = await fetch(`/api/learner/course/${unique_id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.selectedCourse = data.course;
                        this.selectedCourseDetails = data.course_details;
                        this.selectedModules = data.modules;

                        document.getElementById('learnerCoursePanel').classList.add('hidden');
                        document.getElementById('learnerCourseDetails').classList.remove('hidden');
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        this.error = data.message || 'Course not found.';
                    }
                } catch (err) {
                    this.error = 'Failed to load course details.';
                }
            }
        };
    }
</script>
