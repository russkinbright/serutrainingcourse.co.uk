@extends('home.default')


<style>
    /* Custom animations for input fields */
    .input-focus {
        transition: all 0.3s ease-in-out;
    }

    .input-focus:focus {
        transform: scale(1.02);
        box-shadow: 0 0 10px rgba(13, 148, 136, 0.5);
    }

    /* SVG animation for header icons */
    .svg-animate path {
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        animation: draw 2s ease-in-out forwards;
    }

    @keyframes draw {
        to {
            stroke-dashoffset: 0;
        }
    }

    /* Fade out animation for messages */
    .fade-out {
        animation: fadeOut 0.5s ease-in-out forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            display: none;
        }
    }

    /* Purple SVG styling */
    .svg-purple {
        stroke: #7C3AED;
        /* Tailwind purple-600 */
        fill: none;
    }

    .svg-purple-filled {
        fill: #6B21A8;
        /* Tailwind purple-800 */
    }

    /* Form background with SVG */
    .form-background {
        position: relative;        
        background-size: 100px 100px;
        border-radius: 1rem;
        overflow: hidden;
    }

    .form-background::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        /* Subtle white overlay for readability */
        z-index: 0;
    }

    .form-background>* {
        position: relative;
        z-index: 1;
    }

    /* Font Awesome icon styling */
    .input-icon {
        position: relative;
    }

    .input-icon i {
        position: absolute;
        left: 12px;
        top: 36px;
        color: #7C3AED;
        /* Tailwind purple-600 */
        font-size: 1rem;
    }

    .input-icon input,
    .input-icon select {
        padding-left: 2.5rem;
        /* Space for icon */
    }

    /* Dropdown styling */
    .country-dropdown {
        position: relative;
    }

    .country-dropdown select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .country-dropdown .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 2px solid #7C3AED;
        /* Tailwind purple-600 */
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
        display: none;
    }

    .country-dropdown .dropdown-menu.show {
        display: block;
    }

    .country-dropdown .search-input {
        width: 100%;
        padding: 0.5rem;
        padding-left: 2.5rem;
        border: 2px solid #7C3AED;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .country-dropdown .dropdown-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
        color: black;
    }

    .country-dropdown .dropdown-item:hover {
        background: #EDE9FE;
        /* Tailwind purple-100 */
    }

    .country-dropdown .dropdown-item.selected {
        background: #C4B5FD;
        /* Tailwind purple-300 */
        font-weight: bold;
    }
</style>

