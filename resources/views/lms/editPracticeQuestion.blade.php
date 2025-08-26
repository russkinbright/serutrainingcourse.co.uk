<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="editQuestionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-edit text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Edit Practice Questions
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
                    placeholder="Search section by tag or ID">
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
                            <li @click="selectSection(section.unique_id, section.tag)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                <span x-text="section.tag"></span> (ID: <span x-text="section.unique_id"></span>)
                            </li>
                        </template>
                    </ul>
                </div>
                <div x-show="!isSectionSearching && sectionSearch && sectionSearchResults.length === 0"
                    class="mt-2 text-gray-600">
                    No sections found.
                </div>
                <div x-show="selectedSection" class="mt-2 text-gray-700">
                    Selected Section: <span x-text="selectedSection"></span> (ID: <span
                        x-text="section_unique_id"></span>)
                </div>
            </div>

            <!-- Question Search -->
            <div x-show="selectedSection" class="mb-8">
                <label for="questionSearch" class="block text-xl font-bold text-gray-900 mb-3">
                    Search Questions
                </label>
                <input type="text" id="questionSearch" x-model="questionSearch"
                    @input.debounce.500="searchQuestions()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500"
                    placeholder="Search by question text">
                <div x-show="isQuestionSearching" class="mt-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Searching questions...</span>
                </div>
                <div x-show="!isQuestionSearching && questionFields.length === 0" class="mt-2 text-gray-600">
                    No questions found for this section.
                </div>
            </div>

            <!-- Delete All Button -->
            <div x-show="selectedSection" class="mb-8">
                <button @click="showDeleteAllModal = true"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 hover:ring-4 hover:ring-red-400/50">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <i class="fas fa-trash-alt mr-3 animate-pulse text-amber-300"></i>
                    Delete All Questions
                </button>
            </div>

            <!-- Question Inputs -->
            <div x-show="questionFields.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Questions</h2>
                <template x-for="(field, index) in questionFields" :key="field.unique_id">
                    <div class="mb-6 p-4 bg-white/80 rounded-xl shadow-md border border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <span
                                    x-text="field.type === 'single' ? 'Single-Answer Question' : 'Double-Answer Question'"></span>
                                <span x-text="index + 1"></span>
                            </h3>
                            <div class="flex space-x-2">
                                <button @click="updateQuestion(index)"
                                    :disabled="!field.question_text || !field.option_a || !field.option_b || !field.option_c ||
                                        !field.answers.length || field.questionExists"
                                    class="px-3 py-2 text-white font-bold rounded-xl transition-all duration-500 flex items-center"
                                    :class="{
                                        'bg-gray-400 cursor-not-allowed': !field.question_text || !field.option_a ||
                                            !field.option_b || !field.option_c || !field.answers.length || field.questionExists,
                                        'bg-purple-600 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50': field.question_text && field.option_a && field.option_b && field.option_c &&
                                            field.answers.length && !field.questionExists
                                    }">
                                    <i class="fas fa-save mr-2 animate-pulse text-amber-300"></i>
                                    Save
                                </button>
                                <button @click="deleteQuestion(index)"
                                    class="px-3 py-2 bg-red-600 text-white font-bold rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label :for="'question-' + index"
                                class="block text-lg font-semibold text-purple-900 mb-2">
                                Question Text
                            </label>
                            <input type="text" :id="'question-' + index"
                                x-model="questionFields[index].question_text"
                                @input.debounce.500="checkQuestionExists(index)"
                                class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-red-700"
                                placeholder="Enter question text">
                            <div x-show="questionFields[index].questionExists"
                                class="mt-2 p-3 rounded-xl bg-red-100/80 text-red-900 flex items-center slide-up">
                                <i class="fas fa-exclamation-circle text-red-600 animate-pulse mr-2"></i>
                                <span>Question already exists in database</span>
                            </div>
                        </div>
                        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-lg font-semibold text-purple-900 mb-2">Option A</label>
                                <input type="text" :id="'option-a-' + index"
                                    x-model="questionFields[index].option_a"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Option A">
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" :id="'answer-' + index + '-a'" value="a"
                                        @change="updateAnswer(index, 'a')"
                                        :checked="questionFields[index].answers.includes('a')"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-lg font-semibold text-purple-900 mb-2">Option B</label>
                                <input type="text" :id="'option-b-' + index"
                                    x-model="questionFields[index].option_b"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Option B">
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" :id="'answer-' + index + '-b'" value="b"
                                        @change="updateAnswer(index, 'b')"
                                        :checked="questionFields[index].answers.includes('b')"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-lg font-semibold text-purple-900 mb-2">Option C</label>
                                <input type="text" :id="'option-c-' + index"
                                    x-model="questionFields[index].option_c"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Option C">
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" :id="'answer-' + index + '-c'" value="c"
                                        @change="updateAnswer(index, 'c')"
                                        :checked="questionFields[index].answers.includes('c')"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label :for="'incorrect-' + index"
                                class="block text-lg font-semibold text-purple-900 mb-2">
                                Incorrect Explanation
                            </label>
                            <textarea :id="'incorrect-' + index" x-model="questionFields[index].incorrect"
                                class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-pink-600"
                                placeholder="Enter incorrect explanation"></textarea>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Delete All Confirmation Modal -->
            <div x-show="showDeleteAllModal"
                class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl border border-indigo-200/50">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Confirm Delete All Questions</h3>
                    <p class="text-gray-700 mb-6">
                        Are you sure you want to delete all questions for the section
                        <span x-text="selectedSection"></span> (ID: <span x-text="section_unique_id"></span>)?
                        This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-4">
                        <button @click="showDeleteAllModal = false"
                            class="px-4 py-2 bg-gray-400 text-white font-bold rounded-xl hover:bg-gray-500 transition-all duration-300">
                            Cancel
                        </button>
                        <button @click="deleteAllQuestions()"
                            class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 transition-all duration-300 flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('editQuestionManager', () => ({
            sectionSearch: '',
            sectionSearchResults: [],
            isSectionSearching: false,
            section_unique_id: '',
            selectedSection: '',
            questionSearch: '',
            isQuestionSearching: false,
            questionFields: [],
            formMessage: '',
            formStatus: '',
            showDeleteAllModal: false,
            init() {
                console.log('EditQuestionManager initialized');
            },
            async searchSections() {
                const query = this.sectionSearch.trim();
                if (!query) {
                    this.sectionSearchResults = [];
                    this.isSectionSearching = false;
                    return;
                }
                this.isSectionSearching = true;
                const url = '{{ route('editpracticequestion.search') }}?query=' + encodeURIComponent(query);
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
            async selectSection(unique_id, tag) {
                this.section_unique_id = unique_id;
                this.selectedSection = tag;
                this.sectionSearch = '';
                this.sectionSearchResults = [];
                this.questionSearch = '';
                this.formMessage = '';
                this.formStatus = '';
                console.log('Selected section:', { unique_id, tag });
                await this.loadQuestions();
            },
            async searchQuestions() {
                await this.loadQuestions();
            },
            async loadQuestions() {
                if (!this.section_unique_id) return;
                this.isQuestionSearching = true;
                const query = this.questionSearch.trim();
                const url = '{{ route('editpracticequestion.questions') }}?section_unique_id=' +
                    encodeURIComponent(this.section_unique_id) +
                    (query ? '&query=' + encodeURIComponent(query) : '');
                console.log('Fetching questions from:', url);
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    console.log('Questions fetch response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    this.questionFields = await response.json();
                    console.log('Fetched questions:', this.questionFields);
                    for (let index = 0; index < this.questionFields.length; index++) {
                        if (this.questionFields[index].question_text) {
                            await this.checkQuestionExists(index);
                        }
                    }
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
                    this.isQuestionSearching = false;
                }
            },
            async checkQuestionExists(index) {
                const questionText = this.questionFields[index].question_text.trim();
                const uniqueId = this.questionFields[index].unique_id;
                if (!questionText) {
                    this.questionFields[index].questionExists = false;
                    return;
                }
                const url = '{{ route('editpracticequestion.check-exists') }}?question_text=' +
                    encodeURIComponent(questionText) + '&unique_id=' +
                    encodeURIComponent(uniqueId);
                console.log('Checking question existence:', url);
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    console.log('Check question response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.questionFields[index].questionExists = data.exists;
                    console.log('Question exists:', data.exists);
                } catch (error) {
                    console.error('Error checking question existence:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to check question existence. Please try again.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            async updateQuestion(index) {
                const field = this.questionFields[index];
                if (!field.question_text || !field.option_a || !field.option_b || !field.option_c ||
                    !field.answers.length || field.questionExists) {
                    this.formStatus = 'error';
                    this.formMessage =
                        'Please fill in all required fields, select at least one answer, and ensure the question does not already exist.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                    return;
                }
                const url = '{{ route('editpracticequestion.update') }}';
                console.log('Updating question:', field);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            unique_id: field.unique_id,
                            section_unique_id: this.section_unique_id,
                            type: field.type,
                            question_text: field.question_text,
                            option_a: field.option_a,
                            option_b: field.option_b,
                            option_c: field.option_c,
                            answer_1: field.answer_1,
                            answer_2: field.answer_2,
                            incorrect: field.incorrect
                        })
                    });
                    console.log('Update question response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    console.log('Question updated:', data);
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } catch (error) {
                    console.error('Error updating question:', error);
                    this.formStatus = 'error';
                    this.formMessage = `Error updating question: ${error.message}`;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            async deleteQuestion(index) {
                const field = this.questionFields[index];
                const url = '{{ route('editpracticequestion.delete') }}';
                console.log('Deleting question:', field.unique_id);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            unique_id: field.unique_id
                        })
                    });
                    console.log('Delete question response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.questionFields.splice(index, 1);
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    console.log('Question deleted:', data);
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } catch (error) {
                    console.error('Error deleting question:', error);
                    this.formStatus = 'error';
                    this.formMessage = `Error deleting question: ${error.message}`;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            async deleteAllQuestions() {
                const url = '{{ route('editpracticequestion.delete-all') }}';
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
                    this.questionFields = [];
                    this.showDeleteAllModal = false;
                    this.questionSearch = '';
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    console.log('All questions deleted:', data);
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } catch (error) {
                    console.error('Error deleting all questions:', error);
                    this.formStatus = 'error';
                    this.formMessage = `Error deleting all questions: ${error.message}`;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            updateAnswer(index, value) {
                const field = this.questionFields[index];
                const answers = field.answers;
                if (answers.includes(value)) {
                    field.answers = answers.filter(ans => ans !== value);
                } else if (answers.length < (field.type === 'single' ? 1 : 2)) {
                    field.answers = [...answers, value].sort();
                }
                field.answer_1 = field.answers[0] || '';
                field.answer_2 = field.type === 'single' ? null : (field.answers[1] || '');
                document.querySelectorAll(`input[id^='answer-${index}-']`).forEach(checkbox => {
                    checkbox.checked = field.answers.includes(checkbox.value);
                });
                console.log('Updated answers for question', index, ':', field.answers);
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
