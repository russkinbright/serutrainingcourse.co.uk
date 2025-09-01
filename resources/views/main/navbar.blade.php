@php
    $is = fn($pattern) => request()->is(trim($pattern, '/')) || request()->is(trim($pattern, '/') . '/*');
@endphp

<nav
    class="bg-gradient-to-r from-purple-900 via-purple-800 to-purple-900 shadow-2xl sticky w-full z-50 top-0 backdrop-blur-sm bg-opacity-90 border-b border-purple-500/20">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/"
                    class="text-2xl font-extrabold tracking-tight transition-all duration-500 hover:scale-105">
                    <div class="w-[120px] sm:w-[150px] md:w-[180px] relative">
                        <img src="{{ asset('image/seru-logo.png') }}" alt="Logo"
                            class="w-full h-auto">
                        {{-- <div class="absolute inset-0 bg-purple-500/20 rounded-lg blur-md -z-10"></div> --}}
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8 font-medium text-base lg:text-lg">
                <a href="/"
                    class="nav-link {{ $is('/') ? 'is-active' : '' }} text-purple-100 hover:text-white no-underline transition-all duration-300 relative group">
                    <span class="relative z-10 flex items-center"><i
                            class="fa-solid fa-house mr-2"></i><span>Home</span></span>
                    <span
                        class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-purple-400 to-fuchsia-400 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></span>
                    <span
                        class="absolute -inset-2 rounded-lg bg-purple-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></span>
                </a>

                <a href="/course"
                    class="nav-link {{ $is('course') ? 'is-active' : '' }} text-purple-100 hover:text-white no-underline transition-all duration-300 relative group">
                    <span class="relative z-10 flex items-center"><i
                            class="fa-solid fa-book-open mr-2"></i><span>Courses</span></span>
                    <span
                        class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-purple-400 to-fuchsia-400 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></span>
                    <span
                        class="absolute -inset-2 rounded-lg bg-purple-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></span>
                </a>

                <a href="/blog"
                    class="nav-link {{ $is('blog') ? 'is-active' : '' }} text-purple-100 hover:text-white no-underline transition-all duration-300 relative group">
                    <span class="relative z-10 flex items-center"><i
                            class="fa-solid fa-blog mr-2"></i><span>Blog</span></span>
                    <span
                        class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-purple-400 to-fuchsia-400 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></span>
                    <span
                        class="absolute -inset-2 rounded-lg bg-purple-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></span>
                </a>

                <a href="/about-us"
                    class="nav-link {{ $is('about-us') ? 'is-active' : '' }} text-purple-100 hover:text-white no-underline transition-all duration-300 relative group">
                    <span class="relative z-10 flex items-center"><i
                            class="fa-solid fa-circle-info mr-2"></i><span>About Us</span></span>
                    <span
                        class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-purple-400 to-fuchsia-400 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></span>
                    <span
                        class="absolute -inset-2 rounded-lg bg-purple-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></span>
                </a>

                <a href="/contact"
                    class="nav-link {{ $is('contact') ? 'is-active' : '' }} text-purple-100 hover:text-white no-underline transition-all duration-300 relative group">
                    <span class="relative z-10 flex items-center"><i class="fa-solid fa-envelope mr-2"></i><span>Contact
                            Us</span></span>
                    <span
                        class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-purple-400 to-fuchsia-400 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></span>
                    <span
                        class="absolute -inset-2 rounded-lg bg-purple-900/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></span>
                </a>
            </div>

            <!-- Right: search + cart + login -->
            <div class="flex items-center space-x-4 sm:space-x-6">
                {{-- <div class="relative group">
                    <input type="text" placeholder="Search..."
                        class="px-5 py-3 bg-purple-900/50 text-white placeholder-purple-300 border border-purple-600/50 rounded-full
                        focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-500
                        w-56
                        group-hover:w-60 focus:w-60 backdrop-blur-sm text-base" />
                    <span
                        class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-purple-300 group-hover:text-purple-100 transition-colors duration-300">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div> --}}

                <!-- CART / BASKET -->
                <div class="relative group">
                    <button id="cart-btn"
                        class="relative text-xl sm:text-2xl text-purple-100 hover:text-white transition-all duration-300 cursor-pointer">
                        <span
                            class="inline-block transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">🛒</span>
                        <span id="cart-count"
                            class="absolute -top-2 -right-2 bg-gradient-to-br from-purple-400 to-fuchsia-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg transform group-hover:scale-125 transition-transform duration-300">0</span>
                    </button>

                    <!-- Hover panel -->
                    <div
                        class="absolute right-0 mt-2 w-96 bg-purple-700 border border-purple-600/50 rounded-lg shadow-lg p-4 text-white z-50
                        opacity-0 scale-95 translate-y-2 pointer-events-none
                        group-hover:opacity-100 group-hover:scale-100 group-hover:translate-y-0 group-hover:pointer-events-auto
                        transition-all duration-300 ease-out">
                        <h4 class="text-lg font-semibold mb-2 rounded">Cart Items</h4>
                        <hr>
                        <ul id="cart-list" class="space-y-2 text-sm font-bold"></ul>
                    </div>
                </div>

                

                    @auth('learner')
                        {{-- Avatar / user dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open=!open"
                                class="hidden sm:flex items-center gap-2 px-4 py-2 bg-purple-900/50 text-purple-100 rounded-full hover:text-white hover:bg-purple-800/60 transition">
                                <span
                                    class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-fuchsia-500 text-white font-bold">
                                    {{ strtoupper(substr(Auth::guard('learner')->user()->name ?? 'U', 0, 1)) }}
                                </span>
                                <span class="font-semibold truncate max-w-[10rem]">
                                    {{ Auth::guard('learner')->user()->name ?? 'Learner' }}
                                </span>
                                <svg class="w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-cloak x-show="open" @click.away="open=false"
                                class="absolute right-0 mt-2 w-56 bg-purple-800 text-white rounded-xl shadow-xl border border-purple-600/40 overflow-hidden z-50">
                                <a href="{{ route('learner.page') }}"
                                    class="block px-4 py-3 hover:bg-purple-500">Dashboard</a>
                                <a href="{{ route('learner.profile.show') }}"
                                    class="block px-4 py-3 hover:bg-purple-500">Profile</a>
                               <a href="{{ route('learner.logout') }}"
                                        class="block px-4 py-3 hover:bg-purple-500">Logout</button>
                               </a>
                            </div>
                        </div>
                    @else
                        {{-- Show Login when not authenticated --}}
                        <a href="/learner/login"
                            class="hidden sm:flex px-5 py-2 bg-gradient-to-r from-purple-500 to-fuchsia-500 text-white font-semibold rounded-full hover:shadow-[0_0_15px_rgba(192,132,252,0.7)] transition-all duration-500 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 hover:scale-105 text-sm sm:text-base relative overflow-hidden group">
                            <span class="relative z-10">Login</span>
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-purple-600 to-fuchsia-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                            <span
                                class="absolute inset-0 rounded-full border-2 border-purple-300/50 opacity-0 group-hover:opacity-100 transition-opacity duration-500 animate-[ping_1.5s_linear_infinite]"></span>
                        </a>
                    @endauth

                    <button id="menu-btn"
                        class="md:hidden text-purple-100 text-2xl sm:text-3xl focus:outline-none hover:text-white transition-all duration-300 group">
                        <span class="block group-hover:rotate-90 transition-transform duration-500">☰</span>
                    </button>
                </div>


            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-gradient-to-b from-purple-900 to-purple-800 text-center py-4 absolute w-full top-20 left-0 shadow-xl transform -translate-y-full transition-all duration-500 ease-in-out backdrop-blur-lg border-t border-purple-700/50">

            <a href="/"
                class="block py-3 px-4 text-purple-100 hover:text-white hover:bg-purple-800/50 no-underline transition-all duration-300 group flex items-center justify-center">
                <i class="fa fa-home mr-2 group-hover:scale-125 transition-transform duration-300"></i>
                <span class="group-hover:font-medium">Home</span>
            </a>

            <a href="/course"
                class="block py-3 px-4 text-purple-100 hover:text-white hover:bg-purple-800/50 no-underline transition-all duration-300 group flex items-center justify-center">
                <i class="fa fa-graduation-cap mr-2 group-hover:scale-125 transition-transform duration-300"></i>
                <span class="group-hover:font-medium">Courses</span>
            </a>

            <a href="/blog"
                class="block py-3 px-4 text-purple-100 hover:text-white hover:bg-purple-800/50 no-underline transition-all duration-300 group flex items-center justify-center">
                <i class="fa fa-blog mr-2 group-hover:scale-125 transition-transform duration-300"></i>
                <span class="group-hover:font-medium">Blog</span>
            </a>

            <a href="/about-us"
                class="block py-3 px-4 text-purple-100 hover:text-white hover:bg-purple-800/50 no-underline transition-all duration-300 group flex items-center justify-center">
                <i class="fa fa-info-circle mr-2 group-hover:scale-125 transition-transform duration-300"></i>
                <span class="group-hover:font-medium">About Us</span>
            </a>

            <a href="/contact"
                class="block py-3 px-4 text-purple-100 hover:text-white hover:bg-purple-800/50 no-underline transition-all duration-300 group flex items-center justify-center">
                <i class="fa fa-envelope mr-2 group-hover:scale-125 transition-transform duration-300"></i>
                <span class="group-hover:font-medium">Contact Us</span>
            </a>

            <div class="mt-4 px-4">
                <div class="relative">
                    <input type="text" placeholder="Search..."
                        class="px-4 py-2 bg-purple-800/70 text-white placeholder-purple-300 border border-purple-600/50 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-300 w-full backdrop-blur-sm">
                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-purple-300">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </div>

            <a href="/learner/login"
                class="block mt-4 mx-auto px-6 py-2 bg-gradient-to-r from-purple-500 to-fuchsia-500 text-white font-semibold rounded-full hover:shadow-[0_0_10px_rgba(192,132,252,0.7)] transition-all duration-300 w-32 shadow-md">
                Login
            </a>
        </div>

        <style>
            [x-cloak] {
                display: none !important;
            }

            .is-active {
                color: #fff !important;
                font-weight: 800;
                padding: 10px;
                position: relative;
                topacity: 1 !important;
                background: rgba(168, 85, 247, .25);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(192, 132, 252, .9);
                box-shadow: 0 0 25px rgba(144, 60, 223, .5), inset 0 0 10px rgba(236, 72, 153, .3);
                border-radius: 12px;
                transform: scale(1.08);
                transition: all .35s ease-in-out
            }

            .is-active .underline-bar {
                transform: scaleX(1) !important;
                height: 3px;
                border-radius: 4px;
                background: linear-gradient(90deg, #c084fc, #f472b6);
                box-shadow: 0 0 15px rgba(192, 132, 252, .7), 0 0 25px rgba(244, 114, 182, .6)
            }

            .is-active .bg-hover {
                opacity: 1 !important;
                background: rgba(168, 85, 247, .25);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(192, 132, 252, .4);
                box-shadow: 0 0 25px rgba(168, 85, 247, .5), inset 0 0 10px rgba(236, 72, 153, .3);
                border-radius: 12px
            }
        </style>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const menuBtn = document.getElementById("menu-btn");
                const mobileMenu = document.getElementById("mobile-menu");
                const cartCount = document.getElementById("cart-count");
                const cartList = document.getElementById("cart-list");
                const cartBtn = document.getElementById("cart-btn");

                // --- Mobile menu toggle ---
                if (menuBtn && mobileMenu) {
                    menuBtn.addEventListener("click", function() {
                        const isHidden = mobileMenu.classList.contains("hidden");
                        if (isHidden) {
                            mobileMenu.classList.remove("hidden");
                            setTimeout(() => mobileMenu.classList.remove("-translate-y-full"), 10);
                        } else {
                            mobileMenu.classList.add("-translate-y-full");
                            mobileMenu.addEventListener("transitionend", function handler() {
                                mobileMenu.classList.add("hidden");
                                mobileMenu.removeEventListener("transitionend", handler);
                            }, {
                                once: true
                            });
                        }
                    });
                }

                // --- CART helpers (reads `localStorage["cartItems"]`) ---
                function safeGetCartItems() {
                    try {
                        const data = JSON.parse(localStorage.getItem("cartItems"));
                        return Array.isArray(data) ? data : [];
                    } catch {
                        return [];
                    }
                }

                // total count = sum of quantities (default 1)
                function getTotalCount(items) {
                    return items.reduce((n, it) => n + (parseInt(it?.quantity ?? 1) || 1), 0);
                }

                function renderCartBadgeAndList() {
                    const items = safeGetCartItems();
                    cartCount.textContent = getTotalCount(items);

                    if (!items.length) {
                        cartList.innerHTML = '<li class="text-purple-300">Your cart is empty</li>';
                        return;
                    }

                    cartList.innerHTML = items.map(it => {
                        const title = it?.title ?? 'Item';
                        const price = (typeof it?.price === 'number') ? `£${it.price.toFixed(2)}` : (it
                            ?.price ?? 'N/A');
                        const qty = parseInt(it?.quantity ?? 1) || 1;
                        return `<li class="flex justify-between items-center bg-purple-800/50 rounded-md px-3 py-2">
                    <span class="truncate">${title}</span>
                    <span class="ml-3 text-purple-200">x${qty} • ${price}</span>
                  </li>`;
                    }).join('');
                }

                // initial render
                renderCartBadgeAndList();

                // updates from other tabs
                window.addEventListener("storage", (e) => {
                    if (e.key === "cartItems") renderCartBadgeAndList();
                });

                // updates from this tab (dispatch this after you set cartItems)
                window.addEventListener("cart:updated", renderCartBadgeAndList);

                // go to cart page on click
                cartBtn.addEventListener("click", () => {
                    window.location.href = "/cart";
                });

                // Optional helper for testing from console:
                window.__debugAddToCart = function(item) {
                    const items = safeGetCartItems();
                    items.push(item);
                    localStorage.setItem("cartItems", JSON.stringify(items));
                    window.dispatchEvent(new Event("cart:updated"));
                };
            });
        </script>
</nav>
