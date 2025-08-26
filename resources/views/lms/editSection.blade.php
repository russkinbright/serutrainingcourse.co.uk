<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-auto mx-auto bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="sectionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-edit text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Edit Section
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Search Bar -->
            <div class="mb-8 relative">
                <label for="search" class="block text-xl font-bold text-gray-900 mb-3">
                    Search Section
                </label>
                <input type="text" id="search" x-model.debounce.500="searchQuery" @input="searchSections()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                    placeholder="Search by section name or ID">
                <div x-show="isSearching" class="mt-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Searching sections...</span>
                </div>
                <div x-show="searchResults.length > 0"
                    class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                    <ul class="max-h-60 overflow-y-auto">
                        <template x-for="section in searchResults" :key="section.id">
                            <li @click="selectSection(section.id)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                <span x-text="'Section: ' + section.name + ' (Sequence: ' + section.sequence + ', ID: ' + section.unique_id + ', Courses: ' + section.course_titles + ')'"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div x-show="!isSearching && searchQuery && searchResults.length === 0"
                    class="mt-2 text-gray-600">
                    No sections found.
                </div>
                <div x-show="selectedSection" class="mt-2 text-gray-700">
                    Selected Section: <span
                        x-text="'Section: ' + selectedSection.name + ' (Sequence: ' + selectedSection.sequence + ', ID: ' + selectedSection.unique_id + ', Courses: ' + selectedSection.course_titles + ')'"></span>
                </div>
            </div>

            <!-- Section Edit Form -->
            <div x-show="selectedSection"
                class="mb-8 p-6 bg-gray-50 rounded-xl shadow-inner border border-indigo-200/50">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Edit Section</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="sectionName" class="block text-lg font-semibold text-gray-900 mb-2">
                            Section Name
                        </label>
                        <input type="text" id="sectionName" x-model="selectedSection.name"
                            @input="checkDuplicateSection()" class="w-full px-5 py-4 rounded-xl border"
                            :class="isDuplicate ? 'border-red-500 bg-red-100/50' :
                                'border-gray-300 focus:ring-4 focus:ring-cyan-400/50'"
                            placeholder="Enter section name">
                        <div x-show="isDuplicate" class="mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2 animate-pulse"></i>
                            <span class="text-red-600">This section name already exists.</span>
                        </div>
                    </div>
                    <div>
                        <label for="sequence" class="block text-lg font-semibold text-gray-900 mb-2">
                            Sequence
                        </label>
                        <input type="number" id="sequence" x-model.number="selectedSection.sequence" min="1"
                            class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                            placeholder="Enter sequence">
                    </div>
                </div>
                <div class="mt-6 flex space-x-4">
                    <button @click.prevent="updateSection()"
                        :disabled="isSubmitting || isDuplicate || !selectedSection.name || !selectedSection.sequence"
                        class="flex-1 px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                        :class="(isSubmitting || isDuplicate || !selectedSection.name || !selectedSection.sequence) ? 'bg-gray-400 cursor-not-allowed' :
                        'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
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
                                <span>Saving...</span>
                            </div>
                        </template>
                        <template x-if="!isSubmitting">
                            <div class="flex items-center">
                                <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                                <span>Save Changes</span>
                            </div>
                        </template>
                    </button>
                    <button @click="openDeleteModal()"
                        class="px-6 py-4 bg-red-600 text-white font-bold text-lg rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Section
                    </button>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Confirm Deletion</h2>
                    <p class="text-gray-700 mb-6">Are you sure you want to delete the section "<span
                            x-text="selectedSection?.name"></span>"? This action cannot be undone.</p>
                    <div class="flex justify-end space-x-4">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-900 font-semibold rounded-xl hover:bg-gray-400 transition-all duration-300">
                            Cancel
                        </button>
                        <button @click="deleteSection()"
                            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sectionManager', () => ({
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            selectedSection: null,
            isDuplicate: false,
            isSubmitting: false,
            formMessage: '',
            formStatus: '',
            showDeleteModal: false,
            init() {
                console.log('SectionManager initialized');
            },
            async searchSections() {
                if (!this.searchQuery.trim()) {
                    this.searchResults = [];
                    this.isSearching = false;
                    return;
                }
                this.isSearching = true;
                const url = '{{ route('edit-section.search') }}?query=' + encodeURIComponent(this.searchQuery.trim());
                console.log('Searching sections:', url);
                try {
                    const response = await fetch(url);
                    console.log('Search sections response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    this.searchResults = await response.json();
                    console.log('Search results:', this.searchResults);
                } catch (error) {
                    console.error('Error searching sections:', error);
                    this.searchResults = [];
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to search sections: ' + error.message;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                } finally {
                    this.isSearching = false;
                }
            },
            async selectSection(id) {
                const url = '{{ url('edit-section') }}/' + id;
                console.log('Fetching section:', url);
                try {
                    const response = await fetch(url);
                    console.log('Fetch section response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    this.selectedSection = await response.json();
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.formMessage = '';
                    this.formStatus = '';
                    console.log('Selected section:', this.selectedSection);
                    await this.checkDuplicateSection();
                } catch (error) {
                    console.error('Error fetching section:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to load section details: ' + error.message;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            async checkDuplicateSection() {
                if (!this.selectedSection || !this.selectedSection.name.trim()) {
                    this.isDuplicate = false;
                    return;
                }
                const url = '{{ route('edit-section.search') }}?query=' + encodeURIComponent(this.selectedSection.name.trim());
                console.log('Checking duplicate section:', url);
                try {
                    const response = await fetch(url);
                    console.log('Check duplicate response status:', response.status);
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const data = await response.json();
                    this.isDuplicate = data.some(section =>
                        section.name.toLowerCase() === this.selectedSection.name.trim().toLowerCase() &&
                        section.id !== this.selectedSection.id
                    );
                    console.log('Duplicate check result:', this.isDuplicate);
                } catch (error) {
                    console.error('Error checking duplicate section:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to check section name: ' + error.message;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            async updateSection() {
                if (!this.selectedSection || this.isDuplicate || !this.selectedSection.name || !this.selectedSection.sequence) {
                    this.formStatus = 'error';
                    this.formMessage = this.isDuplicate ? 'Section name already exists.' : 'Please fill in all fields.';
                    console.log('Update section blocked:', {
                        isDuplicate: this.isDuplicate,
                        name: this.selectedSection?.name,
                        sequence: this.selectedSection?.sequence
                    });
                    return;
                }
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';
                const url = '{{ url('edit-section') }}/' + this.selectedSection.id;
                console.log('Updating section:', url, this.selectedSection);
                try {
                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            name: this.selectedSection.name,
                            sequence: this.selectedSection.sequence
                        })
                    });
                    console.log('Update section response status:', response.status);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.errors?.general || `HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    this.selectedSection = null;
                    this.searchQuery = '';
                    this.searchResults = [];
                    console.log('Section updated:', data);
                } catch (error) {
                    console.error('Error updating section:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to update section: ' + error.message;
                } finally {
                    this.isSubmitting = false;
                    setTimeout(() => {
                        this.formMessage = '';
                        this.formStatus = '';
                    }, 3000);
                }
            },
            openDeleteModal() {
                this.showDeleteModal = true;
                console.log('Opened delete modal for section:', this.selectedSection?.id);
            },
            async deleteSection() {
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';
                const url = '{{ url('edit-section') }}/' + this.selectedSection.id;
                console.log('Deleting section:', url);
                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    });
                    console.log('Delete section response status:', response.status);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.errors?.general || `HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    this.formStatus = 'success';
                    this.formMessage = data.message;
                    this.selectedSection = null;
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.showDeleteModal = false;
                    console.log('Section deleted:', data);
                } catch (error) {
                    console.error('Error deleting section:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to delete section: ' + error.message;
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
        0% { transform: scale(0.9) translateY(-20px); opacity: 0; }
        60% { transform: scale(1.05) translateY(5px); opacity: 1; }
        100% { transform: scale(1) translateY(0); opacity: 1; }
    }
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    .animate-bounce-in { animation: bounce-in 0.5s ease-out; }
    .animate-pulse-slow { animation: pulse-slow 3s infinite ease-in-out; }
    .glassmorphic {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    .slide-up {
        animation: slide-up 0.5s ease-out;
    }
    @keyframes slide-up {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