<div class="mx-auto px-4 py-8 max-w-7xl" x-data="{
    messages: [],
    showPasswords: false,
    password: '',
    passwordError: false,
    countryOpen: false,
    countrySearch: '',
    selectedCountry: '{{ old('country', $learner->country) }}',
    countries: [
        { value: 'Afghanistan', text: 'Afghanistan' },
        { value: 'Åland Islands', text: 'Åland Islands' },
        { value: 'Albania', text: 'Albania' },
        { value: 'Algeria', text: 'Algeria' },
        { value: 'American Samoa', text: 'American Samoa' },
        { value: 'Andorra', text: 'Andorra' },
        { value: 'Angola', text: 'Angola' },
        { value: 'Anguilla', text: 'Anguilla' },
        { value: 'Antarctica', text: 'Antarctica' },
        { value: 'Antigua and Barbuda', text: 'Antigua and Barbuda' },
        { value: 'Argentina', text: 'Argentina' },
        { value: 'Armenia', text: 'Armenia' },
        { value: 'Aruba', text: 'Aruba' },
        { value: 'Australia', text: 'Australia' },
        { value: 'Austria', text: 'Austria' },
        { value: 'Azerbaijan', text: 'Azerbaijan' },
        { value: 'Bahamas', text: 'Bahamas' },
        { value: 'Bahrain', text: 'Bahrain' },
        { value: 'Bangladesh', text: 'Bangladesh' },
        { value: 'Barbados', text: 'Barbados' },
        { value: 'Belarus', text: 'Belarus' },
        { value: 'Belgium', text: 'Belgium' },
        { value: 'Belize', text: 'Belize' },
        { value: 'Benin', text: 'Benin' },
        { value: 'Bermuda', text: 'Bermuda' },
        { value: 'Bhutan', text: 'Bhutan' },
        { value: 'Bolivia', text: 'Bolivia' },
        { value: 'Bosnia and Herzegovina', text: 'Bosnia and Herzegovina' },
        { value: 'Botswana', text: 'Botswana' },
        { value: 'Brazil', text: 'Brazil' },
        { value: 'British Indian Ocean Territory', text: 'British Indian Ocean Territory' },
        { value: 'Brunei Darussalam', text: 'Brunei Darussalam' },
        { value: 'Bulgaria', text: 'Bulgaria' },
        { value: 'Burkina Faso', text: 'Burkina Faso' },
        { value: 'Burundi', text: 'Burundi' },
        { value: 'Cambodia', text: 'Cambodia' },
        { value: 'Cameroon', text: 'Cameroon' },
        { value: 'Canada', text: 'Canada' },
        { value: 'Cape Verde', text: 'Cape Verde' },
        { value: 'Cayman Islands', text: 'Cayman Islands' },
        { value: 'Central African Republic', text: 'Central African Republic' },
        { value: 'Chad', text: 'Chad' },
        { value: 'Chile', text: 'Chile' },
        { value: 'China', text: 'China' },
        { value: 'Colombia', text: 'Colombia' },
        { value: 'Comoros', text: 'Comoros' },
        { value: 'Congo', text: 'Congo' },
        { value: 'Costa Rica', text: 'Costa Rica' },
        { value: 'Croatia', text: 'Croatia' },
        { value: 'Cuba', text: 'Cuba' },
        { value: 'Cyprus', text: 'Cyprus' },
        { value: 'Czech Republic', text: 'Czech Republic' },
        { value: 'Denmark', text: 'Denmark' },
        { value: 'Djibouti', text: 'Djibouti' },
        { value: 'Dominica', text: 'Dominica' },
        { value: 'Dominican Republic', text: 'Dominican Republic' },
        { value: 'Ecuador', text: 'Ecuador' },
        { value: 'Egypt', text: 'Egypt' },
        { value: 'El Salvador', text: 'El Salvador' },
        { value: 'Equatorial Guinea', text: 'Equatorial Guinea' },
        { value: 'Eritrea', text: 'Eritrea' },
        { value: 'Estonia', text: 'Estonia' },
        { value: 'Eswatini', text: 'Eswatini' },
        { value: 'Ethiopia', text: 'Ethiopia' },
        { value: 'Fiji', text: 'Fiji' },
        { value: 'Finland', text: 'Finland' },
        { value: 'France', text: 'France' },
        { value: 'Gabon', text: 'Gabon' },
        { value: 'Gambia', text: 'Gambia' },
        { value: 'Georgia', text: 'Georgia' },
        { value: 'Germany', text: 'Germany' },
        { value: 'Ghana', text: 'Ghana' },
        { value: 'Greece', text: 'Greece' },
        { value: 'Grenada', text: 'Grenada' },
        { value: 'Guatemala', text: 'Guatemala' },
        { value: 'Guinea', text: 'Guinea' },
        { value: 'Guinea-Bissau', text: 'Guinea-Bissau' },
        { value: 'Guyana', text: 'Guyana' },
        { value: 'Haiti', text: 'Haiti' },
        { value: 'Honduras', text: 'Honduras' },
        { value: 'Hungary', text: 'Hungary' },
        { value: 'Iceland', text: 'Iceland' },
        { value: 'India', text: 'India' },
        { value: 'Indonesia', text: 'Indonesia' },
        { value: 'Iran', text: 'Iran' },
        { value: 'Iraq', text: 'Iraq' },
        { value: 'Ireland', text: 'Ireland' },
        { value: 'Israel', text: 'Israel' },
        { value: 'Italy', text: 'Italy' },
        { value: 'Jamaica', text: 'Jamaica' },
        { value: 'Japan', text: 'Japan' },
        { value: 'Jordan', text: 'Jordan' },
        { value: 'Kazakhstan', text: 'Kazakhstan' },
        { value: 'Kenya', text: 'Kenya' },
        { value: 'Kiribati', text: 'Kiribati' },
        { value: 'Kuwait', text: 'Kuwait' },
        { value: 'Kyrgyzstan', text: 'Kyrgyzstan' },
        { value: 'Laos', text: 'Laos' },
        { value: 'Latvia', text: 'Latvia' },
        { value: 'Lebanon', text: 'Lebanon' },
        { value: 'Lesotho', text: 'Lesotho' },
        { value: 'Liberia', text: 'Liberia' },
        { value: 'Libya', text: 'Libya' },
        { value: 'Liechtenstein', text: 'Liechtenstein' },
        { value: 'Lithuania', text: 'Lithuania' },
        { value: 'Luxembourg', text: 'Luxembourg' },
        { value: 'Madagascar', text: 'Madagascar' },
        { value: 'Malawi', text: 'Malawi' },
        { value: 'Malaysia', text: 'Malaysia' },
        { value: 'Maldives', text: 'Maldives' },
        { value: 'Mali', text: 'Mali' },
        { value: 'Malta', text: 'Malta' },
        { value: 'Marshall Islands', text: 'Marshall Islands' },
        { value: 'Mauritania', text: 'Mauritania' },
        { value: 'Mauritius', text: 'Mauritius' },
        { value: 'Mexico', text: 'Mexico' },
        { value: 'Micronesia', text: 'Micronesia' },
        { value: 'Moldova', text: 'Moldova' },
        { value: 'Monaco', text: 'Monaco' },
        { value: 'Mongolia', text: 'Mongolia' },
        { value: 'Montenegro', text: 'Montenegro' },
        { value: 'Morocco', text: 'Morocco' },
        { value: 'Mozambique', text: 'Mozambique' },
        { value: 'Myanmar', text: 'Myanmar' },
        { value: 'Namibia', text: 'Namibia' },
        { value: 'Nauru', text: 'Nauru' },
        { value: 'Nepal', text: 'Nepal' },
        { value: 'Netherlands', text: 'Netherlands' },
        { value: 'New Zealand', text: 'New Zealand' },
        { value: 'Nicaragua', text: 'Nicaragua' },
        { value: 'Niger', text: 'Niger' },
        { value: 'Nigeria', text: 'Nigeria' },
        { value: 'North Macedonia', text: 'North Macedonia' },
        { value: 'Norway', text: 'Norway' },
        { value: 'Oman', text: 'Oman' },
        { value: 'Pakistan', text: 'Pakistan' },
        { value: 'Palau', text: 'Palau' },
        { value: 'Panama', text: 'Panama' },
        { value: 'Papua New Guinea', text: 'Papua New Guinea' },
        { value: 'Paraguay', text: 'Paraguay' },
        { value: 'Peru', text: 'Peru' },
        { value: 'Philippines', text: 'Philippines' },
        { value: 'Poland', text: 'Poland' },
        { value: 'Portugal', text: 'Portugal' },
        { value: 'Qatar', text: 'Qatar' },
        { value: 'Romania', text: 'Romania' },
        { value: 'Russia', text: 'Russia' },
        { value: 'Rwanda', text: 'Rwanda' },
        { value: 'Saint Kitts and Nevis', text: 'Saint Kitts and Nevis' },
        { value: 'Saint Lucia', text: 'Saint Lucia' },
        { value: 'Saint Vincent and the Grenadines', text: 'Saint Vincent and the Grenadines' },
        { value: 'Samoa', text: 'Samoa' },
        { value: 'San Marino', text: 'San Marino' },
        { value: 'Sao Tome and Principe', text: 'Sao Tome and Principe' },
        { value: 'Saudi Arabia', text: 'Saudi Arabia' },
        { value: 'Senegal', text: 'Senegal' },
        { value: 'Serbia', text: 'Serbia' },
        { value: 'Seychelles', text: 'Seychelles' },
        { value: 'Sierra Leone', text: 'Sierra Leone' },
        { value: 'Singapore', text: 'Singapore' },
        { value: 'Slovakia', text: 'Slovakia' },
        { value: 'Slovenia', text: 'Slovenia' },
        { value: 'Solomon Islands', text: 'Solomon Islands' },
        { value: 'Somalia', text: 'Somalia' },
        { value: 'South Africa', text: 'South Africa' },
        { value: 'South Sudan', text: 'South Sudan' },
        { value: 'Spain', text: 'Spain' },
        { value: 'Sri Lanka', text: 'Sri Lanka' },
        { value: 'Sudan', text: 'Sudan' },
        { value: 'Suriname', text: 'Suriname' },
        { value: 'Sweden', text: 'Sweden' },
        { value: 'Switzerland', text: 'Switzerland' },
        { value: 'Syria', text: 'Syria' },
        { value: 'Taiwan', text: 'Taiwan' },
        { value: 'Tajikistan', text: 'Tajikistan' },
        { value: 'Tanzania', text: 'Tanzania' },
        { value: 'Thailand', text: 'Thailand' },
        { value: 'Timor-Leste', text: 'Timor-Leste' },
        { value: 'Togo', text: 'Togo' },
        { value: 'Tonga', text: 'Tonga' },
        { value: 'Trinidad and Tobago', text: 'Trinidad and Tobago' },
        { value: 'Tunisia', text: 'Tunisia' },
        { value: 'Turkey', text: 'Turkey' },
        { value: 'Turkmenistan', text: 'Turkmenistan' },
        { value: 'Tuvalu', text: 'Tuvalu' },
        { value: 'Uganda', text: 'Uganda' },
        { value: 'Ukraine', text: 'Ukraine' },
        { value: 'United Arab Emirates', text: 'United Arab Emirates' },
        { value: 'United Kingdom', text: 'United Kingdom' },
        { value: 'United States', text: 'United States' },
        { value: 'Uruguay', text: 'Uruguay' },
        { value: 'Uzbekistan', text: 'Uzbekistan' },
        { value: 'Vanuatu', text: 'Vanuatu' },
        { value: 'Venezuela', text: 'Venezuela' },
        { value: 'Vietnam', text: 'Vietnam' },
        { value: 'Yemen', text: 'Yemen' },
        { value: 'Zambia', text: 'Zambia' },
        { value: 'Zimbabwe', text: 'Zimbabwe' }
    ],
    filteredCountries() {
        if (!this.countrySearch) return this.countries;
        return this.countries.filter(country =>
            country.text.toLowerCase().includes(this.countrySearch.toLowerCase())
        );
    },
    selectCountry(country) {
        this.selectedCountry = country.value;
        this.countryOpen = false;
        this.countrySearch = '';
    },
    init() {
        @if(session('success'))
        this.messages.push({ type: 'success', text: '{{ session('success') }}' });
        @endif
        @if(session('error'))
        this.messages.push({ type: 'error', text: '{{ session('error') }}' });
        @endif
        @if($errors->any())
        this.messages.push({ type: 'error', text: @json(implode('<br>', $errors->all())) });
        @endif
        this.messages.forEach((msg, index) => {
            setTimeout(() => {
                this.messages[index].fadeOut = true;
                setTimeout(() => this.messages.splice(index, 1), 500);
            }, 2000);
        });
    },
    async submitForm(formId, url, resetForm = false) {
        const form = document.getElementById(formId);
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type=submit]');
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        if (formId === 'password-form') {
            const password = formData.get('password');
            const passwordConfirmation = formData.get('password_confirmation');
            const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
            if (!regex.test(password)) {
                this.messages.push({
                    type: 'error',
                    text: 'Password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.'
                });
                submitButton.disabled = false;
                submitButton.textContent = 'Update Password';
                this.autoHideMessage();
                return;
            }
            if (password !== passwordConfirmation) {
                this.messages.push({
                    type: 'error',
                    text: 'New password and confirmation do not match.'
                });
                submitButton.disabled = false;
                submitButton.textContent = 'Update Password';
                this.autoHideMessage();
                return;
            }
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'PUT'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();
            this.messages.push({
                type: response.ok ? 'success' : 'error',
                text: result.message || (response.ok ? 'Update successful!' : 'An error occurred. Please try again.')
            });

            if (response.ok && resetForm) {
                form.reset();
                this.password = '';
                this.passwordError = false;
                this.selectedCountry = '';
                this.countrySearch = '';
            }
        } catch (error) {
            console.error('Fetch error:', error);
            this.messages.push({
                type: 'error',
                text: 'Network error. Please check your connection and try again.'
            });
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = formId === 'password-form' ? 'Update Password' : 'Update Profile';
            this.autoHideMessage();
        }
    },
    autoHideMessage() {
        this.messages.forEach((msg, index) => {
            if (!msg.fadeOut) {
                setTimeout(() => {
                    this.messages[index].fadeOut = true;
                    setTimeout(() => this.messages.splice(index, 1), 500);
                }, 2000);
            }
        });
    },
    validatePassword() {
        const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
        this.passwordError = !regex.test(this.password);
    }
}">

