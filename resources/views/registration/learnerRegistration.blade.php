@extends('home.default')

@section('content')
    <style>
        body {
            background-image: url('{{ asset('image/regi-bg-2.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }

        .speech-bubble {
            position: absolute;
            background: linear-gradient(135deg, #6a0dad, #8e2de2);
            color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-10px);
            animation: float 1.5s infinite ease-in-out;
            z-index: 10;
        }

        .speech-bubble:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border: 10px solid transparent;
            border-top-color: #10b981;
        }

        .avatar {
            transition: all 0.3s ease;
            position: relative;
        }

        .avatar-active {
            transform: scale(1.15) rotate(3deg);
            filter: drop-shadow(0 0 12px rgba(16, 185, 129, 0.8));
        }

        .input-glow {
            transition: all 0.3s ease;
        }

        .input-glow:focus {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        .register-btn {
            transition: all 0.3s ease;
        }

        .wave-bg {
            position: absolute;
            top: 0;
            left: 0px;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .lap-img {
            position: absolute;
            top: 45%;
            left: 60%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 920px;
            z-index: 1;
        }

        .country-select {
            position: absolute;
            left: 2px;
            top: 20%;
            transform: translateY(-30%);
            width: 100px;
            background: transparent;
            border: none;
            outline: none;
            color: #3b82f6;
        }

        .phone-input {
            padding-left: 110px !important;
        }

        .show-password-checkbox {
            display: flex;
            align-items: center;
            margin-top: 5px;
            color: #3b82f6;
            font-size: 0.85rem;
        }

        .show-password-checkbox input {
            margin-right: 5px;
        }

        .question-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.5rem;
            padding: 8px;
            margin-bottom: 1rem;
        }

        .question-select {
            color: black;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .question-select:hover {
            transform: scale(1.05);
        }

        .word-count {
            font-size: 0.85rem;
            color: #3b82f6;
            margin-top: 0.5rem;
        }
    </style>

    <div class="w-auto flex justify-center items-center text-center md:flex-row">
        <!-- Registration Form -->
        <div x-data="registrationForm()" class="lg:w-[400px] sm:w-auto h-[550px] max-h-min mt-30 p-6">
            <!-- Avatar + Bubble -->
            <div class="relative mb-4 flex flex-col items-center">
                <div x-show="showBubble" x-transition:enter="transition ease-out duration-300"
                    x-transition:leave="transition ease-in duration-200"
                    class="speech-bubble p-3 w-auto text-sm absolute -top-16">
                    <p x-text="feedbackMessage" class="z-10 relative"></p>
                    <div class="thought-dots absolute left-1/2 transform -translate-x-1/2 top-full mt-1 space-x-1 flex">
                        <span class="dot w-2 h-2 bg-emerald-400 rounded-full opacity-80"></span>
                        <span class="dot w-1.5 h-1.5 bg-blue-400 rounded-full opacity-60"></span>
                        <span class="dot w-1 h-1 bg-blue-300 rounded-full opacity-40"></span>
                    </div>
                </div>

                <div class="avatar w-32 h-20 rounded-full bg-white flex items-center justify-center text-5xl">
                    <span x-bind:class="{ 'avatar-active': isTyping }">🧙‍♂️</span>
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center text-white mb-6">
                <h1 class="text-xl font-extrabold font-orbitron bg-white bg-clip-text text-transparent">
                    Join Our Learning Community
                </h1>
                <div class="flex items-center text-center justify-center">
                    <p class="text-white text-sm">Start your journey with <strong>Course Cave</strong></p>
                    <img src="{{ asset('image/favicon.png') }}" class="h-8 w-8" />
                </div>
            </div>

            <!-- Full Name -->
            <div class="mb-4 relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500">
                    <i class="fas fa-user"></i>
                </span>
                <input x-model="fullName" @input="updateNameFeedback()" @focus="isTyping = true; currentField = 'name'"
                    @blur="isTyping = false" type="text"
                    class="input-glow w-full pl-10 pr-4 py-3 rounded-lg bg-white border-b-4 border-blue-800 text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                    placeholder="Your full name">
            </div>

            <!-- Email -->
            <div class="mb-4 relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500">
                    <i class="fas fa-envelope"></i>
                </span>
                <input x-model="email" @input="updateEmailFeedback()" @focus="isTyping = true; currentField = 'email'"
                    @blur="isTyping = false" type="email"
                    class="input-glow w-full pl-10 pr-4 py-3 rounded-lg bg-white border-b-4 border-blue-800 text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                    placeholder="Your email address">
            </div>

            <!-- Phone Number with Country Select -->
            <div class="mb-4 relative">
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500 flex items-center">
                    <select x-model="countryCode" @change="updateCountryFeedback()"
                        @focus="isTyping = true; currentField = 'phone'; showCountryMessage = true" class="country-select">
                        @include('learner.countryCode')
                    </select>
                </div>
                <input x-model="phoneNumber" @input="updatePhoneFeedback()"
                    @focus="isTyping = true; currentField = 'phone'; showCountryMessage = true"
                    @blur="isTyping = false; showCountryMessage = false" type="tel"
                    class="input-glow phone-input w-full pl-10 pr-4 py-3 rounded-lg bg-white border-b-4 border-blue-800 text-black text-center placeholder-black focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                    placeholder="Phone number" />
            </div>

            <!-- Password -->
            <div class="mb-4 relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'" x-model="password" @input="updatePasswordFeedback()"
                    @focus="isTyping = true; currentField = 'password'" @blur="isTyping = false; currentField = ''"
                    class="input-glow w-full pl-10 pr-10 py-3 rounded-lg bg-white border-b-4 border-blue-800 text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                    placeholder="Create a password">
            </div>

            <!-- Confirm Password -->
            <div class="mb-4 relative">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-blue-500">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'" x-model="confirmPassword"
                    @input="updateConfirmPasswordFeedback()" @focus="isTyping = true; currentField = 'confirmPassword'"
                    @blur="isTyping = false; currentField = ''"
                    class="input-glow w-full pl-10 pr-10 py-3 rounded-lg bg-white border-b-4 border-blue-800 text-black placeholder-black focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                    placeholder="Confirm your password">
            </div>

            <!-- Question Section -->
            <div class="mb-4 question-section">
                <select x-model="selectedQuestion" @change="updateSelectedQuestion()" class="question-select">
                    <option value="">Select a personal question</option>
                    <option value="pet_name">What is your pet's name?</option>
                    <option value="childhood_friend">What is the name of your childhood friend?</option>
                    <option value="first_school">What is the name of your first school?</option>
                    <option value="favorite_book">What is your favorite book?</option>
                    <option value="dream_destination">What is your dream travel destination?</option>
                </select>
                <div x-show="selectedQuestion" x-transition:enter="transition ease-out duration-300" class="mt-4">
                    <input x-model="questionAnswer" @input="updateQuestionAnswer()"
                        class="w-full p-2 rounded-lg bg-white border border-blue-800"
                        placeholder="Your answer (max 100 words)">
                </div>

            </div>

            <!-- Show Password Checkbox -->
            <div class="flex items-center justify-between mt-2">
                <label class="flex items-center space-x-2 text-sm text-purple-800">
                    <input type="checkbox" id="showPassword" x-model="showPassword" @change="togglePasswordVisibility">
                    <span class="font-medium hover:text-blue-950 ">Show Password</span>
                </label>
                <a href="{{ route('learner.learnerLogin') }}"
                    class="text-sm font-medium text-purple-800 hover:text-blue-950 transition">
                    Already have an account? Login
                </a>
            </div>


            <!-- Register Button -->
            <div class="relative flex justify-center mt-6 mb-4">
                <button x-ref="registerBtn" @click="submitForm()" @mouseover="dodgeButton()"
                    :class="{
                        'opacity-40 cursor-not-allowed': !formValid,
                        'translate-x-0': buttonPosition === 'center',
                        '-translate-x-20': buttonPosition === 'left',
                        'translate-x-20': buttonPosition === 'right'
                    }"
                    :disabled="!formValid"
                    class="register-btn bg-gradient-to-r from-purple-700 to-purple-800 hover:from-purple-600 hover:to-blue-600 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                    Create Account
                </button>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registrationForm', () => ({
                fullName: '',
                email: '',
                countryCode: '+1',
                phoneNumber: '',
                password: '',
                confirmPassword: '',
                showPassword: false,
                isTyping: false,
                currentField: '',
                feedbackMessage: 'Welcome! Let\'s get you registered!',
                formValid: false,
                nameStep: 0,
                emailStep: 0,
                phoneStep: 0,
                passwordStep: 0,
                buttonPosition: 'center',
                lastDodgeDirection: 'right',
                idleTimer: null,
                showCountryMessage: false,
                selectedQuestion: '',
                questionAnswer: '',
                countryNames: {
                    '+1': 'United States',
                    '+44': 'United Kingdom',
                    '+91': 'India',
                    '+880': 'Bangladesh',
                    '+81': 'Japan',
                    '+61': 'Australia',
                    '+49': 'Germany',
                    '+33': 'France',
                    '+39': 'Italy',
                    '+7': 'Russia',
                    '+86': 'China',
                    '+34': 'Spain',
                    '+55': 'Brazil',
                    '+27': 'South Africa',
                    '+82': 'South Korea',
                    '+46': 'Sweden',
                    '+31': 'Netherlands',
                    '+41': 'Switzerland wailing',
                    '+351': 'Portugal',
                    '+48': 'Poland',
                    '+64': 'New Zealand',
                    '+65': 'Singapore',
                    '+90': 'Turkey',
                    '+66': 'Thailand',
                    '+60': 'Malaysia',
                    '+62': 'Indonesia',
                    '+63': 'Philippines',
                    '+20': 'Egypt',
                    '+254': 'Kenya',
                    '+92': 'Pakistan',
                    '+977': 'Nepal',
                    '+94': 'Sri Lanka',
                    '+84': 'Vietnam',
                    '+961': 'Lebanon',
                    '+962': 'Jordan',
                    '+971': 'United Arab Emirates',
                    '+973': 'Bahrain',
                    '+974': 'Qatar',
                    '+968': 'Oman',
                    '+965': 'Kuwait',
                    '+98': 'Iran',
                    '+964': 'Iraq',
                    '+93': 'Afghanistan',
                    '+47': 'Norway',
                    '+45': 'Denmark',
                    '+358': 'Finland',
                    '+386': 'Slovenia',
                    '+385': 'Croatia',
                    '+387': 'Bosnia and Herzegovina',
                    '+381': 'Serbia',
                    '+382': 'Montenegro',
                    '+389': 'North Macedonia',
                    '+36': 'Hungary',
                    '+40': 'Romania',
                    '+373': 'Moldova',
                    '+380': 'Ukraine',
                    '+375': 'Belarus',
                    '+420': 'Czech Republic',
                    '+421': 'Slovakia',
                    '+43': 'Austria',
                    '+32': 'Belgium',
                    '+352': 'Luxembourg',
                    '+353': 'Ireland',
                    '+354': 'Iceland',
                    '+356': 'Malta',
                    '+357': 'Cyprus',
                    '+216': 'Tunisia',
                    '+213': 'Algeria',
                    '+212': 'Morocco',
                    '+967': 'Yemen',
                    '+960': 'Maldives',
                    '+976': 'Mongolia',
                    '+856': 'Laos',
                    '+855': 'Cambodia',
                    '+95': 'Myanmar',
                    '+251': 'Ethiopia',
                    '+250': 'Rwanda',
                    '+256': 'Uganda',
                    '+255': 'Tanzania',
                    '+260': 'Zambia',
                    '+263': 'Zimbabwe',
                    '+258': 'Mozambique',
                    '+269': 'Comoros',
                    '+291': 'Eritrea',
                    '+297': 'Aruba',
                    '+298': 'Faroe Islands',
                    '+299': 'Greenland',
                    '+290': 'Saint Helena',
                    '+350': 'Gibraltar',
                    '+378': 'San Marino',
                    '+500': 'Falkland Islands',
                    '+501': 'Belize',
                    '+502': 'Guatemala',
                    '+503': 'El Salvador',
                    '+504': 'Honduras',
                    '+505': 'Nicaragua',
                    '+506': 'Costa Rica',
                    '+507': 'Panama',
                    '+508': 'Saint Pierre and Miquelon',
                    '+509': 'Haiti',
                    '+590': 'Guadeloupe',
                    '+591': 'Bolivia',
                    '+592': 'Guyana',
                    '+593': 'Ecuador',
                    '+594': 'French Guiana',
                    '+595': 'Paraguay',
                    '+596': 'Martinique',
                    '+597': 'Suriname',
                    '+598': 'Uruguay',
                    '+599': 'Caribbean Netherlands',
                    '+675': 'Papua New Guinea',
                    '+676': 'Tonga',
                    '+677': 'Solomon Islands',
                    '+678': 'Vanuatu',
                    '+679': 'Fiji',
                    '+680': 'Palau',
                    '+681': 'Wallis and Futuna',
                    '+682': 'Cook Islands',
                    '+683': 'Niue',
                    '+685': 'Samoa',
                    '+686': 'Kiribati',
                    '+687': 'New Caledonia',
                    '+688': 'Tuvalu',
                    '+689': 'French Polynesia',
                    '+690': 'Tokelau',
                    '+691': 'Micronesia',
                    '+692': 'Marshall Islands'
                },
                idleMessages: [
                    "Ready to start your learning journey?",
                    "We're excited to have you join us!",
                    "Just a few details and you're in!",
                    "Your future starts here!",
                    "Education is the most powerful weapon!",
                    "Let's build your future together!"
                ],
                showBubble: true,

                init() {
                    this.startIdleTimer();

                    this.$watch('fullName', (value) => {
                        this.resetIdleTimer();
                        this.updateNameFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'name') {
                            this.isTyping = true;
                        }
                    });

                    this.$watch('email', (value) => {
                        this.resetIdleTimer();
                        this.updateEmailFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'email') {
                            this.isTyping = true;
                        }
                    });

                    this.$watch('phoneNumber', (value) => {
                        this.resetIdleTimer();
                        this.updatePhoneFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'phone') {
                            this.isTyping = true;
                        }
                    });

                    this.$watch('password', (value) => {
                        this.resetIdleTimer();
                        this.updatePasswordFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'password') {
                            this.isTyping = true;
                        }
                    });

                    this.$watch('confirmPassword', (value) => {
                        this.resetIdleTimer();
                        this.updateConfirmPasswordFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'confirmPassword') {
                            this.isTyping = true;
                        }
                    });

                    this.$watch('countryCode', (value) => {
                        if (this.showCountryMessage) {
                            this.feedbackMessage =
                                `Oh, you're from ${this.countryNames[value]}! Now enter your phone number.`;
                        }
                    });

                    this.$watch('selectedQuestion', () => {
                        this.updateSelectedQuestion();
                        this.checkFormValidity();
                    });

                    this.$watch('questionAnswer', () => {
                        this.updateQuestionAnswer();
                        this.checkFormValidity();
                    });
                },

                async submitForm() {
                    if (!this.formValid) {
                        this.feedbackMessage =
                            "Please complete all fields and the question correctly!";
                        this.showBubble = true;
                        return;
                    }

                    const formData = {
                        fullName: this.fullName,
                        email: this.email,
                        countryCode: this.countryCode,
                        phoneNumber: this.phoneNumber,
                        password: this.password,
                        confirmPassword: this.confirmPassword,
                        selectedQuestion: this.selectedQuestion,
                        questionAnswer: this.questionAnswer
                    };

                    try {
                        const response = await fetch('/learner/register', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(formData),
                        });

                        const result = await response.json();

                        this.feedbackMessage = result.message;
                        this.showBubble = true;

                        if (result.success) {
                            setTimeout(() => {
                                window.location.href = '/learner/login';
                            }, 2000);
                        }
                    } catch (error) {
                        this.feedbackMessage = "Network error. Please try again.";
                        this.showBubble = true;
                    }
                },

                startIdleTimer() {
                    this.idleTimer = setTimeout(() => {
                        if (!this.isTyping) {
                            this.showRandomIdleMessage();
                        }
                    }, 7000);
                },

                resetIdleTimer() {
                    clearTimeout(this.idleTimer);
                    this.startIdleTimer();
                },

                showRandomIdleMessage() {
                    const randomIndex = Math.floor(Math.random() * this.idleMessages.length);
                    this.feedbackMessage = this.idleMessages[randomIndex];
                    this.showBubble = true;
                },

                updateNameFeedback() {
                    if (!this.fullName) {
                        this.feedbackMessage = "Let's start with your full name!";
                        this.nameStep = 0;
                        return;
                    }

                    if (this.fullName.length < 3 && this.nameStep <= 1) {
                        this.feedbackMessage = "Your name is a bit short, isn't it?";
                        this.nameStep = 1;
                    } else if (!this.fullName.includes(' ') && this.nameStep <= 2) {
                        this.feedbackMessage = "Don't forget your last name too!";
                        this.nameStep = 2;
                    } else if (this.nameStep <= 3) {
                        this.feedbackMessage = "Great name! Now enter your email.";
                        this.nameStep = 3;
                    }
                },

                updateEmailFeedback() {
                    if (!this.email) {
                        this.feedbackMessage = "Next, we need your email address!";
                        this.emailStep = 0;
                        return;
                    }

                    if (!this.email.includes('@') && this.emailStep <= 1) {
                        this.feedbackMessage = "Don't forget the '@' in your email!";
                        this.emailStep = 1;
                    } else if (!this.email.includes('.') && this.emailStep <= 2) {
                        this.feedbackMessage = "Almost there! Need a domain like '.com'";
                        this.emailStep = 2;
                    } else if (this.isEmailValid() && this.emailStep <= 3) {
                        this.feedbackMessage = "Perfect! Now let's get your phone number.";
                        this.emailStep = 3;
                    }
                },

                updateCountryFeedback() {
                    this.feedbackMessage =
                        `Oh, you're from ${this.countryNames[this.countryCode]}! Now enter your phone number.`;
                },

                updatePhoneFeedback() {
                    if (!this.phoneNumber) {
                        if (this.showCountryMessage) {
                            this.feedbackMessage =
                                `Now enter your ${this.countryNames[this.countryCode]} phone number`;
                        } else {
                            this.feedbackMessage =
                                "Click the country code first, then enter your phone number";
                        }
                        this.phoneStep = 0;
                        return;
                    }

                    if (this.phoneNumber.length < 5 && this.phoneStep <= 1) {
                        this.feedbackMessage = "Keep going! Enter more digits";
                        this.phoneStep = 1;
                    } else if (!/^\d+$/.test(this.phoneNumber) && this.phoneStep <= 2) {
                        this.feedbackMessage = "Phone numbers should only contain digits";
                        this.phoneStep = 2;
                    } else if (this.isPhoneValid() && this.phoneStep <= 3) {
                        this.feedbackMessage = "Great! Now create a secure password";
                        this.phoneStep = 3;
                    }
                },

                updatePasswordFeedback() {
                    if (!this.password) {
                        this.feedbackMessage = "Create a strong password to protect your account";
                        this.passwordStep = 0;
                        return;
                    }

                    if (this.password.length < 8 && this.passwordStep <= 1) {
                        this.feedbackMessage = "Make it at least 8 characters long";
                        this.passwordStep = 1;
                    } else if (!/[A-Z]/.test(this.password) && this.passwordStep <= 2) {
                        this.feedbackMessage = "Add a capital letter for extra strength";
                        this.passwordStep = 2;
                    } else if (!/\d/.test(this.password) && this.passwordStep <= 3) {
                        this.feedbackMessage = "Include a number to make it stronger";
                        this.passwordStep = 3;
                    } else if (!/[!@#$%^&*]/.test(this.password) && this.passwordStep <= 4) {
                        this.feedbackMessage = "A special character (!@#) would make it perfect";
                        this.passwordStep = 4;
                    } else if (this.isPasswordValid() && this.passwordStep <= 5) {
                        this.feedbackMessage = "Great password! Now confirm it below";
                        this.passwordStep = 5;
                    }
                },

                updateConfirmPasswordFeedback() {
                    if (!this.confirmPassword) {
                        this.feedbackMessage = "Please confirm your password";
                        return;
                    }

                    if (this.password !== this.confirmPassword) {
                        this.feedbackMessage = "Oops! Passwords don't match";
                    } else {
                        this.feedbackMessage = "Perfect! Select a personal question to continue!";
                    }
                },

                updateSelectedQuestion() {
                    if (this.selectedQuestion) {
                        this.feedbackMessage = "Great! Now provide your answer below (max 100 words).";
                        this.questionAnswer = ''; // Reset answer when a new question is selected
                    } else {
                        this.feedbackMessage = "Please select a personal question!";
                        this.questionAnswer = '';
                    }
                    this.checkFormValidity();
                },

                updateQuestionAnswer() {
                    if (this.selectedQuestion && this.questionAnswer) {
                        if (this.questionAnswer.length > 50) {
                            this.questionAnswer = this.questionAnswer.slice(0, 50);
                            this.feedbackMessage = "Answer limited to 50 characters!";
                        } else {
                            this.feedbackMessage = `Characters: ${this.questionAnswer.length} / 50`;
                        }
                    } else if (this.selectedQuestion && !this.questionAnswer) {
                        this.feedbackMessage = "Please enter your answer.";
                    }
                    this.checkFormValidity();
                },

                togglePasswordVisibility() {
                    // This will automatically update both password fields due to x-model binding
                },

                isNameValid() {
                    return this.fullName.length >= 3 && this.fullName.includes(' ');
                },

                isEmailValid() {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
                },

                isPhoneValid() {
                    return this.phoneNumber.length >= 8 && /^\d+$/.test(this.phoneNumber);
                },

                isPasswordValid() {
                    return this.password.length >= 8 &&
                        /[A-Z]/.test(this.password) &&
                        /\d/.test(this.password) &&
                        /[!@#$%^&*]/.test(this.password);
                },

                isConfirmPasswordValid() {
                    return this.password === this.confirmPassword;
                },

                isQuestionValid() {
                    return this.selectedQuestion !== '' && this.questionAnswer.trim() !== '';
                },

                checkFormValidity() {
                    this.formValid = this.isNameValid() &&
                        this.isEmailValid() &&
                        this.isPhoneValid() &&
                        this.isPasswordValid() &&
                        this.isConfirmPasswordValid() &&
                        this.isQuestionValid();
                },

                dodgeButton() {
                    if (!this.formValid) {
                        this.feedbackMessage = "Please complete all fields and the question correctly!";
                        this.showBubble = true;

                        if (this.lastDodgeDirection === 'right') {
                            this.buttonPosition = 'left';
                            this.lastDodgeDirection = 'left';
                        } else {
                            this.buttonPosition = 'right';
                            this.lastDodgeDirection = 'right';
                        }

                        setTimeout(() => {
                            this.buttonPosition = 'center';
                        }, 200);
                    }
                }
            }));
        });
    </script>
@endsection
