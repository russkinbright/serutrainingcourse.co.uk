<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-auto mx-auto bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="practiceManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-book-open text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Create Practices
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Number of Practices Input -->
            <div class="mb-8">
                <label for="practiceCount" class="block text-xl font-bold text-gray-900 mb-3">
                    Number of Practices
                </label>
                <input type="number" id="practiceCount" x-model.number="practiceCount" min="0" max="20"
                    @input="generatePracticeFields()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                    placeholder="Enter number of practices (0-20)">
            </div>

            <!-- Practice Inputs -->
            <div x-show="practiceFields.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Practice Names</h2>
                <template x-for="(field, index) in practiceFields" :key="index">
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="relative">
                            <label :for="'tag-' + index" class="block text-lg font-semibold text-gray-900 mb-2">
                                Tag (Section Name)
                            </label>
                            <input type="text" :id="'tag-' + index" x-model="practiceFields[index].tagSearch"
                                @input.debounce.500="searchTagSections(index)"
                                class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                                placeholder="Search section for tag">
                            <div x-show="practiceFields[index].isTagSearching" class="mt-2 flex items-center">
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
                            <div x-show="practiceFields[index].tagSearchResults.length > 0"
                                class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                                <ul class="max-h-60 overflow-y-auto">
                                    <template x-for="section in practiceFields[index].tagSearchResults"
                                        :key="section.id">
                                        <li @click="selectTag(index, section.name)"
                                            class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                            <span x-text="section.name"></span> (ID: <span
                                                x-text="section.unique_id"></span>)
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            <div x-show="practiceFields[index].tag" class="mt-2 text-gray-700">
                                <span class="font-bold text-purple-700">Selected Tag: </span> <span x-text="practiceFields[index].tag"></span>
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label :for="'practice-' + index" class="block text-lg font-semibold text-gray-900 mb-2">
                                Practice <span x-text="index + 1"></span>
                            </label>
                            <input type="text" :id="'practice-' + index" x-model="practiceFields[index].name"
                                class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                                placeholder="Enter practice name">
                        </div>
                        <div class="flex items-end">
                            <button @click="removePractice(index)"
                                class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Submit Button -->
            <div x-show="practiceFields.length > 0" class="mb-8">
                <button @click.prevent="submitPractices()"
                    :disabled="isSubmitting || practiceFields.some(field => !field.name || !field.tag)"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="(isSubmitting || practiceFields.some(field => !field.name || !field.tag)) ?
                    'bg-gray-400 cursor-not-allowed' :
                    'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                    <span
                        class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                    <template x-if="isSubmitting">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Creating Practices...</span>
                        </div>
                    </template>
                    <template x-if="!isSubmitting">
                        <div class="flex items-center">
                            <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                            <span>Create Practices</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('practiceManager', () => ({
            practiceCount: 0,
            practiceFields: [],
            isSubmitting: false,
            formMessage: '',
            formStatus: '',
            init() {
                // Initialize component
            },
            generatePracticeFields() {
                if (this.practiceCount < 0 || this.practiceCount > 20) {
                    this.practiceFields = [];
                    return;
                }
                this.practiceFields = Array.from({
                    length: this.practiceCount
                }, () => ({
                    name: '',
                    tag: '',
                    tagSearch: '',
                    tagSearchResults: [],
                    isTagSearching: false
                }));
            },
            searchTagSections(index) {
                const query = this.practiceFields[index].tagSearch.trim();
                if (!query) {
                    this.practiceFields[index].tagSearchResults = [];
                    this.practiceFields[index].isTagSearching = false;
                    return;
                }
                this.practiceFields[index].isTagSearching = true;
                fetch('{{ route('practice.search') }}?query=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        this.practiceFields[index].tagSearchResults = data;
                    })
                    .catch(error => {
                        console.error('Error searching sections for tag:', error);
                        this.practiceFields[index].tagSearchResults = [];
                        this.formStatus = 'error';
                        this.formMessage =
                            'Failed to search sections for tag. Please try again.';
                    })
                    .finally(() => {
                        this.practiceFields[index].isTagSearching = false;
                    });
            },
            selectTag(index, name) {
                this.practiceFields[index].tag = name;
                this.practiceFields[index].tagSearch = '';
                this.practiceFields[index].tagSearchResults = [];
            },
            removePractice(index) {
                this.practiceFields.splice(index, 1);
                this.practiceCount = this.practiceFields.length;
            },
            submitPractices() {
                if (this.practiceFields.some(field => !field.name || !field.tag)) {
                    this.formStatus = 'error';
                    this.formMessage = 'Please fill in all practice names and tags.';
                    return;
                }
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';

                fetch('{{ route('practice.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            practices: this.practiceFields
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            this.formStatus = 'success';
                            this.formMessage = data.message;
                            this.practiceCount = 0;
                            this.practiceFields = [];
                        } else if (data.errors) {
                            this.formStatus = 'error';
                            this.formMessage = Object.values(data.errors).flat().join(' ');
                        }
                    })
                    .catch(error => {
                        console.error('Error creating practices:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to create practices. Please try again.';
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
            }
        }));
    });
</script>
