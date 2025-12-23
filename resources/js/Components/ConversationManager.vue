<template>
  <div class="conversation-manager">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white flex items-center gap-2">
          <MessageCircle class="w-5 h-5" />
          {{ $t('Conversations') }}
          <span v-if="filteredConversations.length" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
            {{ filteredConversations.length }}
          </span>
        </h2>
        <div class="flex items-center gap-2">
          <button @click="refreshConversations" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Refresh')">
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': refreshing }" />
          </button>
          <button @click="startNewConversation" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
            <Plus class="w-4 h-4" />
            {{ $t('New Conversation') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
        <span class="ml-2 text-slate-600 dark:text-slate-400">{{ $t('Loading conversations...') }}</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredConversations.length === 0" class="text-center py-8">
        <MessageCircle class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No conversations yet') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 mb-4">{{ $t('Start a conversation to discuss this ticket') }}</p>
        <button @click="startNewConversation" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
          <Plus class="w-4 h-4" />
          {{ $t('Start First Conversation') }}
        </button>
      </div>

      <!-- Conversations List -->
      <div v-else class="space-y-4">
        <div v-for="conversation in filteredConversations" :key="conversation.id" :data-conversation-id="conversation.id" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden">
          <!-- Conversation Header -->
          <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold" :class="getConversationTypeClass(conversation.type)">
                  <MessageCircle class="w-4 h-4" />
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h4 class="text-sm font-medium text-slate-900 dark:text-white">
                      {{ getConversationTitle(conversation) }}
                    </h4>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="getConversationTypeBadgeClass(conversation.type)">
                      {{ conversation.type === 'internal' ? $t('Internal') : $t('Customer') }}
                    </span>
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400">
                    {{ $t('Created') }} {{ moment(conversation.created_at).fromNow() }}
                    <span v-if="conversation.participants?.length" class="ml-2">
                      • {{ conversation.participants.length }} {{ $t('participants') }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button @click="toggleConversation(conversation.id)" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                  <ChevronDown v-if="!expandedConversations.includes(conversation.id)" class="w-4 h-4" />
                  <ChevronUp v-else class="w-4 h-4" />
                </button>
                <button @click="joinConversation(conversation)" class="p-1 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :title="$t('Join Conversation')">
                  <ExternalLink class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Conversation Details (Expandable) -->
          <div v-if="expandedConversations.includes(conversation.id)" class="p-4 space-y-4">
            <!-- Participants -->
            <div v-if="conversation.participants?.length">
              <h5 class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $t('Participants') }}</h5>
              <div class="flex flex-wrap gap-2">
                <div v-for="participant in conversation.participants" :key="participant.id" class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
                  <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                    {{ getInitials(participant.user?.name || participant.user?.first_name) }}
                  </div>
                  <span class="text-sm text-slate-700 dark:text-slate-300">{{ participant.user?.name || participant.user?.first_name }}</span>
                  <span class="text-xs text-slate-500 dark:text-slate-400 capitalize">{{ participant.role }}</span>
                </div>
              </div>
            </div>

            <!-- Recent Messages -->
            <div v-if="conversation.messages?.length">
              <h5 class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $t('Recent Messages') }}</h5>
              <div class="space-y-2 max-h-40 overflow-y-auto">
                <div v-for="message in conversation.messages.slice(-3)" :key="message.id" class="flex gap-2 p-2 bg-white dark:bg-slate-700 rounded border border-slate-200 dark:border-slate-600">
                  <div class="w-6 h-6 bg-gradient-to-br from-slate-500 to-slate-600 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                    {{ getInitials(message.user?.name || message.user?.first_name) }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                      <span class="text-xs font-medium text-slate-900 dark:text-white">{{ message.user?.name || message.user?.first_name }}</span>
                      <span class="text-xs text-slate-500 dark:text-slate-400">{{ moment(message.created_at).format('MMM D, h:mm A') }}</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2">{{ stripHtml(message.message) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Context Information -->
            <div v-if="conversation.context" class="p-3 bg-slate-100 dark:bg-slate-600 rounded-lg">
              <h5 class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $t('Context') }}</h5>
              <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
                <div v-if="conversation.context.ticket_uid">
                  <span class="font-medium">{{ $t('Ticket') }}:</span> #{{ conversation.context.ticket_uid }}
                </div>
                <div v-if="conversation.context.ticket_subject">
                  <span class="font-medium">{{ $t('Subject') }}:</span> {{ conversation.context.ticket_subject }}
                </div>
                <div v-if="conversation.context.ticket_status">
                  <span class="font-medium">{{ $t('Status') }}:</span> {{ conversation.context.ticket_status }}
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-600">
              <div class="flex items-center gap-2">
                <button @click="joinConversation(conversation)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                  <ExternalLink class="w-3 h-3" />
                  {{ $t('Join') }}
                </button>
                <button @click="viewConversationDetails(conversation)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-600 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-500 transition-colors">
                  <Eye class="w-3 h-3" />
                  {{ $t('Details') }}
                </button>
              </div>
              <div class="text-xs text-slate-500 dark:text-slate-400">
                {{ $t('Last activity') }} {{ moment(conversation.updated_at).fromNow() }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import {
  MessageCircle,
  RefreshCw,
  Plus,
  Loader2,
  ChevronDown,
  ChevronUp,
  ExternalLink,
  Eye
} from 'lucide-vue-next'

export default {
  name: 'ConversationManager',
  components: {
    MessageCircle,
    RefreshCw,
    Plus,
    Loader2,
    ChevronDown,
    ChevronUp,
    ExternalLink,
    Eye
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    },
    conversations: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    user: {
      type: Object,
      default: () => ({})
    }
  },
  computed: {
    filteredConversations() {
      if (!this.user?.id) {
        return []
      }
      
      return this.conversations.filter(conversation => {
        // Check if user is a participant in this conversation
        const isParticipant = conversation.participants?.some(
          participant => participant.user_id === this.user.id
        )
        
        // Check if user is admin/manager/agent
        const isAdmin = ['admin', 'manager', 'agent'].includes(this.user?.role?.slug)
        
        // Determine access permissions
        // - Participants can always access conversations they're part of
        // - Admins can access internal conversations (but not customer conversations they're not part of)
        return isParticipant || (isAdmin && conversation.type === 'internal')
      })
    }
  },
  data() {
    return {
      moment: moment,
      refreshing: false,
      expandedConversations: []
    }
  },
  mounted() {
    // Component mounted
  },
  methods: {
    async refreshConversations() {
      this.refreshing = true
      this.$emit('refresh-conversations')
      // Simulate refresh delay
      setTimeout(() => {
        this.refreshing = false
      }, 1000)
    },
    startNewConversation() {
      this.$emit('start-new-conversation')
    },
    toggleConversation(conversationId) {
      const index = this.expandedConversations.indexOf(conversationId)
      if (index > -1) {
        this.expandedConversations.splice(index, 1)
      } else {
        this.expandedConversations.push(conversationId)
      }
    },
    joinConversation(conversation) {
      this.$emit('join-conversation', conversation)
    },
    viewConversationDetails(conversation) {
      this.$emit('view-conversation-details', conversation)
    },
    getConversationTypeClass(type) {
      return type === 'internal' 
        ? 'bg-gradient-to-br from-slate-500 to-slate-600'
        : 'bg-gradient-to-br from-blue-500 to-blue-600'
    },
    getConversationTypeBadgeClass(type) {
      return type === 'internal'
        ? 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200'
        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
    },
    getConversationTitle(conversation) {
      if (conversation.context?.ticket_uid) {
        return `${conversation.type === 'internal' ? 'Internal' : 'Customer'} Discussion - #${conversation.context.ticket_uid}`
      }
      return `${conversation.type === 'internal' ? 'Internal' : 'Customer'} Discussion`
    },
    getInitials(name) {
      if (!name) return 'U'
      return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2)
    },
    stripHtml(html) {
      if (!html) return ''
      const temp = document.createElement('div')
      temp.innerHTML = html
      return temp.textContent || temp.innerText || ''
    }
  }
}
</script>

<style scoped>
.conversation-manager {
  @apply relative;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
