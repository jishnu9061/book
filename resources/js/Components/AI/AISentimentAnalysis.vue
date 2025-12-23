<template>
  <div class="ai-sentiment-analysis bg-gradient-to-br from-purple-50 to-pink-50 dark:from-slate-800 dark:to-slate-900 border border-purple-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
          <Heart class="w-6 h-6 text-purple-600 dark:text-purple-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI Sentiment Analysis') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('Real-time customer mood and emotion analysis') }}
          </p>
        </div>
      </div>
      
      <div class="flex items-center space-x-2">
        <div v-if="aiStatus.available" class="flex items-center space-x-1 text-green-600 dark:text-green-400">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-xs font-medium">{{ $t('AI Active') }}</span>
        </div>
        <div v-else class="flex items-center space-x-1 text-red-600 dark:text-red-400">
          <div class="w-2 h-2 bg-red-500 rounded-full"></div>
          <span class="text-xs font-medium">{{ $t('AI Offline') }}</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="flex items-center space-x-3">
        <Loader2 class="w-6 h-6 text-purple-500 animate-spin" />
        <span class="text-slate-600 dark:text-slate-400">{{ $t('Analyzing sentiment...') }}</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
      <div class="flex items-center space-x-2">
        <AlertCircle class="w-5 h-5 text-red-500" />
        <span class="text-red-700 dark:text-red-300 font-medium">{{ $t('Sentiment Analysis Failed') }}</span>
      </div>
      <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ error }}</p>
      <button 
        @click="analyzeSentiment"
        class="mt-3 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 underline"
      >
        {{ $t('Try Again') }}
      </button>
    </div>

    <!-- Sentiment Analysis Results -->
    <div v-else-if="sentiment" class="space-y-6">
      <!-- Overall Sentiment -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Overall Sentiment') }}</h4>
          <div class="flex items-center space-x-2">
            <span class="text-sm font-medium" :class="getSentimentTextClass(sentiment.sentiment_score)">
              {{ getSentimentLabel(sentiment.overall_sentiment) }}
            </span>
            <span class="text-sm text-slate-500">
              {{ Math.round(sentiment.confidence * 100) }}% confidence
            </span>
          </div>
        </div>
        
        <!-- Sentiment Score Bar -->
        <div class="relative">
          <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3">
            <div 
              class="h-3 rounded-full transition-all duration-1000"
              :class="getSentimentBarClass(sentiment.sentiment_score)"
              :style="{ 
                width: Math.abs(sentiment.sentiment_score) * 50 + 50 + '%',
                marginLeft: sentiment.sentiment_score < 0 ? '50%' : '0%',
                transform: sentiment.sentiment_score < 0 ? 'translateX(-100%)' : 'translateX(0%)'
              }"
            ></div>
          </div>
          <div class="flex justify-between text-xs text-slate-500 mt-2">
            <span>{{ $t('Very Negative') }}</span>
            <span>{{ $t('Neutral') }}</span>
            <span>{{ $t('Very Positive') }}</span>
          </div>
        </div>
        
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-3">
          {{ sentiment.analysis_summary }}
        </p>
      </div>

      <!-- Emotional Indicators -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Emotional Indicators') }}</h4>
        <div v-if="sentiment.emotional_indicators && sentiment.emotional_indicators.length > 0" class="flex flex-wrap gap-2">
          <span 
            v-for="emotion in sentiment.emotional_indicators" 
            :key="emotion"
            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300"
          >
            <Heart class="w-4 h-4 mr-1" />
            {{ emotion }}
          </span>
        </div>
        <p v-else class="text-slate-600 dark:text-slate-400">{{ $t('No specific emotional indicators detected') }}</p>
      </div>

      <!-- Urgency and Escalation -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Urgency Level -->
        <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Urgency Level') }}</h4>
          <div class="flex items-center space-x-3">
            <div class="p-3 rounded-lg" :class="getUrgencyBgClass(sentiment.urgency_level)">
              <AlertTriangle class="w-6 h-6" :class="getUrgencyIconClass(sentiment.urgency_level)" />
            </div>
            <div>
              <p class="font-medium text-slate-900 dark:text-white capitalize">
                {{ sentiment.urgency_level }}
              </p>
              <p class="text-sm text-slate-600 dark:text-slate-400">
                {{ getUrgencyDescription(sentiment.urgency_level) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Escalation Recommendation -->
        <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Escalation Recommendation') }}</h4>
          <div class="flex items-center space-x-3">
            <div class="p-3 rounded-lg" :class="sentiment.should_escalate ? 'bg-red-100 dark:bg-red-900/30' : 'bg-green-100 dark:bg-green-900/30'">
              <AlertCircle v-if="sentiment.should_escalate" class="w-6 h-6 text-red-600 dark:text-red-400" />
              <CheckCircle v-else class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <div>
              <p class="font-medium" :class="sentiment.should_escalate ? 'text-red-700 dark:text-red-300' : 'text-green-700 dark:text-green-300'">
                {{ sentiment.should_escalate ? $t('Escalation Recommended') : $t('No Escalation Needed') }}
              </p>
              <p v-if="sentiment.escalation_reason" class="text-sm text-slate-600 dark:text-slate-400">
                {{ sentiment.escalation_reason }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Key Phrases -->
      <div v-if="sentiment.key_phrases && sentiment.key_phrases.length > 0" class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Key Phrases') }}</h4>
        <div class="flex flex-wrap gap-2">
          <span 
            v-for="phrase in sentiment.key_phrases" 
            :key="phrase"
            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300"
          >
            <Tag class="w-4 h-4 mr-1" />
            {{ phrase }}
          </span>
        </div>
      </div>

      <!-- Response Approach -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 border border-slate-200 dark:border-slate-700">
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">{{ $t('Recommended Response Approach') }}</h4>
        <div class="flex items-center space-x-3">
          <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <MessageCircle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
          </div>
          <div>
            <p class="font-medium text-slate-900 dark:text-white capitalize">
              {{ sentiment.response_approach }} {{ $t('approach') }}
            </p>
            <p class="text-sm text-slate-600 dark:text-slate-400">
              {{ getResponseApproachDescription(sentiment.response_approach) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
        <button 
          @click="analyzeSentiment"
          class="inline-flex items-center px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-medium rounded-lg transition-colors duration-200"
        >
          <RefreshCw class="w-4 h-4 mr-2" />
          {{ $t('Re-analyze') }}
        </button>
        
        <div class="flex items-center space-x-2">
          <button 
            v-if="sentiment.should_escalate"
            @click="escalateTicket"
            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
          >
            <AlertTriangle class="w-4 h-4 mr-2" />
            {{ $t('Escalate Ticket') }}
          </button>
        </div>
      </div>
    </div>

    <!-- No Analysis State -->
    <div v-else class="text-center py-8">
      <div class="p-3 bg-slate-100 dark:bg-slate-700 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
        <Heart class="w-8 h-8 text-slate-400" />
      </div>
      <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No Sentiment Analysis Available') }}</h4>
      <p class="text-slate-600 dark:text-slate-400 mb-4">{{ $t('Click the button below to analyze customer sentiment') }}</p>
      <button 
        @click="analyzeSentiment"
        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
      >
        <Heart class="w-4 h-4 mr-2" />
        {{ $t('Analyze Sentiment') }}
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { 
  Heart, 
  Loader2, 
  AlertCircle, 
  AlertTriangle, 
  CheckCircle, 
  MessageCircle, 
  Tag, 
  RefreshCw 
} from 'lucide-vue-next'

export default {
  name: 'AISentimentAnalysis',
  components: {
    Heart,
    Loader2,
    AlertCircle,
    AlertTriangle,
    CheckCircle,
    MessageCircle,
    Tag,
    RefreshCw
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    }
  },
  emits: ['escalation-requested'],
  setup(props, { emit }) {
    const loading = ref(false)
    const error = ref(null)
    const sentiment = ref(null)
    const aiStatus = ref({ available: false })

    const analyzeSentiment = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await fetch('/dashboard/ai/sentiment-analysis', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            ticket_id: props.ticketId
          })
        })

        const result = await response.json()

        if (result.success) {
          sentiment.value = result.data
        } else {
          throw new Error(result.message || 'Failed to analyze sentiment')
        }
      } catch (err) {
        error.value = err.message
        console.error('Error analyzing sentiment:', err)
      } finally {
        loading.value = false
      }
    }

    const escalateTicket = () => {
      emit('escalation-requested', sentiment.value)
    }

    const getSentimentLabel = (sentiment) => {
      const labels = {
        'very_negative': 'Very Negative',
        'negative': 'Negative',
        'neutral': 'Neutral',
        'positive': 'Positive',
        'very_positive': 'Very Positive'
      }
      return labels[sentiment] || sentiment
    }

    const getSentimentTextClass = (score) => {
      if (score >= 0.3) return 'text-green-600 dark:text-green-400'
      if (score >= -0.1) return 'text-yellow-600 dark:text-yellow-400'
      if (score >= -0.5) return 'text-orange-600 dark:text-orange-400'
      return 'text-red-600 dark:text-red-400'
    }

    const getSentimentBarClass = (score) => {
      if (score >= 0.3) return 'bg-green-500'
      if (score >= -0.1) return 'bg-yellow-500'
      if (score >= -0.5) return 'bg-orange-500'
      return 'bg-red-500'
    }

    const getUrgencyBgClass = (urgency) => {
      const classes = {
        'critical': 'bg-red-100 dark:bg-red-900/30',
        'high': 'bg-orange-100 dark:bg-orange-900/30',
        'medium': 'bg-yellow-100 dark:bg-yellow-900/30',
        'low': 'bg-green-100 dark:bg-green-900/30'
      }
      return classes[urgency] || 'bg-slate-100 dark:bg-slate-700'
    }

    const getUrgencyIconClass = (urgency) => {
      const classes = {
        'critical': 'text-red-600 dark:text-red-400',
        'high': 'text-orange-600 dark:text-orange-400',
        'medium': 'text-yellow-600 dark:text-yellow-400',
        'low': 'text-green-600 dark:text-green-400'
      }
      return classes[urgency] || 'text-slate-600 dark:text-slate-400'
    }

    const getUrgencyDescription = (urgency) => {
      const descriptions = {
        'critical': 'Immediate attention required',
        'high': 'Priority handling recommended',
        'medium': 'Standard response time',
        'low': 'Can be handled during normal hours'
      }
      return descriptions[urgency] || 'Standard priority'
    }

    const getResponseApproachDescription = (approach) => {
      const descriptions = {
        'empathetic': 'Show understanding and compassion',
        'professional': 'Maintain formal and courteous tone',
        'friendly': 'Use warm and approachable language',
        'technical': 'Focus on solution and technical details',
        'urgent': 'Respond quickly with immediate actions'
      }
      return descriptions[approach] || 'Use appropriate professional tone'
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

    onMounted(() => {
      checkAIStatus()
    })

    return {
      loading,
      error,
      sentiment,
      aiStatus,
      analyzeSentiment,
      escalateTicket,
      getSentimentLabel,
      getSentimentTextClass,
      getSentimentBarClass,
      getUrgencyBgClass,
      getUrgencyIconClass,
      getUrgencyDescription,
      getResponseApproachDescription
    }
  }
}
</script>
