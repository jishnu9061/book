<template>
  <div class="conversation-analytics">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white flex items-center gap-2">
          <BarChart3 class="w-5 h-5" />
          {{ $t('Conversation Analytics') }}
        </h2>
        <div class="flex items-center gap-2">
          <select v-model="timeRange" class="text-sm border border-slate-300 dark:border-slate-600 rounded-lg px-3 py-1 dark:bg-slate-700 dark:text-white">
            <option value="7d">{{ $t('Last 7 days') }}</option>
            <option value="30d">{{ $t('Last 30 days') }}</option>
            <option value="90d">{{ $t('Last 90 days') }}</option>
          </select>
          <button @click="refreshAnalytics" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': refreshing }" />
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
        <span class="ml-2 text-slate-600 dark:text-slate-400">{{ $t('Loading analytics...') }}</span>
      </div>

      <!-- Analytics Content -->
      <div v-else class="space-y-6">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                  <MessageCircle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $t('Total Conversations') }}</p>
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ analytics.totalConversations }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                  <Users class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $t('Active Participants') }}</p>
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ analytics.activeParticipants }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                  <Clock class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $t('Avg Response Time') }}</p>
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ formatDuration(analytics.avgResponseTime) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-4">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                  <TrendingUp class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $t('Messages per Day') }}</p>
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ analytics.messagesPerDay }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Conversation Types Chart -->
          <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-6">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Conversation Types') }}</h3>
            <div class="space-y-3">
              <div v-for="type in analytics.conversationTypes" :key="type.name" class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-3 h-3 rounded-full" :class="type.color"></div>
                  <span class="text-sm text-slate-700 dark:text-slate-300">{{ type.label }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <div class="w-24 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="h-2 rounded-full" :class="type.color" :style="{ width: `${type.percentage}%` }"></div>
                  </div>
                  <span class="text-sm font-medium text-slate-900 dark:text-white w-8 text-right">{{ type.count }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Timeline -->
          <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-6">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Activity Timeline') }}</h3>
            <div class="space-y-3">
              <div v-for="day in analytics.activityTimeline" :key="day.date" class="flex items-center justify-between">
                <span class="text-sm text-slate-700 dark:text-slate-300">{{ formatDate(day.date) }}</span>
                <div class="flex items-center gap-2">
                  <div class="w-32 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="h-2 bg-blue-500 rounded-full" :style="{ width: `${(day.count / analytics.maxActivity) * 100}%` }"></div>
                  </div>
                  <span class="text-sm font-medium text-slate-900 dark:text-white w-8 text-right">{{ day.count }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Participants -->
        <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 p-6">
          <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Top Participants') }}</h3>
          <div class="space-y-3">
            <div v-for="participant in analytics.topParticipants" :key="participant.id" class="flex items-center gap-3">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                {{ getInitials(participant.name) }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ participant.name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ participant.role }}</div>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-24 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                  <div class="h-2 bg-green-500 rounded-full" :style="{ width: `${(participant.messageCount / analytics.maxMessages) * 100}%` }"></div>
                </div>
                <span class="text-sm font-medium text-slate-900 dark:text-white w-8 text-right">{{ participant.messageCount }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  BarChart3,
  RefreshCw,
  Loader2,
  MessageCircle,
  Users,
  Clock,
  TrendingUp
} from 'lucide-vue-next'

export default {
  name: 'ConversationAnalytics',
  components: {
    BarChart3,
    RefreshCw,
    Loader2,
    MessageCircle,
    Users,
    Clock,
    TrendingUp
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    },
    conversations: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      loading: false,
      refreshing: false,
      timeRange: '30d',
      analytics: {
        totalConversations: 0,
        activeParticipants: 0,
        avgResponseTime: 0,
        messagesPerDay: 0,
        conversationTypes: [],
        activityTimeline: [],
        topParticipants: [],
        maxActivity: 0,
        maxMessages: 0
      }
    }
  },
  watch: {
    conversations: {
      handler() {
        this.calculateAnalytics()
      },
      immediate: true
    },
    timeRange() {
      this.calculateAnalytics()
    }
  },
  methods: {
    calculateAnalytics() {
      this.loading = true
      
      // Simulate calculation delay
      setTimeout(() => {
        this.analytics = this.generateAnalytics()
        this.loading = false
      }, 500)
    },
    generateAnalytics() {
      const conversations = this.conversations || []
      
      // Calculate basic stats
      const totalConversations = conversations.length
      const allParticipants = new Set()
      let totalMessages = 0
      let totalResponseTime = 0
      let responseCount = 0
      
      conversations.forEach(conversation => {
        // Count participants
        if (conversation.participants) {
          conversation.participants.forEach(p => allParticipants.add(p.user?.id || p.user_id))
        }
        
        // Count messages
        if (conversation.messages) {
          totalMessages += conversation.messages.length
        }
      })
      
      // Calculate conversation types
      const conversationTypes = [
        {
          name: 'internal',
          label: 'Internal',
          count: conversations.filter(c => c.type === 'internal').length,
          color: 'bg-slate-500'
        },
        {
          name: 'customer',
          label: 'Customer',
          count: conversations.filter(c => c.type === 'customer').length,
          color: 'bg-blue-500'
        }
      ]
      
      // Calculate percentages
      conversationTypes.forEach(type => {
        type.percentage = totalConversations > 0 ? (type.count / totalConversations) * 100 : 0
      })
      
      // Generate activity timeline (last 7 days)
      const activityTimeline = []
      const maxActivity = Math.max(...Array.from({ length: 7 }, (_, i) => {
        const date = new Date()
        date.setDate(date.getDate() - i)
        const dayConversations = conversations.filter(c => {
          const convDate = new Date(c.created_at)
          return convDate.toDateString() === date.toDateString()
        })
        return dayConversations.length
      }))
      
      for (let i = 6; i >= 0; i--) {
        const date = new Date()
        date.setDate(date.getDate() - i)
        const dayConversations = conversations.filter(c => {
          const convDate = new Date(c.created_at)
          return convDate.toDateString() === date.toDateString()
        })
        activityTimeline.push({
          date: date.toISOString().split('T')[0],
          count: dayConversations.length
        })
      }
      
      // Generate top participants
      const participantStats = {}
      conversations.forEach(conversation => {
        if (conversation.participants) {
          conversation.participants.forEach(participant => {
            const userId = participant.user?.id || participant.user_id
            if (!participantStats[userId]) {
              participantStats[userId] = {
                id: userId,
                name: participant.user?.name || `${participant.user?.first_name} ${participant.user?.last_name}`,
                role: participant.role,
                messageCount: 0
              }
            }
          })
        }
        
        if (conversation.messages) {
          conversation.messages.forEach(message => {
            const userId = message.user?.id || message.user_id
            if (participantStats[userId]) {
              participantStats[userId].messageCount++
            }
          })
        }
      })
      
      const topParticipants = Object.values(participantStats)
        .sort((a, b) => b.messageCount - a.messageCount)
        .slice(0, 5)
      
      const maxMessages = Math.max(...topParticipants.map(p => p.messageCount), 1)
      
      return {
        totalConversations,
        activeParticipants: allParticipants.size,
        avgResponseTime: responseCount > 0 ? totalResponseTime / responseCount : 0,
        messagesPerDay: Math.round(totalMessages / 7),
        conversationTypes,
        activityTimeline,
        topParticipants,
        maxActivity: Math.max(maxActivity, 1),
        maxMessages
      }
    },
    refreshAnalytics() {
      this.refreshing = true
      this.calculateAnalytics()
      setTimeout(() => {
        this.refreshing = false
      }, 1000)
    },
    formatDuration(minutes) {
      if (minutes < 60) {
        return `${Math.round(minutes)}m`
      } else if (minutes < 1440) {
        return `${Math.round(minutes / 60)}h`
      } else {
        return `${Math.round(minutes / 1440)}d`
      }
    },
    formatDate(dateString) {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    },
    getInitials(name) {
      if (!name) return 'U'
      return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2)
    }
  }
}
</script>

<style scoped>
.conversation-analytics {
  @apply relative;
}
</style>
