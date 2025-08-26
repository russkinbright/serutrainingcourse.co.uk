@include('home.default')
<style>
    /* ------- Glass + Frost ------- */
    .glassmorphic {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }

    /* ------- Inputs ------- */
    .input-focus {
        transition: all 0.25s ease;
    }

    .input-focus:focus {
        border-color: #7c3aed;
        /* purple-600 */
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
    }

    /* ------- Buttons: shimmer stripe ------- */
    .btn-gradient {
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .25), transparent);
        transform: translateX(-120%);
        transition: transform .7s ease;
        z-index: 1;
    }

    .btn-gradient:hover::before {
        transform: translateX(120%);
    }

    /* ------- Icon micro-move ------- */
    .contact-icon {
        transition: transform 0.25s ease, filter 0.25s ease;
    }

    .contact-icon:hover {
        transform: translateY(-2px) scale(1.08);
        filter: drop-shadow(0 6px 10px rgba(99, 102, 241, .25));
    }

    /* ------- Fancy gradient border wrapper ------- */
    .gradient-border {
        position: relative;
    }


    /* ------- Toast ------- */
    .toast-enter {
        transform: translateY(10px);
        opacity: 0;
    }

    .toast-enter-active {
        transform: translateY(0);
        opacity: 1;
        transition: all .25s ease;
    }

    .toast-exit {
        transform: translateY(0);
        opacity: 1;
    }

    .toast-exit-active {
        transform: translateY(10px);
        opacity: 0;
        transition: all .2s ease;
    }
</style>

<!-- Sticky Nav -->
<header
    class="sticky top-0 left-0 w-full bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg z-20 transition-all duration-300"
    data-header>
    @include('main.navbar')
</header>

