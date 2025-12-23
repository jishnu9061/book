<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="title" />
    
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Pusher Settings') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Configure real-time communication settings for live updates and notifications') }}
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
      <!-- Pusher Configuration Form -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <form @submit.prevent="update">
          <div class="p-8">
            <!-- App Configuration -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Zap class="w-5 h-5 text-yellow-600" />
                {{ $t('App Configuration') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput 
                  v-model="form.PUSHER_APP_ID" 
                  :error="form.errors.PUSHER_APP_ID" 
                  :label="$t('Pusher App ID')"
                  :placeholder="$t('Enter your Pusher App ID')"
                  required
                />
                <TextInput 
                  v-model="form.PUSHER_APP_KEY" 
                  :error="form.errors.PUSHER_APP_KEY" 
                  :label="$t('Pusher App Key')"
                  :placeholder="$t('Enter your Pusher App Key')"
                  required
                />
              </div>
            </div>

            <!-- Security -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <Shield class="w-5 h-5 text-red-600" />
                {{ $t('Security') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <TextInput 
                  v-model="form.PUSHER_APP_SECRET" 
                  :error="form.errors.PUSHER_APP_SECRET" 
                  :label="$t('Pusher App Secret')"
                  type="password"
                  :placeholder="$t('Enter your Pusher App Secret')"
                  required
                />
                <TextInput 
                  v-model="form.PUSHER_APP_CLUSTER" 
                  :error="form.errors.PUSHER_APP_CLUSTER" 
                  :label="$t('Pusher App Cluster')"
                  :placeholder="$t('Enter your Pusher App Cluster (e.g., eu, us2)')"
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
              {{ $t('Real-time features will be enabled after saving') }}
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

      <!-- Help Section -->
      <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
          <HelpCircle class="w-5 h-5" />
          {{ $t('Pusher Configuration Help') }}
        </h3>
        <div class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
          <p><strong>{{ $t('Getting Started:') }}</strong> {{ $t('Create a free account at pusher.com and create a new app to get your credentials.') }}</p>
          <p><strong>{{ $t('App ID:') }}</strong> {{ $t('Found in your Pusher app dashboard under "App Keys".') }}</p>
          <p><strong>{{ $t('App Key:') }}</strong> {{ $t('Public key used by your frontend application.') }}</p>
          <p><strong>{{ $t('App Secret:') }}</strong> {{ $t('Private key used by your backend application. Keep this secure!') }}</p>
          <p><strong>{{ $t('Cluster:') }}</strong> {{ $t('Choose the region closest to your users for better performance.') }}</p>
        </div>
      </div>

      <!-- Features Enabled -->
      <div class="mt-8 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 p-6">
        <h3 class="text-lg font-medium text-green-900 dark:text-green-100 mb-3 flex items-center gap-2">
          <Zap class="w-5 h-5" />
          {{ $t('Real-time Features') }}
        </h3>
        <div class="text-sm text-green-800 dark:text-green-200 space-y-2">
          <p>• {{ $t('Live chat messages and typing indicators') }}</p>
          <p>• {{ $t('Real-time ticket updates and notifications') }}</p>
          <p>• {{ $t('Live conversation updates') }}</p>
          <p>• {{ $t('Instant system notifications') }}</p>
          <p>• {{ $t('Real-time dashboard updates') }}</p>
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
  Zap, 
  Shield, 
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
    Zap,
    Shield,
    Wifi,
    Loader2,
    CheckCircle,
    XCircle,
    HelpCircle
  },
  layout: Layout,
  props: {
    title: String,
    keys: Object,
  },
  remember: 'form',
  data() {
    return {
      testingConnection: false,
      testResult: null,
      form: this.$inertia.form({
        PUSHER_APP_ID: this.keys.PUSHER_APP_ID || '',
        PUSHER_APP_KEY: this.keys.PUSHER_APP_KEY || '',
        PUSHER_APP_SECRET: this.keys.PUSHER_APP_SECRET || '',
        PUSHER_APP_CLUSTER: this.keys.PUSHER_APP_CLUSTER || '',
      })
    }
  },
  methods: {
    update() {
      this.form.put(route('settings.pusher.update'))
    },
    async testConnection() {
      this.testingConnection = true
      this.testResult = null

      try {
        const response = await axios.post(route('settings.pusher.test'), {
          PUSHER_APP_ID: this.form.PUSHER_APP_ID,
          PUSHER_APP_KEY: this.form.PUSHER_APP_KEY,
          PUSHER_APP_SECRET: this.form.PUSHER_APP_SECRET,
          PUSHER_APP_CLUSTER: this.form.PUSHER_APP_CLUSTER,
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