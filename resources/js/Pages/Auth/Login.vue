<template>
  <Head title="Login" />

  <!-- Modern Login Page with Gradient Background -->
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 flex items-center justify-center p-4">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-400/20 to-pink-400/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Flash Messages -->
    <flash-messages />

    <!-- Main Login Container -->
    <div class="relative w-full max-w-md">
      <!-- Logo Section -->
      <div class="text-center mb-8">
        <Link :href="route('home')" class="inline-block">
          <Logo class="block w-48 mx-auto fill-white drop-shadow-lg" />
        </Link>
        <p class="mt-4 text-slate-600 dark:text-slate-400 text-sm">
          Welcome back! Please sign in to your account
        </p>
      </div>

      <!-- Login Card -->
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-white/20 dark:border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="px-8 pt-8 pb-6">
          <h1 class="text-2xl font-bold text-center text-slate-900 dark:text-white mb-2">
            {{ $t('Sign In') }}
          </h1>
          <p class="text-center text-slate-600 dark:text-slate-400 text-sm">
            Enter your credentials to access your account
          </p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleLogin" class="px-8 pb-8">
          <!-- Email Field -->
          <div class="mb-6">
            <text-input
              v-model="form.email"
              :error="form.errors.email"
              label="Email Address"
              type="email"
              autofocus
              autocapitalize="off"
              placeholder="Enter your email"
              class="w-full"
              @input="clearError"
            />
          </div>

          <!-- Password Field -->
          <div class="mb-6">
            <text-input
              v-model="form.password"
              :error="form.errors.password"
              label="Password"
              type="password"
              placeholder="Enter your password"
              class="w-full"
              @input="clearError"
            />
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center cursor-pointer group">
              <input
                id="remember"
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
              />
              <span class="ml-2 text-sm text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                {{ $t('Remember Me') }}
              </span>
            </label>
            <Link
              :href="route('password.reset')"
              class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
            >
              {{ $t('Forgot Password?') }}
            </Link>
          </div>

          <!-- reCAPTCHA -->
          <div v-if="site_key" class="flex justify-center mb-6">
            <vue-recaptcha
              :sitekey="site_key"
              size="normal"
              theme="light"
              @verify="recaptchaVerified"
              @expire="recaptchaExpired"
              @fail="recaptchaFailed"
              @error="recaptchaError"
              ref="vueRecaptcha"
            />
          </div>

          <!-- Login Error Message -->
          <div v-if="loginError" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              {{ loginError }}
            </div>
          </div>

          <!-- Login Button -->
          <loading-button
            :disabled="(disable_login_button && site_key) || isLoggingIn"
            :loading="isLoggingIn"
            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg hover:shadow-xl"
            type="submit"
          >
            <span v-if="!isLoggingIn">{{ $t('Sign In') }}</span>
            <span v-else>{{ $t('Signing In...') }}</span>
          </loading-button>

          <!-- Registration Link -->
          <div v-if="enable_registration" class="mt-6 text-center">
            <p class="text-sm text-slate-600 dark:text-slate-400">
              {{ $t("Don't have an account?") }}
              <Link
                :href="route('register')"
                class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
              >
                {{ $t('Sign Up') }}
              </Link>
            </p>
          </div>
        </form>
      </div>

      <!-- Demo Credentials Section -->
      <div v-if="is_demo" class="mt-8 bg-white/60 dark:bg-slate-800/60 backdrop-blur-xl border border-white/20 dark:border-slate-700/50 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/50 dark:border-slate-700/50">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white text-center">
            🚀 Demo Credentials
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400 text-center mt-1">
            Try different user roles instantly
          </p>
        </div>

        <div class="p-6">
          <!-- Quick Login Buttons -->
          <div class="grid grid-cols-2 gap-3 mb-6">
            <button
              @click="autofillLogin($event, 'admin', true)"
              class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg"
            >
              <Crown class="w-4 h-4 mr-2" />
              Admin
            </button>
            <button
              @click="autofillLogin($event, 'manager', true)"
              class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg"
            >
              <Shield class="w-4 h-4 mr-2" />
              Manager
            </button>
            <button
              @click="autofillLogin($event, 'agent', true)"
              class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg"
            >
              <User class="w-4 h-4 mr-2" />
              Agent
            </button>
            <button
              @click="autofillLogin($event, 'customer', true)"
              class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-500 to-violet-500 hover:from-purple-600 hover:to-violet-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg"
            >
              <Users class="w-4 h-4 mr-2" />
              Customer
            </button>
          </div>

          <!-- Detailed Credentials Table -->
          <div class="space-y-3">
            <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
              Or copy credentials manually:
            </h4>

            <div class="space-y-2">
              <div
                v-for="(credential, role) in demoCredentials"
                :key="role"
                class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg border border-slate-200 dark:border-slate-600"
              >
                <div class="flex-1">
                  <div class="flex items-center space-x-2">
                    <component :is="credential.icon" class="w-4 h-4 text-slate-500" />
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300 capitalize">{{ role }}</span>
                  </div>
                  <div class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                    {{ credential.email }}
                  </div>
                </div>
                <button
                  @click="autofillLogin($event, role)"
                  class="ml-3 px-3 py-1 text-xs bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 dark:hover:bg-slate-500 text-slate-700 dark:text-slate-300 rounded-md transition-colors"
                >
                  Copy
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { Head, Link } from '@inertiajs/vue3'
import FlashMessages from '@/Shared/FlashMessages.vue'
import vueRecaptcha from 'vue3-recaptcha2'
import { Crown, Shield, User, Users } from 'lucide-vue-next'

