<div class="mx-auto px-4 py-6 flex flex-col lg:flex-row gap-6">
    <!-- Left Panel: Course Info and Modules -->
    <div class="lg:w-1/3 bg-white rounded-2xl shadow-lg p-6 resize-panel" id="leftPanel" x-show="!isLeftPanelHidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-x-10"
        x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-10">
        <!-- Course Header -->
        <div
            class="mb-6 bg-gradient-to-r from-purple-800 to-purple-600 text-white shadow-xl rounded-2xl p-8  transform transition hover:shadow-2xl">
            <h1 class="text-4xl font-extrabold tracking-tight" x-text="selectedCourse?.title"></h1>
            <div class="flex space-x-3 mt-2 font-bold">
                <p class="text-sm opacity-80" x-text="selectedCourse?.category"></p>
                <p class="text-sm opacity-80" x-text="selectedCourse?.level"></p>
            </div>
        </div>

        <!-- Modules List -->
        <h2 class="text-xl font-semibold text-purple-900 mb-4">Modules</h2>
        <template x-if="modules && Array.isArray(modules)">
            <template x-for="module in selectedModules" :key="module.unique_id">
                <div x-data="{ open: false }" class="border border-purple-200 rounded-lg mb-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-100 to-purple-50 px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-purple-200 transition-colors duration-200"
                        @click="open = !open">
                        <div class="flex items-center">
                            <!-- SVG Bookmark Icon -->
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v18l7-5 7 5V3H5z"></path>
                            </svg>
                            <h4 class="font-semibold text-gray-800 text-sm" x-text="module.name || 'Unnamed Module'">
                            </h4>
                        </div>
                        <!-- SVG Chevron Icon -->
                        <svg class="w-4 h-4 text-purple-600 transition-transform duration-300"
                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="px-4 py-3 bg-white border-t border-purple-200">
                        <ul class="space-y-2">
                            <li>
                                <button @click="selectModule(module)"
                                    class="flex items-center text-gray-700 hover:text-purple-700 w-full text-left transition-colors duration-200">
                                    <!-- SVG Book Icon -->
                                    <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    <span x-text="`Module: ${module.name || 'Unnamed Module'}`"></span>
                                </button>
                            </li>
                            <template x-if="module.quizzes && Array.isArray(module.quizzes)">
                                <template x-for="(quiz, index) in module.quizzes" :key="index">
                                    <li @click="selectQuiz(quiz, module)">
                                        <div class="flex items-center">
                                            <!-- SVG Question Circle Icon -->
                                            <svg class="w-5 h-5 text-purple-500 mr-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.144 2.586-2.93 2.943M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <span class="font-medium text-gray-700">Quiz — </span>
                                            <span class="text-gray-600 mx-2 font-medium"
                                                x-text="quiz.title || 'Untitled Quiz'"></span>
                                        </div>
                                    </li>
                                </template>
                            </template>
                            <template x-if="!module.quizzes || !Array.isArray(module.quizzes)">
                                <li class="text-gray-500 italic">No quizzes available.</li>
                            </template>
                        </ul>
                    </div>
                </div>
            </template>
        </template>
    </div>


    <!-- Resize Handle -->
    <div class="hidden lg:block w-2 bg-purple-300 cursor-col-resize hover:bg-purple-400 transition-colors duration-200"
        id="resizeHandle" x-show="!isLeftPanelHidden"></div>



    <!-- Right Panel: Content Display -->
    <div class="lg:flex-1 bg-white rounded-2xl shadow-lg p-6" :class="{ 'lg:w-full': isLeftPanelHidden }">

        <!-- Toggle Button -->
        <div class="lg:w-1/3 flex justify-between items-center mb-4">
            <button @click="toggleLeftPanel()"
                class="flex items-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                <span x-text="isLeftPanelHidden ? 'Show List' : 'Hide List'"></span>
                <svg class="w-5 h-5 ml-2 transition-transform duration-300" :class="{ 'rotate-180': isLeftPanelHidden }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
        </div>

        <template x-if="selectedContent">
            <div>
                <h2 class="text-2xl font-semibold text-purple-900 mb-4" x-text="selectedContent.title"></h2>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <template x-if="selectedContent.type === 'module'">
                        <div>
                            <p class="text-gray-700 mb-4"
                                x-html="selectedContent.text_description || 'No description available.'"></p>
                            <template x-if="selectedContent.video_file">
                                <div class="mb-4">
                                    <video controls class="w-full rounded-lg shadow-sm"
                                        :src="selectedContent.video_file">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            </template>
                            <button @click="markModuleComplete(selectedContent)"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                                Mark as Complete
                            </button>
                        </div>
                    </template>
                    <template x-if="selectedContent.type === 'quiz'">
                        <div>

                            <h3 class="text-lg font-semibold text-purple-800 mb-4" x-text="selectedContent.time">
                            </h3>
                            <template x-if="selectedContent.questions && selectedContent.questions.length">
                                <template x-for="(q, qIndex) in selectedContent.questions" :key="q.quiz_id || qIndex">
                                    <div class="mb-6 p-4 bg-white rounded-lg shadow-sm border border-purple-200">
                                        <p class="mb-3 font-medium text-gray-800"
                                            x-text="`${qIndex + 1}. ${q.question_text}`"></p>

                                        <!-- MCQ -->
                                        <template x-if="q.question_type === 'mcq'">
                                            <div class="space-y-2">
                                                <template x-for="option in ['a', 'b', 'c', 'd']"
                                                    :key="option">
                                                    <label
                                                        class="flex items-center space-x-2 cursor-pointer p-2 rounded transition-colors duration-200"
                                                        :class="{
                                                            'bg-green-100 text-green-800 font-semibold border border-green-400': selectedContent
                                                                .resultFeedback &&
                                                                q.correct_answer === option,
                                                            'bg-red-100 text-red-800 font-semibold border border-red-400': selectedContent
                                                                .resultFeedback &&
                                                                selectedContent.selectedAnswers[qIndex] === option &&
                                                                q.correct_answer !== option
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
                                            </div>
                                        </template>

                                        <!-- True/False -->
                                        <template x-if="q.question_type === 'true_false'">
                                            <div class="space-y-2">
                                                <template x-for="option in ['true', 'false']" :key="option">
                                                    <label
                                                        class="flex items-center space-x-2 cursor-pointer p-2 rounded transition-colors duration-200"
                                                        :class="{
                                                            'bg-green-100 text-green-800 font-semibold border border-green-400': selectedContent
                                                                .resultFeedback &&
                                                                q.correct_answer === option,
                                                            'bg-red-100 text-red-800 font-semibold border border-red-400': selectedContent
                                                                .resultFeedback &&
                                                                selectedContent.selectedAnswers[qIndex] === option &&
                                                                q.correct_answer !== option
                                                        }">
                                                        <input type="radio" :name="`q_${qIndex}`"
                                                            :value="option"
                                                            :checked="selectedContent.selectedAnswers[qIndex] === option"
                                                            @change="selectedContent.selectedAnswers[qIndex] = option"
                                                            class="form-radio text-purple-600 focus:ring-purple-500">
                                                        <span
                                                            x-text="option.charAt(0).toUpperCase() + option.slice(1)"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </template>



                                    </div>
                                </template>
                            </template>
                            <button @click="checkAnswersLocally(selectedContent)"
                                class="mt-6 w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                                Check Answers
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </template>
        <template x-if="!selectedContent">
            <div class="text-gray-500 italic flex items-center justify-center h-full">
                <!-- SVG Info Icon -->
                <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Select a module or quiz to view content.
            </div>
        </template>
    </div>

    <!-- Add this modal just before </body> -->
    <div x-show="showQuizModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-white/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

        <div class="bg-white rounded-xl shadow-2xl max-w-xl w-full p-6 border border-purple-100">
            <h2 class="text-2xl font-extrabold text-purple-800 mb-4 flex items-center">
                📝 <span class="ml-2" x-text="selectedQuizTitle"></span>
            </h2>

            <div class="space-y-4 text-gray-700 text-sm leading-relaxed">
                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">📌</span>
                    <p><strong>Answer all questions:</strong> Unanswered questions will be marked incorrect.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">🔘</span>
                    <p><strong>Multiple Choice:</strong> Select one answer. Each answer can be saved only once.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">✅</span>
                    <p><strong>True/False:</strong> Select one answer. Answers are final once saved.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">⚠️</span>
                    <p>Be careful using a mouse scroll wheel — it might change your answer unintentionally.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">💾</span>
                    <p>Click <strong>Save Answer</strong> to submit your response for each question.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">📤</span>
                    <p>Use the <strong>Submit</strong> button to finalize and grade your quiz.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">📈</span>
                    <p>Results will appear in your profile under <strong>Courses → Results</strong>.</p>
                </div>

                <div class="flex items-start">
                    <span class="text-purple-500 mr-2">🔁</span>
                    <p>To retake the quiz, contact your instructor for a reset.</p>
                </div>

                <div class="flex items-start font-semibold text-purple-700">
                    ⏳ <p class="ml-2">You have <span x-text="countdownStart"></span> seconds to complete the quiz
                        once started.</p>
                </div>
            </div>

            <button @click="startQuiz()"
                class="w-full mt-6 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                Start Quiz
            </button>
        </div>
    </div>

