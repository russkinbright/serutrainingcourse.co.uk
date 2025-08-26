<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="editMockSecondQuestionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-edit text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Edit Mock Test 2 Questions
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Section Search -->
            <div class="mb-8 relative">
                <label for="sectionSearch" class="block text-xl font-bold text-gray-900 mb-3">
                    Select Section
                </label>
                <input type="text" id="sectionSearch" x-model="sectionSearch"
                    @input.debounce.500="searchSections()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500"
                    placeholder="Search section by tag, name, or ID">
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
                        <template x-for="section in sectionSearchResults" :key="section.unique_id">
                            <li @click="selectSection(section.unique_id, section.tag, section.name)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                              <span x-html="'Section ' + section.tag + ' - <span class=\'text-purple-700 font-semibold\'>' + section.name + '</span> (ID: ' + section.unique_id + ')'"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div x-show="!isSectionSearching && sectionSearch && sectionSearchResults.length === 0"
                    class="mt-2 text-gray-600">
                    No sections found.
                </div>
                <div x-show="selectedSection" class="mt-2 text-gray-700">
                    Selected Section: <span x-text="selectedSection"></span>
                </div>
            </div>

            <!-- Delete All Button -->
            <div x-show="selectedSection" class="mb-8">
                <button @click="deleteAllQuestions()" :disabled="isDeletingAll"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 hover:ring-4 hover:ring-red-400/50">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <template x-if="isDeletingAll">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Deleting All...</span>
                        </div>
                    </template>
                    <template x-if="!isDeletingAll">
                        <div class="flex items-center">
                            <i class="fas fa-trash-alt mr-3 animate-pulse text-amber-300"></i>
                            <span>Delete All Questions</span>
                        </div>
                    </template>
                </button>
            </div>

            <!-- Question List -->
            <div x-show="questionFields.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Questions (<span x-text="questionFields.length"></span>)
                </h2>
                <div class="grid grid-cols-1 gap-4">
                    <template x-for="(field, index) in questionFields" :key="field.unique_id">
                        <div class="p-4 bg-white/80 rounded-xl shadow-md border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Question <span x-text="index + 1"></span> (<span
                                        x-text="field.type.charAt(0).toUpperCase() + field.type.slice(1)"></span>)
                                </h3>
                                <div class="flex space-x-2">
                                    <button @click="openEditModal(index)"
                                        class="px-3 py-2 text-white bg-purple-900 hover:bg-purple-700 rounded-xl flex items-center">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </button>
                                    <button @click="openDeleteModal(field.unique_id, index)"
                                        class="px-3 py-2 text-white bg-gradient-to-r from-red-500 to-red-600 rounded-lg hover:from-red-600 hover:to-red-700 flex items-center">
                                        <i class="fas fa-trash-alt mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                            <p class="text-gray-700 mb-2" x-text="field.question_text"></p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div>
                                    <span class="font-semibold text-purple-900">A:</span>
                                    <span x-text="field.option_a"></span>
                                </div>
                                <div>
                                    <span class="font-semibold text-purple-900">B:</span>
                                    <span x-text="field.option_b"></span>
                                </div>
                                <div>
                                    <span class="font-semibold text-purple-900">C:</span>
                                    <span x-text="field.option_c"></span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="font-semibold text-purple-900">Answer(s):</span>
                                <span x-text="[field.answer_1, field.answer_2].filter(Boolean).join(', ')"></span>
                            </div>
                            <div class="mt-2">
                                <span class="font-semibold text-purple-900">Incorrect Explanation:</span>
                                <span x-text="field.incorrect"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Edit Modal -->
            <div x-show="showEditModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-xl p-6 w-full max-w-2xl">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Edit Question</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-lg font-semibold text-gray-900">Question Type</label>
                            <select x-model="editForm.type" @change="updateAnswerValidation()"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50">
                                <option value="single">Single Answer</option>
                                <option value="double">Double Answer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-900">Question Text</label>
                            <textarea x-model="editForm.question_text"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50"
                                rows="4"></textarea>
                        </div>
                        <template x-for="option in ['a', 'b', 'c']" :key="option">
                            <div>
                                <label class="block text-lg font-semibold text-gray-900"
                                    x-text="'Option ' + option.toUpperCase()"></label>
                                <input x-model="editForm['option_' + option]"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50"
                                    type="text">
                            </div>
                        </template>
                        <div>
                            <label class="block text-lg font-semibold text-gray-900">Incorrect Explanation</label>
                            <textarea x-model="editForm.incorrect"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50"
                                rows="3"></textarea>
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-900">Answer(s)</label>
                            <div class="grid grid-cols-3 gap-4">
                                <template x-for="opt in ['a', 'b', 'c']" :key="opt">
                                    <label class="flex items-center">
                                        <input type="checkbox" :value="opt"
                                            @change="updateEditAnswers(opt)"
                                            :checked="editForm.answers.includes(opt)"
                                            class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                        <span x-text="opt.toUpperCase()"></span>
                                    </label>
                                </template>
                            </div>
                            <div x-show="!isValidAnswerCount" class="mt-2 text-red-600">
                                <span
                                    x-text="editForm.type === 'single' ? 'Please select exactly one answer.' : 'Please select exactly two answers.'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button @click="showEditModal = false"
                            class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="updateQuestion()" :disabled="isSubmitting || !isValidAnswerCount"
                            class="px-4 py-2 text-white bg-purple-900 hover:bg-purple-700 rounded-xl flex items-center"
                            :class="{ 'bg-gray-400 cursor-not-allowed': isSubmitting || !isValidAnswerCount }">
                            <template x-if="isSubmitting">
                                <svg class="w-5 h-5 mr-2 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </template>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-xl p-6 w-full max-w-md">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Confirm Deletion</h2>
                    <p class="text-gray-700 mb-4">Are you sure you want to delete this question?</p>
                    <div class="flex justify-end space-x-4">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="confirmDelete()" :disabled="isSubmitting"
                            class="px-4 py-2 text-white bg-gradient-to-r from-red-500 to-red-600 rounded-lg hover:from-red-600 hover:to-red-700 flex items-center">
                            <template x-if="isSubmitting">
                                <svg class="w-5 h-5 mr-2 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </template>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('editMockSecondQuestionManager', () => ({
            sectionSearch: '',
            sectionSearchResults: [],
            isSectionSearching: false,
            section_unique_id: '',
            selectedSection: '',
            questionFields: [],
            isLoading: false,
            isSubmitting: false,
            isDeletingAll: false,
            formMessage: '',
            formStatus: '',
            showEditModal: false,
            showDeleteModal: false,
            deleteUniqueId: '',
            deleteIndex: null,
            editForm: {
                unique_id: '',
                type: 'single',
                question_text: '',
                option_a: '',
                option_b: '',
                option_c: '',
                incorrect: '',
                answers: [],
                answer_1: '',
                answer_2: ''
            },
            editIndex: null,
            isValidAnswerCount: false,
            init() {
                this.updateAnswerValidation();
                console.log('EditMockSecondQuestionManager initialized');
            },
            updateAnswerValidation() {
                this.isValidAnswerCount = this.editForm.type === 'single' ?
                    this.editForm.answers.length === 1 :
                    this.editForm.answers.length === 2;
            },
            async searchSections() {
                const query = this.sectionSearch.trim();
                if (!query) {
                    this.sectionSearchResults = [];
                    this.isSectionSearching = false;
                    return;
                }
                this.isSectionSearching = true;
                const url = '{{ route('editmocksecondquestion.search') }}?query=' + encodeURIComponent(query);
                console.log('Searching sections:', url);
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    console.log('Section search response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    this.sectionSearchResults = await response.json();
                    console.log('Section search results:', this.sectionSearchResults);
                } catch (error) {
                    console.error('Error searching sections:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to search sections. Please try again.';
                    this.sectionSearchResults = [];
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } finally {
                    this.isSectionSearching = false;
                }
            },
            async selectSection(unique_id, tag, name) {
                this.section_unique_id = unique_id;
                this.selectedSection = `Section ${tag} - ${name} (ID: ${unique_id}) - Mock Test-2`;
                this.sectionSearch = '';
                this.sectionSearchResults = [];
                this.questionFields = [];
                console.log('Selected section:', { unique_id, tag, name });
                await this.loadQuestions();
            },
            async loadQuestions() {
                if (!this.section_unique_id) return;
                this.isLoading = true;
                const url = '{{ route('editmocksecondquestion.questions') }}?section_unique_id=' +
                    encodeURIComponent(this.section_unique_id);
                console.log('Fetching questions from:', url);
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    console.log('Questions fetch response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const questions = await response.json();
                    this.questionFields = questions.map(q => ({
                        unique_id: q.unique_id,
                        type: q.type,
                        question_text: q.question_text,
                        option_a: q.option_a,
                        option_b: q.option_b,
                        option_c: q.option_c,
                        incorrect: q.incorrect,
                        answers: [q.answer_1, q.answer_2].filter(Boolean).sort(),
                        answer_1: q.answer_1 || '',
                        answer_2: q.answer_2 || ''
                    }));
                    console.log('Fetched questions:', this.questionFields);
                    this.formStatus = 'success';
                    this.formMessage = `${questions.length} questions loaded.`;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } catch (error) {
                    console.error('Error loading questions:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to load questions. Please try again.';
                    this.questionFields = [];
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } finally {
                    this.isLoading = false;
                }
            },
            openEditModal(index) {
                const field = this.questionFields[index];
                this.editForm = {
                    unique_id: field.unique_id,
                    type: field.type,
                    question_text: field.question_text,
                    option_a: field.option_a,
                    option_b: field.option_b,
                    option_c: field.option_c,
                    incorrect: field.incorrect,
                    answers: field.answers,
                    answer_1: field.answer_1,
                    answer_2: field.answer_2
                };
                this.editIndex = index;
                this.updateAnswerValidation();
                this.showEditModal = true;
                console.log('Opened edit modal for question:', field.unique_id);
            },
            updateEditAnswers(value) {
                const maxAnswers = this.editForm.type === 'single' ? 1 : 2;
                if (this.editForm.answers.includes(value)) {
                    this.editForm.answers = this.editForm.answers.filter(ans => ans !== value);
                } else if (this.editForm.answers.length < maxAnswers) {
                    this.editForm.answers = [...this.editForm.answers, value].sort();
                }
                this.editForm.answer_1 = this.editForm.answers[0] || '';
                this.editForm.answer_2 = this.editForm.answers[1] || '';
                this.updateAnswerValidation();
                console.log('Updated edit answers:', this.editForm.answers);
            },
            async updateQuestion() {
                if (!this.isValidAnswerCount) {
                    this.formStatus = 'error';
                    this.formMessage = this.editForm.type === 'single' ?
                        'Please select exactly one answer.' :
                        'Please select exactly two answers.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                    return;
                }
                this.isSubmitting = true;
                const url = '{{ route('editmocksecondquestion.update') }}';
                console.log('Updating question:', this.editForm);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.editForm)
                    });
                    console.log('Update question response status:', response.status);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.errors?.general || `HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    this.questionFields[this.editIndex] = { ...this.editForm };
                    this.showEditModal = false;
                    console.log('Question updated:', data);
                } catch (error) {
                    console.error('Error updating question:', error);
                    this.formStatus = 'error';
                    this.formMessage = `Error updating question: ${error.message}`;
                } finally {
                    this.isSubmitting = false;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            openDeleteModal(unique_id, index) {
                this.deleteUniqueId = unique_id;
                this.deleteIndex = index;
                this.showDeleteModal = true;
                console.log('Opened delete modal for question:', unique_id);
            },
            async confirmDelete() {
                this.isSubmitting = true;
                const url = '{{ route('editmocksecondquestion.delete') }}';
                console.log('Deleting question:', this.deleteUniqueId);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            unique_id: this.deleteUniqueId
                        })
                    });
                    console.log('Delete question response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    this.questionFields.splice(this.deleteIndex, 1);
                    this.showDeleteModal = false;
                    console.log('Question deleted:', data);
                } catch (error) {
                    console.error('Error deleting question:', error);
                    this.formStatus = 'error';
                    this.formMessage = `Error deleting question: ${error.message}`;
                } finally {
                    this.isSubmitting = false;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            async deleteAllQuestions() {
                if (!confirm('Are you sure you want to delete all questions for this section?')) return;
                this.isDeletingAll = true;
                const url = '{{ route('editmocksecondquestion.delete-all') }}';
                console.log('Deleting all questions for section:', this.section_unique_id);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            section_unique_id: this.section_unique_id
                        })
                    });
                    console.log('Delete all questions response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    this.questionFields = [];
                    console.log('All questions deleted:', data);
                } catch (error) {
                    console.error('Error deleting all questions:', error);
                    this.formStatus = 'error';
                    this.formMessage = `Error deleting all questions: ${error.message}`;
                } finally {
                    this.isDeletingAll = false;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            }
        }));
    });
</script>

<style>
    @keyframes bounce-in {
        0% { transform: scale(0.9) translateY(-20px); opacity: 0; }
        60% { transform: scale(1.05) translateY(5px); opacity: 1; }
        100% { transform: scale(1) translateY(0); opacity: 1; }
    }
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    .animate-bounce-in { animation: bounce-in 0.5s ease-out; }
    .animate-pulse-slow { animation: pulse-slow 3s infinite ease-in-out; }
    .glassmorphic {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    .slide-up {
        animation: slide-up 0.5s ease-out;
    }
    @keyframes slide-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
