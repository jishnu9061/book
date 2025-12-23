<template>
    <div>
        <Head :title="$t(title)" />
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
                        <PenTool class="w-4 h-4 text-yellow-300" />
                        <span>Latest News & Insights</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">
                        {{ $t('Blogs & News') }}
                    </h1>

                    <!-- Subtitle -->
                    <p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto">
                        Stay updated with the latest news, insights, and trends in customer support and technology.
                    </p>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center justify-center gap-8 text-white/70">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Expert Insights</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Industry News</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Weekly Updates</span>
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

        <!-- Blog Section -->
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
                        <PenTool class="w-4 h-4 text-primary" />
                        Latest Articles
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        Our Latest Posts
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                        Discover insights, tips, and industry news from our expert team.
                    </p>
                </div>

                <!-- Blog Grid -->
                <div v-if="posts.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    <div v-for="post in posts.data" :key="post.id"
                         class="group bg-white dark:bg-slate-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden">

                        <!-- Image Container -->
                        <div class="relative overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9">
                                <img v-if="post.image"
                                     :src="post.image"
                                     :alt="post.title"
                                     class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110">
                                <div v-else class="w-full h-48 bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                    <PenTool class="w-16 h-16 text-white/50" />
                                </div>
                            </div>

                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <!-- Date Badge -->
                            <div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-white bg-white/20 rounded-full" style="backdrop-filter: blur(12px);">
                                <Calendar class="w-3 h-3" />
                                {{ moment(post.updated_at).format('MMM DD, YYYY') }}
                            </div>

                            <!-- Read More Button -->
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center" style="backdrop-filter: blur(12px);">
                                    <ArrowRight class="w-5 h-5 text-white" />
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-8">
                            <!-- Title -->
                            <h3 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">
                                <Link :href="route('blog.details', post.id)" class="hover:underline">
                                    {{ post.title }}
                                </Link>
                            </h3>

                            <!-- Description -->
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                                {{ post.details.length < 120 ? post.details : post.details.substring(0,120) + "..." }}
                            </p>

                            <!-- Read More Link -->
                            <div class="flex items-center justify-between">
                                <a :href="route('blog.details', post.id)" class="flex items-center text-primary-600 font-semibold group-hover:text-primary-700 transition-colors duration-300">
                                    <span class="text-sm">Read More</span>
                                    <ArrowRight class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" />
                                </a>

                                <!-- Author Info -->
                                <div class="flex items-center gap-2 text-slate-500 text-sm">
                                    <User class="w-4 h-4" />
                                    <span>Admin</span>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- No Results -->
                <div v-else class="text-center py-16">
                    <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <PenTool class="w-12 h-12 text-slate-400" />
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">No Blog Posts Found</h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-8">Check back later for new articles and insights.</p>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    <pagination class="mt-6" :links="posts.links" />
                </div>
            </div>
        </section>
        <!-- End Blog Section -->
    </div>
</template>
<script>
import Layout from '@/Shared/Landing/Layout.vue'
import Pagination from '@/Shared/Landing/Pagination.vue'
import { Link, Head } from '@inertiajs/vue3'
import moment from 'moment'
import {
    PenTool,
    CheckCircle,
    Calendar,
    ArrowRight,
    User
} from 'lucide-vue-next'

export default {
    layout: Layout,
    props: {
        posts: Object,
        title: String,
    },
    components: {
        Pagination,
        Link,
        Head,
        PenTool,
        CheckCircle,
        Calendar,
        ArrowRight,
        User
    },
    created() {
        this.moment = moment
    }
}
</script>