export default {
  metaInfo: { title: 'Login' },
  components: {
    FlashMessages,
    LoadingButton,
    Logo,
    TextInput,
    Head,
    Link,
    vueRecaptcha,
    Crown,
    Shield,
    User,
    Users,
  },
  props: {
    is_demo: Number,
    site_key: String,
    enable_registration: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      loadingTimeout: 30000,
      disable_login_button: true,
      isLoggingIn: false,
      loginError: null,
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false,
      }),
      demoCredentials: {
        admin: {
          email: 'john.due.helo@mail.com',
          icon: Crown
        },
        manager: {
          email: 'robert.slaughter@mail.com',
          icon: Shield
        },
        agent: {
          email: 'john.ali@mail.com',
          icon: User
        },
        customer: {
          email: 'mmarks@example.com',
          icon: Users
        }
      }
    }
  },
  methods: {
    recaptchaVerified(response) {
      this.disable_login_button = false
    },
    recaptchaExpired() {
      this.$refs.vueRecaptcha.reset();
    },
    recaptchaFailed() {
      // Handle recaptcha failure
    },
    recaptchaError(reason) {
      console.log(reason)
    },
    clearError() {
      this.loginError = null
    },
    async handleLogin() {
      // Clear any previous errors
      this.clearError()

      // Set loading state
      this.isLoggingIn = true

      try {
        // Create a form element for submission
        const form = document.createElement('form')
        form.method = 'POST'
        form.action = route('login.store')

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
        if (csrfToken) {
          const csrfInput = document.createElement('input')
          csrfInput.type = 'hidden'
          csrfInput.name = '_token'
          csrfInput.value = csrfToken.getAttribute('content')
          form.appendChild(csrfInput)
        }

        // Add form data
        const emailInput = document.createElement('input')
        emailInput.type = 'hidden'
        emailInput.name = 'email'
        emailInput.value = this.form.email
        form.appendChild(emailInput)

        const passwordInput = document.createElement('input')
        passwordInput.type = 'hidden'
        passwordInput.name = 'password'
        passwordInput.value = this.form.password
        form.appendChild(passwordInput)

        if (this.form.remember) {
          const rememberInput = document.createElement('input')
          rememberInput.type = 'hidden'
          rememberInput.name = 'remember'
          rememberInput.value = '1'
          form.appendChild(rememberInput)
        }

        // Append form to body and submit
        document.body.appendChild(form)
        form.submit()

      } catch (error) {
        console.error('Login error:', error)
        this.isLoggingIn = false
        this.loginError = 'Login failed. Please try again.'
      }
    },
    autofillLogin(e, role, login = false) {
      e.preventDefault()
      const roleEmails = {
        'admin': 'john.due.helo@mail.com',
        'manager': 'robert.slaughter@mail.com',
        'agent': 'john.ali@mail.com',
        'customer': 'mmarks@example.com'
      }
      this.form.email = roleEmails[role]
      this.form.password = 'w3bd.com'
      if (login) {
        this.handleLogin();
      }
    }
  }
}
</script>

<style scoped>
/* Custom animations and effects */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

/* Glassmorphism effect */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

/* Custom gradient text */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Enhanced focus states */
input:focus {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Smooth transitions */
* {
  transition: all 0.2s ease-in-out;
}
</style>
