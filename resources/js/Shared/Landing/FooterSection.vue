
<template>
    <!-- ====== Footer Section Start -->
    <footer class="relative overflow-hidden">
        <!-- Advanced Background with Multiple Layers -->
        <div class="absolute inset-0">
            <!-- Primary Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>

            <!-- Animated Mesh Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary-600/15 via-transparent to-blue-600/15 animate-pulse"></div>

            <!-- Dynamic Grid Pattern -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat animate-pulse" style="background-image: url('/images/patterns/grid.svg'); mask-image: radial-gradient(ellipse_at_center,white,transparent_70%); animation-duration: 4s;"></div>
            </div>

            <!-- Floating Geometric Shapes -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-br from-primary-500/20 to-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/3 right-1/4 w-80 h-80 bg-gradient-to-br from-purple-500/15 to-pink-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-gradient-to-br from-cyan-500/20 to-blue-500/15 rounded-full blur-3xl animate-pulse delay-500"></div>

            <!-- Animated Particles -->
            <div class="absolute top-20 left-20 w-2 h-2 bg-white/40 rounded-full animate-ping"></div>
            <div class="absolute top-40 right-32 w-3 h-3 bg-primary-300/60 rounded-full animate-ping delay-1000"></div>
            <div class="absolute bottom-32 left-16 w-1 h-1 bg-blue-300/80 rounded-full animate-ping delay-500"></div>
            <div class="absolute top-60 right-1/4 w-2 h-2 bg-purple-300/50 rounded-full animate-ping delay-700"></div>
        </div>

        <!-- Main Footer Content -->
        <div class="relative z-10">


            <!-- Footer Links Section -->
            <div class="py-16 lg:py-20">
                <div class="container">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                        <!-- Company Info -->
                        <div class="lg:col-span-1">
                            <div class="mb-8">
                                <Link :href="route('home')" class="inline-block mb-8 group">
                                    <Logo class="help-desk-logo group-hover:scale-105 transition-transform duration-300" />
                                </Link>
                                <p class="text-slate-300 leading-relaxed mb-8 text-lg" v-html="$t(footer_text)"></p>

                                <!-- Social Links with Enhanced Design -->
                                <div class="space-y-4">
                                    <h5 class="text-white font-semibold mb-4">Follow Us</h5>
                                    <div class="flex items-center gap-3">
                                        <a href="#" class="group w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center hover:from-blue-700 hover:to-blue-800 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                                            <Facebook class="w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" />
                                        </a>
                                        <a href="#" class="group w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl flex items-center justify-center hover:from-sky-600 hover:to-sky-700 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                                            <Twitter class="w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" />
                                        </a>
                                        <a href="#" class="group w-12 h-12 bg-gradient-to-br from-blue-700 to-blue-800 rounded-2xl flex items-center justify-center hover:from-blue-800 hover:to-blue-900 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                                            <Linkedin class="w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" />
                                        </a>
                                        <a href="#" class="group w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center hover:from-pink-600 hover:to-pink-700 transition-all duration-300 hover:scale-110 hover:shadow-xl">
                                            <Instagram class="w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company Links -->
                        <div>
                            <h4 class="text-2xl font-bold text-white mb-8 flex items-center gap-3">
                                <div class="w-2 h-8 bg-gradient-to-b from-primary-500 to-primary-600 rounded-full"></div>
                                {{ $t('Company') }}
                            </h4>
                            <ul class="space-y-4">
                                <li v-if="!!this.enable_option && this.enable_option.show_login">
                                    <a :href="route('login')" class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2">
                                        <div class="w-1 h-1 bg-primary-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                        <span class="text-lg">{{ $t('Login') }}</span>
                                        <ArrowRight class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" />
                                    </a>
                                </li>
                                <li v-if="!!this.enable_option && this.enable_option.show_login">
                                    <a :href="route('register')" class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2">
                                        <div class="w-1 h-1 bg-primary-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                        <span class="text-lg">{{ $t('Register') }}</span>
                                        <ArrowRight class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" />
                                    </a>
                                </li>
                                <li v-if="!!this.enable_option && this.enable_option.blog">
                                    <a :href="route('blog')" class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2">
                                        <div class="w-1 h-1 bg-primary-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                        <span class="text-lg">{{ $t('Blog') }}</span>
                                        <ArrowRight class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" />
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Useful Links -->
                        <div>
                            <h4 class="text-2xl font-bold text-white mb-8 flex items-center gap-3">
                                <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                                {{ $t('Useful Links') }}
                            </h4>
                            <ul class="space-y-4">
                                <li v-if="!!this.enable_option && this.enable_option.terms_of_services">
                                    <a :href="route('terms_service')" class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2">
                                        <div class="w-1 h-1 bg-blue-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                        <span class="text-lg">{{ $t('Terms of Services') }}</span>
                                        <ArrowRight class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" />
                                    </a>
                                </li>
                                <li v-if="!!this.enable_option && this.enable_option.privacy_policy">
                                    <a :href="route('privacy')" class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2">
                                        <div class="w-1 h-1 bg-blue-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                        <span class="text-lg">{{ $t('Privacy Policy') }}</span>
                                        <ArrowRight class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" />
                                    </a>
                                </li>
                                <li v-if="!!this.enable_option && this.enable_option.kb">
                                    <a :href="route('kb')" class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2">
                                        <div class="w-1 h-1 bg-blue-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                        <span class="text-lg">{{ $t('Knowledge Base') }}</span>
                                        <ArrowRight class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" />
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Newsletter with Premium Design -->
                        <div v-if="!!this.enable_option && this.enable_option.newsletter">
                            <h4 class="text-2xl font-bold text-white mb-8 flex items-center gap-3">
                                <div class="w-2 h-8 bg-gradient-to-b from-green-500 to-green-600 rounded-full"></div>
                                {{ $t('Newsletter') }}
                            </h4>
                            <p class="text-slate-300 mb-8 text-lg">{{ $t('Join our newsletter service.') }}</p>

                            <form @submit.prevent="subscribe" class="space-y-6">
                                <div class="relative group">
                                    <div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-blue-500/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    <div class="relative">
                                        <Mail class="w-6 h-6 absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 group-hover:text-primary-400 transition-colors duration-300" />
                                        <input
                                            type="email"
                                            v-model="form.email"
                                            class="w-full pl-14 pr-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 text-lg"
                                            placeholder="Enter your email address"
                                            required
                                            style="backdrop-filter: blur(20px);"
                                        />
                                    </div>
                                </div>

                                <loading-button
                                    :loading="form.processing"
                                    class="w-full group relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold rounded-2xl hover:from-green-700 hover:to-green-800 transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-green-300 text-lg"
                                    type="submit"
                                >
                                    <Send class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" />
                                    <span>{{ $t('Subscribe Now') }}</span>
                                </loading-button>

                                <p class="text-xs text-slate-400 text-center">
                                    By subscribing, you agree to our privacy policy and terms of service.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom with Enhanced Design -->
            <div class="border-t border-white/10 bg-gradient-to-r from-slate-900/50 to-slate-800/50" style="backdrop-filter: blur(20px);">
                <div class="container py-8">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                        <!-- Copyright -->
                        <div class="text-center lg:text-left">
                            <p class="text-slate-400 text-lg" v-html="$t(footer_content.copyright)"></p>
                        </div>

                        <!-- Additional Links with Icons -->
                        <div class="flex items-center gap-8">
                            <a href="#" class="group text-slate-400 hover:text-white transition-all duration-300 flex items-center gap-2">
                                <Headphones class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                                <span class="text-sm font-medium">Support</span>
                            </a>
                            <a href="#" class="group text-slate-400 hover:text-white transition-all duration-300 flex items-center gap-2">
                                <FileText class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                                <span class="text-sm font-medium">Documentation</span>
                            </a>
                            <a href="#" class="group text-slate-400 hover:text-white transition-all duration-300 flex items-center gap-2">
                                <BarChart3 class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                                <span class="text-sm font-medium">Status</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import Logo from '@/Shared/Logo.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { Link } from '@inertiajs/vue3'
