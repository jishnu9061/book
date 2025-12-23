<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <button @click="goBack" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
              <ArrowLeft class="w-5 h-5" />
            </button>
            <div>
              <h1 class="text-xl font-semibold text-slate-900 dark:text-white">
                {{ $t('Conversation') }} #{{ conversation.id }}
              </h1>
              <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Ticket') }} #{{ conversation.ticket?.uid }} - {{ conversation.ticket?.subject }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getConversationTypeBadgeClass(conversation.type)">
              {{ conversation.type === 'internal' ? $t('Internal') : $t('Customer') }}
            </span>
            <button @click="refreshConversation" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
              <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': refreshing }" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="flex h-[calc(100vh-80px)]">
      <!-- Participants Sidebar -->
      <div class="w-72 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
            {{ $t('Participants') }} ({{ conversation.participants.length }})
          </h3>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
          <div v-for="participant in conversation.participants" :key="participant.id" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
            <div class="relative">
              <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold shadow-sm" :class="getParticipantColor(participant.role)">
                {{ getInitials(participant.user?.name || participant.user?.first_name + ' ' + participant.user?.last_name) }}
              </div>
              <div v-if="participant.user_id === user.id" class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-slate-800"></div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                {{ participant.user?.name || participant.user?.first_name + ' ' + participant.user?.last_name }}
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400">{{ getRoleLabel(participant.role) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Messages Area -->
      <div class="flex-1 flex flex-col min-w-0">
        <!-- Messages List - Increased height -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-6 messages-container bg-slate-50/50 dark:bg-slate-900/50">
          <div v-for="message in messages" :key="message.id" class="flex gap-4 message-item group">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 shadow-sm" :class="getMessageUserColor(message.user?.id)">
              {{ getInitials(message.user?.name || message.user?.first_name + ' ' + message.user?.last_name) }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-2">
                <span class="text-sm font-semibold text-slate-900 dark:text-white">
                  {{ message.user?.name || message.user?.first_name + ' ' + message.user?.last_name }}
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                  {{ moment(message.created_at).format('MMM D, h:mm A') }}
                </span>
                <div class="flex-1"></div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                  <button class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1 rounded">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                    </svg>
                  </button>
                </div>
              </div>
              <!-- Enhanced message display with HTML formatting -->
              <div class="message-content prose dark:prose-invert max-w-none text-sm bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700" v-html="formatMessage(message.message)"></div>
            </div>
          </div>
        </div>

        <!-- Compact Message Input - Decreased height -->
        <div class="border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 shadow-lg">
          <div class="space-y-3">
            <!-- Compact RichEditor for message input -->
            <div class="rich-editor-container">
              <RichEditor
                v-model="newMessage"
                :id="'conversation-editor-' + conversation.id"
                :height="150"
                :compact="true"
                :toolbar="'undo redo | bold italic underline | emoticons | link | bullist numlist | removeformat'"
                :plugins="'emoticons link lists'"
                :placeholder="$t('Type a message...')"
                @update:modelValue="onMessageChange"
              />
            </div>

            <!-- Compact Input Actions -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <!-- Formatting Help -->
                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                  <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs border border-slate-200 dark:border-slate-600">Ctrl</kbd>
                  <span>+</span>
                  <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs border border-slate-200 dark:border-slate-600">Enter</kbd>
                  <span class="ml-1">{{ $t('to send') }}</span>
                </div>
              </div>

              <!-- Send Button -->
              <button
                type="button"
                @click="sendMessage"
                :disabled="!newMessage.trim() || sending"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center gap-2 shadow-sm hover:shadow-md font-medium"
              >
                <Send v-if="!sending" class="w-4 h-4" />
                <Loader2 v-else class="w-4 h-4 animate-spin" />
                {{ $t('Send') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import RichEditor from '@/Shared/RichEditor.vue'
import moment from 'moment'
import axios from 'axios'
import {
  ArrowLeft,
  RefreshCw,
  Send,
  Loader2
} from 'lucide-vue-next'

export default {
  name: 'ConversationView',
  components: {
    Link,
    Head,
    RichEditor,
    ArrowLeft,
    RefreshCw,
    Send,
    Loader2
  },
  layout: Layout,
  props: {
    conversation: {
      type: Object,
      required: true
    },
    user: {
      type: Object,
      required: true
    },
    availableUsers: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      moment: moment,
      newMessage: '',
      sending: false,
      refreshing: false,
      localMessages: [], // Local copy to avoid mutating prop
      hasLocalMessages: false // Flag to track if we've initialized local messages
    }
  },
  computed: {
    // Use local messages if we've initialized them, otherwise fall back to prop
    messages() {
      // If we've initialized local messages (hasLocalMessages flag), always use local copy
      // This prevents mutating the prop which breaks Inertia navigation
      if (this.hasLocalMessages) {
        return this.localMessages
      }
      // Before initialization, use prop (this only happens during initial render)
      return this.conversation?.messages || []
    }
  },
  watch: {
    // Sync with prop when conversation changes
    'conversation.messages': {
      handler(newMessages) {
        if (newMessages && Array.isArray(newMessages)) {
          this.localMessages = [...newMessages]
          this.hasLocalMessages = true
        }
      },
      immediate: true,
      deep: true
    }
  },
  mounted() {
    // Initialize local messages from prop (always initialize, even if empty)
    this.localMessages = Array.isArray(this.conversation?.messages) 
      ? [...this.conversation.messages] 
      : []
    this.hasLocalMessages = true
    this.scrollToBottom()
    this.setupKeyboardShortcuts()
  },
  beforeUnmount() {
    this.removeKeyboardShortcuts()
  },
  methods: {
    getInitials(name) {
      if (!name) return 'U'
      return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2)
    },
    formatMessage(message) {
      if (!message) return ''

      // Convert line breaks to HTML
      let formatted = message.replace(/\n/g, '<br>')

      // Convert URLs to clickable links
      const urlRegex = /(https?:\/\/[^\s]+)/g
      formatted = formatted.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">$1</a>')

      // Convert email addresses to mailto links
      const emailRegex = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/g
      formatted = formatted.replace(emailRegex, '<a href="mailto:$1" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline">$1</a>')

      return formatted
    },
    onMessageChange(value) {
      this.newMessage = value
    },
    setupKeyboardShortcuts() {
      this.keydownHandler = (event) => {
        // Ctrl+Enter to send message
        if (event.ctrlKey && event.key === 'Enter') {
          event.preventDefault()
          this.sendMessage()
        }
      }
      document.addEventListener('keydown', this.keydownHandler)
    },
    removeKeyboardShortcuts() {
      if (this.keydownHandler) {
        document.removeEventListener('keydown', this.keydownHandler)
      }
    },
    getConversationTypeBadgeClass(type) {
      return type === 'internal'
        ? 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200'
        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
    },
    getParticipantColor(role) {
      switch (role) {
        case 'customer':
          return 'bg-blue-500'
        case 'agent':
          return 'bg-green-500'
        case 'participant':
          return 'bg-purple-500'
        default:
          return 'bg-slate-500'
      }
    },
    getMessageUserColor(userId) {
      if (userId === this.user.id) {
        return 'bg-blue-500'
      }
      return 'bg-slate-500'
    },
    getRoleLabel(role) {
      switch (role) {
        case 'customer':
          return this.$t('Customer')
        case 'agent':
          return this.$t('Agent')
        case 'participant':
          return this.$t('Participant')
        default:
          return this.$t('User')
      }
    },
    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return

      // Get the HTML content from RichEditor first
      const editorId = 'conversation-editor-' + this.conversation.id
      const editor = window.tinymce?.get(editorId)
      const messageContent = editor ? editor.getContent() : this.newMessage

      // Early return if message is empty (before setting sending state)
      if (!messageContent.trim()) return

      // Store original message before clearing
      const originalMessage = messageContent
      
      // Declare message variable outside try block to ensure it's accessible in catch
      let message = null

      this.sending = true
      try {
        // Clear the message inputs
        this.newMessage = ''
        if (editor) {
          editor.setContent('')
        }

        // Add the message to the local messages array immediately for better UX
        message = {
          id: Date.now(),
          message: originalMessage,
          user: this.user,
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString()
        }
        this.localMessages.push(message)
        this.$nextTick(() => {
          this.scrollToBottom()
        })

        // Send via API using the conversation-specific route
        const response = await axios.post(route('conversations.send-message', this.conversation.id), {
          message: messageContent
        }, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })

        if (response.data && response.data.success) {
          console.log('Message sent successfully via API:', response.data)
          // Update the local message with the server response if needed
          if (response.data.data) {
            const serverMessage = response.data.data
            // Find and update the local message with server data
            const localIndex = this.localMessages.findIndex(m => m.id === message.id)
            if (localIndex !== -1) {
              // Update with server message data (Vue 3 - direct assignment works)
              this.localMessages[localIndex] = {
                ...message,
                id: serverMessage.id || message.id,
                created_at: serverMessage.created_at || message.created_at,
                updated_at: serverMessage.updated_at || message.updated_at
              }
            }
          }
        } else {
          throw new Error(response.data?.message || response.data?.error || 'Failed to send message')
        }

      } catch (error) {
        console.error('Error sending message:', error)
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        })
        
        // Remove the optimistic message from the array (only if it was added)
        if (message) {
          const messageIndex = this.localMessages.findIndex(m => m.id === message.id && m.user?.id === this.user.id)
          if (messageIndex !== -1) {
            this.localMessages.splice(messageIndex, 1)
          }
        }
        
        // Restore message on error
        this.newMessage = originalMessage
        const editorId = 'conversation-editor-' + this.conversation.id
        const editor = window.tinymce?.get(editorId)
        if (editor) {
          editor.setContent(originalMessage)
        }
        
        // Show user-friendly error message
        const errorMessage = error.response?.data?.message || error.response?.data?.error || error.message || 'Failed to send message. Please try again.'
        alert('Error sending message: ' + errorMessage)
      } finally {
        this.sending = false
      }
    },
    async refreshConversation() {
      this.refreshing = true
      try {
        // Here you would refresh the conversation data
        await new Promise(resolve => setTimeout(resolve, 1000))
      } catch (error) {
        console.error('Error refreshing conversation:', error)
      } finally {
        this.refreshing = false
      }
    },
    scrollToBottom() {
      this.$nextTick(() => {
        if (this.$refs.messagesContainer) {
          this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight
        }
      })
    },
    goBack() {
      try {
        // Try to use browser history first
        if (window.history.length > 1) {
          window.history.back()
        } else {
          // Fallback to ticket details if no history
          this.navigateToTicket()
        }
      } catch (error) {
        console.warn('History back failed, using fallback navigation:', error)
        this.navigateToTicket()
      }
    },
    navigateToTicket() {
      const ticketUid = this.conversation.ticket?.uid
      if (ticketUid) {
        this.$inertia.visit(route('tickets.show', ticketUid), {
          preserveState: false,
          preserveScroll: false,
          replace: false
        })
      } else {
        // If no ticket, go to tickets index
        this.$inertia.visit(route('tickets.index'), {
          preserveState: false,
          preserveScroll: false,
          replace: false
        })
      }
    }
  }
}
</script>

