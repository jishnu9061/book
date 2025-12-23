<template>
  <div class="animate-fade-in-up">
    <div class="text-center mb-8">
      <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
        <User class="w-10 h-10 text-white" />
      </div>
      <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
        Admin Account Setup
      </h2>
      <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        Create your administrator account to access the HelpDesk dashboard.
      </p>
    </div>

    <!-- Admin Form -->
    <div class="max-w-2xl mx-auto">
      <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6 mb-6">
        <div class="space-y-4">
          <!-- Name Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                First Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.firstName"
                type="text"
                placeholder="John"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': errors.firstName }"
                @input="clearErrors"
              />
              <p v-if="errors.firstName" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.firstName }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.lastName"
                type="text"
                placeholder="Doe"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': errors.lastName }"
                @input="clearErrors"
              />
              <p v-if="errors.lastName" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.lastName }}
              </p>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              Email Address <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.email"
              type="email"
              placeholder="admin@yourdomain.com"
              class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
              :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': errors.email }"
              @input="clearErrors"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ errors.email }}
            </p>
            <p v-else class="mt-1 text-sm text-slate-500 dark:text-slate-400">
              This will be your login email address
            </p>
          </div>

          <!-- Password Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Password <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Enter a strong password"
                  class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                  :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': errors.password }"
                  @input="clearErrors"
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
              <p v-if="errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.password }}
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
                  v-model="form.confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  placeholder="Confirm your password"
                  class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                  :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': errors.confirmPassword }"
                  @input="clearErrors"
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
              <p v-if="errors.confirmPassword" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ errors.confirmPassword }}
              </p>
              <p v-else-if="form.confirmPassword && form.password !== form.confirmPassword" class="mt-1 text-sm text-red-600 dark:text-red-400">
                Passwords do not match
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Security Tips -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
        <div class="flex items-start">
          <Shield class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" />
          <div>
            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Security Best Practices</h4>
            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
              <li>• Use a strong, unique password with at least 8 characters</li>
              <li>• Include uppercase, lowercase, numbers, and special characters</li>
              <li>• Avoid using personal information or common words</li>
              <li>• Consider using a password manager to generate and store passwords</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Account Preview -->
      <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
        <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-3">Account Preview</h4>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Name:</span>
            <span class="text-slate-900 dark:text-white font-medium">
              {{ form.firstName }} {{ form.lastName }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Email:</span>
            <span class="text-slate-900 dark:text-white font-medium">{{ form.email }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-600 dark:text-slate-400">Role:</span>
            <span class="text-slate-900 dark:text-white font-medium">Administrator</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
      <button 
        @click="$emit('back')"
        class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
      >
        <ArrowLeft class="w-4 h-4 mr-2 inline" />
        Back
      </button>
      
      <button 
        @click="validateAndContinue"
        :disabled="!isFormValid"
        :class="[
          'px-8 py-3 rounded-lg font-semibold transition-all duration-200 transform',
          isFormValid
            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white hover:scale-105 shadow-lg hover:shadow-xl'
            : 'bg-slate-300 dark:bg-slate-600 text-slate-500 dark:text-slate-400 cursor-not-allowed'
        ]"
      >
        Start Installation
        <ArrowRight class="w-4 h-4 ml-2 inline" />
      </button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue'
import { User, Eye, EyeOff, Shield, ArrowLeft, ArrowRight } from 'lucide-vue-next'

export default {
  name: 'InstallerAdmin',
  components: {
    User,
    Eye,
    EyeOff,
    Shield,
    ArrowLeft,
    ArrowRight
  },
  props: {
    form: {
      type: Object,
      required: true
    }
  },
  emits: ['next', 'back', 'update:form'],
  setup(props, { emit }) {
    const showPassword = ref(false)
    const showConfirmPassword = ref(false)
    const errors = reactive({})

    const form = reactive({
      firstName: props.form.firstName || '',
      lastName: props.form.lastName || '',
      email: props.form.email || '',
      password: props.form.password || '',
      confirmPassword: props.form.confirmPassword || ''
    })

    const passwordStrength = computed(() => {
      const password = form.password
      if (!password) return 0
      
      let strength = 0
      if (password.length >= 8) strength++
      if (/[A-Z]/.test(password)) strength++
      if (/[a-z]/.test(password)) strength++
      if (/[0-9]/.test(password)) strength++
      if (/[^A-Za-z0-9]/.test(password)) strength++
      
      return Math.min(strength, 3)
    })

    const passwordStrengthText = computed(() => {
      switch (passwordStrength.value) {
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
    })

    const isFormValid = computed(() => {
      return form.firstName && 
             form.lastName && 
             form.email && 
             form.password && 
             form.confirmPassword &&
             form.password === form.confirmPassword &&
             passwordStrength.value >= 2
    })

    const clearErrors = () => {
      Object.keys(errors).forEach(key => delete errors[key])
    }

    const validateForm = () => {
      clearErrors()

      if (!form.firstName) {
        errors.firstName = 'First name is required'
      }

      if (!form.lastName) {
        errors.lastName = 'Last name is required'
      }

      if (!form.email) {
        errors.email = 'Email is required'
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address'
      }

      if (!form.password) {
        errors.password = 'Password is required'
      } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters long'
      } else if (passwordStrength.value < 2) {
        errors.password = 'Password is too weak. Please use a stronger password.'
      }

      if (!form.confirmPassword) {
        errors.confirmPassword = 'Please confirm your password'
      } else if (form.password !== form.confirmPassword) {
        errors.confirmPassword = 'Passwords do not match'
      }

      return Object.keys(errors).length === 0
    }

    const validateAndContinue = () => {
      if (validateForm()) {
        emit('update:form', { ...form })
        emit('next')
      }
    }

    const updateForm = () => {
      emit('update:form', { ...form })
    }

    // Watch for form changes
    watch(form, () => {
      updateForm()
    }, { deep: true })

    return {
      form,
      showPassword,
      showConfirmPassword,
      errors,
      passwordStrength,
      passwordStrengthText,
      isFormValid,
      clearErrors,
      validateAndContinue
    }
  }
}
</script>
