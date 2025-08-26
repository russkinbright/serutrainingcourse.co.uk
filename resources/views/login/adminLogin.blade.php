@extends('home.default')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #4c1d95 0%, #6b21a8 100%);
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .avatar {
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        .avatar:hover {
            transform: scale(1.1);
            filter: drop-shadow(0 0 15px rgba(216, 180, 254, 0.7));
        }

        .input-field {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(216, 180, 254, 0.3);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 10px rgba(167, 139, 250, 0.5);
        }

        .login-button {
            background: linear-gradient(90deg, #7c3aed, #a78bfa);
            transition: all 0.3s ease;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(167, 139, 250, 0.5);
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
    </style>

    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Particle Background -->
        <div id="particles-js" class="absolute inset-0 z-0 pointer-events-none"></div>

        <!-- Login Form Container -->
        <div x-data="loginForm()" class="login-container p-8 w-full max-w-md relative z-10">
            <!-- Avatar -->
            <div class="flex justify-center mb-6">
                <div class="avatar w-24 h-24 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-4xl text-white">
                    <span>👑</span>
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-extrabold text-white">Admin Login</h1>
            </div>

            <!-- Login Form -->
            <div>
                <!-- Email -->
                <div class="mb-4 relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-purple-300 mx-3">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input x-model="email" @input="updateEmailFeedback()" @focus="isTyping = true" @blur="isTyping = false" type="email"
                        class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-white placeholder-purple-300 focus:outline-none"
                        placeholder="Enter your email" aria-label="Email address">
                </div>

                <!-- Password -->
                <div class="mb-6 relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-purple-300 mx-3">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'" x-model="password" @input="updatePasswordFeedback()"
                        @focus="isTyping = true" @blur="isTyping = false"
                        class="input-field w-full pl-10 pr-10 py-3 rounded-lg text-white placeholder-purple-300 focus:outline-none"
                        placeholder="Enter your password" aria-label="Password">
                    <button type="button" class="absolute right-3 top-3 text-purple-300"
                        @click="showPassword = !showPassword" aria-label="Toggle password visibility">
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                </div>

                <!-- Feedback Message -->
                <div x-show="showBubble" x-transition:enter="transition ease-out duration-300"
                    x-transition:leave="transition ease-in duration-200"
                    class="text-center text-purple-200 mb-4 text-sm" x-text="feedbackMessage"></div>

                <!-- Login Button -->
                <div class="flex justify-center">
                    <button @click="submitForm()" :disabled="!formValid"
                        :class="{ 'opacity-50 cursor-not-allowed': !formValid }"
                        class="login-button w-full py-3 rounded-lg text-white font-semibold focus:outline-none">
                        <span>🚀 Login</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 80, density: { enable: true, value_area: 800 } },
                    color: { value: '#a78bfa' },
                    shape: { type: 'circle' },
                    opacity: { value: 0.5, random: true },
                    size: { value: 3, random: true },
                    line_linked: { enable: true, distance: 150, color: '#a78bfa', opacity: 0.4, width: 1 },
                    move: { enable: true, speed: 3, direction: 'none', random: true }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: { onhover: { enable: true, mode: 'repulse' }, onclick: { enable: true, mode: 'push' } },
                    modes: { repulse: { distance: 100, duration: 0.4 }, push: { particles_nb: 4 } }
                }
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                email: '',
                password: '',
                showPassword: false,
                isTyping: false,
                feedbackMessage: 'Welcome, Admin! Enter your credentials.',
                formValid: false,
                showBubble: true,

                init() {
                    this.$watch('email', () => {
                        this.updateEmailFeedback();
                        this.checkFormValidity();
                    });
                    this.$watch('password', () => {
                        this.updatePasswordFeedback();
                        this.checkFormValidity();
                    });
                },

                updateEmailFeedback() {
                    if (!this.email) {
                        this.feedbackMessage = 'Please enter your email.';
                        return;
                    }
                    if (!this.isEmailValid()) {
                        this.feedbackMessage = 'Enter a valid email address.';
                    } else {
                        this.feedbackMessage = 'Email looks good! Now your password.';
                    }
                },

                updatePasswordFeedback() {
                    if (!this.password) {
                        this.feedbackMessage = 'Please enter your password.';
                        return;
                    }
                    if (this.password.length < 8) {
                        this.feedbackMessage = 'Password must be at least 8 characters.';
                    } else if (this.isEmailValid()) {
                        this.feedbackMessage = 'Ready to login!';
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

                async submitForm() {
                    if (!this.formValid) {
                        this.feedbackMessage = 'Please complete all fields correctly.';
                        this.showBubble = true;
                        return;
                    }
                    try {
                        const response = await fetch('/admin/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                email: this.email,
                                password: this.password
                            }),
                        });
                        const result = await response.json();
                        this.feedbackMessage = result.message;
                        this.showBubble = true;
                        if (result.success) {
                            setTimeout(() => {
                                window.location.href = result.redirect;
                            }, 1000);
                        }
                    } catch (error) {
                        console.error('Error submitting form:', error);
                        this.feedbackMessage = 'Network error. Please try again.';
                        this.showBubble = true;
                    }
                }
            }));
        });
    </script>
@endsection