<style scoped>
/* Modern Web Standards - Enhanced Layout */

/* Enhanced message content styling with modern design */
.message-content {
  @apply break-words leading-relaxed;
}

.message-content :deep(p) {
  @apply mb-3 last:mb-0;
}

.message-content :deep(ul),
.message-content :deep(ol) {
  @apply ml-6 mb-3 space-y-1;
}

.message-content :deep(li) {
  @apply mb-1;
}

.message-content :deep(blockquote) {
  @apply border-l-4 border-blue-500 dark:border-blue-400 pl-4 italic text-slate-600 dark:text-slate-400 my-3 bg-blue-50 dark:bg-blue-900/20 py-2 rounded-r-lg;
}

.message-content :deep(code) {
  @apply bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-sm font-medium;
  font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', 'Courier New', monospace;
}

.message-content :deep(pre) {
  @apply bg-slate-100 dark:bg-slate-700 p-4 rounded-lg overflow-x-auto my-3 border border-slate-200 dark:border-slate-600;
}

.message-content :deep(pre code) {
  @apply bg-transparent p-0 border-0;
}

.message-content :deep(a) {
  @apply text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline decoration-2 underline-offset-2 break-all transition-colors;
}

.message-content :deep(strong) {
  @apply font-semibold text-slate-900 dark:text-slate-100;
}

