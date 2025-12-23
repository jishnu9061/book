<template>
  <Head title="Register" />
  
  <!-- Background with animated elements -->
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="floating-element absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl animate-float-1"></div>
      <div class="floating-element absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-400/20 to-pink-400/20 rounded-full blur-3xl animate-float-2"></div>
      <div class="floating-element absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-purple-400/10 to-blue-400/10 rounded-full blur-3xl animate-float-3"></div>
      <div class="floating-element absolute top-1/4 right-1/4 w-64 h-64 bg-gradient-to-br from-pink-400/15 to-indigo-400/15 rounded-full blur-3xl animate-float-4"></div>
      <div class="floating-element absolute bottom-1/4 left-1/4 w-72 h-72 bg-gradient-to-br from-blue-400/15 to-purple-400/15 rounded-full blur-3xl animate-float-5"></div>
    </div>

    <!-- Main container -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="w-full max-w-2xl">
        <!-- Header -->
        <div class="text-center mb-8">
          <Link :href="route('home')" class="inline-block">
            <img src="/images/logo.png" alt="HelpDesk Logo" class="h-16 w-auto mx-auto mb-6" />
          </Link>
          <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">
            Create Your Account
          </h1>
          <p class="text-lg text-slate-600 dark:text-slate-400">
            Join our helpdesk community and get started today
          </p>
        </div>

        <!-- Registration Form Card -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-white/20 dark:border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">
          <div class="p-8">
            <!-- Flash Messages -->
            <FlashMessages />
            
            <form @submit.prevent="register" class="space-y-6">
              <!-- Personal Information Section -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center">
                  <User class="w-5 h-5 mr-2" />
                  Personal Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                      First Name <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.first_name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                      :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.first_name }"
                      placeholder="Enter your first name"
                    />
                    <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.first_name }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                      Last Name <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.last_name"
                      type="text"
                      required
                      class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                      :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.last_name }"
                      placeholder="Enter your last name"
                    />
                    <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.last_name }}
                    </p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Email Address <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                    :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.email }"
                    placeholder="Enter your email address"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.email }}
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Phone Number
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                    :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.phone }"
                    placeholder="Enter your phone number"
                  />
                  <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.phone }}
                  </p>
                </div>
              </div>

              <!-- Location Information Section -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center">
                  <MapPin class="w-5 h-5 mr-2" />
                  Location Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                      Country
                    </label>
                    <input
                      v-model="form.country_id"
                      type="text"
                      class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                      :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.country_id }"
                      placeholder="Enter your country"
                    />
                    <p v-if="form.errors.country_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.country_id }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                      City
                    </label>
                    <input
                      v-model="form.city"
                      type="text"
                      class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                      :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.city }"
                      placeholder="Enter your city"
                    />
                    <p v-if="form.errors.city" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.city }}
                    </p>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Address
                  </label>
                  <textarea
                    v-model="form.address"
                    rows="3"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200 resize-none"
                    :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.address }"
                    placeholder="Enter your full address"
                  ></textarea>
                  <p v-if="form.errors.address" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.address }}
                  </p>
                </div>
              </div>

              <!-- Security Section -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center">
                  <Shield class="w-5 h-5 mr-2" />
                  Security
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                      Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                        :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.password }"
                        placeholder="Create a strong password"
                      />
                      <button
                        @click="showPassword = !showPassword"
                        type="button"
                        class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                      >
                        <Eye v-if="!showPassword" class="w-5 h-5" />
                        <EyeOff v-else class="w-5 h-5" />
                      </button>
                    </div>
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.password }}
                    </p>
                    <div v-else class="mt-1">
                      <div class="flex items-center space-x-2">
                        <div 
                          :class="[
                            'w-2 h-2 rounded-full',
                            passwordStrength >= 1 ? 'bg-red-500' : 'bg-slate-200 dark:bg-slate-600'
                          ]"
                        ></div>
                        <div 
                          :class="[
                            'w-2 h-2 rounded-full',
                            passwordStrength >= 2 ? 'bg-yellow-500' : 'bg-slate-200 dark:bg-slate-600'
                          ]"
                        ></div>
                        <div 
                          :class="[
                            'w-2 h-2 rounded-full',
                            passwordStrength >= 3 ? 'bg-green-500' : 'bg-slate-200 dark:bg-slate-600'
                          ]"
                        ></div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                          {{ passwordStrengthText }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                      Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        v-model="form.confirm_password"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        required
                        class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                        :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': form.errors.confirm_password }"
                        placeholder="Confirm your password"
                      />
                      <button
                        @click="showConfirmPassword = !showConfirmPassword"
                        type="button"
                        class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                      >
                        <Eye v-if="!showConfirmPassword" class="w-5 h-5" />
                        <EyeOff v-else class="w-5 h-5" />
                      </button>
                    </div>
                    <p v-if="form.errors.confirm_password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      {{ form.errors.confirm_password }}
                    </p>
                    <p v-else-if="form.confirm_password && form.password !== form.confirm_password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                      Passwords do not match
                    </p>
                  </div>
                </div>
              </div>

              <!-- reCAPTCHA -->
              <div v-if="site_key" class="flex justify-center">
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

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="(disable_button && site_key) || form.processing"
                :class="[
                  'w-full py-4 px-6 rounded-lg font-semibold text-white transition-all duration-200 transform',
                  (disable_button && site_key) || form.processing
                    ? 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed'
                    : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 hover:scale-105 shadow-lg hover:shadow-xl'
                ]"
              >
                <div v-if="form.processing" class="flex items-center justify-center">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                  Creating Account...
                </div>
                <div v-else class="flex items-center justify-center">
                  <UserPlus class="w-5 h-5 mr-2" />
                  Create Account
                </div>
              </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
              <p class="text-slate-600 dark:text-slate-400">
                Already have an account? 
                <Link :href="route('login')" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">
                  Sign in here
                </Link>
              </p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
          <p class="text-sm text-slate-500 dark:text-slate-400">
            By creating an account, you agree to our 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Terms of Service</a> 
            and 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Privacy Policy</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { User, MapPin, Shield, Eye, EyeOff, UserPlus } from 'lucide-vue-next'
