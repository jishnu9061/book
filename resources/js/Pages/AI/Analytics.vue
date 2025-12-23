<template>
  <Head title="AI Analytics" />
  
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
              <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                <BarChart3 class="w-6 h-6 text-white" />
              </div>
              AI Analytics Dashboard
            </h1>
            <p class="mt-2 text-slate-600 dark:text-slate-400">
              Comprehensive insights into AI performance and ticket classification
            </p>
          </div>
          
          <div class="flex items-center gap-3">
            <button 
              @click="refreshAnalytics"
              :disabled="loading"
              class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors disabled:opacity-50 flex items-center gap-2"
            >
              <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
              Refresh
            </button>
            
            <Link 
              :href="route('dashboard')"
              class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all flex items-center gap-2"
            >
              <ArrowLeft class="w-4 h-4" />
              Back to Dashboard
            </Link>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p class="text-slate-600 dark:text-slate-400">Loading AI analytics...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-8">
        <div class="flex items-center gap-3">
          <AlertCircle class="w-6 h-6 text-red-600 dark:text-red-400" />
          <div>
            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">Error Loading Analytics</h3>
            <p class="text-red-600 dark:text-red-400 mt-1">{{ error }}</p>
          </div>
        </div>
      </div>

      <!-- Analytics Content -->
      <div v-else-if="analytics" class="space-y-8">
        <!-- AI Status Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Classifications</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                  {{ analytics.classification?.total_classifications || 0 }}
                </p>
              </div>
              <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <Brain class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Applied Classifications</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                  {{ analytics.classification?.applied_classifications || 0 }}
                </p>
              </div>
              <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">High Confidence</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                  {{ analytics.classification?.high_confidence_classifications || 0 }}
                </p>
              </div>
              <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                <TrendingUp class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Average Confidence</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                  {{ formatPercentage(analytics.classification?.average_confidence || 0) }}
                </p>
              </div>
              <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                <Target class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>

        <!-- Classification Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Classification Accuracy -->
          <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
              <Target class="w-5 h-5" />
              Classification Accuracy
            </h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">Overall Accuracy</span>
                <span class="text-lg font-semibold text-slate-900 dark:text-white">
                  {{ formatPercentage(analytics.classification?.classification_accuracy || 0) }}
                </span>
              </div>
              <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                <div 
                  class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: (analytics.classification?.classification_accuracy || 0) * 100 + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <!-- AI Generated vs Manual -->
          <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
              <Brain class="w-5 h-5" />
              AI vs Manual Classifications
            </h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">AI Generated</span>
                <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                  {{ analytics.classification?.ai_generated_classifications || 0 }}
                </span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">Manual</span>
                <span class="text-lg font-semibold text-slate-600 dark:text-slate-400">
                  {{ (analytics.classification?.total_classifications || 0) - (analytics.classification?.ai_generated_classifications || 0) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <Clock class="w-5 h-5" />
            Recent Classification Activity
          </h3>
          
          <div v-if="analytics.classification?.recent_activity?.length" class="space-y-4">
            <div 
              v-for="activity in analytics.classification.recent_activity" 
              :key="activity.id"
              class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg"
            >
              <div class="flex items-center gap-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                  <Brain class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                  <p class="font-medium text-slate-900 dark:text-white">
                    Ticket #{{ activity.ticket?.uid || 'N/A' }}
                  </p>
                  <p class="text-sm text-slate-600 dark:text-slate-400">
                    {{ activity.priority?.name || 'Unknown' }} • {{ activity.category?.name || 'Unknown' }}
                  </p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-medium text-slate-900 dark:text-white">
                  {{ formatPercentage(activity.confidence_score) }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                  {{ formatDate(activity.created_at) }}
                </p>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-slate-500 dark:text-slate-400">
            <Brain class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
            <p>No recent classification activity</p>
          </div>
        </div>

        <!-- Predictive Analytics -->
        <div v-if="analytics.predictions" class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <TrendingUp class="w-5 h-5" />
            Predictive Analytics
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <p class="text-sm text-slate-600 dark:text-slate-400">Predicted Ticket Volume</p>
              <p class="text-2xl font-bold text-slate-900 dark:text-white">
                {{ analytics.predictions?.ticket_volume?.predicted || 0 }}
              </p>
            </div>
            <div class="text-center">
              <p class="text-sm text-slate-600 dark:text-slate-400">SLA Compliance</p>
              <p class="text-2xl font-bold text-slate-900 dark:text-white">
                {{ formatPercentage(analytics.predictions?.sla_compliance?.predicted || 0) }}
              </p>
            </div>
            <div class="text-center">
              <p class="text-sm text-slate-600 dark:text-slate-400">Avg Resolution Time</p>
              <p class="text-2xl font-bold text-slate-900 dark:text-white">
                {{ analytics.predictions?.resolution_time?.predicted || 0 }}h
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import {
  BarChart3,
  RefreshCw,
  ArrowLeft,
  AlertCircle,
  Brain,
  CheckCircle,
  TrendingUp,
  Target,
  Clock
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'AIAnalytics',
  layout: Layout,
  components: {
    Head,
    Link,
    BarChart3,
    RefreshCw,
    ArrowLeft,
    AlertCircle,
    Brain,
    CheckCircle,
    TrendingUp,
    Target,
    Clock
  },
  setup() {
    const analytics = ref(null)
    const loading = ref(true)
    const error = ref(null)

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
          error.value = result.message || 'Failed to load analytics'
        }
      } catch (err) {
        console.error('Failed to load AI analytics:', err)
        error.value = 'Failed to load analytics. Please try again.'
      } finally {
        loading.value = false
      }
    }

    const refreshAnalytics = () => {
      loadAnalytics()
    }

    const formatPercentage = (value) => {
      return (value * 100).toFixed(1) + '%'
    }

    const formatDate = (date) => {
      return moment(date).fromNow()
    }

    onMounted(() => {
      loadAnalytics()
    })

    return {
      analytics,
      loading,
      error,
      refreshAnalytics,
      formatPercentage,
      formatDate
    }
  }
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
