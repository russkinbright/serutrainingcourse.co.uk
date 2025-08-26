<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="mockSecondQuestionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-question-circle text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Create Mock Test 2 Questions
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Mock Search -->
            <div class="mb-8">
                <label for="mockSearch" class="block text-xl font-bold text-gray-900 mb-3">
                    Select Mock
                </label>
                <input type="text" id="mockSearch" x-model="mockSearch" @input.debounce.500="searchMocks()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                    placeholder="Search mock by name or ID">
                <div x-show="isMockSearching" class="mt-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Searching mocks...</span>
                </div>
                <div x-show="mockSearchResults.length > 0"
                    class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                    <ul class="max-h-60 overflow-y-auto">
                        <template x-for="mock in mockSearchResults" :key="mock.mock_unique_id">
                            <li @click="selectMock(mock.mock_unique_id, mock.tag, mock.practice_unique_id)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                <span x-text="mock.tag"></span> (ID: <span x-text="mock.mock_unique_id"></span>) - <span
                                class="text-purple-500 font-bold" x-text="mock.name"></span>

                            </li>
                        </template>
                    </ul>
                </div>
                <div x-show="selectedMock" class="mt-2 text-gray-700">
                    Selected Mock: <span x-text="selectedMock"></span> (Mock ID: <span x-text="mock_unique_id"></span>)
                    <br> Section ID: <span x-text="section_unique_id"></span>
                </div>

            </div>

            <!-- Question Count and Selection Mode -->
            <div x-show="selectedMock" class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="questionCount" class="block text-xl font-bold text-gray-900 mb-3">
                        Number of Questions
                    </label>
                    <input type="number" id="questionCount" x-model.number="questionCount" min="0"
                        max="100"
                        class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                        placeholder="Enter number of questions (0-100)">
                </div>
                <div>
                    <label class="block text-xl font-bold text-gray-900 mb-3">
                        Selection Mode
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="selectionMode" value="random" x-model="selectionMode"
                                class="mr-2 text-cyan-500 focus:ring-cyan-400">
                            <span>Random</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="selectionMode" value="custom" x-model="selectionMode"
                                class="mr-2 text-cyan-500 focus:ring-cyan-400">
                            <span>Custom</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Select Questions Button -->
            <div x-show="selectedMock && questionCount > 0" class="mb-8">
                <button @click="selectQuestions()" :disabled="isLoading || questionCount <= 0 || !selectionMode"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="isLoading || questionCount <= 0 || !selectionMode ? 'bg-gray-400 cursor-not-allowed' :
                        'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <template x-if="isLoading">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Loading...</span>
                        </div>
                    </template>
                    <template x-if="!isLoading">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3 animate-pulse text-amber-300"></i>
                            <span>Select Questions</span>
                        </div>
                    </template>
                </button>
            </div>

            <!-- Question List -->
            <div x-show="questions.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Questions (<span x-text="selectedQuestions.length"></span>/<span x-text="questionCount"></span>
                    selected)
                </h2>
                <div class="grid grid-cols-1 gap-4">
                    <template x-for="(question, index) in questions" :key="question.unique_id">
                        <div class="p-4 bg-white/80 rounded-xl shadow-md border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <span
                                        x-text="question.type === 'single' ? 'Single-Answer' : 'Double-Answer'"></span>
                                    Question <span x-text="index + 1"></span>
                                </h3>
                                <label class="flex items-center">
                                    <input type="checkbox" x-model="selectedQuestions" :value="question.unique_id"
                                        :disabled="selectionMode === 'random' || (selectedQuestions.length >= questionCount && !
                                            selectedQuestions.includes(question.unique_id))"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Select</span>
                                </label>
                            </div>
                            <p class="text-gray-700 mb-2" x-text="question.question_text"></p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div>
                                    <span class="font-semibold text-purple-900">A:</span>
                                    <span x-text="question.option_a"></span>
                                </div>
                                <div>
                                    <span class="font-semibold text-purple-900">B:</span>
                                    <span x-text="question.option_b"></span>
                                </div>
                                <div>
                                    <span class="font-semibold text-purple-900">C:</span>
                                    <span x-text="question.option_c"></span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="font-semibold text-purple-900">Answer(s):</span>
                                <span
                                    x-text="[question.answer_1, question.answer_2].filter(Boolean).join(', ')"></span>
                            </div>
                            <div class="mt-2">
                                <span class="font-semibold text-purple-900">Incorrect Explanation:</span>
                                <span x-text="question.incorrect"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Button -->
            <div x-show="selectedQuestions.length > 0" class="mb-8">
                <button @click="submitQuestions()"
                    :disabled="isSubmitting || selectedQuestions.length !== questionCount"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="isSubmitting || selectedQuestions.length !== questionCount ? 'bg-gray-400 cursor-not-allowed' :
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
                            <span>Saving Questions...</span>
                        </div>
                    </template>
                    <template x-if="!isSubmitting">
                        <div class="flex items-center">
                            <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                            <span>Save Questions</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mockSecondQuestionManager', () => ({
                mockSearch: '',
                mockSearchResults: [],
                isMockSearching: false,
                mock_unique_id: '',
                selectedMock: '',
                questionCount: 0,
                selectionMode: 'random',
                questions: [],
                selectedQuestions: [],
                isLoading: false,
                isSubmitting: false,
                formMessage: '',
                formStatus: '',
                init() {
                    // Initialize component
                },
                async searchMocks() {
                    const query = this.mockSearch.trim();
                    if (!query) {
                        this.mockSearchResults = [];
                        this.isMockSearching = false;
                        return;
                    }
                    this.isMockSearching = true;
                    try {
                        const response = await fetch(
                            '{{ route('mocksecondquestion.search') }}?query=' +
                            encodeURIComponent(query), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                        this.mockSearchResults = await response.json();
                    } catch (error) {
                        console.error('Error searching mocks:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to search mocks. Please try again.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                    } finally {
                        this.isMockSearching = false;
                    }
                },
                selectMock(unique_id, tag, practice_id) {
                    this.mock_unique_id = unique_id;
                    this.selectedMock = tag;
                    this.practice_unique_id = practice_id; // if you need it
                    this.mockSearch = '';
                    this.mockSearchResults = [];
                    this.questions = [];
                    this.selectedQuestions = [];
                    this.loadQuestions();
                },
                async loadQuestions() {
                    if (!this.practice_unique_id) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch(
                            '{{ route('mocksecondquestion.questions') }}?practice_unique_id=' +
                            encodeURIComponent(this.practice_unique_id), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                        this.questions = await response.json();
                        this.formStatus = 'success';
                        this.formMessage = `${this.questions.length} questions loaded.`;
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                    } catch (error) {
                        console.error('Error loading questions:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to load questions. Please try again.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                    } finally {
                        this.isLoading = false;
                    }
                },
                selectQuestions() {
                    if (this.questionCount <= 0 || this.questionCount > 100) {
                        this.formStatus = 'error';
                        this.formMessage = 'Please enter a valid number (1-100) for question count.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                        return;
                    }
                    if (this.questionCount > this.questions.length) {
                        this.formStatus = 'error';
                        this.formMessage =
                            `Only ${this.questions.length} questions available. Please select a number up to ${this.questions.length}.`;
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                        return;
                    }
                    this.selectedQuestions = [];
                    if (this.selectionMode === 'random') {
                        const shuffled = [...this.questions].sort(() => Math.random() - 0.5);
                        this.selectedQuestions = shuffled.slice(0, this.questionCount).map(q => q
                            .unique_id);
                    }
                    this.formStatus = 'success';
                    this.formMessage = this.selectionMode === 'random' ?
                        `${this.questionCount} questions selected randomly.` :
                        'Please select questions manually.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                },
                async submitQuestions() {
                    if (this.selectedQuestions.length !== this.questionCount) {
                        this.formStatus = 'error';
                        this.formMessage = `Please select exactly ${this.questionCount} questions.`;
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                        return;
                    }
                    if (!this.mock_unique_id) {
                        this.formStatus = 'error';
                        this.formMessage = 'Mock ID is missing. Please select a mock.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        const selectedQuestionData = this.questions.filter(q =>
                            this.selectedQuestions.includes(q.unique_id));
                        const response = await fetch('{{ route('mocksecondquestion.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                mock_unique_id: this.mock_unique_id,
                                questions: selectedQuestionData
                            })
                        });
                        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                        const data = await response.json();
                        this.formStatus = 'success';
                        this.formMessage = data.message;
                        this.mock_unique_id = '';
                        this.selectedMock = '';
                        this.mockSearch = '';
                        this.questionCount = 0;
                        this.selectionMode = 'random';
                        this.questions = [];
                        this.selectedQuestions = [];
                    } catch (error) {
                        console.error('Error submitting questions:', error);
                        this.formStatus = 'error';
                        this.formMessage = `Error submitting questions: ${error.message}`;
                    } finally {
                        this.isSubmitting = false;
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
            0% {
                transform: scale(0.9) translateY(-20px);
                opacity: 0;
            }

            60% {
                transform: scale(1.05) translateY(5px);
                opacity: 1;
            }

            100% {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .animate-bounce-in {
            animation: bounce-in 0.5s ease-out;
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s infinite ease-in-out;
        }

        .glassmorphic {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .slide-up {
            animation: slide-up 0.5s ease-out;
        }

        @keyframes slide-up {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</div>