<div class="relative bg-gray-50 font-sans overflow-hidden">

    <!-- Page Header -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 text-white py-2">
        <!-- Subtle grid pattern -->
        <svg aria-hidden="true" class="absolute inset-0 w-full h-full opacity-10">
            <defs>
                <pattern id="grid" width="32" height="32" patternUnits="userSpaceOnUse">
                    <path d="M32 0H0V32" fill="none" stroke="white" stroke-width=".5" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>

        <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span
                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3v18M3 12h18" stroke="white" stroke-width="2" stroke-linecap="round" />
                </svg>
                We’d love to hear from you
            </span>
            <h1 class="mt-3 text-3xl md:text-5xl font-extrabold tracking-tight">Contact SERU Training</h1>
            <p class="mt-3 text-base font-bold md:text-lg text-indigo-100">Get in touch with us for any inquiries about
                our
                courses!</p>
        </div>
    </div>

    <!-- Main Content -->
    <section class="relative container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Contact Form (kept as lg:col-span-2) -->
            <div class="w-full p-8 bg-white rounded-3xl shadow-xl animate__animated animate__fadeInUp">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Form</h2>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-purple-700 font-semibold mb-2">First Name *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-purple-400 mx-3"></i>
                                <input type="text" id="firstName" placeholder="Enter Your First Name"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-purple-700 font-semibold mb-2">Last Name *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-user-tag absolute left-3 top-1/2 -translate-y-1/2 text-purple-400 mx-3"></i>
                                <input type="text" id="lastName" placeholder="Enter Your Last Name"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-purple-700 font-semibold mb-2">Email *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-purple-400 mx-3"></i>
                                <input type="email" id="email" placeholder="Enter Your Email"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-purple-700 font-semibold mb-2">Phone</label>
                            <div class="relative">
                                <i
                                    class="fas fa-phone-alt absolute left-3 top-1/2 -translate-y-1/2 text-purple-400 mx-3"></i>
                                <input type="tel" id="phone" placeholder="Enter Your Phone Number"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-purple-700 font-semibold mb-2">Subject *</label>
                        <div class="relative">
                            <i class="fas fa-edit absolute left-3 top-1/2 -translate-y-1/2 text-purple-400 mx-3"></i>
                            <input type="text" id="subject" placeholder="Enter Your Subject"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-purple-700 font-semibold mb-2">Message *</label>
                        <div class="relative">
                            <i class="fas fa-comment-dots absolute left-3 top-4 text-purple-400 mx-3"></i>
                            <textarea id="message" placeholder="Write your message here"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 h-32 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-800 text-white py-4 rounded-xl text-lg font-bold hover:from-purple-700 hover:to-purple-900 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                        <i class="fas fa-sms text-xl"></i>
                        <span>Submit Message</span>
                    </button>
                </form>

            </div>

            <!-- Map / CTA Card -->
            <div class="gradient-border  rounded-2xl">
                <div class="bg-white/80 glassmorphic rounded-2xl shadow-xl p-8 h-auto">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Find Us</h2>
                    <div class="relative aspect-video w-full overflow-hidden rounded-xl ring-1 ring-gray-200">
                        <!-- Google Maps Embed -->
                        <iframe class="w-full h-[550px]"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2482.234974604054!2d-0.121772684229576!3d51.5324528796394!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x48761b4f6b7f1b4b%3A0x8f8c7f8b7f8b7f8b!2s66%20Caledonian%20Rd%2C%20London%20N1%209DP%2C%20UK!5e0!3m2!1sen!2sus!4v1634567890123!5m2!1sen!2sus"
                            style="border:0 height:auto;" allowfullscreen="" loading="lazy" aria-hidden="true">
                        </iframe>
                    </div>
                    <a href="https://www.google.com/maps?q=66+Caledonian+Rd,+London+N1+9DP,+UK" target="_blank"
                        rel="noopener"
                        class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        Open in Maps
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 17 17 7M17 7H9m8 0v8" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include('main.footer')

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[92%] sm:w-auto max-w-md hidden">
        <div class="glassmorphic rounded-xl shadow-2xl ring-1 ring-white/40 px-4 py-3 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <div class="text-sm text-gray-800">
                <strong class="block">Message sent!</strong>
                We’ll get back to you soon.
            </div>
            <button id="toast-close" class="ml-auto text-gray-400 hover:text-gray-600" aria-label="Close">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    // Helpers
    const $ = (q) => document.querySelector(q);
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const nameEl = $('#name');
    const emailEl = $('#email');
    const subjectEl = $('#subject');
    const messageEl = $('#message');
    const charCount = $('#char-count');
    const btn = $('#submit-contact');
    const btnIcon = $('#btn-icon');
    const btnText = $('#btn-text');
    const toast = $('#toast');
    const toastClose = $('#toast-close');

    // Live char count
    messageEl.setAttribute('maxlength', '500');
    messageEl.addEventListener('input', () => {
        charCount.textContent = `${messageEl.value.length} / 500`;
    });

    // Field validation
    function showInvalid(el, msg) {
        el.setCustomValidity(msg);
        el.reportValidity();
        el.classList.add('border-red-400', 'ring-2', 'ring-red-200');
        setTimeout(() => el.classList.remove('ring-2', 'ring-red-200'), 1500);
    }

    function clearInvalid(el) {
        el.setCustomValidity('');
        el.classList.remove('border-red-400');
    }

    // Toast controls
    function openToast() {
        toast.classList.remove('hidden');
        toast.classList.add('toast-enter-active');
        setTimeout(() => {
            toast.classList.remove('toast-enter-active');
        }, 250);
    }

    function closeToast() {
        toast.classList.add('toast-exit-active');
        setTimeout(() => {
            toast.classList.remove('toast-exit-active');
            toast.classList.add('hidden');
        }, 200);
    }
    toastClose.addEventListener('click', closeToast);

    // Submit
    btn.addEventListener('click', function() {
        const name = nameEl.value.trim();
        const email = emailEl.value.trim();
        const subject = subjectEl.value.trim();
        const message = messageEl.value.trim();

        // Validate
        if (!name) return showInvalid(nameEl, 'Please enter your full name.');
        clearInvalid(nameEl);
        if (!emailRe.test(email)) return showInvalid(emailEl, 'Please enter a valid email address.');
        clearInvalid(emailEl);
        if (!subject) return showInvalid(subjectEl, 'Please enter a subject.');
        clearInvalid(subjectEl);
        if (!message) return showInvalid(messageEl, 'Please enter your message.');
        clearInvalid(messageEl);

        // Simulate submission
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.textContent = 'Sending...';
        btnIcon.innerHTML =
            '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="0.8s" repeatCount="indefinite"/></circle>';

        // Replace with your API POST
        console.log('Form submitted:', {
            name,
            email,
            subject,
            message
        });

        setTimeout(() => {
            // Reset UI
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            btnText.textContent = 'Send Message';
            btnIcon.innerHTML =
                '<path d="M3 12h18M12 3l9 9-9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';

            nameEl.value = '';
            emailEl.value = '';
            subjectEl.value = '';
            messageEl.value = '';
            charCount.textContent = '0 / 500';

            openToast();
            // Also alert for legacy parity
            // alert('Thank you for your message! We will get back to you soon.');
        }, 900);
    });
</script>
