@extends('home.default')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>

    <style>
        .resize-panel {
            min-width: 250px;
            max-width: 500px;
            position: relative;
        }

        #resizeHandle {
            user-select: none;
        }

        @media (max-width: 1023px) {
            #resizeHandle {
                display: none;
            }

            .resize-panel {
                width: 100%;
                max-width: none;
            }
        }

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

    <div class="mx-auto px-4 py-6 flex flex-col lg:flex-row gap-6" x-data="continueCourseApp()" x-init="initContinueCourse('{{ $unique_id }}')">
        <!-- Left Panel: Course Info and Sections -->
        <div class="lg:w-1/3 bg-white rounded-2xl shadow-lg p-6 resize-panel" id="leftPanel" x-show="!isLeftPanelHidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-x-10"
            x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform -translate-x-10">

            <div class="flex items-center mb-6">
                <button onclick="window.location.href='/learner/page'"
                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg border-2 border-white 
                               hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer button">
                    <svg class="w-5 h-5 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </button>
            </div>

            <div class="sticky top-4 space-y-4">
                <!-- Course Header -->
                <div
                    class="mb-6 bg-gradient-to-r from-purple-800 to-purple-600 text-white shadow-xl rounded-2xl p-8 transform transition hover:shadow-2xl">
                    <h1 class="text-xl font-extrabold tracking-tight" x-text="selectedCourse?.title || 'Loading Course...'">
                    </h1>
                </div>

                


                <!-- Sections List -->
                <template x-if="sections && Array.isArray(sections) && sections.length > 0">
                    <template x-for="section in sections" :key="section.unique_id">
                        <div x-data="{ open: false }" class="border border-purple-200 rounded-lg mb-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-100 to-purple-50 px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-purple-200 transition-colors duration-200"
                                @click="open = !open">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-2 svg-animation" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v18l7-5 7 5V3H5z"></path>
                                    </svg>
                                    <h4 class="font-semibold text-gray-800 text-sm"
                                        x-text="section.name || 'Unnamed Section'"></h4>
                                </div>
                                <svg class="w-4 h-4 text-purple-600 transition-transform duration-300"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div x-show="open" class="px-4 py-3 bg-white border-t border-purple-200">
                                <ul class="space-y-2">
                                    <li>
                                        <button @click="selectSection(section)"
                                            class="flex items-center text-gray-700 hover:text-purple-700 w-full text-left transition-colors duration-200">
                                            <svg class="w-5 h-5 text-purple-500 mr-2 svg-animation" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <span x-text="`Section: ${section.name || 'Unnamed Section'}`"></span>
                                        </button>
                                    </li>
                                    <template
                                        x-if="section.quizzes && Array.isArray(section.quizzes) && section.quizzes.length > 0">
                                        <template x-for="(quiz, index) in section.quizzes" :key="index">
                                            <li>
                                                <button @click="selectQuiz(quiz, section)"
                                                    class="flex items-center justify-between text-gray-700 hover:text-purple-700 w-full text-left transition-colors duration-200">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="w-5 h-5 text-purple-500 svg-animation" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.144 2.586-2.93 2.943M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-purple-600 font-semibold cursor-pointer">
                                                            <span x-text="quiz.title || 'Untitled Quiz'"
                                                                class="text-gray-700 font-medium hover:text-purple-600"></span>
                                                        </span>
                                                    </div>
                                                </button>
                                            </li>
                                        </template>
                                    </template>
                                    <template
                                        x-if="!section.quizzes || !Array.isArray(section.quizzes) || section.quizzes.length === 0">
                                        <li class="text-gray-500 italic">No quizzes available.</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>
                </template>
                <template x-if="!sections || !Array.isArray(sections) || sections.length === 0">
                    <div class="text-gray-500 italic flex items-center justify-center h-full">
                        <svg class="w-6 h-6 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        No sections available for this course.
                    </div>
                </template>
            </div>
        </div>

        <!-- Resize Handle -->
        <div class="hidden lg:block w-2 bg-purple-300 cursor-col-resize hover:bg-purple-400 transition-colors duration-200"
            id="resizeHandle" x-show="!isLeftPanelHidden"></div>

        <!-- Right Panel: Content Display -->
        <div class="lg:flex-1 bg-white rounded-2xl shadow-lg p-6" :class="{ 'lg:w-full': isLeftPanelHidden }">
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
            <div x-show="isRightPanelLoading" class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-purple-600 svg-animation" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="mt-2 text-gray-600">Loading...</p>
                </div>
            </div>

            <!-- Toggle Button -->
            <div class="lg:w-1/3 flex justify-between items-center mb-4">
                <button @click="toggleLeftPanel()"
                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg border-2 border-white 
                               hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer button">
                    <span x-text="isLeftPanelHidden ? 'Show List' : 'Hide List'"></span>
                    <svg class="w-5 h-5 ml-2 transition-transform duration-300 svg-icon"
                        :class="{ 'rotate-180': isLeftPanelHidden }" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Quiz Feedback Section -->
            <template x-if="selectedContent?.resultFeedback">
                <div
                    class="mt-8 p-6 bg-gradient-to-br from-purple-100 to-purple-50 border border-purple-200 rounded-2xl shadow-xl">
                    <h3 class="text-xl font-bold text-purple-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-purple-600 mr-2 svg-animation" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 19" />
                        </svg>
                        Quiz Results Summary
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl shadow-md p-4 flex flex-col text-purple-800">
                            <div class="text-4xl font-extrabold text-center"
                                x-text="calculateTotalMarks(selectedContent.resultFeedback) + '/' + selectedContent.questions.length">
                            </div>
                            <div class="text-sm uppercase tracking-wide text-gray-500 text-center">Total Marks</div>
                            <div class="text-xs text-gray-400 mt-1 text-center">
                                Questions: <span x-text="selectedContent.resultFeedback.length"></span>
                            </div>
                            <div class="mt-3 text-center">
                                <template x-if="isQuizPassed(selectedContent.resultFeedback, selectedContent.questions)">
                                    <div class="text-green-600 font-semibold">🎉 Congratulations, you passed the quiz!
                                    </div>
                                </template>
                                <template x-if="!isQuizPassed(selectedContent.resultFeedback, selectedContent.questions)">
                                    <div class="text-red-600 font-semibold">❌ You need at least 80% to pass. Try again!
                                    </div>
                                </template>
                            </div>
                            <div class="mt-3">
                                <div class="flex">
                                    <p class="text-purple-700"><strong>Correct:</strong></p>
                                     <span class="text-black font-bold mx-2" x-text="getCorrectIncorrectQuestions().correct || 'None'"></span>
                                </div>
                                <div class="flex">
                                    <p class="text-rose-700"><strong>Incorrect:</strong></p>
                                     <span class="text-black font-bold mx-2" x-text="getCorrectIncorrectQuestions().incorrect || 'None'"></span>
                                </div>
                            </div>
                            <template
                                x-if="selectedContent.quiz_type === 'mock1' && getIncorrectMock1Feedback().length > 0">
                                <div class="mt-3 text-sm text-gray-700">
                                    <p class="font-semibold text-red-600">Incorrect Answers:</p>
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
                                <button @click="startQuiz()"
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

            <!-- Content Display -->
            <template x-if="selectedContent">
                <div>
                    <h2 class="text-2xl font-semibold text-purple-900 mb-4 mt-8" x-text="selectedContent.title"></h2>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <template x-if="selectedContent.type === 'section'">
                            <div>
                                <p class="text-gray-700 mb-4"
                                    x-html="selectedContent.text_description || 'No description available.'"></p>
                                <template x-if="selectedContent.video_file">
                                    <video controls controlsList="nodownload" class="w-full rounded-lg shadow-sm"
                                        :src="selectedContent.video_file"></video>
                                </template>
                                <template x-if="selectedContent.text_file">
                                    <iframe class="w-full h-screen max-h-auto rounded-lg border shadow-sm"
                                        :src="selectedContent.text_file" frameborder="0"></iframe>
                                </template>
                            </div>
                        </template>
                        <template x-if="selectedContent.type === 'quiz'">
                            <div>
                                <template
                                    x-if="selectedContent.questions && Array.isArray(selectedContent.questions) && selectedContent.questions.length > 0">
                                    <template x-for="(q, qIndex) in selectedContent.questions"
                                        :key="q.unique_id || qIndex">
                                        <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-purple-200">
                                            <div class="flex justify-between items-center mb-3">
                                                <div class="flex gap-2">
                                                    <p class="font-bold text-lg text-purple-600"
                                                        x-html="`Q${qIndex + 1}:`"></p>
                                                    <span class="text font-bold space-x-2 space-y-4"
                                                        x-html="`${formatQuestionText(q, qIndex)}`"> </span>
                                                </div>
                                                <template x-if="selectedContent.resultFeedback">
                                                    <span>
                                                        <template x-if="selectedContent.resultFeedback[qIndex]?.isCorrect">
                                                            <svg class="w-10 h-10 text-green-600 font-bold" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </template>
                                                        <template
                                                            x-if="!selectedContent.resultFeedback[qIndex]?.isCorrect">
                                                            <svg class="w-10 h-10 text-red-500 font-bold" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </template>
                                                    </span>
                                                </template>

                                            </div>

                                            <template
                                                x-if="selectedContent.quiz_type === 'practice' || selectedContent.quiz_type === 'mock2'">
                                                <div class="space-y-2">
                                                    <template x-if="q.type === 'single'">
                                                        <template x-for="option in ['a', 'b', 'c']"
                                                            :key="option">
                                                            <label
                                                                class="flex items-center space-x-2 cursor-pointer p-2 rounded transition-colors duration-200"
                                                                :class="{
                                                                    'border border-green-400 bg-green-100 text-green-800 font-semibold': selectedContent
                                                                        .resultFeedback && selectedContent
                                                                        .resultFeedback[qIndex]?.isCorrect &&
                                                                        selectedContent.selectedAnswers[qIndex] ===
                                                                        option,
                                                                    'border border-red-400 bg-red-100 text-red-800 font-semibold': selectedContent
                                                                        .resultFeedback && !selectedContent
                                                                        .resultFeedback[qIndex]?.isCorrect &&
                                                                        selectedContent.selectedAnswers[qIndex] ===
                                                                        option
                                                                }">
                                                                <input type="radio" :name="`q_${qIndex}`"
                                                                    :value="option"
                                                                    :checked="selectedContent.selectedAnswers[qIndex] === option"
                                                                    @change="selectedContent.selectedAnswers[qIndex] = option"
                                                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                                                <span
                                                                    x-text="option.toUpperCase() + '. ' + (q['option_' + option] || '')"></span>
                                                            </label>
                                                        </template>
                                                    </template>
                                                    <template x-if="q.type === 'double'">
                                                        <template x-for="option in ['a', 'b', 'c']"
                                                            :key="option">
                                                            <label
                                                                class="flex items-center space-x-2 cursor-pointer p-2 rounded transition-colors duration-200"
                                                                :class="{
                                                                    'border border-green-400 bg-green-100 text-green-800 font-semibold': selectedContent
                                                                        .resultFeedback && selectedContent
                                                                        .resultFeedback[qIndex]?.selected.includes(
                                                                            option) && selectedContent.resultFeedback[
                                                                            qIndex]?.isCorrect,
                                                                    'border border-red-400 bg-red-100 text-red-800 font-semibold': selectedContent
                                                                        .resultFeedback && selectedContent
                                                                        .resultFeedback[qIndex]?.selected.includes(
                                                                            option) && !selectedContent.resultFeedback[
                                                                            qIndex]?.isCorrect
                                                                }">
                                                                <input type="checkbox" :name="`q_${qIndex}`"
                                                                    :value="option"
                                                                    :checked="selectedContent.selectedAnswers[qIndex]?.includes(
                                                                        option)"
                                                                    @change="updateDoubleAnswer(qIndex, option)"
                                                                    class="form-checkbox text-purple-600 focus:ring-purple-500">
                                                                <span
                                                                    x-text="option.toUpperCase() + '. ' + (q['option_' + option] || '')"></span>
                                                            </label>
                                                        </template>
                                                    </template>
                                                    <template
                                                        x-if="selectedContent.resultFeedback && !selectedContent.resultFeedback[qIndex]?.isCorrect">
                                                        <div class="mt-4 flex items-start space-x-2">
                                                            <span
                                                                class="font-bold bg-pink-600 rounded-lg p-2 text-white text-sm">Hints:</span>
                                                            <div class="text-rose-600 font-bold mt-1" x-text="q.incorrect">
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>     
                                        </div>
                                    </template>
                                </template>
                                <template
                                    x-if="selectedContent.quiz_type && (!selectedContent.questions || !Array.isArray(selectedContent.questions) || selectedContent.questions.length === 0)">
                                    <div class="text-gray-500 italic flex items-center justify-center h-full">
                                        <svg class="w-6 h-6 text-purple-500 mr-2 svg-animation" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        No questions available for this quiz.
                                    </div>
                                </template>
                                <button @click="checkAnswersLocally(selectedContent)"
                                    class="mt-6 w-full bg-purple-600 cursor-pointer hover:bg-purple-700 text-white font-semibold py-1.5 px-4 rounded-xl shadow-lg border-2 border-white 
                                    hover:shadow-xl hover:scale-95 transition-all duration-300 button"
                                    x-show="!selectedContent.resultFeedback && selectedContent.questions && selectedContent.questions.length > 0">
                                    <span>Check Answers</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template x-if="!selectedContent">
                <div class="text-gray-500 italic flex items-center justify-center h-full">
                    <svg class="w-6 h-6 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Select a section or quiz to view content.
                </div>
            </template>

            <!-- Quiz Modal -->
            <div x-show="showQuizModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-white/50 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-xl shadow-2xl max-w-xl w-full p-6 border border-purple-100">
                    <h2 class="text-2xl font-extrabold text-purple-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-purple-600 mr-2 svg-animation" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.144 2.586-2.93 2.943M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="selectedQuizTitle || 'Quiz'"></span>
                    </h2>
                    <div class="space-y-4 text-gray-700 text-sm leading-relaxed">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p><strong>Answer all questions:</strong> Unanswered questions will be marked incorrect.</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <p><strong>Practice/Mock Test 2 (Single):</strong> Select one answer.</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <p><strong>Practice/Mock Test 2 (Double):</strong> Select two answers.</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <p><strong>Mock Test 1:</strong> Select options for fill-in-the-blank questions.</p>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-500 mr-2 svg-animation" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Be careful using a mouse scroll wheel — it might change your answer unintentionally.</p>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-center gap-4">
                        <button @click="startQuiz()"
                            class="bg-purple-700 hover:bg-purple-800 cursor-pointer text-white font-bold py-2 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition-all duration-300 button">
                            <span>🚀 Start Quiz</span>
                        </button>
                        <button @click="showQuizModal = false"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-xl transform hover:-translate-y-1 transition-all duration-300 button">
                            <span>✖ Cancel</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Scroll to Top Button -->
                <button
                    x-data="{ show: false }"
                    x-show="show"
                    x-transition
                    x-init="window.addEventListener('scroll', () => show = window.scrollY > 300)"
                    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="fixed bottom-6 right-6 bg-gradient-to-br from-rose-500 to-amber-700 hover:bg-purple-800 cursor-pointer text-white font-semibold py-1.5 px-2 rounded-xl shadow-lg border-2 border-white 
                                    hover:shadow-xl hover:scale-105 transition-all duration-300 button z-50"
                    aria-label="Scroll to top"
                >
                    <!-- Up Arrow SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

        </div>
    </div>

    <script>
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function continueCourseApp() {
            return {
                showQuizModal: false,
                selectedQuizTitle: '',
                selectedQuizType: '',
                selectedQuizSectionId: null,
                selectedCourse: null,
                sections: [],
                selectedContent: null,
                error: '',
                isLeftPanelHidden: false,
                isRightPanelLoading: false,
                message: {
                    text: '',
                    type: 'success'
                },

                initContinueCourse(unique_id) {
                    this.currentCourseId = unique_id;
                    this.fetchCourseDetails(unique_id);
                    this.initResize();
                },

                toggleLeftPanel() {
                    this.isLeftPanelHidden = !this.isLeftPanelHidden;
                },

                shuffle(array) {
                    for (let i = array.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [array[i], array[j]] = [array[j], array[i]];
                    }
                    return array;
                },

                async fetchCourseDetails(unique_id) {
                    const timeout = setTimeout(() => {
                        this.isRightPanelLoading = false;
                        this.showMessage('Request timed out. Please try again.', 'error');
                    }, 10000);
                    try {
                        this.isRightPanelLoading = true;
                        const res = await fetch(`/api/learner/course/${unique_id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        });
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        const data = await res.json();
                        console.log('API Response:', data);
                        if (data.success) {
                            this.currentCourseId = unique_id;
                            this.selectedCourse = data.course;
                            this.sections = data.sections;
                        } else {
                            this.error = data.message || 'Course not found.';
                            this.showMessage(this.error, 'error');
                        }
                    } catch (err) {
                        console.error('Fetch Error:', err);
                        this.error = 'Failed to load course details: ' + err.message;
                        this.showMessage(this.error, 'error');
                    } finally {
                        clearTimeout(timeout);
                        this.isRightPanelLoading = false;
                    }
                },

                selectSection(section) {
                    console.log('Selected Section:', section);
                    this.selectedContent = {
                        type: 'section',
                        title: section.name,
                        text_description: section.text_description || 'No description available.',
                        video_file: section.video_file,
                        text_file: section.text_file,
                        unique_id: section.unique_id
                    };
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

                selectQuiz(quiz, section) {
                    console.log('Selected Quiz:', quiz, 'Section:', section);
                    this.selectedQuizRaw = quiz;
                    this.selectedQuizSectionId = section.unique_id;
                    this.selectedQuizTitle = quiz.title || 'Untitled Quiz';
                    this.selectedQuizType = quiz.type;
                    this.showQuizModal = true;
                },

                startQuiz() {
                    console.log('Starting Quiz:', this.selectedQuizRaw);
                    this.showQuizModal = false;
                    if (!this.selectedQuizRaw || !this.selectedQuizRaw.questions || !Array.isArray(this.selectedQuizRaw
                            .questions) || this.selectedQuizRaw.questions.length === 0) {
                        this.showMessage('❌ No questions available for this quiz.', 'error');
                        return;
                    }
                    this.selectedContent = {
                        type: 'quiz',
                        quiz_type: this.selectedQuizType,
                        title: this.selectedQuizRaw.title || 'Untitled Quiz',
                        questions: this.shuffle([...this.selectedQuizRaw.questions]),
                        section_unique_id: this.selectedQuizSectionId,
                        selectedAnswers: this.selectedQuizType === 'mock1' ? Array(this.selectedQuizRaw.questions
                            .length).fill([]).map((_, i) => Array(this.getBlanksCount(this.selectedQuizRaw
                            .questions[i])).fill('')) : {}
                    };
                    console.log('Selected Content for Quiz:', this.selectedContent);
                },

                formatQuestionText(question, qIndex) {
                    if (this.selectedContent.quiz_type === 'mock1') {
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
                    if (!this.selectedContent.selectedAnswers[qIndex]) {
                        this.selectedContent.selectedAnswers[qIndex] = [];
                    }
                    const index = this.selectedContent.selectedAnswers[qIndex].indexOf(option);
                    if (index === -1) {
                        if (this.selectedContent.selectedAnswers[qIndex].length < 2) {
                            this.selectedContent.selectedAnswers[qIndex].push(option);
                        } else {
                            this.selectedContent.selectedAnswers[qIndex].shift();
                            this.selectedContent.selectedAnswers[qIndex].push(option);
                        }
                    } else {
                        this.selectedContent.selectedAnswers[qIndex].splice(index, 1);
                    }
                },

                updateBlankAnswer(qIndex, blankIndex, value) {
                    console.log(`Updating answer for qIndex: ${qIndex}, blankIndex: ${blankIndex}, value: ${value}`);
                    if (!this.selectedContent.selectedAnswers[qIndex]) {
                        this.selectedContent.selectedAnswers[qIndex] = Array(this.getBlanksCount(this.selectedContent
                            .questions[qIndex])).fill('');
                    }
                    this.selectedContent.selectedAnswers[qIndex][blankIndex] = value;
                    console.log('Updated selectedAnswers:', this.selectedContent.selectedAnswers);
                },

                getCorrectIncorrectQuestions() {
                    if (!this.selectedContent?.resultFeedback) return {
                        correct: 'None',
                        incorrect: 'None'
                    };
                    const correct = [];
                    const incorrect = [];
                    this.selectedContent.resultFeedback.forEach((feedback, index) => {
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
                    if (!this.selectedContent?.resultFeedback || this.selectedContent.quiz_type !== 'mock1') return [];
                    const feedbackList = [];
                    this.selectedContent.resultFeedback.forEach((feedback, qIndex) => {
                        if (!feedback.isCorrect) {
                            const question = this.selectedContent.questions[qIndex];
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
                    console.log('Checking answers with selectedAnswers:', content.selectedAnswers);
                    content.resultFeedback = content.questions.map((q, index) => {
                        if (content.quiz_type === 'mock1') {
                            const selected = content.selectedAnswers[index] || Array(this.getBlanksCount(q)).fill(
                                '');
                            const correct = [q.answer_1, q.answer_2, q.answer_3].filter(Boolean);
                            const isCorrect = selected.length === correct.length && selected.every((val, i) =>
                                val === correct[i]);
                            console.log(
                                `Q${index + 1}: selected=${selected}, correct=${correct}, isCorrect=${isCorrect}`
                            );
                            return {
                                unique_id: q.unique_id,
                                selected,
                                correct,
                                isCorrect
                            };
                        } else if (q.type === 'single') {
                            const selected = content.selectedAnswers[index]?.toString().toLowerCase().trim();
                            const correct = q.answer_1?.toString().toLowerCase().trim();
                            return {
                                unique_id: q.unique_id,
                                selected,
                                correct,
                                isCorrect: selected === correct
                            };
                        } else {
                            const selected = content.selectedAnswers[index] || [];
                            const correct = [q.answer_1, q.answer_2].filter(Boolean);
                            const isCorrect = selected.length === 2 && selected.every(val => correct.includes(val));
                            return {
                                unique_id: q.unique_id,
                                selected,
                                correct,
                                isCorrect
                            };
                        }
                    });
                    const correctCount = content.resultFeedback.filter(r => r.isCorrect).length;
                    const incorrectCount = content.resultFeedback.length - correctCount;
                    setTimeout(() => renderChart(correctCount, incorrectCount), 100);
                },

                initResize() {
                    const leftPanel = document.getElementById('leftPanel');
                    const resizeHandle = document.getElementById('resizeHandle');
                    let isResizing = false;
                    resizeHandle.addEventListener('mousedown', (e) => {
                        isResizing = true;
                        document.body.style.cursor = 'col-resize';
                    });
                    document.addEventListener('mousemove', (event) => {
                        if (!isResizing) return;
                        const container = leftPanel.parentElement;
                        const containerRect = container.getBoundingClientRect();
                        const newWidth = event.clientX - containerRect.left;
                        const minWidth = 250;
                        const maxWidth = 500;
                        if (newWidth >= minWidth && newWidth <= maxWidth) {
                            leftPanel.style.width = `${newWidth}px`;
                        }
                    });
                    document.addEventListener('mouseup', () => {
                        isResizing = false;
                        document.body.style.cursor = 'default';
                    });
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
                            text: 'Quiz Performance'
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    </script>
@endsection
