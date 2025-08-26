@extends('home.default')

@section('content')
    <style>
        body {
            background-image: url('{{ asset('image/login-bg-2.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
        }

        .speech-bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 0.75rem;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
            transform: translateY(-10px);
            animation: float 1.5s infinite ease-in-out;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .speech-bubble:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border: 10px solid transparent;
            border-top-color: rgba(107, 33, 168, 0.9);
        }

        .avatar {
            transition: all 0.3s ease;
            position: relative;
            animation: pulseGlow 2s infinite;
        }

        .avatar-active {
            transform: scale(1.15) rotate(3deg);
            filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.8));
        }

        .thought-dots span {
            animation: bounce 1s infinite;
        }

        .thought-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .thought-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(-10px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes pulseGlow {

            0%,
            100% {
                filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
            }

            50% {
                filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8));
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .animate-text-glow {
            animation: textGlow 2s ease-in-out infinite;
        }

        @keyframes textGlow {

            0%,
            100% {
                text-shadow: 0 0 10px rgba(216, 180, 254, 0.5);
            }

            50% {
                text-shadow: 0 0 20px rgba(216, 180, 254, 0.8), 0 0 30px rgba(216, 180, 254, 0.4);
            }
        }
    </style>

    <div class="min-h-screen flex items-center justify-center bg-gray-900 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="/image/login-bg-auto.jpg" alt="Training Background" class="w-full h-full object-cover opacity-50">
        </div>
        <!-- Right Side: Form -->
        <div x-data="loginForm()" class="lg:w-[400px] sm:w-auto h-[450px] max-h-min p-6 lg:ml-3 relative">
            <!-- Particle Background -->
            <div id="particles-js" class="absolute inset-0 z-0 pointer-events-none"></div>

            <!-- Avatar + Bubble -->
            <div class="relative mb-4 flex flex-col items-center z-10">
                <div x-show="showBubble" x-transition:enter="transition ease-out duration-300"
                    x-transition:leave="transition ease-in duration-200"
                    class="speech-bubble p-3 w-auto text-sm absolute -top-16 bg-white text-gray-800 shadow-md rounded-lg">
                    <p x-text="feedbackMessage" class="z-10 relative"></p>
                    <div class="thought-dots absolute left-1/2 transform -translate-x-1/2 top-full mt-1 space-x-1 flex">
                        <span class="dot w-2 h-2 bg-gray-400 rounded-full opacity-80"></span>
                        <span class="dot w-1.5 h-1.5 bg-gray-300 rounded-full opacity-60"></span>
                        <span class="dot w-1 h-1 bg-gray-200 rounded-full opacity-40"></span>
                    </div>
                </div>

                <div
                    class="avatar w-32 h-20 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-5xl text-gray-800">
                    <span x-bind:class="{ 'avatar-active': isTyping }">🎓</span>
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center mb-6 z-10">
                <h1 class="text-3xl font-extrabold bg-white rounded-2xl py-2">
                    <span x-text="isForgotPassword ? 'Reset Password' : 'Learner Login'"></span>
                </h1>
            </div>

            <!-- Login Form -->
            <template x-if="!isForgotPassword">
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:leave="transition ease-in duration-200">
                <!-- Email -->
                <div class="mb-4 relative z-10">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input x-model="email" @input="updateEmailFeedback()" @focus="isTyping = true; currentField = 'email'"
                        @blur="isTyping = false" type="email"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border-b-4 border-gray-300 bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                        placeholder="Enter your email" aria-label="Email address">
                </div>

                <!-- Password -->
                <div class="mb-4 relative z-10">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'" x-model="password" @input="updatePasswordFeedback()"
                        @focus="isTyping = true; currentField = 'password'" @blur="isTyping = false; currentField = ''"
                        class="w-full pl-10 pr-10 py-3 rounded-lg border-b-4 border-gray-300 bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                        placeholder="Create a password" aria-label="Password">
                    <button type="button" class="absolute right-3 top-3 text-gray-500"
                        @click="showPassword = !showPassword" aria-label="Toggle password visibility">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>

                <!-- Login Button -->
                <div class="relative flex justify-center mt-6 mb-4 z-10">
                    <button x-ref="loginBtn" @click="submitForm()" @mouseover="dodgeButton()"
                        :class="{
                            'opacity-40 cursor-not-allowed': !formValid,
                            'translate-x-0': buttonPosition === 'center',
                            '-translate-x-20': buttonPosition === 'left',
                            'translate-x-20': buttonPosition === 'right'
                        }"
                        :disabled="!formValid"
                        class="relative overflow-hidden bg-gradient-to-r from-purple-700 to-indigo-800 text-white cursor-pointer font-semibold py-3 px-8 rounded-xl shadow-lg transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-2xl hover:brightness-110 hover:ring-2 hover:ring-indigo-300 focus:outline-none ring-4 focus:ring-4 focus:ring-indigo-500">
                        <span class="relative z-10">🚀 Beam In</span>
                        <span
                            class="absolute top-0 left-0 w-full h-full bg-white opacity-5 hover:opacity-10 transition duration-300 rounded-xl"></span>
                    </button>
                </div>
            </div>
            </template>

            <!-- Forgot Password Form -->
            <template x-if="isForgotPassword">
                    <div x-transition:enter="transition ease-out duration-300"
                        x-transition:leave="transition ease-in duration-200">
                <!-- Email -->
                <p
                    class="text-white text-center border-4 font-bold px-4 py-2 rounded-md shadow-sm text-sm mb-4">
                    📬 After clicking the reset link, check your mailbox.
                </p>

                <div class="mb-4 relative z-10">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input x-model="email" @input="checkEmailExists()" @focus="isTyping = true; currentField = 'email'"
                        @blur="isTyping = false" type="email"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border-b-4 border-gray-300 bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                        placeholder="Enter your email" aria-label="Email address">
                </div>

                <!-- Send Email Button -->
                <div class="relative flex justify-center mt-6 mb-4 z-10">
                    <button @click="sendResetEmail()" :class="{ 'opacity-40 cursor-not-allowed': !emailExists }"
                        :disabled="!emailExists"
                        class="relative overflow-hidden bg-gradient-to-r from-purple-700 to-indigo-800 text-white cursor-pointer font-semibold py-3 px-8 rounded-xl shadow-lg transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-2xl hover:brightness-110 hover:ring-2 hover:ring-indigo-300 focus:outline-none ring-4 focus:ring-4 focus:ring-indigo-500">
                        <span class="relative z-10">📧 Send Reset Link</span>
                        <span
                            class="absolute top-0 left-0 w-full h-full bg-white opacity-5 hover:opacity-10 transition duration-300 rounded-xl"></span>
                    </button>
                </div>
            </div>
            </template>

            <!-- Links -->
            <div class="flex justify-center items-center text-sm text-white px-1 z-10 mt-5 hover:cursor-pointer relative">
                <!-- Changed <a> to <button> to prevent page reload -->
                <button type="button" @click="toggleForgotPassword()"
                    class="hover:scale-105 transition font-medium">
                    <span x-text="isForgotPassword ? 'Back to Login' : 'Forgot Password?'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: {
                        value: 60,
                        density: {
                            enable: true,
                            value_area: 800
                        }
                    },
                    color: {
                        value: '#9333ea'
                    },
                    shape: {
                        type: 'circle'
                    },
                    opacity: {
                        value: 0.5,
                        random: true
                    },
                    size: {
                        value: 3,
                        random: true
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#9333ea',
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: 'none',
                        random: true
                    }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: {
                        onhover: {
                            enable: true,
                            mode: 'repulse'
                        },
                        onclick: {
                            enable: true,
                            mode: 'push'
                        }
                    },
                    modes: {
                        repulse: {
                            distance: 100,
                            duration: 0.4
                        },
                        push: {
                            particles_nb: 4
                        }
                    }
                }
            });

            gsap.from('.avatar', {
                opacity: 0,
                scale: 0.5,
                duration: 1,
                ease: 'elastic.out(1, 0.3)'
            });
            gsap.from('.input-glow', {
                opacity: 0,
                y: 20,
                duration: 0.8,
                stagger: 0.2,
                ease: 'power2.out',
                delay: 0.5
            });
            gsap.from('.login-btn', {
                opacity: 0,
                y: 20,
                duration: 0.8,
                ease: 'power2.out',
                delay: 0.9
            });
            gsap.from('.speech-bubble', {
                opacity: 0,
                y: -20,
                duration: 0.8,
                ease: 'power2.out',
                delay: 1.2
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                email: '',
                password: '',
                showPassword: false,
                isTyping: false,
                currentField: '',
                feedbackMessage: 'Hey there! Login to see your updates!',
                formValid: false,
                emailStep: 0,
                passwordStep: 0,
                buttonPosition: 'center',
                lastDodgeDirection: 'right',
                idleTimer: null,
                isForgotPassword: false,
                emailExists: false,
                idleMessages: [
                    "Psst... I'm getting lonely over here! Please Login",
                    "Hello? Anyone there? 👀",
                    "I could tell you a joke while you think...",
                    "The password is... just kidding! 😜",
                    "I bet you're thinking really hard about that password!",
                    "Don't worry, I won't peek at your password! 🤫"
                ],
                showBubble: true,

                init() {
                    this.startIdleTimer();
                    this.$watch('email', () => {
                        this.resetIdleTimer();
                        if (!this.isForgotPassword) {
                            this.updateEmailFeedback();
                            this.checkFormValidity();
                        } else {
                            this.checkEmailExists();
                        }
                        if (this.currentField === 'email') {
                            this.isTyping = true;
                        }
                    });

                    this.$watch('password', () => {
                        this.resetIdleTimer();
                        this.updatePasswordFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'password') {
                            this.isTyping = true;
                        }
                    });
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

                updateEmailFeedback() {
                    if (!this.email) {
                        this.feedbackMessage = "Hey! Start with your email!";
                        this.emailStep = 0;
                        return;
                    }
                    if (!this.email.includes('@') && this.emailStep <= 1) {
                        this.feedbackMessage = "Awesome! Hit me with an '@' now!";
                        this.emailStep = 1;
                    } else if (!this.email.includes('.') && this.emailStep <= 2) {
                        this.feedbackMessage = "So close! Add a '.' to finish it!";
                        this.emailStep = 2;
                    } else if (this.isEmailValid() && this.emailStep <= 3) {
                        this.feedbackMessage = "Email looks good! Now your password!";
                        this.emailStep = 3;
                    }
                },

                updatePasswordFeedback() {
                    if (!this.isEmailValid()) return;
                    if (!this.password) {
                        this.feedbackMessage = "Password time! Make it strong!";
                        this.passwordStep = 0;
                        return;
                    }
                    if (this.password.length < 8 && this.passwordStep <= 1) {
                        this.feedbackMessage = "At least 8 chars—keep going!";
                        this.passwordStep = 1;
                    } else if (this.isPasswordValid() && this.passwordStep <= 2) {
                        this.feedbackMessage = "Ready to beam in!";
                        this.passwordStep = 2;
                    }
                },

                isEmailValid() {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email);
                },

                isPasswordValid() {
                    return this.password.length >= 8;
                },

                checkFormValidity() {
                    this.formValid = this.isEmailValid() && this.isPasswordValid();
                },

                dodgeButton() {
                    if (!this.formValid) {
                        this.feedbackMessage = "Oops! Complete your email and password first! 🚀";
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
                },

                toggleForgotPassword() {
                    this.isForgotPassword = !this.isForgotPassword;
                    this.email = '';
                    this.password = '';
                    this.feedbackMessage = this.isForgotPassword ?
                        'Enter your email to reset your password!' :
                        'Hey there! Login to see your updates!';
                    this.formValid = false;
                    this.emailExists = false;
                    this.showBubble = true;
                },

                async checkEmailExists() {
                    if (!this.isEmailValid()) {
                        this.feedbackMessage = "Please enter a valid email!";
                        this.emailExists = false;
                        return;
                    }
                    try {
                        const response = await fetch('/learner/check-email', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                email: this.email
                            }),
                        });
                        const result = await response.json();
                        this.emailExists = result.exists;
                        this.feedbackMessage = result.exists ?
                            "Email found! You can send the reset link!" :
                            "Email not found. Please check and try again.";
                        this.showBubble = true;
                    } catch (error) {
                        console.error('Error checking email:', error);
                        this.feedbackMessage = "Error checking email. Please try again.";
                        this.emailExists = false;
                        this.showBubble = true;
                    }
                },

                async sendResetEmail() {
                    if (!this.emailExists) return;
                    try {
                        const response = await fetch('/learner/forgot-password', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                email: this.email
                            }),
                        });
                        const result = await response.json();
                        this.feedbackMessage = result.message;
                        this.showBubble = true;
                        if (result.success) {
                            setTimeout(() => {
                                this.toggleForgotPassword();
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Error sending reset email:', error);
                        this.feedbackMessage = "Error sending reset link. Please try again.";
                        this.showBubble = true;
                    }
                },

                async submitForm() {
                    if (!this.formValid) {
                        this.feedbackMessage = "Please complete all fields correctly first!";
                        this.showBubble = true;
                        return;
                    }
                    const urlParams = new URLSearchParams(window.location.search);
                    const redirect = urlParams.get('redirect');
                    const formData = {
                        email: this.email,
                        password: this.password,
                        redirect: redirect,
                    };
                    try {
                        const response = await fetch('/learner/login', {
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
                                window.location.href = result.redirect;
                            }, 2000);
                        }
                    } catch (error) {
                        console.error('Error submitting form:', error);
                        this.feedbackMessage = "Network error. Please try again.";
                        this.showBubble = true;
                    }
                }
            }));
        });
    </script>
@endsection
