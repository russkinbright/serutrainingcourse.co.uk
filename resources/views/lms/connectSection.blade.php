<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-auto mx-auto bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="connectSectionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-link text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Connect Sections to Course
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Course Search -->
            <div class="mb-8 relative">
                <label for="courseSearch" class="block text-xl font-bold text-gray-900 mb-3">
                    Search Course
                </label>
                <input type="text" id="courseSearch" x-model.debounce.500="courseSearchQuery" @input="searchCourses()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                    placeholder="Search by title or unique ID">
                <div x-show="isCourseSearching" class="mt-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Searching courses...</span>
                </div>
                <div x-show="courseSearchResults.length > 0"
                    class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                    <ul class="max-h-60 overflow-y-auto">
                        <template x-for="course in courseSearchResults" :key="course.id">
                            <li @click="selectCourse(course)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                <span x-text="course.title"></span> (ID: <span x-text="course.unique_id"></span>)
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Selected Course -->
            <div x-show="selectedCourse"
                class="mb-8 p-6 bg-gray-50 rounded-xl shadow-inner border border-indigo-200/50">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Selected Course</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Title:</strong> <span x-text="selectedCourse.title"></span></p>
                        <p><strong>Unique ID:</strong> <span x-text="selectedCourse.unique_id"></span></p>
                        <p><strong>Price:</strong> $<span x-text="selectedCourse.price"></span></p>
                        <p><strong>Rating:</strong> <span x-text="selectedCourse.rating"></span>/5</p>
                    </div>
                    <div>
                        <p><strong>Total Students:</strong> <span x-text="selectedCourse.total_student"></span></p>
                        <p><strong>Duration:</strong> <span x-text="selectedCourse.week"></span> Week(s)</p>
                        <p><strong>Footer Price:</strong> $<span x-text="selectedCourse.footer_price"></span></p>
                    </div>
                </div>
                <div x-show="selectedCourse.image" class="mt-4">
                    <img :src="selectedCourse.image" alt="Course Image"
                        class="w-full h-48 object-cover rounded-xl border-2 border-amber-200/50">
                </div>
                <button @click="clearCourse()"
                    class="mt-4 px-4 py-2 bg-red-600 text-white font-bold rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Clear Course
                </button>
            </div>

              <!-- Selected Sections -->
            <div x-show="selectedSections.length > 0"
                class="mb-8 p-6 bg-gray-50 rounded-xl shadow-inner border border-indigo-200/50">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Selected Sections</h2>
                <ul class="space-y-2">
                    <template x-for="section in selectedSections" :key="section.id">
                        <li class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
                            <span><strong x-text="section.name"></strong> (ID: <span x-text="section.unique_id"></span>)</span>
                            <button @click="removeSection(section.id)"
                                class="px-3 py-1 bg-red-600 text-white font-bold rounded-lg transition-all duration-500 hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i>Unselect
                            </button>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Section Search -->
            <div x-show="selectedCourse" class="mb-8 relative">
                <label for="sectionSearch" class="block text-xl font-bold text-gray-900 mb-3">
                    Search Sections
                </label>
                <input type="text" id="sectionSearch" x-model.debounce.500="sectionSearchQuery"
                    @input="searchSections()" @change="searchSections()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                    placeholder="Search by section name or unique ID">
                <div x-show="isSectionSearching" class="mt-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Searching sections...</span>
                </div>
                <div x-show="sectionSearchResults.length > 0"
                    class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                    <ul class="max-h-60 overflow-y-auto">
                        <template x-for="section in sectionSearchResults" :key="section.id">
                            <li @click="toggleSection(section)"
                                class="px-4 py-3 cursor-pointer transition-all duration-200 flex items-center justify-between"
                                :class="selectedSections.some(s => s.id === section.id) ? 'bg-cyan-600 text-white font-semibold border-l-4 border-cyan-800' : 'hover:bg-cyan-100 text-gray-900'">
                                <span>
                                    <span x-text="section.name"></span> (ID: <span x-text="section.unique_id"></span>)
                                </span>
                                <i x-show="selectedSections.some(s => s.id === section.id)" class="fas fa-check-circle text-white ml-2"></i>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

          

            <!-- Submit Button -->
            <div x-show="selectedCourse && selectedSections.length > 0" class="mb-8">
                <button @click.prevent="submitConnections()"
                    :disabled="isSubmitting"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="isSubmitting ? 'bg-gray-400 cursor-not-allowed' :
                    'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <template x-if="isSubmitting">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Connecting Sections...</span>
                        </div>
                    </template>
                    <template x-if="!isSubmitting">
                        <div class="flex items-center">
                            <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                            <span>Connect Sections</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('connectSectionManager', () => ({
            courseSearchQuery: '',
            courseSearchResults: [],
            isCourseSearching: false,
            selectedCourse: null,
            sectionSearchQuery: '',
            sectionSearchResults: [],
            isSectionSearching: false,
            selectedSections: [],
            isSubmitting: false,
            formMessage: '',
            formStatus: '',
            init() {
                // Initialize component
            },
            searchCourses() {
                if (!this.courseSearchQuery.trim()) {
                    this.courseSearchResults = [];
                    this.isCourseSearching = false;
                    return;
                }
                this.isCourseSearching = true;
                fetch('{{ route('section.connect.searchCourses') }}?query=' + encodeURIComponent(this.courseSearchQuery.trim()), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.courseSearchResults = data;
                    })
                    .catch(error => {
                        console.error('Error searching courses:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to search courses. Please try again.';
                    })
                    .finally(() => {
                        this.isCourseSearching = false;
                    });
            },
            selectCourse(course) {
                this.selectedCourse = course;
                this.courseSearchQuery = '';
                this.courseSearchResults = [];
                this.sectionSearchQuery = '';
                this.sectionSearchResults = [];
                this.selectedSections = [];
                this.formMessage = '';
                this.formStatus = '';
            },
            clearCourse() {
                this.selectedCourse = null;
                this.courseSearchQuery = '';
                this.courseSearchResults = [];
                this.sectionSearchQuery = '';
                this.sectionSearchResults = [];
                this.selectedSections = [];
            },
            searchSections() {
                if (!this.sectionSearchQuery.trim()) {
                    this.sectionSearchResults = [];
                    this.isSectionSearching = false;
                    return;
                }
                this.isSectionSearching = true;
                fetch('{{ route('section.connect.searchSections') }}?query=' + encodeURIComponent(this.sectionSearchQuery.trim()), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.sectionSearchResults = data;
                    })
                    .catch(error => {
                        console.error('Error searching sections:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to search sections. Please try again.';
                    })
                    .finally(() => {
                        this.isSectionSearching = false;
                    });
            },
            toggleSection(section) {
                const index = this.selectedSections.findIndex(s => s.id === section.id);
                if (index === -1) {
                    this.selectedSections.push(section);
                } else {
                    this.selectedSections.splice(index, 1);
                }
            },
            removeSection(sectionId) {
                this.selectedSections = this.selectedSections.filter(s => s.id !== sectionId);
            },
            submitConnections() {
                if (!this.selectedCourse || this.selectedSections.length === 0) {
                    this.formStatus = 'error';
                    this.formMessage = 'Please select a course and at least one section.';
                    return;
                }
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';

                fetch('{{ route('section.connect.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        course_unique_id: this.selectedCourse.unique_id,
                        section_unique_ids: this.selectedSections.map(s => s.unique_id)
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.message) {
                            this.formStatus = 'success';
                            this.formMessage = data.message;
                            this.selectedCourse = null;
                            this.courseSearchQuery = '';
                            this.courseSearchResults = [];
                            this.sectionSearchQuery = '';
                            this.sectionSearchResults = [];
                            this.selectedSections = [];
                        } else if (data.errors) {
                            this.formStatus = 'error';
                            this.formMessage = Object.values(data.errors).flat().join(' ');
                        }
                    })
                    .catch(error => {
                        console.error('Error connecting sections:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to connect sections. Please try again.';
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
            }
        }));
    });
</script>