<!-- SVG Wave Background -->
        <svg class="absolute inset-0 w-full h-full opacity-20 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 1440 800">
            <defs>
                <linearGradient id="waveGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#832ad6;stop-opacity:0.4" />
                    <stop offset="100%" style="stop-color:#1c65da;stop-opacity:0.2" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGradient1)" d="M0,400 C320,500 720,300 1440,400 L1440,800 L0,800 Z" />
            <circle cx="200" cy="100" r="50" fill="#d8b4fe" opacity="0.3" />
            <circle cx="1200" cy="600" r="70" fill="#a855f7" opacity="0.3" />
        </svg>
        
        
    <!-- Message Container -->
    <div id="message-container" class="mb-6">
        <template x-for="(message, index) in messages" :key="index">
            <div :class="{
                'p-4 rounded-lg shadow-lg text-sm font-semibold animate-pulse': true,
                'bg-teal-100 text-teal-800 border border-teal-300': message.type === 'success',
                'bg-red-100 text-red-800 border border-red-300': message.type === 'error',
                'fade-out': message.fadeOut
            }"
                x-html="message.text"></div>
        </template>
    </div>

    <div class="flex items-center mb-6">
        <button onclick="window.location.href='/learner/page'"
            class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white font-semibold rounded-xl shadow-lg border-2 border-white hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
            <svg class="w-5 h-5 svg-animate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Edit Profile Section -->
        <div class="form-background rounded-2xl shadow-2xl p-6">
            <h2 class="text-3xl font-bold text-black mb-6 flex items-center">
                <svg class="w-8 h-8 mr-3 svg-animate svg-purple" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
                Edit Profile
            </h2>

            <!-- Profile Information Form -->
            <form id="profile-form"
                @submit.prevent="submitForm('profile-form', '{{ route('learner.profile.update') }}')">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="input-icon">
                        <label for="name" class="block text-sm font-medium text-black">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $learner->name) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500"
                            required>
                        <i class="fa fa-user mt-1"></i>
                    </div>
                    <!-- Email -->
                    <div class="input-icon">
                        <label for="email" class="block text-sm font-medium text-black">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $learner->email) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 bg-purple-100 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500 cursor-not-allowed"
                            readonly>
                        <i class="fa fa-envelope mt-1.5"></i>
                    </div>
                    <!-- Card Number -->
                    <div class="input-icon">
                        <label for="card" class="block text-sm font-medium text-black">Card Number</label>
                        <input type="text" name="card" id="card" value="{{ old('card', $learner->card) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-credit-card mt-1.5"></i>
                    </div>
                    <!-- Card Expiry -->
                    <div class="input-icon">
                        <label for="card_expiry" class="block text-sm font-medium text-black">Card Expiry</label>
                        <input type="date" name="card_expiry" id="card_expiry"
                            value="{{ old('card_expiry', $learner->card_expiry) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-calendar mt-1.5"></i>
                    </div>
                    <!-- Card Code -->
                    <div class="input-icon">
                        <label for="card_code" class="block text-sm font-medium text-black">Card Code</label>
                        <input type="text" name="card_code" id="card_code"
                            value="{{ old('card_code', $learner->card_code) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-lock mt-1.5"></i>
                    </div>
                    <!-- Phone -->
                    <div class="input-icon">
                        <label for="phone" class="block text-sm font-medium text-black">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $learner->phone) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-phone mt-1.5"></i>
                    </div>
                    <!-- Country -->
                    <div class="input-icon country-dropdown" x-data="{ open: false }"
                        x-on:click.outside="open = false; countryOpen = false">
                        <label for="country" class="block text-sm font-medium text-black">Country</label>
                        <div class="">
                            <input type="text" readonly x-model="selectedCountry"
                                @click="open = true; countryOpen = true" placeholder="Select a country"
                                class="w-full cursor-pointer rounded-lg border-2 border-purple-500 p-3 shadow-sm">
                            <i class="fa fa-globe mt-1"></i>
                            <div class="dropdown-menu" :class="{ 'show': open }">
                                <input type="text" class="search-input" placeholder="Search country..."
                                    x-model="countrySearch">
                                <template x-for="country in filteredCountries()" :key="country.value">
                                    <div class="dropdown-item"
                                        :class="{ 'selected': country.value === selectedCountry }"
                                        x-on:click="selectCountry(country); open = false">
                                        <span x-text="country.text"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="country" :value="selectedCountry">

                    </div>
                    <!-- City -->
                    <div class="input-icon">
                        <label for="city" class="block text-sm font-medium text-black">City</label>
                        <input type="text" name="city" id="city"
                            value="{{ old('city', $learner->city) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-city mt-1.5"></i>
                    </div>
                    <!-- Address -->
                    <div class="input-icon">
                        <label for="address" class="block text-sm font-medium text-black">Address</label>
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $learner->address) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-home mt-1.5"></i>
                    </div>
                    <!-- Postal Code -->
                    <div class="input-icon">
                        <label for="postal_code" class="block text-sm font-medium text-black">Postal Code</label>
                        <input type="text" name="postal_code" id="postal_code"
                            value="{{ old('postal_code', $learner->postal_code) }}"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500">
                        <i class="fa fa-map-pin mt-1.5"></i>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-purple-700 text-white font-semibold rounded-xl shadow-lg border-2 border-white hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                            fill="currentColor">
                            <path
                                d="M535.6 85.7C513.7 63.8 478.3 63.8 456.4 85.7L432 110.1L529.9 208L554.3 183.6C576.2 161.7 576.2 126.3 554.3 104.4L535.6 85.7zM236.4 305.7C230.3 311.8 225.6 319.3 222.9 327.6L193.3 416.4C190.4 425 192.7 434.5 199.1 441C205.5 447.5 215 449.7 223.7 446.8L312.5 417.2C320.7 414.5 328.2 409.8 334.4 403.7L496 241.9L398.1 144L236.4 305.7zM160 128C107 128 64 171 64 224L64 480C64 533 107 576 160 576L416 576C469 576 512 533 512 480L512 384C512 366.3 497.7 352 480 352C462.3 352 448 366.3 448 384L448 480C448 497.7 433.7 512 416 512L160 512C142.3 512 128 497.7 128 480L128 224C128 206.3 142.3 192 160 192L256 192C273.7 192 288 177.7 288 160C288 142.3 273.7 128 256 128L160 128z" />
                        </svg>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div class="form-background rounded-2xl shadow-2xl p-6">
            <h3 class="text-3xl font-bold text-black mb-6 flex items-center">
                <svg class="w-14 h-14 text-purple-600" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path
                        d="M320 96C284.7 96 256 124.7 256 160L256 224L448 224C483.3 224 512 252.7 512 288L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 288C128 252.7 156.7 224 192 224L192 160C192 89.3 249.3 32 320 32C383.5 32 436.1 78.1 446.2 138.7C449.1 156.1 437.4 172.6 419.9 175.6C402.4 178.6 386 166.8 383 149.3C378 119.1 351.7 96 320 96zM360 424C373.3 424 384 413.3 384 400C384 386.7 373.3 376 360 376L280 376C266.7 376 256 386.7 256 400C256 413.3 266.7 424 280 424L360 424z" />
                </svg>
                Change Password
            </h3>
            <form id="password-form"
                @submit.prevent="submitForm('password-form', '{{ route('learner.password.update') }}', true)">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <!-- Current Password -->
                    <div class="input-icon">
                        <label for="current_password" class="block text-sm font-medium text-black">Current
                            Password</label>
                        <input :type="showPasswords ? 'text' : 'password'" name="current_password"
                            id="current_password"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500"
                            required>
                        <i class="fa fa-lock-open mt-1.5"></i>
                    </div>
                    <!-- New Password -->
                    <div class="input-icon">
                        <label for="password" class="block text-sm font-medium text-black">New Password</label>
                        <input :type="showPasswords ? 'text' : 'password'" name="password" id="password"
                            x-model="password" @input="validatePassword"
                            :class="{ 'border-red-500': passwordError, 'border-purple-500': !passwordError }"
                            class="mt-1 block w-full rounded-lg border-2 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500"
                            required>
                        <i class="fa fa-lock mt-1.5"></i>
                        <p x-show="passwordError" class="mt-1 text-sm text-red-600">Password must contain at least 8
                            characters, one uppercase, one lowercase, one number, and one special character.</p>
                    </div>
                    <!-- Confirm Password -->
                    <div class="input-icon">
                        <label for="password_confirmation" class="block text-sm font-medium text-black">Confirm New
                            Password</label>
                        <input :type="showPasswords ? 'text' : 'password'" name="password_confirmation"
                            id="password_confirmation"
                            class="mt-1 block w-full rounded-lg border-2 border-purple-500 text-black p-3 placeholder-gray-300 shadow-sm input-focus focus:border-purple-400 focus:ring-purple-500"
                            required>
                        <i class="fa fa-lock mt-1.5"></i>
                    </div>
                </div>
                <div class="flex items-center mt-4">
                    <input type="checkbox" id="show-passwords" x-model="showPasswords"
                        class="mr-2 h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="show-passwords" class="text-sm font-medium text-black cursor-pointer">Show
                        Passwords</label>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-purple-700 text-white font-semibold rounded-xl shadow-lg border-2 border-white hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer">
                        <svg class="w-6 h-6 text-white" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 640 640">
                            <path
                                d="M400 416C497.2 416 576 337.2 576 240C576 142.8 497.2 64 400 64C302.8 64 224 142.8 224 240C224 258.7 226.9 276.8 232.3 293.7L71 455C66.5 459.5 64 465.6 64 472L64 552C64 565.3 74.7 576 88 576L168 576C181.3 576 192 565.3 192 552L192 512L232 512C245.3 512 256 501.3 256 488L256 448L296 448C302.4 448 308.5 445.5 313 441L346.3 407.7C363.2 413.1 381.3 416 400 416zM440 160C462.1 160 480 177.9 480 200C480 222.1 462.1 240 440 240C417.9 240 400 222.1 400 200C400 177.9 417.9 160 440 160z" />
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
