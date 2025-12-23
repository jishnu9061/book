<template>
    <div>
        <Head title="FAQ" />
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
                        <HelpCircle class="w-4 h-4 text-yellow-300" />
                        <span>Frequently Asked Questions</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">
                        {{ $t('Frequently Asked Questions') }}
                    </h1>

                    <!-- Subtitle -->
                    <p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto">
                        Find answers to the most common questions about our platform, features, and services.
                    </p>

                    <!-- Search Section -->
                    <div class="max-w-2xl mx-auto mb-12">
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-blue-500/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative">
                                <Search class="w-6 h-6 absolute left-6 top-1/2 transform -translate-y-1/2 text-slate-400 group-hover:text-primary-400 transition-colors duration-300" />
                                <input
                                    type="search"
                                    v-model="form.search"
                                    class="w-full pl-16 pr-6 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 text-lg"
                                    :placeholder="$t('Search your query in the FAQ items...')"
                                    style="backdrop-filter: blur(12px);"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center justify-center gap-8 text-white/70">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Quick Answers</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Updated Daily</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Expert Verified</span>
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

        <!-- FAQ Section -->
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
                        <HelpCircle class="w-4 h-4 text-primary" />
                        FAQ Section
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        Common Questions
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                        Browse through our frequently asked questions to find quick answers to your queries.
                    </p>
                </div>

                <!-- FAQ Accordion -->
                <div class="max-w-4xl mx-auto">
                    <div class="space-y-4">
                        <div v-for="(faq, fi) in faqs.data" :key="faq.id"
                             class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-slate-700 overflow-hidden">

                            <!-- FAQ Question -->
                            <button
                                type="button"
                                @click="toggleActive(fi)"
                                @keydown.enter="toggleActive(fi)"
                                @keydown.space.prevent="toggleActive(fi)"
                                class="w-full p-6 text-left focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"
                                :class="{'bg-primary/5 dark:bg-primary/10': faq.active}"
                                :aria-expanded="faq.active"
                                :aria-controls="`faq-answer-${faq.id}`"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                            <HelpCircle class="w-5 h-5 text-white" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">
                                            {{ faq.name }}
                                        </h3>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <ChevronDown
                                            class="w-6 h-6 text-slate-500 transition-all duration-300"
                                            :class="{'rotate-180': faq.active}"
                                        />
                                    </div>
                                </div>
                            </button>

                            <!-- FAQ Answer -->
                            <div
                                :id="`faq-answer-${faq.id}`"
                                class="overflow-hidden transition-all duration-500 ease-in-out"
                                :class="faq.active ? 'max-h-screen opacity-100' : 'max-h-0 opacity-0'"
                                :style="faq.active ? 'max-height: 1000px;' : 'max-height: 0;'"
                                role="region"
                                :aria-hidden="!faq.active"
                            >
                                <div class="px-6 pb-6">
                                    <div class="pl-14">
                                        <div class="prose prose-slate dark:prose-invert max-w-none">
                                            <div class="text-slate-600 dark:text-slate-300 leading-relaxed" v-html="sanitizeHtml(faq.details)"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-16">
                    <div class="bg-gradient-to-r from-primary-600 to-blue-600 rounded-3xl p-12 text-white">
                        <h3 class="text-3xl font-bold mb-4">Still Have Questions?</h3>
                        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                            Can't find what you're looking for? Our support team is here to help you.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="/contact" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-primary-600 font-semibold rounded-2xl hover:bg-white/90 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <span>Contact Support</span>
                                <ArrowRight class="w-5 h-5" />
                            </a>
                            <a href="/kb" class="inline-flex items-center gap-3 px-8 py-4 bg-white/20 text-white font-semibold rounded-2xl hover:bg-white/30 transition-all duration-300 hover:scale-105" style="backdrop-filter: blur(12px);">
                                <span>Browse Knowledge Base</span>
                                <FileText class="w-5 h-5" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import Layout from '@/Shared/Landing/Layout.vue'
import { Head } from '@inertiajs/vue3'
import sanitizeHtml from 'sanitize-html';
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import {
    HelpCircle,
    Search,
    CheckCircle,
    ChevronDown,
    ArrowRight,
    FileText
} from 'lucide-vue-next'

export default {
    layout: Layout,
    components: {
        Head,
        HelpCircle,
        Search,
        CheckCircle,
        ChevronDown,
        ArrowRight,
        FileText
    },
    props: {
        faqs: Object,
        title: String,
        filters: Object,
    },
    data() {
        return {
            form: {
                search: this.filters.search,
            },
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function() {
                this.$inertia.get(this.route('faq'), pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },
    methods:{
        toggleActive(index){
            // Close all other FAQs
            this.faqs.data.forEach((faq, i) => {
                if (i !== index) {
                    faq.active = false;
                }
            });
            // Toggle current FAQ
            this.faqs.data[index].active = !this.faqs.data[index].active;
        },
        sanitizeHtml : sanitizeHtml,
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },

    created() {
        if(this.faqs.data.length){
            this.faqs.data[0].active = true;
        }
    }
}
</script>
