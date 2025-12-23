<template>
  <div class="w-80 h-96 bg-white rounded-lg shadow-2xl flex flex-col">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-lg px-4 py-3 flex items-center justify-between">
      <div class="flex items-center">
        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
          <Logo class="w-5 h-5 fill-white" />
        </div>
        <div>
          <h3 class="text-white font-semibold text-sm">{{ $t('Live Support') }}</h3>
          <div class="flex items-center">
            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
            <span class="text-blue-100 text-xs">{{ $t('Online now') }}</span>
          </div>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <button @click="toggleMinimize" class="text-blue-100 hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
          </svg>
        </button>
        <button @click="$emit('close')" class="text-blue-100 hover:text-white transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Messages Area -->
    <div 
      ref="messagesContainer" 
      class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50"
      @scroll="handleScroll"
    >
      <!-- Welcome Message -->
      <div v-if="!messages.length" class="text-center py-8">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
        </div>
        <h4 class="text-gray-700 font-semibold mb-2">{{ $t('Welcome to our support!') }}</h4>
        <p class="text-gray-500 text-sm">{{ $t('We\'re here to help. Send us a message to get started.') }}</p>
      </div>

      <!-- Messages -->
      <div v-for="message in messages" :key="message.id" class="flex" :class="message.contact_id ? 'justify-start' : 'justify-end'">
        <div class="flex max-w-xs lg:max-w-md" :class="message.contact_id ? 'flex-row' : 'flex-row-reverse'">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="message.contact_id ? 'bg-gray-300' : 'bg-blue-600'">
              <span class="text-xs font-semibold" :class="message.contact_id ? 'text-gray-600' : 'text-white'">
                {{ getInitials(message) }}
              </span>
            </div>
          </div>
          
          <!-- Message Content -->
          <div class="ml-2 mr-2" :class="message.contact_id ? 'ml-2' : 'mr-2'">
            <div class="px-4 py-2 rounded-lg" :class="message.contact_id ? 'bg-white shadow-sm' : 'bg-blue-600 text-white'">
              <div class="text-sm" v-html="formatMessage(message.message)"></div>
              <div class="flex items-center mt-1" :class="message.contact_id ? 'text-gray-500' : 'text-blue-100'">
                <span class="text-xs">{{ formatTime(message.created_at) }}</span>
                <div v-if="!message.contact_id" class="ml-2">
                  <svg v-if="message.status === 'sent'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                  </svg>
                  <svg v-else-if="message.status === 'delivered'" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Typing Indicator -->
      <div v-if="isTyping && typingUser && typingUser.id !== currentUserId" class="flex justify-start">
        <div class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm">
          <div class="flex space-x-1">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          </div>
          <span class="text-xs text-gray-500">
            {{ getTypingMessage() }}
          </span>
        </div>
      </div>
    </div>

    <!-- Input Area -->
    <div class="border-t bg-white rounded-b-lg">
      <!-- File Upload Preview -->
      <div v-if="uploadedFiles.length" class="px-4 py-2 border-b bg-gray-50">
        <div class="flex items-center space-x-2">
          <span class="text-xs text-gray-600">{{ $t('Attachments:') }}</span>
          <div v-for="(file, index) in uploadedFiles" :key="index" class="flex items-center space-x-1 bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
            <span>{{ file.name }}</span>
            <button @click="removeFile(index)" class="text-blue-600 hover:text-blue-800">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Message Input -->
      <div class="flex items-end p-4 space-x-2">
        <!-- Attachment Button -->
        <button @click="triggerFileUpload" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
          </svg>
        </button>

        <!-- Emoji Button -->
        <button @click="toggleEmojiPicker" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </button>

        <!-- Message Input -->
        <div class="flex-1 relative">
          <textarea
            v-model="message"
            @keydown="handleKeyDown"
            @input="handleTyping"
            :placeholder="$t('Type your message...')"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:outline-none focus:border-blue-500 text-sm"
            rows="1"
            ref="messageInput"
          ></textarea>
        </div>

        <!-- Send Button -->
        <button 
          @click="sendMessage" 
          :disabled="!message.trim() && !uploadedFiles.length"
          :class="[
            'p-2 rounded-lg transition-all duration-200',
            message.trim() || uploadedFiles.length 
              ? 'bg-blue-600 text-white hover:bg-blue-700' 
              : 'bg-gray-200 text-gray-400 cursor-not-allowed'
          ]"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
          </svg>
        </button>
      </div>

      <!-- Hidden File Input -->
      <input 
        ref="fileInput" 
        type="file" 
        multiple 
        accept="image/*,.pdf,.doc,.docx,.txt" 
        @change="handleFileUpload" 
        class="hidden"
      >
    </div>
  </div>
