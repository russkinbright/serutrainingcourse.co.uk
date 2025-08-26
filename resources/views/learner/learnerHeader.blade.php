@extends('home.default')
<style>
    .svg-animation {
        animation: pulse 2s infinite ease-in-out;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.1);
            opacity: 1;
        }
    }

    .dropdown-item:hover .svg-icon {
        transform: rotate(360deg);
        transition: transform 0.3s ease;
    }

    .modal-animation {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
<div x-data="{
    isSidebarOpen: true,
    selected: null,
    subOpen: false,
    selectedSub: null,
    showLogoutModal: false
}" class="" x-cloak>
    <!-- Main Content -->
    <div class="flex-1 p-6">
        <div class="bg-white bg-opacity-90 shadow-lg px-6 py-4 rounded-xl flex justify-between items-center">
            <!-- Left: Avatar and Info with SVG Animation -->
            <div class="flex items-center space-x-4">
                <div>
                    <h1 class="text-2xl font-bold text-purple-800">{{ $learner->name }}</h1>
                    <p class="mt-1 text-sm text-purple-600">
                        <span class="font-bold text-purple-700">Secret ID :</span> {{ $learner->secret_id }}
                    </p>
                </div>
            </div>

            <!-- Right: Settings Dropdown with SVG -->
            <div class="relative" x-data="{ isHovering: false }">
                <button onclick="showPanel('learnerCoursePanel')" @mouseenter="isHovering = true"
                    @mouseleave="isHovering = false"
                    class="p-2 bg-purple-200 rounded-full w-12 h-12 cursor-pointer transition transform hover:scale-110 hover:bg-purple-300 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-800 svg-animation" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                    </svg>
                </button>

                <!-- Hover Dropdown -->
                <ul x-show="isHovering" @mouseenter="isHovering = true" @mouseleave="isHovering = false" x-transition
                    class="absolute right-0 mt-2 w-44 p-2 rounded-lg shadow-lg z-50 backdrop-blur-lg border border-purple-200 bg-white bg-opacity-95">

                    <!-- View Profile -->
                    <li>
                        <a href="{{ route('learner.profile.show') }}"
                            class="w-full flex items-center p-2 text-sm rounded-md text-purple-800 cursor-pointer font-medium hover:bg-purple-300 transition-all dropdown-item">
                            <span class="w-6 h-6 flex items-center justify-center bg-purple-100 rounded-md mr-2">
                                <svg class="w-4 h-4 text-purple-600 svg-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            View Profile
                        </a>
                    </li>

                    <!-- Logout -->
                    <li>
                        <button @click="showLogoutModal = true"
                            class="w-full flex items-center p-2 text-sm rounded-md text-purple-800 cursor-pointer font-medium hover:bg-purple-300 transition-all dropdown-item">
                            <span class="w-6 h-6 flex items-center justify-center bg-purple-100 rounded-md mr-2">
                                <svg class="w-4 h-4 text-purple-600 svg-icon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            Logout
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div x-show="showLogoutModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center backdrop-blur-sm bg-opacity-30 z-50">
        <div class="bg-white bg-opacity-95 rounded-lg p-6 w-96 shadow-xl modal-animation">
            <h3 class="text-lg font-bold text-purple-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Confirm Logout
            </h3>
            <p class="text-purple-600 mb-6">Are you sure you want to log out?</p>
            <div class="flex justify-end space-x-4">
                <button @click="showLogoutModal = false"
                    class="px-4 py-2 bg-purple-200 text-purple-800 rounded hover:bg-purple-300 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </button>
                <a href="{{ route('learner.logout') }}"
                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                    Yes, Logout
                </a>
            </div>
        </div>
    </div>
</div>
