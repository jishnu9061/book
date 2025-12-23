<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="title" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('SMTP Settings') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Configure your email server settings for sending notifications and emails') }}
              </p>
            </div>
            <div v-if="!demo" class="flex items-center gap-3">
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

    <!-- Demo Notice -->
    <div v-if="demo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <div class="flex items-center gap-2">
          <div class="w-5 h-5 text-yellow-600">⚠️</div>
          <h4 class="font-medium text-yellow-800 dark:text-yellow-200">
            {{ $t('Demo Mode') }}
          </h4>
        </div>
        <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
          {{ $t('SMTP authentication fields and testing are hidden in demo mode for security reasons.') }}
        </p>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column: SMTP Configuration Form -->
        <div class="xl:col-span-2">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form @submit.prevent="update">
              <div class="p-8">

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

            <!-- Server Configuration -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Server class="w-5 h-5 text-blue-600" />
                {{ $t('Server Configuration') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput
                  v-model="form.MAIL_HOST"
                  :error="form.errors.MAIL_HOST"
                  :label="$t('SMTP Host')"
                  :placeholder="$t('smtp.gmail.com')"
                  required
                />
                <TextInput
                  v-model="form.MAIL_PORT"
                  :error="form.errors.MAIL_PORT"
                  :label="$t('SMTP Port')"
                  :placeholder="$t('587')"
                  type="number"
                  required
                />
              </div>
            </div>

            <!-- Authentication -->
            <div v-if="!demo" class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Shield class="w-5 h-5 text-green-600" />
                {{ $t('Authentication') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput
                  v-model="form.MAIL_USERNAME"
                  :error="form.errors.MAIL_USERNAME"
                  :label="$t('SMTP Username')"
                  :placeholder="$t('your-email@gmail.com')"
                  required
                />
                <TextInput
                  v-model="form.MAIL_PASSWORD"
                  :error="form.errors.MAIL_PASSWORD"
                  :label="$t('SMTP Password')"
                  type="password"
                  :placeholder="$t('Your email password or app password')"
                  required
                />
              </div>
            </div>

            <!-- Security & Encryption -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Lock class="w-5 h-5 text-purple-600" />
                {{ $t('Security & Encryption') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <SelectInput
                  v-model="form.MAIL_ENCRYPTION"
                  :error="form.errors.MAIL_ENCRYPTION"
                  :label="$t('Mail Encryption')"
                  required
                >
                  <option value="none">{{ $t('None') }}</option>
                  <option value="tls">{{ $t('TLS') }}</option>
                  <option value="ssl">{{ $t('SSL') }}</option>
                </SelectInput>
              </div>
            </div>

            <!-- Email Settings -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Mail class="w-5 h-5 text-orange-600" />
                {{ $t('Email Settings') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput
                  v-model="form.MAIL_FROM_ADDRESS"
                  :error="form.errors.MAIL_FROM_ADDRESS"
                  :label="$t('From Address')"
                  :placeholder="$t('noreply@yourdomain.com')"
                  type="email"
                  required
                />
                <TextInput
                  v-model="form.MAIL_FROM_NAME"
                  :error="form.errors.MAIL_FROM_NAME"
                  :label="$t('From Name')"
                  :placeholder="$t('Your Company Name')"
                  required
                />
              </div>
            </div>

          </div>

          <!-- Form Actions -->
          <div class="px-8 py-4 bg-slate-50 dark:bg-slate-700 border-t border-slate-200 dark:border-slate-600 flex items-center justify-between">
            <div class="text-sm text-slate-500 dark:text-slate-400">
              <span v-if="!demo">{{ $t('Changes will be applied immediately after saving') }}</span>
              <span v-else class="text-yellow-600 dark:text-yellow-400">{{ $t('Settings cannot be saved in demo mode') }}</span>
            </div>
            <LoadingButton
              :loading="form.processing"
              :disabled="demo"
              class="btn-indigo"
              type="submit"
            >
              {{ $t('Save Settings') }}
            </LoadingButton>
          </div>
        </form>
      </div>
        </div>

        <!-- Right Column: Help Section -->
        <div class="xl:col-span-1">
          <div class="top-8" style="position: sticky">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
              <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                <HelpCircle class="w-5 h-5" />
                {{ $t('SMTP Configuration Help') }}
              </h3>
              <div class="text-sm text-blue-800 dark:text-blue-200 space-y-3">
                <div>
                  <p class="font-semibold mb-2">{{ $t('Gmail Configuration:') }}</p>
                  <div class="ml-4 space-y-2">
                    <p><strong>{{ $t('Server Settings:') }}</strong></p>
                    <ul class="ml-4 space-y-1 list-disc">
                      <li>{{ $t('SMTP Host:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">smtp.gmail.com</code></li>
                      <li>{{ $t('Port:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">587</code> ({{ $t('TLS') }}) {{ $t('or') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">465</code> ({{ $t('SSL') }})</li>
                      <li>{{ $t('Encryption:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">TLS</code> {{ $t('or') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">SSL</code></li>
                      <li>{{ $t('Username:') }} {{ $t('Your full Gmail address (e.g., yourname@gmail.com)') }}</li>
                    </ul>

                    <p class="font-semibold mt-3">{{ $t('App Password Setup:') }}</p>
                    <ol class="ml-4 space-y-1 list-decimal">
                      <li>{{ $t('Enable 2-Factor Authentication in your Google Account') }}</li>
                      <li>{{ $t('Go to Google Account Settings > Security > App passwords') }}</li>
                      <li>{{ $t('Generate a new app password for "Mail"') }}</li>
                      <li>{{ $t('Copy this password (without spaces) and paste it directly into the password field of the application you are setting up.') }}</li>
                    </ol>

                    <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded">
                      <p class="text-yellow-800 dark:text-yellow-200"><strong>{{ $t('Important:') }}</strong> {{ $t('Use the App Password (16 characters), not your regular Gmail password. The App Password should contain only letters and numbers, no spaces or special characters.') }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <p class="font-semibold mb-2">{{ $t('Outlook Configuration:') }}</p>
                  <div class="ml-4 space-y-2">
                    <p><strong>{{ $t('Server Settings:') }}</strong></p>
                    <ul class="ml-4 space-y-1 list-disc">
                      <li>{{ $t('SMTP Host:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">smtp-mail.outlook.com</code></li>
                      <li>{{ $t('Port:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">587</code></li>
                      <li>{{ $t('Encryption:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">TLS</code></li>
                      <li>{{ $t('Username:') }} {{ $t('Your full Outlook email address (e.g., yourname@outlook.com)') }}</li>
                      <li>{{ $t('Password:') }} {{ $t('Your Outlook account password') }}</li>
                    </ul>

                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                      <p class="text-blue-800 dark:text-blue-200"><strong>{{ $t('Note:') }}</strong> {{ $t('For Outlook.com, Hotmail.com, and Live.com accounts, use your regular account password. 2-Factor Authentication is recommended for security.') }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <p class="font-semibold mb-2">{{ $t('Yahoo Configuration:') }}</p>
                  <div class="ml-4 space-y-2">
                    <p><strong>{{ $t('Server Settings:') }}</strong></p>
                    <ul class="ml-4 space-y-1 list-disc">
                      <li>{{ $t('SMTP Host:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">smtp.mail.yahoo.com</code></li>
                      <li>{{ $t('Port:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">587</code> ({{ $t('TLS') }}) {{ $t('or') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">465</code> ({{ $t('SSL') }})</li>
                      <li>{{ $t('Encryption:') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">TLS</code> {{ $t('or') }} <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">SSL</code></li>
                      <li>{{ $t('Username:') }} {{ $t('Your full Yahoo email address (e.g., yourname@yahoo.com)') }}</li>
                    </ul>

                    <p class="font-semibold mt-3">{{ $t('App Password Setup:') }}</p>
                    <ol class="ml-4 space-y-1 list-decimal">
                      <li>{{ $t('Enable 2-Factor Authentication in your Yahoo Account') }}</li>
                      <li>{{ $t('Go to Yahoo Account Security > Generate and manage app passwords') }}</li>
                      <li>{{ $t('Generate a new app password for "Mail"') }}</li>
                      <li>{{ $t('Copy this password (without spaces) and paste it directly into the password field of the application you are setting up.') }}</li>
                    </ol>

                    <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded">
                      <p class="text-yellow-800 dark:text-yellow-200"><strong>{{ $t('Important:') }}</strong> {{ $t('Yahoo requires an App Password for SMTP access. Use the App Password, not your regular Yahoo password.') }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <p class="font-semibold mb-2">{{ $t('Custom SMTP Providers:') }}</p>
                  <div class="ml-4 space-y-2">
                    <p><strong>{{ $t('Common SMTP Providers:') }}</strong></p>
                    <ul class="ml-4 space-y-1 list-disc">
                      <li><strong>{{ $t('Mailgun:') }}</strong> <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">smtp.mailgun.org</code> ({{ $t('Port 587, TLS') }})</li>
                      <li><strong>{{ $t('SendGrid:') }}</strong> <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">smtp.sendgrid.net</code> ({{ $t('Port 587, TLS') }})</li>
                      <li><strong>{{ $t('Amazon SES:') }}</strong> <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">email-smtp.region.amazonaws.com</code> ({{ $t('Port 587, TLS') }})</li>
                      <li><strong>{{ $t('Postmark:') }}</strong> <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">smtp.postmarkapp.com</code> ({{ $t('Port 587, TLS') }})</li>
                    </ul>

                    <p class="font-semibold mt-3">{{ $t('General Configuration Steps:') }}</p>
                    <ol class="ml-4 space-y-1 list-decimal">
                      <li>{{ $t('Contact your email provider or hosting company for SMTP settings') }}</li>
                      <li>{{ $t('Obtain the SMTP server hostname, port, and encryption type') }}</li>
                      <li>{{ $t('Get your email credentials (username and password)') }}</li>
                      <li>{{ $t('Test the connection using the Test Connection button') }}</li>
                    </ol>

                    <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded">
                      <p class="text-green-800 dark:text-green-200"><strong>{{ $t('Tip:') }}</strong> {{ $t('Most modern email providers use port 587 with TLS encryption. If you encounter issues, try port 465 with SSL encryption as an alternative.') }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
  Server,
  Shield,
  Lock,
  Mail,
  Wifi,
  Loader2,
  CheckCircle,
  XCircle,
  HelpCircle
} from 'lucide-vue-next'
import axios from 'axios'

export default {
  components: {
    Head,
    TextInput,
    SelectInput,
    LoadingButton,
    Server,
    Shield,
    Lock,
    Mail,
    Wifi,
    Loader2,
    CheckCircle,
    XCircle,
    HelpCircle
  },
  layout: Layout,
  remember: 'form',
  props: {
    title: String,
    demo: Boolean,
    keys: Object,
  },
  data() {
    return {
      testingConnection: false,
      testResult: null,
      form: this.$inertia.form({
        MAIL_HOST: this.keys.MAIL_HOST || '',
        MAIL_PORT: this.keys.MAIL_PORT || '',
        MAIL_USERNAME: this.keys.MAIL_USERNAME || '',
        MAIL_PASSWORD: this.keys.MAIL_PASSWORD || '',
        MAIL_ENCRYPTION: this.keys.MAIL_ENCRYPTION || 'tls',
        MAIL_FROM_ADDRESS: this.keys.MAIL_FROM_ADDRESS || '',
        MAIL_FROM_NAME: this.keys.MAIL_FROM_NAME || '',
      })
    }
  },
  methods: {
    update() {
      this.form.post(route('settings.smtp.update'))
    },
    async testConnection() {
      this.testingConnection = true
      this.testResult = null

      try {
        const response = await axios.post(route('settings.smtp.test'), {
          MAIL_HOST: this.form.MAIL_HOST,
          MAIL_PORT: this.form.MAIL_PORT,
          MAIL_USERNAME: this.form.MAIL_USERNAME,
          MAIL_PASSWORD: this.form.MAIL_PASSWORD,
          MAIL_ENCRYPTION: this.form.MAIL_ENCRYPTION,
          MAIL_FROM_ADDRESS: this.form.MAIL_FROM_ADDRESS,
          MAIL_FROM_NAME: this.form.MAIL_FROM_NAME,
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
