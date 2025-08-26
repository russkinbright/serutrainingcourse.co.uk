<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-auto mx-auto bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="removeSectionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-unlink text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Remove Sections from Course
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

            <!-- Connected Sections -->
            <div x-show="selectedCourse" class="mb-8 p-6 bg-gray-50 rounded-xl shadow-inner border border-indigo-200/50">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Connected Sections</h2>
                <div x-show="isFetchingSections" class="flex items-center mb-4">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Fetching sections...</span>
                </div>
                <div x-show="!isFetchingSections && connectedSections.length === 0" class="text-gray-600">
                    No sections connected to this course.
                </div>
                <ul x-show="!isFetchingSections && connectedSections.length > 0" class="space-y-2">
                    <template x-for="section in connectedSections" :key="section.id">
                        <li class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm">
                            <span><strong x-text="section.name"></strong> (ID: <span x-text="section.unique_id"></span>)</span>
                            <button @click="openRemoveModal(section)"
                                class="px-3 py-1 bg-red-600 text-white font-bold rounded-lg transition-all duration-500 hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i>Remove
                            </button>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Confirmation Modal -->
            <div x-show="showRemoveModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl border border-indigo-200/50">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Confirm Removal</h3>
                    <p class="text-gray-700 mb-6">
                        Are you sure you want to remove <strong x-text="sectionToRemove?.name"></strong> (ID: <span x-text="sectionToRemove?.unique_id"></span>) from this course?
                    </p>
                    <div class="flex justify-end space-x-4">
                        <button @click="showRemoveModal = false"
                            class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition-all duration-300">
                            Cancel
                        </button>
                        <button @click="confirmRemoveSection()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-trash mr-2"></i>Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('removeSectionManager', () => ({
            courseSearchQuery: '',
            courseSearchResults: [],
            isCourseSearching: false,
            selectedCourse: null,
            connectedSections: [],
            isFetchingSections: false,
            showRemoveModal: false,
            sectionToRemove: null,
            formMessage: '',
            formStatus: '',
            init() {
                console.log('RemoveSectionManager initialized');
            },
            searchCourses() {
                if (!this.courseSearchQuery.trim()) {
                    this.courseSearchResults = [];
                    this.isCourseSearching = false;
                    return;
                }
                this.isCourseSearching = true;
                const url = '{{ route('section.remove.searchCourses') }}?query=' + encodeURIComponent(this.courseSearchQuery.trim());
                console.log('Searching courses:', url);
                fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        console.log('Course search response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.courseSearchResults = data;
                        console.log('Course search results:', data);
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
                this.formMessage = '';
                this.formStatus = '';
                this.fetchSections();
            },
            clearCourse() {
                this.selectedCourse = null;
                this.courseSearchQuery = '';
                this.courseSearchResults = [];
                this.connectedSections = [];
                this.showRemoveModal = false;
                this.sectionToRemove = null;
            },
            fetchSections() {
                if (!this.selectedCourse) return;
                this.isFetchingSections = true;
                const url = '{{ route('section.remove.getSections', ['course_unique_id' => ':course_unique_id']) }}'.replace(':course_unique_id', this.selectedCourse.unique_id);
                console.log('Fetching sections from:', url);
                fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        console.log('Sections fetch response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.errors) {
                            console.error('Backend error:', data.errors);
                            this.formStatus = 'error';
                            this.formMessage = data.errors.join(' ');
                            this.connectedSections = [];
                        } else {
                            this.connectedSections = data;
                            console.log('Fetched sections:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching sections:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to fetch sections. Please try again.';
                        this.connectedSections = [];
                    })
                    .finally(() => {
                        this.isFetchingSections = false;
                    });
            },
            openRemoveModal(section) {
                this.sectionToRemove = section;
                this.showRemoveModal = true;
                console.log('Opening modal for section:', section);
            },
            confirmRemoveSection() {
                if (!this.selectedCourse || !this.sectionToRemove) return;
                const url = '{{ route('section.remove.delete', ['course_unique_id' => ':course_unique_id', 'section_unique_id' => ':section_unique_id']) }}'
                    .replace(':course_unique_id', this.selectedCourse.unique_id)
                    .replace(':section_unique_id', this.sectionToRemove.unique_id);
                console.log('Removing section via:', url);
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        console.log('Remove section response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.formStatus = 'success';
                        this.formMessage = data.message;
                        this.connectedSections = this.connectedSections.filter(s => s.id !== this.sectionToRemove.id);
                        this.showRemoveModal = false;
                        this.sectionToRemove = null;
                        console.log('Section removed successfully');
                    })
                    .catch(error => {
                        console.error('Error removing section:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to remove section. Please try again.';
                        this.showRemoveModal = false;
                    });
            }
        }));
    });
</script>
