@vite(['resources/css/app.css', 'resources/js/app.js'])
<div x-data="{
    isSidebarOpen: true,
    editHover: false,
    editOpen: false,
    selected: null,
    subOpen: false,
    selectedSub: null,
    showLogoutModal: false,
    unreadMessageCount: 0,
    debounceTimeout: null,
    pollInterval: null,
    fetchUnreadMessageCount: async function() {
        try {
            const response = await fetch('/admin/messages/unread-count', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            this.unreadMessageCount = data.unread_count !== undefined ? data.unread_count : 0;
        } catch (error) {
            console.error('Error fetching unread message count:', error);
            this.unreadMessageCount = 0;
        }
    },
    updateUnreadCount: function() {
        this.fetchUnreadMessageCount();
    },
    handleMessageUpdated: function() {
        clearTimeout(this.debounceTimeout);
        setTimeout(() => {
            this.updateUnreadCount();
        }, 500);
    }
}" x-init="init" x-on:destroy.window="destroy"
    class="flex h-screen bg-gradient-to-br from-purple-50 to-indigo-50" x-cloak>

    <!-- Hamburger Menu Icon -->
    <button x-on:click="isSidebarOpen = !isSidebarOpen"
        class="fixed top-3 left-2 p-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-full z-50 hover:scale-110 transition-all duration-300 shadow-lg hover:shadow-xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>

    <!-- Sidebar -->
    <aside x-show="isSidebarOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed left-4 top-1/2 -translate-y-1/2 w-52 h-auto text-gray-100 z-40 rounded-xl bg-gradient-to-b from-purple-800 to-indigo-900 shadow-[0_0_30px_rgba(139,92,246,0.3)] border border-purple-300"
        x-cloak>

        <!-- SVG Logo -->
        <div class="flex justify-center mt-4">
            <div
                class="relative group rounded-lg overflow-hidden transition-all duration-500 ease-in-out hover:shadow-2xl hover:scale-105 hover:rotate-1">
                <!-- Purple hover overlay -->
                <div
                    class="absolute inset-0 bg-purple-500 opacity-0 group-hover:opacity-30 transition-opacity duration-500 mix-blend-multiply pointer-events-none">
                </div>

                <img src="{{ secure_asset('image/nav-logo.png') }}" alt="Logo"
                    class="w-52 h-30 transition-transform duration-500 ease-in-out transform group-hover:scale-110 group-hover:brightness-110 rounded-lg">
            </div>
        </div>


        <hr class="border-purple-300 border-1 mx-6 my-2">
        <ul class="flex flex-col h-full p-3 space-y-3 font-medium">
            <!-- Dashboard -->
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    onclick="showPanel('dashboardPanel')">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3M5 10H3a2 2 0 00-2 2v7a2 2 0 002 2h18a2 2 0 002-2v-7a2 2 0 00-2-2h-2" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </button>
            </li>

            <!-- Course -->
            <li class="relative" x-data="{ isHovering: false }">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    @mouseenter="isHovering = true" @mouseleave="isHovering = false">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    <span class="font-medium">Course</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': isHovering }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false" x-transition
                    x-cloak
                    class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md bg-purple-700 text-white">
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('createCoursePanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('editCoursePanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Section -->
            <li class="relative" x-data="{ isHovering: false }">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    @mouseenter="isHovering = true" @mouseleave="isHovering = false">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium">Section</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': isHovering }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false" x-transition
                    x-cloak
                    class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md bg-purple-700 text-white">
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('createSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('connectSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Connect Course
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('removeSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Remove Course
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('editSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Practice -->
            <li class="relative" x-data="{ isHovering: false }">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    @mouseenter="isHovering = true" @mouseleave="isHovering = false">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01" />
                    </svg>
                    <span class="font-medium">Practice</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': isHovering }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false" x-transition
                    x-cloak
                    class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md  bg-purple-700 text-white">
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('createPracticePanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('practiceQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Make Question
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('editPracticeQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Mock -->
            <li class="relative" x-data="{ isHovering: false }">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    @mouseenter="isHovering = true" @mouseleave="isHovering = false">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.5 3.5 0 001.948-.806 3.5 3.5 0 014.434 0 3.5 3.5 0 001.948.806 3.5 3.5 0 013.509 3.555 3.5 3.5 0 01-.806 1.948 3.5 3.5 0 010 4.434 3.5 3.5 0 01.806 1.948 3.5 3.5 0 01-3.509 3.555 3.5 3.5 0 01-1.948.806 3.5 3.5 0 01-4.434 0 3.5 3.5 0 01-1.948-.806 3.5 3.5 0 01-.806-1.948 3.5 3.5 0 010-4.434 3.5 3.5 0 01.806-1.948 3.5 3.5 0 013.509-3.555z" />
                    </svg>
                    <span class="font-medium">Mock</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': isHovering }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false" x-transition
                    x-cloak
                    class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md bg-purple-700 text-white">
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('createMockPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('mockOneQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Make Question Mock-1
                        </button>
                    </li>
                    <li class="relative">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center"
                            onclick="showPanel('mockSecondQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Make Question Mock-2
                        </button>
                    </li>
                    <li class="relative" x-data="{ editHover: false }" @mouseenter="editHover = true"
                        @mouseleave="editHover = false">
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                                :class="{ 'rotate-90': editHover }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="editHover" x-transition x-cloak
                            class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md bg-purple-700 text-white">
                            <li>
                                <button onclick="showPanel('editMockOneQuestionPanel')"
                                    class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300">
                                    Edit Mock 1
                                </button>
                            </li>
                            <li>
                                <button onclick="showPanel('editMockSecondQuestionPanel')"
                                    class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300">
                                    Edit Mock 2
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <!-- Message -->
            <li>
                <button @click="showPanel('messagePanel'); setTimeout(() => fetchUnreadMessageCount(), 3000)"
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium">Messages</span>
                    <span x-show="unreadMessageCount > 0"
                        class="ml-auto bg-purple-500 text-white text-xs px-2 py-1 rounded-full"
                        x-text="unreadMessageCount"></span>
                </button>
            </li>

            <!-- Settings -->
            <li>
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    onclick="showPanel('settingsPanel')">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium">Settings</span>
                </button>
            </li>

            {{-- Tag Manager --}}
              <li class="relative" x-data="{ isHovering: false }">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    @mouseenter="isHovering = true" @mouseleave="isHovering = false">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="font-medium">Tag Manager</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': isHovering }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md bg-purple-700 text-white">
                    <!-- Bookings -->
                    <li class="relative">
                        <button onclick="showPanel('headerFooterPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Header & Footer
                        </button>
                    </li>
                    <!-- Assign Learner -->
                    <li class="relative">
                        <button onclick="showPanel('googleTagIDPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Google Tag ID
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Others -->
            <li class="relative" x-data="{ isHovering: false }">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 hover:bg-purple-500 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-[0_5px_15px_rgba(139,92,246,0.3)] border border-purple-400/50 flex items-center hover-float"
                    @mouseenter="isHovering = true" @mouseleave="isHovering = false">
                    <svg class="w-8 h-8 mr-3 text-purple-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="font-medium">Others</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 ml-auto text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': isHovering }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="absolute left-full top-0 w-48 p-2 rounded-lg shadow-xl z-50 backdrop-blur-md bg-purple-700 text-white">
                    <!-- Bookings -->
                    <li class="relative">
                        <button onclick="showPanel('bookingsPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Payments
                        </button>
                    </li>
                    <!-- Assign Learner -->
                    <li class="relative">
                        <button onclick="showPanel('assignCoursePanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Assign Learner
                        </button>
                    </li>
                    <!-- Assign Course -->
                    <li class="relative">
                        <button onclick="showPanel('assignLearnerPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Assign Course
                        </button>
                    </li>
                    <li class="relative">
                        <button onclick="showPanel('blogsPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 hover:bg-gradient-to-r hover:bg-purple-500 transition-all duration-300 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Blogs
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Logout -->
            <li class="mt-2">
                <button @click="showLogoutModal = true"
                    class="w-full rounded-xl flex items-center justify-center p-3 text-white bg-gradient-to-r from-purple-600 to-indigo-700 hover:bg-purple-500 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-[1.02]">
                    <svg class="w-8 h-8 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium">Logout</span>
                </button>
            </li>
        </ul>
    </aside>

    <!-- Modal -->
    <div x-show="showLogoutModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 backdrop-blur-sm bg-purple-900/10 flex items-center justify-center z-50">
        <div
            class="bg-gradient-to-br from-white to-purple-50 rounded-2xl p-8 w-96 shadow-2xl border border-purple-200">
            <div class="flex justify-center mb-4">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                        stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 8V12" stroke="#7C3AED" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M12 16H12.01" stroke="#7C3AED" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center text-purple-600 mb-4">Confirm Logout</h3>
            <p class="text-purple-500 text-center mb-6">Are you sure you want to log out?</p>
            <div class="flex justify-center space-x-4">
                <button @click="showLogoutModal = false"
                    class="px-6 py-2 bg-white border border-purple-200 text-purple-600 rounded-lg hover:bg-purple-50 transition-all duration-300 hover:shadow-md">
                    Cancel
                </button>

                  <!-- Logout anchor -->
                    <a href="/admin/logout"
                        role="button"
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-300 hover:shadow-md">
                        Logout
                    </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .hover-float:hover {
            animation: float 2s ease-in-out infinite;
        }

        .shadow-purple-glow {
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.3);
        }

        .hover\:shadow-purple-glow:hover {
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }
    </style>

    <script>
        function showPanel(panelId) {
            console.log('Showing panel:', panelId);
            document.querySelectorAll('.panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            const panel = document.getElementById(panelId);
            if (panel) {
                panel.classList.remove('hidden');
            }
        }
    </script>
