<div x-data="{
    bookings: [],
    months: [],
    selectedBooking: null,
    showViewModal: false,
    showDeleteModal: false,
    deleteBookingId: null,
    selectedMonth: '{{ $selectedMonth ?? \Carbon\Carbon::now()->format('F') }}',
    errorMessage: null,
    deleteError: null,
    async fetchMonths() {
        try {
            const response = await fetch('/bookings/months', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            this.months = await response.json();
            this.errorMessage = null;
        } catch (error) {
            console.error('Error fetching months:', error);
            this.months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            this.errorMessage = 'Failed to load months. Using default list.';
        }
    },
    async fetchBookings(month) {
        try {
            const response = await fetch(`/bookings/api?month=${month}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            this.bookings = await response.json();
            this.errorMessage = null;
        } catch (error) {
            console.error('Error fetching bookings:', error);
            this.bookings = [];
            this.errorMessage = 'Failed to load bookings. Please try again later.';
        }
    },
    async deleteBooking(id) {
        try {
            this.deleteError = null;
            const response = await fetch(`{{ route('bookings.destroy', ':id') }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                }
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const result = await response.json();
            this.bookings = this.bookings.filter(booking => booking.id !== id);
            this.closeModals();
            this.errorMessage = result.success;
            setTimeout(() => { this.errorMessage = null; }, 3000);
        } catch (error) {
            console.error('Error deleting booking:', error);
            this.deleteError = 'Failed to delete booking. Please try again.';
        }
    },
    refreshBookings() {
        this.fetchBookings(this.selectedMonth);
    },
    get totalRevenue() {
        return this.bookings.reduce((sum, b) => sum + (Number(b.price) * (Number(b.quantity) || 1) || 0), 0);
    },
    openViewModal(booking) {
        this.selectedBooking = booking;
        this.showViewModal = true;
    },
    openDeleteModal(id) {
        this.deleteBookingId = id;
        this.showDeleteModal = true;
        this.deleteError = null;
    },
    closeModals() {
        this.showViewModal = false;
        this.showDeleteModal = false;
        this.selectedBooking = null;
        this.deleteBookingId = null;
        this.deleteError = null;
    },
    handleMonthChange(event) {
        this.selectedMonth = event.target.value;
        this.fetchBookings(this.selectedMonth); // no URL change
    },

    init() {
        this.fetchMonths();
        this.fetchBookings(this.selectedMonth);
    }
}" x-init="init" class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-50 p-6"
    x-cloak>

    <!-- Purple gradient background SVG -->
    <svg class="absolute top-0 left-0 w-full h-full pointer-events-none" viewBox="0 0 1440 600" fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0H1440V600C1440 600 1200 300 720 300C240 300 0 600 0 600V0Z" fill="url(#paint0_linear_1_3)"
            fill-opacity="0.05" />
        <defs>
            <linearGradient id="paint0_linear_1_3" x1="720" y1="0" x2="720" y2="600"
                gradientUnits="userSpaceOnUse">
                <stop stop-color="#8B5CF6" />
                <stop offset="1" stop-color="#A78BFA" />
            </linearGradient>
        </defs>
    </svg>

    <div class="container-fluid mx-auto relative z-10">
        <!-- Header with calendar icon -->
        <div class="flex items-center justify-center mb-8">
            <svg class="w-10 h-10 text-purple-600 mr-3" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                    clip-rule="evenodd"></path>
            </svg>
            <h1 class="text-3xl font-bold text-gray-800">Payment Management</h1>
        </div>

        <!-- Month Filter Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-purple-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Filter Payments
                    </h2>
                    <p class="text-sm text-gray-500">Select a month to view payments</p>
                </div>
                <div class="flex items-center">
                    <label for="month-filter" class="text-gray-700 font-medium mr-2 hidden md:block">Month:</label>
                    <div class="relative">
                        <select id="month-filter" x-model="selectedMonth" @change="handleMonthChange($event)"
                            class="appearance-none pl-3 pr-10 py-2 border border-purple-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-400 focus:border-purple-400 text-gray-700">
                            <template x-for="month in months" :key="month">
                                <option :value="month" x-text="month" :selected="month === selectedMonth">
                                </option>
                            </template>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-purple-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error/Success Message -->
        <div x-show="errorMessage"
            class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm flex items-start"
            x-bind:class="{ 'bg-green-100 border-green-500 text-green-700': errorMessage.includes('successfully') }">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" x-show="!errorMessage.includes('successfully')"></path>
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" x-show="errorMessage.includes('successfully')"></path>
            </svg>
            <span x-text="errorMessage"></span>
        </div>

        <!-- Payments Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-purple-100">
            <div class="px-6 py-4 border-b border-purple-100 bg-gradient-to-r from-purple-50 to-purple-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                            </path>
                        </svg>
                        Payments in <span class="text-purple-600 ml-1" x-text="selectedMonth"></span>
                    </h3>

                    <!-- Revenue badge -->
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full"
                        x-text="'Total Revenue this month: '+ new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(totalRevenue)">
                    </span>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full"
                            x-text="bookings.length + ' payments'"></span>
                        <button @click="refreshBookings"
                            class="text-purple-600 hover:text-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400 rounded-full p-1 group"
                            aria-label="Refresh bookings">
                            <i class="fa fa-refresh w-5 h-5 group-hover:rotate-90 transition-transform duration-300"
                                aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-purple-200">
                    <thead class="bg-purple-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-32">
                                ID
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-32">
                                Method
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                Payment Email
                            </th>

                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                Learner Email
                            </th>

                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                Learner ID
                            </th>

                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                               Course ID
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">
                                Price
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-48">
                                Date
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-32">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-purple-100">
                        <template x-for="booking in bookings" :key="booking.id">
                            <tr class="hover:bg-purple-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    x-text="booking.payment_unique_id"></td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="booking.payment_type"></td>

                                <td class="px-6 py-4 text-sm text-gray-900 break-words"
                                    x-text="booking.payment_type === 'paypal'
                                ? booking.paypal_email
                                : booking.payment_type === 'stripe'
                                    ? booking.stripe_email
                                    : ''">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    x-text="booking.email">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    x-text="booking.learner_secret_id">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    x-text="booking.course_unique_id">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                    x-text="'£' + Number(booking.price).toFixed(2)">
                                </td>


                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="new Date(booking.created_at).toLocaleDateString('en-GB', { 
                            day: 'numeric', month: 'long', year: 'numeric' 
                        })">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        <!-- Eye Icon -->
                                        <button @click="openViewModal(booking)"
                                            class="text-purple-600 hover:text-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-400 rounded-full p-1 group"
                                            aria-label="View booking details">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                        <!-- Delete Icon -->
                                        <button @click="openDeleteModal(booking.id)"
                                            class="text-red-600 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-400 rounded-full p-1 group"
                                            aria-label="Delete booking">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- No bookings row -->
                        <tr x-show="bookings.length === 0 && !errorMessage">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No bookings found for <span class="text-purple-500 font-medium"
                                    x-text="selectedMonth"></span>.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>

        <!-- Booking View Modal -->
        <div x-show="showViewModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center 
       bg-black/50 backdrop-blur-sm backdrop-brightness-150"
            @click="closeModals">

            <div @click.stop
                class="relative w-full max-w-7xl max-h-[80vh] p-8 overflow-y-auto bg-white border border-purple-200 rounded-2xl shadow-2xl">
                <!-- Decorative Top Bar -->
                <div
                    class="absolute top-0 left-0 w-full h-2 rounded-t-2xl bg-gradient-to-r from-purple-400 to-purple-600">
                </div>
                <!-- Modal Header -->
                <div class="flex items-center mb-6">
                    <svg class="w-8 h-8 mr-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800">Payment Information</h3>
                </div>
                <!-- Modal Content -->
                <div x-show="selectedBooking" class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-3">

                        <!-- Basic Info -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="flex items-center mb-2 font-semibold text-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Basic Information
                            </h4>
                            <p><span class="font-medium text-gray-700">Product ID:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.payment_unique_id"></span></p>
                            <p><span class="font-medium text-gray-700">Learner ID:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.learner_secret_id"></span></p>
                            <p><span class="font-medium text-gray-700">Name:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.name"></span></p>
                            <p><span class="font-medium text-gray-700">Email:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.email"></span></p>
                            <p><span class="font-medium text-gray-700">Phone:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.phone"></span></p>
                        </div>
                        <!-- Location Info -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="flex items-center mb-2 font-semibold text-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Location Details
                            </h4>
                            <p><span class="font-medium text-gray-700">City:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.city"></span></p>
                            <p><span class="font-medium text-gray-700">Address:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.address"></span></p>
                            <p><span class="font-medium text-gray-700">Postal Code:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.postal_code"></span></p>
                            <p><span class="font-medium text-gray-700">Country:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.country"></span></p>
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="space-y-3">
                        <!-- Course Info -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="flex items-center mb-2 font-semibold text-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd"
                                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Course Information
                            </h4>
                            <p><span class="font-medium text-gray-700">Course ID:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.course_unique_id"></span></p>
                            <p><span class="font-medium text-gray-700">Title:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.course_title"></span></p>
                            <p><span class="font-medium text-gray-700">For:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.whom"></span></p>
                            <p><span class="font-medium text-gray-700">Quantity:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.quantity"></span></p>
                            <p><span class="font-medium text-gray-700">Price:</span> <span
                                    class="text-gray-900">$<span x-text="selectedBooking.price"></span></span></p>
                        </div>
                        <!-- Payment Info -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="flex items-center mb-2 font-semibold text-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Payment Details
                            </h4>
                            <p><span class="font-medium text-gray-700">Type:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.payment_type"></span></p>
                            <p><span class="font-medium text-gray-700">Status:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.status"></span></p>
                            <p><span class="font-medium text-gray-700">Transaction ID:</span> <span
                                    class="text-gray-900" x-text="selectedBooking.transaction_id"></span></p>
                            <p><span class="font-medium text-gray-700">Account ID:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.account_id"></span></p>
                            <p x-show="selectedBooking.paypal_email"><span class="font-medium text-gray-700">PayPal
                                    Email:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.paypal_email"></span></p>
                            <p x-show="selectedBooking.stripe_email"><span class="font-medium text-gray-700">Stripe
                                    Email:</span> <span class="text-gray-900"
                                    x-text="selectedBooking.stripe_email"></span></p>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="mt-6 flex justify-end">
                    <button @click="closeModals"
                        class="flex items-center px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-900">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm backdrop-brightness-150"
            @click="closeModals">
            <div @click.stop class="bg-white rounded-2xl p-8 w-96 shadow-xl border border-purple-200 relative">
                <!-- Purple decorative element -->
             
                <div class="text-center">
                    <svg class="mx-auto w-16 h-16 text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mt-4">Confirm Delete</h3>
                    <p class="text-gray-600 mt-2">Are you sure you want to delete this booking? This action cannot be
                        undone.</p>
                    <div x-show="deleteError" class="mt-4 p-2 bg-red-100 text-red-700 rounded-lg"
                        x-text="deleteError"></div>
                </div>
                <div class="mt-6 flex justify-center space-x-4">
                    <button @click="closeModals"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-300 focus:ring-2 focus:ring-gray-400 flex items-center"
                        aria-label="Cancel delete">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button @click="deleteBooking(deleteBookingId)"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 focus:ring-2 focus:ring-red-400 flex items-center"
                        aria-label="Confirm delete">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
