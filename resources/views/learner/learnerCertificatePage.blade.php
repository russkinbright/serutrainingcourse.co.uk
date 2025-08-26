<div x-data="courseCertificateApp()" x-init="fetchCourses()" class="container-fluid mx-auto p-4">
    <!-- Error Message -->
    <template x-if="error">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4" role="alert">
            <p x-text="error"></p>
        </div>
    </template>


    <!-- Course Grid -->
    <div id="learnerCoursePanel" x-show="!showCertificatePage"
        class="bg-gray-900/80 backdrop-blur-md p-6 rounded-xl shadow-2xl max-w-2xl mx-auto">
        <template x-for="course in courses" :key="course.unique_id">
            <div
                class="flex items-center justify-between bg-gray-800/50 p-4 rounded-lg mb-4 transition-all duration-300 hover:bg-gray-700/50 hover:shadow-lg">
                <span x-text="course.title" class="text-white font-medium"></span>
                <template x-if="course.is_completed">
                    <button @click="showCourseCertificate(course.unique_id)"
                        class="flex items-center gap-2 bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-black font-semibold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        aria-label="View certificate">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        View Certificate
                    </button>
                </template>
            </div>
        </template>
        <template x-if="!courses.length">
            <p class="text-gray-400 text-center">No courses available</p>
        </template>
    </div>


    <!-- Certificate Page -->
    <div id="learnerCourseCertificate" x-show="showCertificatePage"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-10"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-10" class="bg-white rounded-2xl shadow-lg p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-extrabold text-purple-800 flex items-center">
                <svg class="w-8 h-8 mr-3 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l2.4 4.8 5.4.8-3.9 3.8.9 5.3-4.8-2.5-4.8 2.5.9-5.3-3.9-3.8 5.4-.8L12 2z" />
                </svg>
                <span x-text="`Certificate for ${selectedCertificate?.course_title || 'Course'}`"></span>
            </h2>
            <button
                @click="showCertificatePage = false; document.getElementById('learnerCoursePanel').classList.remove('hidden'); document.getElementById('learnerCourseCertificate').classList.add('hidden')"
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                Back to Courses
            </button>
        </div>

        <!-- Certificate Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <template x-for="cert in certificateOptions" :key="cert.type">
                <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-md p-6 flex flex-col items-center text-center hover:shadow-lg transition duration-300 certificate-card"
                    :class="{ 'border-2 border-purple-500': selectedCertificateType === cert.type }">
                    <img :src="cert.image" class="h-[400px] w-96 object-contain mb-4 rounded-lg shadow-sm"
                        :alt="cert.title">
                    <h3 class="text-xl font-semibold text-purple-800 mb-2" x-text="cert.title"></h3>
                    <p class="text-gray-600 text-sm mb-4" x-text="cert.description"></p>
                    <p class="text-2xl font-bold text-purple-600 mb-4" x-text="`£${cert.price.toFixed(2)}`"></p>
                    <button @click="selectCertificateType(cert.type)"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-300"
                        :disabled="cert.price === 0 && selectedCertificateType !== cert.type">
                        <template x-if="cert.price === 0">
                            <a :href="`/certificate/show/${selectedCertificate?.course_unique_id}`" target="_blank">
                                Download Free
                            </a>
                        </template>
                        <template x-if="cert.price > 0">
                            <span>Order Now</span>
                        </template>
                    </button>
                </div>
            </template>
        </div>

        <!-- Order Form -->
        <template x-if="selectedCertificateType">
            <form @submit.prevent="submitCertificateOrder" class="bg-purple-50 p-8 rounded-xl border border-purple-200">
                <h3 class="text-2xl font-semibold text-purple-800 mb-6">Order Details</h3>

                <!-- Course Name -->
                <div class="mb-6">
                    <label for="courseName" class="block text-sm font-semibold text-gray-700 mb-2">Course Name</label>
                    <input type="text" id="courseName" :value="selectedCertificate?.course_title || 'Course'"
                        readonly
                        class="block w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 shadow-sm cursor-not-allowed">
                </div>

                <!-- Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Certificate Type -->
                    <div>
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">Certificate Type <span
                                class="text-red-600">*</span></h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.certificateType" value="digital"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Digital Certificate (£0.00)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.certificateType" value="printed"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Printed Hardcopy (£19.99)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.certificateType" value="both"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Both (£19.99)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Transcript Type -->
                    <div>
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">Transcript Type <span
                                class="text-red-600">*</span></h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.transcriptType" value="none"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">None (£0.00)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.transcriptType" value="digital"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Digital Transcript (£5.00)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.transcriptType" value="printed"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Printed Transcript (£10.00)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="orderForm.transcriptType" value="both"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Both (£15.00)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Shipping -->
                    <div>
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">Shipping <span
                                class="text-red-600">*</span></h4>
                        <div class="space-y-2">
                            <label class="flex items-center"
                                :class="{ 'opacity-50 cursor-not-allowed': !requiresShipping }">
                                <input type="radio" x-model="orderForm.shippingMethod" value="none"
                                    :disabled="!requiresShipping"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">None (£0.00)</span>
                            </label>
                            <label class="flex items-center"
                                :class="{ 'opacity-50 cursor-not-allowed': !requiresShipping }">
                                <input type="radio" x-model="orderForm.shippingMethod" value="uk_not_recorded"
                                    :disabled="!requiresShipping"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">UK Not Recorded (£3.99)</span>
                            </label>
                            <label class="flex items-center"
                                :class="{ 'opacity-50 cursor-not-allowed': !requiresShipping }">
                                <input type="radio" x-model="orderForm.shippingMethod" value="international"
                                    :disabled="!requiresShipping"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">International (£20.00)</span>
                            </label>
                            <label class="flex items-center"
                                :class="{ 'opacity-50 cursor-not-allowed': !requiresShipping }">
                                <input type="radio" x-model="orderForm.shippingMethod" value="uk_recorded"
                                    :disabled="!requiresShipping"
                                    class="form-radio text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">UK Recorded (£10.00)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Total Price -->
                <div class="mb-8 p-4 bg-purple-100 rounded-lg flex justify-between items-center">
                    <span class="text-lg font-semibold text-purple-800">Total:</span>
                    <span class="text-2xl font-bold text-purple-600" x-text="`£${totalPrice.toFixed(2)}`"></span>
                </div>

                <!-- Personal Details -->
                <h4 class="text-lg font-semibold text-purple-800 mb-3">Personal Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="firstName" class="block text-sm font-semibold text-gray-700 mb-2">First Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" id="firstName" x-model="orderForm.firstName"
                            placeholder="Enter your first name"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                            :class="{ 'border-red-500 animate-shake': errors.firstName }" required>
                        <template x-if="errors.firstName">
                            <span class="text-red-500 text-xs mt-1" x-text="errors.firstName"></span>
                        </template>
                    </div>
                    <div>
                        <label for="lastName" class="block text-sm font-semibold text-gray-700 mb-2">Last Name <span
                                class="text-red-600">*</span></label>
                        <input type="text" id="lastName" x-model="orderForm.lastName"
                            placeholder="Enter your last name"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                            :class="{ 'border-red-500 animate-shake': errors.lastName }" required>
                        <template x-if="errors.lastName">
                            <span class="text-red-500 text-xs mt-1" x-text="errors.lastName"></span>
                        </template>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email <span
                                class="text-red-600">*</span></label>
                        <input type="email" id="email" x-model="orderForm.email"
                            placeholder="Enter your email"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                            :class="{ 'border-red-500 animate-shake': errors.email }" required>
                        <template x-if="errors.email">
                            <span class="text-red-500 text-xs mt-1" x-text="errors.email"></span>
                        </template>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone <span
                                class="text-red-600">*</span></label>
                        <input type="tel" id="phone" x-model="orderForm.phone"
                            placeholder="Enter your phone number"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                            :class="{ 'border-red-500 animate-shake': errors.phone }" required>
                        <template x-if="errors.phone">
                            <span class="text-red-500 text-xs mt-1" x-text="errors.phone"></span>
                        </template>
                    </div>
                </div>

                <!-- Delivery Details -->
                <template x-if="requiresShipping">
                    <div>
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">Delivery Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Address
                                    <span class="text-red-600">*</span></label>
                                <input type="text" id="address" x-model="orderForm.address"
                                    placeholder="Enter delivery address"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                                    :class="{ 'border-red-500 animate-shake': errors.address }" required>
                                <template x-if="errors.address">
                                    <span class="text-red-500 text-xs mt-1" x-text="errors.address"></span>
                                </template>
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City
                                    <span class="text-red-600">*</span></label>
                                <input type="text" id="city" x-model="orderForm.city"
                                    placeholder="Enter city"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                                    :class="{ 'border-red-500 animate-shake': errors.city }" required>
                                <template x-if="errors.city">
                                    <span class="text-red-500 text-xs mt-1" x-text="errors.city"></span>
                                </template>
                            </div>
                            <div>
                                <label for="postalCode" class="block text-sm font-semibold text-gray-700 mb-2">Postal
                                    Code <span class="text-red-600">*</span></label>
                                <input type="text" id="postalCode" x-model="orderForm.postalCode"
                                    placeholder="Enter postal code"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300"
                                    :class="{ 'border-red-500 animate-shake': errors.postalCode }" required>
                                <template x-if="errors.postalCode">
                                    <span class="text-red-500 text-xs mt-1" x-text="errors.postalCode"></span>
                                </template>
                            </div>
                            <div x-data="{ countrySearch: '', open: false, selectedIndex: -1 }" class="relative">
                                <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country
                                    <span class="text-red-600">*</span></label>

                                <input type="text" id="country" x-model="orderForm.country"
                                    placeholder="Select a country"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300 cursor-pointer"
                                    :class="{ 'border-red-500 animate-shake': errors.country }" readonly required
                                    @click="open = !open" @keydown.down.prevent="navigateDown"
                                    @keydown.up.prevent="navigateUp" @keydown.enter.prevent="selectCountry"
                                    role="combobox" :aria-expanded="open" aria-controls="country-list">

                                <!-- Chevron icon -->
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                <!-- Country Dropdown -->
                                <div x-show="open" x-transition id="country-list"
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                    role="listbox" @click.away="open = false">
                                    <input type="text" x-model="countrySearch" placeholder="Search countries..."
                                        class="block w-full px-4 py-3 border-b border-gray-200 focus:outline-none focus:ring-0"
                                        @input="filterCountries(); selectedIndex = -1"
                                        @keydown.down.prevent="navigateDown" @keydown.up.prevent="navigateUp"
                                        @keydown.enter.prevent="selectCountry">

                                    <template x-for="(country, index) in filteredCountries" :key="country">
                                        <div class="px-4 py-2 hover:bg-purple-50 cursor-pointer"
                                            :class="{ 'bg-purple-100': selectedIndex === index }"
                                            @click="orderForm.country = country; open = false; selectedIndex = -1"
                                            x-text="country" role="option" :aria-selected="selectedIndex === index">
                                        </div>
                                    </template>
                                </div>

                                <!-- Error -->
                                <template x-if="errors.country">
                                    <span class="text-red-500 text-xs mt-1" x-text="errors.country"></span>
                                </template>
                            </div>

                        </div>
                    </div>
                </template>

                <!-- Payment Details -->
                <template x-if="totalPrice > 0">
                    <div>
                        <h4 class="text-lg font-semibold text-purple-800 mb-3">Payment Details</h4>
                        <div class="mb-8">
                            <label for="paymentMethod" class="block text-sm font-semibold text-gray-700 mb-2">Payment
                                Method <span class="text-red-600">*</span></label>
                            <div class="relative">
                                <select id="paymentMethod" x-model="orderForm.payment_method"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-300 hover:shadow-md transition duration-300 appearance-none bg-white"
                                    :class="{ 'border-red-500 animate-shake': errors.payment_method }" required>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <template x-if="errors.payment_method">
                                <span class="text-red-500 text-xs mt-1" x-text="errors.payment_method"></span>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 mt-5">
                    <button type="button" @click="selectedCertificateType = null"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-300"
                        :disabled="isSubmitting || !isFormValid">
                        <template x-if="isSubmitting">
                            <span>Processing...</span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span>Submit Order</span>
                        </template>
                    </button>
                </div>
            </form>
        </template>
    </div>

    <!-- Message Container -->
    <div x-show="message.text" x-transition
        class="fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-semibold"
        :class="{
            'bg-green-100 text-green-800 border border-green-300': message.type === 'success',
            'bg-red-100 text-red-800 border border-red-300': message.type === 'error',
            'bg-yellow-100 text-yellow-800 border border-yellow-300': message.type === 'warning'
        }"
        x-text="message.text">
    </div>
