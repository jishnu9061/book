<template>
  <div class="w-80 h-auto bg-white rounded-lg shadow-2xl">
    <nav class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-lg flex flex-col items-center pt-4 pb-2">
      <Logo class="block mx-auto max-w-xs fill-white" height="40" />
      <p class="text-sm font-medium text-blue-100 text-center px-4 mt-2">
        {{ $t('Let us know who you are, and let\'s get talking.') }}
      </p>
    </nav>

    <div class="p-6">
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Name Fields -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="first_name">
              {{ $t('First name') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.first_name"
              :class="[
                'w-full px-3 py-2 border rounded-md text-sm transition-colors duration-200',
                errors.first_name ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
              ]"
              id="first_name"
              type="text"
              :placeholder="$t('Enter first name')"
              :disabled="isSubmitting"
              @blur="validateField('first_name')"
            />
            <p v-if="errors.first_name" class="text-red-500 text-xs mt-1">{{ errors.first_name }}</p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1" for="last_name">
              {{ $t('Last name') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.last_name"
              :class="[
                'w-full px-3 py-2 border rounded-md text-sm transition-colors duration-200',
                errors.last_name ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
              ]"
              id="last_name"
              type="text"
              :placeholder="$t('Enter last name')"
              :disabled="isSubmitting"
              @blur="validateField('last_name')"
            />
            <p v-if="errors.last_name" class="text-red-500 text-xs mt-1">{{ errors.last_name }}</p>
          </div>
        </div>

        <!-- Email Field -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1" for="email">
            {{ $t('Email Address') }} <span class="text-red-500">*</span>
          </label>
          <input
            v-model="form.email"
            :class="[
              'w-full px-3 py-2 border rounded-md text-sm transition-colors duration-200',
              errors.email ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-blue-500'
            ]"
            id="email"
            type="email"
            :placeholder="$t('Enter email address')"
            :disabled="isSubmitting"
            @blur="validateField('email')"
          />
          <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
        </div>

        <!-- Optional: Department/Subject -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1" for="subject">
            {{ $t('How can we help?') }} <span class="text-gray-400 text-xs">({{ $t('Optional') }})</span>
          </label>
          <select
            v-model="form.subject"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:outline-none"
            :disabled="isSubmitting"
          >
            <option value="">{{ $t('Select a topic') }}</option>
            <option value="general">{{ $t('General Inquiry') }}</option>
            <option value="technical">{{ $t('Technical Support') }}</option>
            <option value="billing">{{ $t('Billing Question') }}</option>
            <option value="feature">{{ $t('Feature Request') }}</option>
            <option value="bug">{{ $t('Bug Report') }}</option>
          </select>
        </div>

        <!-- Error Message -->
        <div v-if="submitError" class="bg-red-50 border border-red-200 rounded-md p-3">
          <p class="text-red-700 text-sm">{{ submitError }}</p>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="isSubmitting || !isFormValid"
          :class="[
            'w-full py-2 px-4 rounded-md text-sm font-semibold transition-all duration-200 flex items-center justify-center',
            isSubmitting || !isFormValid
              ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
              : 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg'
          ]"
        >
          <div v-if="isSubmitting" class="flex items-center">
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
            {{ $t('Starting chat...') }}
          </div>
          <div v-else class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            {{ $t('Start Chat') }}
          </div>
        </button>

        <!-- Privacy Notice -->
        <p class="text-xs text-gray-500 text-center">
          {{ $t('By starting a chat, you agree to our') }}
          <a href="/privacy" class="text-blue-600 hover:underline">{{ $t('Privacy Policy') }}</a>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo.vue'

export default {
  components: { Logo },
  props: {
    onSubmit: Function
  },
  data() {
    return {
      form: {
          first_name: '',
          last_name: '',
        email: '',
        subject: ''
      },
      errors: {},
      isSubmitting: false,
      submitError: null
    }
  },
  computed: {
    isFormValid() {
      return this.form.first_name.trim() &&
             this.form.last_name.trim() &&
             this.form.email.trim() &&
             this.isValidEmail(this.form.email) &&
             !this.isSubmitting
    }
  },
  methods: {
    validateField(field) {
      this.errors[field] = ''

      switch (field) {
        case 'first_name':
          if (!this.form.first_name.trim()) {
            this.errors.first_name = this.$t('First name is required')
          } else if (this.form.first_name.trim().length < 2) {
            this.errors.first_name = this.$t('First name must be at least 2 characters')
          }
          break

        case 'last_name':
          if (!this.form.last_name.trim()) {
            this.errors.last_name = this.$t('Last name is required')
          } else if (this.form.last_name.trim().length < 2) {
            this.errors.last_name = this.$t('Last name must be at least 2 characters')
          }
          break

        case 'email':
          if (!this.form.email.trim()) {
            this.errors.email = this.$t('Email is required')
          } else if (!this.isValidEmail(this.form.email)) {
            this.errors.email = this.$t('Please enter a valid email address')
          }
          break
      }
    },

    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    },

    async handleSubmit() {
      // Validate all fields
      Object.keys(this.form).forEach(field => {
        if (field !== 'subject') {
          this.validateField(field)
        }
      })

      if (!this.isFormValid) return

      this.isSubmitting = true
      this.submitError = null

      try {
        await this.onSubmit(this.form)
      } catch (error) {
        this.submitError = error.message || this.$t('Failed to start chat. Please try again.')
      } finally {
        this.isSubmitting = false
      }
    }
  }
}
</script>