import {
    Star,
    ArrowRight,
    Mail,
    Send,
    Facebook,
    Twitter,
    Linkedin,
    Instagram,
    ExternalLink,
    Headphones,
    FileText,
    BarChart3
} from 'lucide-vue-next'

export default {
    components: {
        Logo,
        LoadingButton,
        Link,
        Star,
        ArrowRight,
        Mail,
        Send,
        Facebook,
        Twitter,
        Linkedin,
        Instagram,
        ExternalLink,
        Headphones,
        FileText,
        BarChart3
    },
    props: {
        footer: Object,
    },
    data() {
        return {
            footer_content: this.footer ? JSON.parse(this.footer.html) : [],
            footer_text: this.footer ? JSON.parse(this.footer.html).text: 'Start working with HelpDesk that can provide everything you need to generate awareness, drive traffic, connect.',
            form: this.$inertia.form({
                email: '',
            }),
            enable_option: {},
        }
    },
    methods: {
        subscribe() {
            this.form.post(this.route('subscribe.news'))
            this.form.email = ''
        },
    },
    created() {
        if(this.$page.props.enable_options){
            let options = JSON.parse(this.$page.props.enable_options.value)
            options.forEach(option=>{
                this.enable_option[option.slug] = !!option.value
            })
        }
    }
}
</script>
