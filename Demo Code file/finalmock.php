@extends('home.default')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .form-radio:focus,
        .form-checkbox:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.3);
        }

        #scoreChart {
            max-width: 100%;
            max-height: 200px;
            width: 100%;
            height: auto;
        }

        .svg-animation {
            animation: pulse 2s infinite ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }
        }

        .button:hover .svg-icon {
            transform: translateX(5px);
            transition: transform 0.3s ease;
        }
    </style>

    <div class="mx-auto px-4 py-6 flex flex-col" x-data="finalMockApp()" x-init="initFinalMock()">
        <!-- Header -->
        <div class="flex items-center mb-6">
            <button onclick="window.location.href='/learner/page'"
                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg border-2 border-white 
                           hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer button">
                <svg class="w-5 h-5 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
            </button>
        </div>

        <!-- Message Container -->
        <div x-show="message.text" x-transition
            class="fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-semibold"
            :class="{
                'bg-green-100 text-green-800 border border-green-300': message.type === 'success',
                'bg-red-100 text-red-800 border border-red-300': message.type === 'error',
                'bg-yellow-100 text-yellow-800 border border-yellow-300': message.type === 'warning'
            }"
            x-text="message.text">
        </div>

        <!-- Loading Overlay -->
        <div x-show="isLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-8 w-8 text-purple-600 svg-animation" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="mt-2 text-gray-600">Loading...</p>
            </div>
        </div>

        <!-- Quiz Feedback Section -->
        <template x-if="quizContent?.resultFeedback">
            <div
                class="mt-8 p-6 bg-gradient-to-br from-purple-100 to-purple-50 border border-purple-200 rounded-2xl shadow-xl">
                <h3 class="text-xl font-bold text-purple-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-purple-600 mr-2 svg-animation" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 19" />
                    </svg>
                    Final Mock Test Results
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-4 flex flex-col text-purple-800">
                        <div class="text-4xl font-extrabold text-center"
                            x-text="calculateTotalMarks(quizContent.resultFeedback) + '/' + quizContent.questions.length">
                        </div>
                        <div class="text-sm uppercase tracking-wide text-gray-500 text-center">Total Marks</div>
                        <div class="text-xs text-gray-400 mt-1 text-center">
                            Questions: <span x-text="quizContent.resultFeedback.length"></span>
                        </div>
                        <div class="mt-3 text-center">
                            <template x-if="isQuizPassed(quizContent.resultFeedback, quizContent.questions)">
                                <div class="text-green-600 font-semibold">🎉 Congratulations, you passed the final mock
                                    test!</div>
                            </template>
                            <template x-if="!isQuizPassed(quizContent.resultFeedback, quizContent.questions)">
                                <div class="text-red-600 font-semibold">❌ You need at least 80% to pass. Try again!</div>
                            </template>
                        </div>
                        <div class="mt-3">
                            <div class="flex">
                                <p class="text-purple-700"><strong>Correct:</strong></p>
                                <span class="text-black font-bold mx-2"
                                    x-text="getCorrectIncorrectQuestions().correct || 'None'"></span>
                            </div>
                            <div class="flex">
                                <p class="text-rose-700"><strong>Incorrect:</strong></p>
                                <span class="text-black font-bold mx-2"
                                    x-text="getCorrectIncorrectQuestions().incorrect || 'None'"></span>
                            </div>
                        </div>
                        <template x-if="quizContent.resultFeedback.some(f => f.source === 'mock1' && !f.isCorrect)">
                            <div class="mt-3 text-sm text-gray-700">
                                <p class="font-semibold text-red-600">Incorrect Answers (Mock Test 1):</p>
                                <ul class="list-disc space-y-3">
                                    <template x-for="feedback in getIncorrectMock1Feedback()" :key="feedback.qIndex">
                                        <li type="none">
                                            <span x-text="`Q${feedback.qIndex + 1}: `" class="font-bold"></span>
                                            <span x-html="feedback.correctText"></span>
                                            <span x-html="feedback.selectedText"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                        <div class="text-center justify-center">
                            <button @click="window.location.reload()"
                                class="mt-5 w-auto bg-purple-600 cursor-pointer hover:bg-purple-700 text-white font-semibold py-1.5 px-4 rounded-xl shadow-lg border-2 border-white 
                                hover:shadow-xl hover:scale-105 transition-all duration-300 button">
                                <span>🔁 Try Again</span>
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-4 flex items-center justify-center">
                        <canvas id="scoreChart" class="w-full h-64"></canvas>
                    </div>
                </div>
            </div>
        </template>

        <!-- Quiz Display -->
        <template x-if="quizContent && !quizContent.resultFeedback">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-semibold text-purple-900 mb-4">Final Mock Test</h2>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <template
                        x-if="quizContent.questions && Array.isArray(quizContent.questions) && quizContent.questions.length > 0">
                        <template x-for="(q, qIndex) in quizContent.questions" :key="q.unique_id || qIndex">
                            <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-purple-200">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex gap-2">
                                        <p class="font-bold text-lg text-purple-600" x-html="`Q${qIndex + 1}:`"></p>
                                        <span class="text font-bold space-x-2 space-y-4"
                                            x-html="formatQuestionText(q, qIndex)"></span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <template x-if="q.type === 'single'">
                                        <template x-for="option in ['a', 'b', 'c']" :key="option">
                                            <label
                                                class="flex items-center space-x-2 cursor-pointer p-2 rounded transition-colors duration-200">
                                                <input type="radio" :name="`q_${qIndex}`" :value="option"
                                                    :checked="quizContent.selectedAnswers[qIndex] === option"
                                                    @change="quizContent.selectedAnswers[qIndex] = option"
                                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                                <span
                                                    x-text="option.toUpperCase() + '. ' + (q['option_' + option] || '')"></span>
                                            </label>
                                        </template>
                                    </template>
                                    <template x-if="q.type === 'double'">
                                        <template x-for="option in ['a', 'b', 'c']" :key="option">
                                            <label
                                                class="flex items-center space-x-2 cursor-pointer p-2 rounded transition-colors duration-200">
                                                <input type="checkbox" :name="`q_${qIndex}`" :value="option"
                                                    :checked="quizContent.selectedAnswers[qIndex]?.includes(option)"
                                                    @change="updateDoubleAnswer(qIndex, option)"
                                                    class="form-checkbox text-purple-600 focus:ring-purple-500">
                                                <span
                                                    x-text="option.toUpperCase() + '. ' + (q['option_' + option] || '')"></span>
                                            </label>
                                        </template>
                                    </template>
                                    <template x-if="q.type === 'mock1'">
                                        <!-- Handled by formatQuestionText for dropdowns -->
                                    </template>
                                </div>
                            </div>
                        </template>
                    </template>
                    <template
                        x-if="!quizContent.questions || !Array.isArray(quizContent.questions) || quizContent.questions.length === 0">
                        <div class="text-gray-500 italic flex items-center justify-center h-full">
                            <svg class="w-6 h-6 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            No questions available for this test.
                        </div>
                    </template>
                    <button @click="checkAnswersLocally(quizContent)"
                        class="mt-6 w-full bg-purple-600 cursor-pointer hover:bg-purple-700 text-white font-semibold py-1.5 px-4 rounded-xl shadow-lg border-2 border-white 
                        hover:shadow-xl hover:scale-95 transition-all duration-300 button"
                        x-show="quizContent.questions && quizContent.questions.length > 0">
                        <span>Check Answers</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Scroll to Top Button -->
        <button x-data="{ show: false }" x-show="show" x-transition x-init="window.addEventListener('scroll', () => show = window.scrollY > 300)"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-6 right-6 bg-gradient-to-br from-rose-500 to-amber-700 hover:bg-purple-800 cursor-pointer text-white font-semibold py-1.5 px-2 rounded-xl shadow-lg border-2 border-white 
                            hover:shadow-xl hover:scale-105 transition-all duration-300 button z-50"
            aria-label="Scroll to top">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>

    <script>
        function finalMockApp() {
            return {
                quizContent: null,
                isLoading: false,
                message: {
                    text: '',
                    type: 'success'
                },

                initFinalMock() {
                    this.fetchFinalMockQuestions();
                },

                async fetchFinalMockQuestions() {
                    const timeout = setTimeout(() => {
                        this.isLoading = false;
                        this.showMessage('Request timed out. Please try again.', 'error');
                    }, 10000);
                    try {
                        this.isLoading = true;
                        const res = await fetch('/api/final-mock/questions', {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        });
                        if (!res.ok) {
                            const errorData = await res.json().catch(() => ({}));
                            const message = res.status === 422 ?
                                'Not enough questions available. Please contact support or try again later.' :
                                errorData.message || `HTTP error! status: ${res.status}`;
                            throw new Error(message);
                        }
                        const data = await res.json();
                        console.log('API Response:', data);
                        if (data.success) {
                            this.quizContent = {
                                type: 'quiz',
                                title: 'Final Mock Test',
                                questions: data.questions,
                                selectedAnswers: data.questions.some(q => q.type === 'mock1') ?
                                    Array(data.questions.length).fill([]).map((_, i) => Array(this.getBlanksCount(
                                        data.questions[i])).fill('')) :
                                    {},
                                resultFeedback: null
                            };
                        } else {
                            this.showMessage(data.message || 'Failed to load questions.', 'error');
                        }
                    } catch (err) {
                        console.error('Fetch Error:', err);
                        this.showMessage(err.message, 'error');
                    } finally {
                        clearTimeout(timeout);
                        this.isLoading = false;
                    }
                },

                showMessage(text, type = 'success', duration = 3000) {
                    this.message.text = text;
                    this.message.type = type;
                    if (duration > 0) {
                        setTimeout(() => {
                            this.message.text = '';
                        }, duration);
                    }
                },

                shuffle(array) {
                    for (let i = array.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [array[i], array[j]] = [array[j], array[i]];
                    }
                    return array;
                },

                formatQuestionText(question, qIndex) {
                    if (question.type === 'mock1') {
                        let parts = question.question_text.split('_______');
                        let result = parts[0];
                        for (let i = 1; i < parts.length; i++) {
                            result += `<select name="q_${qIndex}_${i-1}" class="inline-block text-rose-700 text-lg font-bold border-2 border-rose-300 mx-3 rounded focus:ring-purple-500 focus:border-purple-500"
                                    @change="updateBlankAnswer(${qIndex}, ${i-1}, $event.target.value)">
                                    <option value="" disabled selected>Select</option>
                                    <option value="a">${question.option_a || 'Option A'}</option>
                                    <option value="b">${question.option_b || 'Option B'}</option>
                                    <option value="c">${question.option_c || 'Option C'}</option>
                                    <option value="d">${question.option_d || 'Option D'}</option>
                                    <option value="e">${question.option_e || 'Option E'}</option>
                                    <option value="f">${question.option_f || 'Option F'}</option>
                                </select>${parts[i]}`;
                        }
                        return result;
                    }
                    return question.question_text;
                },

                getBlanksCount(question) {
                    return (question.question_text.match(/_______/g) || []).length;
                },

                updateDoubleAnswer(qIndex, option) {
                    if (!this.quizContent.selectedAnswers[qIndex]) {
                        this.quizContent.selectedAnswers[qIndex] = [];
                    }
                    const index = this.quizContent.selectedAnswers[qIndex].indexOf(option);
                    if (index === -1) {
                        if (this.quizContent.selectedAnswers[qIndex].length < 2) {
                            this.quizContent.selectedAnswers[qIndex].push(option);
                        } else {
                            this.quizContent.selectedAnswers[qIndex].shift();
                            this.quizContent.selectedAnswers[qIndex].push(option);
                        }
                    } else {
                        this.quizContent.selectedAnswers[qIndex].splice(index, 1);
                    }
                },

                updateBlankAnswer(qIndex, blankIndex, value) {
                    if (!this.quizContent.selectedAnswers[qIndex]) {
                        this.quizContent.selectedAnswers[qIndex] = Array(this.getBlanksCount(this.quizContent.questions[
                            qIndex])).fill('');
                    }
                    this.quizContent.selectedAnswers[qIndex][blankIndex] = value;
                },

                getCorrectIncorrectQuestions() {
                    if (!this.quizContent?.resultFeedback) return {
                        correct: 'None',
                        incorrect: 'None'
                    };
                    const correct = [];
                    const incorrect = [];
                    this.quizContent.resultFeedback.forEach((feedback, index) => {
                        if (feedback.isCorrect) {
                            correct.push(`Q${index + 1}`);
                        } else {
                            incorrect.push(`Q${index + 1}`);
                        }
                    });
                    return {
                        correct: correct.length > 0 ? correct.join(', ') : 'None',
                        incorrect: incorrect.length > 0 ? incorrect.join(', ') : 'None'
                    };
                },

                getIncorrectMock1Feedback() {
                    if (!this.quizContent?.resultFeedback) return [];
                    const feedbackList = [];
                    this.quizContent.resultFeedback.forEach((feedback, qIndex) => {
                        if (!feedback.isCorrect && feedback.source === 'mock1') {
                            const question = this.quizContent.questions[qIndex];
                            let correctText = question.question_text;
                            let selectedText = question.question_text;
                            const correctAnswers = [question.answer_1, question.answer_2, question.answer_3].filter(
                                Boolean);
                            const selectedAnswers = feedback.selected || Array(this.getBlanksCount(question)).fill(
                                '');
                            let blankIndex = 0;
                            correctText = correctText.replace(/_______/g, () => {
                                const answer = correctAnswers[blankIndex] || '';
                                blankIndex++;
                                return `<span class="text-green-600 font-semibold">${question[`option_${answer}`] || 'Unknown'}</span>`;
                            });
                            blankIndex = 0;
                            selectedText = selectedText.replace(/_______/g, () => {
                                const answer = selectedAnswers[blankIndex] || '';
                                blankIndex++;
                                return `<span class="text-red-600 font-semibold space-x--2">${answer ? (question[`option_${answer}`] || 'Invalid Option') : 'Not Selected'}</span>`;
                            });
                            selectedText =
                                `<br><span class="text-purple-600 font-bold">You selected:</span> <span class="text-black space-x-3">${selectedText}</span>`;
                            feedbackList.push({
                                qIndex,
                                correctText,
                                selectedText
                            });
                        }
                    });
                    return feedbackList;
                },

                calculateTotalMarks(feedback) {
                    return feedback.filter(r => r.isCorrect).length;
                },

                calculateMaxMarks(questions) {
                    return questions.length;
                },

                isQuizPassed(feedback, questions) {
                    const totalMarks = this.calculateTotalMarks(feedback);
                    const maxMarks = this.calculateMaxMarks(questions);
                    return totalMarks >= 0.8 * maxMarks;
                },

                checkAnswersLocally(content) {
                    if (!content || !content.questions || !content.selectedAnswers) {
                        this.showMessage('❌ Invalid quiz data.', 'error');
                        return;
                    }
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    content.resultFeedback = content.questions.map((q, index) => {
                        if (q.type === 'mock1') {
                            const selected = content.selectedAnswers[index] || Array(this.getBlanksCount(q)).fill(
                                '');
                            const correct = [q.answer_1, q.answer_2, q.answer_3].filter(Boolean);
                            const isCorrect = selected.length === correct.length && selected.every((val, i) =>
                                val === correct[i]);
                            return {
                                unique_id: q.unique_id,
                                selected,
                                correct,
                                isCorrect,
                                source: q.source
                            };
                        } else if (q.type === 'single') {
                            const selected = content.selectedAnswers[index]?.toString().toLowerCase().trim();
                            const correct = q.answer_1?.toString().toLowerCase().trim();
                            return {
                                unique_id: q.unique_id,
                                selected,
                                correct,
                                isCorrect: selected === correct,
                                source: q.source
                            };
                        } else {
                            const selected = content.selectedAnswers[index] || [];
                            const correct = [q.answer_1, q.answer_2].filter(Boolean);
                            const isCorrect = selected.length === 2 && selected.every(val => correct.includes(val));
                            return {
                                unique_id: q.unique_id,
                                selected,
                                correct,
                                isCorrect,
                                source: q.source
                            };
                        }
                    });
                    const correctCount = content.resultFeedback.filter(r => r.isCorrect).length;
                    const incorrectCount = content.resultFeedback.length - correctCount;
                    setTimeout(() => renderChart(correctCount, incorrectCount), 100);
                }
            };
        }

        function renderChart(correct, incorrect) {
            const ctx = document.getElementById('scoreChart')?.getContext('2d');
            if (!ctx) return;
            if (window.scoreChartInstance) {
                window.scoreChartInstance.destroy();
            }
            window.scoreChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Correct', 'Incorrect'],
                    datasets: [{
                        data: [correct, incorrect],
                        backgroundColor: ['#a78bfa', '#fca5a5'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Final Mock Test Performance'
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    </script>
@endsection
