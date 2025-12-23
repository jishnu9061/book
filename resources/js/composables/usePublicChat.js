import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

export function usePublicChat() {
    const page = usePage()

    // Reactive data
    const currentChat = ref(null)
    const openChat = ref(false)
    const unreadCount = ref(0)
    const chatId = ref(0)
    const contactId = ref(0)
    const message = ref(null)
    const isTyping = ref(false)
    const typingUser = ref(null)
    const typingTimeout = ref(null)
    const initUser = ref({
        first_name: '',
        last_name: '',
        email: '',
    })

    // Methods
    const toggleChat = () => {
        openChat.value = !openChat.value
    }

    const startChat = async () => {
        if (!initUser.value.email || !initUser.value.first_name || !initUser.value.last_name) {
            console.warn('Public Chat: Missing required user information')
            alert('Please fill in all required fields (First Name, Last Name, Email)')
            return
        }

        console.log('Public Chat: Starting chat initialization...')

        // Transform data to match backend expectations
        const chatData = {
            first_name: initUser.value.first_name,
            last_name: initUser.value.last_name,
            email: initUser.value.email,
            subject: initUser.value.subject || null,
            department: initUser.value.department || null,
            priority: initUser.value.priority || null,
            source: initUser.value.source || null
        }

        console.log('Public Chat: Sending data to backend:', chatData)

        try {
            const response = await axios.post(route('chat.init'), chatData)

            if (response.data && response.data.success && response.data.conversation) {
                currentChat.value = response.data.conversation
                chatId.value = currentChat.value.id
                contactId.value = currentChat.value.contact_id

                // Initialize messages array if it doesn't exist
                if (!currentChat.value.messages) {
                    currentChat.value.messages = []
                }

                // Store in localStorage for persistence
                localStorage.setItem('chat_id', chatId.value)
                localStorage.setItem('contact_id', contactId.value)

                console.log('Public Chat: Chat initialized successfully', currentChat.value)
                openChat.value = true

                // Start listening for messages after a short delay to ensure Echo is ready
                setTimeout(() => {
                    watchMessage()
                }, 1000)
            } else {
                throw new Error(response.data?.message || 'Failed to initialize chat')
            }
        } catch (error) {
            console.error('Public Chat: Failed to initialize chat:', error)

            // Show user-friendly error message
            if (error.response?.status === 422) {
                const errors = error.response.data.errors
                const firstError = Object.values(errors)[0]
                alert(firstError ? firstError[0] : 'Please check your information and try again.')
            } else if (error.response?.status === 500) {
                alert('Server error. Please try again later.')
            } else if (error.code === 'NETWORK_ERROR') {
                alert('Network error. Please check your connection.')
            } else {
                alert('Failed to start chat. Please try again.')
            }
        }
    }

    const handleStartChat = (userData) => {
        initUser.value = userData
        startChat()
    }

    const handleTyping = (typingData) => {
        // Handle typing events from the chat box
        if (typingData && typingData.isTyping) {
            broadcastTypingIndicator(typingData)
        }
    }

    const broadcastTypingIndicator = (typingData) => {
        // Typing indicators disabled for now - can be implemented later with direct Echo
        console.log('Public Chat: Typing indicator disabled (direct Echo implementation)')
    }

    const handleSendMessage = async (messageData) => {
        if (!messageData.message || !messageData.message.trim()) {
            console.warn('Public Chat: Empty message, not sending')
            return
        }

        const messagePayload = {
            message: messageData.message,
            contact_id: currentChat.value.contact_id,
            conversation_id: currentChat.value.id
        }

        try {
            const response = await axios.post(route('chat.send_message'), messagePayload)

            if (response.data && response.data.success && response.data.message) {
                // Message will be added via real-time event, no need to push manually
                console.log('Public Chat: Message sent successfully')

                // Auto-scroll to bottom
                nextTick(() => {
                    const chatContainer = document.querySelector('.chat-messages')
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight
                    }
                })
            } else {
                throw new Error(response.data?.message || 'Failed to send message')
            }
        } catch (error) {
            console.error('Public Chat: Failed to send message:', error)

            // Show user-friendly error message
            if (error.response?.status === 422) {
                const errors = error.response.data.errors
                const firstError = Object.values(errors)[0]
                alert(firstError ? firstError[0] : 'Please check your message and try again.')
            } else if (error.response?.status === 429) {
                alert('Please wait a moment before sending another message.')
            } else if (error.response?.status === 500) {
                alert('Server error. Please try again later.')
            } else if (error.code === 'NETWORK_ERROR') {
                alert('Network error. Please check your connection.')
            } else {
                alert('Failed to send message. Please try again.')
            }
        }
    }

    const sendMessage = async () => {
        const messageData = {
            message: message.value,
            contact_id: currentChat.value.contact_id,
            conversation_id: currentChat.value.id
        }
        message.value = ''

        try {
            const response = await axios.post(route('chat.send_message'), messageData)
            if (response.data && response.data.success && response.data.message) {
                currentChat.value.messages.push(response.data.message)
            }
        } catch (error) {
            console.error('Error sending message:', error)
        }
    }

    const watchMessage = () => {
        if (!chatId.value || !window.Echo) {
            console.warn('Public Chat: Cannot watch messages - missing chatId or Echo')
            return
        }

        try {
            const channel = window.Echo.channel(`chat.${chatId.value}`)
            
            // Listen for new chat messages
            channel.listen('NewChatMessage', (e) => {
                if (e && e.chatMessage) {
                    const messageObj = {
                        id: e.chatMessage.id,
                        message: e.chatMessage.message || '',
                        conversation_id: e.chatMessage.conversation_id,
                        user_id: e.chatMessage.user_id,
                        contact_id: e.chatMessage.contact_id,
                        created_at: e.chatMessage.created_at,
                        updated_at: e.chatMessage.updated_at,
                        user: e.chatMessage.user,
                        contact: e.chatMessage.contact
                    }

                    // Initialize messages array if it doesn't exist
                    if (!currentChat.value.messages) {
                        currentChat.value.messages = []
                    }

                    currentChat.value.messages.push(messageObj)

                    // Auto-scroll to bottom when new message arrives
                    nextTick(() => {
                        const chatContainer = document.querySelector('.chat-messages')
                        if (chatContainer) {
                            chatContainer.scrollTop = chatContainer.scrollHeight
                        }
                    })
                }
            })

            // Listen for typing indicators
            channel.listen('TypingIndicator', (e) => {
                if (e && e.user_id && e.user_id !== contactId.value) {
                    handleTypingIndicator(e)
                }
            })

        } catch (error) {
            console.error('Public Chat: Error setting up message watching:', error)
        }
    }

    const handleTypingIndicator = (data) => {
        if (data && data.user_id && data.user_id !== contactId.value) {
            isTyping.value = true
            typingUser.value = data.user

            // Clear existing timeout
            if (typingTimeout.value) {
                clearTimeout(typingTimeout.value)
            }

            // Set timeout to stop typing indicator
            typingTimeout.value = setTimeout(() => {
                isTyping.value = false
                typingUser.value = null
            }, 3000)
        }
    }

    const loadExistingChat = () => {
        const savedChatId = localStorage.getItem('chat_id')
        const savedContactId = localStorage.getItem('contact_id')

        if (savedChatId && savedContactId) {
            chatId.value = parseInt(savedChatId)
            contactId.value = parseInt(savedContactId)

            // Load conversation data
            axios.get(route('chat.conversation', [chatId.value, contactId.value]))
                .then(response => {
                    if (response.data && response.data.success) {
                        currentChat.value = response.data.conversation
                        openChat.value = true
                        watchMessage()
                    }
                })
                .catch(error => {
                    console.error('Failed to load existing chat:', error)
                    // Clear invalid data
                    localStorage.removeItem('chat_id')
                    localStorage.removeItem('contact_id')
                })
        }
    }

    const clearChat = () => {
        currentChat.value = null
        openChat.value = false
        chatId.value = 0
        contactId.value = 0
        message.value = null
        isTyping.value = false
        typingUser.value = null
        
        // Clear localStorage
        localStorage.removeItem('chat_id')
        localStorage.removeItem('contact_id')
    }

    // Lifecycle hooks
    onMounted(() => {
        loadExistingChat()
    })

    onUnmounted(() => {
        if (typingTimeout.value) {
            clearTimeout(typingTimeout.value)
        }
    })

    return {
        // State
        currentChat,
        openChat,
        unreadCount,
        chatId,
        contactId,
        message,
        isTyping,
        typingUser,
        initUser,

        // Methods
        toggleChat,
        startChat,
        handleStartChat,
        handleTyping,
        handleSendMessage,
        sendMessage,
        watchMessage,
        loadExistingChat,
        clearChat
    }
}