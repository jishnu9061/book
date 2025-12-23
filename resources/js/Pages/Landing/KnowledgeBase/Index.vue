<template>
    <div>
        <Head title="Knowledge Base" />
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
                        <BookOpen class="w-4 h-4 text-yellow-300" />
                        <span>Knowledge Base</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">
                        Knowledge Base
                    </h1>

                    <!-- Subtitle -->
                    <p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto">
                        Find comprehensive guides, tutorials, and documentation to help you get the most out of our platform.
                    </p>

                    <!-- Search Section -->
                    <div class="max-w-2xl mx-auto mb-12">
                        <div class="relative group">
                            <div class="relative">
                                <Search class="w-6 h-6 absolute left-6 top-1/2 transform -translate-y-1/2 text-slate-400 group-hover:text-primary-400 transition-colors duration-300" />
                                <input
                                    type="search"
                                    v-model="form.search"
                                    class="w-full pl-16 pr-6 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 text-lg"
                                    placeholder="Search in knowledge base..."
                                    style="backdrop-filter: blur(12px);"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center justify-center gap-8 text-white/70">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Expert Guides</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Step-by-Step Tutorials</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Regular Updates</span>
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

        <!-- Knowledge Base Section -->
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
                        <BookOpen class="w-4 h-4 text-primary" />
                        Knowledge Articles
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        Browse Our Guides
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                        Discover comprehensive guides, tutorials, and documentation to help you succeed.
                    </p>
                </div>

                <!-- Knowledge Base Grid -->
                <div v-if="kb.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    <div v-for="knowledge in kb.data" :key="knowledge.id"
                         class="group bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden">

                        <!-- Background Gradient -->

                        <!-- Icon Container -->
                        <div class="relative mb-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <BookOpen class="w-8 h-8 text-white" />
                            </div>
                            <!-- Decorative Elements -->
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 animate-pulse"></div>
                            <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 animate-ping"></div>
                        </div>

                        <!-- Content -->
                        <div class="relative z-10">
                            <!-- Date Badge -->
                            <div class="inline-flex items-center gap-2 px-3 py-1 mb-4 text-xs font-semibold text-primary bg-primary/10 rounded-full">
                                <Calendar class="w-3 h-3" />
                                {{ moment(knowledge.updated_at).format('MMM DD, YYYY') }}
                            </div>

                            <!-- Title -->
                            <h3 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">
                                <Link :href="route('kb.details', knowledge.id)" class="hover:underline">
                                    {{ knowledge.title }}
                                </Link>
                            </h3>

                            <!-- Description -->
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                                {{ knowledge.details.length < 100 ? knowledge.details : knowledge.details.substring(0,100) + "..." }}
                            </p>

                            <!-- Read More Link -->
                            <div class="flex items-center text-primary-600 font-semibold group-hover:text-primary-700 transition-colors duration-300">
                                <span class="text-sm">Read More</span>
                                <ArrowRight class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" />
                            </div>
                        </div>

                    </div>
                </div>

                <!-- No Results -->
                <div v-else class="text-center py-16">
                    <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <Search class="w-12 h-12 text-slate-400" />
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">No Knowledge Base Items Found</h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-8">Try adjusting your search terms or check back later for new content.</p>
                    <button @click="reset" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-all duration-300">
                        <RefreshCw class="w-4 h-4" />
                        Clear Search
                    </button>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    <pagination class="mt-6" :links="kb.links" />
                </div>
            </div>
        </section>
        <!-- End Knowledge Base Section -->
    </div>
</template>
<script>
import Layout from '@/Shared/Landing/Layout.vue'
import Pagination from '@/Shared/Landing/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";
import moment from 'moment'
import {
    BookOpen,
    Search,
    CheckCircle,
    Calendar,
    ArrowRight,
    RefreshCw
} from 'lucide-vue-next'

export default {
    layout: Layout,
    components: {
        Head,
        Link,
        Pagination,
        BookOpen,
        Search,
        CheckCircle,
        Calendar,
        ArrowRight,
        RefreshCw
    },
    props: {
        kb: Object,
        types: Array,
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
                this.$inertia.get(this.route('kb'), pickBy(this.form), { preserveState: true })
            }, 150),
        },
    },

    methods:{
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },

    created() {
        this.moment = moment
    }
}
</script>