import FlashMessages from '@/Shared/FlashMessages.vue'
import vueRecaptcha from "vue3-recaptcha2"

export default {
  name: 'Register',
  components: {
    Head,
    Link,
    FlashMessages,
    vueRecaptcha,
    User,
    MapPin,
    Shield,
    Eye,
    EyeOff,
    UserPlus
  },
  props: {
    is_demo: Number,
    site_key: String,
  },
  data() {
    return {
      showPassword: false,
      showConfirmPassword: false,
      disable_button: true,
      form: this.$inertia.form({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        city: '',
        country_id: '',
        address: '',
        password: '',
        confirm_password: '',
      }),
    }
  },
  computed: {
    passwordStrength() {
      const password = this.form.password
      if (!password) return 0
      
      let strength = 0
      if (password.length >= 8) strength++
      if (/[A-Z]/.test(password)) strength++
      if (/[a-z]/.test(password)) strength++
      if (/[0-9]/.test(password)) strength++
      if (/[^A-Za-z0-9]/.test(password)) strength++
      
      return Math.min(strength, 3)
    },
    passwordStrengthText() {
      switch (this.passwordStrength) {
        case 0:
        case 1:
          return 'Weak'
        case 2:
          return 'Medium'
        case 3:
          return 'Strong'
        default:
          return 'Very Strong'
      }
    }
  },
  methods: {
    recaptchaVerified() {
      this.disable_button = false
    },
    recaptchaExpired() {
      // Reset reCAPTCHA if needed
    },
    recaptchaFailed() {
      // Handle reCAPTCHA failure
    },
    recaptchaError(reason) {
      console.log('reCAPTCHA error:', reason)
    },
    register() {
      if (this.form.password !== this.form.confirm_password) {
        alert('Your password is not matched correctly.')
        return
      }
      
      this.form.post(this.route('register.store'))
    }
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes float-1 {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes float-2 {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(20px) rotate(-180deg); }
}

@keyframes float-3 {
  0%, 100% { transform: translate(-50%, -50%) scale(1) rotate(0deg); }
  50% { transform: translate(-50%, -50%) scale(1.1) rotate(180deg); }
}

@keyframes float-4 {
  0%, 100% { transform: translateY(0px) scale(1); }
  50% { transform: translateY(-15px) scale(1.05); }
}

@keyframes float-5 {
  0%, 100% { transform: translateY(0px) scale(1); }
  50% { transform: translateY(15px) scale(0.95); }
}

.animate-float-1 {
  animation: float-1 6s ease-in-out infinite;
}

.animate-float-2 {
  animation: float-2 8s ease-in-out infinite;
}

.animate-float-3 {
  animation: float-3 10s ease-in-out infinite;
}

.animate-float-4 {
  animation: float-4 7s ease-in-out infinite;
}

.animate-float-5 {
  animation: float-5 9s ease-in-out infinite;
}

/* Glassmorphism effect */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

/* Smooth transitions */
* {
  transition: all 0.3s ease-in-out;
}
</style>
