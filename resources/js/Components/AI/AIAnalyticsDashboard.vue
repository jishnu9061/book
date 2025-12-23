<template>
  <div class="ai-analytics-dashboard bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="p-2 bg-white/20 rounded-lg">
            <BarChart3 class="w-6 h-6 text-white" />
          </div>
          <div>
            <h3 class="text-lg font-semibold text-white">{{ $t('AI Analytics Dashboard') }}</h3>
            <p class="text-blue-100 text-sm">{{ $t('AI performance metrics and insights') }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-2">
          <div v-if="aiStatus.available" class="flex items-center space-x-1 text-green-200">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="text-xs font-medium">{{ $t('AI Active') }}</span>
          </div>
          <div v-else class="flex items-center space-x-1 text-red-200">
            <div class="w-2 h-2 bg-red-400 rounded-full"></div>
            <span class="text-xs font-medium">{{ $t('AI Offline') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="flex items-center space-x-3">
          <Loader2 class="w-8 h-8 text-blue-500 animate-spin" />
          <span class="text-slate-600 dark:text-slate-400">{{ $t('Loading AI analytics...') }}</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
        <div class="flex items-center space-x-3">
          <AlertCircle class="w-6 h-6 text-red-500" />
          <div>
            <h4 class="text-red-700 dark:text-red-300 font-medium">{{ $t('Failed to Load Analytics') }}</h4>
            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ error }}</p>
          </div>
        </div>
        <button 
          @click="loadAnalytics"
          class="mt-4 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 underline"
        >
          {{ $t('Try Again') }}
        </button>
      </div>

      <!-- Analytics Content -->
      <div v-else-if="analytics" class="space-y-6">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Total Classifications -->
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-slate-700 dark:to-slate-600 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-600 dark:text-blue-400 text-sm font-medium">{{ $t('Total Classifications') }}</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ analytics.total_classifications }}</p>
              </div>
              <div class="p-3 bg-blue-200 dark:bg-blue-800 rounded-lg">
                <Target class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
          </div>

          <!-- High Confidence -->
          <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-slate-700 dark:to-slate-600 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-green-600 dark:text-green-400 text-sm font-medium">{{ $t('High Confidence') }}</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ analytics.high_confidence_classifications }}</p>
                <p class="text-xs text-green-700 dark:text-green-300">
                  {{ Math.round((analytics.high_confidence_classifications / analytics.total_classifications) * 100) }}% of total
                </p>
              </div>
              <div class="p-3 bg-green-200 dark:bg-green-800 rounded-lg">
                <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>

          <!-- Applied Classifications -->
          <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-slate-700 dark:to-slate-600 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-600 dark:text-purple-400 text-sm font-medium">{{ $t('Applied') }}</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ analytics.applied_classifications }}</p>
                <p class="text-xs text-purple-700 dark:text-purple-300">
                  {{ Math.round((analytics.applied_classifications / analytics.total_classifications) * 100) }}% adoption rate
                </p>
              </div>
              <div class="p-3 bg-purple-200 dark:bg-purple-800 rounded-lg">
                <Check class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>

          <!-- Average Confidence -->
          <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-slate-700 dark:to-slate-600 rounded-lg p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-600 dark:text-orange-400 text-sm font-medium">{{ $t('Avg Confidence') }}</p>
                <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">
                  {{ Math.round(analytics.average_confidence * 100) }}%
                </p>
                <p class="text-xs text-orange-700 dark:text-orange-300">
                  {{ getConfidenceLevel(analytics.average_confidence) }}
                </p>
              </div>
              <div class="p-3 bg-orange-200 dark:bg-orange-800 rounded-lg">
                <TrendingUp class="w-6 h-6 text-orange-600 dark:text-orange-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Accuracy and Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Classification Accuracy -->
          <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Classification Accuracy') }}</h4>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Overall Accuracy') }}</span>
              <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ Math.round(analytics.classification_accuracy * 100) }}%
              </span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-600 rounded-full h-3">
              <div 
                class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-1000"
                :style="{ width: (analytics.classification_accuracy * 100) + '%' }"
              ></div>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
              {{ $t('Based on manual corrections and feedback') }}
            </p>
          </div>

          <!-- AI vs Manual -->
          <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('AI vs Manual Classifications') }}</h4>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                  <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('AI Generated') }}</span>
                </div>
                <span class="text-sm font-semibold text-slate-900 dark:text-white">
                  {{ analytics.ai_generated_classifications }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <div class="w-3 h-3 bg-slate-400 rounded-full"></div>
                  <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Manual') }}</span>
                </div>
                <span class="text-sm font-semibold text-slate-900 dark:text-white">
                  {{ analytics.total_classifications - analytics.ai_generated_classifications }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-6">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Recent AI Activity') }}</h4>
          <div v-if="analytics.recent_activity && analytics.recent_activity.length > 0" class="space-y-3">
            <div 
              v-for="activity in analytics.recent_activity" 
              :key="activity.id"
              class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600"
            >
              <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                  <Sparkles class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-900 dark:text-white">
                    {{ $t('Ticket') }} #{{ activity.ticket?.uid }}
                  </p>
                  <p class="text-xs text-slate-600 dark:text-slate-400">
                    {{ activity.priority?.name }} • {{ activity.category?.name }} • {{ activity.department?.name }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <div class="flex items-center space-x-2">
                  <span class="text-xs px-2 py-1 rounded-full" :class="getConfidenceBadgeClass(activity.confidence_score)">
                    {{ Math.round(activity.confidence_score * 100) }}%
                  </span>
                  <span class="text-xs text-slate-500">
                    {{ formatDate(activity.created_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <div class="p-3 bg-slate-100 dark:bg-slate-600 rounded-full w-12 h-12 mx-auto mb-3 flex items-center justify-center">
              <Activity class="w-6 h-6 text-slate-400" />
            </div>
            <p class="text-slate-600 dark:text-slate-400">{{ $t('No recent AI activity') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { 
  BarChart3, 
  Loader2, 
  AlertCircle, 
  Target, 
  CheckCircle, 
  Check, 
  TrendingUp, 
  Sparkles, 
  Activity 
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'AIAnalyticsDashboard',
  components: {
    BarChart3,
    Loader2,
    AlertCircle,
    Target,
    CheckCircle,
    Check,
    TrendingUp,
    Sparkles,
    Activity
  },
  setup() {
    const loading = ref(false)
    const error = ref(null)
    const analytics = ref(null)
    const aiStatus = ref({ available: false })

    const loadAnalytics = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await fetch('/dashboard/ai/analytics', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        const result = await response.json()

        if (result.success) {
          analytics.value = result.data
        } else {
          throw new Error(result.message || 'Failed to load analytics')
        }
      } catch (err) {
        error.value = err.message
        console.error('Error loading analytics:', err)
      } finally {
        loading.value = false
      }
    }

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
      } catch (err) {
        console.error('Error checking AI status:', err)
      }
    }

    const getConfidenceLevel = (confidence) => {
      if (confidence >= 0.9) return 'Excellent'
      if (confidence >= 0.8) return 'Very Good'
      if (confidence >= 0.7) return 'Good'
      if (confidence >= 0.6) return 'Fair'
      return 'Poor'
    }

    const getConfidenceBadgeClass = (confidence) => {
      if (confidence >= 0.8) return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
      if (confidence >= 0.6) return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
      return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
    }

    const formatDate = (date) => {
      return moment(date).fromNow()
    }

    onMounted(() => {
      checkAIStatus()
      loadAnalytics()
    })

    return {
      loading,
      error,
      analytics,
      aiStatus,
      loadAnalytics,
      getConfidenceLevel,
      getConfidenceBadgeClass,
      formatDate
    }
  }
}
</script>
