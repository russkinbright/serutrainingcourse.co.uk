@extends('home.default')

@section('content')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div x-data="{
        cartItems: [],
        discount: 0,
        paymentMethod: 'card',
        cardDetails: { number: '', expiry: '', cvv: '' },
        billing: {
            fullName: '{{ Auth::user()->name ?? '' }}',
            email: '{{ Auth::user()->email ?? '' }}',
            phone: '{{ Auth::user()->phone ?? '' }}',
            country: '',
            address: '',
            city: '',
            postalCode: '',
            message: ''
        },
        messages: [],
        showBillingModal: false,
        showTermsModal: false,
        showPrivacyModal: false,
        processing: false,
        agreeTerms: false,
        cardValid: null,
        init() {
            const savedCart = localStorage.getItem('cartItems');
            this.cartItems = savedCart ? JSON.parse(savedCart) : [];
            // Fetch billing information
            fetch('/learner/billing', {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.billing) {
                        this.billing = { ...this.billing, ...data.billing };
                        // Check if all required billing fields are filled
                        if (!this.billing.country || !this.billing.address || !this.billing.city || !this.billing.postalCode || !this.billing.whom) {
                            this.showBillingModal = true;
                            this.addMissingFieldMessage();
                        }
                    } else {
                        this.showBillingModal = true;
                        this.addMessage('Please provide your billing information.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error fetching billing info:', error);
                    this.showBillingModal = true;
                    this.addMessage('Error fetching billing information.', 'error');
                });
        },
        get subtotal() {
            return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
        },
        get total() {
            return (parseFloat(this.subtotal) - this.discount).toFixed(2);
        },
        get billingButtonText() {
            // Check if all required fields are non-null
            if (this.billing.country && this.billing.address && this.billing.city && this.billing.postalCode && this.billing.whom) {
                return 'Edit Address';
            }
            // Identify which specific field is missing
            const missingFields = [];
            if (!this.billing.country) missingFields.push('Country');
            if (!this.billing.address) missingFields.push('Address');
            if (!this.billing.city) missingFields.push('City');
            if (!this.billing.postalCode) missingFields.push('Postal Code');
            if (!this.billing.whom) missingFields.push('Whom ?');
            if (missingFields.length === 1) {
                return `Add ${missingFields[0]}`;
            }
            return 'Add Address';
        },
        addMissingFieldMessage() {
            const missingFields = [];
            if (!this.billing.country) missingFields.push('Country');
            if (!this.billing.address) missingFields.push('Address');
            if (!this.billing.city) missingFields.push('City');
            if (!this.billing.postalCode) missingFields.push('Postal Code');
            if (!this.billing.whom) missingFields.push('Whom ?');
            if (missingFields.length > 0) {
                this.addMessage(`Please provide the following billing information: ${missingFields.join(', ')}.`, 'error');
            }
        },
        validateCard() {
            const card = cardValidator.number(this.cardDetails.number);
            this.cardValid = card.isValid;
            if (!card.isValid && this.cardDetails.number) {
                this.addMessage('Invalid card number.', 'error');
            } else if (card.isValid) {
                this.addMessage('Card number is valid.', 'success');
            }
        },
        addMessage(text, type) {
            const id = Date.now();
            this.messages.push({ id, text, type });
    
            // Only auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    this.messages = this.messages.filter(msg => msg.id !== id);
                }, 3000); // hide after 3 seconds
            }
        },
        async saveBilling() {
            if (!this.billing.country || !this.billing.address || !this.billing.city || !this.billing.postalCode || !this.billing.whom) {
                this.addMessage('Please fill in all required billing fields.', 'error');
                return;
            }
            if (this.billing.whom === 'company' && !this.billing.message) {
                this.addMessage('Please provide a message when selecting for <strong>Company</strong> .', 'error');
                return;
            }
            
            this.processing = true;
            try {
                const response = await fetch('/learner/billing/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        billing: this.billing
                    })
                });
                const result = await response.json();
                if (result.success) {
                    this.addMessage('Billing information saved successfully.', 'success');
                    this.showBillingModal = false;
                } else {
                    this.addMessage(result.message || 'Failed to save billing information.', 'error');
                }
            } catch (error) {
                console.error('Billing save error:', error);
                this.addMessage('An error occurred while saving billing information.', 'error');
            } finally {
                this.processing = false;
            }
        },
        async submitPayment() {
            if (!this.agreeTerms) {
                this.addMessage('Please agree to the terms and privacy policy.', 'error');
                return;
            }
            if (this.paymentMethod === 'card' && !this.cardValid) {
                this.addMessage('Please enter a valid card number.', 'error');
                return;
            }
            if (!this.billing.country || !this.billing.address || !this.billing.city || !this.billing.postalCode || !this.billing.whom) {
                this.addMessage('Please complete your billing information.', 'error');
                return;
            }
            this.processing = true;
            try {
                const response = await fetch('/learner/payment/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cartItems: this.cartItems,
                        billing: this.billing,
                        paymentMethod: this.paymentMethod,
                        cardDetails: this.paymentMethod === 'card' ? this.cardDetails : null,
                        total: this.total
                    })
                });
                const result = await response.json();
                if (result.success) {
                    this.addMessage(result.message, 'success');
                    localStorage.removeItem('cartItems');
                    setTimeout(() => {
                        window.location.href = '/learner/page';
                    }, 2000);
                } else {
                    this.addMessage(result.message || 'Payment failed.', 'error');
                }
            } catch (error) {
                console.error('Payment error:', error);
                this.addMessage('An error occurred. Please try again.', 'error');
            } finally {
                this.processing = false;
            }
        }
    }" class="bg-gradient-to-br from-purple-900 to-indigo-800">

        @include('home.navbar')
        <!-- SVG Wave Background -->
        <svg class="absolute inset-0 w-full h-full opacity-20" preserveAspectRatio="none" viewBox="0 0 1440 800">
            <defs>
                <linearGradient id="waveGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#a855f7;stop-opacity:0.4" />
                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0.2" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGradient1)" d="M0,400 C320,500 720,300 1440,400 L1440,800 L0,800 Z" />
            <circle cx="200" cy="100" r="50" fill="#d8b4fe" opacity="0.3" />
            <circle cx="1200" cy="600" r="70" fill="#a855f7" opacity="0.3" />
        </svg>

        <div class="mx-auto max-w-[1500px] relative z-10 px-4 py-8">
            <div class="text-4xl font-bold text-white mb-5 font-orbitron">
                <h1>Check Out</h1>
            </div>

            <!-- Message Container -->
            <div class="mb-6">
                <template x-for="message in messages" :key="message.id">
                    <div :class="message.type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
                        'bg-red-100 border-red-500 text-red-700'"
                        class="border-l-4 p-4 mb-2 rounded-r-lg flex items-center justify-between animate-fade-in">
                        <div class="flex items-center">
                            <svg :class="message.type === 'success' ? 'text-green-500' : 'text-red-500'"
                                class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="message.type === 'success' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'" />
                            </svg>
                            <span x-html="message.text"></span>
                        </div>

                        <!-- Dismiss (X) button -->
                        <button @click="messages = messages.filter(m => m.id !== message.id)"
                            class="ml-4 text-xl font-bold text-black hover:text-gray-700 focus:outline-none">
                            &times;
                        </button>
                    </div>
                </template>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div>

                    <div
                        class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 mb-8 border border-purple-200 transform hover:-translate-y-2 transition-all duration-500 hover:shadow-2xl">
                        <h2 class="text-2xl font-bold text-purple-900 mb-6 font-orbitron flex items-center">
                            <!-- User Icon -->
                            <svg class="w-7 h-7 mr-3 text-purple-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 14c-4 0-7 3-7 6h14c0-3-3-6-7-6z" />
                            </svg>
                            Billing Information
                        </h2>

                        <div>
                            <template x-if="billingButtonText === 'Edit Address'">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 text-sm mx-2">
                                    <!-- Full Name -->
                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-user text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>Name:</strong> <span x-text="billing.fullName"></span></span>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-user text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>email:</strong> <span x-text="billing.email"></span></span>
                                    </div>

                                    <!-- Phone -->
                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-phone text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>Phone:</strong> <span x-text="billing.phone"></span></span>
                                    </div>

                                    <!-- Country -->
                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-globe text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>Country:</strong> <span x-text="billing.country"></span></span>
                                    </div>

                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-envelope text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>Purchase For :</strong> <span x-text="billing.whom"></span></span>
                                    </div>

                                    <!-- City -->
                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-city text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>City:</strong> <span x-text="billing.city"></span></span>
                                    </div>

                                    <!-- Address -->
                                    <div class="flex items-start space-x-3 md:col-span-2">
                                        <i class="fa fa-map-marker-alt text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>Address:</strong> <span x-text="billing.address"></span></span>
                                    </div>

                                    <!-- Postal Code -->
                                    <div class="flex items-start space-x-3">
                                        <i class="fa fa-envelope text-purple-500 mt-1 w-5 text-base"></i>
                                        <span><strong>Postal Code:</strong> <span x-text="billing.postalCode"></span></span>
                                    </div>

                                </div>

                            </template>

                            <template x-else>
                                <p>Your invoice will be issued according to the details provided.</p>
                            </template>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-6">
                            <button @click="showBillingModal = true"
                                class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-md hover:shadow-[0_8px_20px_rgba(139,92,246,0.4)] transform hover:-translate-y-1 flex items-center">
                                <!-- Edit Icon -->
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.232 5.232l3.536 3.536m-2.036-2.036a2.5 2.5 0 013.536 3.536L9 21H5v-4L16.732 6.732z" />
                                </svg>
                                <span x-text="billingButtonText"></span>
                            </button>
                        </div>
                    </div>




                    <!-- Payment Method -->
                    <div
                        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-purple-200 transform hover:-translate-y-2 transition-all duration-300">
                        <h2 class="text-2xl font-bold text-purple-900 mb-4 font-orbitron flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Payment Method
                        </h2>
                        <div class="flex space-x-4 mb-4">
                            <button @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ?
                                    'bg-gradient-to-r from-purple-600 to-indigo-600 text-white' :
                                    'bg-purple-100 text-purple-700'"
                                class="px-6 py-3 rounded-xl transition-all duration-300 hover:bg-purple-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 abay24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Credit Card
                            </button>
                            <button @click="paymentMethod = 'paypal'"
                                :class="paymentMethod === 'paypal' ?
                                    'bg-gradient-to-r from-purple-600 to-indigo-600 text-white' :
                                    'bg-purple-100 text-purple-700'"
                                class="px-6 py-3 rounded-xl transition-all duration-300 hover:bg-purple-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7.076 2.395A6.563 6.563 0 0112 0h6.666C20.506 0 22 1.492 22 3.332v17.336C22 22.508 20.508 24 18.668 24H5.332C3.492 24 2 22.508 2 20.668V8.666h2v12.002c0 .736.596 1.332 1.332 1.332h13.336c.736 0 1.332-.596 1.332-1.332V3.332c0-.736-.596-1.332-1.332-1.332H12c-1.404 0-2.668.573-3.58 1.495l-.344.4H6.666C5.194 4 4 5.194 4 6.666v2H2v-2C2 4.086 4.086 2 6.666 2h.41z" />
                                </svg>
                                PayPal
                            </button>
                            <button @click="paymentMethod = 'other'"
                                :class="paymentMethod === 'other' ?
                                    'bg-gradient-to-r from-purple-600 to-indigo-600 text-white' :
                                    'bg-purple-100 text-purple-700'"
                                class="px-6 py-3 rounded-xl transition-all duration-300 hover:bg-purple-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7.076 2.395A6.563 6.563 0 0112 0h6.666C20.506 0 22 1.492 22 3.332v17.336C22 22.508 20.508 24 18.668 24H5.332C3.492 24 2 22.508 2 20.668V8.666h2v12.002c0 .736.596 1.332 1.332 1.332h13.336c.736 0 1.332-.596 1.332-1.332V3.332c0-.736-.596-1.332-1.332-1.332H12c-1.404 0-2.668.573-3.58 1.495l-.344.4H6.666C5.194 4 4 5.194 4 6.666v2H2v-2C2 4.086 4.086 2 6.666 2h.41z" />
                                </svg>
                                Other
                            </button>
                        </div>
                        <div x-show="paymentMethod === 'card'" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-medium">Card Number</label>
                                <input x-model="cardDetails.number" @input="validateCard" type="text"
                                    class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition"
                                    :class="cardValid === false ? 'border-red-500' : cardValid ? 'border-green-500' : ''">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-medium">Expiry</label>
                                    <input x-model="cardDetails.expiry" type="text"
                                        class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium">CVV</label>
                                    <input x-model="cardDetails.cvv" type="text"
                                        class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                                </div>
                            </div>
                        </div>

                        {{-- Other Payment Section --}}
                        <div x-show="paymentMethod === 'other'" class="text-purple-600 space-y-2">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a5 5 0 00-10 0v2m-2 0h14a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z" />
                                </svg>
                                <span>Other Payment Method (Testing)</span>
                            </div>
                            <p>Total Amount: <span x-text="'$' + total"></span></p>
                            <p>Click "Confirm & Pay" to record the payment for testing.</p>
                        </div>


                        <div x-show="paymentMethod === 'paypal'" class="text-purple-600 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M7.076 2.395A6.563 6.563 0 0112 0h6.666C20.506 0 22 1.492 22 3.332v17.336C22 22.508 20.508 24 18.668 24H5.332C3.492 24 2 22.508 2 20.668V8.666h2v12.002c0 .736.596 1.332 1.332 1.332h13.336c.736 0 1.332-.596 1.332-1.332V3.332c0-.736-.596-1.332-1.332-1.332H12c-1.404 0-2.668.573-3.58 1.495l-.344.4H6.666C5.194 4 4 5.194 4 6.666v2H2v-2C2 4.086 4.086 2 6.666 2h.41z" />
                            </svg>
                            You will be redirected to PayPal to complete your payment.
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary -->
                <div>
                    <div
                        class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-purple-200 transform hover:-translate-y-2 transition-all duration-500">
                        <h2 class="text-2xl font-bold text-purple-900 mb-4 font-orbitron flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Order Summary
                        </h2>
                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex items-center space-x-4 mb-4">
                                <img :src="item.image"
                                    class="w-16 h-16 rounded-md object-cover border border-purple-200 transform hover:scale-105 transition"
                                    alt="">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900" x-text="item.name"></h3>
                                    <h3 class="font-bold text-gray-900" x-text="item.unique_id"></h3>
                                    <p class="text-sm text-gray-600" x-text="'Quantity: ' + item.quantity"></p>
                                </div>
                                <span class="font-semibold text-gray-900"
                                    x-text="'$' + (item.price * item.quantity).toFixed(2)"></span>
                            </div>
                        </template>
                        <div class="border-t border-purple-200 mt-4 pt-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-900 font-semibold">Subtotal:</span>
                                <span class="text-gray-900 font-medium" x-text="'$' + subtotal"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-900 font-semibold">Total:</span>
                                <span class="text-purple-600 font-bold text-xl" x-text="'$' + total"></span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="agreeTerms" class="mr-2 accent-purple-600">
                                <span class="text-gray-600">I agree to the <a href="#"
                                        @click.prevent="showTermsModal = true"
                                        class="text-purple-600 underline hover:text-purple-800">terms</a> and <a
                                        href="#" @click.prevent="showPrivacyModal = true"
                                        class="text-purple-600 underline hover:text-purple-800">privacy policy</a>.</span>
                            </label>
                        </div>
                        <button @click="submitPayment()" :disabled="processing || !agreeTerms"
                            :class="processing || !agreeTerms ? 'bg-purple-300 cursor-not-allowed' :
                                'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700'"
                            class="w-full mt-6 py-3 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_8px_20px_rgba(139,92,246,0.3)] flex items-center justify-center font-orbitron">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                x-show="!processing">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span x-show="!processing">Confirm & Pay</span>
                            <span x-show="processing">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Modal -->
        <div x-show="showBillingModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div
                class="bg-white/90 backdrop-blur-sm rounded-2xl p-8 w-full max-w-2xl border border-purple-200 shadow-[0_10px_30px_rgba(139,92,246,0.3)] transform scale-95 transition-transform duration-300">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 font-orbitron flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Billing Information
                </h2>
                <div class="space-y-4 grid grid-cols-2 gap-5">

                    <div class="col-span-2">
                        <label class="block text-gray-700 font-medium">Purchase For ?</label>
                        <select x-model="billing.whom"
                            class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                            <option value="" disabled>Select Option</option>
                            <option value="personal">Personal</option>
                            <option value="company">Company</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-gray-700 font-medium">Full Name</label>
                        <input x-model="billing.fullName" type="text" readonly
                            class="w-full border border-purple-200 p-3 rounded-lg bg-gray-100 cursor-not-allowed focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Email</label>
                        <input x-model="billing.email" type="email" readonly
                            class="w-full border border-purple-200 p-3 rounded-lg bg-gray-100 cursor-not-allowed focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Phone</label>
                        <input x-model="billing.phone" type="tel" readonly
                            class="w-full border border-purple-200 p-3 rounded-lg bg-gray-100 cursor-not-allowed focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Country</label>
                        <input x-model="billing.country" type="text" placeholder="Country"
                            class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Address</label>
                        <input x-model="billing.address" type="text" placeholder="Address"
                            class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">City</label>
                        <input x-model="billing.city" type="text" placeholder="City"
                            class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium">Postal Code</label>
                        <input x-model="billing.postalCode" type="text" placeholder="Postal Code"
                            class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 font-medium">Message (Optional)</label>
                        <textarea x-model="billing.message" placeholder="Message"
                            class="w-full border border-purple-200 p-3 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-purple-600 transition"></textarea>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-4">
                    <button @click="showBillingModal = false"
                        class="bg-purple-100 text-purple-700 px-6 py-3 rounded-xl hover:bg-purple-200 transition font-semibold">Cancel</button>
                    <button @click="saveBilling()"
                        :class="processing ? 'bg-purple-300 cursor-not-allowed' :
                            'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700'"
                        class="px-6 py-3 rounded-xl text-white transition font-semibold">
                        <span x-show="!processing">Submit</span>
                        <span x-show="processing">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Terms Modal -->
        <div x-show="showTermsModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            @include('learner.termsCondition')
        </div>

        <!-- Privacy Modal -->
        <div x-show="showPrivacyModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            @include('learner.privacyPolicy')
        </div>

        @include('home.footer')
    </div>

    <style>
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }

        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(167, 139, 250, 0.6);
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(167, 139, 250, 0.6);
            animation: float 10s infinite;
        }

        .particle:nth-child(1) {
            left: 10%;
            top: 20%;
        }

        .particle:nth-child(2) {
            left: 30%;
            top: 50%;
        }

        .particle:nth-child(3) {
            left: 70%;
            top: 30%;
        }

        @keyframes float {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.7;
            }

            50% {
                transform: translateY(-20px) scale(1.2);
                opacity: 0.3;
            }

            100% {
                transform: translateY(0) scale(1);
                opacity: 0.7;
            }
        }

        @keyframes pulse-slow {
            0% {
                transform: scale(1);
                opacity: 0.9;
            }

            50% {
                transform: scale(1.05);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                opacity: 0.9;
            }
        }
    </style>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
