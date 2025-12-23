/**
 * Enhanced Chat Manager for real-time features
 */
class ChatManager {
    constructor() {
        this.conversationId = null
        this.contactId = null
        this.isTyping = false
        this.typingTimeout = null
        this.connectionStatus = 'disconnected'
        this.retryCount = 0
        this.maxRetries = 5
        this.retryDelay = 1000
        
        this.init()
    }

    init() {
        this.setupConnectionMonitoring()
        this.setupTypingIndicators()
        this.setupMessageQueue()
    }

    /**
     * Initialize chat session
     */
    async initializeChat(userData) {
        try {
            const response = await axios.post('/chat/init', userData)
            
            if (response.data.success) {
                this.conversationId = response.data.conversation.id
                this.contactId = response.data.conversation.contact_id
                
                // Store in localStorage
                localStorage.setItem('chat_id', this.conversationId)
                localStorage.setItem('contact_id', this.contactId)
                
                // Setup real-time listeners
                this.setupRealtimeListeners()
                
                return response.data.conversation
            } else {
                throw new Error(response.data.message || 'Failed to initialize chat')
            }
        } catch (error) {
            console.error('Chat initialization failed:', error)
            throw error
        }
    }

    /**
     * Send message with enhanced features
     */
    async sendMessage(messageData) {
        try {
            const formData = new FormData()
            formData.append('message', messageData.message)
            formData.append('conversation_id', this.conversationId)
            formData.append('contact_id', this.contactId)
            
            // Add file attachments
            if (messageData.files && messageData.files.length > 0) {
                messageData.files.forEach((file, index) => {
                    formData.append(`attachments[${index}]`, file)
                })
            }

            const response = await axios.post('/chat/sendMessage', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })

            if (response.data.success) {
                return response.data.message
            } else {
                throw new Error(response.data.message || 'Failed to send message')
            }
        } catch (error) {
            console.error('Failed to send message:', error)
            throw error
        }
    }

    /**
     * Setup real-time listeners
     */
    setupRealtimeListeners() {
        if (!window.PusherManager || !window.PusherManager.echo) {
            console.warn('PusherManager not available')
            return
        }

        // Listen for new messages
        window.PusherManager.listenPrivate(
            `public-chat.${this.conversationId}`,
            'NewPublicChatMessage',
            (data) => {
                this.handleNewMessage(data.chatMessage)
            }
        )

        // Listen for typing indicators
        window.PusherManager.listenPrivate(
            `public-chat.${this.conversationId}`,
            'TypingIndicator',
            (data) => {
                this.handleTypingIndicator(data)
            }
        )

        // Listen for agent status updates
        window.PusherManager.listenPrivate(
            `public-chat.${this.conversationId}`,
            'AgentStatusUpdate',
            (data) => {
                this.handleAgentStatusUpdate(data)
            }
        )
    }

    /**
     * Handle new message
     */
    handleNewMessage(message) {
        // Emit event for Vue component to handle
        window.dispatchEvent(new CustomEvent('chat:new-message', {
            detail: { message }
        }))
    }

    /**
     * Handle typing indicator
     */
    handleTypingIndicator(data) {
        // Emit event for Vue component to handle
        window.dispatchEvent(new CustomEvent('chat:typing-indicator', {
            detail: { 
                isTyping: data.is_typing,
                user: data.user,
                contact: data.contact
            }
        }))
    }

    /**
     * Handle agent status update
     */
    handleAgentStatusUpdate(data) {
        // Emit event for Vue component to handle
        window.dispatchEvent(new CustomEvent('chat:agent-status', {
            detail: { 
                status: data.status,
                agent: data.agent
            }
        }))
    }

    /**
     * Send typing indicator
     */
    sendTypingIndicator(isTyping) {
        if (!this.conversationId) return

        // Debounce typing indicator
        clearTimeout(this.typingTimeout)
        
        if (isTyping) {
            this.typingTimeout = setTimeout(() => {
                this.broadcastTypingIndicator(true)
            }, 500)
        } else {
            this.broadcastTypingIndicator(false)
        }
    }

    /**
     * Broadcast typing indicator
     */
    broadcastTypingIndicator(isTyping) {
        if (!window.PusherManager || !window.PusherManager.echo) return

        window.PusherManager.echo.channel(`public-chat.${this.conversationId}`)
            .whisper('typing', {
                is_typing: isTyping,
                contact_id: this.contactId,
                timestamp: new Date().toISOString()
            })
    }

    /**
     * Setup connection monitoring
     */
    setupConnectionMonitoring() {
        if (!window.PusherManager) return

        window.PusherManager.onConnectionChange((status) => {
            this.connectionStatus = status
            this.handleConnectionChange(status)
        })

        window.PusherManager.onError((error) => {
            this.handleConnectionError(error)
        })
    }

    /**
     * Handle connection changes
     */
    handleConnectionChange(status) {
        window.dispatchEvent(new CustomEvent('chat:connection-change', {
            detail: { status }
        }))

        if (status === 'connected' && this.conversationId) {
            this.setupRealtimeListeners()
        }
    }

    /**
     * Handle connection errors
     */
    handleConnectionError(error) {
        console.error('Chat connection error:', error)
        
        if (this.retryCount < this.maxRetries) {
            this.retryCount++
            setTimeout(() => {
                this.retupRealtimeListeners()
            }, this.retryDelay * this.retryCount)
        }

        window.dispatchEvent(new CustomEvent('chat:connection-error', {
            detail: { error }
        }))
    }

    /**
     * Setup typing indicators
     */
    setupTypingIndicators() {
        // This would be implemented based on your specific needs
        // For now, it's a placeholder
    }

    /**
     * Setup message queue for offline support
     */
    setupMessageQueue() {
        // Implement message queuing for offline scenarios
        this.messageQueue = []
    }

    /**
     * Get conversation history
     */
    async getConversation(conversationId, contactId) {
        try {
            const response = await axios.get(`/chat/getConversation/${conversationId}/${contactId}`)
            
            if (response.data.success) {
                return response.data.conversation
            } else {
                throw new Error(response.data.message || 'Failed to get conversation')
            }
        } catch (error) {
            console.error('Failed to get conversation:', error)
            throw error
        }
    }

    /**
     * Mark messages as read
     */
    async markAsRead(messageIds) {
        try {
            await axios.post('/chat/markAsRead', {
                message_ids: messageIds,
                conversation_id: this.conversationId
            })
        } catch (error) {
            console.error('Failed to mark messages as read:', error)
        }
    }

    /**
     * End chat session
     */
    endChat() {
        if (this.conversationId) {
            // Notify server that chat is ending
            axios.post('/chat/end', {
                conversation_id: this.conversationId
            }).catch(error => {
                console.error('Failed to end chat:', error)
            })
        }

        // Clear local storage
        localStorage.removeItem('chat_id')
        localStorage.removeItem('contact_id')
        
        // Reset state
        this.conversationId = null
        this.contactId = null
    }

    /**
     * Get connection status
     */
    getConnectionStatus() {
        return this.connectionStatus
    }

    /**
     * Check if connected
     */
    isConnected() {
        return this.connectionStatus === 'connected'
    }
}

// Export for use in Vue components
window.ChatManager = ChatManager
