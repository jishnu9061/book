<template>
  <div class="ai-response-suggestions bg-gradient-to-br from-green-50 to-emerald-50 dark:from-slate-800 dark:to-slate-900 border border-green-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
          <MessageCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI Response Suggestions') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('AI-powered response suggestions with different tones') }}
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
        <Loader2 class="w-6 h-6 text-green-500 animate-spin" />
        <span class="text-slate-600 dark:text-slate-400">{{ $t('Generating response suggestions...') }}</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
      <div class="flex items-center space-x-2">
        <AlertCircle class="w-5 h-5 text-red-500" />
        <span class="text-red-700 dark:text-red-300 font-medium">{{ $t('Failed to Generate Suggestions') }}</span>
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
    <div v-else-if="suggestions && suggestions.suggestions" class="space-y-4">
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

      <!-- Response Suggestions -->
      <div class="space-y-4">
        <div 
          v-for="(suggestion, index) in suggestions.suggestions" 
          :key="index"
          class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden"
        >
          <!-- Suggestion Header -->
          <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getToneClass(suggestion.tone)">
                  {{ getToneLabel(suggestion.tone) }}
                </span>
                <span class="text-xs text-slate-500">
                  {{ Math.round(suggestion.confidence * 100) }}% confidence
                </span>
              </div>
              <button 
                @click="useSuggestion(suggestion)"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200"
              >
                <Check class="w-4 h-4 mr-1" />
                {{ $t('Use') }}
              </button>
            </div>
          </div>

          <!-- Suggestion Content -->
          <div class="px-4 py-4">
            <p class="text-slate-900 dark:text-white leading-relaxed whitespace-pre-wrap">
              {{ suggestion.content }}
            </p>
            
            <!-- Tags -->
            <div v-if="suggestion.tags && suggestion.tags.length" class="flex flex-wrap gap-2 mt-3">
              <span 
                v-for="tag in suggestion.tags" 
                :key="tag"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300"
              >
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
        <button 
          @click="getSuggestions"
          class="inline-flex items-center px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 text-sm font-medium rounded-lg transition-colors duration-200"
        >
          <RefreshCw class="w-4 h-4 mr-2" />
          {{ $t('Generate New Suggestions') }}
        </button>
        
        <div class="flex items-center space-x-2">
          <button 
            @click="showKnowledgeBase = !showKnowledgeBase"
            class="inline-flex items-center px-4 py-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium rounded-lg transition-colors duration-200"
          >
            <BookOpen class="w-4 h-4 mr-2" />
            {{ $t('Knowledge Base') }}
          </button>
        </div>
      </div>
    </div>

    <!-- No Suggestions State -->
    <div v-else class="text-center py-8">
      <div class="p-3 bg-slate-100 dark:bg-slate-700 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
        <MessageCircle class="w-8 h-8 text-slate-400" />
      </div>
      <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No Response Suggestions Available') }}</h4>
      <p class="text-slate-600 dark:text-slate-400 mb-4">{{ $t('Click the button below to get AI-powered response suggestions') }}</p>
      <button 
        @click="getSuggestions"
        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
      >
        <MessageCircle class="w-4 h-4 mr-2" />
        {{ $t('Get Response Suggestions') }}
      </button>
    </div>

    <!-- Knowledge Base Suggestions -->
    <div v-if="showKnowledgeBase" class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Knowledge Base Suggestions') }}</h4>
        <button 
          @click="getKnowledgeBaseSuggestions"
          class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
        >
          {{ $t('Refresh') }}
        </button>
      </div>
      
      <div v-if="kbLoading" class="flex items-center justify-center py-4">
        <Loader2 class="w-5 h-5 text-blue-500 animate-spin mr-2" />
        <span class="text-slate-600 dark:text-slate-400">{{ $t('Loading knowledge base suggestions...') }}</span>
      </div>
      
      <div v-else-if="knowledgeBaseSuggestions.length > 0" class="space-y-3">
        <div 
          v-for="suggestion in knowledgeBaseSuggestions" 
          :key="suggestion.id"
          class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h5 class="font-medium text-slate-900 dark:text-white mb-1">{{ suggestion.title }}</h5>
              <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ suggestion.content.substring(0, 150) }}...</p>
              <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                  {{ suggestion.type === 'faq' ? 'FAQ' : 'Knowledge Base' }}
                </span>
                <span class="text-xs text-slate-500">
                  {{ Math.round(suggestion.relevance_score * 100) }}% relevant
                </span>
              </div>
            </div>
            <a 
              :href="suggestion.url" 
              target="_blank"
              class="ml-4 inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200"
            >
              <ExternalLink class="w-4 h-4 mr-1" />
              {{ $t('View') }}
            </a>
          </div>
        </div>
      </div>
      
      <div v-else class="text-center py-4">
        <p class="text-slate-600 dark:text-slate-400">{{ $t('No relevant knowledge base articles found') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { 
  MessageCircle, 
  Loader2, 
  AlertCircle, 
  Check, 
  RefreshCw, 
  BookOpen, 
  ExternalLink 
} from 'lucide-vue-next'

export default {
  name: 'AIResponseSuggestions',
  components: {
    MessageCircle,
    Loader2,
    AlertCircle,
    Check,
    RefreshCw,
    BookOpen,
    ExternalLink
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    },
    context: {
      type: String,
      default: ''
    }
  },
  emits: ['suggestion-used'],
  setup(props, { emit }) {
    const loading = ref(false)
    const kbLoading = ref(false)
    const error = ref(null)
    const suggestions = ref(null)
    const knowledgeBaseSuggestions = ref([])
    const showKnowledgeBase = ref(false)
    const aiStatus = ref({ available: false })

    const getSuggestions = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await fetch('/dashboard/ai/response-suggestions', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            ticket_id: props.ticketId,
            context: props.context
          })
        })

        const result = await response.json()

        if (result.success) {
          suggestions.value = result.data
        } else {
          throw new Error(result.message || 'Failed to get response suggestions')
        }
      } catch (err) {
        error.value = err.message
        console.error('Error getting response suggestions:', err)
      } finally {
        loading.value = false
      }
    }

    const getKnowledgeBaseSuggestions = async () => {
      kbLoading.value = true
      
      try {
        const response = await fetch(`/ai/knowledge-base/suggestions?ticket_id=${props.ticketId}`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        const result = await response.json()

        if (result.success) {
          knowledgeBaseSuggestions.value = result.data
        }
      } catch (err) {
        console.error('Error getting knowledge base suggestions:', err)
      } finally {
        kbLoading.value = false
      }
    }

    const useSuggestion = (suggestion) => {
      emit('suggestion-used', suggestion)
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

    const getToneClass = (tone) => {
      const toneClasses = {
        'professional': 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        'friendly': 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
        'technical': 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
        'empathetic': 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300'
      }
      return toneClasses[tone] || 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300'
    }

    const getToneLabel = (tone) => {
      const toneLabels = {
        'professional': 'Professional',
        'friendly': 'Friendly',
        'technical': 'Technical',
        'empathetic': 'Empathetic'
      }
      return toneLabels[tone] || tone
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
      kbLoading,
      error,
      suggestions,
      knowledgeBaseSuggestions,
      showKnowledgeBase,
      aiStatus,
      getSuggestions,
      getKnowledgeBaseSuggestions,
      useSuggestion,
      getConfidenceColorClass,
      getConfidenceBarClass,
      getToneClass,
      getToneLabel
    }
  }
}
</script>
