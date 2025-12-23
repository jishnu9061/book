<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300" :class="current_mode" :dir="$page.props.dir">
    <!-- Dropdown Portal -->
    <div id="dropdown" />

    <!-- Main Layout Container -->
    <div class="flex h-screen overflow-hidden">
      <!-- Mobile Menu Overlay -->
      <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" @click="mobileMenuOpen = false">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
      </div>

      <!-- Sidebar -->
      <div class="hidden lg:flex lg:flex-col lg:w-72 lg:shrink-0">
        <!-- Sidebar Header -->
        <div class="relative border-b border-primary-500/20">
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-50" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;%3E%3Ccircle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;2&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

          <div class="relative flex items-center justify-between px-6 py-5">
            <Link href="/" class="group flex items-center space-x-3">
              <div class="h-10 bg-white/20 flex items-center justify-center transition-all duration-300">
                <Logo class="h-5 text-white group-hover:scale-110 transition-transform duration-300" />
              </div>
            </Link>
            <button
              @click="mobileMenuOpen = false"
              class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors duration-200"
            >
              <X class="w-5 h-5 text-white/80" />
            </button>
          </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="flex-1 overflow-y-auto bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700">
          <main-menu />
        </div>
      </div>

      <!-- Mobile Sidebar -->
      <div v-if="mobileMenuOpen" class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 lg:hidden">
        <!-- Mobile Header -->
        <div class="relative border-b border-primary-500/20">
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-50" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;%3E%3Ccircle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;2&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

          <div class="relative flex items-center justify-between px-6 py-5">
            <Link href="/" class="group flex items-center space-x-3">
              <div class="h-10 flex items-center justify-center group-hover:bg-white/30 transition-all duration-300">
                <Logo class="h-6 text-white" />
              </div>
            </Link>
            <button
              @click="mobileMenuOpen = false"
              class="p-2 rounded-lg hover:bg-gray-200 transition-colors duration-200"
            >
              <X class="w-5 h-5 text-gray-900" />
            </button>
          </div>
        </div>
        <div class="overflow-y-auto h-full">
          <main-menu />
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm">
          <div class="flex items-center justify-between px-4 py-3 lg:px-6">
            <!-- Left Section -->
            <div class="flex items-center gap-4">
              <!-- Mobile Menu Button -->
              <button
                @click="mobileMenuOpen = true"
                class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"
              >
                <Menu class="w-5 h-5 text-slate-600 dark:text-slate-400" />
              </button>

              <!-- Welcome Message -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t(title || '') }}</h1>
                        <nav class="flex items-center space-x-2 text-sm mt-1">
                            <Link :href="route('dashboard')" class="text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                                <Home class="w-4 h-4" />
                            </Link>
                            <ChevronRight class="w-4 h-4 text-slate-400" />
                            <Link v-if="edit_route" :href="route(edit_route)" class="text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 capitalize">
                                {{ edit_route }}
                            </Link>
                            <ChevronRight v-if="edit_route" class="w-4 h-4 text-slate-400" />
                            <span class="text-slate-900 dark:text-white font-medium">{{ $t(title || '') }}</span>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-3">
              <!-- Notifications -->
              <NotificationBell />

              <!-- Language Selector -->
              <dropdown class="language_menu_wrapper" placement="bottom-end">
                <template #default>
                  <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">
                    <icon class="w-5 h-5" :name="selected_language.code" />
                    <span class="hidden sm:block text-sm font-medium text-slate-700 dark:text-slate-300">{{ selected_language.name }}</span>
                    <ChevronDown class="w-4 h-4 text-slate-500" />
                  </button>
                </template>
                <template #dropdown>
                  <div class="py-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 min-w-[160px]">
                    <div v-for="language in languages_except_selected" :key="language.code"
                         class="flex items-center gap-3 px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-colors duration-200"
                         @click="updateLanguage(language.code)">
                      <icon class="w-5 h-5" :name="language.code" />
                      <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ language.name }}</span>
                    </div>
                  </div>
                </template>
              </dropdown>

              <!-- Theme Toggle -->
              <button
                @click="switchMode"
                class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"
                :title="`Switch to ${current_mode === 'light' ? 'dark' : 'light'} mode`"
                :aria-label="`Switch to ${current_mode === 'light' ? 'dark' : 'light'} mode`"
              >
                <Sun v-if="current_mode === 'light'" class="w-5 h-5 text-slate-600 dark:text-slate-400" />
                <Moon v-else class="w-5 h-5 text-slate-600 dark:text-slate-400" />
              </button>

              <!-- User Menu -->
              <dropdown class="select_user" placement="bottom-end">
                <template #default>
                  <button class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">
                    <div class="relative">
                      <img v-if="$page.props.auth?.user?.photo"
                           class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700"
                           :alt="$page.props.auth?.user?.first_name || 'User'"
                           :src="$page.props.auth?.user?.photo" />
                      <div v-else class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                        <User class="w-4 h-4 text-white" />
                      </div>
                      <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-slate-800"></div>
                    </div>
                    <div class="hidden sm:block text-left">
                      <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $page.props.auth?.user?.first_name || 'User' }} {{ $page.props.auth?.user?.last_name || '' }}</p>
                      <p class="text-xs text-slate-500 dark:text-slate-400">{{ $page.props.auth?.user?.email || 'user@example.com' }}</p>
                    </div>
                    <ChevronDown class="w-4 h-4 text-slate-500" />
                  </button>
                </template>
                <template #dropdown>
                  <div class="py-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 min-w-[240px]">
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                      <div class="flex items-center gap-3">
                        <img v-if="$page.props.auth?.user?.photo"
                             class="w-12 h-12 rounded-full object-cover"
                             :alt="$page.props.auth?.user?.first_name || 'User'"
                             :src="$page.props.auth?.user?.photo" />
                        <div v-else class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                          <User class="w-6 h-6 text-white" />
                        </div>
                        <div>
                          <p class="font-semibold text-slate-900 dark:text-white">{{ $page.props.auth?.user?.first_name || 'User' }} {{ $page.props.auth?.user?.last_name || '' }}</p>
                          <p class="text-sm text-slate-500 dark:text-slate-400">{{ $page.props.auth?.user?.email || 'user@example.com' }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                      <Link :href="route('users.edit.profile')"
                            class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">
                        <User class="w-4 h-4" />
                        {{ $t('Edit Profile') }}
                      </Link>
                      <LogoutButton />
                    </div>
                  </div>
                </template>
              </dropdown>
            </div>
          </div>
        </header>

        <!-- Page Header -->


        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900">
          <!-- Flash Messages -->
          <flash-messages />

          <!-- Page Content -->
          <div class="p-4 lg:p-6">
            <slot />
          </div>
        </main>
      </div>
    </div>
    
    <!-- Session Timeout Warning -->
    <SessionTimeout />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon.vue'
import Logo from '@/Shared/Logo.vue'
import Dropdown from '@/Shared/Dropdown.vue'
import MainMenu from '@/Shared/MainMenu.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import LogoutButton from '@/Components/LogoutButton.vue'
import SessionTimeout from '@/Components/SessionTimeout.vue'
import {Link, usePage} from '@inertiajs/vue3'
import moment from 'moment'
import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';
import axios from 'axios'
import {computed} from "vue";
import NotificationBell from '@/Components/NotificationBell.vue';
import {
    Menu,
    X,
    ChevronDown,
    ChevronRight,
    Sun,
    Moon,
    User,
    LogOut,
    Home
} from 'lucide-vue-next';

export default {
    components: {
        NotificationBell,
        Dropdown,
        FlashMessages,
        Icon,
        Logo,
        Link,
        LogoutButton,
        MainMenu,
        SessionTimeout,
        Menu,
        X,
        ChevronDown,
        ChevronRight,
        Sun,
        Moon,
        User,
        LogOut,
        Home,
    },
    props: {
        title: String,
    },
    data() {
        return{
            time: '',
            current_mode: 'light',
            modes: ['dark', 'light'],
            edit_route: '',
            locale: (this.$page.props.auth && this.$page.props.auth.user && this.$page.props.auth.user.locale) || (this.$page.props.settings && this.$page.props.settings.default_language) || 'en',
            mobileMenuOpen: false,
        }
    },
    computed: {
        selected_language() {
            if (!this.$page.props.languages || !Array.isArray(this.$page.props.languages)) {
                return { code: 'en', name: 'English' }; // Default fallback
            }
            return this.$page.props.languages.find(language => language.code === (this.$page.props.locale || 'en')) || { code: 'en', name: 'English' };
        },
        languages_except_selected(){
            if (!this.$page.props.languages || !Array.isArray(this.$page.props.languages)) {
                return []; // Return empty array if languages is not available
            }
            return this.$page.props.languages.filter(language => language.code !== (this.$page.props.locale || 'en'));
        }
    },
    setup() {
        const page = usePage();
        const license_invalid = computed(() => page.props.license_invalid);

        return {
            license_invalid
        };
    },
    methods:{
        updateLanguage(code){
            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = token ? token.getAttribute('content') : '';

            // Use FormData to send the request (Laravel expects form data for CSRF)
            const formData = new FormData();
            formData.append('_token', csrfToken);

            fetch(`/language/${code}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Failed to update language:', error);
            });
        },
        switchMode(){
            this.current_mode = this.current_mode === 'light' ? 'dark' : 'light'
            localStorage.setItem('current_mode', this.current_mode)
        },
        detectCurrentUrl(){
            const url = this.$page.url;
            const splitUrl = url.split('/');
            let editString = ['edit', 'create'].includes(url.substring(url.lastIndexOf("/") + 1));
            if(!editString){
                editString = splitUrl[splitUrl.length - 2] === 'tickets';
            }
            let editRoute = url.split('/')[2]
            if(['settings','front_pages'].includes(editRoute)){
                editRoute = url.split('/')[3];
            }
            this.edit_route = editString? editRoute : '';
        },
        closeMobileMenu() {
            this.mobileMenuOpen = false;
        },
    },
    updated() {
        this.detectCurrentUrl()
    },
    created() {
        this.moment = moment;
        let vm = this
        if(localStorage.getItem('current_mode')){
            this.current_mode = localStorage.getItem('current_mode')
        }
        vm.time = vm.moment().format('MMMM Do YYYY, h:mm A')
        window.setInterval(function () {
            vm.time = vm.moment().format('MMMM Do YYYY, h:mm A')
        }, 1000)
        this.detectCurrentUrl()

        if(getActiveLanguage() !== this.locale){
            loadLanguageAsync(this.locale)
        }
    }
}
</script>
