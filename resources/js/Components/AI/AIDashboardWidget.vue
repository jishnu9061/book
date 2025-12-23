<template>
  <div class="ai-dashboard-widget bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
          <Sparkles class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI Assistant') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('Powered by OpenAI') }}
          </p>
        </div>
      </div>
      
      <div class="flex items-center space-x-1">
        <div 
          class="w-2 h-2 rounded-full"
          :class="statusColor"
        ></div>
        <span class="text-xs font-medium" :class="statusTextColor">{{ statusText }}</span>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div class="text-center">
        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ aiStats.classifications }}</div>
        <div class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Classifications') }}</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ aiStats.suggestions }}</div>
        <div class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Suggestions') }}</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="space-y-2">
      <button 
        @click="openAISettings"
        class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
      >
        <Settings class="w-4 h-4" />
        <span>{{ $t('AI Settings') }}</span>
      </button>
      
      <button 
        @click="viewAnalytics"
        class="w-full flex items-center justify-center space-x-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors"
      >
        <BarChart3 class="w-4 h-4" />
        <span>{{ $t('View Analytics') }}</span>
      </button>
    </div>

    <!-- Status Message -->
    <div v-if="statusMessage" class="mt-4 p-3 rounded-lg text-sm" :class="statusMessageClass">
      <div class="flex items-center space-x-2">
        <component :is="statusIcon" class="w-4 h-4" />
        <span>{{ statusMessage }}</span>
      </div>
    </div>

    <!-- Feature Toggle -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Auto-classify new tickets') }}</span>
        <button 
          @click="toggleAutoClassify"
          class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
          :class="autoClassifyEnabled ? 'bg-blue-600' : 'bg-slate-200 dark:bg-slate-700'"
        >
          <span 
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
            :class="autoClassifyEnabled ? 'translate-x-6' : 'translate-x-1'"
          ></span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { Sparkles, Settings, BarChart3, CheckCircle, AlertCircle, Clock } from 'lucide-vue-next'
import axios from 'axios'

export default {
  name: 'AIDashboardWidget',
  components: {
    Sparkles,
    Settings,
    BarChart3,
    CheckCircle,
    AlertCircle,
    Clock
  },
  setup() {
    const aiStatus = ref({
      available: false,
      rateLimited: false,
      lastCheck: null
    })
    
    const aiStats = ref({
      classifications: 0,
      suggestions: 0
    })
    
    const autoClassifyEnabled = ref(true)

    const statusColor = computed(() => {
      if (aiStatus.value.rateLimited) return 'bg-yellow-500'
      if (aiStatus.value.available) return 'bg-green-500'
      return 'bg-red-500'
    })

    const statusText = computed(() => {
      if (aiStatus.value.rateLimited) return 'Rate Limited'
      if (aiStatus.value.available) return 'Active'
      return 'Offline'
    })

    const statusTextColor = computed(() => {
      if (aiStatus.value.rateLimited) return 'text-yellow-600 dark:text-yellow-400'
      if (aiStatus.value.available) return 'text-green-600 dark:text-green-400'
      return 'text-red-600 dark:text-red-400'
    })

    const statusMessage = computed(() => {
      if (aiStatus.value.rateLimited) {
        return 'AI temporarily unavailable due to rate limits. Will reset in ~1 hour.'
      }
      if (aiStatus.value.available) {
        return 'AI is ready to help with ticket classification and responses.'
      }
      return 'AI services are currently offline. Check your configuration.'
    })

    const statusMessageClass = computed(() => {
      if (aiStatus.value.rateLimited) {
        return 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300'
      }
      if (aiStatus.value.available) {
        return 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300'
      }
      return 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
    })

    const statusIcon = computed(() => {
      if (aiStatus.value.rateLimited) return Clock
      if (aiStatus.value.available) return CheckCircle
      return AlertCircle
    })

    const checkAIStatus = async () => {
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
        console.error('Failed to check AI status:', error)
        aiStatus.value.available = false
      }
    }

    const loadAIStats = async () => {
      try {
        // Load AI statistics using axios with proper authentication
        const response = await axios.get('/dashboard/ai/analytics', {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })

        if (response.data && response.data.classification) {
          aiStats.value = {
            classifications: response.data.classification.total_classifications || 0,
            suggestions: response.data.classification.applied_classifications || 0
          }
        }
      } catch (error) {
        console.error('Failed to load AI stats:', error)
        // Set default values when AI stats can't be loaded
        aiStats.value = {
          classifications: 0,
          suggestions: 0
        }
      }
    }

    const openAISettings = () => {
      window.location.href = '/dashboard/settings/ai'
    }

    const viewAnalytics = () => {
      // Navigate to AI analytics
      window.location.href = '/dashboard/ai/analytics-page'
    }

    const toggleAutoClassify = () => {
      autoClassifyEnabled.value = !autoClassifyEnabled.value
      // Here you would typically save this setting to the backend
      console.log('Auto-classify toggled:', autoClassifyEnabled.value)
    }

    onMounted(() => {
      checkAIStatus()
      loadAIStats()
    })

    return {
      aiStatus,
      aiStats,
      autoClassifyEnabled,
      statusColor,
      statusText,
      statusTextColor,
      statusMessage,
      statusMessageClass,
      statusIcon,
      openAISettings,
      viewAnalytics,
      toggleAutoClassify
    }
  }
}
</script>
