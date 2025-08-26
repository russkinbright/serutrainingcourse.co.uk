<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container-fluid px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-auto mx-auto bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="createSectionManager" x-init="init">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-layer-group text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Create Sections
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Number of Sections to Create Input -->
            <div class="mb-8">
                <label for="sectionsToCreate" class="block text-xl font-bold text-gray-900 mb-3">
                    Number of Sections to Create
                </label>
                <input type="number" id="sectionsToCreate" x-model.number="sectionsToCreate" min="1" max="20"
                    @input="generateSectionFields()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                    placeholder="Enter number of sections to create (1-20)">
            </div>

            <!-- Sequence Input and Button -->
            <div x-show="sectionFields.length > 0" class="mb-8 flex items-end space-x-4">
                <div class="flex-1">
                    <label for="startSequence" class="block text-xl font-bold text-gray-900 mb-3">
                        Starting Sequence (Optional)
                    </label>
                    <input type="number" id="startSequence" x-model.number="startSequence" min="1"
                        class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                        placeholder="Enter starting sequence (e.g., 10)">
                </div>
                <button @click="setSequence()"
                    class="px-6 py-4 bg-indigo-600 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center"
                    :class="sectionFields.length === 0 ? 'bg-gray-400 cursor-not-allowed' :
                        'hover:bg-indigo-700 hover:ring-4 hover:ring-indigo-400/50'">
                    <i class="fas fa-sort-numeric-up mr-3 animate-pulse"></i>
                    Set Sequence
                </button>
            </div>

            <!-- Section Name Inputs -->
            <div x-show="sectionFields.length > 0" class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Section Names</h2>
                <template x-for="(field, index) in sectionFields" :key="index">
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label :for="'sequence-' + index" class="block text-lg font-semibold text-gray-900 mb-2">
                                Sequence
                            </label>
                            <input type="number" :id="'sequence-' + index"
                                x-model.number="sectionFields[index].sequence"
                                class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900"
                                placeholder="Sequence" required>
                        </div>
                        <div class="md:col-span-3">
                            <label :for="'section-' + index" class="block text-lg font-semibold text-gray-900 mb-2">
                                Section <span x-text="index + 1"></span>
                            </label>
                            <input type="text" :id="'section-' + index" x-model="sectionFields[index].name"
                                @input="checkDuplicateSections()" class="w-full px-5 py-4 rounded-xl border"
                                :class="sectionFields[index].isDuplicate ? 'border-red-500 bg-red-100/50' :
                                    'border-gray-300 focus:ring-4 focus:ring-cyan-400/50'"
                                placeholder="Enter section name">
                            <div x-show="sectionFields[index].isDuplicate" class="mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2 animate-pulse"></i>
                                <span class="text-red-600">This section name is duplicated.</span>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button @click="removeSection(index)"
                                class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl transition-all duration-500 hover:bg-red-700 hover:ring-4 hover:ring-red-400/50 flex items-center">
                                <i class="fas fa-trash mr-2"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Submit Button -->
            <div x-show="sectionFields.length > 0" class="mb-8">
                <button @click.prevent="submitSections()"
                    :disabled="isSubmitting || hasDuplicates || sectionFields.some(field => !field.name || !field.sequence)"
                    class="w-full px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                    :class="(isSubmitting || hasDuplicates || sectionFields.some(field => !field.name || !field.sequence)) ?
                    'bg-gray-400 cursor-not-allowed' :
                    'bg-purple-800 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
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
                            <span>Creating Sections...</span>
                        </div>
                    </template>
                    <template x-if="!isSubmitting">
                        <div class="flex items-center">
                            <i class="fas fa-save mr-3 animate-pulse text-amber-300"></i>
                            <span>Create Sections</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('createSectionManager', () => ({
            sectionsToCreate: 0,
            sectionCount: 0,
            sectionFields: [],
            startSequence: null,
            isSubmitting: false,
            formMessage: '',
            formStatus: '',
            hasDuplicates: false,
            init() {
                // Initialize component (if needed)
            },
            generateSectionFields() {
                // Use sectionsToCreate if it's set, otherwise use sectionCount
                const count = this.sectionsToCreate > 0 ? this.sectionsToCreate : this.sectionCount;
                if (count < 1 || count > 20) {
                    this.sectionFields = [];
                    return;
                }
                this.sectionFields = Array.from({
                    length: count
                }, () => ({
                    name: '',
                    sequence: null,
                    isDuplicate: false
                }));
                this.checkDuplicateSections();
            },
            checkDuplicateSections() {
                const names = this.sectionFields.map(field => field.name.trim().toLowerCase());
                this.sectionFields.forEach((field, index) => {
                    const name = field.name.trim().toLowerCase();
                    field.isDuplicate = name && names.indexOf(name) !== names.lastIndexOf(name);
                });
                this.hasDuplicates = this.sectionFields.some(field => field.isDuplicate);
            },
            setSequence() {
                if (this.sectionFields.length === 0) return;
                const start = this.startSequence && this.startSequence >= 1 ? this.startSequence : 1;
                this.sectionFields.forEach((field, index) => {
                    field.sequence = start + index;
                });
            },
            removeSection(index) {
                this.sectionFields.splice(index, 1);
                this.sectionCount = this.sectionFields.length;
                this.sectionsToCreate = this.sectionFields.length;
                this.checkDuplicateSections();
                if (this.sectionFields.length > 0 && this.sectionFields.some(field => field.sequence)) {
                    this.setSequence(); // Reassign sequences to maintain continuity
                }
            },
            submitSections() {
                if (this.sectionFields.some(field => !field.name || !field.sequence) || this.hasDuplicates) {
                    this.formStatus = 'error';
                    this.formMessage = this.hasDuplicates ?
                        'Please resolve duplicate section names.' :
                        'Please fill in all section names and sequences.';
                    return;
                }
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';

                fetch('{{ route('section.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            sections: this.sectionFields
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            this.formStatus = 'success';
                            this.formMessage = data.message;
                            this.sectionsToCreate = 0;
                            this.sectionCount = 0;
                            this.sectionFields = [];
                            this.startSequence = null;
                        } else if (data.errors) {
                            this.formStatus = 'error';
                            this.formMessage = Object.values(data.errors).flat().join(' ');
                        }
                    })
                    .catch(error => {
                        console.error('Error creating sections:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to create sections. Please try again.';
                    })
                    .finally(() => {
                        this.isSubmitting = false;
                    });
            }
        }));
    });
</script>