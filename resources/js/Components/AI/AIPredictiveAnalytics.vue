<template>
  <div class="ai-predictive-analytics bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800 dark:to-slate-900 border border-indigo-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
          <TrendingUp class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI Predictive Analytics') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('Forecast trends and optimize helpdesk performance') }}
          </p>
        </div>
      </div>
      
      <div class="flex items-center space-x-2">
        <select 
          v-model="selectedPeriod"
          @change="loadAnalytics"
          class="text-sm border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-1.5 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
          <option value="7">{{ $t('Last 7 days') }}</option>
          <option value="14">{{ $t('Last 14 days') }}</option>
          <option value="30">{{ $t('Last 30 days') }}</option>
        </select>
        
        <button 
          @click="loadAnalytics"
          class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
          :title="$t('Refresh Analytics')"
        >
          <RefreshCw class="w-5 h-5" :class="{ 'animate-spin': loading }" />
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="flex items-center space-x-3">
        <Loader2 class="w-6 h-6 text-indigo-500 animate-spin" />
        <span class="text-slate-600 dark:text-slate-400">{{ $t('Generating predictive analytics...') }}</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
      <div class="flex items-center space-x-2">
        <AlertCircle class="w-5 h-5 text-red-500" />
        <span class="text-red-700 dark:text-red-300 font-medium">{{ $t('Analytics Generation Failed') }}</span>
      </div>
      <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ error }}</p>
      <button 
        @click="loadAnalytics"
        class="mt-3 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 underline"
      >
        {{ $t('Try Again') }}
      </button>
    </div>

    <!-- Analytics Content -->
    <div v-else-if="analytics" class="space-y-6">
      <!-- Ticket Volume Predictions -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Ticket Volume Forecast') }}</h4>
          <div class="flex items-center space-x-2">
            <span class="text-sm font-medium" :class="getTrendColorClass(analytics.ticket_volume.current_period.trend)">
              {{ getTrendLabel(analytics.ticket_volume.current_period.trend) }}
            </span>
            <span class="text-sm text-slate-500">
              {{ analytics.ticket_volume.current_period.trend_percentage > 0 ? '+' : '' }}{{ analytics.ticket_volume.current_period.trend_percentage }}%
            </span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $t('Total Tickets') }}</h5>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ analytics.ticket_volume.current_period.total_tickets }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $t('Daily Average') }}</h5>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ analytics.ticket_volume.current_period.average_daily }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $t('Next 7 Days') }}</h5>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">
              {{ Math.round(Object.values(analytics.ticket_volume.predictions).reduce((a, b) => a + b, 0) / 7) }}
            </p>
          </div>
        </div>
      </div>

      <!-- SLA Compliance Predictions -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('SLA Compliance Forecast') }}</h4>
          <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getSlaRiskClass(analytics.sla_compliance.risk_level)">
              {{ getSlaRiskLabel(analytics.sla_compliance.risk_level) }}
            </span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Current Compliance') }}</span>
              <span class="text-lg font-bold text-slate-900 dark:text-white">{{ analytics.sla_compliance.current_compliance }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
              <div 
                class="h-2 rounded-full transition-all duration-500"
                :class="getSlaComplianceBarClass(analytics.sla_compliance.current_compliance)"
                :style="{ width: analytics.sla_compliance.current_compliance + '%' }"
              ></div>
            </div>
          </div>
          
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Predicted Compliance') }}</span>
              <span class="text-lg font-bold text-slate-900 dark:text-white">{{ analytics.sla_compliance.predicted_compliance }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
              <div 
                class="h-2 rounded-full transition-all duration-500"
                :class="getSlaComplianceBarClass(analytics.sla_compliance.predicted_compliance)"
                :style="{ width: analytics.sla_compliance.predicted_compliance + '%' }"
              ></div>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ $t('Recommendations') }}</h5>
          <ul class="space-y-1">
            <li v-for="recommendation in analytics.sla_compliance.recommendations" :key="recommendation" class="text-sm text-slate-600 dark:text-slate-400 flex items-start">
              <CheckCircle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
              {{ recommendation }}
            </li>
          </ul>
        </div>
      </div>

      <!-- Resolution Time Predictions -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Resolution Time Forecast') }}</h4>
          <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getPerformanceClass(analytics.resolution_times.performance_level)">
              {{ getPerformanceLabel(analytics.resolution_times.performance_level) }}
            </span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $t('Current Average') }}</h5>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ analytics.resolution_times.current_average }}h</p>
          </div>
          <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $t('Current Median') }}</h5>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ analytics.resolution_times.current_median }}h</p>
          </div>
          <div class="bg-slate-50 dark:bg-slate-700 rounded-lg p-4">
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">{{ $t('Predicted Average') }}</h5>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ analytics.resolution_times.predicted_average }}h</p>
          </div>
        </div>
      </div>

      <!-- Agent Workload Predictions -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Agent Workload Analysis') }}</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-3">{{ $t('Workload Distribution') }}</h5>
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Low Workload') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ analytics.agent_workload.workload_distribution.low }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Medium Workload') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ analytics.agent_workload.workload_distribution.medium }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('High Workload') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ analytics.agent_workload.workload_distribution.high }}</span>
              </div>
            </div>
          </div>
          
          <div>
            <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-3">{{ $t('Workload Metrics') }}</h5>
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Average Workload') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ analytics.agent_workload.average_workload }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Overloaded Agents') }}</span>
                <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ analytics.agent_workload.overloaded_agents }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ $t('Recommendations') }}</h5>
          <ul class="space-y-1">
            <li v-for="recommendation in analytics.agent_workload.recommendations" :key="recommendation" class="text-sm text-slate-600 dark:text-slate-400 flex items-start">
              <CheckCircle class="w-4 h-4 mr-2 mt-0.5 text-green-500 flex-shrink-0" />
              {{ recommendation }}
            </li>
          </ul>
        </div>
      </div>

      <!-- Customer Satisfaction Predictions -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Customer Satisfaction Forecast') }}</h4>
          <div class="flex items-center space-x-2">
            <span class="text-sm font-medium" :class="getSatisfactionTrendClass(analytics.customer_satisfaction.trend)">
              {{ getSatisfactionTrendLabel(analytics.customer_satisfaction.trend) }}
            </span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Current Satisfaction') }}</span>
              <span class="text-lg font-bold text-slate-900 dark:text-white">{{ analytics.customer_satisfaction.current_satisfaction }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
              <div 
                class="h-2 rounded-full transition-all duration-500 bg-green-500"
                :style="{ width: analytics.customer_satisfaction.current_satisfaction + '%' }"
              ></div>
            </div>
          </div>
          
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Predicted Satisfaction') }}</span>
              <span class="text-lg font-bold text-slate-900 dark:text-white">{{ analytics.customer_satisfaction.predicted_satisfaction }}%</span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
              <div 
                class="h-2 rounded-full transition-all duration-500 bg-green-500"
                :style="{ width: analytics.customer_satisfaction.predicted_satisfaction + '%' }"
              ></div>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <h5 class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ $t('Key Factors') }}</h5>
          <ul class="space-y-1">
            <li v-for="factor in analytics.customer_satisfaction.key_factors" :key="factor" class="text-sm text-slate-600 dark:text-slate-400 flex items-start">
              <Star class="w-4 h-4 mr-2 mt-0.5 text-yellow-500 flex-shrink-0" />
              {{ factor }}
            </li>
          </ul>
        </div>
      </div>

      <!-- Last Updated -->
      <div class="text-center text-sm text-slate-500 dark:text-slate-400">
        {{ $t('Last updated') }}: {{ formatDate(analytics.generated_at) }}
      </div>
    </div>

    <!-- No Analytics State -->
    <div v-else class="text-center py-8">
      <div class="p-3 bg-slate-100 dark:bg-slate-700 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
        <TrendingUp class="w-8 h-8 text-slate-400" />
      </div>
      <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No Predictive Analytics Available') }}</h4>
      <p class="text-slate-600 dark:text-slate-400 mb-4">{{ $t('Click the button below to generate AI-powered predictive analytics') }}</p>
      <button 
        @click="loadAnalytics"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
      >
        <TrendingUp class="w-4 h-4 mr-2" />
        {{ $t('Generate Analytics') }}
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { 
  TrendingUp, 
  Loader2, 
  AlertCircle, 
  RefreshCw, 
  CheckCircle, 
  Star 
} from 'lucide-vue-next'

