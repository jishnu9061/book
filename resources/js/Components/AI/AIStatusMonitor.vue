<template>
  <div class="ai-status-monitor bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-slate-800 dark:to-slate-900 border border-blue-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
          <Sparkles class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI System Status') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('Real-time AI service monitoring') }}
          </p>
        </div>
      </div>
      
      <button 
        @click="checkStatus"
        :disabled="checking"
        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors disabled:opacity-50"
        :title="$t('Refresh Status')"
      >
        <RefreshCw class="w-5 h-5" :class="{ 'animate-spin': checking }" />
      </button>
    </div>

    <!-- Status Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Overall Status -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Overall Status') }}</span>
          <div class="flex items-center space-x-1">
            <div 
              class="w-2 h-2 rounded-full"
              :class="statusColor"
            ></div>
            <span class="text-xs font-medium" :class="statusTextColor">{{ statusText }}</span>
          </div>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-500">{{ statusDescription }}</p>
      </div>

      <!-- Rate Limit Status -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Rate Limits') }}</span>
          <span class="text-xs text-slate-500 dark:text-slate-500">{{ rateLimitUsage }}</span>
        </div>
        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
          <div 
            class="h-2 rounded-full transition-all duration-300"
            :class="rateLimitBarColor"
            :style="{ width: rateLimitPercentage + '%' }"
          ></div>
        </div>
      </div>

      <!-- API Status -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('OpenAI API') }}</span>
          <div class="flex items-center space-x-1">
            <div 
              class="w-2 h-2 rounded-full"
              :class="apiStatusColor"
            ></div>
            <span class="text-xs font-medium" :class="apiStatusTextColor">{{ apiStatusText }}</span>
          </div>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-500">{{ apiStatusDescription }}</p>
      </div>

      <!-- Last Check -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Last Check') }}</span>
          <span class="text-xs text-slate-500 dark:text-slate-500">{{ lastCheckTime }}</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-500">{{ $t('Auto-refresh every 5 minutes') }}</p>
      </div>
    </div>

    <!-- Features Status -->
    <div class="mt-4">
      <h4 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-3">{{ $t('AI Features') }}</h4>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div 
          v-for="feature in features" 
          :key="feature.name"
          class="flex items-center space-x-2 p-2 rounded-lg"
          :class="feature.enabled ? 'bg-green-50 dark:bg-green-900/20' : 'bg-slate-50 dark:bg-slate-700'"
        >
          <div 
            class="w-2 h-2 rounded-full"
            :class="feature.enabled ? 'bg-green-500' : 'bg-slate-400'"
          ></div>
          <span class="text-xs font-medium" :class="feature.enabled ? 'text-green-700 dark:text-green-300' : 'text-slate-500'">
            {{ feature.name }}
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Quick Actions') }}</span>
        <div class="flex items-center space-x-2">
          <button 
            @click="openAISettings"
            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline"
          >
            {{ $t('AI Settings') }}
          </button>
          <span class="text-slate-300 dark:text-slate-600">•</span>
          <button 
            @click="viewAnalytics"
            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 underline"
          >
            {{ $t('Analytics') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Sparkles, RefreshCw } from 'lucide-vue-next'

export default {
  name: 'AIStatusMonitor',
  components: {
    Sparkles,
    RefreshCw
  },
  setup() {
    const status = ref({
      available: false,
      rateLimited: false,
      lastCheck: null,
      rateLimitUsage: { minute: 0, hour: 0 },
      features: {
        classification: true,
        responseSuggestions: true,
        sentimentAnalysis: true,
        knowledgeBase: true
      }
    })
    
    const checking = ref(false)
    const refreshInterval = ref(null)

    const statusColor = computed(() => {
      if (status.value.rateLimited) return 'bg-yellow-500'
      if (status.value.available) return 'bg-green-500'
      return 'bg-red-500'
    })

    const statusText = computed(() => {
      if (status.value.rateLimited) return 'Rate Limited'
      if (status.value.available) return 'Active'
      return 'Offline'
    })

    const statusTextColor = computed(() => {
      if (status.value.rateLimited) return 'text-yellow-600 dark:text-yellow-400'
      if (status.value.available) return 'text-green-600 dark:text-green-400'
      return 'text-red-600 dark:text-red-400'
    })

    const statusDescription = computed(() => {
      if (status.value.rateLimited) return 'AI temporarily unavailable due to rate limits'
      if (status.value.available) return 'All AI services are operational'
      return 'AI services are currently offline'
    })

    const rateLimitUsage = computed(() => {
      const usage = status.value.rateLimitUsage || { minute: 0, hour: 0 }
      return `${usage.minute || 0}/20 min, ${usage.hour || 0}/300 hr`
    })

    const rateLimitPercentage = computed(() => {
      const usage = status.value.rateLimitUsage || { minute: 0, hour: 0 }
      const minutePercent = ((usage.minute || 0) / 20) * 100
      const hourPercent = ((usage.hour || 0) / 300) * 100
      return Math.max(minutePercent, hourPercent)
    })

    const rateLimitBarColor = computed(() => {
      const percentage = rateLimitPercentage.value
      if (percentage >= 90) return 'bg-red-500'
      if (percentage >= 70) return 'bg-yellow-500'
      return 'bg-green-500'
    })

    const apiStatusColor = computed(() => {
      if (status.value.rateLimited) return 'bg-yellow-500'
      if (status.value.available) return 'bg-green-500'
      return 'bg-red-500'
    })

    const apiStatusText = computed(() => {
      if (status.value.rateLimited) return 'Limited'
      if (status.value.available) return 'Connected'
      return 'Disconnected'
    })

    const apiStatusTextColor = computed(() => {
      if (status.value.rateLimited) return 'text-yellow-600 dark:text-yellow-400'
      if (status.value.available) return 'text-green-600 dark:text-green-400'
      return 'text-red-600 dark:text-red-400'
    })

    const apiStatusDescription = computed(() => {
      if (status.value.rateLimited) return 'Rate limits exceeded, will reset soon'
      if (status.value.available) return 'OpenAI API is responding normally'
      return 'Unable to connect to OpenAI API'
    })

    const lastCheckTime = computed(() => {
      if (!status.value.lastCheck) return 'Never'
      return new Date(status.value.lastCheck).toLocaleTimeString()
    })

    const features = computed(() => {
      const features = status.value.features || {
        classification: true,
        responseSuggestions: true,
        sentimentAnalysis: true,
        knowledgeBase: true
      }
      return [
        { name: 'Classification', enabled: features.classification },
        { name: 'Responses', enabled: features.responseSuggestions },
        { name: 'Sentiment', enabled: features.sentimentAnalysis },
        { name: 'Knowledge', enabled: features.knowledgeBase }
      ]
    })

    const checkStatus = async () => {
      checking.value = true
      
      try {
        const response = await fetch('/dashboard/ai/status', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        const result = await response.json()
        if (result.success && result.data) {
          status.value = {
            available: result.data.available || false,
            rateLimited: result.data.rateLimited || false,
            rateLimitUsage: result.data.rateLimitUsage || { minute: 0, hour: 0 },
            features: result.data.features || {
              classification: true,
              responseSuggestions: true,
              sentimentAnalysis: true,
              knowledgeBase: true
            },
            lastCheck: new Date().toISOString()
          }
        } else {
          // Fallback to default status if API call fails
          status.value = {
            ...status.value,
            available: false,
            lastCheck: new Date().toISOString()
          }
        }
      } catch (error) {
        console.error('Failed to check AI status:', error)
        status.value = {
          ...status.value,
          available: false,
          lastCheck: new Date().toISOString()
        }
      } finally {
        checking.value = false
      }
    }

    const openAISettings = () => {
      window.location.href = '/dashboard/settings/ai'
    }

    const viewAnalytics = () => {
      // Navigate to AI analytics
      window.location.href = '/dashboard/ai/analytics-page'
    }

    onMounted(() => {
      checkStatus()
      // Auto-refresh every 5 minutes
      refreshInterval.value = setInterval(checkStatus, 5 * 60 * 1000)
    })

    onUnmounted(() => {
      if (refreshInterval.value) {
        clearInterval(refreshInterval.value)
      }
    })

    return {
      status,
      checking,
      statusColor,
      statusText,
      statusTextColor,
      statusDescription,
      rateLimitUsage,
      rateLimitPercentage,
      rateLimitBarColor,
      apiStatusColor,
      apiStatusText,
      apiStatusTextColor,
      apiStatusDescription,
      lastCheckTime,
      features,
      checkStatus,
      openAISettings,
      viewAnalytics
    }
  }
}
</script>
