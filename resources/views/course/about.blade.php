@include('home.default')

<!-- Add AOS CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
  /* Smooth scrolling */
  html {
    scroll-behavior: smooth;
  }

  /* Glassmorphic effect */
  .glassmorphic {
    background: rgba(255, 255, 255, 0.78);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  /* Gradient border */
  .gradient-border { position: relative; }
  .gradient-border::after {
    content: ""; position: absolute; inset: -1px; border-radius: 1rem;
    padding: 1px; background: linear-gradient(135deg, #6366f1, #a855f7);
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none;
  }

  /* Hover effects */
  .glassmorphic:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(99, 102, 241, 0.25);
  }

  .badge-shadow { box-shadow: 0 8px 24px rgba(99, 102, 241, 0.18); }

  /* Parallax background for hero */
  .hero-parallax {
    background-attachment: fixed;
    background-position: center;
    background-size: cover;
    position: relative;
  }

  /* Button hover animation */
  .btn-hover {
    transition: all 0.3s ease-in-out;
  }
  .btn-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  }

  /* Image hover effect */
  .team-img {
    transition: transform 0.3s ease, filter 0.3s ease;
  }
  .team-img:hover {
    transform: scale(1.1);
    filter: brightness(1.1);
  }
</style>

<div class="relative min-h-screen bg-gray-50 font-sans overflow-hidden">

  <!-- Navbar -->
  <header class="sticky top-0 left-0 w-full bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg z-20" data-header>
    @include('main.navbar')
  </header>

  <!-- Top Section -->
  <div class="relative bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 text-white py-2" data-aos="fade-down" data-aos-duration="800">
    <svg aria-hidden="true" class="absolute inset-0 w-full h-full opacity-10">
      <defs>
        <pattern id="grid" width="32" height="32" patternUnits="userSpaceOnUse">
          <path d="M32 0H0V32" fill="none" stroke="white" stroke-width=".5" />
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#grid)" />
    </svg>
    <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium backdrop-blur" data-aos="zoom-in" data-aos-delay="100">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 3v18M3 12h18" stroke="white" stroke-width="2" stroke-linecap="round" />
        </svg>
        About SERU Training
      </span>
      <h1 class="mt-3 text-3xl md:text-5xl font-extrabold tracking-tight" data-aos="fade-up" data-aos-delay="200">Who We Are</h1>
      <p class="mt-3 text-base md:text-lg text-indigo-100 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="300">
        Empowering individuals with top-tier training solutions to achieve their professional goals.
      </p>
    </div>
  </div>

  <!-- Hero -->
  <section class="container mx-auto text-gray-700 hero-parallax mt-4 rounded-xl" style="background-image: url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?q=80&w=1920&auto=format&fit=crop')" data-aos="fade-up" data-aos-duration="1000">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 text-center">
      <h1 class="mt-3 text-3xl md:text-5xl font-extrabold tracking-tight text-white" data-aos="zoom-in" data-aos-delay="100">Empowering London’s private hire drivers to pass SERU with confidence</h1>
      <p class="mt-3 text-base md:text-lg text-white max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
        We deliver clear, up-to-date preparation for the Safety, Equality and Regulatory Understanding (SERU) assessment—online and in person—built by educators who know the TFL standards inside out.
      </p>
    </div>
  </section>

  <!-- Mission / Story -->
  <section class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="gradient-border border-2 rounded-2xl" data-aos="fade-right" data-aos-duration="800">
      <div class="glassmorphic rounded-2xl p-8 h-full">
        <div class="flex items-center gap-3 mb-4">
          <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="none"><path d="M12 3l7 4v10l-7 4-7-4V7l7-4z" stroke="currentColor" stroke-width="2"/></svg>
          <h2 class="text-2xl font-bold text-gray-900">Our Mission</h2>
        </div>
        <p class="text-gray-700 leading-relaxed">
          SERU Training exists to make the licensing journey simpler, fairer, and faster. We turn complex policy into easy lessons, practice questions, and focused coaching so you spend less time revising and more time driving.
        </p>
        <ul class="mt-6 space-y-3 text-gray-700">
          <li class="flex items-start gap-3" data-aos="fade-up" data-aos-delay="100"><svg class="w-5 h-5 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Up-to-date content mapped to TFL guidelines</li>
          <li class="flex items-start gap-3" data-aos="fade-up" data-aos-delay="200"><svg class="w-5 h-5 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Flexible schedules: evenings & weekends</li>
          <li class="flex items-start gap-3" data-aos="fade-up" data-aos-delay="300"><svg class="w-5 h-5 text-emerald-600 mt-0.5" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>Clear, multilingual support</li>
        </ul>
      </div>
    </div>

    <div class="gradient-border border-2 rounded-2xl" data-aos="fade-left" data-aos-duration="800">
      <div class="glassmorphic rounded-2xl p-8 h-full">
        <div class="flex items-center gap-3 mb-4">
          <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M7 12h10M10 18h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          <h2 class="text-2xl font-bold text-gray-900">Our Story</h2>
        </div>
        <p class="text-gray-700 leading-relaxed">
          Founded in London, we started by helping friends prepare for SERU the right way—no jargon, just practical guidance. Word spread, classes filled, and today we support thousands of learners each year across online modules, live workshops, and 1-to-1 sessions.
        </p>
        <div class="mt-6 grid grid-cols-3 gap-4">
          <div class="text-center bg-white rounded-xl p-4 shadow-sm" data-aos="zoom-in" data-aos-delay="100">
            <div class="text-2xl font-extrabold text-indigo-600">7k+</div>
            <div class="text-xs text-gray-500">Learners supported</div>
          </div>
          <div class="text-center bg-white rounded-xl p-4 shadow-sm" data-aos="zoom-in" data-aos-delay="200">
            <div class="text-2xl font-extrabold text-indigo-600">94%</div>
            <div class="text-xs text-gray-500">First-time pass rate*</div>
          </div>
          <div class="text-center bg-white rounded-xl p-4 shadow-sm" data-aos="zoom-in" data-aos-delay="300">
            <div class="text-2xl font-extrabold text-indigo-600">4.9★</div>
            <div class="text-xs text-gray-500">Average rating</div>
          </div>
        </div>
        <p class="mt-2 text-[11px] text-gray-400">*Based on internal completion & feedback data.</p>
      </div>
    </div>
  </section>

  <!-- Badges / Why trust us -->
  <section class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8 md:pb-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="glassmorphic rounded-2xl p-6 flex items-start gap-4" data-aos="fade-up" data-aos-delay="100">
        <div class="badge-shadow inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white">
          <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M8 12l2.5 2.5L16 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <div>
          <h3 class="font-semibold text-gray-900">Aligned with TFL SERU</h3>
          <p class="text-gray-600 text-sm">Curriculum tracks the latest policy and assessment structure.</p>
        </div>
      </div>
      <div class="glassmorphic rounded-2xl p-6 flex items-start gap-4" data-aos="fade-up" data-aos-delay="200">
        <div class="badge-shadow inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white">
          <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="none"><path d="M12 2l3 7h7l-5.5 4 2 7L12 17l-6.5 3 2-7L2 9h7l3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
        </div>
        <div>
          <h3 class="font-semibold text-gray-900">Experienced tutors</h3>
          <p class="text-gray-600 text-sm">Friendly team with classroom + industry experience.</p>
        </div>
      </div>
      <div class="glassmorphic rounded-2xl p-6 flex items-start gap-4" data-aos="fade-up" data-aos-delay="300">
        <div class="badge-shadow inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white">
          <svg class="w-6 h-6 text-indigo-600" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M5 12a7 7 0 1 1 14 0 7 7 0 1 1-14 0z" stroke="currentColor" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
        <div>
          <h3 class="font-semibold text-gray-900">Flexible learning</h3>
          <p class="text-gray-600 text-sm">Online modules, live classes, and private coaching.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Team -->
  <section class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-8" data-aos="fade-down" data-aos-duration="800">Meet the Team</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Member -->
      <div class="glassmorphic rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="100">
        <img class="mx-auto h-20 w-20 rounded-xl object-cover team-img" src="https://images.unsplash.com/photo-1607746882042-944635dfe10e?q=80&w=256&auto=format&fit=crop" alt="Instructor">
        <h3 class="mt-4 font-semibold text-gray-900">Aisha Khan</h3>
        <p class="text-sm text-gray-600">Lead Tutor · SERU Specialist</p>
      </div>
      <div class="glassmorphic rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="200">
        <img class="mx-auto h-20 w-20 rounded-xl object-cover team-img" src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=256&auto=format&fit=crop" alt="Instructor">
        <h3 class="mt-4 font-semibold text-gray-900">Daniel Green</h3>
        <p class="text-sm text-gray-600">Curriculum & QA</p>
      </div>
      <div class="glassmorphic rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="300">
        <img class="mx-auto h-20 w-20 rounded-xl object-cover team-img" src="https://images.unsplash.com/photo-1502685104226-ee32379fefbe?q=80&w=256&auto=format&fit=crop" alt="Support">
        <h3 class="mt-4 font-semibold text-gray-900">Sara Williams</h3>
        <p class="text-sm text-gray-600">Learner Support</p>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="relative">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mb-5">
      <div class="gradient-border border-2 rounded-2xl" data-aos="zoom-in" data-aos-duration="800">
        <div class="glassmorphic rounded-2xl p-8 md:p-10 text-center">
          <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900">Ready to get SERU-ready?</h2>
          <p class="mt-2 text-gray-600">Join the next cohort or message our team for guidance.</p>
          <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/courses" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 font-semibold text-white btn-hover" data-aos="fade-up" data-aos-delay="100">View Courses</a>
            <a href="/contact" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 font-semibold text-gray-700 btn-hover" data-aos="fade-up" data-aos-delay="200">Contact Us</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  @include('main.footer')
</div>

<!-- Add AOS JS and Initialize -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-out',
    once: true,
  });

  // Fallback for smooth scrolling in older browsers
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute('href')).scrollIntoView({
        behavior: 'smooth'
      });
    });
  });
</script>