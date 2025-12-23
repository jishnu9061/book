<template>
    <div>
        <Head title="Services" />
        <!-- Start Hero -->
        <section class="relative flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800 py-24 lg:py-32">
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
                <div class="text-center max-w-4xl mx-auto">
                    <!-- Trust Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white bg-opacity-10 rounded-full border border-white border-opacity-20 shadow-lg" style="backdrop-filter: blur(12px);">
                        <Star class="w-4 h-4 text-yellow-300" />
                        <span>Professional Services</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">
                        {{ $t(data.title) }}
                    </h1>

                    <!-- Subtitle -->
                    <p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto">
                        Discover our comprehensive range of professional services designed to help your business thrive and succeed in today's competitive landscape.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                        <a href="#services" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-white bg-opacity-20 border border-white border-opacity-30 rounded-2xl transition-all duration-300 hover:bg-white hover:text-slate-900 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30" style="backdrop-filter: blur(12px);">
                            <span>Explore Services</span>
                            <ArrowDown class="w-5 h-5 transition-transform group-hover:translate-y-1" />
                        </a>
                        <a href="/contact" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-2xl transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-primary-300">
                            <span>Get Started</span>
                            <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap items-center justify-center gap-8 text-white/70">
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Expert Team</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">24/7 Support</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle class="w-4 h-4 text-green-400" />
                            <span class="text-sm font-medium">Proven Results</span>
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

        <!-- ====== Services Section Start -->
        <section id="services" class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
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
                        Our Services
                    </div>
                    <h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">
                        What We Offer
                    </h2>
                    <p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                        We provide comprehensive solutions tailored to meet your business needs and drive success.
                    </p>
                </div>

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="(service, index) in page.services" :key="index"
                         class="group relative bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden">

                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <!-- Icon Container -->
                        <div class="relative mb-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <component :is="getServiceIcon(service.icon)" class="w-8 h-8 text-white" />
                            </div>
                            <!-- Decorative Elements -->
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 animate-pulse"></div>
                            <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 animate-ping"></div>
                        </div>

                        <!-- Content -->
                        <div class="relative z-10">
                            <h4 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">
                                {{ service.name }}
                            </h4>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                                {{ service.details }}
                            </p>

                            <!-- Learn More Link -->
                            <div class="flex items-center text-primary-600 font-semibold group-hover:text-primary-700 transition-colors duration-300">
                                <span class="text-sm">Learn More</span>
                                <ArrowRight class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" />
                            </div>
                        </div>

                        <!-- Hover Border Effect -->
                        <div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-primary/20 transition-all duration-300"></div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-20">
                    <div class="bg-gradient-to-r from-primary-600 to-blue-600 rounded-3xl p-12 text-white">
                        <h3 class="text-3xl font-bold mb-4">Ready to Get Started?</h3>
                        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                            Let's discuss how our services can help your business grow and succeed.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="/contact" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-primary-600 font-semibold rounded-2xl hover:bg-white/90 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                                <span>Contact Us</span>
                                <ArrowRight class="w-5 h-5" />
                            </a>
                            <a href="/" class="inline-flex items-center gap-3 px-8 py-4 bg-white/20 text-white font-semibold rounded-2xl hover:bg-white/30 transition-all duration-300 hover:scale-105" style="backdrop-filter: blur(12px);">
                                <span>Learn More</span>
                                <Info class="w-5 h-5" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ====== Services Section End -->

    </div>
</template>
<script>
import Layout from '@/Shared/Landing/Layout.vue'
import { Head } from '@inertiajs/vue3'
import {
    Star,
    CheckCircle,
    ArrowRight,
    ArrowDown,
    Info,
    Settings,
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
    ExternalLink,
    FileText,
    MessageCircle,
    Ticket,
    User
} from 'lucide-vue-next'
import sanitizeHtml from "sanitize-html";

export default {
    layout: Layout,
    components: {
        Head,
        Star,
        CheckCircle,
        ArrowRight,
        ArrowDown,
        Info,
        Settings,
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
        ExternalLink,
        FileText,
        MessageCircle,
        Ticket,
        User
    },
    props: {
        data: Object,
    },
    data() {
        return {
            page: JSON.parse(this.data.html),
        }
    },
    methods: {
        getServiceIcon(iconName) {
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
        sanitizeHtml: sanitizeHtml,
    },
}
</script>
