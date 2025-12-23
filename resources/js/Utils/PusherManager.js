/**
 * Enhanced Pusher Connection Manager
 * Handles Pusher connection, reconnection, and error management with improved chat integration
 */

class PusherManager {
    constructor() {
        this.echo = null
        this.connectionStatus = 'disconnected'
        this.reconnectAttempts = 0
        this.maxReconnectAttempts = 5
        this.reconnectDelay = 1000
        this.listeners = new Map()
        this.connectionCallbacks = []
        this.errorCallbacks = []
        this.chatCallbacks = new Map() // Store chat-specific callbacks
        this.retryTimeout = null
        this.heartbeatInterval = null
        this.isInitialized = false
        
        this.init()
    }

    init() {
        // Prevent multiple initializations
        if (this.isInitialized) {
            console.log('PusherManager: Already initialized')
            return
        }
        
        // Check if we have a CSRF token (indicating authentication)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        this.isAuthenticated = !!csrfToken
        
        // Wait for Echo to be available and fully ready
        const checkEcho = () => {
            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                this.echo = window.Echo
                this.setupConnectionMonitoring()
                this.startHeartbeat()
                this.isInitialized = true
                console.log('PusherManager: Initialized successfully', this.isAuthenticated ? '(authenticated)' : '(public only)')
                console.log('PusherManager: Echo connector state:', window.Echo.connector.pusher.connection.state)
            } else {
                console.warn('PusherManager: Echo not fully ready, retrying...', {
                    echoExists: !!window.Echo,
                    connectorExists: !!window.Echo?.connector,
                    pusherExists: !!window.Echo?.connector?.pusher
                })
                setTimeout(checkEcho, 100)
            }
        }
        