</div>

<script>
    function courseCertificateApp() {
        return {
            courses: [],
            error: '',
            selectedCourse: null,
            selectedCourseDetails: null,
            selectedModules: [],
            showCertificatePage: false,
            selectedCertificate: null,
            certificateOptions: [],
            selectedCertificateType: null,
            orderForm: {
                certificateType: 'digital',
                transcriptType: 'none',
                shippingMethod: 'none',
                firstName: '',
                lastName: '',
                email: '',
                phone: '',
                address: '',
                city: '',
                postalCode: '',
                country: '',
                payment_method: 'card'
            },
            isSubmitting: false,
            message: {
                text: '',
                type: 'success'
            },
            errors: {
                firstName: '',
                lastName: '',
                email: '',
                phone: '',
                address: '',
                city: '',
                postalCode: '',
                country: '',
                payment_method: ''
            },
            countries: [
                'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina',
                'Armenia', 'Australia', 'Austria',
                'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize',
                'Benin', 'Bhutan',
                'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso',
                'Burundi', 'Cabo Verde', 'Cambodia',
                'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros',
                'Congo (Congo-Brazzaville)', 'Costa Rica',
                'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Democratic Republic of the Congo', 'Denmark',
                'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador',
                'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji',
                'Finland', 'France',
                'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea',
                'Guinea-Bissau',
                'Guyana', 'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq',
                'Ireland',
                'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait',
                'Kyrgyzstan',
                'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania',
                'Luxembourg', 'Madagascar',
                'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius',
                'Mexico', 'Micronesia',
                'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia',
                'Nauru', 'Nepal',
                'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia',
                'Norway', 'Oman', 'Pakistan',
                'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland',
                'Portugal', 'Qatar',
                'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia',
                'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia',
                'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia',
                'Solomon Islands', 'Somalia', 'South Africa',
                'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland',
                'Syria', 'Taiwan',
                'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago',
                'Tunisia', 'Turkey', 'Turkmenistan',
                'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay',
                'Uzbekistan', 'Vanuatu', 'Vatican City',
                'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
            ],
            countrySearch: '',
            filteredCountries: [],
            selectedIndex: -1,

            init() {
                this.filteredCountries = this.countries;
            },

            filterCountries() {
                this.filteredCountries = this.countries.filter(country =>
                    country.toLowerCase().includes(this.countrySearch.toLowerCase())
                );
            },

            navigateDown() {
                if (this.selectedIndex < this.filteredCountries.length - 1) {
                    this.selectedIndex++;
                    this.scrollIntoView();
                }
            },

            navigateUp() {
                if (this.selectedIndex > -1) {
                    this.selectedIndex--;
                    this.scrollIntoView();
                }
            },

            selectCountry() {
                if (this.selectedIndex >= 0 && this.selectedIndex < this.filteredCountries.length) {
                    this.orderForm.country = this.filteredCountries[this.selectedIndex];
                    this.open = false;
                    this.selectedIndex = -1;
                }
            },

            scrollIntoView() {
                const list = document.querySelector('#country-list');
                const selected = list.querySelector(`[role="option"][aria-selected="true"]`);
                if (selected) {
                    selected.scrollIntoView({
                        block: 'nearest'
                    });
                }
            },

            validateForm() {
                this.errors.firstName = this.orderForm.firstName ? '' : 'First name is required';
                this.errors.lastName = this.orderForm.lastName ? '' : 'Last name is required';
                this.errors.email = this.orderForm.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.orderForm.email) ?
                    '' : 'Valid email is required';
                this.errors.phone = this.orderForm.phone && /^\+?[\d\s-]{7,}$/.test(this.orderForm.phone) ? '' :
                    'Valid phone number is required';
                if (this.requiresShipping) {
                    this.errors.address = this.orderForm.address ? '' : 'Address is required';
                    this.errors.city = this.orderForm.city ? '' : 'City is required';
                    this.errors.postalCode = this.orderForm.postalCode ? '' : 'Postal code is required';
                    this.errors.country = this.orderForm.country ? '' : 'Country is required';
                } else {
                    this.errors.address = this.errors.city = this.errors.postalCode = this.errors.country = '';
                }
                this.errors.payment_method = this.totalPrice > 0 && this.orderForm.payment_method ? '' :
                    'Payment method is required';
            },

            get requiresShipping() {
                return this.orderForm.certificateType === 'printed' ||
                    this.orderForm.certificateType === 'both' ||
                    this.orderForm.transcriptType === 'printed' ||
                    this.orderForm.transcriptType === 'both';
            },

            get totalPrice() {
                let price = 0;
                if (this.orderForm.certificateType === 'printed' || this.orderForm.certificateType === 'both') {
                    price += 19.99;
                }
                if (this.orderForm.transcriptType === 'digital') {
                    price += 5.00;
                } else if (this.orderForm.transcriptType === 'printed') {
                    price += 10.00;
                } else if (this.orderForm.transcriptType === 'both') {
                    price += 15.00;
                }
                if (this.requiresShipping) {
                    if (this.orderForm.shippingMethod === 'uk_not_recorded') {
                        price += 3.99;
                    } else if (this.orderForm.shippingMethod === 'international') {
                        price += 20.00;
                    } else if (this.orderForm.shippingMethod === 'uk_recorded') {
                        price += 10.00;
                    }
                }
                return price;
            },

            get isFormValid() {
                this.validateForm();
                return !Object.values(this.errors).some(error => error);
            },

            async fetchCourses() {
                try {
                    const res = await fetch('/api/learner/courses', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.courses = data.courses;
                    } else {
                        this.error = data.message || 'Unable to load courses.';
                        this.showMessage(data.message || 'Unable to load courses.', 'error');
                    }
                } catch (err) {
                    this.error = 'Something went wrong.';
                    this.showMessage('Something went wrong.', 'error');
                }
            },

            async showCourseDetails(unique_id) {
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
                        document.getElementById('learnerCourseDetails').classList.remove('hidden');
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        this.error = data.message || 'Course not found.';
                        this.showMessage(data.message || 'Course not found.', 'error');
                    }
                } catch (err) {
                    this.error = 'Failed to load course details.';
                    this.showMessage('Failed to load course details.', 'error');
                }
            },

            async showContinueCourse(unique_id) {
                console.log('Continue course:', unique_id);
            },

            async showCourseCertificate(unique_id) {
                try {
                    const res = await fetch(`/api/learner/certificate/${unique_id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    console.log('Certificate API response:', data);
                    if (data.success) {
                        this.selectedCertificate = data.certificate;
                        this.certificateOptions = data.options;
                        this.showCertificatePage = true;
                        this.selectedCertificateType = null;
                        this.orderForm = {
                            certificateType: 'digital',
                            transcriptType: 'none',
                            shippingMethod: 'none',
                            firstName: '',
                            lastName: '',
                            email: '',
                            phone: '',
                            address: '',
                            city: '',
                            postalCode: '',
                            country: '',
                            payment_method: 'card'
                        };
                        Object.keys(this.errors).forEach(key => this.errors[key] = '');
                        document.getElementById('learnerCoursePanel').classList.add('hidden');
                        document.getElementById('learnerCourseCertificate').classList.remove('hidden');
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        this.showMessage(data.message || 'Certificate not available.', 'error');
                    }
                } catch (err) {
                    console.error('Certificate fetch error:', err);
                    this.showMessage('Failed to load certificate details.', 'error');
                }
            },

            selectCertificateType(type) {
                this.selectedCertificateType = type;
                this.orderForm.certificateType = type === 'digital' ? 'digital' : type === 'printed' ? 'printed' :
                    'both';
            },

            async submitCertificateOrder() {
                if (this.isSubmitting) return;
                this.validateForm();
                if (!this.isFormValid) {
                    this.showMessage('Please correct the errors in the form.', 'error');
                    return;
                }
                this.isSubmitting = true;
                try {
                    const res = await fetch(
                        `/api/learner/certificate/${this.selectedCertificate.course_unique_id}/order`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                certificate_type: this.orderForm.certificateType,
                                transcript_type: this.orderForm.transcriptType,
                                shipping_method: this.orderForm.shippingMethod,
                                first_name: this.orderForm.firstName,
                                last_name: this.orderForm.lastName,
                                email: this.orderForm.email,
                                phone: this.orderForm.phone,
                                address: this.orderForm.address,
                                city: this.orderForm.city,
                                postal_code: this.orderForm.postalCode,
                                country: this.orderForm.country,
                                payment_method: this.orderForm.payment_method,
                                total_price: this.totalPrice
                            })
                        });
                    const data = await res.json();
                    console.log('Order API response:', data);
                    if (data.success) {
                        this.showMessage('Certificate order placed successfully!', 'success');
                        this.showCertificatePage = false;
                        document.getElementById('learnerCoursePanel').classList.remove('hidden');
                        document.getElementById('learnerCourseCertificate').classList.add('hidden');
                    } else {
                        this.showMessage(data.message || 'Failed to place order.', 'error');
                    }
                } catch (err) {
                    console.error('Order submission error:', err);
                    this.showMessage('Error submitting order.', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            },

            showMessage(text, type = 'success', duration = 3000) {
                this.message.text = text;
                this.message.type = type;
                if (duration > 0) {
                    setTimeout(() => {
                        this.message.text = '';
                    }, duration);
                }
            }
        };
    }
</script>

<style>
    .certificate-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .certificate-card:hover {
        transform: translateY(-5px);
    }

    .form-radio:checked {
        background-color: #6b46c1;
        border-color: #6b46c1;
    }

    .form-radio:focus {
        box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.3);
    }

    select.appearance-none {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .animate-shake {
        animation: shake 0.3s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-4px);
        }

        75% {
            transform: translateX(4px);
        }
    }
</style>
