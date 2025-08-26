<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background: #faf5ff;
    }

    .container {
        background: #ffffff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #d8b4fe;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .input-field {
        background: #ffffff;
        border: 1px solid #d8b4fe;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        color: #4b5563;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(216, 180, 254, 0.3);
        outline: none;
        border-color: #9333ea;
    }

    .btn-purple {
        background: linear-gradient(to right, #9333ea, #7e22ce);
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .btn-purple:hover {
        background: linear-gradient(to right, #a855f7, #9333ea);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }

    .btn-purple:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .message-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        padding: 1rem 2rem;
        border-radius: 0.5rem;
        font-size: 1.2rem;
        max-width: 90%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        color: #ffffff;
    }

    .message-container.success {
        background-color: #4CAF50;
    }

    .message-container.error {
        background-color: #f44336;
    }

    .error-pulse {
        animation: pulse 0.5s ease-in-out;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .toggle-icon {
        transition: transform 0.3s ease;
    }

    .toggle-icon:hover {
        transform: scale(1.15);
    }

    [x-cloak] {
        display: none !important;
    }
</style>

<div class="min-h-screen flex items-center justify-center p-4 bg-gray-100">
    <div x-data="{
        form: {
            name: '',
            email: '',
            password: '',
            confirmPassword: ''
        },
        errors: {
            name: '',
            email: '',
            password: '',
            confirmPassword: ''
        },
        showPassword: false,
        showConfirmPassword: false,
        isSubmitting: false,
        message: { text: '', type: 'success' },
        emailStatus: { checking: false, exists: false, message: '' },
        validateForm() {
            this.errors.name = this.form.name.trim() ? '' : 'Name is required';
            this.errors.email = this.form.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email) ? (this.emailStatus.exists ? 'Email already exists' : '') : 'Valid email is required';
            this.errors.password = this.form.password && /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(this.form.password) ? '' : 'Password must be at least 8 characters with 1 uppercase, 1 lowercase, 1 number, and 1 special character';
            this.errors.confirmPassword = this.form.confirmPassword === this.form.password ? '' : 'Passwords do not match';
        },
        get isFormValid() {
            this.validateForm();
            return !Object.values(this.errors).some(error => error);
        },
        togglePassword(field) {
            if (field === 'password') {
                this.showPassword = !this.showPassword;
            } else {
                this.showConfirmPassword = !this.showConfirmPassword;
            }
        },
        async checkEmail() {
            if (!this.form.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) {
                this.emailStatus = { checking: false, exists: false, message: '' };
                this.validateForm();
                return;
            }
            this.emailStatus.checking = true;
            try {
                const response = await fetch('{{ route('learner.check-emails') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ email: this.form.email })
                });
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                const data = await response.json();
                console.log('Email check response:', data);
                this.emailStatus = {
                    checking: false,
                    exists: data.exists,
                    message: data.message || ''
                };
                this.validateForm();
            } catch (err) {
                console.error('Email check error:', err);
                this.emailStatus = {
                    checking: false,
                    exists: false,
                    message: 'Error checking email: ' + err.message
                };
                this.showMessage('Error checking email. Please try again.', 'error');
                this.validateForm();
            }
        },
        async submitForm() {
            if (!this.isFormValid || this.isSubmitting) return;
            this.isSubmitting = true;
            try {
                const response = await fetch('{{ route('learner.create') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        name: this.form.name,
                        email: this.form.email,
                        password: this.form.password,
                        confirm_password: this.form.confirmPassword
                    })
                });
                const data = await response.json();
                console.log('Create learner response:', data);
                if (data.success) {
                    this.showMessage(data.message || 'Learner created successfully!', 'success');
                    this.form = { name: '', email: '', password: '', confirmPassword: '' };
                    this.emailStatus = { checking: false, exists: false, message: '' };
                    this.errors = { name: '', email: '', password: '', confirmPassword: '' };
                } else {
                    this.showMessage(data.message || 'Failed to create learner.', 'error');
                }
            } catch (err) {
                console.error('Submission error:', err);
                this.showMessage('Error creating learner: ' + err.message, 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        showMessage(text, type = 'success', duration = 5000) {
            this.message.text = text;
            this.message.type = type;
            if (duration > 0) {
                setTimeout(() => { this.message.text = ''; }, duration);
            }
        }
    }" class="w-full max-w-5xl container fade-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-purple-800 mt-4">Create Learner</h1>
            <p class="text-gray-600 text-sm mt-2">Register a new learner account</p>
        </div>

        <!-- Message Alert -->
        <div x-show="message.text" x-transition
             class="message-container fade-in"
             :class="{ 'success': message.type === 'success', 'error': message.type === 'error' }"
             x-text="message.text">
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6" autocomplete="off">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-purple-700">Full Name</label>
                <div class="relative">
                    <input type="text" id="name" x-model="form.name" placeholder="Your full name"
                           class="input-field w-full text-sm"
                           :class="{ 'error-pulse': errors.name }"
                           @input="validateForm">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <template x-if="errors.name">
                    <p class="mt-1 text-red-500 text-xs" x-text="errors.name"></p>
                </template>
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-purple-700">Email Address</label>
                <div class="relative">
                    <input type="email" id="email" x-model="form.email" placeholder="Your email"
                           class="input-field w-full text-sm" autocomplete="off"
                           :class="{ 'error-pulse': errors.email }"
                           @input.debounce.500ms="checkEmail">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <template x-if="emailStatus.checking">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!emailStatus.checking && !errors.email && emailStatus.message">
                            <svg class="h-5 w-5" :class="emailStatus.exists ? 'text-red-500' : 'text-green-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </template>
                        <template x-if="!emailStatus.checking && !emailStatus.message">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </template>
                    </div>
                </div>
                <template x-if="errors.email">
                    <p class="mt-1 text-red-500 text-xs" x-text="errors.email"></p>
                </template>
                <template x-if="!errors.email && emailStatus.message && !emailStatus.checking">
                    <p class="mt-1 text-xs" :class="emailStatus.exists ? 'text-red-500' : 'text-green-500'" x-text="emailStatus.message"></p>
                </template>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-purple-700">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" x-model="form.password"
                           placeholder="Create a password" autocomplete="new-password"
                           class="input-field w-full text-sm pr-10"
                           :class="{ 'error-pulse': errors.password }"
                           @input="validateForm">
                    <button type="button" @click="togglePassword('password')"
                            class="toggle-icon absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!showPassword">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 11-6 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="showPassword" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <template x-if="errors.password">
                    <p class="mt-1 text-red-500 text-xs" x-text="errors.password"></p>
                </template>
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label for="confirmPassword" class="block text-sm font-medium text-purple-700">Confirm Password</label>
                <div class="relative">
                    <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword"
                           x-model="form.confirmPassword" placeholder="Confirm your password"
                           class="input-field w-full text-sm pr-10"
                           :class="{ 'error-pulse': errors.confirmPassword }"
                           @input="validateForm">
                    <button type="button" @click="togglePassword('confirmPassword')"
                            class="toggle-icon absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!showConfirmPassword">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 11-6 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="showConfirmPassword" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <template x-if="errors.confirmPassword">
                    <p class="mt-1 text-red-500 text-xs" x-text="errors.confirmPassword"></p>
                </template>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit"
                        class="btn-purple w-full text-sm scale-100 cursor-pointer hover:scale-95"
                        :class="{ 'opacity-50 cursor-not-allowed': isSubmitting || !isFormValid }"
                        :disabled="isSubmitting || !isFormValid">
                    <div class="flex items-center justify-center">
                        <template x-if="isSubmitting">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        </template>
                        <span x-text="isSubmitting ? 'Creating...' : 'Create Learner'"></span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>
