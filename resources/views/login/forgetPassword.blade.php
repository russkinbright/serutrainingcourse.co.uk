@extends('home.default')

@section('content')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        .thought-dots span:nth-child(2) { animation-delay: 0.2s; }
        .thought-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes float {
            0%, 100% { transform: translateY(-10px); }
            50% { transform: translateY(-5px); }
        }

        @keyframes pulseGlow {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5)); }
            50% { filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8)); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
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
            0%, 100% { text-shadow: 0 0 10px rgba(216, 180, 254, 0.5); }
            50% { text-shadow: 0 0 20px rgba(216, 180, 254, 0.8), 0 0 30px rgba(216, 180, 254, 0.4); }
        }
    </style>

    <div class="min-h-screen flex items-center justify-center bg-gray-900 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="/image/login-bg-auto.jpg" alt="Training Background" class="w-full h-full object-cover opacity-50">
        </div>
        <!-- Form -->
        <div x-data="resetPasswordForm()" class="lg:w-[400px] sm:w-auto h-[500px] max-h-min p-6 lg:ml-3 relative">
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

                <div class="avatar w-32 h-20 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-5xl text-gray-800">
                    <span x-bind:class="{ 'avatar-active': isTyping }">🎓</span>
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center mb-6 z-10">
                <h1 class="text-3xl font-extrabold bg-white rounded-2xl py-2">Set New Password</h1>
            </div>

               <p class="text-white text-center border-4 font-bold px-4 py-2 rounded-md shadow-sm text-sm mb-4">
                    📬 Password should contain at least one uppercase, one lowercase, one number, one special character, and be a minimum of 8 characters long.
                </p>


            <!-- New Password -->
            <div class="mb-4 relative z-10">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'" x-model="password" @input="updatePasswordFeedback()"
                    @focus="isTyping = true; currentField = 'password'" @blur="isTyping = false; currentField = ''"
                    class="w-full pl-10 pr-10 py-3 rounded-lg border-b-4 border-gray-300 bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                    placeholder="Enter new password" aria-label="New Password">
                <button type="button" class="absolute right-3 top-3 text-gray-500" @click="showPassword = !showPassword"
                    aria-label="Toggle password visibility">
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4 relative z-10">
                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                    <i class="fas fa-lock"></i>
                </span>
                <input :type="showConfirmPassword ? 'text' : 'password'" x-model="confirmPassword" @input="updateConfirmPasswordFeedback()"
                    @focus="isTyping = true; currentField = 'confirmPassword'" @blur="isTyping = false; currentField = ''"
                    class="w-full pl-10 pr-10 py-3 rounded-lg border-b-4 border-gray-300 bg-gray-100 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition"
                    placeholder="Confirm new password" aria-label="Confirm Password">
                <button type="button" class="absolute right-3 top-3 text-gray-500" @click="showConfirmPassword = !showConfirmPassword"
                    aria-label="Toggle confirm password visibility">
                    <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>

            <!-- Submit Button -->
            <div class="relative flex justify-center mt-6 mb-4 z-10">
                <button @click="submitResetForm()" :disabled="!formValid"
                    :class="{ 'opacity-40 cursor-not-allowed': !formValid }"
                    class="relative overflow-hidden bg-gradient-to-r from-purple-700 to-indigo-800 text-white cursor-pointer font-semibold py-3 px-8 rounded-xl shadow-lg transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-2xl hover:brightness-110 hover:ring-2 hover:ring-indigo-300 focus:outline-none ring-4 focus:ring-4 focus:ring-indigo-500">
                    <span class="relative z-10">🔒 Set Password</span>
                    <span class="absolute top-0 left-0 w-full h-full bg-white opacity-5 hover:opacity-10 transition duration-300 rounded-xl"></span>
                </button>
            </div>

            <!-- Back to Login -->
            <div class="flex justify-center text-sm text-white px-1 z-10">
                <a href="{{ route('learner.learnerLogin') }}" class="hover:text-gray-800 transition font-medium">Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 60, density: { enable: true, value_area: 800 } },
                    color: { value: '#9333ea' },
                    shape: { type: 'circle' },
                    opacity: { value: 0.5, random: true },
                    size: { value: 3, random: true },
                    line_linked: { enable: true, distance: 150, color: '#9333ea', opacity: 0.4, width: 1 },
                    move: { enable: true, speed: 2, direction: 'none', random: true }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' } },
                    modes: { repulse: { distance: 100, duration: 0.4 }, push: { particles_nb: 4 } }
                }
            });

            gsap.from('.avatar', { opacity: 0, scale: 0.5, duration: 1, ease: 'elastic.out(1, 0.3)' });
            gsap.from('.input-glow', { opacity: 0, y: 20, duration: 0.8, stagger: 0.2, ease: 'power2.out', delay: 0.5 });
            gsap.from('.login-btn', { opacity: 0, y: 20, duration: 0.8, ease: 'power2.out', delay: 0.9 });
            gsap.from('.speech-bubble', { opacity: 0, y: -20, duration: 0.8, ease: 'power2.out', delay: 1.2 });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('resetPasswordForm', () => ({
                password: '',
                confirmPassword: '',
                showPassword: false,
                showConfirmPassword: false,
                isTyping: false,
                currentField: '',
                feedbackMessage: 'Enter your new password!',
                formValid: false,
                showBubble: true,

                init() {
                    this.$watch('password', () => {
                        this.updatePasswordFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'password') this.isTyping = true;
                    });
                    this.$watch('confirmPassword', () => {
                        this.updateConfirmPasswordFeedback();
                        this.checkFormValidity();
                        if (this.currentField === 'confirmPassword') this.isTyping = true;
                    });
                },

                updatePasswordFeedback() {
                    if (!this.password) {
                        this.feedbackMessage = "Please enter a new password!";
                        return;
                    }
                    if (this.password.length < 8) {
                        this.feedbackMessage = "Password must be at least 8 characters!";
                    } else {
                        this.feedbackMessage = "Password looks good! Confirm it!";
                    }
                },

                updateConfirmPasswordFeedback() {
                    if (!this.confirmPassword) {
                        this.feedbackMessage = "Please confirm your password!";
                        return;
                    }
                    if (this.password !== this.confirmPassword) {
                        this.feedbackMessage = "Passwords do not match!";
                    } else if (this.isPasswordValid()) {
                        this.feedbackMessage = "Ready to set your new password!";
                    }
                },

                isPasswordValid() {
                    return this.password.length >= 8 && this.password === this.confirmPassword;
                },

                checkFormValidity() {
                    this.formValid = this.isPasswordValid();
                },

                async submitResetForm() {
                    if (!this.formValid) {
                        this.feedbackMessage = "Please ensure passwords match and are at least 8 characters!";
                        this.showBubble = true;
                        return;
                    }
                    const urlParams = new URLSearchParams(window.location.search);
                    const token = urlParams.get('token');
                    const email = urlParams.get('email');
                    const formData = {
                        email: email,
                        token: token,
                        password: this.password,
                        password_confirmation: this.confirmPassword
                    };
                    try {
                        const response = await fetch('/learner/reset-password', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(formData),
                        });
                        const result = await response.json();
                        this.feedbackMessage = result.message;
                        this.showBubble = true;
                        if (result.success) {
                            setTimeout(() => {
                                window.location.href = '{{ route('learner.learnerLogin') }}';
                            }, 2000);
                        }
                    } catch (error) {
                        this.feedbackMessage = "Error resetting password. Please try again.";
                        this.showBubble = true;
                    }
                }
            }));
        });
    </script>
@endsection