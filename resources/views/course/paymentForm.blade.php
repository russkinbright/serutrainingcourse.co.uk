@extends('home.default')

@section('content')
<div x-data="{
    cartItems: [],
    discount: 0,
    paymentMethod: 'paypal',
    cardDetails: { number: '', expiry: '', cvv: '' },
    billing: {
        fullName: '',
        email: '',
        phone: '',
        country: '',
        address: '',
        city: '',
        postalCode: '',
        whom: '',
        message: ''
    },
    messages: [],
    showTermsModal: false,
    showPrivacyModal: false,
    showBillingModal: false,
    processing: false,
    agreeTerms: false,
    cardValid: null,
    countrySearch: '',
    showCountryDropdown: false,

    /* ---------- GA4 helpers ---------- */
    ga4Items(items){
        return (items || []).map(i => ({
            item_id: String(i.unique_id ?? i.id ?? ''),
            item_name: i.title ?? 'Course',
            currency: 'GBP',
            price: Number(i.price || 0),
            quantity: Number(i.quantity || 1),
            item_brand: 'serutrainingcourse',
            item_category: i.category ?? 'Courses',
            item_variant: i.level ?? 'Default'
        }));
    },
    ga4Total(items){
        return (items || []).reduce((s,i)=> s + Number(i.price||0) * Number(i.quantity||1), 0);
    },
    pushBeginCheckout(){
        if (!this.cartItems.length) return;
        if (window.__beginCheckoutSentOnce) return; // fire once
        window.__beginCheckoutSentOnce = true;

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({
            event: 'begin_checkout',
            event_id: 'bc-' + Date.now(),
            ecommerce: {
                currency: 'GBP',
                value: parseFloat(this.total),
                items: this.ga4Items(this.cartItems)
            }
        });
    },
    /* ---------------------------------- */

    countries: [
        'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia',
        'Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus',
        'Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil',
        'Brunei','Bulgaria','Burkina Faso','Burundi','Cabo Verde','Cambodia','Cameroon','Canada',
        'Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo, Democratic Republic of the',
        'Congo, Republic of the','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark',
        'Djibouti','Dominica','Dominican Republic','East Timor','Ecuador','Egypt','El Salvador',
        'Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia','Fiji','Finland','France',
        'Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea',
        'Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia',
        'Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan',
        'Kenya','Kiribati','Korea, North','Korea, South','Kosovo','Kuwait','Kyrgyzstan','Laos',
        'Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg',
        'Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania',
        'Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco',
        'Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua',
        'Niger','Nigeria','North Macedonia','Norway','Oman','Pakistan','Palau','Panama',
        'Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Qatar',
        'Romania','Russia','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines',
        'Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles',
        'Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa',
        'South Sudan','Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria',
        'Taiwan','Tajikistan','Tanzania','Thailand','Togo','Tonga','Trinidad and Tobago',
        'Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates',
        'United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City',
        'Venezuela','Vietnam','Yemen','Zambia','Zimbabwe'
    ],

    init() {
        this.cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
        this.discount = JSON.parse(localStorage.getItem('cartDiscount')) || 0;

        if (this.cartItems.length === 0) {
            this.addMessage('Cart is empty. Please add courses.', 'error');
            return;
        }

        // Fire GA4 on entry
        this.pushBeginCheckout();

        if (window.AOS) AOS.init({ duration: 1000, easing: 'ease-in-out', once: false, mirror: true });
        window.scrollTo({ top: 0, behavior: 'smooth' });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.country-dropdown')) this.showCountryDropdown = false;
        });
    },

    get subtotal() {
        return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
    },
    get total() {
        return (parseFloat(this.subtotal) * (1 - this.discount)).toFixed(2);
    },

    addMissingFieldMessage() {
        const missing = [];
        if (!this.billing.country) missing.push('Country');
        if (!this.billing.address) missing.push('Address');
        if (!this.billing.city) missing.push('City');
        if (!this.billing.postalCode) missing.push('Postal Code');
        if (!this.billing.whom) missing.push('Whom');
        if (missing.length) {
            this.addMessage(`Please provide the following billing information: ${missing.join(', ')}.`, 'error');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    },

    addMessage(text, type) {
        const id = Date.now();
        this.messages.push({ id, text, type });
        if (type === 'success') {
            setTimeout(() => { this.messages = this.messages.filter(msg => msg.id !== id); }, 3000);
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    filteredCountries() {
        return this.countries.filter(c => c.toLowerCase().includes(this.countrySearch.toLowerCase())).slice(0, 10);
    },
    selectCountry(country) { this.billing.country = country; this.countrySearch = country; this.showCountryDropdown = false; },
    clearSearch() { this.countrySearch = ''; this.billing.country = ''; this.showCountryDropdown = true; },

    // === OTHER: auto-submit when selected ===
    async payWithOtherNow(){
        if (this.processing) return;

        // require terms + minimal billing
        if (!this.agreeTerms) { this.addMessage('Please agree to the terms and privacy policy.', 'error'); return; }
        if (!this.billing.country || !this.billing.address || !this.billing.city || !this.billing.postalCode || !this.billing.whom) {
            this.addMissingFieldMessage(); return;
        }
        if (this.billing.whom === 'company' && !this.billing.message) {
            this.addMessage('Please provide a message when selecting for <strong>Company</strong>.', 'error');
            return;
        }

        this.paymentMethod = 'other';
        await this.submitPayment(true); // silent path (no redirect)
    },

    async submitPayment(silentOnOther = false) {
        if (this.processing) return;
        this.processing = true;

        try {
            const cartItemsForPayment = this.cartItems.map(item => ({
                unique_id: item.unique_id,
                name: item.title,
                price: item.price,
                quantity: item.quantity,
                location: item.location,
                date: item.date,
                category: 'course'
            }));

            const response = await fetch('/payment/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cartItems: cartItemsForPayment,
                    billing: {
                        whom: this.billing.whom,
                        fullName: this.billing.fullName,
                        email: this.billing.email,
                        phone: this.billing.phone,
                        country: this.billing.country,
                        address: this.billing.address,
                        city: this.billing.city,
                        postalCode: this.billing.postalCode,
                        message: this.billing.message
                    },
                    paymentMethod: this.paymentMethod, // 'card' | 'paypal' | 'other'
                    cardDetails: this.paymentMethod === 'card' ? this.cardDetails : null,
                    total: this.total,
                    discount: this.discount
                })
            });

            const result = await response.json();

            if (!result.success) {
                this.addMessage(result.message || 'Payment failed.', 'error');
                return;
            }

            // Redirect flows
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
                return;
            }

            // Other flow: show success and clear
            localStorage.removeItem('cartItems');
            localStorage.removeItem('cartDiscount');
            this.addMessage('Purchase successfully completed!', 'success');

            setTimeout(() => { window.location.href = '/'; }, 1200);

        } catch (e) {
            console.error('Payment error:', e);
            this.addMessage('An error occurred. Please try again.', 'error');
        } finally {
            this.processing = false;
        }
    }
}" x-init="init()" class="relative">

    @include('main.navbar')

    <main id="top" class="relative bg-gray-100 min-h-screen">
        <div class="mx-auto max-w-[1500px] relative z-10 px-4 py-8">
            <button onclick="window.location.href='/learner/cart'"
                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r mb-4 from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg border-2 border-white 
                       hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer button"
                data-aos="fade-right">
                <svg class="w-5 h-5 svg-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </button>

            <div class="text-4xl font-bold mb-5 text-orange-500" data-aos="fade-down">
                <h1>Check Out</h1>
            </div>

            <!-- Messages -->
            <div class="mb-6" data-aos="fade-up">
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
                        <button @click="messages = messages.filter(m => m.id !== message.id)"
                            class="ml-4 text-xl font-bold text-black hover:text-gray-700 focus:outline-none">&times;</button>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div>
                    <!-- Billing Information -->
                    <div class="relative bg-white glassmorphic rounded-3xl p-8 mb-12 card-hover shadow-2xl overflow-hidden min-h-[650px]"
                        data-aos="fade-up">
                        <h2 class="text-3xl font-extrabold text-purple-900 mb-8 relative z-10 flex items-center">
                            <i class="fas fa-user mr-4 text-purple-500"></i> Billing Information
                        </h2>

                        <div class="space-y-4 grid grid-cols-2 gap-6 relative z-10">
                            <div class="col-span-2">
                                <label class="block text-gray-700 font-medium">Purchase For <span class="text-red-500">*</span></label>
                                <select x-model="billing.whom" required
                                        class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition">
                                    <option value="" disabled>Select Option</option>
                                    <option value="personal">Personal</option>
                                    <option value="company">Company (Please add a message)</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 font-medium">Full Name <span class="text-red-500">*</span></label>
                                <input x-model="billing.fullName" type="text" placeholder="Enter Your Full Name" required
                                       class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium">Email <span class="text-red-500">*</span></label>
                                <input x-model="billing.email" type="email" placeholder="Enter Your Valid Email" required
                                       class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium">Phone <span class="text-red-500">*</span></label>
                                <input x-model="billing.phone" type="tel" placeholder="Enter Valid Phone Number" required
                                       class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md">
                            </div>

                            <div class="relative country-dropdown">
                                <label class="block text-gray-700 font-medium">Country <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input x-model="billing.country" type="text" placeholder="Search Country"
                                           class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md"
                                           @input="countrySearch = $event.target.value; showCountryDropdown = true"
                                           @click="showCountryDropdown = true; countrySearch = billing.country"
                                           @keydown="if ($event.key === 'Backspace' && !billing.country) countrySearch = ''">
                                    <button @click="clearSearch" x-show="billing.country"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <input x-model="billing.country" type="hidden" required>
                                <div x-show="showCountryDropdown"
                                     class="absolute z-20 w-full mt-1 bg-white border border-purple-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="country in filteredCountries()" :key="country">
                                        <div @click="selectCountry(country)" class="px-4 py-2 hover:bg-purple-100 cursor-pointer text-sm">
                                            <span x-text="country"></span>
                                        </div>
                                    </template>
                                    <div x-show="filteredCountries().length === 0" class="px-4 py-2 text-gray-500 text-sm">
                                        No countries found
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium">Address <span class="text-red-500">*</span></label>
                                <input x-model="billing.address" type="text" placeholder="Address" required
                                       class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium">City <span class="text-red-500">*</span></label>
                                <input x-model="billing.city" type="text" placeholder="City" required
                                       class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium">Postal Code <span class="text-red-500">*</span></label>
                                <input x-model="billing.postalCode" type="text" placeholder="Postal Code" required
                                       class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 font-medium">Message (Optional)</label>
                                <textarea x-model="billing.message" placeholder="Message"
                                    class="w-full border-2 border-purple-500 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-pink-300 transition hover:shadow-md"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-purple-200 transform hover:-translate-y-2 transition-all duration-300"
                         data-aos="fade-up" data-aos-delay="200">
                        <h2 class="text-2xl font-bold text-purple-900 mb-4 font-orbitron flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Payment Method
                        </h2>

                        <div class="flex space-x-4 mb-4">
                            <button @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white' : 'bg-purple-100 text-purple-700'"
                                class="px-6 py-3 rounded-xl transition-all duration-300 hover:bg-purple-200 flex items-center font-bold hover:cursor-pointer hover:shadow-md">
                                <i class="fa fa-cc-visa mx-2"></i> Credit Card
                            </button>

                            <button @click="paymentMethod = 'paypal'"
                                :class="paymentMethod === 'paypal' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white' : 'bg-purple-100 text-purple-700'"
                                class="px-6 py-3 rounded-xl transition-all duration-300 hover:bg-purple-200 flex items-center font-bold hover:cursor-pointer hover:shadow-md">
                                <i class="fa fa-paypal mx-2"></i> PayPal
                            </button>

                            <!-- OTHER: auto submit -->
                            <button @click="payWithOtherNow()"
                                :class="paymentMethod === 'other' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white' : 'bg-purple-100 text-purple-700'"
                                class="px-6 py-3 rounded-xl transition-all duration-300 hover:bg-purple-200 flex items-center font-bold hover:cursor-pointer hover:shadow-md">
                                <i class="fa fa-bolt mx-2"></i> Other (Instant)
                            </button>
                        </div>

                        <div x-show="paymentMethod === 'card'" class="text-purple-600 flex items-center" data-aos="fade-up" data-aos-delay="300">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.076 2.395A6.563 6.563 0 0112 0h6.666C20.506 0 22 1.492 22 3.332v17.336C22 22.508 20.508 24 18.668 24H5.332C3.492 24 2 22.508 2 20.668V8.666h2v12.002c0 .736.596 1.332 1.332 1.332h13.336c.736 0 1.332-.596 1.332-1.332V3.332c0-.736-.596-1.332-1.332-1.332H12c-1.404 0-2.668.573-3.58 1.495l-.344.4H6.666C5.194 4 4 5.194 4 6.666v2H2v-2C2 4.086 4.086 2 6.666 2h.41z" />
                            </svg>
                            You will be redirected to Stripe to complete your payment.
                        </div>

                        <div x-show="paymentMethod === 'paypal'" class="text-purple-600 flex items-center" data-aos="fade-up" data-aos-delay="300">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.076 2.395A6.563 6.563 0 0112 0h6.666C20.506 0 22 1.492 22 3.332v17.336C22 22.508 20.508 24 18.668 24H5.332C3.492 24 2 22.508 2 20.668V8.666h2v12.002c0 .736.596 1.332 1.332 1.332h13.336c.736 0 1.332-.596 1.332-1.332V3.332c0-.736-.596-1.332-1.332-1.332H12c-1.404 0-2.668.573-3.58 1.495l-.344.4H6.666C5.194 4 4 5.194 4 6.666v2H2v-2C2 4.086 4.086 2 6.666 2h.41z" />
                            </svg>
                            You will be redirected to PayPal to complete your payment.
                        </div>

                        <div x-show="paymentMethod === 'other'" class="text-purple-600 flex items-center" data-aos="fade-up" data-aos-delay="300">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2L3 14h7v8l10-12h-7z"/></svg>
                            Processing instantly…
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary -->
                <div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-purple-200 transform hover:-translate-y-2 transition-all duration-500 sticky top-4"
                        data-aos="fade-up" data-aos-delay="100">
                        <h2 class="text-2xl font-bold text-purple-900 mb-4 font-orbitron flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Order Summary
                        </h2>

                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex items-center space-x-4 mb-4">
                                <img :src="item.image" class="w-16 h-16 rounded-md object-cover border border-purple-200 transform hover:scale-105 transition" alt="">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900" x-text="item.title"></h3>
                                    <p class="text-sm text-gray-600" x-text="'Quantity: ' + item.quantity"></p>
                                </div>
                                <span class="font-semibold text-gray-900" x-text="'£' + (item.price * item.quantity).toFixed(2)"></span>
                            </div>
                        </template>

                        <div class="border-t border-purple-200 mt-4 pt-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-900 font-semibold">Subtotal:</span>
                                <span class="text-gray-900 font-medium" x-text="'£' + subtotal"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-900 font-semibold">Total:</span>
                                <span class="text-purple-600 font-bold text-xl" x-text="'£' + total"></span>
                            </div>
                        </div>

                        <!-- Primary button for Card/PayPal -->
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="agreeTerms" class="mr-2 accent-purple-600">
                                <span class="text-gray-600">
                                    I agree to the <a href="#" @click.prevent="showTermsModal = true" class="text-purple-600 underline hover:text-purple-800">terms</a> and
                                    <a href="#" @click.prevent="showPrivacyModal = true" class="text-purple-600 underline hover:text-purple-800">privacy policy</a>.
                                </span>
                            </label>
                        </div>

                        <button @click="submitPayment()" :disabled="processing || !agreeTerms"
                            :class="processing || !agreeTerms ? 'bg-purple-300 cursor-not-allowed' :
                                'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700'"
                            class="w-full mt-6 py-3 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-[0_8px_20px_rgba(139,92,246,0.3)] flex items-center justify-center font-orbitron">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!processing">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span x-show="!processing">Confirm & Pay</span>
                            <span x-show="processing">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Modal (optional – keep your existing includes) -->
        <div x-show="showBillingModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            @include('learner.billingModal')
        </div>

        <!-- Terms Modal -->
        <div x-show="showTermsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            @include('learner.termsCondition')
        </div>

        <!-- Privacy Modal -->
        <div x-show="showPrivacyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            @include('learner.privacyPolicy')
        </div>

        @include('main.footer')
    </main>

    <style>
        .font-orbitron { font-family: 'Orbitron', sans-serif; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .glassmorphic { background: rgba(255,255,255,.15); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,.3); }
        .card-hover { transition: transform .4s ease, box-shadow .4s ease, background .4s ease; }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,.25); background: rgba(255,255,255,.2); }
        .animate-fade-in { animation: fadeIn .5s ease-in-out; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
    </style>

</div>

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://kit.fontawesome.com/69ba9af9da.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/card-validator@8.1.1/dist/card-validator.min.js"></script>
@endsection
@endsection