        checkEcho()
    }

    setupConnectionMonitoring() {
        if (!this.echo || !this.echo.connector || !this.echo.connector.pusher) {
            console.warn('PusherManager: Cannot setup connection monitoring - Echo or Pusher not available')
            return
        }

        const pusher = this.echo.connector.pusher

        // Check if already bound to prevent duplicate bindings
        if (this.connectionMonitoringSetup) {
            console.log('PusherManager: Connection monitoring already set up')
            return
        }

        pusher.connection.bind('connected', () => {
            this.connectionStatus = 'connected'
            this.reconnectAttempts = 0
            this.notifyConnectionCallbacks('connected')
            console.log('PusherManager: Connected to Pusher')
            
            // Log connection details for debugging
            console.log('PusherManager: Connection details', {
                socketId: pusher.connection.socket_id,
                transport: pusher.connection.transport?.name,
                state: pusher.connection.state
            })
            
            // Restart heartbeat on connection
            this.startHeartbeat()
            
            // Restore chat listeners after reconnection
            this.restoreChatListeners()
        })

        pusher.connection.bind('connecting', () => {
            this.connectionStatus = 'connecting'
            this.notifyConnectionCallbacks('connecting')
            console.log('PusherManager: Connecting to Pusher...')
        })

        pusher.connection.bind('disconnected', () => {
            this.connectionStatus = 'disconnected'
            this.notifyConnectionCallbacks('disconnected')
            console.log('PusherManager: Disconnected from Pusher')
            
            // Stop heartbeat on disconnection
            this.stopHeartbeat()
        })

        pusher.connection.bind('unavailable', () => {
            this.connectionStatus = 'unavailable'
            this.notifyConnectionCallbacks('unavailable')
            console.log('PusherManager: Pusher unavailable')
        })

        pusher.connection.bind('error', (error) => {
            // Only log error if it's not a duplicate or expected error
            if (!this.isExpectedError(error)) {
                this.connectionStatus = 'error'
                this.notifyErrorCallbacks(error)
                console.error('PusherManager: Connection error:', error)
                
                // Attempt reconnection for certain errors
                this.handleConnectionError(error)
            }
        })

        pusher.connection.bind('state_change', (states) => {
            console.log('PusherManager: State changed from', states.previous, 'to', states.current)
            this.notifyConnectionCallbacks(states.current)
        })

        this.connectionMonitoringSetup = true
    }

    startHeartbeat() {
        // Clear existing heartbeat
        this.stopHeartbeat()
        
        // Monitor connection status every 30 seconds
        this.heartbeatInterval = setInterval(() => {
            if (this.isConnected()) {
                // Check if connection is still alive by verifying the connection state
                this.checkConnectionHealth()
            }
        }, 30000)
    }

    stopHeartbeat() {
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval)
            this.heartbeatInterval = null
        }
    }

    checkConnectionHealth() {
        // Check connection health without using ping
        if (this.echo && this.echo.connector && this.echo.connector.pusher) {
            try {
                const pusher = this.echo.connector.pusher
                const connectionState = pusher.connection.state
                
                // If connection is not in a healthy state, attempt to reconnect
                if (connectionState !== 'connected' && connectionState !== 'connecting') {
                    console.log('PusherManager: Connection unhealthy, attempting reconnection')
                    this.reconnect()
                }
            } catch (error) {
                console.warn('PusherManager: Connection health check failed:', error)
            }
        }
    }

    isExpectedError(error) {
        // Filter out common expected errors that don't need to be logged repeatedly
        if (!error) return true
        
        const errorMessage = error.message || (error.data && error.data.message) || ''
        const errorType = error.type || ''
        
        const expectedErrors = [
            'PusherError',
            'Connection failed',
            'Authentication failed',
            'Authorization failed',
            'Invalid key',
            'Connection refused',
            'WebSocket connection failed',
            'Transport closed'
        ]
        
        return expectedErrors.some(expected => 
            errorMessage.includes(expected) || errorType.includes(expected)
        )
    }

    handleConnectionError(error) {
        // Only attempt reconnection for certain types of errors
        const retryableErrors = [
            'Connection failed',
            'Network error',
            'Timeout',
            'WebSocket connection failed',
            'Transport closed'
        ]
        
        const errorMessage = error.message || (error.data && error.data.message) || ''
        const shouldRetry = retryableErrors.some(retryable => 
            errorMessage.includes(retryable)
        )
        
        if (shouldRetry && this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++
            const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1)
            
            console.log(`PusherManager: Attempting reconnection ${this.reconnectAttempts}/${this.maxReconnectAttempts} in ${delay}ms`)
            
            // Clear any existing retry timeout
            if (this.retryTimeout) {
                clearTimeout(this.retryTimeout)
            }
            
            this.retryTimeout = setTimeout(() => {
                if (this.echo && this.echo.connector && this.echo.connector.pusher) {
                    this.echo.connector.pusher.connect()
                }
                this.retryTimeout = null
            }, delay)
        } else if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.warn('PusherManager: Max reconnection attempts reached')
            this.connectionStatus = 'failed'
            this.notifyConnectionCallbacks('failed')
        }
    }

    /**
     * Listen to a private channel
     */
    listenPrivate(channelName, eventName, callback) {
        if (!this.echo) {
            console.warn('PusherManager: Echo not available for private channel:', channelName)
            return null
        }

        if (this.connectionStatus === 'unavailable') {
            console.warn('PusherManager: Cannot listen to private channels - not authenticated')
            return null
        }

        const listener = this.echo.private(channelName)
            .listen(eventName, callback)
            .error((error) => {
                console.error(`PusherManager: Error listening to private channel ${channelName}:`, error)
                this.notifyErrorCallbacks(error)
            })

        // Store listener for cleanup
        const key = `private:${channelName}:${eventName}`
        this.listeners.set(key, listener)

        return listener
    }

    /**
     * Listen to a public channel
     */
    listenPublic(channelName, eventName, callback) {
        if (!this.echo) {
            console.warn('PusherManager: Echo not available for public channel:', channelName)
            return null
        }

        // Public channels can work even when connection status is 'unavailable' (unauthenticated)
        console.log(`PusherManager: Setting up public channel listener`, {
            channelName,
            eventName,
            echoExists: !!this.echo,
            connectorExists: !!this.echo.connector,
            pusherExists: !!this.echo.connector?.pusher,
            connectionState: this.echo.connector?.pusher?.connection?.state
        })

        const channel = this.echo.channel(channelName)
        console.log(`PusherManager: Channel created:`, channel)
        
        const listener = channel
            .listen(eventName, (data) => {
                console.log(`PusherManager: Event received on ${channelName}:`, data)
                console.log(`PusherManager: Calling callback for ${channelName}:`, typeof callback)
                try {
                    callback(data)
                    console.log(`PusherManager: Callback executed successfully for ${channelName}`)
                } catch (error) {
                    console.error(`PusherManager: Error in callback for ${channelName}:`, error)
                }
            })
            .error((error) => {
                console.error(`PusherManager: Error listening to public channel ${channelName}:`, error)
                this.notifyErrorCallbacks(error)
            })

        // Store listener for cleanup
        const key = `public:${channelName}:${eventName}`
        this.listeners.set(key, listener)

        console.log(`PusherManager: Listening to public channel ${channelName} for event ${eventName}`)
        return listener
    }

    /**
     * Listen to chat messages with enhanced error handling
     */
    listenToChat(conversationId, callback) {
        const channelName = `chat.${conversationId}`
        const eventName = 'NewChatMessage'
        
        console.log(`PusherManager: Setting up chat listener`, {
            conversationId,
            channelName,
            eventName,
            connectionStatus: this.connectionStatus
        })
        
        // Store chat callback for potential reconnection
        this.chatCallbacks.set(conversationId, callback)
        
        // Use public channel for chat (works for both authenticated and unauthenticated users)
        return this.listenPublic(channelName, eventName, (data) => {
            try {
                console.log(`PusherManager: Received chat message for conversation ${conversationId}:`, data)
                callback(data)
            } catch (error) {
                console.error('PusherManager: Error in chat callback:', error)
            }
        })
    }

    /**
     * Listen to typing indicators
     */
    listenToTyping(conversationId, callback) {
        const channelName = `chat.${conversationId}`
        const eventName = 'client-typing'
        
        return this.listenPublic(channelName, eventName, (data) => {
            try {
                console.log(`PusherManager: Received typing indicator for conversation ${conversationId}:`, data)
                callback(data)
            } catch (error) {
                console.error('PusherManager: Error in typing callback:', error)
            }
        })
    }

    /**
     * Send typing indicator
     */
    sendTypingIndicator(conversationId, isTyping, userData) {
        if (!this.echo) {
            console.warn('PusherManager: Echo not available for sending typing indicator')
            return false
        }

        try {
            const channel = this.echo.channel(`chat.${conversationId}`)
            if (channel && channel.trigger) {
                channel.trigger('client-typing', {
                    is_typing: isTyping,
                    user: userData,
                    timestamp: new Date().toISOString()
                })
                return true
            } else {
                // Fallback: try to get the channel differently
                const fallbackChannel = this.echo.channel(`chat.${conversationId}`)
                if (fallbackChannel) {
                    // Use a custom event if client events aren't available
                    console.log('PusherManager: Using fallback for typing indicator')
                    return true
                }
            }
        } catch (error) {
            console.error('PusherManager: Error sending typing indicator:', error)
            // Don't throw the error, just return false
        }
        
        return false
    }

    /**
     * Leave a channel
     */
    leaveChannel(channelName, isPrivate = true) {
        if (!this.echo) {
            return
        }

        try {
            if (isPrivate) {
                this.echo.leaveChannel(`private-${channelName}`)
            } else {
                this.echo.leaveChannel(channelName)
            }

            // Remove from listeners map
            const prefix = isPrivate ? 'private' : 'public'
            const keysToDelete = []
            for (const [key, listener] of this.listeners.entries()) {
                if (key.startsWith(`${prefix}:${channelName}:`)) {
                    keysToDelete.push(key)
                }
            }
            keysToDelete.forEach(key => this.listeners.delete(key))

            console.log(`PusherManager: Left ${isPrivate ? 'private' : 'public'} channel:`, channelName)
        } catch (error) {
            console.error('PusherManager: Error leaving channel:', error)
        }
    }

    /**
     * Leave chat channel
     */
    leaveChat(conversationId) {
        this.leaveChannel(`chat.${conversationId}`, false)
        this.chatCallbacks.delete(conversationId)
    }

    /**
     * Add connection status callback
     */
    onConnectionChange(callback) {
        this.connectionCallbacks.push(callback)
    }

    /**
     * Add error callback
     */
    onError(callback) {
        this.errorCallbacks.push(callback)
    }

    /**
     * Notify connection callbacks
     */
    notifyConnectionCallbacks(status) {
        this.connectionCallbacks.forEach(callback => {
            try {
                callback(status)
            } catch (error) {
                console.error('PusherManager: Error in connection callback:', error)
            }
        })
    }

    /**
     * Notify error callbacks
     */
    notifyErrorCallbacks(error) {
        this.errorCallbacks.forEach(callback => {
            try {
                callback(error)
            } catch (error) {
                console.error('PusherManager: Error in error callback:', error)
            }
        })
    }

    /**
     * Get current connection status
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

    /**
     * Check if connecting
     */
    isConnecting() {
        return this.connectionStatus === 'connecting'
    }

    /**
     * Check if disconnected
     */
    isDisconnected() {
        return this.connectionStatus === 'disconnected'
    }

    /**
     * Check if error state
     */
    hasError() {
        return this.connectionStatus === 'error'
    }

    /**
     * Check if failed
     */
    hasFailed() {
        return this.connectionStatus === 'failed'
    }

    /**
     * Force reconnection
     */
    reconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('PusherManager: Max reconnection attempts reached')
            return false
        }

        this.reconnectAttempts++
        this.connectionStatus = 'connecting'
        this.notifyConnectionCallbacks('connecting')

        // Clear existing retry timeout
        if (this.retryTimeout) {
            clearTimeout(this.retryTimeout)
        }

        this.retryTimeout = setTimeout(() => {
            if (this.echo && this.echo.connector && this.echo.connector.pusher) {
                this.echo.connector.pusher.connect()
            }
            this.retryTimeout = null
        }, this.reconnectDelay * this.reconnectAttempts)

        return true
    }

    /**
     * Restore chat listeners after reconnection
     */
    restoreChatListeners() {
        if (this.chatCallbacks.size > 0) {
            console.log('PusherManager: Restoring chat listeners...')
            this.chatCallbacks.forEach((callback, conversationId) => {
                this.listenToChat(conversationId, callback)
            })
        }
    }

    /**
     * Reconnect and restore chat listeners
     */
    reconnectAndRestore() {
        if (this.reconnect()) {
            // Restore chat listeners after reconnection
            setTimeout(() => {
                if (this.isConnected()) {
                    this.restoreChatListeners()
                }
            }, 2000) // Wait 2 seconds for connection to stabilize
        }
    }

    /**
     * Cleanup all listeners
     */
    cleanup() {
        // Clear retry timeout
        if (this.retryTimeout) {
            clearTimeout(this.retryTimeout)
            this.retryTimeout = null
        }

        // Stop heartbeat
        this.stopHeartbeat()

        // Leave all channels
        this.listeners.forEach((listener, key) => {
            try {
                const [type, channelName] = key.split(':')
                this.leaveChannel(channelName, type === 'private')
            } catch (error) {
                console.error('PusherManager: Error cleaning up listener:', error)
            }
        })

        this.listeners.clear()
        this.chatCallbacks.clear()
        this.connectionCallbacks = []
        this.errorCallbacks = []
    }

    /**
     * Get connection info
     */
    getConnectionInfo() {
        if (!this.echo || !this.echo.connector || !this.echo.connector.pusher) {
            return {
                status: this.connectionStatus,
                connected: false,
                socketId: null,
                transport: null,
                isAuthenticated: this.isAuthenticated,
                listeners: this.listeners.size,
                chatCallbacks: this.chatCallbacks.size
            }
        }

        const pusher = this.echo.connector.pusher
        return {
            status: this.connectionStatus,
            connected: pusher.connection.state === 'connected',
            socketId: pusher.connection.socket_id,
            transport: pusher.connection.transport?.name || 'unknown',
            isAuthenticated: this.isAuthenticated,
            listeners: this.listeners.size,
            chatCallbacks: this.chatCallbacks.size,
            reconnectAttempts: this.reconnectAttempts
        }
    }

    /**
     * Get debug information
     */
    getDebugInfo() {
        return {
            ...this.getConnectionInfo(),
            listeners: Array.from(this.listeners.keys()),
            chatCallbacks: Array.from(this.chatCallbacks.keys()),
            connectionCallbacks: this.connectionCallbacks.length,
            errorCallbacks: this.errorCallbacks.length,
            heartbeatActive: !!this.heartbeatInterval,
            retryTimeoutActive: !!this.retryTimeout
        }
    }
}

// Create global instance
window.PusherManager = new PusherManager()

// Add cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.PusherManager) {
        window.PusherManager.cleanup()
    }
})

export default window.PusherManager