.message-content :deep(em) {
  @apply italic text-slate-700 dark:text-slate-300;
}

.message-content :deep(u) {
  @apply underline decoration-2 underline-offset-2;
}

.message-content :deep(s) {
  @apply line-through text-slate-500 dark:text-slate-400;
}

/* Compact RichEditor container styling */
.rich-editor-container {
  @apply border border-slate-300 dark:border-slate-600 rounded-xl overflow-hidden shadow-sm;
}

.rich-editor-container :deep(.tox-tinymce) {
  @apply border-0 rounded-xl;
}

.rich-editor-container :deep(.tox-editor-header) {
  @apply border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700;
}

.rich-editor-container :deep(.tox-toolbar) {
  @apply bg-slate-50 dark:bg-slate-700 px-2;
}

.rich-editor-container :deep(.tox-edit-area) {
  @apply bg-white dark:bg-slate-800;
}

.rich-editor-container :deep(.tox-edit-area__iframe) {
  @apply bg-white dark:bg-slate-800;
}

/* Modern keyboard shortcut styling */
kbd {
  @apply text-xs font-medium;
  font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', 'Courier New', monospace;
}

/* Enhanced scrollbar styling for messages */
.messages-container::-webkit-scrollbar {
  @apply w-3;
}

.messages-container::-webkit-scrollbar-track {
  @apply bg-transparent;
}

