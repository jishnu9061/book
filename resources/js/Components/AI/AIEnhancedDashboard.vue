<template>
  <div class="ai-enhanced-dashboard space-y-6">
    <!-- AI Status Overview -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl">
            <Sparkles class="w-8 h-8 text-white" />
          </div>
          <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('AI-Powered Helpdesk') }}</h2>
            <p class="text-slate-600 dark:text-slate-400">{{ $t('Intelligent automation and insights for your support team') }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <div v-if="aiStatus.available" class="flex items-center space-x-2 text-green-600 dark:text-green-400">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <span class="font-medium">{{ $t('AI Active') }}</span>
          </div>
          <div v-else class="flex items-center space-x-2 text-red-600 dark:text-red-400">
            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
            <span class="font-medium">{{ $t('AI Offline') }}</span>
          </div>
          
          <button 
            @click="refreshAll"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
          >
            <RefreshCw class="w-4 h-4 mr-2" :class="{ 'animate-spin': refreshing }" />
            {{ $t('Refresh All') }}
          </button>
        </div>
      </div>

      <!-- AI Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
              <MessageCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <div>
              <h3 class="font-semibold text-slate-900 dark:text-white">{{ $t('Response Suggestions') }}</h3>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('AI-powered reply recommendations') }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
              <Heart class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <div>
              <h3 class="font-semibold text-slate-900 dark:text-white">{{ $t('Sentiment Analysis') }}</h3>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Customer mood detection') }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-800">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
              <TrendingUp class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
              <h3 class="font-semibold text-slate-900 dark:text-white">{{ $t('Predictive Analytics') }}</h3>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Forecast trends and performance') }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-800">
          <div class="flex items-center space-x-3">
            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
              <BookOpen class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
            <div>
              <h3 class="font-semibold text-slate-900 dark:text-white">{{ $t('Knowledge Base') }}</h3>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Smart article suggestions') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- AI Components Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- AI Classification Suggestions -->
      <div v-if="showClassification">
        <AIClassificationSuggestions 
          :ticket-id="selectedTicketId"
          :current-classification="currentClassification"
          @suggestion-applied="handleSuggestionApplied"
          @classification-updated="handleClassificationUpdated"
        />
      </div>

      <!-- AI Response Suggestions -->
      <div v-if="showResponseSuggestions">
        <AIResponseSuggestions 
          :ticket-id="selectedTicketId"
          :context="responseContext"
          @suggestion-used="handleSuggestionUsed"
        />
      </div>

      <!-- AI Sentiment Analysis -->
      <div v-if="showSentimentAnalysis">
        <AISentimentAnalysis 
          :ticket-id="selectedTicketId"
          @escalation-requested="handleEscalationRequested"
        />
      </div>

      <!-- AI Predictive Analytics -->
      <div v-if="showPredictiveAnalytics" class="lg:col-span-2">
        <AIPredictiveAnalytics />
      </div>
    </div>

    <!-- AI Analytics Dashboard -->
    <div v-if="showAnalytics">
      <AIAnalyticsDashboard />
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
      <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Quick Actions') }}</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <button 
          @click="toggleComponent('classification')"
          class="flex items-center justify-center space-x-2 p-4 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 rounded-lg border border-slate-200 dark:border-slate-600 transition-colors duration-200"
        >
          <Sparkles class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          <span class="font-medium text-slate-900 dark:text-white">{{ $t('Auto-Classify') }}</span>
        </button>

        <button 
          @click="toggleComponent('responseSuggestions')"
          class="flex items-center justify-center space-x-2 p-4 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 rounded-lg border border-slate-200 dark:border-slate-600 transition-colors duration-200"
        >
          <MessageCircle class="w-5 h-5 text-green-600 dark:text-green-400" />
          <span class="font-medium text-slate-900 dark:text-white">{{ $t('Response Help') }}</span>
        </button>

        <button 
          @click="toggleComponent('sentimentAnalysis')"
          class="flex items-center justify-center space-x-2 p-4 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 rounded-lg border border-slate-200 dark:border-slate-600 transition-colors duration-200"
        >
          <Heart class="w-5 h-5 text-purple-600 dark:text-purple-400" />
          <span class="font-medium text-slate-900 dark:text-white">{{ $t('Sentiment Check') }}</span>
        </button>

        <button 
          @click="toggleComponent('predictiveAnalytics')"
          class="flex items-center justify-center space-x-2 p-4 bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 rounded-lg border border-slate-200 dark:border-slate-600 transition-colors duration-200"
        >
          <TrendingUp class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
          <span class="font-medium text-slate-900 dark:text-white">{{ $t('Analytics') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { 
  Sparkles, 
  RefreshCw, 
  MessageCircle, 
  Heart, 
  TrendingUp, 
  BookOpen 
} from 'lucide-vue-next'

import AIClassificationSuggestions from './AIClassificationSuggestions.vue'
import AIResponseSuggestions from './AIResponseSuggestions.vue'
import AISentimentAnalysis from './AISentimentAnalysis.vue'
import AIPredictiveAnalytics from './AIPredictiveAnalytics.vue'
import AIAnalyticsDashboard from './AIAnalyticsDashboard.vue'

export default {
  name: 'AIEnhancedDashboard',
  components: {
    Sparkles,
    RefreshCw,
    MessageCircle,
    Heart,
    TrendingUp,
    BookOpen,
    AIClassificationSuggestions,
    AIResponseSuggestions,
    AISentimentAnalysis,
    AIPredictiveAnalytics,
    AIAnalyticsDashboard
  },
  props: {
    ticketId: {
      type: [String, Number],
      default: null
    },
    currentClassification: {
      type: Object,
      default: () => ({})
    },
    responseContext: {
      type: String,
      default: ''
    }
  },
  emits: [
    'suggestion-applied',
    'classification-updated',
    'suggestion-used',
    'escalation-requested'
  ],
  setup(props, { emit }) {
    const aiStatus = ref({ available: false })
    const refreshing = ref(false)
    
    // Component visibility states
    const showClassification = ref(false)
    const showResponseSuggestions = ref(false)
    const showSentimentAnalysis = ref(false)
    const showPredictiveAnalytics = ref(false)
    const showAnalytics = ref(false)

    const selectedTicketId = ref(props.ticketId)

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

    const refreshAll = async () => {
      refreshing.value = true
      try {
        await checkAIStatus()
        // Trigger refresh for all visible components
        // This would typically emit events to child components
      } finally {
        refreshing.value = false
      }
    }

    const toggleComponent = (component) => {
      switch (component) {
        case 'classification':
          showClassification.value = !showClassification.value
          break
        case 'responseSuggestions':
          showResponseSuggestions.value = !showResponseSuggestions.value
          break
        case 'sentimentAnalysis':
          showSentimentAnalysis.value = !showSentimentAnalysis.value
          break
        case 'predictiveAnalytics':
          showPredictiveAnalytics.value = !showPredictiveAnalytics.value
          break
        case 'analytics':
          showAnalytics.value = !showAnalytics.value
          break
      }
    }

    const handleSuggestionApplied = (data) => {
      emit('suggestion-applied', data)
    }

    const handleClassificationUpdated = () => {
      emit('classification-updated')
    }

    const handleSuggestionUsed = (suggestion) => {
      emit('suggestion-used', suggestion)
    }

    const handleEscalationRequested = (sentiment) => {
      emit('escalation-requested', sentiment)
    }

    onMounted(() => {
      checkAIStatus()
      
      // Auto-show classification if ticket ID is provided
      if (props.ticketId) {
        showClassification.value = true
      }
    })

    return {
      aiStatus,
      refreshing,
      showClassification,
      showResponseSuggestions,
      showSentimentAnalysis,
      showPredictiveAnalytics,
      showAnalytics,
      selectedTicketId,
      checkAIStatus,
      refreshAll,
      toggleComponent,
      handleSuggestionApplied,
      handleClassificationUpdated,
      handleSuggestionUsed,
      handleEscalationRequested
    }
  }
}
</script>
