<template>
  <div class="real-time-messaging">
    <!-- Conversation Header -->
    <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
            <MessageCircle class="w-4 h-4" />
          </div>
          <div>
            <h3 class="text-sm font-medium text-slate-900 dark:text-white">{{ conversation.subject }}</h3>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
              <span>{{ conversation.participants?.length || 0 }} {{ $t('participants') }}</span>
              <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
              <span :class="connectionStatusClass">{{ connectionStatusText }}</span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button @click="toggleTypingIndicator" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Typing indicator')">
            <Edit3 class="w-4 h-4" />
          </button>
          <button @click="toggleNotifications" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Notifications')">
            <Bell v-if="notificationsEnabled" class="w-4 h-4" />
            <BellOff v-else class="w-4 h-4" />
          </button>
          <button @click="toggleFullscreen" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Fullscreen')">
            <Maximize2 class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Messages Container -->
    <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4" @scroll="handleScroll">
      <!-- Typing Indicators -->
      <div v-if="typingUsers.length > 0" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <div class="flex space-x-1">
          <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
          <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
          <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
        </div>
        <span>{{ typingUsersText }}</span>
      </div>

      <!-- Messages -->
      <div v-for="message in messages" :key="message.id" class="message-item">
        <div class="flex gap-3" :class="{ 'flex-row-reverse': message.user_id === currentUserId }">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-gradient-to-br from-slate-500 to-slate-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
              {{ getInitials(message.user?.name || message.user?.first_name) }}
            </div>
          </div>

          <!-- Message Content -->
          <div class="flex-1 min-w-0" :class="{ 'flex flex-col items-end': message.user_id === currentUserId }">
            <div class="flex items-center gap-2 mb-1" :class="{ 'flex-row-reverse': message.user_id === currentUserId }">
              <span class="text-sm font-medium text-slate-900 dark:text-white">{{ message.user?.name || message.user?.first_name }}</span>
              <span class="text-xs text-slate-500 dark:text-slate-400">{{ formatTime(message.created_at) }}</span>
              <span v-if="message.is_internal" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300">
                {{ $t('Internal') }}
              </span>
            </div>

            <div class="bg-white dark:bg-slate-700 rounded-lg p-3 shadow-sm border border-slate-200 dark:border-slate-600 max-w-md" :class="{ 'bg-blue-500 text-white': message.user_id === currentUserId }">
              <div class="prose dark:prose-invert max-w-none" v-html="message.message"></div>
              
              <!-- Message Actions -->
              <div class="flex items-center gap-2 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="replyToMessage(message)" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                  {{ $t('Reply') }}
                </button>
                <button @click="editMessage(message)" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                  {{ $t('Edit') }}
                </button>
                <button @click="deleteMessage(message)" class="text-xs text-red-500 hover:text-red-700">
                  {{ $t('Delete') }}
                </button>
              </div>
            </div>

            <!-- Message Status -->
            <div v-if="message.user_id === currentUserId" class="flex items-center gap-1 mt-1">
              <Check v-if="message.read_at" class="w-3 h-3 text-green-500" />
              <Clock v-else class="w-3 h-3 text-slate-400" />
              <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ message.read_at ? $t('Read') : $t('Sent') }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Load More Button -->
      <div v-if="hasMoreMessages" class="flex justify-center">
        <button @click="loadMoreMessages" :disabled="loadingMore" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors disabled:opacity-50">
          <Loader2 v-if="loadingMore" class="w-4 h-4 mr-2 animate-spin" />
          {{ $t('Load More Messages') }}
        </button>
      </div>
    </div>

    <!-- Message Input -->
    <div class="p-4 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-600">
      <div class="flex gap-3">
        <div class="flex-1">
          <div class="relative">
            <textarea
              ref="messageInput"
              v-model="newMessage"
              @keydown="handleKeyDown"
              @input="handleTyping"
              :placeholder="$t('Type your message...')"
              class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white resize-none"
              rows="3"
            ></textarea>
            
            <!-- Quick Actions -->
            <div class="absolute bottom-2 right-2 flex items-center gap-1">
              <button @click="toggleEmojiPicker" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <Smile class="w-4 h-4" />
              </button>
              <button @click="toggleFileUpload" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <Paperclip class="w-4 h-4" />
              </button>
              <button @click="toggleMentionPicker" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                <AtSign class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
        
        <button
          @click="sendMessage"
          :disabled="!newMessage.trim() || sending"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <Send v-if="!sending" class="w-4 h-4" />
          <Loader2 v-else class="w-4 h-4 animate-spin" />
        </button>
      </div>

      <!-- Typing Indicator -->
      <div v-if="isTyping" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
        {{ $t('You are typing...') }}
      </div>
    </div>

    <!-- Emoji Picker Modal -->
    <div v-if="showEmojiPicker" class="absolute bottom-16 right-4 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-600 p-4 z-10">
      <div class="grid grid-cols-8 gap-1">
        <button
          v-for="emoji in emojis"
          :key="emoji"
          @click="insertEmoji(emoji)"
          class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded text-lg"
        >
          {{ emoji }}
        </button>
      </div>
    </div>

    <!-- File Upload Modal -->
    <div v-if="showFileUpload" class="absolute bottom-16 right-4 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-600 p-4 z-10 w-64">
      <div class="space-y-3">
        <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Upload File') }}</h4>
        <input
          ref="fileInput"
          type="file"
          multiple
          @change="handleFileSelect"
          class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-slate-300"
        />
        <div class="flex justify-end gap-2">
          <button @click="showFileUpload = false" class="px-3 py-1 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200">
            {{ $t('Cancel') }}
          </button>
          <button @click="uploadFiles" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $t('Upload') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  MessageCircle,
  Edit3,
  Bell,
  BellOff,
  Maximize2,
  Check,
  Clock,
  Loader2,
  Smile,
  Paperclip,
  AtSign,
  Send
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'RealTimeMessaging',
  components: {
    MessageCircle,
    Edit3,
    Bell,
    BellOff,
    Maximize2,
    Check,
    Clock,
    Loader2,
    Smile,
    Paperclip,
    AtSign,
    Send
  },
  props: {
    conversation: {
      type: Object,
      required: true
    },
    currentUserId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      moment: moment,
      messages: [],
      newMessage: '',
      sending: false,
      loadingMore: false,
      hasMoreMessages: true,
      isTyping: false,
      typingUsers: [],
      notificationsEnabled: true,
      connectionStatus: 'connected', // 'connected', 'connecting', 'disconnected'
      showEmojiPicker: false,
      showFileUpload: false,
      selectedFiles: [],
      emojis: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏'],
      typingTimeout: null,
      scrollToBottom: true
    }
  },
  computed: {
    connectionStatusClass() {
      return {
        'text-green-500': this.connectionStatus === 'connected',
        'text-yellow-500': this.connectionStatus === 'connecting',
        'text-red-500': this.connectionStatus === 'disconnected'
      }
    },
    connectionStatusText() {
      switch (this.connectionStatus) {
        case 'connected': return this.$t('Connected')
        case 'connecting': return this.$t('Connecting...')
        case 'disconnected': return this.$t('Disconnected')
        default: return this.$t('Unknown')
      }
    },
    typingUsersText() {
      if (this.typingUsers.length === 0) return ''
      if (this.typingUsers.length === 1) {
        return `${this.typingUsers[0]} is typing...`
      }
      return `${this.typingUsers.join(', ')} are typing...`
    }
  },
  mounted() {
    this.initializeWebSocket()
    this.loadMessages()
    this.setupScrollListener()
  },
  beforeUnmount() {
    this.cleanupWebSocket()
    if (this.typingTimeout) {
      clearTimeout(this.typingTimeout)
    }
  },
  methods: {
    initializeWebSocket() {
      // Initialize WebSocket connection
      this.connectionStatus = 'connecting'
      
      // Simulate WebSocket connection
      setTimeout(() => {
        this.connectionStatus = 'connected'
        this.setupWebSocketListeners()
      }, 1000)
    },
    
    setupWebSocketListeners() {
      // Set up WebSocket event listeners
      // This would be replaced with actual WebSocket implementation
      console.log('WebSocket listeners set up')
    },
    
    cleanupWebSocket() {
      // Clean up WebSocket connection
      this.connectionStatus = 'disconnected'
    },
    
    async loadMessages() {
      try {
        // Load initial messages
        this.messages = this.conversation.messages || []
        this.$nextTick(() => {
          this.scrollToBottom()
        })
      } catch (error) {
        console.error('Error loading messages:', error)
      }
    },
    
    async loadMoreMessages() {
      this.loadingMore = true
      try {
        // Load more messages (pagination)
        // This would make an API call to load more messages
        await new Promise(resolve => setTimeout(resolve, 1000))
        this.loadingMore = false
      } catch (error) {
        console.error('Error loading more messages:', error)
        this.loadingMore = false
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return
      
      this.sending = true
      try {
        const message = {
          id: Date.now(),
          conversation_id: this.conversation.id,
          user_id: this.currentUserId,
          message: this.newMessage,
          is_internal: this.conversation.type === 'internal',
          created_at: new Date().toISOString(),
          user: {
            id: this.currentUserId,
            name: 'Current User' // This would come from the current user data
          }
        }
        
        // Add message to local state
        this.messages.push(message)
        this.newMessage = ''
        
        // Send via WebSocket
        this.sendWebSocketMessage('message', message)
        
        // Scroll to bottom
        this.$nextTick(() => {
          this.scrollToBottom()
        })
        
      } catch (error) {
        console.error('Error sending message:', error)
      } finally {
        this.sending = false
      }
    },
    
    sendWebSocketMessage(type, data) {
      // Send message via WebSocket
      console.log('Sending WebSocket message:', { type, data })
    },
    
    handleKeyDown(event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault()
        this.sendMessage()
      }
    },
    
    handleTyping() {
      this.isTyping = true
      
      // Send typing indicator
      this.sendWebSocketMessage('typing', {
        conversation_id: this.conversation.id,
        user_id: this.currentUserId
      })
      
      // Clear typing indicator after 3 seconds
      if (this.typingTimeout) {
        clearTimeout(this.typingTimeout)
      }
      this.typingTimeout = setTimeout(() => {
        this.isTyping = false
        this.sendWebSocketMessage('stop_typing', {
          conversation_id: this.conversation.id,
          user_id: this.currentUserId
        })
      }, 3000)
    },
    
    handleScroll(event) {
      const { scrollTop, scrollHeight, clientHeight } = event.target
      this.scrollToBottom = scrollTop + clientHeight >= scrollHeight - 10
    },
    
    setupScrollListener() {
      // Set up scroll listener for auto-scroll behavior
    },
    
    scrollToBottom() {
      if (this.scrollToBottom) {
        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight
      }
    },
    
    toggleTypingIndicator() {
      // Toggle typing indicator visibility
    },
    
    toggleNotifications() {
      this.notificationsEnabled = !this.notificationsEnabled
    },
    
    toggleFullscreen() {
      // Toggle fullscreen mode
    },
    
    toggleEmojiPicker() {
      this.showEmojiPicker = !this.showEmojiPicker
    },
    
    toggleFileUpload() {
      this.showFileUpload = !this.showFileUpload
    },
    
    toggleMentionPicker() {
      // Toggle mention picker
    },
    
    insertEmoji(emoji) {
      this.newMessage += emoji
      this.showEmojiPicker = false
      this.$refs.messageInput.focus()
    },
    
    handleFileSelect(event) {
      this.selectedFiles = Array.from(event.target.files)
    },
    
    async uploadFiles() {
      if (this.selectedFiles.length === 0) return
      
      try {
        // Upload files
        for (const file of this.selectedFiles) {
          // Upload file logic here
          console.log('Uploading file:', file.name)
        }
        
        this.selectedFiles = []
        this.showFileUpload = false
      } catch (error) {
        console.error('Error uploading files:', error)
      }
    },
    
    replyToMessage(message) {
      this.newMessage = `@${message.user?.name} `
      this.$refs.messageInput.focus()
    },
    
    editMessage(message) {
      // Edit message functionality
    },
    
    deleteMessage(message) {
      // Delete message functionality
    },
    
    formatTime(timestamp) {
      return moment(timestamp).format('h:mm A')
    },
    
    getInitials(name) {
      if (!name) return 'U'
      return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2)
    }
  }
}
</script>

<style scoped>
.real-time-messaging {
  @apply flex flex-col h-full;
}

.message-item {
  @apply group;
}

/* Custom scrollbar */
.messages-container::-webkit-scrollbar {
  width: 6px;
}

.messages-container::-webkit-scrollbar-track {
  @apply bg-slate-100 dark:bg-slate-700;
}

.messages-container::-webkit-scrollbar-thumb {
  @apply bg-slate-300 dark:bg-slate-600 rounded-full;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  @apply bg-slate-400 dark:bg-slate-500;
}
</style>
