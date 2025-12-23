<template>
  <div class="ai-usage-guide bg-gradient-to-br from-green-50 to-emerald-50 dark:from-slate-800 dark:to-slate-900 border border-green-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
          <BookOpen class="w-6 h-6 text-green-600 dark:text-green-400" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ $t('AI Features Guide') }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('Learn how to use AI features effectively') }}
          </p>
        </div>
      </div>
      
      <button 
        @click="toggleExpanded"
        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
        :title="expanded ? $t('Collapse') : $t('Expand')"
      >
        <ChevronDown class="w-5 h-5 transition-transform" :class="{ 'rotate-180': expanded }" />
      </button>
    </div>

    <!-- Quick Tips -->
    <div v-if="!expanded" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-2">
          <Sparkles class="w-4 h-4 text-blue-500" />
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Classification') }}</span>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-400">{{ $t('AI automatically suggests priority, category, and department for tickets') }}</p>
      </div>
      
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-2">
          <MessageSquare class="w-4 h-4 text-green-500" />
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Response Suggestions') }}</span>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Get AI-generated response suggestions in different tones') }}</p>
      </div>
      
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-2">
          <Heart class="w-4 h-4 text-red-500" />
          <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Sentiment Analysis') }}</span>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Understand customer emotions and urgency levels') }}</p>
      </div>
    </div>

    <!-- Expanded Guide -->
    <div v-else class="space-y-6">
      <!-- AI Classification -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-3">
          <Sparkles class="w-5 h-5 text-blue-500" />
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('AI Classification') }}</h4>
        </div>
        <div class="space-y-3">
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('How it works:') }}</h5>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('AI analyzes ticket content and automatically suggests the most appropriate priority, category, department, and type.') }}</p>
          </div>
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Where to find it:') }}</h5>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('On any ticket edit page, look for the AI Classification Suggestions panel.') }}</p>
          </div>
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('How to use:') }}</h5>
            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
              <li>• {{ $t('Review AI suggestions with confidence scores') }}</li>
              <li>• {{ $t('Apply individual suggestions or all at once') }}</li>
              <li>• {{ $t('Use auto-classify for new tickets') }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Response Suggestions -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-3">
          <MessageSquare class="w-5 h-5 text-green-500" />
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Response Suggestions') }}</h4>
        </div>
        <div class="space-y-3">
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('How it works:') }}</h5>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('AI generates contextual response suggestions based on ticket content and customer sentiment.') }}</p>
          </div>
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Available tones:') }}</h5>
            <div class="flex flex-wrap gap-2">
              <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-full">{{ $t('Professional') }}</span>
              <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded-full">{{ $t('Friendly') }}</span>
              <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded-full">{{ $t('Technical') }}</span>
              <span class="px-2 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 text-xs rounded-full">{{ $t('Empathetic') }}</span>
            </div>
          </div>
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('How to use:') }}</h5>
            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
              <li>• {{ $t('Click "Get AI Suggestions" in ticket conversations') }}</li>
              <li>• {{ $t('Choose from multiple tone options') }}</li>
              <li>• {{ $t('Customize suggestions before sending') }}</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Sentiment Analysis -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-3">
          <Heart class="w-5 h-5 text-red-500" />
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Sentiment Analysis') }}</h4>
        </div>
        <div class="space-y-3">
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('How it works:') }}</h5>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('AI analyzes customer emotional tone and provides urgency indicators and escalation recommendations.') }}</p>
          </div>
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('What it detects:') }}</h5>
            <div class="grid grid-cols-2 gap-2">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <span class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Frustration') }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <span class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Satisfaction') }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                <span class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Urgency') }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span class="text-xs text-slate-600 dark:text-slate-400">{{ $t('Politeness') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Best Practices -->
      <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center space-x-2 mb-3">
          <Lightbulb class="w-5 h-5 text-yellow-500" />
          <h4 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Best Practices') }}</h4>
        </div>
        <div class="space-y-3">
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('For better AI results:') }}</h5>
            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
              <li>• {{ $t('Provide detailed ticket descriptions') }}</li>
              <li>• {{ $t('Review AI suggestions before applying') }}</li>
              <li>• {{ $t('Use confidence scores to guide decisions') }}</li>
              <li>• {{ $t('Monitor AI performance in analytics') }}</li>
            </ul>
          </div>
          <div>
            <h5 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('When AI is rate limited:') }}</h5>
            <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
              <li>• {{ $t('Default suggestions will be shown') }}</li>
              <li>• {{ $t('Wait for rate limits to reset (usually 1 hour)') }}</li>
              <li>• {{ $t('Check AI status monitor for updates') }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { BookOpen, ChevronDown, Sparkles, MessageSquare, Heart, Lightbulb } from 'lucide-vue-next'

export default {
  name: 'AIUsageGuide',
  components: {
    BookOpen,
    ChevronDown,
    Sparkles,
    MessageSquare,
    Heart,
    Lightbulb
  },
  setup() {
    const expanded = ref(false)

    const toggleExpanded = () => {
      expanded.value = !expanded.value
    }

    return {
      expanded,
      toggleExpanded
    }
  }
}
</script>