</div>

<style>
    /* Custom styles for the resizable panel */
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

    /* Smooth transitions for radio buttons */
    .form-radio:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.3);
    }
</style>

<script>
    function continueCourseApp() {
        return {
            showQuizModal: false,
            countdownStart: 0,
            countdownTimer: null,
            selectedQuizTitle: '',
            courses: [],
            selectedCourse: null,
            selectedCourseDetails: null,
            selectedModules: [],
            currentCourseId: null,
            course: {},
            modules: [],
            selectedContent: null,
            selectedQuizAnswer: null,
            error: '',
            isLeftPanelHidden: false,

            initContinueCourse(unique_id) {
                this.currentCourseId = unique_id;
                this.fetchCourseDetails(unique_id);
                this.initResize();
            },

            toggleLeftPanel() {
                this.isLeftPanelHidden = !this.isLeftPanelHidden;
            },

            async fetchCourseDetails(unique_id) {
                try {
                    const res = await fetch(`/api/learner/course-details/${unique_id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.currentCourseId = unique_id; // ✅ Set course ID here
                        this.modules = data.modules;
                        this.selectedModules = data.modules;
                    } else {
                        this.error = data.message || 'Course not found.';
                    }
                } catch (err) {
                    this.error = 'Failed to load course details.';
                }
            },

            selectModule(module) {
                this.selectedContent = {
                    type: 'module',
                    title: module.name,
                    text_description: module.text_description,
                    video_file: module.video_file,
                    unique_id: module.unique_id
                };
            },

            selectQuiz(quizGroup, module) {
                this.selectedQuizRaw = quizGroup;
                this.selectedQuizModuleId = module.unique_id;
                this.selectedQuizTitle = quizGroup.title;

                // Ensure the time is a valid number and fallback to 60 seconds if not
                const time = parseInt(quizGroup.time);
                this.countdownStart = Number(quizGroup.time) > 0 ? Number(quizGroup.time) * 60 : 600;
                this.showQuizModal = true;
            },


            startQuiz() {
                this.showQuizModal = false;

                const duration = Number(this.countdownStart); // in seconds
                const endTime = Date.now() + duration * 1000;

                this.selectedContent = {
                    type: 'quiz',
                    title: this.selectedQuizRaw.title,
                    time: this.formatTime(duration),
                    questions: this.selectedQuizRaw.questions,
                    module_unique_id: this.selectedQuizModuleId,
                    selectedAnswers: {}
                };

                const updateTimer = () => {
                    const now = Date.now();
                    const remaining = Math.max(0, Math.round((endTime - now) / 1000));
                    this.selectedContent.time = this.formatTime(remaining);

                    if (remaining > 0) {
                        requestAnimationFrame(updateTimer); // more stable than setInterval
                    } else {
                        alert('Time is up! Submitting your answers...');
                        this.submitBatchQuizAnswers(this.selectedContent);
                    }
                };

                requestAnimationFrame(updateTimer);
            },




            formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            },


            selectQuizOption(option) {
                this.selectedQuizAnswer = option;
            },

            async markModuleComplete(module) {
                try {
                    const res = await fetch(`/api/learner/course/${this.currentCourseId}/progress`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            type: 'module',
                            unique_id: module.unique_id,
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert('Module marked as complete!');
                        this.selectedContent = null;
                    } else {
                        this.error = data.message || 'Failed to update progress.';
                    }
                } catch (err) {
                    this.error = 'Something went wrong.';
                }
            },

            async submitQuizAnswer(quiz) {
                if (!this.selectedQuizAnswer) {
                    alert('Please select an answer.');
                    return;
                }
                try {
                    const res = await fetch(`/api/learner/course/${this.currentCourseId}/progress`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            type: 'quiz',
                            unique_id: quiz.unique_id,
                            module_unique_id: quiz.module_unique_id,
                            answer: this.selectedQuizAnswer
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(data.correct ? 'Correct answer!' : 'Incorrect answer. Try again!');
                        if (data.correct) {
                            this.selectedContent = null;
                            this.selectedQuizAnswer = null;
                        }
                    } else {
                        this.error = data.message || 'Failed to submit quiz.';
                    }
                } catch (err) {
                    this.error = 'Something went wrong.';
                }
            },

            initResize() {
                const leftPanel = document.getElementById('leftPanel');
                const resizeHandle = document.getElementById('resizeHandle');
                let isResizing = false;

                resizeHandle.addEventListener('mousedown', (e) => {
                    isResizing = true;
                    document.body.style.cursor = 'col-resize';
                });

                document.addEventListener('mousemove', (e) => {
                    if (!isResizing) return;
                    const container = leftPanel.parentElement;
                    const containerRect = container.getBoundingClientRect();
                    const newWidth = e.clientX - containerRect.left;
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
            },

            init() {
                this.fetchCourseDetails();
            },

            checkAnswersLocally(content) {
                if (!content || !content.questions || !content.selectedAnswers) {
                    alert("Invalid quiz data.");
                    return;
                }

                content.resultFeedback = content.questions.map((q, index) => {
                    const qId = q.quiz_id || index;
                    const userAnswer = content.selectedAnswers[qId] || content.selectedAnswers[index];
                    const correctAnswer = q.correct_answer?.toLowerCase()?.trim();
                    const isCorrect = userAnswer?.toLowerCase()?.trim() === correctAnswer;

                    return {
                        question_id: qId,
                        selected: userAnswer,
                        correct: correctAnswer,
                        isCorrect
                    };
                });

                // This will trigger re-rendering with color classes (see below)
            },


            async submitBatchQuizAnswers(content) {
                // Ensure course ID is available
                if (!this.currentCourseId) {
                    alert('Course ID is missing. Please reload or reselect the course.');
                    return;
                }

                // Check if answers exist
                if (!content || !content.selectedAnswers || Object.keys(content.selectedAnswers).length === 0) {
                    alert('Please answer at least one question before submitting.');
                    return;
                }

                // Format the answers for submission
                const answersArray = content.questions.map((q, index) => {
                    const answer = content.selectedAnswers[q.quiz_id]; // for true/false
                    const fallbackAnswer = content.selectedAnswers[index]; // for MCQ fallback
                    return {
                        question_id: q.quiz_id || q.id || q.question_id, // Ensure proper ID is used
                        answer: answer || fallbackAnswer || '' // fallback in case mapping is by index
                    };
                }).filter(ans => ans.answer); // remove unanswered

                // Prevent empty submissions
                if (answersArray.length === 0) {
                    alert('No valid answers found. Please try again.');
                    return;
                }

                try {
                    const res = await fetch(`/learner/course/${this.currentCourseId}/progress/batch`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            type: 'quiz_batch',
                            module_unique_id: content.module_unique_id,
                            answers: answersArray
                        })
                    });

                    const data = await res.json();

                    if (data.success) {
                        const correct = data.results.filter(r => r.is_correct).length;
                        const total = data.results.length;
                        alert(`✅ You got ${correct} out of ${total} correct.`);

                        // Optionally reset view
                        this.selectedContent = null;
                    } else {
                        alert(`❌ Submission failed: ${data.message || 'Unknown error'}`);
                    }
                } catch (err) {
                    console.error('Error submitting quiz:', err);
                    alert('🚫 Failed to submit answers. Please try again later.');
                }
            },


            async showContinueCourse(unique_id) {
                try {
                    const res = await fetch(`/api/learner/course/${unique_id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.selectedCourse = data.course;
                        this.selectedCourseDetails = data.course_details;
                        this.selectedModules = data.modules;

                        document.getElementById('learnerCoursePanel').classList.add('hidden');
                        document.getElementById('learnerCourseDetails').classList.add('hidden');
                        document.getElementById('learnerContinueCourse').classList.remove('hidden');
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        this.error = data.message || 'Course not found.';
                    }
                } catch (err) {
                    this.error = 'Failed to load course details.';
                }
            }
        };
    }
</script>
