@extends('home.default')

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
    :root {
        --purple-dark: #6b21a8;
        --purple-light: #9333ea;
        --purple-gradient: linear-gradient(135deg, #9333ea, #4f46e5);
        --shadow-glow: 0 4px 20px rgba(147, 51, 234, 0.3);
    }

    .purple-input {
        border: 2px solid var(--purple-light);
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 0.75rem;
    }

    .purple-input:focus {
        outline: none;
        border-color: var(--purple-dark);
        box-shadow: var(--shadow-glow);
        transform: scale(1.01);
    }

    .purple-button {
        background: var(--purple-gradient);
        transition: all 0.3s ease;
        border-radius: 0.75rem;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .purple-button:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-glow);
        background: linear-gradient(135deg, #7e22ce, #4338ca);
    }

    .purple-button::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }

    .purple-button:hover::after {
        width: 200%;
        height: 200%;
    }

    #editor {
        min-height: 300px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .ql-toolbar {
        border: 2px solid #e5e7eb !important;
        border-radius: 0.75rem 0.75rem 0 0 !important;
        background: linear-gradient(to bottom, #f9fafb, #ffffff);
    }

    .ql-container {
        border: 2px solid #e5e7eb !important;
        border-radius: 0 0 0.75rem 0.75rem !important;
    }

    .invalid-input {
        border-color: #ef4444 !important;
        background: #fef2f2 !important;
    }

    .valid-input {
        border-color: #22c55e !important;
        background: #f0fdf4 !important;
    }

    .form-container {
        background: linear-gradient(145deg, #ffffff, #f3e8ff);
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(147, 51, 234, 0.1);
        transition: transform 0.3s ease;
    }

    .form-container:hover {
        transform: translateY(-5px);
    }

    .card-section {
        background: linear-gradient(145deg, #f3e8ff, #e9d5ff);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(147, 51, 234, 0.1);
    }

    .glow-text {
        text-shadow: 0 0 10px rgba(147, 51, 234, 0.3);
    }
</style>

<div class="container mx-auto p-6 sm:p-8 form-container " x-data="{
    title: '',
    description: '',
    card_image_url: '',
    background_image_url: '',
    slug: '',
    slugAvailable: true,
    slugError: '',
    addCard: false,
    card_title: '',
    card_image: '',
    course_details_link: '',
    errorMessage: '',
    successMessage: '',
    isSubmitting: false,
    wordCount: 0,
    charCount: 0,
    errors: {
        title: '',
        description: '',
        card_image_url: '',
        background_image_url: '',
        slug: '',
        card_title: '',
        card_image: '',
        course_details_link: ''
    },
    updateCounts() {
        const text = this.description.replace(/<[^>]+>/g, '').trim();
        this.wordCount = text ? text.split(/\s+/).filter(word => word.length > 0).length : 0;
        this.charCount = text.length;
        this.errors.description = this.wordCount < 10 ? 'Description must be at least 10 words long' : '';
    },
    validateField(field, value) {
        if (field === 'title') {
            this.errors.title = value.length < 3 ? 'Title must be at least 3 characters long' : '';
        }
        if (field === 'description') {
            this.updateCounts();
        }
        if (field === 'card_image_url' || field === 'background_image_url' || field === 'card_image' || field === 'course_details_link') {
            this.errors[field] = value && !/^(https?:\/\/)/i.test(value) ? 'Please enter a valid URL' : '';
        }
        if (field === 'card_title' && this.addCard) {
            this.errors.card_title = value.length < 3 ? 'Card title must be at least 3 characters long' : '';
        }
        if (field === 'slug') {
            this.errors.slug = value.length < 3 ? 'Slug must be at least 3 characters long' : '';
        }
    },
    generateSlug() {
        this.slug = this.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        this.validateField('slug', this.slug);
        this.checkSlugAvailability();
    },
    async checkSlugAvailability() {
        if (!this.slug) return;
        this.validateField('slug', this.slug);
        try {
            const response = await fetch('/check-slug', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ slug: this.slug })
            });
            const data = await response.json();
            this.slugAvailable = !data.exists;
            this.errors.slug = data.exists ? 'This slug is already taken' : this.errors.slug;
        } catch (error) {
            console.error('Error checking slug:', error);
            this.errors.slug = 'Error checking slug availability';
        }
    },
    async handleSubmit() {
        this.isSubmitting = true;
        this.errorMessage = '';
        this.successMessage = '';
        this.description = document.querySelector('#editor .ql-editor').innerHTML;
        this.validateField('title', this.title);
        this.validateField('description', this.description);
        this.validateField('card_image_url', this.card_image_url);
        this.validateField('background_image_url', this.background_image_url);
        this.validateField('slug', this.slug);
        if (this.addCard) {
            this.validateField('card_title', this.card_title);
            this.validateField('card_image', this.card_image);
            this.validateField('course_details_link', this.course_details_link);
        }

        const hasErrors = Object.values(this.errors).some(error => error !== '') || !this.slugAvailable;
        if (hasErrors) {
            this.errorMessage = 'Please fix the errors in the form before submitting.';
            this.isSubmitting = false;
            return;
        }

        const formData = {
            title: this.title,
            description: this.description,
            card_image_url: this.card_image_url,
            background_image_url: this.background_image_url,
            slug: this.slug,
            card_title: this.addCard ? this.card_title : null,
            card_image: this.addCard ? this.card_image : null,
            course_details_link: this.addCard ? this.course_details_link : null
        };

        try {
            const response = await fetch('{{ route('blog.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            const data = await response.json();
            if (response.ok && data.success) {
                this.successMessage = 'Blog created successfully!';
                this.resetForm();
            } else {
                this.errorMessage = data.message || 'Failed to create blog.';
            }
        } catch (error) {
            console.error('Submission error:', error);
            this.errorMessage = 'An error occurred while submitting the form.';
        } finally {
            this.isSubmitting = false;
        }
    },
    resetForm() {
        this.title = '';
        this.description = '';
        this.card_image_url = '';
        this.background_image_url = '';
        this.slug = '';
        this.addCard = false;
        this.card_title = '';
        this.card_image = '';
        this.course_details_link = '';
        this.slugAvailable = true;
        this.errors = {
            title: '',
            description: '',
            card_image_url: '',
            background_image_url: '',
            slug: '',
            card_title: '',
            card_image: '',
            course_details_link: ''
        };
        this.wordCount = 0;
        this.charCount = 0;
        document.querySelector('#editor .ql-editor').innerHTML = '';
    }
}" x-init="
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['link', 'image', 'video'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                ['clean']
            ]
        }
    });
    quill.on('text-change', () => {
        this.description = quill.root.innerHTML;
        this.updateCounts();
    });
    this.updateCounts();
