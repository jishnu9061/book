<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head title="AI Settings" />

    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl">
                <Sparkles class="w-8 h-8 text-white" />
              </div>
              <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                  {{ $t('AI Settings') }}
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-1">
                  {{ $t('Configure AI-powered features for your helpdesk system') }}
                </p>
              </div>
            </div>
            
            <!-- AI Status Indicator -->
            <div class="flex items-center space-x-3">
              <div v-if="aiStatus.available" class="flex items-center space-x-2 text-green-600 dark:text-green-400">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium">{{ $t('AI Active') }}</span>
              </div>
              <div v-else class="flex items-center space-x-2 text-red-600 dark:text-red-400">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <span class="text-sm font-medium">{{ $t('AI Offline') }}</span>
              </div>
              <button 
                @click="checkAIStatus"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                :title="$t('Refresh AI Status')"
              >
                <RefreshCw class="w-5 h-5" :class="{ 'animate-spin': checkingStatus }" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="saveSettings" class="space-y-8">
        
        <!-- General AI Settings -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
              {{ $t('General AI Settings') }}
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
              {{ $t('Configure basic AI functionality and API settings') }}
            </p>
          </div>
          
          <div class="p-6 space-y-6">
            <!-- AI Enabled Toggle -->
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white">
                  {{ $t('Enable AI Features') }}
                </h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                  {{ $t('Turn on AI-powered features for your helpdesk system') }}
                </p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="form.ai_enabled"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
              </label>
            </div>

            <!-- OpenAI API Key -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('OpenAI API Key') }}
              </label>
              <div class="relative">
                <input 
                  type="password"
                  v-model="form.openai_api_key"
                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :placeholder="$t('Enter your OpenAI API key')"
                />
                <button 
                  type="button"
                  @click="toggleApiKeyVisibility"
                  class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                >
                  <Eye v-if="!showApiKey" class="w-5 h-5" />
                  <EyeOff v-else class="w-5 h-5" />
                </button>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('Get your API key from') }} <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:text-blue-700">OpenAI Platform</a>
              </p>
            </div>

            <!-- OpenAI Model -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('OpenAI Model') }}
              </label>
              <select 
                v-model="form.openai_model"
                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Recommended)</option>
                <option value="gpt-4">GPT-4 (Higher Accuracy)</option>
                <option value="gpt-4-turbo">GPT-4 Turbo (Latest)</option>
              </select>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('GPT-3.5 Turbo is cost-effective, GPT-4 provides higher accuracy') }}
              </p>
            </div>

            <!-- Max Tokens -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Max Tokens') }}
              </label>
              <input 
                type="number"
                v-model="form.openai_max_tokens"
                min="100"
                max="4000"
                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :placeholder="$t('Maximum tokens per request')"
              />
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('Higher values allow more detailed responses but cost more') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Classification Settings -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
              {{ $t('Ticket Classification') }}
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
              {{ $t('Configure AI-powered ticket classification settings') }}
            </p>
          </div>
          
          <div class="p-6 space-y-6">
            <!-- Auto Classify New Tickets -->
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-slate-900 dark:text-white">
                  {{ $t('Auto-classify New Tickets') }}
                </h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                  {{ $t('Automatically classify tickets when they are created') }}
                </p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="form.auto_classify_new_tickets"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
              </label>
            </div>

            <!-- Confidence Threshold -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Confidence Threshold') }}
              </label>
              <div class="flex items-center space-x-4">
                <input 
                  type="range"
                  v-model="form.confidence_threshold"
                  min="0.1"
                  max="1.0"
                  step="0.1"
                  class="flex-1 h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700"
                />
                <span class="text-lg font-semibold text-slate-900 dark:text-white min-w-[3rem]">
                  {{ Math.round(form.confidence_threshold * 100) }}%
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('Only apply suggestions above this confidence level') }}
              </p>
            </div>

            <!-- Cache Duration -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Cache Duration (seconds)') }}
              </label>
              <input 
                type="number"
                v-model="form.cache_duration"
                min="300"
                max="86400"
                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :placeholder="$t('How long to cache AI results')"
              />
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('Caching reduces API calls and costs for similar tickets') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Performance Settings -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
              {{ $t('Performance & Limits') }}
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
              {{ $t('Configure rate limits and performance settings') }}
            </p>
          </div>
          
          <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Rate Limit Per Minute -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  {{ $t('Rate Limit (per minute)') }}
                </label>
                <input 
                  type="number"
                  v-model="form.rate_limit_per_minute"
                  min="1"
                  max="100"
                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>

              <!-- Rate Limit Per Hour -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  {{ $t('Rate Limit (per hour)') }}
                </label>
                <input 
                  type="number"
                  v-model="form.rate_limit_per_hour"
                  min="10"
                  max="2000"
                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>

              <!-- Batch Size -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  {{ $t('Batch Size') }}
                </label>
                <input 
                  type="number"
                  v-model="form.batch_size"
                  min="1"
                  max="50"
                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  {{ $t('Number of tickets to process in batch operations') }}
                </p>
              </div>

              <!-- Timeout -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  {{ $t('Request Timeout (seconds)') }}
                </label>
                <input 
                  type="number"
                  v-model="form.timeout"
                  min="5"
                  max="120"
                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- AI Analytics -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
          <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
              {{ $t('AI Analytics') }}
            </h2>
            <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
              {{ $t('View AI performance metrics and insights') }}
            </p>
          </div>
          
          <div class="p-6">
            <AIAnalyticsDashboard />
          </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end space-x-4">
          <button 
            type="button"
            @click="resetForm"
            class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 font-medium transition-colors duration-200"
          >
            {{ $t('Reset') }}
          </button>
          <loading-button 
            :loading="form.processing"
            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            type="submit"
          >
            {{ $t('Save Settings') }}
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import AIAnalyticsDashboard from '@/Components/AI/AIAnalyticsDashboard.vue'
import { 
  Sparkles, 
  RefreshCw, 
  Eye, 
  EyeOff 
} from 'lucide-vue-next'

