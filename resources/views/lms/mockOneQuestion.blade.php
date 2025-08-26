<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="mockOneQuestionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-question-circle text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Create Mock Test 1 Questions
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
                <input type="text" id="mockSearch" x-model="mockSearch"
                    @input.debounce.500="searchMocks()"
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
                        <template x-for="mock in mockSearchResults" :key="mock.unique_id">
                            <li @click="selectMock(mock.unique_id, mock.tag)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                <span x-text="mock.tag"></span> (ID: <span x-text="mock.unique_id"></span>) - <span class="text-purple-500 font-bold" x-text="mock.name"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div x-show="selectedMock" class="mt-2 text-gray-700">
                    Selected Mock: <span x-text="selectedMock"></span> (ID: <span
                        x-text="mock_unique_id"></span>)
                </div>
            </div>

            <!-- Question Count Input -->
            <div class="mb-8">
                <label for="questionCount" class="block text-xl font-bold text-gray-900 mb-3">
                    Number of Questions
                </label>
                <input type="number" id="questionCount" x-model.number="questionCount" min="0"
                    max="20"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                    placeholder="Enter number of questions (0-20)">
            </div>

            <!-- Generate Button -->
            <div class="mb-8">
                <button @click="generateQuestionFields()" :disabled="!mock_unique_id || questionCount === 0"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="!mock_unique_id || questionCount === 0 ? 'bg-gray-400 cursor-not-allowed' :
                        'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <i class="fas fa-plus-circle mr-3 animate-pulse text-amber-300"></i>
                    Generate Questions
                </button>
            </div>

            <!-- PDF Upload -->
            <div class="mb-8">
                <label for="pdfUpload" class="block text-xl font-bold text-gray-900 mb-3">
                    Upload PDF
                </label>
                <input type="file" id="pdfUpload" accept=".pdf" @change="handleFileUpload"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900">
                <button @click="parsePDF()" x-show="pdfFile" :disabled="!mock_unique_id || isParsing"
                    class="mt-4 px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="!mock_unique_id || isParsing ? 'bg-gray-400 cursor-not-allowed' :
                        'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <template x-if="isParsing">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Parsing PDF...</span>
                        </div>
                    </template>
                    <template x-if="!isParsing">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf mr-3 animate-pulse text-amber-300"></i>
                            <span>Parse PDF</span>
                        </div>
                    </template>
                </button>
            </div>

            <!-- Question Inputs -->
            <div x-show="questionFields.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Questions</h2>
                <template x-for="(field, index) in questionFields" :key="index">
                    <div class="mb-6 p-4 bg-white/80 rounded-xl shadow-md border border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Question <span x-text="index + 1"></span>
                            </h3>
                            <button @click="removeQuestion(index)"
                                class="px-3 py-2 bg-red-600 text-white font-bold rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                Remove
                            </button>
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
                            <div>
                                <label class="block text-lg font-semibold text-purple-900 mb-2">Option D</label>
                                <input type="text" :id="'option-d-' + index"
                                    x-model="questionFields[index].option_d"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Option D">
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" :id="'answer-' + index + '-d'" value="d"
                                        @change="updateAnswer(index, 'd')"
                                        :checked="questionFields[index].answers.includes('d')"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-lg font-semibold text-purple-900 mb-2">Option E</label>
                                <input type="text" :id="'option-e-' + index"
                                    x-model="questionFields[index].option_e"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Option E">
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" :id="'answer-' + index + '-e'" value="e"
                                        @change="updateAnswer(index, 'e')"
                                        :checked="questionFields[index].answers.includes('e')"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-lg font-semibold text-purple-900 mb-2">Option F</label>
                                <input type="text" :id="'option-f-' + index"
                                    x-model="questionFields[index].option_f"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Option F">
                                <label class="flex items-center mt-2">
                                    <input type="checkbox" :id="'answer-' + index + '-f'" value="f"
                                        @change="updateAnswer(index, 'f')"
                                        :checked="questionFields[index].answers.includes('f')"
                                        class="mr-2 text-cyan-500 focus:ring-cyan-400">
                                    <span>Correct Answer</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Submit Button -->
            <div x-show="questionFields.length > 0" class="mb-8">
                <button @click.prevent="submitQuestions()"
                    :disabled="isSubmitting || !mock_unique_id || questionFields.some(field =>
                        !field.question_text || !field.option_a || !field.option_b || !field.option_c ||
                        !field.option_d || !field.option_e || !field.option_f || field.answers.length !== 3 ||
                        field.questionExists)"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="{
                        'bg-gray-400 cursor-not-allowed': isSubmitting || !mock_unique_id || questionFields.some(
                            f => !f.question_text || !f.option_a || !f.option_b || !f.option_c ||
                            !f.option_d || !f.option_e || !f.option_f || f.answers.length !== 3 || f
                            .questionExists
                        ),
                        'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50':
                            !(isSubmitting || !mock_unique_id || questionFields.some(
                                f => !f.question_text || !f.option_a || !f.option_b || !f.option_c ||
                                !f.option_d || !f.option_e || !f.option_f || f.answers.length !== 3 || f
                                .questionExists
                            ))
                    }">
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
                            <span>Creating Questions...</span>
                        </div>
                    </template>
                    <template x-if="!isSubmitting">
                        <div class="flex items-center">
                            <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                            <span>Create Questions</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mockOneQuestionManager', () => ({
            mockSearch: '',
            mockSearchResults: [],
            isMockSearching: false,
            mock_unique_id: '',
            selectedMock: '',
            questionCount: 0,
            questionFields: [],
            pdfFile: null,
            isParsing: false,
            isSubmitting: false,
            formMessage: '',
            formStatus: '',
            init() {
                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
            },
            async checkQuestionExists(index) {
                const questionText = this.questionFields[index].question_text.trim();
                if (!questionText) {
                    this.questionFields[index].questionExists = false;
                    return;
                }
                try {
                    const response = await fetch(
                        '{{ route('mockonequestion.check-exists') }}?question_text=' +
                        encodeURIComponent(questionText), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.questionFields[index].questionExists = data.exists;
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
                        '{{ route('mockonequestion.search') }}?query=' + encodeURIComponent(
                            query), {
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
            selectMock(unique_id, name) {
                this.mock_unique_id = unique_id;
                this.selectedMock = name;
                this.mockSearch = '';
                this.mockSearchResults = [];
            },
            generateQuestionFields() {
                if (this.questionCount < 0 || this.questionCount > 20) {
                    this.questionFields = [];
                    this.formStatus = 'error';
                    this.formMessage = 'Please enter a valid number (0-20) for question count.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                    return;
                }
                this.questionFields = [];
                for (let i = 0; i < this.questionCount; i++) {
                    this.questionFields.push({
                        question_text: '',
                        option_a: '',
                        option_b: '',
                        option_c: '',
                        option_d: '',
                        option_e: '',
                        option_f: '',
                        answers: [],
                        answer_1: '',
                        answer_2: '',
                        answer_3: '',
                        questionExists: false
                    });
                }
                this.formStatus = 'success';
                this.formMessage = 'Question fields generated successfully!';
                setTimeout(() => {
                    this.formMessage = '';
                    this.formStatus = '';
                }, 3000);
            },
            async parsePDF() {
                if (!this.pdfFile || !this.mock_unique_id) {
                    this.formStatus = 'error';
                    this.formMessage = 'Please select a mock and upload a PDF.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                    return;
                }
                this.isParsing = true;
                try {
                    const arrayBuffer = await this.pdfFile.arrayBuffer();
                    const pdf = await pdfjsLib.getDocument(arrayBuffer).promise;
                    let textContent = '';
                    for (let i = 1; i <= pdf.numPages; i++) {
                        const page = await pdf.getPage(i);
                        const text = await page.getTextContent();
                        textContent += text.items.map(item => item.str).join('') + ' ';
                    }
                    textContent = textContent.replace(/\s+/g, ' ').trim();
                    console.log('Parsed PDF text:', textContent);

                    const questionBlocks = textContent.split(/(?=Question_\d+)/);
                    console.log('Question blocks:', questionBlocks);

                    let questionIndex = 0;
                    const newQuestionFields = [];

                    for (const block of questionBlocks) {
                        const trimmedBlock = block.trim();
                        if (!trimmedBlock) continue;

                        console.log('Processing block:', trimmedBlock);

                        const questionMatch = trimmedBlock.match(/Question_\d+(.+?)a\)/);
                        const optionsMatch = trimmedBlock.match(
                            /a\)(.+?)b\)(.+?)c\)(.+?)d\)(.+?)e\)(.+?)f\)(.+?)Answer_1:/);
                        const answer1Match = trimmedBlock.match(/Answer_1:\s*([a-f])/i);
                        const answer2Match = trimmedBlock.match(/Answer_2:\s*([a-f])/i);
                        const answer3Match = trimmedBlock.match(/Answer_3:\s*([a-f])/i);

                        if (questionMatch && optionsMatch && answer1Match && answer2Match &&
                            answer3Match) {
                            const answers = [
                                answer1Match[1].toLowerCase(),
                                answer2Match[1].toLowerCase(),
                                answer3Match[1].toLowerCase()
                            ].sort();
                            const questionField = {
                                question_text: questionMatch[1].trim(),
                                option_a: optionsMatch[1].trim(),
                                option_b: optionsMatch[2].trim(),
                                option_c: optionsMatch[3].trim(),
                                option_d: optionsMatch[4].trim(),
                                option_e: optionsMatch[5].trim(),
                                option_f: optionsMatch[6].trim(),
                                answers: answers,
                                answer_1: answers[0],
                                answer_2: answers[1],
                                answer_3: answers[2],
                                questionExists: false
                            };
                            newQuestionFields.push(questionField);
                            questionIndex++;
                        } else {
                            console.warn('Question block parse failed:', trimmedBlock);
                        }
                    }

                    // Merge parsed questions with existing fields
                    const existingFields = [...this.questionFields];
                    this.questionFields = [];

                    newQuestionFields.forEach((parsedField, index) => {
                        if (index < existingFields.length) {
                            this.questionFields.push({
                                ...existingFields[index],
                                ...parsedField,
                                questionExists: false
                            });
                        } else {
                            this.questionFields.push(parsedField);
                        }
                    });

                    if (newQuestionFields.length < existingFields.length) {
                        for (let i = newQuestionFields.length; i < existingFields.length; i++) {
                            this.questionFields.push(existingFields[i]);
                        }
                    }

                    this.questionCount = this.questionFields.length;

                    this.questionFields.forEach((field, index) => {
                        if (field.question_text) {
                            document.getElementById(`question-${index}`).value = field
                                .question_text;
                            document.getElementById(`option-a-${index}`).value = field
                                .option_a;
                            document.getElementById(`option-b-${index}`).value = field
                                .option_b;
                            document.getElementById(`option-c-${index}`).value = field
                                .option_c;
                            document.getElementById(`option-d-${index}`).value = field
                                .option_d;
                            document.getElementById(`option-e-${index}`).value = field
                                .option_e;
                            document.getElementById(`option-f-${index}`).value = field
                                .option_f;
                            field.answers.forEach(ans => {
                                const answerInput = document.getElementById(
                                    `answer-${index}-${ans}`);
                                if (answerInput) answerInput.checked = true;
                            });
                            this.checkQuestionExists(index);
                        }
                    });

                    console.log('Final questionFields:', this.questionFields);
                    console.log('Question count:', this.questionCount);

                    this.formStatus = 'success';
                    this.formMessage =
                        `PDF parsed successfully! ${questionIndex} questions loaded.`;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } catch (error) {
                    console.error('Error parsing PDF:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Error parsing PDF: ' + error.message;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } finally {
                    this.isParsing = false;
                }
            },
            updateAnswer(index, value) {
                const field = this.questionFields[index];
                const answers = field.answers;
                if (answers.includes(value)) {
                    field.answers = answers.filter(ans => ans !== value);
                } else if (answers.length < 3) {
                    field.answers = [...answers, value].sort();
                }
                field.answer_1 = field.answers[0] || '';
                field.answer_2 = field.answers[1] || '';
                field.answer_3 = field.answers[2] || '';
                document.querySelectorAll(`input[id^='answer-${index}-']`).forEach(checkbox => {
                    checkbox.checked = field.answers.includes(checkbox.value);
                });
            },
            handleFileUpload(event) {
                this.pdfFile = event.target.files[0];
            },
            removeQuestion(index) {
                this.questionFields.splice(index, 1);
                this.questionCount = Math.max(0, this.questionCount - 1);
            },
            submitQuestions() {
                if (!this.mock_unique_id || this.questionFields.some(
                        field => !field.question_text || !field.option_a || !field.option_b || !
                        field.option_c ||
                        !field.option_d || !field.option_e || !field.option_f ||
                        field.answers.length !== 3 || field.questionExists)) {
                    this.formStatus = 'error';
                    this.formMessage =
                        'Please fill in all required fields, select exactly three answers, and ensure no questions already exist.';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                    return;
                }
                this.isSubmitting = true;
                fetch('{{ route('mockonequestion.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            mock_unique_id: this.mock_unique_id,
                            questions: this.questionFields.map(field => ({
                                question_text: field.question_text,
                                option_a: field.option_a,
                                option_b: field.option_b,
                                option_c: field.option_c,
                                option_d: field.option_d,
                                option_e: field.option_e,
                                option_f: field.option_f,
                                answer_1: field.answer_1,
                                answer_2: field.answer_2,
                                answer_3: field.answer_3
                            }))
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data.message) {
                            this.formStatus = 'success';
                            this.formMessage = data.message;
                            this.mock_unique_id = '';
                            this.selectedMock = '';
                            this.mockSearch = '';
                            this.questionCount = 0;
                            this.questionFields = [];
                            this.pdfFile = null;
                            document.getElementById('pdfUpload').value = '';
                        } else if (data.errors) {
                            this.formStatus = 'error';
                            this.formMessage = Object.values(data.errors).flat().join(' ');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting questions:', error);
                        this.formStatus = 'error';
                        this.formMessage = `Error submitting questions: ${error.message}`;
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                    });
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
