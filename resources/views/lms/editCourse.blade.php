<div class="bg-gradient-to-br from-gray-100 to-indigo-100 font-sans min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-auto mx-auto bg-white/90 shadow-2xl rounded-3xl p-10 glassmorphic tilt slide-up border border-indigo-200/50"
            x-data="editCourse" x-init="initQuill">
            <h1
                class="text-5xl font-extrabold text-center bg-gradient-to-r from-cyan-500 via-purple-600 to-magenta-500 text-transparent bg-clip-text mb-10 drop-shadow-lg">
                <i class="fas fa-edit text-amber-300 mr-3 animate-pulse text-4xl"></i>
                Edit a Course
            </h1>

            <!-- Message Container -->
            <div x-show="formMessage" class="mb-8 p-5 rounded-2xl flex items-center slide-up shadow-md"
                :class="formStatus === 'success' ? 'bg-green-100/80 text-green-900' : 'bg-red-100/80 text-red-900'">
                <i :class="formStatus === 'success' ? 'fas fa-check-circle text-green-600 animate-bounce' :
                    'fas fa-exclamation-circle text-red-600 animate-pulse'"
                    class="mr-3"></i>
                <span x-text="formMessage"></span>
            </div>

            <!-- Delete Confirmation Modal -->
            <div x-show="showDeleteModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
                <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-2xl">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Confirm Deletion</h2>
                    <p class="text-gray-700 mb-6">
                        Are you sure you want to delete the course "<span
                            x-text="selectedCourse ? selectedCourse.title : ''"></span>"? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-4">
                        <button @click="showDeleteModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition-all duration-300">
                            Cancel
                        </button>
                        <button @click="deleteCourse()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-8 relative">
                <label for="search" class="block text-xl font-bold text-gray-900 mb-3">
                    Search Course
                </label>
                <input type="text" id="search" x-model.debounce.500="searchQuery" @input="searchCourses()"
                    class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.02] transform"
                    placeholder="Search by title or unique ID">
                <div x-show="isSearching" class="mt-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin text-purple-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-purple-600">Searching courses...</span>
                </div>
                <div x-show="searchResults.length > 0"
                    class="absolute z-10 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200">
                    <ul class="max-h-60 overflow-y-auto">
                        <template x-for="course in searchResults" :key="course.id">
                            <li @click="selectCourse(course.id)"
                                class="px-4 py-3 hover:bg-cyan-100 cursor-pointer transition-all duration-200">
                                <span x-text="course.title"></span> (ID: <span x-text="course.unique_id"></span>)
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Edit Form (Hidden until a course is selected) -->
            <div x-show="selectedCourse" class="mt-8">
                <form id="editCourseForm"
                    x-bind:action="selectedCourse ? '{{ url('edit-course') }}/' + selectedCourse.id : ''"
                    method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <!-- Course Title and Slug -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Course Title -->
                            <div>
                                <label for="title" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Course Title <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="title" id="title" required x-model.debounce.500="title"
                                    @input.debounce.500="checkTitle(); generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                    placeholder="Enter course title">
                                <div x-show="!title && !isCheckingTitle && !titleStatus" class="mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-500 mr-2 animate-pulse"></i>
                                    <span class="text-red-600 text-sm">Title is required</span>
                                </div>
                                <div x-show="isCheckingTitle" class="mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 animate-spin text-purple-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span class="text-purple-600 text-sm">Checking title availability...</span>
                                </div>
                                <div x-show="titleStatus && !isCheckingTitle" class="mt-2 flex items-center">
                                    <i x-show="titleStatus === 'success'"
                                        class="fas fa-check-circle text-green-500 mr-2 animate-pulse"></i>
                                    <i x-show="titleStatus === 'error'"
                                        class="fas fa-exclamation-circle text-red-500 mr-2 animate-pulse"></i>
                                    <span :class="titleStatus === 'success' ? 'text-green-600' : 'text-red-600'"
                                        class="text-sm" x-text="titleMessage"></span>
                                </div>
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Slug <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="slug" id="slug" required x-model="slug"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                    placeholder="Enter course slug (e.g., course-name)">
                                <div class="mt-2 text-sm text-gray-600">
                                    URL-friendly version of the title (lowercase, hyphens, no spaces).
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">SEO Settings</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Meta Title + Canonical + Robots Meta in one row -->
                            <div class="col-span-1 md:col-span-2">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                                    <!-- Meta Title -->
                                    <div>
                                        <label for="meta_title" class="block text-lg font-semibold text-gray-900 mb-2">
                                            Meta Title
                                        </label>
                                        <input type="text" name="meta_title" id="meta_title" x-model="meta_title"
                                            @input.debounce.500="generateSchema()"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                            placeholder="Enter meta title (60-70 chars)">
                                        <div class="mt-2 text-sm text-gray-600">
                                            Optimize for search engines (optional).
                                        </div>
                                    </div>

                                    <!-- Canonical URL -->
                                    <div>
                                        <label for="canonical_url" class="block text-lg font-semibold text-gray-900 mb-2">
                                            Canonical URL
                                        </label>
                                        <input type="url" name="canonical_url" id="canonical_url"
                                            x-model="canonical_url"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                            placeholder="e.g., https://example.com/course-name">
                                        <div class="mt-2 text-sm text-gray-600">
                                            Preferred URL for duplicate content (optional).
                                        </div>
                                    </div>

                                    <!-- Robots Meta -->
                                    <div>
                                        <label for="robots_meta" class="block text-lg font-semibold text-gray-900 mb-2">
                                            Robots Meta
                                        </label>
                                        <select name="robots_meta" id="robots_meta" x-model="robots_meta"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 hover:scale-[1.01] transform">
                                            <option value="" selected>Select robots meta</option>
                                            <option value="index,follow">Index, Follow</option>
                                            <option value="noindex,nofollow">Noindex, Nofollow</option>
                                        </select>
                                        <div class="mt-2 text-sm text-gray-600">
                                            Control search engine indexing (optional).
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Meta Description -->
                            <div>
                                <label for="meta_description" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Meta Description
                                </label>
                                <textarea name="meta_description" id="meta_description" x-model="meta_description"
                                    @input.debounce.500="generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                    placeholder="Brief description (150-160 chars)" rows="3"></textarea>
                                <div class="mt-2 text-sm text-gray-600">
                                    Describe course for search engines (optional).
                                </div>
                            </div>

                            <!-- Meta Keywords -->
                            <div>
                                <label for="meta_keywords" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Meta Keywords
                                </label>
                                <textarea name="meta_keywords" id="meta_keywords" x-model="meta_keywords"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                    placeholder="Enter keywords (e.g., course, learning)" rows="3"></textarea>
                                <div class="mt-2 text-sm text-gray-600">
                                    Comma-separated keywords (optional).
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schema Markup (Full Width) -->
                    <div class="mb-8">
                        <div class="flex items-center mb-3">
                            <input type="checkbox" id="autoSchema" x-model="autoSchema"
                                class="mr-2 h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="autoSchema" class="text-lg font-semibold text-gray-900">
                                Auto-generate Schema Markup
                            </label>
                        </div>
                        <label for="schema_markup" class="block text-lg font-semibold text-gray-900 mb-2">
                            Schema Markup
                        </label>
                        <textarea name="schema_markup" id="schema_markup" x-model="schema_markup"
                            @input="autoSchema = false"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-900 focus:bg-white hover:scale-[1.01] transform"
                            placeholder='e.g., {"@type":"Course","name":"Course Name"}' rows="6"></textarea>
                        <div class="mt-2 text-sm text-gray-600">
                            Structured data for search engines (must be valid JSON, optional).
                            <span x-text="autoSchema ? 'Auto-generated based on form inputs.' : 'Manually edited.'"></span>
                        </div>
                        <div x-show="schema_markup && !validateJson()" class="mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2 animate-pulse"></i>
                            <span class="text-red-600 text-sm">Invalid JSON format</span>
                        </div>
                    </div>

                    <!-- Course Description (Full Width) -->
                    <div class="mb-8">
                        <label for="description" class="block text-lg font-semibold text-gray-900 mb-2">
                            Course Description <span class="text-red-600">*</span>
                        </label>
                        <div x-ref="quillEditor" class="h-64 rounded-xl border border-gray-300 shadow-inner bg-white/50">
                        </div>
                        <input type="hidden" name="description" x-model="description">
                        <div class="mt-2 flex items-center space-x-4">
                            <span class="text-gray-600 text-sm">
                                Words: <span x-text="wordCount" class="font-semibold text-purple-600"></span>
                            </span>
                            <span class="text-gray-600 text-sm">
                                Characters: <span x-text="charCount" class="font-semibold text-purple-600"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Course Details -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Course Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Price <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="price" id="price" x-model="price" x-ref="priceInput"
                                    required step="0.01" min="0" placeholder="Enter course price"
                                    @input="generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 focus:bg-white transition-all duration-500 hover:scale-[1.01] transform" />
                            </div>

                            <!-- Footer Price -->
                            <div>
                                <label for="footer_price" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Footer Price
                                </label>
                                <input type="number" name="footer_price" id="footer_price" x-model="footer_price" x-ref="footerPriceInput"
                                    step="0.01" min="0" placeholder="Enter footer price (optional)"
                                    @input="generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 focus:bg-white transition-all duration-500 hover:scale-[1.01] transform" />
                            </div>

                            <!-- Enroll -->
                            <div>
                                <label for="enroll" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Total Enrolled Students <span class="text-red-600">*</span>
                                </label>
                                <input type="number" name="enroll" id="enroll" x-model="enroll"
                                    x-ref="enrollInput" required min="0"
                                    placeholder="Enter total enrolled students"
                                    @input="generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 focus:bg-white transition-all duration-500 hover:scale-[1.01] transform" />
                            </div>

                            <!-- Duration -->
                            <div>
                                <label for="duration" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Course Duration <span class="text-red-600">*</span>
                                </label>
                                <select name="duration" id="duration" required x-model="duration" x-ref="durationInput"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 hover:scale-[1.01] transform">
                                    <option value="" disabled selected>Select duration</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }} Week{{ $i > 1 ? 's' : '' }}">
                                            {{ $i }} Week{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label for="rating" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Rating <span class="text-red-600">*</span>
                                </label>
                                <select name="rating" id="rating" required x-model="rating" x-ref="ratingInput"
                                    @change="generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 hover:scale-[1.01] transform">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="1.5">1.5</option>
                                    <option value="2">2</option>
                                    <option value="2.5">2.5</option>
                                    <option value="3">3</option>
                                    <option value="3.5">3.5</option>
                                    <option value="4">4</option>
                                    <option value="4.5">4.5</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <!-- Image URL -->
                            <div class="md:col-span-2 lg:col-span-3">
                                <label for="image_url" class="block text-lg font-semibold text-gray-900 mb-2">
                                    Course Image URL
                                </label>
                                <input type="url" name="image_url" id="image_url" x-model="imagePreview"
                                    x-ref="imageInput"
                                    @input.debounce.500="imagePreview = $event.target.value; generateSchema()"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-4 focus:ring-cyan-400/50 transition-all duration-500 bg-white/50 shadow-inner text-gray-900 placeholder-gray-500 focus:bg-white hover:scale-[1.01] transform"
                                    placeholder="Enter image URL (e.g., https://example.com/image.jpg)">
                                <div x-show="imagePreview" class="mt-4">
                                    <img :src="imagePreview" alt="Image Preview"
                                        class="w-full h-48 sm:h-64 object-cover rounded-xl border-2 border-amber-200/50 shadow-lg hover:scale-[1.02] transform transition-all duration-300">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex space-x-4">
                        <button type="button"
                            :disabled="!title || !description || !duration || !price || !enroll || !slug || !rating || isSubmitting ||
                                titleStatus === 'error' || !validateJson()"
                            @click="submitForm()"
                            class="flex-1 px-6 py-4 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                            :class="(!title || !description || !duration || !price || !enroll || !slug || !rating ||
                                isSubmitting || titleStatus === 'error' || !validateJson()) ? 'bg-gray-400 cursor-not-allowed' :
                            'bg-gradient-to-r from-cyan-500 to-magenta-500 hover:from-cyan-600 hover:to-magenta-600 hover:ring-4 hover:ring-cyan-400/50'">
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                            <template x-if="isSubmitting">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 animate-spin text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Updating...</span>
                                </div>
                            </template>
                            <template x-if="!isSubmitting">
                                <div class="flex items-center">
                                    <i class="fas fa-rocket mr-3 animate-pulse text-amber-300"></i>
                                    <span>Update Course</span>
                                </div>
                            </template>
                        </button>
                        <button type="button" @click="showDeleteModal = true"
                            class="flex-1 px-6 py-4 bg-red-600 text-white font-bold text-lg rounded-xl transition-all duration-500 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 flex items-center justify-center relative overflow-hidden"
                            :class="isSubmitting ? 'bg-gray-400 cursor-not-allowed' :
                                'hover:bg-red-700 hover:ring-4 hover:ring-red-400/50'">
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-amber-300/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></span>
                            <div class="flex items-center">
                                <i class="fas fa-trash mr-3 animate-pulse"></i>
                                <span>Delete Course</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('editCourse', () => ({
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            selectedCourse: null,
            title: '',
            meta_title: '',
            meta_keywords: '',
            meta_description: '',
            slug: '',
            canonical_url: '',
            robots_meta: '',
            schema_markup: '',
            description: '',
            imagePreview: null,
            duration: '',
            price: '',
            footer_price: '',
            enroll: '',
            rating: '0',
            titleStatus: '',
            titleMessage: '',
            isCheckingTitle: false,
            isSubmitting: false,
            wordCount: 0,
            charCount: 0,
            formMessage: '',
            formStatus: '',
            showDeleteModal: false,
            autoSchema: true,
            quill: null,
            initQuill() {
                if (!this.$refs.quillEditor) {
                    console.error('Quill editor container not found');
                    return;
                }
                this.quill = new Quill(this.$refs.quillEditor, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            [{ 'font': [] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'color': [] }, { 'background': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    }
                });
                this.quill.root.innerHTML = this.description;
                this.quill.on('text-change', () => {
                    this.description = this.quill.root.innerHTML;
                    const text = this.quill.getText().trim();
                    this.wordCount = text ? text.split(/\s+/).length : 0;
                    this.charCount = text.length;
                    this.generateSchema();
                });
            },
            validateJson() {
                if (!this.schema_markup.trim()) return true;
                try {
                    JSON.parse(this.schema_markup);
                    return true;
                } catch (e) {
                    return false;
                }
            },
            generateSchema() {
                if (!this.autoSchema) return;
                const schema = {
                    '@context': 'https://schema.org',
                    '@type': 'Course',
                    name: this.title.trim() || 'Untitled Course',
                    description: this.meta_description.trim() || this.quill?.getText().trim() || 'No description provided',
                    image: this.imagePreview || undefined,
                    offers: {
                        '@type': 'Offer',
                        price: this.footer_price ? parseFloat(this.footer_price).toFixed(2) : (this.price ? parseFloat(this.price).toFixed(2) : '0.00'),
                        priceCurrency: 'GBP'
                    },
                    aggregateRating: {
                        '@type': 'AggregateRating',
                        ratingValue: this.rating || '0',
                        reviewCount: this.enroll || 0
                    }
                };
                if (this.footer_price) {
                    schema.offers.regularPrice = parseFloat(this.price).toFixed(2);
                }
                this.schema_markup = JSON.stringify(schema, null, 2);
            },
            searchCourses() {
                if (!this.searchQuery.trim()) {
                    this.searchResults = [];
                    this.isSearching = false;
                    return;
                }
                this.isSearching = true;
                fetch('{{ route('edit-course.search') }}?query=' + encodeURIComponent(this.searchQuery.trim()))
                    .then(response => response.json())
                    .then(data => {
                        this.searchResults = data;
                    })
                    .catch(error => {
                        console.error('Error searching courses:', error);
                        this.searchResults = [];
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to search courses. Please try again.';
                    })
                    .finally(() => {
                        this.isSearching = false;
                    });
            },
            selectCourse(id) {
                fetch('{{ url('edit-course') }}/' + id)
                    .then(response => response.json())
                    .then(data => {
                        this.selectedCourse = data;
                        this.searchQuery = '';
                        this.searchResults = [];
                        this.title = data.title || '';
                        this.meta_title = data.meta_title || '';
                        this.meta_keywords = data.meta_keywords || '';
                        this.meta_description = data.meta_description || '';
                        this.slug = data.slug || '';
                        this.canonical_url = data.canonical_url || '';
                        this.robots_meta = data.robots_meta || '';
                        this.schema_markup = data.schema_markup || '';
                        this.description = data.description || '';
                        this.duration = data.duration || '';
                        this.price = data.price || '';
                        this.footer_price = data.footer_price || '';
                        this.enroll = data.enroll || '';
                        this.rating = data.rating || '0';
                        this.imagePreview = data.image || null;
                        this.autoSchema = !this.schema_markup.trim();
                        if (this.quill) {
                            this.quill.root.innerHTML = this.description;
                        } else {
                            this.initQuill();
                        }
                        this.generateSchema();
                    })
                    .catch(error => {
                        console.error('Error fetching course:', error);
                        this.formStatus = 'error';
                        this.formMessage = 'Failed to load course details. Please try again.';
                    });
            },
            checkTitle() {
                if (!this.title.trim() || !this.selectedCourse) {
                    this.titleStatus = '';
                    this.titleMessage = '';
                    this.isCheckingTitle = false;
                    return;
                }
                this.isCheckingTitle = true;
                fetch('{{ route('edit-course.check-title', ':id') }}'.replace(':id', this.selectedCourse.id) + '?title=' +
                        encodeURIComponent(this.title.trim()))
                    .then(response => response.json())
                    .then(data => {
                        this.titleStatus = data.exists ? 'error' : 'success';
                        this.titleMessage = data.message;
                        this.slug = data.suggested_slug || this.slug;
                        this.generateSchema();
                    })
                    .catch(error => {
                        console.error('Error checking title:', error);
                        this.titleStatus = 'error';
                        this.titleMessage = 'Error checking title availability';
                        this.slug = '';
                    })
                    .finally(() => {
                        this.isCheckingTitle = false;
                    });
            },
            clearForm() {
                this.title = '';
                this.meta_title = '';
                this.meta_keywords = '';
                this.meta_description = '';
                this.slug = '';
                this.canonical_url = '';
                this.robots_meta = '';
                this.schema_markup = '';
                this.description = '';
                this.duration = '';
                this.price = '';
                this.footer_price = '';
                this.enroll = '';
                this.rating = '0';
                this.imagePreview = null;
                this.titleStatus = '';
                this.titleMessage = '';
                this.isCheckingTitle = false;
                this.isSubmitting = false;
                this.wordCount = 0;
                this.charCount = 0;
                this.formMessage = '';
                this.formStatus = '';
                this.autoSchema = true;
                if (this.quill) {
                    this.quill.root.innerHTML = '';
                }
                this.$refs.durationInput.selectedIndex = 0;
                this.$refs.priceInput.value = '';
                this.$refs.footerPriceInput.value = '';
                this.$refs.enrollInput.value = '';
                this.$refs.ratingInput.selectedIndex = 0;
                this.$refs.imageInput.value = '';
            },
            submitForm() {
                if (!this.title.trim() || !this.description || !this.duration || !this.price || !this.enroll || !this.slug || !this.rating || !this.selectedCourse) {
                    this.formStatus = 'error';
                    this.formMessage = 'Please fill in all required fields.';
                    this.isSubmitting = false;
                    return;
                }
                if (this.titleStatus === 'error') {
                    this.formStatus = 'error';
                    this.formMessage = 'Please choose a unique course title.';
                    this.isSubmitting = false;
                    return;
                }
                if (!this.validateJson()) {
                    this.formStatus = 'error';
                    this.formMessage = 'Schema markup must be valid JSON.';
                    this.isSubmitting = false;
                    return;
                }
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';

                const formData = new FormData();
                formData.append('title', this.title.trim());
                formData.append('meta_title', this.meta_title.trim());
                formData.append('meta_keywords', this.meta_keywords.trim());
                formData.append('meta_description', this.meta_description.trim());
                formData.append('slug', this.slug.trim());
                formData.append('canonical_url', this.canonical_url.trim());
                formData.append('robots_meta', this.robots_meta);
                formData.append('schema_markup', this.schema_markup.trim());
                formData.append('description', this.description);
                formData.append('duration', this.duration);
                formData.append('price', this.price);
                formData.append('footer_price', this.footer_price || '');
                formData.append('enroll', this.enroll);
                formData.append('rating', this.rating);
                if (this.imagePreview) {
                    formData.append('image_url', this.imagePreview);
                }
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');

                console.log('Submitting form with data:', Object.fromEntries(formData));
                fetch('{{ url('edit-course') }}/' + this.selectedCourse.id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    console.log('Response status:', response.status);
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed JSON:', data);
                        if (response.ok) {
                            this.formStatus = 'success';
                            this.formMessage = data.message || 'Course updated successfully!';
                            this.clearForm();
                            this.selectedCourse = null;
                            this.searchQuery = '';
                            this.searchResults = [];
                        } else if (data.errors) {
                            this.formStatus = 'error';
                            this.formMessage = Object.values(data.errors).flat().join(' ');
                        } else {
                            this.formStatus = 'error';
                            this.formMessage = 'Unexpected error occurred. Please try again.';
                        }
                    } catch (e) {
                        console.error('Failed to parse JSON. Raw response:', text);
                        this.formStatus = 'error';
                        this.formMessage = 'Server returned an invalid response. Please check the console and try again.';
                    }
                })
                .catch(error => {
                    console.error('Form submission error:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to update the course. Please check the console for details and try again.';
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
            },
            deleteCourse() {
                if (!this.selectedCourse) {
                    this.formStatus = 'error';
                    this.formMessage = 'No course selected for deletion.';
                    this.showDeleteModal = false;
                    return;
                }
                this.isSubmitting = true;
                this.formMessage = '';
                this.formStatus = '';

                fetch('{{ url('edit-course') }}/' + this.selectedCourse.id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(async response => {
                    console.log('Response status:', response.status);
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed JSON:', data);
                        if (response.ok) {
                            this.formStatus = 'success';
                            this.formMessage = data.message || 'Course deleted successfully!';
                            this.clearForm();
                            this.selectedCourse = null;
                            this.searchQuery = '';
                            this.searchResults = [];
                            
                        } else if (data.errors) {
                            this.formStatus = 'error';
                            this.formMessage = Object.values(data.errors).flat().join(' ');
                        } else {
                            this.formStatus = 'error';
                            this.formMessage = 'Unexpected error occurred. Please try again.';
                        }
                    } catch (e) {
                        console.error('Failed to parse JSON. Raw response:', text);
                        this.formStatus = 'error';
                        this.formMessage = 'Server returned an invalid response. Please check the console and try again.';
                    }
                })
                .catch(error => {
                    console.error('Error deleting course:', error);
                    this.formStatus = 'error';
                    this.formMessage = 'Failed to delete the course. Please check the console for details and try again.';
                })
                .finally(() => {
                    this.isSubmitting = false;
                    this.showDeleteModal = false;
                });
            }
        }));
    });
</script>