">
    <h1 class="text-3xl font-extrabold mb-8 text-purple-900 glow-text">Create a Stunning Blog</h1>

    <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Blog Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-purple-700 mb-2">Blog Title</label>
                <input type="text" id="title" x-model="title" @input="generateSlug; validateField('title', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.title, 'valid-input': title && !errors.title}" required placeholder="Enter your blog title">
                <p x-show="errors.title" class="text-red-500 text-sm mt-2" x-text="errors.title"></p>
                <p x-show="title && !errors.title" class="text-green-500 text-sm mt-2">Looks great!</p>
            </div>

            <!-- Description with Quill -->
            <div>
                <label for="editor" class="block text-sm font-semibold text-purple-700 mb-2">Description</label>
                <div id="editor"></div>
                <div class="flex justify-between mt-3 text-sm text-gray-600">
                    <p>Words: <span class="font-semibold" x-text="wordCount"></span></p>
                    <p>Characters: <span class="font-semibold" x-text="charCount"></span></p>
                </div>
                <p x-show="errors.description" class="text-red-500 text-sm mt-2" x-text="errors.description"></p>
                <p x-show="description && !errors.description" class="text-green-500 text-sm mt-2">Content looks good!</p>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Card Image URL -->
            <div>
                <label for="card_image_url" class="block text-sm font-semibold text-purple-700 mb-2">Card Image URL</label>
                <input type="url" id="card_image_url" x-model="card_image_url" @input="validateField('card_image_url', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.card_image_url, 'valid-input': card_image_url && !errors.card_image_url}" placeholder="https://example.com/image.jpg">
                <p x-show="errors.card_image_url" class="text-red-500 text-sm mt-2" x-text="errors.card_image_url"></p>
                <p x-show="card_image_url && !errors.card_image_url" class="text-green-500 text-sm mt-2">Valid URL</p>
            </div>

            <!-- Background Image URL -->
            <div>
                <label for="background_image_url" class="block text-sm font-semibold text-purple-700 mb-2">Background Image URL</label>
                <input type="url" id="background_image_url" x-model="background_image_url" @input="validateField('background_image_url', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.background_image_url, 'valid-input': background_image_url && !errors.background_image_url}" placeholder="https://example.com/background.jpg">
                <p x-show="errors.background_image_url" class="text-red-500 text-sm mt-2" x-text="errors.background_image_url"></p>
                <p x-show="background_image_url && !errors.background_image_url" class="text-green-500 text-sm mt-2">Valid URL</p>
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-semibold text-purple-700 mb-2">Slug</label>
                <input type="text" id="slug" x-model="slug" @blur="checkSlugAvailability" @input="validateField('slug', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.slug || !slugAvailable, 'valid-input': slug && !errors.slug && slugAvailable}" required placeholder="your-blog-slug">
                <p x-show="errors.slug || !slugAvailable" class="text-red-500 text-sm mt-2" x-text="errors.slug || slugError"></p>
                <p x-show="slug && !errors.slug && slugAvailable" class="text-green-500 text-sm mt-2">Slug is available!</p>
            </div>

            <!-- Add Card Option -->
            <div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" x-model="addCard" class="mr-3 h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <span class="text-sm font-semibold text-purple-700">Add Promotional Card</span>
                </label>
            </div>
        </div>

        <!-- Card Fields -->
        <div x-show="addCard" class="lg:col-span-2 card-section grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card Title -->
            <div>
                <label for="card_title" class="block text-sm font-semibold text-purple-700 mb-2">Card Title</label>
                <input type="text" id="card_title" x-model="card_title" @input="validateField('card_title', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.card_title, 'valid-input': card_title && !errors.card_title}" placeholder="Card title">
                <p x-show="errors.card_title" class="text-red-500 text-sm mt-2" x-text="errors.card_title"></p>
                <p x-show="card_title && !errors.card_title" class="text-green-500 text-sm mt-2">Looks great!</p>
            </div>

            <!-- Card Image URL -->
            <div>
                <label for="card_image" class="block text-sm font-semibold text-purple-700 mb-2">Card Image URL</label>
                <input type="url" id="card_image" x-model="card_image" @input="validateField('card_image', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.card_image, 'valid-input': card_image && !errors.card_image}" placeholder="https://example.com/card-image.jpg">
                <p x-show="errors.card_image" class="text-red-500 text-sm mt-2" x-text="errors.card_image"></p>
                <p x-show="card_image && !errors.card_image" class="text-green-500 text-sm mt-2">Valid URL</p>
            </div>

            <!-- Course Details Link -->
            <div>
                <label for="course_details_link" class="block text-sm font-semibold text-purple-700 mb-2">Course Details Link</label>
                <input type="url" id="course_details_link" x-model="course_details_link" @input="validateField('course_details_link', $event.target.value)" class="w-full p-4 border purple-input focus:ring-2 focus:ring-purple-500 text-sm" :class="{'invalid-input': errors.course_details_link, 'valid-input': course_details_link && !errors.course_details_link}" placeholder="https://example.com/course">
                <p x-show="errors.course_details_link" class="text-red-500 text-sm mt-2" x-text="errors.course_details_link"></p>
                <p x-show="course_details_link && !errors.course_details_link" class="text-green-500 text-sm mt-2">Valid URL</p>
            </div>
        </div>

        <!-- Messages -->
        <div class="lg:col-span-2">
            <div x-show="errorMessage" class="text-red-500 text-sm font-semibold bg-red-50 p-4 rounded-lg" x-text="errorMessage"></div>
            <div x-show="successMessage" class="text-green-500 text-sm font-semibold bg-green-50 p-4 rounded-lg" x-text="successMessage"></div>
        </div>

        <!-- Submit Button -->
        <div class="lg:col-span-2">
            <button type="submit" :disabled="isSubmitting" class="w-full px-6 py-4 text-white purple-button disabled:opacity-50 disabled:cursor-not-allowed relative">
                <span x-show="!isSubmitting">Create Blog</span>
                <span x-show="isSubmitting" class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 mr-2 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Submitting...
                </span>
            </button>
        </div>
    </form>
</div>