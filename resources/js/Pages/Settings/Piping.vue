<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="title" />
    
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Email Piping Settings') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Configure email piping to automatically create tickets from incoming emails') }}
              </p>
            </div>
            <div class="flex items-center gap-3">
              <button 
                @click="testConnection"
                :disabled="testingConnection"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 shadow-sm hover:shadow-md"
              >
                <Loader2 v-if="testingConnection" class="w-4 h-4 animate-spin" />
                <Wifi v-else class="w-4 h-4" />
                {{ testingConnection ? $t('Testing...') : $t('Test Connection') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Email Piping Configuration Form -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <form @submit.prevent="update">
          <div class="p-8">
            <!-- Enable Email Piping -->
            <div class="mb-8">
              <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex-1">
                  <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center gap-2">
                    <Mail class="w-5 h-5" />
                    {{ $t('Enable Email Piping') }}
                  </h3>
                  <p class="text-sm text-blue-800 dark:text-blue-200">
                    {{ $t('Automatically create tickets from incoming emails including attachments') }}
                  </p>
                </div>
                <label class="flex items-center cursor-pointer">
                  <div class="relative">
                    <input 
                      type="checkbox" 
                      class="sr-only" 
                      v-model="option.value"
                      @change="updatePipingOption"
                    />
                    <div 
                      :class="[
                        'w-12 h-6 rounded-full shadow-inner transition-colors duration-200',
                        option.value ? 'bg-blue-600' : 'bg-gray-400'
                      ]"
                    ></div>
                    <div 
                      :class="[
                        'absolute w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-200 top-0.5',
                        option.value ? 'translate-x-6' : 'translate-x-0.5'
                      ]"
                    ></div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Demo Notice -->
            <div v-if="demo" class="mb-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
              <div class="flex items-center gap-2">
                <AlertTriangle class="w-5 h-5 text-yellow-600" />
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                  {{ $t('Demo Mode: You can test email piping by sending emails to piping@atorali.com') }}
                </p>
              </div>
            </div>

            <!-- IMAP Configuration -->
            <div v-if="option.value" class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Server class="w-5 h-5 text-green-600" />
                {{ $t('IMAP Configuration') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput 
                  v-model="form.IMAP_HOST" 
                  :error="form.errors.IMAP_HOST" 
                  :label="$t('IMAP Host')"
                  :placeholder="$t('imap.gmail.com')"
                  required
                />
                <TextInput 
                  v-model="form.IMAP_PORT" 
                  :error="form.errors.IMAP_PORT" 
                  :label="$t('IMAP Port')"
                  :placeholder="$t('993')"
                  type="number"
                  required
                />
                <TextInput 
                  v-model="form.IMAP_PROTOCOL" 
                  :error="form.errors.IMAP_PROTOCOL" 
                  :label="$t('IMAP Protocol')"
                  :placeholder="$t('imap')"
                  required
                />
                <SelectInput 
                  v-model="form.IMAP_ENCRYPTION" 
                  :error="form.errors.IMAP_ENCRYPTION" 
                  :label="$t('IMAP Encryption')"
                >
                  <option value="">{{ $t('None') }}</option>
                  <option value="ssl">{{ $t('SSL') }}</option>
                  <option value="tls">{{ $t('TLS') }}</option>
                </SelectInput>
                <TextInput 
                  v-model="form.IMAP_USERNAME" 
                  :error="form.errors.IMAP_USERNAME" 
                  :label="$t('IMAP Username')"
                  :placeholder="$t('your-email@gmail.com')"
                  required
                />
                <TextInput 
                  v-model="form.IMAP_PASSWORD" 
                  :error="form.errors.IMAP_PASSWORD" 
                  :label="$t('IMAP Password')"
                  type="password"
                  :placeholder="$t('Your email password or app password')"
                  required
                />
              </div>
            </div>

            <!-- Test Results -->
            <div v-if="testResult" class="mb-8">
              <div 
                :class="[
                  'p-4 rounded-lg border',
                  testResult.success 
                    ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' 
                    : 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800'
                ]"
              >
                <div class="flex items-center gap-2">
                  <CheckCircle v-if="testResult.success" class="w-5 h-5 text-green-600" />
                  <XCircle v-else class="w-5 h-5 text-red-600" />
                  <h4 class="font-medium" :class="testResult.success ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'">
                    {{ testResult.success ? $t('Connection Successful') : $t('Connection Failed') }}
                  </h4>
                </div>
                <p class="mt-2 text-sm" :class="testResult.success ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300'">
                  {{ testResult.message }}
                </p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="px-8 py-4 bg-slate-50 dark:bg-slate-700 border-t border-slate-200 dark:border-slate-600 flex items-center justify-between">
            <div class="text-sm text-slate-500 dark:text-slate-400">
              {{ $t('Email piping will be enabled after saving and setting up cron job') }}
            </div>
            <LoadingButton 
              :loading="form.processing" 
              class="btn-indigo" 
              type="submit"
            >
              {{ $t('Save Settings') }}
            </LoadingButton>
          </div>
        </form>
      </div>

      <!-- Cron Job Instructions -->
      <div v-if="option.value" class="mt-8 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <Clock class="w-5 h-5 text-purple-600" />
          {{ $t('Cron Job Setup Instructions') }}
        </h3>
        <div class="space-y-4">
          <div>
            <h4 class="font-medium text-slate-900 dark:text-white mb-2">{{ $t('For VPS/Dedicated Servers:') }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
              {{ $t('Add this cron job to run every 3 minutes:') }}
            </p>
            <div class="bg-slate-900 dark:bg-slate-700 rounded-lg p-4 font-mono text-sm text-green-400 overflow-x-auto">
              <code>*/3 * * * * /usr/bin/php {{ basePath }}/artisan command:piping_email</code>
            </div>
          </div>
          
          <div>
            <h4 class="font-medium text-slate-900 dark:text-white mb-2">{{ $t('For Shared Hosting (cPanel):') }}</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
              {{ $t('Add this URL-based cron job to run every 3 minutes:') }}
            </p>
            <div class="bg-slate-900 dark:bg-slate-700 rounded-lg p-4 font-mono text-sm text-green-400 overflow-x-auto">
              <code>*/3 * * * * wget -q -O - {{ baseUrl }}/cron/piping >/dev/null 2>&1</code>
            </div>
          </div>
        </div>
      </div>

      <!-- Help Section -->
      <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
          <HelpCircle class="w-5 h-5" />
          {{ $t('Email Piping Help') }}
        </h3>
        <div class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
          <p><strong>{{ $t('Gmail:') }}</strong> {{ $t('Use imap.gmail.com, port 993, SSL encryption, and enable 2-factor authentication with an app password.') }}</p>
          <p><strong>{{ $t('Outlook:') }}</strong> {{ $t('Use outlook.office365.com, port 993, SSL encryption.') }}</p>
          <p><strong>{{ $t('Yahoo:') }}</strong> {{ $t('Use imap.mail.yahoo.com, port 993, SSL encryption, and use an app password.') }}</p>
          <p><strong>{{ $t('Custom IMAP:') }}</strong> {{ $t('Contact your email provider for the correct IMAP settings.') }}</p>
        </div>
      </div>

      <!-- Features -->
      <div class="mt-8 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 p-6">
        <h3 class="text-lg font-medium text-green-900 dark:text-green-100 mb-3 flex items-center gap-2">
          <Mail class="w-5 h-5" />
          {{ $t('Email Piping Features') }}
        </h3>
        <div class="text-sm text-green-800 dark:text-green-200 space-y-2">
          <p>• {{ $t('Automatic ticket creation from emails') }}</p>
          <p>• {{ $t('Email attachments are included as ticket attachments') }}</p>
          <p>• {{ $t('Email sender becomes the ticket customer') }}</p>
          <p>• {{ $t('Email subject becomes the ticket subject') }}</p>
          <p>• {{ $t('Email body becomes the ticket description') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { 
  Mail, 
  Server, 
  Wifi, 
  Loader2,
  CheckCircle,
  XCircle,
  HelpCircle,
  Clock,
  AlertTriangle
} from 'lucide-vue-next'
import axios from 'axios'

export default {
  components: {
    Head,
    TextInput,
    SelectInput,
    LoadingButton,
    Mail,
    Server,
    Wifi,
    Loader2,
    CheckCircle,
    XCircle,
    HelpCircle,
    Clock,
    AlertTriangle
  },
  layout: Layout,
  props: {
    title: String,
    demo: Boolean,
    keys: Object,
    option: Object,
  },
  remember: 'form',
  data() {
    return {
      testingConnection: false,
      testResult: null,
      basePath: window.location.origin,
      baseUrl: window.location.origin,
      form: this.$inertia.form({
        IMAP_HOST: this.keys.IMAP_HOST || '',
        IMAP_PORT: this.keys.IMAP_PORT || '',
        IMAP_PROTOCOL: this.keys.IMAP_PROTOCOL || '',
        IMAP_ENCRYPTION: this.keys.IMAP_ENCRYPTION || '',
        IMAP_USERNAME: this.keys.IMAP_USERNAME || '',
        IMAP_PASSWORD: this.keys.IMAP_PASSWORD || '',
      })
    }
  },
  methods: {
    update() {
      this.form.post(route('settings.piping.update'))
    },
    updatePipingOption() {
      this.$inertia.put(route('settings.piping.option.update'), {
        value: this.option.value
      })
    },
    async testConnection() {
      this.testingConnection = true
      this.testResult = null

      try {
        const response = await axios.post(route('settings.piping.test'), {
          IMAP_HOST: this.form.IMAP_HOST,
          IMAP_PORT: this.form.IMAP_PORT,
          IMAP_PROTOCOL: this.form.IMAP_PROTOCOL,
          IMAP_ENCRYPTION: this.form.IMAP_ENCRYPTION,
          IMAP_USERNAME: this.form.IMAP_USERNAME,
          IMAP_PASSWORD: this.form.IMAP_PASSWORD,
        })

        this.testResult = {
          success: response.data.success,
          message: response.data.message
        }
      } catch (error) {
        this.testResult = {
          success: false,
          message: error.response?.data?.message || this.$t('Connection test failed. Please check your settings.')
        }
      } finally {
        this.testingConnection = false
      }
    }
  }
}
</script>