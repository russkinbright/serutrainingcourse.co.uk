<div class="container-fluid p-5" x-data="{
    pixelId: 1,
    isLoading: false,
    header: '',
    body: '',
    footer: '',
    formMessage: '',
    formStatus: '',
    isSubmitting: false,
    fetchPixel: async function() {
        this.isLoading = true;
        this.formMessage = '';
        this.formStatus = '';
        try {
            const response = await fetch('{{ route('pixel.show', ':id') }}'.replace(':id', this.pixelId), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            if (data.pixel) {
                this.header = data.pixel.header || '';
                this.body = data.pixel.body || '';
                this.footer = data.pixel.footer || '';
                this.formStatus = 'success';
                this.formMessage = 'Pixel data loaded successfully.';
            } else {
                this.formStatus = 'error';
                this.formMessage = data.message || 'No pixel data found.';
            }
        } catch (error) {
            console.error('Error fetching pixel data:', error);
            this.formStatus = 'error';
            this.formMessage = `Error loading pixel data: ${error.message}. Please try again.`;
        } finally {
            this.isLoading = false;
        }
    },
    submitForm: async function() {
        if (!this.header.trim() && !this.body.trim() && !this.footer.trim()) {
            this.formStatus = 'error';
            this.formMessage = 'Please provide at least one pixel code (header, body, or footer).';
            this.isSubmitting = false;
            return;
        }
        this.isSubmitting = true;
        this.formMessage = '';
        this.formStatus = '';

        const b64 = s => btoa(unescape(encodeURIComponent(s)));

        const formData = new FormData();
        formData.append('header', b64(this.header));
        formData.append('body', b64(this.body));
        formData.append('footer', b64(this.footer));
        formData.append('_token', document.querySelector('meta[name=csrf-token]')?.content || '');
        formData.append('_method', 'PUT');

        try {
            const response = await fetch('{{ route('pixel.update', ':id') }}'.replace(':id', this.pixelId), {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const res = await fetch(url, { method: 'POST', body: formData, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) { throw new Error(await res.text()); }
            const data = await res.json();

            const data = await response.json();
            if (data.message) {
                this.formStatus = 'success';
                this.formMessage = data.message;
            } else if (data.errors) {
                this.formStatus = 'error';
                this.formMessage = Object.values(data.errors).flat().join(' ');
            } else {
                this.formStatus = 'error';
                this.formMessage = 'Unexpected response from server.';
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.formStatus = 'error';
            this.formMessage = `Failed to submit the form: ${error.message}. Please try again.`;
        } finally {
            this.isSubmitting = false;
        }
    }
}" x-init="fetchPixel()">
    <nav class="bg-purple-600 text-white p-4 shadow-md rounded-2xl">
        <div class="container-fluid flex justify-between items-center">
            <h1 class="text-2xl font-bold">Pixel Setup</h1>
            <div class="flex space-x-4">
                <a href="https://obstacourse.co.uk/" class="px-3 py-2 rounded hover:bg-purple-500 font-bold">Home</a>
                <button @click="showLogoutModal = true"
                    class="px-3 py-2 rounded font-bold hover:bg-purple-500 cursor-pointer">Logout</button>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div
            class="bg-white/80 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-purple-200/30 backdrop-blur-lg">
            <h1
                class="text-5xl font-extrabold text-center bg-purple-500 text-transparent bg-clip-text mb-10 drop-shadow-xl animate-gradient">
                <i class="fas fa-code text-purple-300 mr-3 animate-bounce text-4xl"></i>
                Pixel Setup
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage"
                class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-lg border border-purple-100/50"
                :class="formStatus === 'success' ? 'bg-purple-100/80 text-purple-900' : 'bg-red-100/80 text-red-900'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-4">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-purple-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Loading Indicator -->
            <div x-show="isLoading"
                class="mb-8 p-5 rounded-2xl flex items-center justify-center bg-purple-100/80 text-purple-900">
                <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>Loading pixel data...</span>
            </div>

            <!-- Form -->
            <form id="pixelForm" x-show="!isLoading" action="" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Header Pixel Code -->
                    <div class="relative">
                        <label for="header" class="block text-xl font-bold text-purple-900 mb-3">
                            Header Pixel Code
                        </label>
                        <textarea name="header" id="header" x-model="header"
                            class="w-full px-5 py-4 rounded-xl border border-purple-300 focus:outline-none focus:ring-4 focus:ring-purple-400/50 transition-all duration-500 bg-white/70 shadow-inner text-purple-900 placeholder-purple-400 focus:bg-white hover:scale-[1.02] transform hover:shadow-lg"
                            placeholder="Enter header pixel code (e.g., Google Analytics, Facebook Pixel)" rows="6"></textarea>
                    </div>

                    <!-- Body Pixel Code -->
                    <div class="relative">
                        <label for="body" class="block text-xl font-bold text-purple-900 mb-3">
                            Body Pixel Code
                        </label>
                        <textarea name="body" id="body" x-model="body"
                            class="w-full px-5 py-4 rounded-xl border border-purple-300 focus:outline-none focus:ring-4 focus:ring-purple-400/50 transition-all duration-500 bg-white/70 shadow-inner text-purple-900 placeholder-purple-400 focus:bg-white hover:scale-[1.02] transform hover:shadow-lg"
                            placeholder="Enter body pixel code (e.g., tracking scripts in body)" rows="6"></textarea>
                    </div>

                    <!-- Footer Pixel Code -->
                    <div class="relative md:col-span-2">
                        <label for="footer" class="block text-xl font-bold text-purple-900 mb-3">
                            Footer Pixel Code
                        </label>
                        <textarea name="footer" id="footer" x-model="footer"
                            class="w-full px-5 py-4 rounded-xl border border-purple-300 focus:outline-none focus:ring-4 focus:ring-purple-400/50 transition-all duration-500 bg-white/70 shadow-inner text-purple-900 placeholder-purple-400 focus:bg-white hover:scale-[1.02] transform hover:shadow-lg"
                            placeholder="Enter footer pixel code (e.g., tracking scripts)" rows="6"></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center mt-8">
                    <button type="button"
                        :disabled="isSubmitting || (!header.trim() && !body.trim() && !footer.trim())"
                        @click="submitForm()"
                        class="w-full md:w-1/2 px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-2 flex items-center justify-center relative overflow-hidden"
                        :class="(isSubmitting || (!header.trim() && !body.trim() && !footer.trim())) ?
                        'bg-purple-400 cursor-not-allowed' :
                        'bg-gradient-to-r from-purple-500 to-purple-600 hover:bg-gradient-to-r hover:from-purple-600 hover:to-purple-700 hover:ring-4 hover:ring-purple-400/50'">
                        <span
                            class="absolute inset-0 bg-purple-700 opacity-30 hover:opacity-50 transition-opacity duration-500 z-0"></span>
                        <div class="flex items-center relative z-10">
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
                                    <span>Submitting...</span>
                                </div>
                            </template>
                            <template x-if="!isSubmitting">
                                <div class="flex items-center">
                                    <i class="fas fa-save mr-3 animate-pulse"></i>
                                    <span>Save Pixel Code</span>
                                </div>
                            </template>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .glassmorphic {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .slide-up {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 6s ease infinite;
        }
    </style>
</div>
