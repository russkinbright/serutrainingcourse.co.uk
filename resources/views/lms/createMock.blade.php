<div>
    <div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
        <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
                x-data="mockManager" x-init="init">
                <h1
                    class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                    <i class="fas fa-file-alt text-amber-300 mr-3 animate-pulse text-4xl"></i>
                    Create Mock Tests
                </h1>

                <!-- Message Container -->
                <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                    :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                    <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                        'fas fa-exclamation-circle text-red-600 animate-pulse'"
                        class="mr-3"></i>
                    <span x-text="formMessage"></span>
                </div>

                <!-- Mock Test Count Inputs -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="mock1Count" class="block text-xl font-bold text-gray-900 mb-3">
                            Number of Mock Test 1
                        </label>
                        <input type="number" id="mock1Count" x-model.number="mock1Count" min="0" max="20"
                            class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                            placeholder="Enter number of Mock Test 1 (0-20)">
                    </div>
                    <div>
                        <label for="mock2Count" class="block text-xl font-bold text-gray-900 mb-3">
                            Number of Mock Test 2
                        </label>
                        <input type="number" id="mock2Count" x-model.number="mock2Count" min="0" max="20"
                            class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                            placeholder="Enter number of Mock Test 2 (0-20)">
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="mb-8">
                    <button @click="generateMockFields()" :disabled="mock1Count === 0 && mock2Count === 0"
                        class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                        :class="mock1Count === 0 && mock2Count === 0 ? 'bg-gray-400 cursor-not-allowed' :
                            'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                        <i class="fas fa-plus-circle mr-3 animate-pulse text-amber-300"></i>
                        Generate Mock Test Fields
                    </button>
                </div>

                <!-- Mock Test Inputs -->
                <div x-show="mockFields.length > 0" class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Mock Tests</h2>
                    <template x-for="(field, index) in mockFields" :key="index">
                        <div class="mb-6 p-4 bg-white/80 rounded-xl shadow-md border border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <span
                                        x-text="field.mock_number === 'mock 1' ? 'Mock Test 1' : 'Mock Test 2'"></span> || 
                                          Index Number: <span class="text-pink-600" x-text="index + 1"></span>
                                </h3>
                                <button @click="removeMockField(index)"
                                    class="px-3 py-2 bg-red-600 text-white font-bold rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Remove
                                </button>
                            </div>
                            <div class="mb-4">
                                <label :for="'mock-name-' + index"
                                    class="block text-lg font-semibold text-purple-900 mb-2">
                                    Mock Test Name
                                </label>
                                <input type="text" :id="'mock-name-' + index" x-model="mockFields[index].name"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner font-bold text-green-600"
                                    placeholder="Enter mock test name">
                            </div>
                            <div class="mb-4">
                                <label :for="'section-search-' + index"
                                    class="block text-lg font-semibold text-purple-900 mb-2">
                                    Section Tag
                                </label>
                                <input type="text" :id="'section-search-' + index"
                                    x-model="mockFields[index].sectionSearch"
                                    @input.debounce.500="searchSections(index)"
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                                    placeholder="Search section by name or ID">
                                <div x-show="mockFields[index].isSectionSearching" class="mt-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span class="text-purple-600">Searching sections...</span>
                                </div>
                                <div x-show="mockFields[index].sectionSearchResults.length > 0"
                                    class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                                    <ul class="max-h-60 overflow-y-auto">
                                        <template x-for="section in mockFields[index].sectionSearchResults"
                                            :key="section.unique_id">
                                            <li @click="selectSection(index, section.unique_id, section.name)"
                                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                                <span x-text="section.name"></span> (ID: <span
                                                    x-text="section.unique_id"></span>)
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div x-show="mockFields[index].tag" class="mt-2 text-gray-700">
                                    <span class="font-bold text-green-700">Selected Tag:</span> <span class="font-bold" x-text="mockFields[index].tag"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Submit Button -->
                <div x-show="mockFields.length > 0" class="mb-8">
                    <button @click.prevent="submitMocks()"
                        :disabled="isSubmitting || mockFields.some(field => !field.name || !field.tag)"
                        class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                        :class="{
                            'bg-gray-400 cursor-not-allowed': isSubmitting || mockFields.some(f => !f.name || !f.tag),
                            'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50':
                                !(isSubmitting || mockFields.some(f => !f.name || !f.tag))
                        }">
                        <span
                            class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                        <template x-if="isSubmitting">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Creating Mock Tests...</span>
                            </div>
                        </template>
                        <template x-if="!isSubmitting">
                            <div class="flex items-center">
                                <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                                <span>Create Mock Tests</span>
                            </div>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mockManager', () => ({
                mock1Count: 0,
                mock2Count: 0,
                mockFields: [],
                isSubmitting: false,
                formMessage: '',
                formStatus: '',
                init() {
                    // Initialize component
                },
                async searchSections(index) {
                    const query = this.mockFields[index].sectionSearch.trim();
                    if (!query) {
                        this.mockFields[index].sectionSearchResults = [];
                        this.mockFields[index].isSectionSearching = false;
                        return;
                    }
                    this.mockFields[index].isSectionSearching = true;
                    try {
                        const response = await fetch(
                            '{{ route('createmock.search') }}?query=' + encodeURIComponent(
                                query), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                        this.mockFields[index].sectionSearchResults = await response.json();
                    } catch (error) {
                        console.error('Error searching sections:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to search sections. Please try again.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                    } finally {
                        this.mockFields[index].isSectionSearching = false;
                    }
                },
                selectSection(index, unique_id, name) {
                    this.mockFields[index].tag = name;
                    this.mockFields[index].sectionSearch = '';
                    this.mockFields[index].sectionSearchResults = [];
                },
                generateMockFields() {
                    if (this.mock1Count < 0 || this.mock1Count > 20 || this.mock2Count < 0 || this
                        .mock2Count > 20) {
                        this.mockFields = [];
                        this.formStatus = 'error';
                        this.formMessage = 'Please enter valid numbers (0-20) for mock test counts.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                        return;
                    }
                    this.mockFields = [];
                    for (let i = 0; i < this.mock1Count; i++) {
                        this.mockFields.push({
                            mock_number: 'mock 1',
                            name: '',
                            tag: '',
                            sectionSearch: '',
                            sectionSearchResults: [],
                            isSectionSearching: false
                        });
                    }
                    for (let i = 0; i < this.mock2Count; i++) {
                        this.mockFields.push({
                            mock_number: 'mock 2',
                            name: '',
                            tag: '',
                            sectionSearch: '',
                            sectionSearchResults: [],
                            isSectionSearching: false
                        });
                    }
                    this.formStatus = 'success';
                    this.formMessage = 'Mock test fields generated successfully!';
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                },
                removeMockField(index) {
                    const mockNumber = this.mockFields[index].mock_number;
                    this.mockFields.splice(index, 1);
                    if (mockNumber === 'mock 1') {
                        this.mock1Count = Math.max(0, this.mock1Count - 1);
                    } else {
                        this.mock2Count = Math.max(0, this.mock2Count - 1);
                    }
                },
                async submitMocks() {
                    if (this.mockFields.some(field => !field.name || !field.tag)) {
                        this.formStatus = 'error';
                        this.formMessage =
                            'Please fill in all mock test names and select a section tag for each.';
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                        return;
                    }
                    this.isSubmitting = true;
                    try {
                        const response = await fetch('{{ route('createmock.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                mocks: this.mockFields.map(field => ({
                                    mock_number: field.mock_number,
                                    tag: field.tag,
                                    name: field.name
                                }))
                            })
                        });
                        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                        const data = await response.json();
                        this.formStatus = 'success';
                        this.formMessage = data.message;
                        this.mock1Count = 0;
                        this.mock2Count = 0;
                        this.mockFields = [];
                    } catch (error) {
                        console.error('Error submitting mock tests:', error);
                        this.formStatus = 'error';
                        this.formMessage = `Error submitting mock tests: ${error.message}`;
                    } finally {
                        this.isSubmitting = false;
                        setTimeout(() => {
                            this.formMessage = '';
                            this.formStatus = '';
                        }, 3000);
                    }
                }
            }));
        });
    </script>

    <style>
        @keyframes bounce-in {
            0% {
                transform: scale(0.9) translateY(-20px);
                opacity: 0;
            }

            60% {
                transform: scale(1.05) translateY(5px);
                opacity: 1;
            }

            100% {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .animate-bounce-in {
            animation: bounce-in 0.5s ease-out;
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s infinite ease-in-out;
        }

        .glassmorphic {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .slide-up {
            animation: slide-up 0.5s ease-out;
        }

        @keyframes slide-up {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</div>
