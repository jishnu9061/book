<template>
  <div class="ai-suggestions-panel bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-slate-800 dark:to-slate-900 border border-blue-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
          <Sparkles class="w-6 h-6 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI Classification Suggestions') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('AI-powered ticket classification with confidence scoring') }}
          </p>
        </div>
      </div>
      
      <div class="flex items-center space-x-2">
        <div v-if="aiStatus.available && !isRateLimited" class="flex items-center space-x-1 text-green-600 dark:text-green-400">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-xs font-medium">{{ $t('AI Active') }}</span>
        </div>
        <div v-else-if="isRateLimited" class="flex items-center space-x-1 text-yellow-600 dark:text-yellow-400">
          <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
          <span class="text-xs font-medium">{{ $t('Rate Limited') }}</span>
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
        <Loader2 class="w-6 h-6 text-blue-500 animate-spin" />
        <span class="text-slate-600 dark:text-slate-400">{{ $t('Analyzing ticket...') }}</span>
      </div>
    </div>

    <!-- Rate Limit Warning -->
    <div v-else-if="isRateLimited" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
      <div class="flex items-center space-x-2">
        <Clock class="w-5 h-5 text-yellow-500" />
        <span class="text-yellow-700 dark:text-yellow-300 font-medium">{{ $t('AI Rate Limited') }}</span>
      </div>
      <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-1">
        {{ $t('OpenAI rate limits are currently exceeded. AI suggestions will be available again in approximately 1 hour.') }}
      </p>
      <div class="mt-3 flex items-center space-x-2">
        <button 
          @click="getSuggestions"
          class="text-sm text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300 underline"
        >
          {{ $t('Check Again') }}
        </button>
        <span class="text-xs text-yellow-500">•</span>
        <span class="text-xs text-yellow-600 dark:text-yellow-400">{{ $t('Default suggestions shown below') }}</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
      <div class="flex items-center space-x-2">
        <AlertCircle class="w-5 h-5 text-red-500" />
        <span class="text-red-700 dark:text-red-300 font-medium">{{ $t('AI Analysis Failed') }}</span>
      </div>
      <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ error }}</p>
      <button 
        @click="getSuggestions"
        class="mt-3 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 underline"
      >
        {{ $t('Try Again') }}
      </button>
    </div>

    <!-- Suggestions -->
    <div v-else-if="suggestions" class="space-y-4">
      <!-- Overall Confidence -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-3">
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Overall Confidence') }}</span>
          <span class="text-sm font-semibold" :class="getConfidenceColorClass(suggestions.overall_confidence)">
            {{ Math.round(suggestions.overall_confidence * 100) }}%
          </span>
        </div>
        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
          <div 
            class="h-2 rounded-full transition-all duration-500"
            :class="getConfidenceBarClass(suggestions.overall_confidence)"
            :style="{ width: (suggestions.overall_confidence * 100) + '%' }"
          ></div>
        </div>
        <p v-if="suggestions.reasoning" class="text-xs text-slate-600 dark:text-slate-400 mt-2">
          {{ suggestions.reasoning }}
        </p>
      </div>

      <!-- Classification Suggestions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Priority -->
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Priority') }}</span>
            <div class="flex items-center space-x-2">
              <span class="text-xs px-2 py-1 rounded-full" :class="getPriorityClass(suggestions.suggestions.priority.name)">
                {{ suggestions.suggestions.priority.name }}
              </span>
              <span class="text-xs text-slate-500">{{ Math.round(suggestions.suggestions.priority.confidence * 100) }}%</span>
            </div>
          </div>
          <button 
            @click="applySuggestion('priority', suggestions.suggestions.priority)"
            class="w-full text-left p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors duration-200"
          >
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-900 dark:text-white">{{ suggestions.suggestions.priority.name }}</span>
              <Check class="w-4 h-4 text-green-500" />
            </div>
          </button>
        </div>

        <!-- Category -->
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Category') }}</span>
            <div class="flex items-center space-x-2">
              <span class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                {{ suggestions.suggestions.category.name }}
              </span>
              <span class="text-xs text-slate-500">{{ Math.round(suggestions.suggestions.category.confidence * 100) }}%</span>
            </div>
          </div>
          <button 
            @click="applySuggestion('category', suggestions.suggestions.category)"
            class="w-full text-left p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors duration-200"
          >
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-900 dark:text-white">{{ suggestions.suggestions.category.name }}</span>
              <Check class="w-4 h-4 text-green-500" />
            </div>
          </button>
        </div>

        <!-- Department -->
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Department') }}</span>
            <div class="flex items-center space-x-2">
              <span class="text-xs px-2 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                {{ suggestions.suggestions.department.name }}
              </span>
              <span class="text-xs text-slate-500">{{ Math.round(suggestions.suggestions.department.confidence * 100) }}%</span>
            </div>
          </div>
          <button 
            @click="applySuggestion('department', suggestions.suggestions.department)"
            class="w-full text-left p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors duration-200"
          >
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-900 dark:text-white">{{ suggestions.suggestions.department.name }}</span>
              <Check class="w-4 h-4 text-green-500" />
            </div>
          </button>
        </div>

        <!-- Type -->
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Type') }}</span>
            <div class="flex items-center space-x-2">
              <span class="text-xs px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">
                {{ suggestions.suggestions.type.name }}
              </span>
              <span class="text-xs text-slate-500">{{ Math.round(suggestions.suggestions.type.confidence * 100) }}%</span>
            </div>
          </div>
          <button 
            @click="applySuggestion('type', suggestions.suggestions.type)"
            class="w-full text-left p-2 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-colors duration-200"
          >
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-900 dark:text-white">{{ suggestions.suggestions.type.name }}</span>
              <Check class="w-4 h-4 text-green-500" />
            </div>
          </button>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
        <button 
          @click="applyAllSuggestions"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
        >
          <Check class="w-4 h-4 mr-2" />
          {{ $t('Apply All Suggestions') }}
        </button>
        
        <button 
          @click="getSuggestions"
          class="inline-flex items-center px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-medium rounded-lg transition-colors duration-200"
        >
          <RefreshCw class="w-4 h-4 mr-2" />
          {{ $t('Refresh') }}
        </button>
      </div>
    </div>

    <!-- No Suggestions State -->
    <div v-else class="text-center py-8">
      <div class="p-3 bg-slate-100 dark:bg-slate-700 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
        <Sparkles class="w-8 h-8 text-slate-400" />
      </div>
      <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No AI Suggestions Available') }}</h4>
      <p class="text-slate-600 dark:text-slate-400 mb-4">{{ $t('Click the button below to get AI-powered classification suggestions') }}</p>
      <button 
        @click="getSuggestions"
        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
      >
        <Sparkles class="w-4 h-4 mr-2" />
        {{ $t('Get AI Suggestions') }}
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { Sparkles, Loader2, AlertCircle, Check, RefreshCw, Clock } from 'lucide-vue-next'

