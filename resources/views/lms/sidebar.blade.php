@vite(['resources/css/app.css', 'resources/js/app.js'])
<div x-data="{
    isSidebarOpen: true,
    selected: null,
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
    },
    toggleSubmenu(menu) {
        this.selected = this.selected === menu ? null : menu;
    }
}" x-init="fetchUnreadMessageCount(); pollInterval = setInterval(() => fetchUnreadMessageCount(), 30000)"
    x-on:destroy.window="clearInterval(pollInterval)"
    class="flex h-screen bg-gradient-to-br from-purple-50 to-indigo-50" x-cloak>

    <!-- Hamburger Menu Icon (Mobile) -->
    <button x-on:click="isSidebarOpen = !isSidebarOpen"
        class="fixed top-3 left-2 p-2 bg-gradient-to-r from-purple-600 to-indigo-700 text-white rounded-full z-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:bg-purple-700 md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>

    <!-- Sidebar -->
    <aside x-show="isSidebarOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed left-0 top-0 w-64 max-w-[80vw] h-screen text-gray-100 z-40 rounded-none bg-gradient-to-b from-purple-800/90 to-indigo-900/90 backdrop-blur-lg shadow-[0_0_30px_rgba(139,92,246,0.4)] border-r border-purple-300/50 overflow-y-auto scrollbar-hide md:w-56 md:left-4 md:top-4 md:h-[calc(100vh-2rem)] md:rounded-xl md:border md:border-purple-300/30"
        x-cloak>

        <!-- Sidebar Toggle Button -->
        <div class="flex justify-end p-3">
            <button x-on:click="isSidebarOpen = false"
                class="p-2 bg-purple-600/80 text-white rounded-full hover:bg-purple-700/90 transition-all duration-300 shadow-md hover:shadow-lg hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- SVG Logo -->
        <div class="flex justify-center px-4">
            <div class="relative rounded-lg overflow-hidden">
                <img src="{{ secure_asset('image/nav-logo.png') }}" alt="Logo"
                    class="w-full h-auto max-h-32 rounded-lg shadow-md">
            </div>
        </div>

        <hr class="border-purple-300/50 border-1 mx-6 my-2">
        <ul class="flex flex-col p-3 space-y-3 font-medium">
            <!-- Dashboard -->
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    onclick="showPanel('dashboardPanel')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3M5 10H3a2 2 0 00-2 2v7a2 2 0 002 2h18a2 2 0 002-2v-7a2 2 0 00-2-2h-2" />
                    </svg>
                    <span class="font-medium flex-1">Dashboard</span>
                </button>
            </li>

            <!-- Course -->
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    @click="toggleSubmenu('course')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                    <span class="font-medium flex-1">Course</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': selected === 'course' }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="selected === 'course'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                    class="pl-6 mt-2 space-y-2">
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('createCoursePanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('editCoursePanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
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
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    @click="toggleSubmenu('section')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium flex-1">Section</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': selected === 'section' }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="selected === 'section'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                    class="pl-6 mt-2 space-y-2">
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('createSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('connectSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Connect Course
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('removeSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Remove Course
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('editSectionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
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
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    @click="toggleSubmenu('practice')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01" />
                    </svg>
                    <span class="font-medium flex-1">Practice</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': selected === 'practice' }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="selected === 'practice'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                    class="pl-6 mt-2 space-y-2">
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('createPracticePanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('practiceQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Make Question
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('editPracticeQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
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
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    @click="toggleSubmenu('mock')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.5 3.5 0 001.948-.806 3.5 3.5 0 014.434 0 3.5 3.5 0 001.948.806 3.5 3.5 0 013.509 3.555 3.5 3.5 0 01-.806 1.948 3.5 3.5 0 010 4.434 3.5 3.5 0 01.806 1.948 3.5 3.5 0 01-3.509 3.555 3.5 3.5 0 01-1.948.806 3.5 3.5 0 01-4.434 0 3.5 3.5 0 01-1.948-.806 3.5 3.5 0 01-.806-1.948 3.5 3.5 0 010-4.434 3.5 3.5 0 01.806-1.948 3.5 3.5 0 013.509-3.555z" />
                    </svg>
                    <span class="font-medium flex-1">Mock</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': selected === 'mock' }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="selected === 'mock'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                    class="pl-6 mt-2 space-y-2">
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('createMockPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Create
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('mockOneQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Make Question Mock-1
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            onclick="showPanel('mockSecondQuestionPanel')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Make Question Mock-2
                        </button>
                    </li>
                    <li>
                        <button
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60"
                            @click="toggleSubmenu('mock_edit')">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                                :class="{ 'rotate-90': selected === 'mock_edit' }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="selected === 'mock_edit'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-6 mt-2 space-y-2">
                            <li>
                                <button onclick="showPanel('editMockOneQuestionPanel')"
                                    class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 shadow-sm hover:bg-purple-600/60">
                                    Edit Mock 1
                                </button>
                            </li>
                            <li>
                                <button onclick="showPanel('editMockSecondQuestionPanel')"
                                    class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 shadow-sm hover:bg-purple-600/60">
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
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium flex-1">Messages</span>
                    <span x-show="unreadMessageCount > 0"
                        class="ml-auto bg-purple-500/80 text-white text-xs px-2 py-1 rounded-full shadow-sm"
                        x-text="unreadMessageCount"></span>
                </button>
            </li>

            <!-- Settings -->
            <li>
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    onclick="showPanel('settingsPanel')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium flex-1">Settings</span>
                </button>
            </li>

            <!-- Tag Manager -->
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    @click="toggleSubmenu('tag_manager')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="font-medium flex-1">Tag Manager</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': selected === 'tag_manager' }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="selected === 'tag_manager'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                    class="pl-6 mt-2 space-y-2">
                    <li>
                        <button onclick="showPanel('headerFooterPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Header & Footer
                        </button>
                    </li>
                    <li>
                        <button onclick="showPanel('googleTagIDPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
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
            <li class="relative">
                <button
                    class="w-full p-3 text-left rounded-lg text-gray-100 bg-gradient-to-r from-purple-700/50 to-indigo-700/50 transition-all duration-300 border border-purple-400/30 flex items-center shadow-sm hover:bg-purple-600/60"
                    @click="toggleSubmenu('others')">
                    <svg class="w-8 h-8 mr-3 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="font-medium flex-1">Others</span>
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-purple-300 transform transition-transform duration-300"
                        :class="{ 'rotate-90': selected === 'others' }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <ul x-show="selected === 'others'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-screen"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 max-h-0"
                    class="pl-6 mt-2 space-y-2">
                    <li>
                        <button onclick="showPanel('bookingsPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Payments
                        </button>
                    </li>
                    <li>
                        <button onclick="showPanel('assignCoursePanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Assign Learner
                        </button>
                    </li>
                    <li>
                        <button onclick="showPanel('assignLearnerPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Assign Course
                        </button>
                    </li>
                    <li>
                        <button onclick="showPanel('blogsPanel')"
                            class="w-full p-2 text-sm text-left rounded-lg text-gray-100 bg-purple-700/50 transition-all duration-300 flex items-center shadow-sm hover:bg-purple-600/60">
                            <svg class="w-6 h-6 mr-2 text-purple-300 flex-shrink-0" fill="none" stroke="currentColor"
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
                    class="w-full rounded-xl flex items-center justify-center p-3 text-white bg-gradient-to-r from-purple-600 to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg hover:bg-purple-700">
                    <svg class="w-8 h-8 mr-2 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium flex-1 text-center">Logout</span>
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
            class="bg-gradient-to-br from-white to-purple-50 rounded-2xl p-8 w-96 max-w-[90vw] shadow-2xl border border-purple-200/50">
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
                    class="px-6 py-2 bg-white border border-purple-200/50 text-purple-600 rounded-lg hover:bg-purple-50 transition-all duration-300 hover:shadow-md">
                    Cancel
                </button>
                <a href="/admin/logout"
                    role="button"
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-300 hover:shadow-md">
                    Logout
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Hide Scrollbar but Keep Scrollable */
        .scrollbar-hide {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        /* Smooth Transitions for Sidebar Items */
        .sidebar-item {
            transition: all 0.3s ease-in-out;
        }

        /* Glassmorphism Effect */
        aside {
            background: linear-gradient(180deg, rgba(88, 28, 135, 0.9) 0%, rgba(49, 46, 129, 0.9) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Hover Effects for Buttons */
        .sidebar-item:hover {
            background: linear-gradient(to right, rgba(147, 51, 234, 0.6) 0%, rgba(79, 70, 229, 0.6) 100%);
            transform: translateX(5px);
        }

        /* Submenu Styling */
        aside ul ul {
            background: rgba(88, 28, 135, 0.7);
            border-radius: 8px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            aside ul ul {
                width: 100%;
                max-width: 100%;
            }
        }

        @media (min-width: 768px) {
            button[x-on\\:click="isSidebarOpen = !isSidebarOpen"] {
                display: none;
            }
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