.messages-container::-webkit-scrollbar-thumb {
  @apply bg-slate-300 dark:bg-slate-600 rounded-full border-2 border-transparent;
  background-clip: content-box;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  @apply bg-slate-400 dark:bg-slate-500;
}

/* Modern animation for new messages */
@keyframes messageSlideIn {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.message-item {
  animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced hover effects */
.message-item:hover {
  @apply transform transition-transform duration-200;
}

/* Modern shadow and border effects */
.message-content {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.dark .message-content {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
}

/* Improved focus states for accessibility */
button:focus-visible {
  @apply outline-none ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-800;
}

/* Modern button hover effects */
button {
  @apply transition-all duration-200 ease-in-out;
}

/* Enhanced participant sidebar styling */
.participant-item {
  @apply transition-all duration-200 ease-in-out;
}

.participant-item:hover {
  @apply transform translate-x-1;
}

/* Modern typography improvements */
h1, h2, h3, h4, h5, h6 {
  @apply font-semibold tracking-tight;
}

/* Improved contrast for better accessibility */
.text-slate-500 {
  color: rgb(100 116 139);
}

.dark .text-slate-500 {
  color: rgb(148 163 184);
}

/* Modern spacing and layout */
.space-y-6 > * + * {
  margin-top: 1.5rem;
}

/* Enhanced dark mode support */
.dark .message-content :deep(blockquote) {
  @apply border-blue-400 text-slate-300 bg-blue-900/30;
}

.dark .message-content :deep(code) {
  @apply bg-slate-700 text-slate-200 border border-slate-600;
}

.dark .message-content :deep(pre) {
  @apply bg-slate-800 border-slate-600;
}

.dark .message-content :deep(pre code) {
  @apply bg-transparent text-slate-200;
}

/* Responsive design improvements */
@media (max-width: 768px) {
  .messages-container {
    @apply p-4 space-y-4;
  }

  .message-content {
    @apply p-3;
  }

  .rich-editor-container {
    @apply rounded-lg;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .message-content {
    @apply border-2 border-slate-400 dark:border-slate-500;
  }

  .rich-editor-container {
    @apply border-2 border-slate-400 dark:border-slate-500;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .message-item {
    animation: none;
  }

  button, .participant-item {
    @apply transition-none;
  }
}
</style>
