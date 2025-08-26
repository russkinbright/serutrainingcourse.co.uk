<footer class="footer bg-gradient-to-b from-purple-900 to-purple-950 text-white relative overflow-hidden">
    <!-- Background elements -->
    <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute top-0 left-0 w-64 h-64 bg-purple-600 rounded-full filter blur-3xl opacity-30 animate-float-slow"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-fuchsia-600 rounded-full filter blur-3xl opacity-30 animate-float"></div>
    </div>
    
    <div class="footer-top section py-16 relative z-10">
        <div class="container mx-auto px-6 grid grid-cols-1 gap-12">
            <!-- Row 1: Logo, Validation Form, Social Icons -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3 md:gap-12 items-center">
                <!-- Logo with Glow Effect -->
                <a href="#" class="flex justify-center md:justify-start transition-all duration-500 hover:scale-105 group">
                    <img src="/image/seru-logo.png" width="180" height="55" alt="Seru Logo" 
                         class="h-auto drop-shadow-[0_0_10px_rgba(192,132,252,0.7)] group-hover:drop-shadow-[0_0_15px_rgba(192,132,252,0.9)] transition-all duration-500">
                </a>

                <!-- Social Media Icons with Floating Animation -->
                <ul class="social-list flex gap-6 justify-center md:justify-end">
                    @foreach(['facebook', 'linkedin', 'instagram', 'twitter', 'youtube'] as $index => $platform)
                        <li class="animate-float" style="animation-delay: {{ $index * 0.1 }}s;">
                            <a href="#" class="social-link text-2xl transition-all duration-500 hover:text-white group">
                                <div class="relative">
                                    <ion-icon name="logo-{{ $platform }}" class="relative z-10 group-hover:scale-125"></ion-icon>
                                    <div class="absolute inset-0 bg-purple-500 rounded-full opacity-0 group-hover:opacity-100 -z-10 transition-opacity duration-500 group-hover:animate-pulse-slow"></div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Row 2: Address and Other Sections -->
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                <!-- Address and Contact Info -->
                <div class="footer-brand animate-fade-in-up">
                    <div class="wrapper flex gap-3 mt-4 items-start">
                        <div class="text-purple-300 mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <span class="span font-medium text-purple-200">Address:</span>
                            <address class="address text-white/90 hover:text-white transition-colors duration-300">66 Caledonian Road, London. N1 9DP</address>
                        </div>
                    </div>
                    <div class="wrapper flex gap-3 mt-4 items-center">
                        <div class="text-purple-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <div>
                            <span class="span font-medium text-purple-200">Call:</span>
                            <a href="tel:+442032898484" class="footer-link text-white/90 hover:text-white transition-colors duration-300 hover:underline">0203 289 8484</a>
                        </div>
                    </div>
                    <div class="wrapper flex gap-3 mt-4 items-center">
                        <div class="text-purple-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <div>
                            <span class="span font-medium text-purple-200">Email:</span>
                            <a href="mailto:support@trainingtale.org" class="footer-link text-white/90 hover:text-white transition-colors duration-300 hover:underline">support@trainingtale.org</a>
                        </div>
                    </div>
                </div>

                <!-- Online Platform Links -->
                <ul class="footer-list animate-fade-in-up" style="animation-delay: 0.2s;">
                    <li>
                        <p class="footer-list-title text-white font-semibold text-lg mb-4 flex items-center">
                            <span class="w-3 h-3 bg-purple-400 rounded-full mr-2 animate-pulse"></span>
                            Online Platform
                        </p>
                    </li>
                    @foreach(['About', 'Courses', 'Instructor', 'Events', 'Instructor Profile', 'Purchase Guide'] as $link)
                        <li class="mb-2">
                            <a href="#" class="footer-link py-1.5 text-white/80 hover:text-white transition-all duration-300 group flex items-center">
                                <span class="w-2 h-2 bg-purple-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                {{ $link }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Additional Links -->
                <ul class="footer-list animate-fade-in-up" style="animation-delay: 0.4s;">
                    <li>
                        <p class="footer-list-title text-white font-semibold text-lg mb-4 flex items-center">
                            <span class="w-3 h-3 bg-fuchsia-400 rounded-full mr-2 animate-pulse" style="animation-delay: 0.2s"></span>
                            Quick Links
                        </p>
                    </li>
                    @foreach(['Contact Us', 'Gallery', 'News & Articles', "FAQ's", 'Sign In/Registration', 'Coming Soon'] as $link)
                        <li class="mb-2">
                            <a href="#" class="footer-link py-1.5 text-white/80 hover:text-white transition-all duration-300 group flex items-center">
                                <span class="w-2 h-2 bg-fuchsia-400 rounded-full mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                {{ $link }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Newsletter Subscription -->
                <div class="footer-list animate-fade-in-up" style="animation-delay: 0.6s;">
                    <p class="footer-list-title text-white font-semibold text-lg mb-4 flex items-center">
                        <span class="w-3 h-3 bg-purple-400 rounded-full mr-2 animate-pulse" style="animation-delay: 0.4s"></span>
                        Newsletter
                    </p>
                    <p class="footer-list-text mb-6 text-white/80">Get updates on new courses and special offers</p>
                    <form action="" class="newsletter-form">
                        <div class="relative mb-4">
                            <input type="email" name="email_address" placeholder="Your email" required 
                                class="input-field bg-white/90 p-3 pl-5 pr-12 rounded-full w-full text-purple-900 transition-all duration-500 focus:ring-2 focus:ring-purple-400 border-2 border-transparent hover:border-purple-300/50">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-purple-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                        </div>

                        <button type="submit" 
                                class="btn relative flex items-center justify-center bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white font-medium px-6 py-3 rounded-full overflow-hidden w-full group hover:shadow-[0_0_15px_rgba(192,132,252,0.7)] transition-all duration-500">
                            <span class="relative z-10 mr-2 group-hover:translate-x-1 transition-transform duration-300">Subscribe</span>
                            <ion-icon name="arrow-forward-outline" class="relative z-10 group-hover:translate-x-2 transition-transform duration-300"></ion-icon>
                            <span class="absolute inset-0 bg-gradient-to-r from-purple-500 to-fuchsia-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom with Animated Border -->
    <div class="footer-bottom border-t border-purple-800/50 py-8 relative">
        <div class="max-w-7xl mx-auto px-6">
            <p class="copyright text-center text-white/80">
                © <span class="current-year">2025</span> All Rights Reserved by 
                <a href="#" class="copyright-link text-white font-bold hover:text-purple-300 transition-colors duration-300 relative inline-block">
                    <span class="relative z-10">Seru Training Course</span>
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-purple-400 transform scale-x-0 origin-left group-hover:scale-x-100 transition-transform duration-500"></span>
                </a>
            </p>
        </div>
    </div>

    <!-- Floating particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-purple-400 rounded-full opacity-70 animate-float-slow"></div>
        <div class="absolute top-1/3 right-1/5 w-3 h-3 bg-fuchsia-400 rounded-full opacity-50 animate-float"></div>
        <div class="absolute bottom-1/4 left-2/5 w-1.5 h-1.5 bg-purple-300 rounded-full opacity-60 animate-float-slower"></div>
    </div>
</footer>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes float-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
    
    @keyframes float-slower {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes ping-slow {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(2); opacity: 0; }
    }
    
    .animate-float {
        animation: float 4s ease-in-out infinite;
    }
    
    .animate-float-slow {
        animation: float-slow 6s ease-in-out infinite;
    }
    
    .animate-float-slower {
        animation: float-slower 8s ease-in-out infinite;
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.8s ease-out forwards;
    }
    
    .animate-pulse-slow {
        animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .hover\:animate-ping-slow:hover {
        animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
</style>
