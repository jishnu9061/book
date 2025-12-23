<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
        <Head title="Home" />

        <!-- Header Section -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Home Page Management') }}</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $t('Customize your landing page sections and content') }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button
                            type="button"
                            @click="previewPage"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                        >
                            <Eye class="w-4 h-4 mr-2" />
                            {{ $t('Preview') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form @submit.prevent="update">
                <div class="p-8">
                    <!-- Modern Tab Navigation -->
            <div class="border-b border-slate-200 dark:border-slate-700 mb-8">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button
                                v-for="(tab, ti) in tabs"
                                :key="ti"
                                type="button"
                                @click="activeTab(ti)"
                                :class="[
                                    tab.active
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-600',
                                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                                ]"
                            >
                                <div class="flex items-center space-x-2">
                                    <component :is="tab.icon" class="w-4 h-4" />
                                    <span>{{ tab.name }}</span>
                                </div>
                            </button>
                        </nav>
                        </div>
                    <!-- Tab Content -->
                        <div class="tab-content">
                        <!-- Settings Section -->
                        <div class="tab-pane" :class="{'hidden': !tabs[0].active}">
                            <div class="space-y-8">
                                <!-- Section Header -->
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Page Settings') }}</h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Configure page behavior and ticket submission options') }}</p>
                                </div>

                                <!-- Ticket Section Settings -->
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Ticket Section') }}</h4>

                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <text-input v-model="form.html.sections[3].badge_text" :label="$t('Badge Text')" class="w-full" />
                                            <text-input v-model="form.html.sections[3].title" :label="$t('Section Title')" class="w-full" />
                                        </div>
                                        <textarea-input v-model="form.html.sections[3].subtitle" :rows="2" :label="$t('Section Subtitle')" class="w-full" />
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <text-input v-model="form.html.sections[3].submit_header" :label="$t('Form Header Title')" class="w-full" />
                                            <text-input v-model="form.html.sections[3].cta_submit_label" :label="$t('Submit Button Label')" class="w-full" />
                                        </div>
                                        <textarea-input v-model="form.html.sections[3].submit_subtitle" :rows="2" :label="$t('Form Header Subtitle')" class="w-full" />
                                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                                            <div>
                                                <h5 class="font-medium text-slate-900 dark:text-white">{{ $t('Enable Ticket Section') }}</h5>
                                                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Show ticket submission form on the homepage') }}</p>
                                            </div>
                                            <label class="flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.html.sections[2].enable_ticket_section"
                                                    class="sr-only"
                                                />
                                        <div class="relative">
                                                    <div class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="form.html.sections[2].enable_ticket_section ? 'bg-blue-500' : ''"></div>
                                                    <div class="absolute w-5 h-5 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform duration-200" :class="form.html.sections[2].enable_ticket_section ? 'transform translate-x-6' : ''"></div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>
                        </div>
                        <!-- Hero Section -->
                        <div class="tab-pane" :class="{'hidden': !tabs[1].active}">
                            <div class="space-y-8">
                                <!-- Section Header -->
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Hero Section') }}</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Main banner section with title, description, and call-to-action buttons') }}</p>
                                        </div>
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.html.sections[0].enabled"
                                                class="sr-only"
                                            />
                                            <div class="relative">
                                                <div class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="form.html.sections[0].enabled ? 'bg-blue-500' : ''"></div>
                                                <div class="absolute w-5 h-5 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform duration-200" :class="form.html.sections[0].enabled ? 'transform translate-x-6' : ''"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Title and Details -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div>
                                        <text-input
                                            v-model="form.html.sections[0].title"
                                            class="w-full"
                                            :label="$t('Hero Title')"
                                            :placeholder="$t('Enter your main headline...')"
                                        />
                                    </div>
                                    <div>
                                        <text-input
                                            v-model="form.html.sections[0].badge_text"
                                            class="w-full"
                                            :label="$t('Hero Badge Text')"
                                            :placeholder="$t('e.g., Trusted by 10,000+ companies')"
                                        />
                                    </div>
                                    <div>
                                        <textarea-input
                                            v-model="form.html.sections[0].details"
                                            class="w-full"
                                            :label="$t('Hero Description')"
                                            :placeholder="$t('Describe your helpdesk solution...')"
                                            :rows="4"
                                        />
                                    </div>
                                </div>

                                <!-- Trust Indicators -->
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Trust Indicators') }}</h4>
                                        <button type="button" @click="addTrustIndicator()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                                            <Plus class="w-4 h-4 mr-2" />{{ $t('Add Item') }}
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div v-for="(ti, idx) in (form.html.sections[0].trust_indicators || [])" :key="idx" class="flex items-center gap-3">
                                            <text-input v-model="form.html.sections[0].trust_indicators[idx]" class="flex-1" :label="idx===0?$t('Items'):null" />
                                            <button type="button" @click="removeTrustIndicator(idx)" class="w-9 h-9 rounded-md bg-red-500 hover:bg-red-600 text-white flex items-center justify-center"><X class="w-4 h-4" /></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Call-to-Action Buttons') }}</h4>
                                        <button
                                            type="button"
                                            @click="newButton(0)"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200"
                                        >
                                            <Plus class="w-4 h-4 mr-2" />
                                            {{ $t('Add Button') }}
                                        </button>
                                    </div>

                                    <div class="space-y-4">
                                        <div
                                            v-for="(button, si) in form.html.sections[0].buttons"
                                            :key="si"
                                            class="bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg p-4 relative"
                                        >
                                            <button
                                                type="button"
                                                @click="removeButton(0, si)"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors duration-200"
                                            >
                                                <X class="w-4 h-4" />
                                            </button>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <text-input
                                                    v-model="button.text"
                                                    class="w-full"
                                                    :label="$t('Button Text')"
                                                    :placeholder="$t('e.g., Get Started')"
                                                />
                                                <text-input
                                                    v-model="button.link"
                                                    class="w-full"
                                                    :label="$t('Button Link')"
                                                    :placeholder="$t('e.g., /login')"
                                                />
                                                <div class="flex items-center pt-6">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            v-model="button.new_tab"
                                                            class="sr-only"
                                                        />
                                                <div class="relative">
                                                            <div class="w-10 h-5 bg-slate-300 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="button.new_tab ? 'bg-blue-500' : ''"></div>
                                                            <div class="absolute w-4 h-4 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform duration-200" :class="button.new_tab ? 'transform translate-x-5' : ''"></div>
                                                        </div>
                                                        <span class="ml-3 text-sm text-slate-700 dark:text-slate-300">{{ $t('Open in new tab') }}</span>
                                                    </label>
                                                </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hero Image -->
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Hero Image') }}</h4>
                                    <input ref="section0image" type="file" accept="image/*" class="hidden" @change="fileInputChange($event, 0)" />

                                    <div class="space-y-4">
                                        <button
                                            type="button"
                                            @click="fileBrowse(0)"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200"
                                        >
                                            <ImageIcon class="w-4 h-4 mr-2" />
                                            {{ form.html.sections[0].image ? $t('Change Image') : $t('Add Image') }}
                                        </button>

                                        <div v-if="form.html.sections[0].image" class="relative">
                                            <img
                                                :src="form.html.sections[0].image"
                                                alt="Hero Image"
                                                class="max-w-full h-auto max-h-64 rounded-lg border border-slate-200 dark:border-slate-600"
                                            />
                                            <button
                                                type="button"
                                                @click="fileRemove(0)"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors duration-200"
                                            >
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Features Section -->
                        <div class="tab-pane" :class="{'hidden': !tabs[2].active}">
                            <div class="space-y-8">
                                <!-- Section Header -->
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Features Section') }}</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Showcase your helpdesk features and benefits') }}</p>
                                        </div>
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.html.sections[1].enabled"
                                                class="sr-only"
                                            />
                                            <div class="relative">
                                                <div class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="form.html.sections[1].enabled ? 'bg-blue-500' : ''"></div>
                                                <div class="absolute w-5 h-5 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform duration-200" :class="form.html.sections[1].enabled ? 'transform translate-x-6' : ''"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Section Content -->
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <div>
                                        <text-input
                                            v-model="form.html.sections[1].tagline"
                                            class="w-full"
                                            :label="$t('Section Tagline')"
                                            :placeholder="$t('e.g., Process')"
                                        />
                                    </div>
                                    <div>
                                        <text-input
                                            v-model="form.html.sections[1].title"
                                            class="w-full"
                                            :label="$t('Section Title')"
                                            :placeholder="$t('e.g., How HelpDesk Works')"
                                        />
                                    </div>
                                    <div>
                                        <textarea-input
                                            v-model="form.html.sections[1].details"
                                            class="w-full"
                                            :label="$t('Section Description')"
                                            :placeholder="$t('Brief description of your process...')"
                                            :rows="3"
                                        />
                                    </div>
                                </div>

                                <!-- Features List -->
                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Feature List') }}</h4>
                                        <button
                                            type="button"
                                            @click="newFeature(1)"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200"
                                        >
                                            <Plus class="w-4 h-4 mr-2" />
                                            {{ $t('Add Feature') }}
                                        </button>
                                    </div>

                                    <div class="space-y-6">
                                        <div
                                            v-for="(feature, si) in form.html.sections[1].features"
                                            :key="si"
                                            class="bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg p-6 relative"
                                        >
                                            <button
                                                type="button"
                                                @click="removeFeature(1, si)"
                                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors duration-200"
                                            >
                                                <X class="w-4 h-4" />
                                            </button>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Icon') }}</label>
                                                    <select
                                                        v-model="feature.icon"
                                                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    >
                                                        <option value="">{{ $t('Select Icon') }}</option>
                                                        <option v-for="(icon, li) in icons" :key="li" :value="icon">{{ icon }}</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <text-input
                                                        v-model="feature.title"
                                                        class="w-full"
                                                        :label="$t('Feature Title')"
                                                        :placeholder="$t('e.g., Submit A Ticket')"
                                                    />
                                                </div>
                                                <div>
                                                    <textarea-input
                                                        v-model="feature.details"
                                                        class="w-full"
                                                        :label="$t('Feature Description')"
                                                        :placeholder="$t('Describe this feature...')"
                                                        :rows="3"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        <div class="tab-pane" :class="{'hidden': !tabs[3].active}">
                            <div class="space-y-8">
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Stats Section') }}</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Showcase key metrics of your helpdesk') }}</p>
                                        </div>
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.html.sections[3].enabled"
                                                class="sr-only"
                                            />
                                            <div class="relative">
                                                <div class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="form.html.sections[3].enabled ? 'bg-blue-500' : ''"></div>
                                                <div class="absolute w-5 h-5 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform duration-200" :class="form.html.sections[3].enabled ? 'transform translate-x-6' : ''"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <text-input v-model="form.html.sections[3].tagline" :label="$t('Section Tagline')" class="w-full" />
                                    <text-input v-model="form.html.sections[3].title" :label="$t('Section Title')" class="w-full" />
                                    <textarea-input v-model="form.html.sections[3].details" :rows="3" :label="$t('Section Description')" class="w-full" />
                                </div>

                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Stats List') }}</h4>
                                        <button type="button" @click="newStat(3)" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                                            <Plus class="w-4 h-4 mr-2" />{{ $t('Add Stat') }}
                                        </button>
                                    </div>
                                    <div class="space-y-6">
                                        <div v-for="(stat, si) in form.html.sections[3].stats" :key="si" class="bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg p-6 relative">
                                            <button type="button" @click="removeStat(3, si)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center"><X class="w-4 h-4" /></button>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <text-input v-model="stat.label" :label="$t('Label')" class="w-full" />
                                                <text-input v-model="stat.value" :label="$t('Value')" class="w-full" />
                                                <select v-model="stat.icon" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option value="">{{ $t('Select Icon') }}</option>
                                                    <option v-for="(icon, li) in icons" :key="li" :value="icon">{{ icon }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonials Section -->
                        <div class="tab-pane" :class="{'hidden': !tabs[4].active}">
                            <div class="space-y-8">
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">{{ $t('Testimonials Section') }}</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Showcase customer feedback and ratings') }}</p>
                                        </div>
                                        <label class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.html.sections[4].enabled"
                                                class="sr-only"
                                            />
                                            <div class="relative">
                                                <div class="w-12 h-6 bg-slate-300 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="form.html.sections[4].enabled ? 'bg-blue-500' : ''"></div>
                                                <div class="absolute w-5 h-5 bg-white rounded-full shadow top-0.5 left-0.5 transition-transform duration-200" :class="form.html.sections[4].enabled ? 'transform translate-x-6' : ''"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <text-input v-model="form.html.sections[4].tagline" :label="$t('Section Tagline')" class="w-full" />
                                    <text-input v-model="form.html.sections[4].title" :label="$t('Section Title')" class="w-full" />
                                    <textarea-input v-model="form.html.sections[4].details" :rows="3" :label="$t('Section Description')" class="w-full" />
                                </div>

                                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Testimonials') }}</h4>
                                        <button type="button" @click="newTestimonial(4)" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                                            <Plus class="w-4 h-4 mr-2" />{{ $t('Add Testimonial') }}
                                        </button>
                                    </div>
                                    <div class="space-y-6">
                                        <div v-for="(tm, ti) in form.html.sections[4].testimonials" :key="ti" class="bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-lg p-6 relative">
                                            <button type="button" @click="removeTestimonial(4, ti)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center"><X class="w-4 h-4" /></button>
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                                <text-input v-model="tm.name" :label="$t('Name')" class="w-full" />
                                                <text-input v-model="tm.company" :label="$t('Company')" class="w-full" />
                                                <textarea-input v-model="tm.content" :rows="2" :label="$t('Content')" class="w-full" />
                                                <text-input v-model="tm.rating" :label="$t('Rating (1-5)')" class="w-full" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                <!-- Form Footer -->
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        {{ $t('Changes are saved automatically when you submit the form') }}
                    </div>
                    <div class="flex items-center space-x-3">
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                        >
                            {{ $t('Reset') }}
                        </button>
                        <loading-button
                            :loading="form.processing"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            type="submit"
                        >
                            {{ $t('Save Changes') }}
                        </loading-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import Pagination from '@/Shared/Pagination.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TextareaInput from '@/Shared/TextareaInput.vue'
import {
    Eye,
    Plus,
    X,
    ImageIcon,
    MessageCircle,
    BarChart3,
    HelpCircle,
    Phone,
    Mail,
    Share2,
    Home,
    Settings,
    Star
} from 'lucide-vue-next'
import axios from 'axios'

export default {
    metaInfo: { title: 'Home' },
    components: {
        Icon,
        Link,
        Head,
        Pagination,
        TextInput,
        SelectInput,
        LoadingButton,
        TextareaInput,
        Eye,
        Plus,
        X,
        ImageIcon,
        MessageCircle,
        BarChart3,
        HelpCircle,
        Phone,
        Mail,
        Share2,
        Home,
        Settings,
        Star
    },
    layout: Layout,
    props: {
        page: Object,
    },
    remember: 'form',
    data() {
        return {
            icons: ['apple', 'book', 'cheveron-down', 'cheveron-right', 'location', 'office', 'shopping-cart', 'store-front', 'trash', 'service', 'category', 'status', 'ticket', 'contact', 'faq', 'chat', 'knowledge', 'home', 'clock', 'settings', 'dashboard', 'edit', 'up', 'down', 'tick', 'cross', 'close', 'file', 'users', 'all_users', 'types', 'notes', 'plus', 'dash', 'check', 'post', 'no_image', 'to_up', 'angle-up', 'gear', 'phone', 'email', 'user', 'security', 'airplay', 'compass', 'aperture', 'camera', 'palette', 'login', 'page', 'send', 'send_plan', 'user_role', 'global_setting', 'image'],
            tabs:[
                {'name': 'Settings', 'active': true, 'icon': 'Settings'},
                {'name': 'Hero Section', 'active': false, 'icon': 'Home'},
                {'name': 'Features', 'active': false, 'icon': 'Star'},
                {'name': 'Stats', 'active': false, 'icon': 'BarChart3'},
                {'name': 'Testimonials', 'active': false, 'icon': 'MessageCircle'},
            ],
            form: this.$inertia.form({
                title: 'Home',
                slug: 'home',
                is_active: this.page.is_active,
                html: JSON.parse(this.page.html),
                // html: {'sections' : [
                //         {'title': 'Make your working process easier with <span>HelpDesk</span>', 'details': 'It\'s much easier now to create, assign, manage and resolve tickets with HelpDesk. You just need to host this web application on any hosting server of your choice and use it.', buttons: [
                //                 { 'text': 'Login HelpDesk', 'link': '/login', 'new_tab': 'on' },
                //                 { 'text': 'Submit ticket', 'link': '/ticket/open', 'new_tab': 'on' },
                //             ],
                //             'image' : null
                //         },
                //         {'title': 'How It Work ?', 'details': 'You can create a ticket with logged-in on the Dashboard as an existing user or create a ticket as a new user from this front page.',
                //             'title_2': 'You can do following with this HelpDesk', 'details_2': 'Manage tickets\n' +
                //                 'Chat with customers\n' +
                //                 'Manage your marketing contacts and organizations\n' +
                //                 'Manage your blog posts', buttons: [
                //                 { 'text': 'Login HelpDesk', 'link': '/login', 'new_tab': 'on' },
                //             ],
                //             'image' : null },
                //         {'enable_ticket_section': true},
                //         {'title': 'Have Question ? Get in touch!', 'details': 'Start working with HelpDesk that can provide everything you need to generate awareness, drive traffic, connect.', buttons: [
                //                 { 'text': 'Contact us', 'link': '/contact', 'new_tab': 'on' },
                //             ]},
                //     ]}
            }),
        }
    },
    methods: {
        update() {
            this.form.put(this.route('front_pages.update', 'home'))
        },
    activeTab(index){
            for (const tab_item of this.tabs) {
                tab_item.active = false
            }
            this.tabs[index].active = true;
        },
        previewPage() {
            // Open the homepage in a new tab for preview
            window.open('/', '_blank');
        },
    resetForm() {
            // Reset form to original values
            this.form.html = JSON.parse(this.page.html);
        },
    addSection(sectionType) {
            // Add new section based on type
            const newSection = this.getSectionTemplate(sectionType);
            this.form.html.sections.push(newSection);

            // Add new tab for the section
            this.tabs.push({
                name: this.getSectionName(sectionType),
                active: false,
                icon: this.getSectionIcon(sectionType)
            });
        },
        getSectionTemplate(type) {
            const templates = {
                testimonials: {
                    title: 'What Our Customers Say',
                    tagline: 'Testimonials',
                    details: 'Read what our satisfied customers have to say about our helpdesk solution.',
                    testimonials: [
                        {
                            name: 'John Doe',
                            company: 'ABC Company',
                            content: 'This helpdesk solution has transformed our customer support.',
                            rating: 5
                        }
                    ]
                },
                statistics: {
                    title: 'Our Impact',
                    tagline: 'Statistics',
                    details: 'Key metrics that showcase our success and reliability.',
                    stats: [
                        { label: 'Tickets Resolved', value: '10,000+', icon: 'tick' },
                        { label: 'Happy Customers', value: '500+', icon: 'users' },
                        { label: 'Response Time', value: '< 2 hours', icon: 'clock' }
                    ]
                },
                faq: {
                    title: 'Frequently Asked Questions',
                    tagline: 'FAQ',
                    details: 'Find answers to common questions about our helpdesk solution.',
                    faqs: [
                        {
                            question: 'How do I submit a ticket?',
                            answer: 'You can submit a ticket by clicking the "Submit Ticket" button on our homepage.'
                        }
                    ]
                },
                contact: {
                    title: 'Get In Touch',
                    tagline: 'Contact',
                    details: 'Reach out to us for any questions or support.',
                    email: 'support@example.com',
                    phone: '+1 (555) 123-4567',
                    address: '123 Support Street, Help City, HC 12345'
                },
                newsletter: {
                    title: 'Stay Updated',
                    tagline: 'Newsletter',
                    details: 'Subscribe to our newsletter for the latest updates and tips.',
                    placeholder: 'Enter your email address'
                },
                social: {
                    title: 'Follow Us',
                    tagline: 'Social Media',
                    details: 'Connect with us on social media for updates and support.',
                    links: [
                        { platform: 'Facebook', url: 'https://facebook.com', icon: 'facebook' },
                        { platform: 'Twitter', url: 'https://twitter.com', icon: 'twitter' }
                    ]
                }
            };
            return templates[type] || {};
        },
        getSectionName(type) {
            const names = {
                testimonials: 'Testimonials',
                statistics: 'Statistics',
                faq: 'FAQ',
                contact: 'Contact',
                newsletter: 'Newsletter',
                social: 'Social Media'
            };
            return names[type] || 'New Section';
        },
        getSectionIcon(type) {
            const icons = {
                testimonials: 'MessageCircle',
                statistics: 'BarChart3',
                faq: 'HelpCircle',
                contact: 'Phone',
                newsletter: 'Mail',
                social: 'Share2'
            };
            return icons[type] || 'Settings';
        },
        newButton(index){
            if(this.form.html.sections[index] && this.form.html.sections[index].buttons){
                this.form.html.sections[index].buttons.push({'name': '', 'icon': '', 'details': ''})
            }
        },
        newFeature(index){
            if(this.form.html.sections[index] && this.form.html.sections[index].features){
                this.form.html.sections[index].features.push({'icon': '', 'title': '', 'details': ''})
            }
        },
        newStat(index){
            if(this.form.html.sections[index] && this.form.html.sections[index].stats){
                this.form.html.sections[index].stats.push({ label: '', value: '', icon: '' })
            }
        },
        removeStat(si, index){
            if(this.form.html.sections[si] && this.form.html.sections[si].stats){
                this.form.html.sections[si].stats.splice(index, 1)
            }
        },
        newTestimonial(index){
            if(this.form.html.sections[index] && this.form.html.sections[index].testimonials){
                this.form.html.sections[index].testimonials.push({ name: '', company: '', content: '', rating: 5 })
            }
        },
        removeTestimonial(si, index){
            if(this.form.html.sections[si] && this.form.html.sections[si].testimonials){
                this.form.html.sections[si].testimonials.splice(index, 1)
            }
        },
        removeButton(si, index){
            if(this.form.html.sections[si] && this.form.html.sections[si].buttons){
                this.form.html.sections[si].buttons.splice(index, 1)
            }
        },
        removeFeature(si, index){
            if(this.form.html.sections[si] && this.form.html.sections[si].features){
                this.form.html.sections[si].features.splice(index, 1)
            }
        },
        addTrustIndicator(){
            if(!Array.isArray(this.form.html.sections[0].trust_indicators)){
                this.form.html.sections[0].trust_indicators = []
            }
            this.form.html.sections[0].trust_indicators.push('')
        },
        removeTrustIndicator(index){
            if(Array.isArray(this.form.html.sections[0].trust_indicators)){
                this.form.html.sections[0].trust_indicators.splice(index, 1)
            }
        },
        fileInputChange(e, index) {
            let selectedFiles = e.target.files;
            this.form.processing = true
            if(selectedFiles.length){
                let data = new FormData()
                data.append('image', selectedFiles[0]);
                axios.post(this.route('upload.image'), data).then(( response ) => {
                    if(response.data && response.data.image){
                        this.form.html.sections[index].image = response.data.image;
                        this.form.processing = false
                    }else{
                        alert('something went wrong!')
                        this.form.processing = false
                    }
                }).catch((error) => {
                    this.form.processing = false
                    console.log(error)
                })
            }
        },
        fileRemove(index) {
            this.form.html.sections[index].image = null
        },
        getFileSize(size) {
            const i = Math.floor(Math.log(size) / Math.log(1024))
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
        },
        fileBrowse(index) {
            this.$refs['section'+index+'image'].click()
        },
    },
    created() {
        this.form.html.sections.map((section, index)=>{
            if(section.buttons && section.buttons.length){
                for (let i = 0; i < section.buttons.length; i++){
                    section.buttons[i].new_tab = !!section.buttons[i].new_tab
                }
            }
            section.enable_ticket_section = !!section.enable_ticket_section
            section.login_require_create_ticket = !!section.login_require_create_ticket
            // Initialize enabled property for sections that need it (Hero, Features, Stats, Testimonials)
            if (index === 0 || index === 1 || index === 3 || index === 4) {
                section.enabled = section.enabled !== undefined ? !!section.enabled : true
            }
            return section
        })

        // Ensure new sections exist to avoid undefined access in template
        if (!Array.isArray(this.form.html.sections)) {
            this.form.html.sections = []
        }

        // Index 3: Statistics section
        if (!this.form.html.sections[3]) {
            this.form.html.sections[3] = {
                title: 'Our Impact',
                tagline: 'Statistics',
                details: '',
                stats: []
            }
        } else {
            // Ensure required keys exist
            const s3 = this.form.html.sections[3]
            s3.title = s3.title ?? 'Our Impact'
            s3.tagline = s3.tagline ?? 'Statistics'
            s3.details = s3.details ?? ''
            s3.stats = Array.isArray(s3.stats) ? s3.stats : []
        }

        // Index 4: Testimonials section
        if (!this.form.html.sections[4]) {
            this.form.html.sections[4] = {
                title: 'What Our Customers Say',
                tagline: 'Testimonials',
                details: '',
                testimonials: []
            }
        } else {
            const s4 = this.form.html.sections[4]
            s4.title = s4.title ?? 'What Our Customers Say'
            s4.tagline = s4.tagline ?? 'Testimonials'
            s4.details = s4.details ?? ''
            s4.testimonials = Array.isArray(s4.testimonials) ? s4.testimonials : []
        }
    }
}
</script>
