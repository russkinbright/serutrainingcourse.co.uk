<div x-data="{
    courses: [],
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
    continueCourse(unique_id) {
        window.location.href = '/learner/course/' + unique_id;
    },
    showCourseDetails(unique_id) {
        window.location.href = '/learner/course-details/' + unique_id;
    },
    showCourseCertificate(unique_id) {
        window.location.href = '/learner/certificate/' + unique_id;
    }
}"  class="container-fluid mx-auto p-4">
    <template x-if="error">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4" role="alert">
            <p x-text="error"></p>
        </div>
    </template>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="course in courses" :key="course.unique_id">
            <div
                class="relative bg-gradient-to-br from-purple-700 to-purple-900 text-white rounded-xl shadow-lg overflow-hidden transform transition duration-300 hover:shadow-2xl hover:scale-105">
                <!-- Card Header with Title -->
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-3 line-clamp-2" x-text="course.title"></h2>

                    <!-- Progress Bar -->
                    <div class="w-full bg-purple-200 rounded-full h-2.5 mb-2">
                        <div class="bg-purple-400 h-2.5 rounded-full transition-all duration-500"
                             :style="`width: ${course.progress}%`"></div>
                    </div>
                    <p class="text-sm text-purple-100" x-text="`${course.progress}% Complete`"></p>
                </div>

                <!-- Card Footer with Buttons -->
                <div class="p-4 bg-purple-800 bg-opacity-50 flex justify-between space-x-3">
                    <div>
                        <button @click="continueCourse(course.unique_id)"
                                class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                            Continue Course
                        </button>
                        <button @click="showCourseDetails(course.unique_id)"
                                class="bg-transparent border border-purple-400 hover:bg-purple-400 hover:text-purple-900 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                            View Course
                        </button>
                    </div>
                    <template x-if="course.is_completed">
                        <button @click="showCourseCertificate(course.unique_id)"
                                class="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold py-2 px-4 rounded-lg transition duration-300">
                            Documents
                        </button>
                    </template>
                </div>

                <!-- Decorative Element -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500 opacity-20 rounded-full -mr-12 -mt-12"></div>
            </div>
        </template>
    </div>
</div>

