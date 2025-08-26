<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background: #faf5ff;
    }

    .container {
        background: #ffffff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #d8b4fe;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .hover-scale:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }

    .btn-purple {
        background: linear-gradient(to right, #9333ea, #7e22ce);
        color: #ffffff;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .search-results {
        background: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        width: 100%;
        z-index: 20;
        border: 1px solid #d8b4fe;
        display: none;
    }

    .search-results[x-show] {
        display: block !important;
    }

    .search-result-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .search-result-item:hover {
        background: #f3e8ff;
    }

    .search-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .message-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        padding: 1rem 2rem;
        border-radius: 0.5rem;
        font-size: 1.2rem;
        max-width: 90%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        color: #ffffff;
    }

    .message-container.success {
        background-color: #4CAF50;
    }

    .message-container.error {
        background-color: #f44336;
    }

    .form-container {
        background: #faf5ff;
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid #e9d5ff;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .input-field {
        background: #ffffff;
        border: 1px solid #d8b4fe;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        color: #4b5563;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(216, 180, 254, 0.3);
        outline: none;
        border-color: #9333ea;
    }

    .input-field[readonly] {
        background: #f3f4f6;
        cursor: not-allowed;
    }

    [x-cloak] {
        display: none !important;
    }
</style>

<div class="flex mx-4 items-center justify-center p-4 bg-gray-100">
    <div x-data="{
        learnerQuery: '',
        learnerResults: [],
        selectedLearner: null,
        numberOfCourses: 1,
        courseQueries: [''],
        courseResults: [],
        selectedCourses: [],
        errors: {},
        message: { text: '', type: 'success' },
        isSubmitting: false,
        async searchLearners() {
            if (!this.learnerQuery.trim() || this.learnerQuery.length < 2) {
                this.learnerResults = [];
                return;
            }
            try {
                const response = await fetch('{{ route('assign-course.search-learners') }}?q=' + encodeURIComponent(this.learnerQuery), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                console.log('Raw learner response:', data);
                this.learnerResults = Array.isArray(data) ? data : [];
                console.log('Learner results:', this.learnerResults);
                this.$nextTick(() => {
                    console.log('Learner dropdown should be visible, results:', this.learnerResults);
                    const dropdown = document.querySelector('.search-results');
                    console.log('Dropdown element:', dropdown, 'Style:', dropdown?.style.display);
                });
            } catch (err) {
                console.error('Learner search error:', err);
                this.showMessage('Error searching learners: ' + err.message, 'error');
                this.learnerResults = [];
            }
        },
        async selectLearner(learner) {
            this.learnerResults = [];
            this.selectedLearner = learner;
            this.learnerQuery = learner.name;
            this.numberOfCourses = 1;
            this.courseQueries = [''];
            this.courseResults = [];
            this.selectedCourses = [];
            this.updateCourseQueries();
        },
        updateCourseQueries() {
            const count = Math.max(1, parseInt(this.numberOfCourses) || 1);
            this.courseQueries = Array(count).fill('');
            this.courseResults = Array(count).fill([]);
            this.selectedCourses = Array(count).fill(null);
        },
        async searchCourses(index) {
            if (!this.courseQueries[index].trim() || !this.selectedLearner || this.courseQueries[index].length < 2) {
                this.courseResults[index] = [];
                return;
            }
            try {
                const response = await fetch('{{ route('assign-course.search-courses') }}?q=' + encodeURIComponent(this.courseQueries[index]), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                console.log('Raw course response:', data);
                this.courseResults[index] = Array.isArray(data) ? data : [];
                console.log('Course results for index', index, ':', this.courseResults[index]);
            } catch (err) {
                console.error('Course search error:', err);
                this.showMessage('Error searching courses: ' + err.message, 'error');
                this.courseResults[index] = [];
            }
        },
        selectCourse(index, course) {
            this.courseResults[index] = [];
            this.courseQueries[index] = course.title;
            this.selectedCourses[index] = course;
        },
        async submitAssignment() {
            if (!this.selectedLearner) {
                this.showMessage('Please select a learner.', 'error');
                return;
            }
            if (this.selectedCourses.some(course => !course)) {
                this.showMessage('Please select a course for all inputs.', 'error');
                return;
            }
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const formData = new FormData();
            formData.append('learner_secret_id', this.selectedLearner.secret_id);
            formData.append('name', this.selectedLearner.name);
            formData.append('email', this.selectedLearner.email);
            this.selectedCourses.forEach((course, index) => {
                if (course) {
                    formData.append(`courses[${index}][course_unique_id]`, course.unique_id);
                    formData.append(`courses[${index}][course_title]`, course.title);
                }
            });
            try {
                const response = await fetch('{{ route('assign-course.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: formData
                });
                const data = await response.json();
                if (response.ok) {
                    this.showMessage(data.message || 'Courses assigned successfully!', 'success');
                    this.selectedLearner = null;
                    this.learnerQuery = '';
                    this.numberOfCourses = 1;
                    this.courseQueries = [''];
                    this.courseResults = [];
                    this.selectedCourses = [];
                    this.updateCourseQueries();
                } else {
                    this.showMessage(data.message || 'Failed to assign courses.', 'error');
                }
            } catch (err) {
                console.error('Assignment error:', err);
                this.showMessage('Error assigning courses: ' + err.message, 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        showMessage(text, type = 'success', duration = 5000) {
            this.message.text = text;
            this.message.type = type;
            if (duration > 0) {
                setTimeout(() => { this.message.text = ''; }, duration);
            }
        }
    }" class="container-fluid w-full fade-in">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-purple-800 mt-4">Assign Course to Learner</h1>
            <p class="text-gray-600 text-sm mt-2">Search for a learner and assign multiple courses</p>
        </div>

        <!-- Message Alert -->
        <div x-show="message.text" x-transition
             class="message-container fade-in"
             :class="{ 'success': message.type === 'success', 'error': message.type === 'error' }"
             x-text="message.text">
        </div>

        <!-- Learner Search -->
        <div class="search-container">
            <div class="relative">
                <input type="text" x-model="learnerQuery" placeholder="Search by learner name, email, or ID"
                       class="input-field w-full p-2 mt-1"
                       @input.debounce.500ms="searchLearners"
                       @keydown.escape="learnerResults = []">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div x-show="learnerResults.length" class="search-results mt-2" x-transition>
                <template x-for="learner in learnerResults" :key="learner.secret_id">
                    <div class="search-result-item" @click="selectLearner(learner)">
                        <p class="text-gray-800 text-sm">
                            <span x-text="learner.name"></span> (<span x-text="learner.email"></span>) - ID: <span x-text="learner.secret_id"></span>
                        </p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Number of Courses and Course Search -->
        <template x-if="selectedLearner">
            <div class="form-container fade-in">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-purple-700">Number of Courses to Assign</label>
                    <input type="number" x-model="numberOfCourses" min="1" max="10"
                           class="input-field w-full p-2 mt-1"
                           @input="updateCourseQueries">
                </div>

                <template x-for="(query, index) in courseQueries" :key="index">
                    <div class="search-container mb-4">
                        <div class="relative">
                            <input type="text" x-model="courseQueries[index]" placeholder="Search by course title or ID"
                                   class="input-field w-full p-2 mt-1"
                                   @input.debounce.500ms="searchCourses(index)"
                                   @keydown.escape="courseResults[index] = []">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div x-show="courseResults[index] && courseResults[index].length" class="search-results mt-2" x-transition>
                            <template x-for="course in courseResults[index]" :key="course.unique_id">
                                <div class="search-result-item" @click="selectCourse(index, course)">
                                    <p class="text-gray-800 text-sm">
                                        <span x-text="course.title"></span> (ID: <span x-text="course.unique_id"></span>)
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Assignment Form -->
        <template x-if="selectedLearner && selectedCourses.some(course => course)">
            <div class="form-container fade-in">
                <form @submit.prevent="submitAssignment" class="space-y-6">
                    <div class="pt-4">
                        <button type="submit"
                                class="btn-purple w-full cursor-pointer hover:scale-95"
                                :class="{ 'opacity-50 cursor-not-allowed': isSubmitting || selectedCourses.some(course => !course) }"
                                :disabled="isSubmitting || selectedCourses.some(course => !course)">
                            <div class="flex items-center justify-center">
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSubmitting ? 'Assigning...' : 'Assign Courses'"></span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>
</div>

