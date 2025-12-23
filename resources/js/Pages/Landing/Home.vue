<template>
    <div>
        <!-- Start Hero -->
        <Head :title="$t('Home')" />
        <!-- ====== Hero Section Start -->
        <section
            v-if="html.sections[0] && html.sections[0].enabled !== false"
            id="home"
            class="relative flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800 py-24 lg:py-32"
        >
            <!-- Animated Background -->
            <div class="absolute inset-0">
                <!-- Gradient Mesh -->
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div>

                    <!-- Animated Grid -->
                    <div class="absolute inset-0 opacity-30">
                        <div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="background-image: url('/images/patterns/grid.svg'); mask-image: radial-gradient(ellipse at center, white, transparent 70%);"></div>
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
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Content -->
                    <div class="text-center lg:text-left">
                        <!-- Trust Badge -->
                            <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white bg-opacity-10 rounded-full border border-white border-opacity-20 shadow-lg" style="backdrop-filter: blur(12px);">
                                <Star class="w-4 h-4 text-yellow-300" />
                                <span>{{ html.sections[0].badge_text || 'Trusted by 10,000+ companies' }}</span>
                            </div>

                        <!-- Main Heading -->
                        <h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl" v-html="sanitizeHtml(html.sections[0].title)"></h1>

                        <!-- Subtitle -->
                        <p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-2xl mx-auto lg:mx-0" v-html="sanitizeHtml(html.sections[0].details)"></p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-12">
                            <Link v-for="(button, bi) in html.sections[0].buttons" :key="bi" :href="button.link"
                               class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-white bg-opacity-20 border border-white border-opacity-30 rounded-2xl transition-all duration-300 hover:bg-white hover:text-slate-900 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30" style="backdrop-filter: blur(12px);">
                                <span>{{ button.text }}</span>
                                <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                            </Link>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-8 text-white/70">
                            <div v-for="(ti, i) in (html.sections[0].trust_indicators || ['Free 14-day trial','No credit card required','24/7 support'])" :key="i" class="flex items-center gap-2">
                                <CheckCircle class="w-4 h-4 text-green-400" />
                                <span class="text-sm font-medium">{{ ti }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="relative">
                        <!-- Main Image Container -->
                        <div class="relative group">
                            <!-- Glow Effect -->
                            <div class="absolute -inset-8 bg-gradient-to-r from-primary-500/20 to-blue-500/20 rounded-3xl blur-2xl group-hover:blur-3xl transition-all duration-700"></div>

                            <!-- Image Frame -->
                            <div class="relative bg-white bg-opacity-5 rounded-3xl p-1 border border-white border-opacity-10 shadow-2xl" style="backdrop-filter: blur(4px);">
                                <div class="relative overflow-hidden rounded-2xl">
                                    <img v-if="html.sections[0].image" :src="html.sections[0].image" alt="HelpDesk Dashboard" class="w-full h-auto transition-transform duration-700 group-hover:scale-105">
                                    <img v-else src="/landing/images/dashboard-helpdesk.png" alt="HelpDesk Dashboard" class="w-full h-auto transition-transform duration-700 group-hover:scale-105">
                                </div>
                            </div>
                        </div>

                        <!-- Floating Elements -->
                        <div class="absolute -top-6 -right-6 w-20 h-20 bg-gradient-to-br from-primary-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-xl animate-bounce">
                            <MessageCircle class="w-8 h-8 text-white" />
                        </div>
                        <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-xl animate-pulse">
                            <Ticket class="w-6 h-6 text-white" />
                        </div>
                        <div class="absolute top-1/2 -left-8 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg animate-ping">
                            <Star class="w-5 h-5 text-white" />
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
        <!-- Features Section -->
        <section v-if="html.sections[1] && html.sections[1].enabled !== false" class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl"></div>
            </div>

            <div class="container relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">
                        <Star class="w-4 h-4 text-primary" />
                        {{ html.sections[1].tagline }}
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        {{ html.sections[1].title }}
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto" v-html="html.sections[1].details"></p>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20" v-if="html.sections[1]">
                    <div v-for="(feature, fi) in html.sections[1].features" :key="fi"
                         class="group relative bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden">

                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <!-- Icon Container -->
                        <div class="relative mb-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <component :is="getFeatureIcon(feature.icon)" class="w-8 h-8 text-white" />
                            </div>
                            <!-- Decorative Elements -->
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 animate-pulse"></div>
                            <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 animate-ping"></div>
                        </div>

                        <!-- Content -->
                        <div class="relative z-10">
                            <h4 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">
                                {{ feature.title }}
                            </h4>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed" v-html="feature.details"></p>
                        </div>

                        <!-- Hover Border Effect -->
                        <div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-primary/20 transition-all duration-300"></div>
                    </div>
                </div>

                <!-- Stats Section (Dynamic) -->
                <div v-if="html.sections && html.sections[3] && html.sections[3].enabled !== false" class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-900 rounded-3xl p-12 shadow-2xl">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">
                            <BarChart3 class="w-4 h-4 text-primary" />
                            {{ html.sections[3].tagline }}
                        </div>
                        <h3 class="text-3xl font-bold text-white mb-4">{{ html.sections[3].title }}</h3>
                        <p class="text-slate-300 text-lg" v-html="html.sections[3].details"></p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div v-for="(stat, si) in (html.sections[3].stats || [])" :key="si" class="text-center group">
                            <div class="text-5xl font-bold text-white mb-2 group-hover:text-primary-400 transition-colors duration-300">{{ stat.value }}</div>
                            <div class="text-slate-300 font-medium">{{ stat.label }}</div>
                            <div class="w-12 h-1 bg-gradient-to-r from-primary-500 to-blue-500 mx-auto mt-2 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section (Dynamic) -->
        <section v-if="html.sections && html.sections[4] && html.sections[4].enabled !== false" class="relative py-24 lg:py-32 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-5" style="background-image: url('/images/patterns/grid.svg'); background-position: center; background-repeat: repeat;"></div>
                <div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
            </div>

            <div class="container relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">
                        <Star class="w-4 h-4 text-primary" />
                        {{ html.sections[4].tagline }}
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-white sm:text-5xl lg:text-6xl">{{ html.sections[4].title }}</h2>
                    <p class="text-xl leading-relaxed text-slate-300 max-w-3xl mx-auto" v-html="html.sections[4].details"></p>
                </div>

                <!-- Testimonials Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="(tm, ti) in (html.sections[4].testimonials || [])" :key="ti" class="group bg-white bg-opacity-5 rounded-3xl p-8 border border-white border-opacity-10 hover:border-primary hover:border-opacity-30 transition-all duration-300 hover:scale-105" style="backdrop-filter: blur(4px);">
                        <div class="flex items-center gap-1 mb-4">
                            <Star v-for="n in (tm.rating || 5)" :key="n" class="w-5 h-5 text-transparent fill-yellow-400" />
                        </div>
                        <p class="text-slate-300 mb-6 leading-relaxed">{{ tm.content }}</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ (tm.name || '').split(' ').map(p=>p[0]).join('').substring(0,2).toUpperCase() }}</span>
                            </div>
                            <div>
                                <div class="text-white font-semibold">{{ tm.name }}</div>
                                <div class="text-slate-400 text-sm">{{ tm.company }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Ticket Submission Section -->
        <section v-if="html.sections[2].enable_ticket_section" class="relative py-24 lg:py-32 bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900" id="ticketSubmit">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-primary-500/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
            </div>

            <div class="container relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-20">
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">
                        <Ticket class="w-4 h-4 text-primary" />
                        {{ (html.sections[3] && html.sections[3].badge_text) || 'Get Support' }}
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        {{ (html.sections[3] && html.sections[3].title) || $t('Create a ticket') }}
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                        {{ (html.sections[3] && html.sections[3].subtitle) || "Get help from our support team. Fill out the form below and we'll get back to you as soon as possible." }}
                    </p>
                </div>

                <!-- Multi-Step Form Container -->
                <div class="max-w-4xl mx-auto">

                    <form class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" @submit.prevent="store" enctype="multipart/form-data">
                        <!-- Form Header -->
                        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">
                                    <Ticket class="w-8 h-8 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-white">{{ (html.sections[3] && html.sections[3].submit_header) || $t('Submit Your Request') }}</h3>
                                    <p class="text-white/80 text-lg">{{ (html.sections[3] && html.sections[3].submit_subtitle) || "We're here to help you resolve any issues quickly" }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Body -->
                        <div class="p-8 space-y-8">
                            <!-- Step 1: Personal Information -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <User class="w-5 h-5 text-primary" />
                                    </div>
                                    <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Personal Information</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('First name') }} <span class="text-red-500">*</span></label>
                                        <input v-model="form.first_name"
                                               @blur="validateField('first_name', form.first_name)"
                                               @input="clearValidationError('first_name')"
                                               :class="{'border-red-500': hasFieldError('first_name')}"
                                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"
                                               :placeholder="$t('Enter your first name')" required />
                                        <p v-if="getFieldError('first_name')" class="text-sm text-red-500 flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('first_name') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Last name') }} <span class="text-red-500">*</span></label>
                                        <input v-model="form.last_name"
                                               @blur="validateField('last_name', form.last_name)"
                                               @input="clearValidationError('last_name')"
                                               :class="{'border-red-500': hasFieldError('last_name')}"
                                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"
                                               :placeholder="$t('Enter your last name')" required />
                                        <p v-if="getFieldError('last_name')" class="text-sm text-red-500 flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('last_name') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Email Address') }} <span class="text-red-500">*</span></label>
                                        <input v-model="form.email" type="email"
                                               @blur="validateField('email', form.email)"
                                               @input="clearValidationError('email')"
                                               :class="{'border-red-500': hasFieldError('email')}"
                                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"
                                               :placeholder="$t('Enter your email address')" required />
                                        <p v-if="getFieldError('email')" class="text-sm text-red-500 flex items-center gap-1">
                                            <AlertCircle class="w-4 h-4" />
                                            {{ getFieldError('email') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Ticket Details -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <Settings class="w-5 h-5 text-primary" />
                                    </div>
                                    <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Ticket Details</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Priority') }}</label>
                                        <select v-model="form.priority_id" :class="{'border-red-500': form.errors.priority_id}"
                                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200">
                                            <option :value="null">{{ $t('Select Priority') }}</option>
                                            <option v-for="s in priorities" :key="s.id" :value="s.id">{{ s.name }}</option>
                                        </select>
                                        <p v-if="form.errors.priority_id" class="text-sm text-red-500">{{ form.errors.priority_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Type') }}</label>
                                        <select v-model="form.type_id" :class="{'border-red-500': form.errors.type_id}"
                                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200">
                                            <option :value="null">{{ $t('Select a type') }}</option>
                                            <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
                                        </select>
                                        <p v-if="form.errors.type_id" class="text-sm text-red-500">{{ form.errors.type_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Department') }}</label>
                                        <select @change="getCategories()" v-model="form.department_id" :class="{'border-red-500': form.errors.department_id}"
                                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200">
                                            <option :value="null">{{ $t('Select a department') }}</option>
                                            <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                                        </select>
                                        <p v-if="form.errors.department_id" class="text-sm text-red-500">{{ form.errors.department_id }}</p>
                                    </div>

                                    <div class="space-y-2" v-if="form.department_id">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Category') }}</label>
                                        <select ref="category" @change="getSubCategories()" v-model="form.category_id" :class="{'border-red-500': form.errors.category_id}"
                                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200">
                                            <option :value="null">{{ $t('Select a category') }}</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <p v-if="form.errors.category_id" class="text-sm text-red-500">{{ form.errors.category_id }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" v-if="form.category_id">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Sub Category') }}</label>
                                        <select ref="sub_category" v-model="form.sub_category_id" :class="{'border-red-500': form.errors.sub_category_id}"
                                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200">
                                            <option :value="null">{{ $t('Select a sub category') }}</option>
                                            <option v-for="category in sub_categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                        </select>
                                        <p v-if="form.errors.sub_category_id" class="text-sm text-red-500">{{ form.errors.sub_category_id }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Subject') }} <span class="text-red-500">*</span></label>
                                    <input v-model="form.subject"
                                           @blur="validateField('subject', form.subject)"
                                           @input="clearValidationError('subject')"
                                           :class="{'border-red-500': hasFieldError('subject')}"
                                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"
                                           :placeholder="$t('Brief description of your issue')" required />
                                    <p v-if="getFieldError('subject')" class="text-sm text-red-500 flex items-center gap-1">
                                        <AlertCircle class="w-4 h-4" />
                                        {{ getFieldError('subject') }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Request Details') }} <span class="text-red-500">*</span></label>
                                    <div class="border border-slate-300 dark:border-slate-600 rounded-xl overflow-hidden" :class="{'border-red-500': hasFieldError('details')}">
                                        <RichEditor
                    v-model="form.details"
                    :placeholder="$t('Enter details...')"
                    :height="'300px'"
                />
                <div v-if="form.errors.details" class="text-red-600 text-sm mt-1">{{ form.errors.details }}</div>
                                    </div>
                                    <p v-if="getFieldError('details')" class="text-sm text-red-500 flex items-center gap-1">
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

                            <!-- File Attachments -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <FileText class="w-5 h-5 text-primary" />
                                    </div>
                                    <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Attachments</h4>
                                    <span class="text-sm text-slate-500">(Optional)</span>
                                </div>

                                <input ref="file" type="file" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf, .zip" class="hidden" multiple="multiple" @change="fileInputChange" />

                                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-12 text-center hover:border-primary transition-all duration-300 group cursor-pointer" @click="fileBrowse">
                                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-primary/20 transition-colors duration-300">
                                        <Upload class="w-8 h-8 text-primary" />
                                    </div>
                                    <h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Choose Files') }}</h5>
                                    <p class="text-slate-500 dark:text-slate-400">or drag and drop files here</p>
                                    <p class="text-sm text-slate-400 mt-2">Supports: Images, PDF, DOC, XLS, PPT, TXT, ZIP</p>
                                </div>

                                <!-- File List -->
                                <div v-if="form.files.length" class="space-y-3">
                                    <div v-for="(file, fi) in form.files" :key="fi" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600" :class="{'border-red-500': hasFieldError(`file_${fi}`)}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                                <FileText class="w-5 h-5 text-primary" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900 dark:text-white">{{ file.name }}</div>
                                                <div class="text-sm text-slate-500 dark:text-slate-400">{{ getFileSize(file.size) }}</div>
                                                <p v-if="getFieldError(`file_${fi}`)" class="text-red-500 text-sm flex items-center gap-1 mt-1">
                                                    <AlertCircle class="w-3 h-3" />
                                                    {{ getFieldError(`file_${fi}`) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button" @click="fileRemove(file, fi)" class="text-red-500 hover:text-red-700 transition-colors duration-200 p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                            <Trash2 class="w-5 h-5 text-current" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="flex items-start gap-3 p-6 bg-slate-50 dark:bg-slate-700 rounded-xl" :class="{'border border-red-500': hasFieldError('terms')}">
                                <input id="terms" type="checkbox" v-model="accept_terms"
                                       @change="clearValidationError('terms')"
                                       class="mt-1 w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary dark:border-slate-600 dark:bg-slate-800" required>
                                <div>
                                    <label for="terms" class="text-sm text-slate-700 dark:text-slate-300">
                                        {{ $t('I agree with the') }}
                                        <a :href="this.route('terms_service')" target="_blank" class="text-primary hover:underline font-medium">
                                            {{ $t('terms and conditions') }}
                                        </a>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <p v-if="getFieldError('terms')" class="text-red-500 text-sm flex items-center gap-1 mt-2">
                                        <AlertCircle class="w-4 h-4" />
                                        {{ getFieldError('terms') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Footer -->
                        <div class="bg-slate-50 dark:bg-slate-700 px-8 py-6">
                            <!-- Validation Summary -->
                            <div v-if="Object.keys(validationErrors).length > 0" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
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

                            <div class="flex justify-end">
                                <loading-button :loading="isSubmitting"
                                               class="inline-flex items-center gap-3 px-10 py-4 bg-gradient-to-r from-primary to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                               type="submit"
                                               :disabled="isSubmitting">
                                    <Send v-if="!isSubmitting" class="w-5 h-5 text-white" />
                                    <div v-else class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                    <span>{{ isSubmitting ? 'Submitting...' : ((html.sections[3] && html.sections[3].cta_submit_label) || $t('Submit Ticket')) }}</span>
                                </loading-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- End -->




    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import Layout from '@/Shared/Landing/Layout.vue'
import {
    Star,
    CheckCircle,
    ArrowRight,
    MessageCircle,
    Ticket,
    Star as StarIcon,
    User,
    Settings,
    FileText,
    Upload,
    Trash2,
    Send,
    Shield,
    Zap,
    Users,
    BarChart3,
    Clock,
    Headphones,
    Globe,
    Lock,
    Smartphone,
    Mail,
    Phone,
    HelpCircle,
    MessageSquare,
    Calendar,
    Bell,
    Search,
    Filter,
    Download,
    Share,
    Edit,
    Plus,
    Minus,
    X,
    Check,
    AlertCircle,
    Info,
    ExternalLink
} from 'lucide-vue-next'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import sanitizeHtml from "sanitize-html";

import RichEditor from '@/Shared/RichEditor.vue';

export default {
    metaInfo: { title: 'Home' },
    layout: Layout,
    components: {
        Logo,
        LoadingButton,
        SelectInput,
        TextInput,
        Head,
        Link,
        RichEditor,
        Star,
        CheckCircle,
        ArrowRight,
        MessageCircle,
        Ticket,
        StarIcon,
        User,
        Settings,
        FileText,
        Upload,
        Trash2,
        Send,
        Shield,
        Zap,
        Users,
        BarChart3,
        Clock,
        Headphones,
        Globe,
        Lock,
        Smartphone,
        Mail,
        Phone,
        HelpCircle,
        MessageSquare,
        Calendar,
        Bell,
        Search,
        Filter,
        Download,
        Share,
        Edit,
        Plus,
        Minus,
        X,
        Check,
        AlertCircle,
        Info,
        ExternalLink
    },
    props: {
        all_categories: { required: true },
        departments: Array,
        types: Array,
        title: String,
        priorities: {
            type: Array,
            default: []
        },
        page: Object,
        auth: Object,
        custom_fields: Object,
        hide_ticket_fields: Array,
        require_login: Boolean,
    },
    remember: 'form',
    data() {
        return {
            accept_terms: false,
            html: JSON.parse(this.page.html),
            validationErrors: {},
            isSubmitting: false,
            categories: [],
            sub_categories: [],
            form: this.$inertia.form({
                first_name: '',
                last_name: '',
                email: '',
                department_id: null,
                priority_id: this.priorities?.length?this.priorities[1].id:null,
                category_id: null,
                sub_category_id: null,
                type_id: null,
                subject: null,
                details: '',
                files: [],
                custom_field: {}
            }),
            sanitizeHtml : sanitizeHtml
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

            if (!this.form.subject || this.form.subject.trim() === '') {
                this.validationErrors.subject = 'Subject is required';
                isValid = false;
            } else if (this.form.subject.length < 5) {
                this.validationErrors.subject = 'Subject must be at least 5 characters';
                isValid = false;
            }

            if (!this.form.details || this.form.details.trim() === '') {
                this.validationErrors.details = 'Request details are required';
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
            alert('Ticket submitted successfully! We will get back to you soon.');
        },

        handleServerErrors(errors) {
            Object.keys(errors).forEach(key => {
                this.validationErrors[key] = errors[key];
            });
            this.scrollToFirstError();
        },

        getFeatureIcon(iconName) {
            const iconMap = {
                'ticket': Ticket,
                'user': User,
                'users': Users,
                'settings': Settings,
                'shield': Shield,
                'zap': Zap,
                'bar-chart': BarChart3,
                'clock': Clock,
                'headphones': Headphones,
                'globe': Globe,
                'lock': Lock,
                'smartphone': Smartphone,
                'mail': Mail,
                'phone': Phone,
                'help-circle': HelpCircle,
                'message-square': MessageSquare,
                'calendar': Calendar,
                'bell': Bell,
                'search': Search,
                'filter': Filter,
                'download': Download,
                'share': Share,
                'edit': Edit,
                'plus': Plus,
                'minus': Minus,
                'x': X,
                'check': Check,
                'alert-circle': AlertCircle,
                'info': Info,
                'external-link': ExternalLink,
                'star': Star,
                'file': FileText,
                'message': MessageCircle
            };
            return iconMap[iconName] || HelpCircle;
        },
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
                        this.validationErrors.details = 'Request details are required';
                    } else if (value.length < 10) {
                        this.validationErrors.details = 'Please provide more details (at least 10 characters)';
                    }
                    break;
            }
        }
    },
    created() {
        // Initialize enabled property for sections (defaults to true if not set)
        if (this.html && this.html.sections && Array.isArray(this.html.sections)) {
            this.html.sections.forEach((section, index) => {
                // Initialize enabled property for sections that need it (Hero, Features, Stats, Testimonials)
                if (index === 0 || index === 1 || index === 3 || index === 4) {
                    if (section.enabled === undefined) {
                        section.enabled = true;
                    }
                }
            });
        }

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




