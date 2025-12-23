<template>
    <div>
        <Head title="Open Ticket" />
        <!-- Start Hero -->
        <section class="relative flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800 py-24 lg:py-32">
            <!-- Animated Background -->
            <div class="absolute inset-0">
                <!-- Gradient Mesh -->
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div>

                <!-- Animated Grid -->
                <div class="absolute inset-0 opacity-30">
                    <div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="background-image: url('/images/patterns/grid.svg'); mask-image: radial-gradient(ellipse_at_center,white,transparent_70%);"></div>
                </div>

                <!-- Floating Orbs -->
                <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
                <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div>

                <!-- Geometric Shapes -->
                <div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div>
                <div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div>
                <div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div>
            </div>

            <div class="container relative z-10 py-16">
                <div class="text-center max-w-4xl mx-auto">
                    <!-- Trust Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white/10 rounded-full border border-white/20 shadow-lg" style="backdrop-filter: blur(12px);">
                        <Ticket class="w-4 h-4 text-yellow-300" />
                        <span>Submit Support Request</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">
                        Open a Ticket
                    </h1>

                    <!-- Subtitle -->
                    <p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto">
                        Need help? Submit a support ticket and our team will get back to you as soon as possible.
                    </p>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center justify-center gap-8 text-white/70">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Quick Response</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Expert Support</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">24/7 Available</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div>
                </div>
            </div>
        </section>
        <!-- End Hero -->

        <!-- Ticket Form Section -->
        <section class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-5" style="background-image: url('/images/patterns/grid.svg'); background-position: center; background-repeat: repeat;"></div>
                <div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
            </div>

            <div class="container relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">
                        <FileText class="w-4 h-4 text-primary" />
                        Support Request Form
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        Submit Your Request
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                        Fill out the form below with your details and we'll get back to you as soon as possible.
                    </p>
                </div>

                <!-- Ticket Form -->
                <div class="max-w-4xl mx-auto">
                    <form @submit.prevent="store" enctype="multipart/form-data" class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                        <!-- Form Header -->
                        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
                                    <Ticket class="w-8 h-8 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-white">Support Ticket</h3>
                                    <p class="text-white/80 text-lg">Please provide as much detail as possible</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Body -->
                        <div class="p-8">
                            <!-- Personal Information Section -->
                            <div class="mb-12">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <User class="w-5 h-5 text-primary" />
                                    </div>
                                    <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Personal Information</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('First name') }} <span class="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            v-model="form.first_name"
                                            @blur="validateField('first_name', form.first_name)"
                                            @input="clearValidationError('first_name')"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': hasFieldError('first_name')}"
                                            required
                                        />
                                        <p v-if="getFieldError('first_name')" class="text-red-500 text-sm flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('first_name') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Last name') }} <span class="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            v-model="form.last_name"
                                            @blur="validateField('last_name', form.last_name)"
                                            @input="clearValidationError('last_name')"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': hasFieldError('last_name')}"
                                            required
                                        />
                                        <p v-if="getFieldError('last_name')" class="text-red-500 text-sm flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('last_name') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Email Address') }} <span class="text-red-500">*</span></label>
                                        <input
                                            type="email"
                                            v-model="form.email"
                                            @blur="validateField('email', form.email)"
                                            @input="clearValidationError('email')"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': hasFieldError('email')}"
                                            required
                                        />
                                        <p v-if="getFieldError('email')" class="text-red-500 text-sm flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('email') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Details Section -->
                            <div class="mb-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <Settings class="w-5 h-5 text-primary" />
                                    </div>
                                    <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Ticket Details</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Priority') }} <span class="text-red-500">*</span></label>
                                        <select
                                            v-model="form.priority_id"
                                            @change="clearValidationError('priority_id')"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': hasFieldError('priority_id')}"
                                            required
                                        >
                                            <option :value="null">Select Priority</option>
                                            <option v-for="s in priorities" :key="s.id" :value="s.id">{{ s.name }}</option>
                                        </select>
                                        <p v-if="getFieldError('priority_id')" class="text-red-500 text-sm flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('priority_id') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Type') }}</label>
                                        <select
                                            v-model="form.type_id"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': form.errors.type_id}"
                                        >
                                            <option :value="null">{{ $t('Select a type') }}</option>
                                            <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
                                        </select>
                                        <p v-if="form.errors.type_id" class="text-red-500 text-sm">{{ form.errors.type_id }}</p>
                                    </div>

                                    <div v-if="!hide_ticket_fields.includes('department')" class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Department') }}</label>
                                        <select
                                            @change="getCategories()"
                                            v-model="form.department_id"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': form.errors.department_id}"
                                        >
                                            <option :value="null">{{ $t('Select a department') }}</option>
                                            <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                                        </select>
                                        <p v-if="form.errors.department_id" class="text-red-500 text-sm">{{ form.errors.department_id }}</p>
                                    </div>

                                    <div v-if="form.department_id" class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Category') }}</label>
                                        <select
                                            ref="category"
                                            @change="getSubCategories()"
                                            v-model="form.category_id"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': form.errors.category_id}"
                                        >
                                            <option :value="null">{{ $t('Select a category') }}</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <p v-if="form.errors.category_id" class="text-red-500 text-sm">{{ form.errors.category_id }}</p>
                                    </div>

                                    <div v-if="form.category_id" class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Sub Category') }}</label>
                                        <select
                                            ref="sub_category"
                                            v-model="form.sub_category_id"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': form.errors.sub_category_id}"
                                        >
                                            <option :value="null">{{ $t('Select a sub category') }}</option>
                                            <option v-for="category in sub_categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <p v-if="form.errors.sub_category_id" class="text-red-500 text-sm">{{ form.errors.sub_category_id }}</p>
                                    </div>
                                </div>

                                <!-- Subject Field -->
                                <div class="mt-6 space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Subject') }} <span class="text-red-500">*</span></label>
                                    <input
                                        type="text"
                                        v-model="form.subject"
                                        @blur="validateField('subject', form.subject)"
                                        @input="clearValidationError('subject')"
                                        class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                        :class="{'border-red-500': hasFieldError('subject')}"
                                        placeholder="Brief description of your issue"
                                        required
                                    />
                                    <p v-if="getFieldError('subject')" class="text-red-500 text-sm flex items-center gap-1">
                                        <AlertCircle class="w-4 h-4" />
                                        {{ getFieldError('subject') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Message Section -->
                            <div class="mb-12">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Request Details') }} <span class="text-red-500">*</span></label>
                                    <div class="border border-slate-200 dark:border-slate-600 rounded-xl overflow-hidden" :class="{'border-red-500': hasFieldError('details')}">
                                        <RichEditor v-model="form.details" :enableMedia="false" class="mt-1" />
                                    </div>
                                    <p v-if="getFieldError('details')" class="text-red-500 text-sm flex items-center gap-1">
                                        <AlertCircle class="w-4 h-4" />
                                        {{ getFieldError('details') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Custom Fields Section -->
                            <div v-if="custom_fields.length" class="mb-12">
                                <h4 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <div class="w-2 h-8 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
                                    Additional Information
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div v-for="ticket_field in custom_fields" :key="ticket_field.id" class="space-y-2">
                                        <label :for="!['checkbox'].includes(ticket_field.type) ? 'ticket_field_'+ticket_field.id : null" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            {{ ticket_field.label }}
                                            <span v-if="ticket_field.required" class="text-red-500">*</span>
                                            <span v-else class="text-slate-500 text-xs">(optional)</span>
                                        </label>

                                        <input
                                            v-if="['text', 'email', 'number'].includes(ticket_field.type)"
                                            v-model="form.custom_field[ticket_field.name]"
                                            @blur="validateField(`custom_field_${ticket_field.name}`, form.custom_field[ticket_field.name])"
                                            @input="clearValidationError(`custom_field_${ticket_field.name}`)"
                                            :type="ticket_field.type"
                                            :id="'ticket_field_'+ticket_field.id"
                                            :placeholder="ticket_field.placeholder"
                                            :required="ticket_field.required"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"
                                            :class="{'border-red-500': hasFieldError(`custom_field_${ticket_field.name}`)}"
                                        />

                                        <textarea
                                            v-if="ticket_field.type === 'textarea'"
                                            :id="'ticket_field_'+ticket_field.id"
                                            rows="3"
                                            v-model="form.custom_field[ticket_field.name]"
                                            @blur="validateField(`custom_field_${ticket_field.name}`, form.custom_field[ticket_field.name])"
                                            @input="clearValidationError(`custom_field_${ticket_field.name}`)"
                                            class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 resize-none"
                                            :class="{'border-red-500': hasFieldError(`custom_field_${ticket_field.name}`)}"
                                            :placeholder="ticket_field.placeholder"
                                        ></textarea>

                                        <p v-if="getFieldError(`custom_field_${ticket_field.name}`)" class="text-red-500 text-sm flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError(`custom_field_${ticket_field.name}`) }}
                                        </p>
                                        <p v-else-if="ticket_field.hint" class="text-slate-500 dark:text-slate-400 text-sm">{{ ticket_field.hint }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- File Attachments Section -->
                            <div class="mb-12">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <FileText class="w-5 h-5 text-primary" />
                                    </div>
                                    <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Attachments</h4>
                                    <span class="text-sm text-slate-500">(Optional)</span>
                                </div>

                                <input ref="file" type="file" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf, .zip" class="hidden" multiple="multiple" @change="fileInputChange" />

                                <!-- File Upload Area -->
                                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-8 text-center hover:border-primary-400 transition-colors duration-300 cursor-pointer" @click="fileBrowse">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-2xl flex items-center justify-center">
                                            <Upload class="w-8 h-8 text-primary-600" />
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Attach files') }}</h5>
                                            <p class="text-slate-600 dark:text-slate-400">Click to browse or drag and drop files here</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">Supports: Images, PDF, DOC, XLS, PPT, TXT, ZIP (Max 10MB each)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- File List -->
                                <div v-if="form.files.length" class="mt-6 space-y-3">
                                    <div v-for="(file, fi) in form.files" :key="fi" class="flex items-center justify-between bg-slate-50 dark:bg-slate-700 rounded-xl p-4" :class="{'border border-red-500': hasFieldError(`file_${fi}`)}">
                                        <div class="flex items-center gap-3">
                                            <File class="w-5 h-5 text-slate-500" />
                                            <div>
                                                <p class="font-medium text-slate-900 dark:text-white">{{ file.name }}</p>
                                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ getFileSize(file.size) }}</p>
                                                <p v-if="getFieldError(`file_${fi}`)" class="text-red-500 text-sm flex items-center gap-1 mt-1">
                                                    <AlertCircle class="w-3 h-3" />
                                                    {{ getFieldError(`file_${fi}`) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" @click="fileRemove(file, fi)" class="text-red-500 hover:text-red-700 transition-colors duration-300">
                                            <X class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-8">
                                <div class="flex items-start gap-4 p-6 bg-slate-50 dark:bg-slate-700 rounded-2xl" :class="{'border border-red-500': hasFieldError('terms')}">
                                    <div class="flex items-center h-5 mt-1">
                                        <input
                                            id="terms"
                                            type="checkbox"
                                            v-model="accept_terms"
                                            @change="clearValidationError('terms')"
                                            class="w-5 h-5 text-primary-600 bg-slate-100 border-slate-300 rounded focus:ring-primary-500 focus:ring-2"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label for="terms" class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ $t('I agree to the') }}
                                            <a :href="this.route('terms_service')" target="_blank" class="text-primary-600 hover:text-primary-700 underline font-semibold">
                                                {{ $t('terms and conditions') }}
                                            </a>
                                        </label>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">By submitting this ticket, you agree to our terms of service and privacy policy.</p>
                                        <p v-if="getFieldError('terms')" class="text-red-500 text-sm flex items-center gap-1 mt-2">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('terms') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-center">
                                <loading-button
                                    :loading="isSubmitting"
                                    class="group relative inline-flex items-center gap-3 px-12 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-primary-300 text-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                    type="submit"
                                    :disabled="isSubmitting"
                                >
                                    <Send v-if="!isSubmitting" class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" />
                                    <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    <span>{{ isSubmitting ? 'Submitting...' : $t('Submit Ticket') }}</span>
                                </loading-button>
                            </div>

                            <!-- Validation Summary -->
                            <div v-if="Object.keys(validationErrors).length > 0" class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                                <div class="flex items-start gap-3">
                                    <AlertCircle class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" />
                                    <div>
                                        <h4 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</h4>
                                        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                            <li v-for="(error, field) in validationErrors" :key="field" class="flex items-center gap-2">
                                                <span class="w-1 h-1 bg-red-500 rounded-full"></span>
                                                {{ error }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Landing/Layout.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

import RichEditor from '@/Shared/RichEditor.vue';

import {
    Ticket,
    CheckCircle,
    FileText,
    Upload,
    File,
    X,
    Send,
    AlertCircle, User, Settings
} from 'lucide-vue-next'

export default {
    metaInfo: { title: 'Open Ticket' },
    layout: Layout,
    components: {
        Settings,
        User,
        LoadingButton,
        Head,
        Link,
        RichEditor,
        Ticket,
        CheckCircle,
        FileText,
        Upload,
        File,
        X,
        Send,
        AlertCircle
    },
    props: {
        all_categories: Array,
        departments: Array,
        types: Array,
        priorities: {
            type: Array,
            default: []
        },
        title: String,
        hide_ticket_fields: Array,
        auth: Object,
        custom_fields: Object,
    },
    remember: 'form',
    data() {
        return {
            accept_terms: false,
            categories: [],
            sub_categories: [],
            validationErrors: {},
            isSubmitting: false,
            form: this.$inertia.form({
                first_name: '',
                last_name: '',
                email: '',
                priority_id: this.priorities?.length?this.priorities[1].id:null,
                department_id: null,
                category_id: null,
                sub_category_id: null,
                type_id: null,
                subject: null,
                details: '',
                files: [],
                custom_field: {}
            }),
        }
    },

    methods: {
        // Validation Methods
        validateForm() {
            this.validationErrors = {};
            let isValid = true;

            // Required field validation
            if (!this.form.first_name || this.form.first_name.trim() === '') {
                this.validationErrors.first_name = 'First name is required';
                isValid = false;
            } else if (this.form.first_name.length < 2) {
                this.validationErrors.first_name = 'First name must be at least 2 characters';
                isValid = false;
            }

            if (!this.form.last_name || this.form.last_name.trim() === '') {
                this.validationErrors.last_name = 'Last name is required';
                isValid = false;
            } else if (this.form.last_name.length < 2) {
                this.validationErrors.last_name = 'Last name must be at least 2 characters';
                isValid = false;
            }

            if (!this.form.email || this.form.email.trim() === '') {
                this.validationErrors.email = 'Email address is required';
                isValid = false;
            } else if (!this.isValidEmail(this.form.email)) {
                this.validationErrors.email = 'Please enter a valid email address';
                isValid = false;
            }

            if (!this.form.priority_id) {
                this.validationErrors.priority_id = 'Priority is required';
                isValid = false;
            }

            if (!this.form.subject || this.form.subject.trim() === '') {
                this.validationErrors.subject = 'Subject is required';
                isValid = false;
            } else if (this.form.subject.length < 5) {
                this.validationErrors.subject = 'Subject must be at least 5 characters';
                isValid = false;
            }

            if (!this.form.details || this.form.details.trim() === '') {
                this.validationErrors.details = 'Ticket details are required';
                isValid = false;
            } else if (this.form.details.length < 10) {
                this.validationErrors.details = 'Please provide more details (at least 10 characters)';
                isValid = false;
            }

            // Custom fields validation
            this.custom_fields.forEach(field => {
                if (field.required && (!this.form.custom_field[field.name] || this.form.custom_field[field.name].trim() === '')) {
                    this.validationErrors[`custom_field_${field.name}`] = `${field.label} is required`;
                    isValid = false;
                }
            });

            // File validation
            if (this.form.files.length > 0) {
                this.form.files.forEach((file, index) => {
                    if (file.size > 10 * 1024 * 1024) { // 10MB limit
                        this.validationErrors[`file_${index}`] = `File "${file.name}" is too large. Maximum size is 10MB`;
                        isValid = false;
                    }
                });
            }

            // Terms validation
            if (!this.accept_terms) {
                this.validationErrors.terms = 'You must accept the terms and conditions';
                isValid = false;
            }

            return isValid;
        },

        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        clearValidationError(field) {
            if (this.validationErrors[field]) {
                delete this.validationErrors[field];
            }
        },

        getFieldError(field) {
            return this.validationErrors[field] || this.form.errors[field];
        },

        hasFieldError(field) {
            return !!(this.validationErrors[field] || this.form.errors[field]);
        },

        // Form Methods
        getCategories(){
            this.categories = this.all_categories.filter(cat=>cat.department_id === this.form.department_id)
            this.form.category_id = null;
            this.form.sub_category_id = null;
            this.sub_categories = [];
            if (this.$refs.category) {
                this.$refs.category.selected = null;
            }
            if (this.$refs.sub_category) {
                this.$refs.sub_category.selected = null;
            }
            this.clearValidationError('category_id');
            this.clearValidationError('sub_category_id');
        },

        getSubCategories(){
            this.sub_categories = this.all_categories.filter(cat=>cat.parent_id === this.form.category_id)
            this.form.sub_category_id = null;
            if (this.$refs.sub_category) {
                this.$refs.sub_category.selected = null;
            }
            this.clearValidationError('sub_category_id');
        },

        uploadFiles() {
            this.form.files = this.$refs.files.files
        },

        store() {
            // Clear previous validation errors
            this.validationErrors = {};

            // Validate form
            if (!this.validateForm()) {
                // Scroll to first error
                this.scrollToFirstError();
                return;
            }

            this.isSubmitting = true;
            this.form.post(this.route('ticket_store'),{
                onSuccess: () => {
                    this.form.reset();
                    this.accept_terms = false;
                    this.validationErrors = {};
                    this.isSubmitting = false;
                    // Show success message
                    this.showSuccessMessage();
                },
                onError: (errors) => {
                    this.isSubmitting = false;
                    // Handle server-side validation errors
                    this.handleServerErrors(errors);
                },
                onFinish: () => {
                    this.isSubmitting = false;
                }
            })
        },

        handleServerErrors(errors) {
            // Convert server errors to our validation format
            Object.keys(errors).forEach(key => {
                this.validationErrors[key] = errors[key];
            });
            this.scrollToFirstError();
        },

        scrollToFirstError() {
            this.$nextTick(() => {
                const firstError = document.querySelector('.border-red-500, .text-red-500');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstError.focus();
                }
            });
        },

        showSuccessMessage() {
            // You can implement a toast notification here
            alert('Ticket submitted successfully! We will get back to you soon.');
        },

        fileInputChange(e) {
            let selectedFiles = e.target.files;
            let validFiles = [];
            let hasErrors = false;

            for (let i = 0; i < selectedFiles.length; i++) {
                const file = selectedFiles[i];

                // Validate file size
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    this.validationErrors[`file_${i}`] = `File "${file.name}" is too large. Maximum size is 10MB`;
                    hasErrors = true;
                    continue;
                }

                // Validate file type
                const allowedTypes = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain',
                    'application/zip'
                ];

                if (!allowedTypes.includes(file.type)) {
                    this.validationErrors[`file_${i}`] = `File "${file.name}" type is not supported`;
                    hasErrors = true;
                    continue;
                }

                validFiles.push(file);
            }

            if (hasErrors) {
                this.scrollToFirstError();
            }

            this.form.files = [...this.form.files, ...validFiles];
        },

        fileRemove(image, index) {
            this.form.files.splice(index, 1);
            // Clear any validation errors for this file
            delete this.validationErrors[`file_${index}`];
        },

        getFileSize(size) {
            const i = Math.floor(Math.log(size) / Math.log(1024))
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
        },

        fileBrowse() {
            this.$refs.file.click()
        },

        // Real-time validation
        validateField(field, value) {
            this.clearValidationError(field);

            switch (field) {
                case 'first_name':
                    if (!value || value.trim() === '') {
                        this.validationErrors.first_name = 'First name is required';
                    } else if (value.length < 2) {
                        this.validationErrors.first_name = 'First name must be at least 2 characters';
                    }
                    break;
                case 'last_name':
                    if (!value || value.trim() === '') {
                        this.validationErrors.last_name = 'Last name is required';
                    } else if (value.length < 2) {
                        this.validationErrors.last_name = 'Last name must be at least 2 characters';
                    }
                    break;
                case 'email':
                    if (!value || value.trim() === '') {
                        this.validationErrors.email = 'Email address is required';
                    } else if (!this.isValidEmail(value)) {
                        this.validationErrors.email = 'Please enter a valid email address';
                    }
                    break;
                case 'subject':
                    if (!value || value.trim() === '') {
                        this.validationErrors.subject = 'Subject is required';
                    } else if (value.length < 5) {
                        this.validationErrors.subject = 'Subject must be at least 5 characters';
                    }
                    break;
                case 'details':
                    if (!value || value.trim() === '') {
                        this.validationErrors.details = 'Ticket details are required';
                    } else if (value.length < 10) {
                        this.validationErrors.details = 'Please provide more details (at least 10 characters)';
                    }
                    break;
            }
        }
    },

    created() {
        if(this.auth && this.auth.user){
            this.form.first_name = this.auth.user.first_name;
            this.form.last_name = this.auth.user.last_name;
            this.form.email = this.auth.user.email;
        }
        if(this.custom_fields && Array.isArray(this.custom_fields)) {
            for (let cf_c = 0; cf_c < this.custom_fields.length; cf_c++) {
                if(this.custom_fields[cf_c.name]){
                    this.form.custom_field[this.custom_fields[cf_c.name]] = '';
                }
            }
        }
    }
}
</script>




