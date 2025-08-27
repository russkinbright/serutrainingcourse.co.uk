@extends('home.default')

@section('content')
    <style>
        /* Glassmorphism Effect */
        .glassmorphic {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* SVG Wave Background */
        .wave-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: -1;
        }

        /* Sticky Summary */
        .sticky-cta {
            position: sticky;
            top: 20px;
            z-index: 10;
        }

        /* Disable sticky on mobile */
        @media (max-width: 767px) {
            .sticky-cta {
                position: static;
            }
        }

        /* Table Styles */
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 1rem;
        }

        .cart-table th,
        .cart-table td {
            padding: 1rem;
            text-align: left;
        }

        .cart-table th {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            color: white;
            font-weight: 600;
        }

        .cart-table td {
            background: white;
        }

        .cart-table img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #fbcfe8;
        }

        .cart-table input {
            width: 80px;
            border: 2px solid #f472b6;
            border-radius: 8px;
            padding: 0.5rem;
            text-align: center;
        }

        /* Mobile Card Layout */
        @media (max-width: 767px) {
            .cart-table {
                display: none;
            }

            .cart-card {
                display: block;
            }
        }

        @media (min-width: 768px) {
            .cart-card {
                display: none;
            }
        }
    </style>

    <header
        class="fixed top-0 left-0 w-full bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg z-20 transition-all duration-300"
        data-header>
        @include('main.navbar')
    </header>

    <main id="top" class="relative bg-gray-100 min-h-screen pt-20">
        <!-- SVG Wave Background -->
        <svg class="wave-bg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="#f472b6" fill-opacity="0.3"
                d="M0,192L48,197.3C96,203,192,213,288,213.3C384,213,480,203,576,181.3C672,160,768,128,864,138.7C960,149,1056,203,1152,213.3C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
            </path>
        </svg>

        <div class="mx-auto max-w-[1500px] relative z-10 px-4 py-8">
            <div x-data="{
                cartItems: [],
                messages: [],
                init() {
                    const storedItems = JSON.parse(localStorage.getItem('cartItems')) || [];
            
                    // merge duplicates
                    const mergedItems = [];
                    storedItems.forEach(item => {
                        const existingItem = mergedItems.find(i => i.unique_id === item.unique_id);
                        if (existingItem) {
                            existingItem.quantity = (existingItem.quantity || 1) + (item.quantity || 1);
                        } else {
                            mergedItems.push({ ...item, quantity: item.quantity || 1 });
                        }
                    });
            
                    this.cartItems = mergedItems;
                    localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
            
                    // Fire AddToCart for everything currently in cart (optional but common)
                    if (this.cartItems.length) {
                        this.sendMetaAddToCart();
                    }
            
                    AOS.init({ duration: 800, easing: 'ease-in-out', once: true });
                },
            
                // ---- NEW: Meta Pixel helpers ----
                sendMetaAddToCart() {
                    try {
                        if (!window.fbq || !this.cartItems.length) return;
            
                        const ids = this.cartItems.map(i => String(i.unique_id));
                        const contents = this.cartItems.map(i => ({
                            id: String(i.unique_id),
                            quantity: i.quantity || 1,
                            item_price: Number(i.price || 0)
                        }));
                        const value = this.cartItems.reduce((sum, i) => sum + Number(i.price || 0) * (i.quantity || 1), 0);
                        const eventId = 'atc-' + Date.now() + '-' + Math.floor(Math.random() * 1e6);
            
                        fbq('track', 'AddToCart', {
                            content_ids: ids,
                            contents: contents,
                            content_type: 'product',
                            value: value,
                            currency: 'GBP',
                            event_id: eventId
                        });
                        // console.log('FB AddToCart sent', {ids, value, eventId});
                    } catch (e) {
                        console.error('FB AddToCart error', e);
                    }
                },
            
                sendMetaInitiateCheckout() {
                    try {
                        if (!window.fbq || !this.cartItems.length) return;
            
                        const ids = this.cartItems.map(i => String(i.unique_id));
                        const contents = this.cartItems.map(i => ({
                            id: String(i.unique_id),
                            quantity: i.quantity || 1,
                            item_price: Number(i.price || 0)
                        }));
                        const value = this.cartItems.reduce((sum, i) => sum + Number(i.price || 0) * (i.quantity || 1), 0);
                        const eventId = 'ic-' + Date.now() + '-' + Math.floor(Math.random() * 1e6);
            
                        fbq('track', 'InitiateCheckout', {
                            content_ids: ids,
                            contents: contents,
                            content_type: 'product',
                            num_items: this.cartItems.length,
                            value: value,
                            currency: 'GBP',
                            event_id: eventId
                        });
                        // console.log('FB InitiateCheckout sent', {ids, value, eventId});
                    } catch (e) {
                        console.error('FB InitiateCheckout error', e);
                    }
                },
            
            
                addMessage(text, type) {
                    const id = Date.now();
                    this.messages.push({ id, text, type });
                    if (type === 'success') {
                        setTimeout(() => {
                            this.messages = this.messages.filter(msg => msg.id !== id);
                        }, 3000);
                    }
                },
                updateQuantity(index, quantity) {
                    if (quantity < 1) {
                        this.addMessage('Quantity must be at least 1.', 'error');
                        this.cartItems[index].quantity = 1;
                    } else {
                        this.cartItems[index].quantity = parseInt(quantity);
                        this.addMessage('Cart updated!', 'success');
                    }
                    localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
                    // Optional: re-fire AddToCart after quantity change (comment out if you prefer not to)
                    // this.sendMetaAddToCart();
                },
                removeItem(index) {
                    this.cartItems.splice(index, 1);
                    localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
                    this.addMessage('Item removed from cart!', 'success');
                },
                clearCart() {
                    this.cartItems = [];
                    localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
                    this.addMessage('Cart cleared!', 'success');
                },
                getCartTotal() {
                    return this.cartItems.reduce((total, item) => total + (item.price * (item.quantity || 1)), 0).toFixed(2);
                },
            
                checkout() {
                    // fire InitiateCheckout before leaving the page
                    this.sendMetaInitiateCheckout();
                    window.location.href = '{{ route('learner.payment') }}';
                }
            }" x-init="init()">

                <!-- Message Container -->
                <div class="mb-6" data-aos="fade-up">
                    <template x-for="message in messages" :key="message.id">
                        <div :class="message.type === 'success' ? 'bg-green-100 border-green-500 text-green-700' :
                            'bg-red-100 border-red-500 text-red-700'"
                            class="border-l-4 p-4 mb-2 rounded-r-lg flex items-center justify-between animate-fade-in glassmorphic">
                            <div class="flex items-center">
                                <i :class="message.type === 'success' ? 'fas fa-check-circle text-green-500' :
                                    'fas fa-exclamation-circle text-red-500'"
                                    class="mr-2"></i>
                                <span x-text="message.text"></span>
                            </div>
                            <button @click="messages = messages.filter(m => m.id !== message.id)"
                                class="text-xl font-bold text-gray-700 hover:text-gray-900">
                                ×
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Cart Title -->
                <h1 class="text-3xl font-extrabold text-orange-600 mb-6 font-orbitron flex items-center" data-aos="fade-up">
                    <i class="fas fa-cart-shopping mr-2 text-orange-500"></i> Your Cart
                </h1>

                <!-- Cart Table (Desktop) -->
                <div x-show="cartItems.length > 0" class="mb-8" data-aos="fade-up">
                    <table class="cart-table glassmorphic rounded-2xl">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Course</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cartItems" :key="item.unique_id">
                                <tr class="card-hover">
                                    <td><img :src="item.image" :alt="item.title"></td>
                                    <td x-text="item.title"></td>
                                    <td x-text="'£' + item.price.toFixed(2)"></td>
                                    <td>
                                        <input type="number" x-model.number="cartItems[index].quantity" min="1"
                                            @change="updateQuantity(index, $event.target.value)"
                                            class="focus:ring-2 focus:ring-pink-300">
                                    </td>
                                    <td x-text="'£' + (item.price * (item.quantity || 1)).toFixed(2)"></td>
                                    <td>
                                        <button @click="removeItem(index)" class="cursor-pointer" aria-label="Remove item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Cart Cards (Mobile) -->
                <div x-show="cartItems.length > 0" class="mb-8" data-aos="fade-up">
                    <template x-for="(item, index) in cartItems" :key="item.unique_id">
                        <div class="cart-card bg-white glassmorphic rounded-2xl p-6 mb-4 card-hover">
                            <div class="flex items-center mb-4">
                                <img :src="item.image" :alt="item.title" class="mr-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700" x-text="item.title"></h3>
                                    <p class="text-green-600 font-bold" x-text="'£' + item.price.toFixed(2)"></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <label class="text-gray-700 mr-2">Quantity:</label>
                                    <input type="number" x-model.number="cartItems[index].quantity" min="1"
                                        @change="updateQuantity(index, $event.target.value)"
                                        class="focus:ring-2 focus:ring-pink-300">
                                </div>
                                <p class="text-green-600 font-bold"
                                    x-text="'Total: £' + (item.price * (item.quantity || 1)).toFixed(2)"></p>
                            </div>
                            <button @click="removeItem(index)"
                                class="mt-4 w-full bg-red-500 text-white py-2 rounded-lg cursor-pointer btn-pulse transition transform hover:scale-95 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i> Remove
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Empty Cart -->
                <div x-show="cartItems.length === 0" class="text-center text-gray-700 mt-8" data-aos="fade-up">
                    <h2 class="text-2xl font-bold">Your Cart is Empty</h2>
                    <p class="mt-2">Browse our <a href="/courses" class="text-pink-600 hover:underline">courses</a> to add
                        some!</p>
                </div>

                <!-- Cart Summary -->
                <div class="mt-8 lg:mt-0">
                    <div class="rounded-xl overflow-hidden bg-white p-6 sticky top-24">
                        <h2 class="text-lg font-medium text-purple-800 mb-4">Order Summary</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="font-bold">Subtotal</span>
                                <span class="text-sm font-medium text-purple-400" x-text="'£' + getCartTotal()"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-bold">Discount</span>
                                <span class="text-sm font-medium text-purple-400">£0.00</span>
                            </div>
                            <div class="border-t pt-4 flex justify-between">
                                <span class="text-base font-medium text-purple-600">Total</span>
                                <span class="font-bold text-purple-700" x-text="'£' + getCartTotal()"></span>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <button
                            class="w-full bg-gradient-to-r from-purple-500 to-indigo-500 text-white py-4 rounded-xl font-bold hover:from-purple-600 hover:to-indigo-600 transition-all shadow-lg shadow-purple-300/50 transform hover:scale-100 cursor-pointer flex items-center justify-center"
                            @click="checkout">
                            Checkout
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>

                        <!-- Clear Cart Button (Desktop) -->
                        <div class="mt-4 hidden md:block">
                            <button @click="clearCart()"
                                class="w-full flex items-center justify-center px-4 py-2 border cursor-pointer border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:scale-100 transition-colors">
                                Clear Cart
                            </button>
                        </div>
                        <div class="mt-6 flex items-center justify-center text-center text-purple-700 font-extrabold">
                            <svg class="flex-shrink-0 mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <p>Secure checkout</p>
                        </div>
                    </div>
                    <!-- Continue Shopping -->
                    <div class="mt-4 text-center">
                        <a href="/course"
                            class="inline-flex items-center text-sm text-purple-700 font-extrabold transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back to Top Button -->
            <a href="#top" id="backToTopBtn"
                class="fixed bottom-6 right-6 bg-gradient-to-r from-pink-600 to-orange-600 text-white p-4 rounded-full shadow-lg z-50 transition-all duration-300 opacity-0 pointer-events-none hover:scale-110"
                aria-label="Back to Top" data-aos="fade-up">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </a>
        </div>

        @include('main.footer')
    </main>

    <!-- Back to Top JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const backTopBtn = document.getElementById('backToTopBtn');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                } else {
                    backTopBtn.classList.add('opacity-0', 'pointer-events-none');
                }
            });
            backTopBtn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endsection