export default {
  name: 'AIPredictiveAnalytics',
  components: {
    TrendingUp,
    Loader2,
    AlertCircle,
    RefreshCw,
    CheckCircle,
    Star
  },
  setup() {
    const loading = ref(false)
    const error = ref(null)
    const analytics = ref(null)
    const selectedPeriod = ref(7)

    const loadAnalytics = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await fetch(`/dashboard/ai/analytics?days=${selectedPeriod.value}`, {
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

    const getTrendColorClass = (trend) => {
      const classes = {
        'increasing': 'text-red-600 dark:text-red-400',
        'decreasing': 'text-green-600 dark:text-green-400',
        'stable': 'text-slate-600 dark:text-slate-400'
      }
      return classes[trend] || 'text-slate-600 dark:text-slate-400'
    }

    const getTrendLabel = (trend) => {
      const labels = {
        'increasing': 'Increasing',
        'decreasing': 'Decreasing',
        'stable': 'Stable'
      }
      return labels[trend] || trend
    }

    const getSlaRiskClass = (risk) => {
      const classes = {
        'low': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
        'medium': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
        'high': 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
      }
      return classes[risk] || 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'
    }

    const getSlaRiskLabel = (risk) => {
      const labels = {
        'low': 'Low Risk',
        'medium': 'Medium Risk',
        'high': 'High Risk'
      }
      return labels[risk] || risk
    }

    const getSlaComplianceBarClass = (compliance) => {
      if (compliance >= 95) return 'bg-green-500'
      if (compliance >= 85) return 'bg-yellow-500'
      return 'bg-red-500'
    }

    const getPerformanceClass = (level) => {
      const classes = {
        'excellent': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
        'good': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        'average': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
        'needs_improvement': 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
      }
      return classes[level] || 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'
    }

    const getPerformanceLabel = (level) => {
      const labels = {
        'excellent': 'Excellent',
        'good': 'Good',
        'average': 'Average',
        'needs_improvement': 'Needs Improvement'
      }
      return labels[level] || level
    }

    const getSatisfactionTrendClass = (trend) => {
      const classes = {
        'improving': 'text-green-600 dark:text-green-400',
        'declining': 'text-red-600 dark:text-red-400',
        'stable': 'text-slate-600 dark:text-slate-400'
      }
      return classes[trend] || 'text-slate-600 dark:text-slate-400'
    }

    const getSatisfactionTrendLabel = (trend) => {
      const labels = {
        'improving': 'Improving',
        'declining': 'Declining',
        'stable': 'Stable'
      }
      return labels[trend] || trend
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleString()
    }

    onMounted(() => {
      loadAnalytics()
    })

    return {
      loading,
      error,
      analytics,
      selectedPeriod,
      loadAnalytics,
      getTrendColorClass,
      getTrendLabel,
      getSlaRiskClass,
      getSlaRiskLabel,
      getSlaComplianceBarClass,
      getPerformanceClass,
      getPerformanceLabel,
      getSatisfactionTrendClass,
      getSatisfactionTrendLabel,
      formatDate
    }
  }
}
</script>