export default {
  name: 'AISettings',
  components: {
    Head,
    Layout,
    LoadingButton,
    AIAnalyticsDashboard,
    Sparkles,
    RefreshCw,
    Eye,
    EyeOff
  },
  layout: Layout,
  setup() {
    const form = ref({
      processing: false,
      ai_enabled: false,
      openai_api_key: '',
      openai_model: 'gpt-3.5-turbo',
      openai_max_tokens: 500,
      auto_classify_new_tickets: true,
      confidence_threshold: 0.7,
      cache_duration: 3600,
      rate_limit_per_minute: 60,
      rate_limit_per_hour: 1000,
      batch_size: 10,
      timeout: 30
    })

    const aiStatus = ref({ available: false })
    const checkingStatus = ref(false)
    const showApiKey = ref(false)

    const loadSettings = async () => {
      try {
        // Load current settings from the backend
        const response = await fetch('/dashboard/ai/settings', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        if (response.ok) {
          const result = await response.json()
          if (result.success) {
            Object.assign(form.value, result.data)
          }
        }
      } catch (error) {
        console.error('Error loading AI settings:', error)
      }
    }

    const saveSettings = async () => {
      form.value.processing = true
      
      try {
        const response = await fetch('/dashboard/ai/settings', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(form.value)
        })

        const result = await response.json()

        if (result.success) {
          // Show success message
          console.log('AI settings saved successfully')
        } else {
          throw new Error(result.message || 'Failed to save settings')
        }
      } catch (error) {
        console.error('Error saving AI settings:', error)
      } finally {
        form.value.processing = false
      }
    }

    const checkAIStatus = async () => {
      checkingStatus.value = true
      
      try {
        const response = await fetch('/dashboard/ai/status', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        const result = await response.json()
        if (result.success) {
          aiStatus.value = result.data
        }
      } catch (error) {
        console.error('Error checking AI status:', error)
      } finally {
        checkingStatus.value = false
      }
    }

    const resetForm = () => {
      // Reset to default values
      form.value = {
        processing: false,
        ai_enabled: false,
        openai_api_key: '',
        openai_model: 'gpt-3.5-turbo',
        openai_max_tokens: 500,
        auto_classify_new_tickets: true,
        confidence_threshold: 0.7,
        cache_duration: 3600,
        rate_limit_per_minute: 60,
        rate_limit_per_hour: 1000,
        batch_size: 10,
        timeout: 30
      }
    }

    const toggleApiKeyVisibility = () => {
      showApiKey.value = !showApiKey.value
    }

    onMounted(() => {
      loadSettings()
      checkAIStatus()
    })

    return {
      form,
      aiStatus,
      checkingStatus,
      showApiKey,
      saveSettings,
      checkAIStatus,
      resetForm,
      toggleApiKeyVisibility
    }
  }
}
</script>
