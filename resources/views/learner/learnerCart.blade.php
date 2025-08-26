@extends('home.default')

@section('content')
    <!-- Main Container -->
    <div class="bg-gradient-to-br from-purple-900 to-indigo-800">
        @include('home.navbar')
        <!-- SVG Background Decoration -->
        <svg class="absolute inset-0 w-full h-full opacity-20" preserveAspectRatio="none" viewBox="0 0 1440 800">
            <defs>
                <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#a855f7;stop-opacity:0.4" />
                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0.2" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGradient)" d="M0,400 C320,500 720,300 1440,400 L1440,800 L0,800 Z" />
            <circle cx="200" cy="100" r="50" fill="#d8b4fe" opacity="0.3" />
            <circle cx="1200" cy="600" r="70" fill="#a855f7" opacity="0.3" />
        </svg>

        <div class="mx-auto max-w-7xl relative z-10">
            <!-- Header with Animated SVG Progress Indicator -->
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-extrabold text-white mb-4 tracking-tight drop-shadow-md">
                    Your Cosmic Cart
                </h1>
                <div class="flex justify-center items-center gap-4">
                    <svg class="w-8 h-8 text-purple-300 animate-pulse" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-purple-200 font-medium">Explore Your Selections</p>
                </div>
            </div>

            <div x-data="cart" class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items Column -->
                <div class="lg:w-2/3">
                    <!-- Cart Items -->
                    <div class="bg-white bg-opacity-95 rounded-2xl shadow-xl p-6 mb-6 backdrop-blur-md">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-purple-900" x-text="items.length + ' Items'"></h2>
                            <p
                                class="text-purple-600 hover:text-purple-800 font-medium flex items-center transition-colors">
                                <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 7h14M5 7l2 10h10l2-10M5 7H3m18 0h-2m-7 10v4m-4-4v4" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Products in your cart
                            </p>
                        </div>

                        <!-- Cart Items -->
                        <template x-for="(item, index) in items" :key="item.id">
                            <div
                                class="flex flex-col sm:flex-row gap-4 py-6 border-b border-purple-100 last:border-b-0 hover:bg-purple-50 transition-colors rounded-lg">
                                <div
                                    class="w-full sm:w-28 h-28 bg-gradient-to-br from-purple-200 to-indigo-200 rounded-xl overflow-hidden flex items-center justify-center relative">
                                    <img :src="item.image" :alt="item.name"
                                        class="w-20 object-cover transform hover:scale-105 transition-transform">
                                    <svg class="absolute inset-0 w-full h-full opacity-30" viewBox="0 0 100 100">
                                        <path d="M0,50 Q25,25 50,50 T100,50" stroke="#a855f7" stroke-width="2"
                                            fill="none" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <div>
                                            <h3 class="font-bold text-purple-900" x-text="item.name"></h3>
                                            <h3 class="hidden" x-text="item.unique_id"></h3>
                                            <p class="text-purple-500 text-sm" x-text="'Category: ' + item.category"></p>
                                        </div>
                                        <button class="text-purple-400 hover:text-red-500 h-6 transition-colors"
                                            @click="removeItem(index)">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <button
                                                class="w-8 h-8 rounded-full bg-purple-100 hover:bg-purple-200 text-purple-600 flex items-center justify-center transition-colors transform hover:scale-110"
                                                @click="decreaseQuantity(index)">
                                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <span class="font-medium w-6 text-center text-purple-900"
                                                x-text="item.quantity"></span>
                                            <button
                                                class="w-8 h-8 rounded-full bg-purple-100 hover:bg-purple-200 text-purple-600 flex items-center justify-center transition-colors transform hover:scale-110"
                                                @click="increaseQuantity(index)">
                                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-purple-400 line-through text-sm" x-show="item.originalPrice"
                                                x-text="'$' + item.originalPrice.toFixed(2)"></p>
                                            <p class="text-lg font-bold text-purple-600"
                                                x-text="'$' + (item.price * item.quantity).toFixed(2)"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty Cart Message -->
                        <div x-show="!items.length" class="text-center py-10">
                            <svg class="w-16 h-16 mx-auto text-purple-300 animate-bounce" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <p class="text-purple-600 mt-4 font-medium">Your cart is empty. Start exploring now!</p>
                        </div>
                    </div>

                    <!-- Promo Code Section -->
                    <div class="bg-white bg-opacity-95 rounded-2xl shadow-xl p-6 backdrop-blur-md mb-10">
                        <h3 class="font-bold text-purple-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15 5v2m0 4v2m0 4v2M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Have a Promo Code?
                        </h3>
                        <div class="flex relative">
                            <input type="text" x-model="couponCode" placeholder="Enter promo code"
                                class="flex-1 border border-purple-200 rounded-l-xl px-4 py-3 bg-purple-50 text-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all">
                            <button
                                class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 rounded-r-xl transition-colors font-medium flex items-center"
                                @click="applyCoupon">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Apply
                            </button>
                            <svg class="absolute -top-2 -right-2 w-8 h-8 text-purple-300 animate-spin-slow"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16z"
                                    fill="currentColor" opacity="0.3" />
                            </svg>
                        </div>
                        <p x-show="discount > 0" class="text-green-500 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            Discount applied: <span x-text="'$' + discount"></span>
                        </p>
                    </div>
                </div>

                <!-- Order Summary Column -->
                <div class="lg:w-1/3">
                    <div class="bg-white bg-opacity-95 rounded-2xl shadow-xl p-6 sticky top-6 backdrop-blur-md">
                        <h2 class="text-xl font-bold text-purple-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Order Summary
                        </h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-purple-600" x-text="'Subtotal (' + items.length + ' items)'"></span>
                                <span class="font-medium text-purple-900" x-text="'$' + subtotal"></span>
                            </div>
                            <div class="flex justify-between" x-show="discount > 0">
                                <span class="text-purple-600">Discount</span>
                                <span class="font-medium text-green-500" x-text="'-$' + discount"></span>
                            </div>
                        </div>

                        <div class="border-t border-purple-200 pt-4 mb-6">
                            <div class="flex justify-between">
                                <span class="font-bold text-lg text-purple-900">Estimated Total</span>
                                <span class="font-bold text-purple-600 text-lg" x-text="'$' + total"></span>
                            </div>
                        </div>

                        @if (Auth::guard('learner')->check())
                            <!-- Checkout Button -->
                            <button
                                class="w-full bg-gradient-to-r from-purple-500 to-indigo-500 text-white py-4 rounded-xl font-bold hover:from-purple-600 hover:to-indigo-600 transition-all shadow-lg shadow-purple-300/50 transform hover:scale-105 flex items-center justify-center"
                                @click="checkout">
                                <svg class="w-5 h-5 mr-2" ...></svg>
                                Proceed to Checkout
                            </button>
                        @else
                            <a href="{{ route('learner.login', ['redirect' => route('learner.cart')]) }}"
                                class="mt-6 block w-full px-6 py-3 bg-purple-800 hover:bg-purple-900 text-white rounded-xl 
                                transition duration-300 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1 
                                flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login First to Checkout
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cart', () => ({
                // Course data passed from the backend
                course: @json($course),

                // Initial cart state
                items: [],
                couponCode: '',
                discount: 0,

                // Initialize the cart from localStorage
                init() {
                    const savedCart = localStorage.getItem('cartItems');
                    this.items = savedCart ? JSON.parse(savedCart) : [];

                    this.couponCode = localStorage.getItem('couponCode') || '';
                    this.discount = parseFloat(localStorage.getItem('discount')) || 0;

                    if (this.course && this.course.id) {
                        const courseInCart = this.items.find(item => item.id === this.course.id);
                        if (!courseInCart) {
                            this.items.push({
                                id: this.course.id,
                                unique_id: this.course.unique_id || 'Not showing',
                                name: this.course.course_title,
                                category: this.course.category || 'Course',
                                price: this.course.price || 'N/A',
                                originalPrice: this.course.discount_price || 'N/A',
                                quantity: 1,
                                image: this.course.image ||
                                    'https://images.unsplash.com/photo-1554568218-0f1715e72254'
                            });
                            localStorage.setItem('cartItems', JSON.stringify(this
                            .items)); // Save immediately
                        }
                    }

                    this.$watch('items', (newItems) => {
                        localStorage.setItem('cartItems', JSON.stringify(newItems));
                    });
                    this.$watch('couponCode', (newCode) => {
                        localStorage.setItem('couponCode', newCode);
                    });
                    this.$watch('discount', (newDiscount) => {
                        localStorage.setItem('discount', newDiscount);
                    });
                },

                increaseQuantity(index) {
                    this.items[index].quantity++;
                },

                decreaseQuantity(index) {
                    if (this.items[index].quantity > 1) {
                        this.items[index].quantity--;
                    }
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                applyCoupon() {
                    if (this.couponCode.toLowerCase() === 'save10') {
                        this.discount = this.subtotal * 0.1; // 10% discount
                        alert('Coupon applied! You saved 10%.');
                    } else {
                        this.discount = 0;
                        alert('Invalid coupon code.');
                    }
                },

                updateCart() {
                    alert('Cart updated!');
                    // Add your update logic here (e.g., save to backend)
                },

                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
                        .toFixed(2);
                },

                get total() {
                    return (parseFloat(this.subtotal) - parseFloat(this.discount)).toFixed(2);
                },

                checkout() {
                    window.location.href = "{{ route('learner.payment') }}";
                }
            }));
        });
    </script>

    @include('home.footer')
@endsection
