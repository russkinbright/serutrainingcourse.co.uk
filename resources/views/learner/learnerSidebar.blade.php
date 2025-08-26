<div x-data="{ 
    isSidebarOpen: true, 
    selected: null,
    subOpen: false, 
    selectedSub: null,
    showLogoutModal: false
}" class="flex h-screen" x-cloak>

    <!-- Hamburger Menu Icon -->
    <button x-on:click="isSidebarOpen = !isSidebarOpen"
            class="fixed top-4 left-4 p-2 bg-blue-600 backdrop-blur-md text-white rounded-lg z-50 hover:bg-gray-700/80 transition-all duration-300 shadow-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>

    <!-- Sidebar -->
    <aside
     x-show="isSidebarOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed left-2 top-1/2 -translate-y-1/2 w-48 h-auto text-white z-40 rounded-2xl bg-blue-900 backdrop-blur-lg border border-gray-800 shadow-2xl" x-cloak>

            <hr class="bg-blue-200">

    
        <!-- Menu Items -->
        <ul class="flex flex-col h-full p-3 space-y-2">

            {{-- Dashboard --}}

            <li>
                <button 
                onclick="showPanel('learnersDashboardPanel')"
                class="w-full flex items-center p-3 text-left rounded-lg text-gray-200 hover:text-cyan-300 bg-transparent hover:bg-gray-800/50 transition-all duration-300">
                <span class="w-8 h-8 flex items-center justify-center bg-blue-200 rounded-lg mr-3">
                        <i class="fas fa-tachometer-alt text-lime-700"></i>
                    </span>
                    <span class="font-medium">Dashboard</span>
                </button>
            </li>
            
            <!-- Course -->
            <li class="relative group" x-data="{ isHovering: false }">
                <button  onclick="showPanel('learnerCoursePanel')"
                    class="w-full flex items-center p-3 text-left rounded-lg text-gray-200 hover:text-cyan-300 bg-transparent hover:bg-gray-800/50 transition-all duration-300">
                    <span class="w-8 h-8 flex items-center justify-center bg-blue-200 rounded-lg mr-3">
                        📚
                    </span>
                    <span class="font-medium">Course</span>
                </button>
            </li>

            <!-- Profile -->
            <li class="relative group" x-data="{ isHovering: false }">
                <button 
                    @mouseenter="isHovering = true" 
                    @mouseleave="isHovering = false"
                    class="w-full flex items-center p-3 text-left rounded-lg text-gray-200 hover:text-purple-300 bg-transparent hover:bg-gray-800/50 transition-all duration-300">
                    <span class="w-8 h-8 flex items-center justify-center bg-blue-200 rounded-lg mr-3 ">
                        👤
                    </span>
                    <span class="font-medium">Profile</span>
                    <span class="ml-auto transition-opacity text-white">→</span>
                </button>
                <ul x-show="isHovering"
                    @mouseenter="isHovering = true"
                    @mouseleave="isHovering = false"
                    x-transition
                    class="absolute left-full top-0 ml-1 w-36 p-2 rounded-lg shadow-xl z-50 backdrop-blur-lg border-gray-700 space-y-1 bg-blue-500">
                    <li>
                        <button class="w-full flex items-center p-2 text-sm rounded-md text-white font-medium hover:text-white hover:bg-gray-700/50 transition-all">
                            <span class="w-6 h-6 flex items-center justify-center bg-blue-200 rounded-md mr-2">👀</span>
                            View Profile
                        </button>
                    </li>
                    <li>
                        <button class="w-full flex items-center p-2 text-sm rounded-md text-white font-medium hover:text-white hover:bg-gray-700/50 transition-all">
                            <span class="w-6 h-6 flex items-center justify-center bg-blue-200 rounded-md mr-2">✏️</span>
                            Edit Profile
                        </button>
                    </li>
                </ul>
            </li>
                        
            <!-- logout -->
            <li class="mt-auto pt-2">
                <button 
                    @click="showLogoutModal = true"
                    class="w-full flex items-center justify-center p-3 rounded-lg text-white bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <span class="mr-2">🚪</span>
                    Logout
                </button>
            </li>
        </ul>
    </aside>

    <!-- <div x-show="showLogoutModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96 shadow-xl">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Confirm Logout</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to log out?</p>
            <div class="flex justify-end space-x-4">
                <button 
                    @click="showLogoutModal = false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                    Cancel
                </button>
                <a href="{{ route('learner.logout') }}"
                   class="px-4 py-2 bg-rose-500 text-white rounded hover:bg-rose-600 transition">
                    Yes, Logout
                </a>
            </div>
        </div>
    </div> -->




   

    <script>
         function showPanel(panelId) {
        // Hide all panels
        document.querySelectorAll('.panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Show the selected panel
        document.getElementById(panelId).classList.remove('hidden');
    }

    window.addEventListener('resize', () => {
        const sidebar = document.querySelector('aside');
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('hidden');
        } else {
            sidebar.classList.add('hidden');
        }
    });
    </script>
