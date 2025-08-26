<style>
    .continue-button:hover .svg-icon {
        transform: translateX(5px);
        transition: transform 0.3s ease;
    }
</style>
<div class="container-fluid" x-data="{
    courses: [],
    error: '',
    showModal: false,
    showExpireModal: false,
    selectedCourseId: null,
    expiredToday: false,

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
        if (this.selectedCourseId) {
            window.location.href = '/learner/final-mock';
        }
        this.closeModal();
    },

    showCourseDetails(unique_id) {
        window.location.href = '/learner/course-details/' + unique_id;
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
    }
}" x-init="fetchCourses()">
    <div class="flex-1 p-6 gradient-bg">
        <!-- My Courses Section -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">My Courses</h2>
                <!-- <a href="#" class="text-purple-200 hover:text-purple-100 transition">View All</a> -->
            </div>
            <div class="bg-white/90 rounded-2xl shadow-xl overflow-hidden border border-purple-100">
                <table class="w-full border-collapse">
                    <!-- Table Header -->
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700">
                            <th class="p-4 text-left font-semibold">Course Name</th>
                            <th class="p-4 text-center font-semibold">Expire At</th>
                            <th class="p-4 text-center font-semibold">Action</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        <template x-for="course in courses" :key="course.unique_id">
                            <tr class="border-t border-purple-200 hover:bg-purple-50/50 transition-colors duration-300">
                                <!-- Course Name -->
                                <td class="p-4 align-middle">
                                    <h2 class="text-lg font-semibold text-purple-800 line-clamp-2"
                                        x-text="course.title"></h2>
                                </td>

                                <!-- Expire Date -->
                                <td class="p-4 align-middle text-center">
                                    <div
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-md border border-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                                        <span x-text="course.expireFormatted"></span>
                                    </div>
                                </td>

                                <!-- Action Button -->
                                <td class="p-4 align-middle text-center">
                                    <button @click="openModal(course.unique_id)"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-md border border-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                                        Continue
                                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-300 mb-2">
                        Practice Mode</h2>
                    <p class="text-white font-semibold text-sm">Choose how you want to practice for your SERU training
                    </p>
                </div>

                <div class="flex flex-col space-y-4">
                    <button @click="navigateToSectionTest"
                        class="group flex items-center justify-between px-6 py-4 bg-gradient-to-r from-cyan-600/80 to-blue-600/80 hover:from-cyan-500 hover:to-blue-500 text-white font-semibold rounded-xl shadow-lg 
                                hover:shadow-cyan-500/20 transition-all duration-300 cursor-pointer border border-cyan-400/20 relative overflow-hidden">
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

                    <button @click="navigateToFinalMock"
                        class="group flex items-center justify-between px-6 py-4 bg-gradient-to-r from-indigo-600/80 to-purple-600/80 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-xl shadow-lg 
                                hover:shadow-purple-500/20 transition-all duration-300 cursor-pointer border border-indigo-400/20 relative overflow-hidden">
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
                    <p class="text-xs text-white font-semibold">SERU Training Course - Master your knowledge</p>
                </div>
            </div>
        </div>
    </div>
    <div x-show="showExpireModal" x-cloak
        class="fixed inset-0 backdrop-blur-sm bg-black/50 flex items-center justify-center z-50 p-4"
        @click="closeModal">
        <div @click.stop
            class="relative bg-white p-8 rounded-xl shadow-2xl max-w-xl w-full border border-purple-700 overflow-hidden">

            <!-- Decorative glowing blobs -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-500/20 rounded-full filter blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-500/20 rounded-full filter blur-2xl"></div>

            <div class="relative z-10 text-center space-y-5">
                <!-- Icon -->
                <div
                    class="mx-auto w-16 h-16 bg-purple-500/30 rounded-full border-purple-700 border-2 flex items-center justify-center shadow-lg shadow-purple-500/20">
                    <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Heading -->
                <div class="space-y-2 mb-3">
                    <h2 class="text-2xl font-bold text-rose-600">
                        Course Access Expired
                    </h2>
                    <p class="text-black font-bold">Continue your SERU training by renewing your access</p>
                </div>

                <!-- Button -->
                <button @click="confirmExpire"
                    class="w-full px-6 py-4 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white rounded-xl font-semibold text-lg transition-all duration-300 shadow-lg hover:shadow-purple-500/30 group transform hover:scale-[1.02]">
                    <span class="relative flex items-center justify-center space-x-2">
                        <span>Buy Again</span>
                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