export default {
  name: 'AIClassificationSuggestions',
  components: {
    Sparkles,
    Loader2,
    AlertCircle,
    Check,
    RefreshCw,
    Clock
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    },
    currentClassification: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['suggestion-applied', 'classification-updated'],
  setup(props, { emit }) {
    const loading = ref(false)
    const error = ref(null)
    const suggestions = ref(null)
    const aiStatus = ref({ available: false })

    const getSuggestions = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await fetch(`/ai/tickets/${props.ticketId}/suggestions`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        const result = await response.json()

        if (result.success) {
          suggestions.value = result.data
        } else {
          throw new Error(result.message || 'Failed to get AI suggestions')
        }
      } catch (err) {
        error.value = err.message
        console.error('Error getting AI suggestions:', err)
      } finally {
        loading.value = false
      }
    }

    const applySuggestion = async (field, suggestion) => {
      try {
        const response = await fetch(`/ai/tickets/${props.ticketId}/apply-classification`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            [`${field}_id`]: suggestion.id,
            confidence_score: suggestion.confidence,
            reasoning: `AI suggestion applied for ${field}`
          })
        })

        const result = await response.json()

        if (result.success) {
          emit('suggestion-applied', { field, suggestion })
          emit('classification-updated')
        } else {
          throw new Error(result.message || 'Failed to apply suggestion')
        }
      } catch (err) {
        error.value = err.message
        console.error('Error applying suggestion:', err)
      }
    }

    const applyAllSuggestions = async () => {
      if (!suggestions.value) return

      try {
        const response = await fetch(`/ai/tickets/${props.ticketId}/apply-classification`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            priority_id: suggestions.value.suggestions.priority.id,
            category_id: suggestions.value.suggestions.category.id,
            department_id: suggestions.value.suggestions.department.id,
            type_id: suggestions.value.suggestions.type.id,
            confidence_score: suggestions.value.overall_confidence,
            reasoning: 'All AI suggestions applied'
          })
        })

        const result = await response.json()

        if (result.success) {
          emit('classification-updated')
        } else {
          throw new Error(result.message || 'Failed to apply all suggestions')
        }
      } catch (err) {
        error.value = err.message
        console.error('Error applying all suggestions:', err)
      }
    }

    const getConfidenceColorClass = (confidence) => {
      if (confidence >= 0.8) return 'text-green-600 dark:text-green-400'
      if (confidence >= 0.6) return 'text-yellow-600 dark:text-yellow-400'
      return 'text-red-600 dark:text-red-400'
    }

    const getConfidenceBarClass = (confidence) => {
      if (confidence >= 0.8) return 'bg-green-500'
      if (confidence >= 0.6) return 'bg-yellow-500'
      return 'bg-red-500'
    }

    const getPriorityClass = (priority) => {
      const priorityClasses = {
        'urgent': 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
        'high': 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
        'medium': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
        'low': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
      }
      return priorityClasses[priority?.toLowerCase()] || 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'
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

    const isRateLimited = computed(() => {
      return suggestions.value && suggestions.value.reasoning && 
             (suggestions.value.reasoning.includes('rate limit') || 
              suggestions.value.reasoning.includes('Rate limit'))
    })

    return {
      loading,
      error,
      suggestions,
      aiStatus,
      isRateLimited,
      getSuggestions,
      applySuggestion,
      applyAllSuggestions,
      getConfidenceColorClass,
      getConfidenceBarClass,
      getPriorityClass
    }
  }
}
</script>