</template>

<script>
import Logo from '@/Shared/Logo.vue'
import moment from 'moment'

export default {
  components: { Logo },
  props: {
    messages: Array,
    isTyping: Boolean,
    typingUser: {
      type: Object,
      default: null
    },
    currentUserId: {
      type: [Number, String],
      default: null
    }
  },
  emits: ['send-message', 'close', 'typing'],
  data() {
    return {
      message: '',
      uploadedFiles: [],
      isMinimized: false
    }
  },
  methods: {
    getInitials(message) {
      if (!message) return '?'
      
      if (message.contact_id) {
        return 'C'
      }
      return 'A' // Agent
    },
    
    getTypingMessage() {
      if (!this.typingUser) return this.$t('Someone is typing...')
      
      // Check if it's a contact (customer) or user (agent)
      if (this.typingUser.contact_id) {
        return this.$t('Customer is typing...')
      } else if (this.typingUser.user_id) {
        return this.$t('Agent is typing...')
      }
      
      return this.$t('Someone is typing...')
    },
    
    formatMessage(message) {
      // Handle null/undefined cases
      if (!message) {
        return ''
      }
      
      // If message is an object, try to extract text content
      if (typeof message === 'object') {
        if (message.text) return message.text
        if (message.content) return message.content
        if (message.message) return message.message
        return JSON.stringify(message)
      }
      
      // Ensure it's a string
      const messageStr = String(message || '')
      
      // Basic HTML sanitization and formatting
      return messageStr.replace(/\n/g, '<br>')
    },
    
    formatTime(timestamp) {
      if (!timestamp) return ''
      return moment(timestamp).format('h:mm A')
    },
    
    handleKeyDown(event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault()
        this.sendMessage()
      }
    },
    
    handleTyping() {
      // Emit typing event with current user info
      // Note: This will be used to broadcast to other users, not to show typing indicator for self
      this.$emit('typing', {
        isTyping: true,
        user: {
          id: this.currentUserId,
          contact_id: null, // This will be set by the parent component
          user_id: this.currentUserId
        }
      })
    },
    
    sendMessage() {
      if (this.message.trim() || this.uploadedFiles.length) {
        this.$emit('send-message', {
          message: this.message,
          files: this.uploadedFiles
        })
        this.message = ''
        this.uploadedFiles = []
      }
    },
    
    triggerFileUpload() {
      this.$refs.fileInput.click()
    },
    
    handleFileUpload(event) {
      const files = Array.from(event.target.files)
      this.uploadedFiles.push(...files)
    },
    
    removeFile(index) {
      this.uploadedFiles.splice(index, 1)
    },
    
    toggleEmojiPicker() {
      // Emoji picker functionality - can be enhanced with a proper emoji picker component
      const emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😬', '🙄', '😯', '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐', '🥴', '🤢', '🤮', '🤧', '😷', '🤒', '🤕', '🤑', '🤠', '😈', '👿', '👹', '👺', '🤡', '💩', '👻', '💀', '☠️', '👽', '👾', '🤖', '🎃', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾']
      
      // Simple emoji insertion - in a real implementation, you'd show a proper emoji picker
      const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)]
      this.message += randomEmoji
    },
    
    toggleMinimize() {
      this.isMinimized = !this.isMinimized
    },
    
    handleScroll() {
      // Auto-scroll to bottom when new messages arrive
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer
        if (container) {
          container.scrollTop = container.scrollHeight
        }
      })
    }
  },
  
  watch: {
    messages: {
      handler() {
        this.handleScroll()
      },
      deep: true
    }
  }
}
</script>
