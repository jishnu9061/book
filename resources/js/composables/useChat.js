import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import moment from 'moment'
import axios from 'axios'

export function useChat(props) {
    const page = usePage()

    // Reactive data
    const messageText = ref('')
    const isSending = ref(false)
    const isTyping = ref(false)
    const currentFilter = ref('all')
    const showUserMenu = ref(false)
    const showChatInfo = ref(false)
    const showChatSettings = ref(false)
    const showAttachmentMenu = ref(false)
    const showEmojiPicker = ref(false)
    const showConversationMenu = ref(null)
    const typingTimeout = ref(null)
    const lastTypingTime = ref(null)
    const searchTimeout = ref(null)
    const connectionStatus = ref('connected')
    const unreadCount = ref(0)

    // Form data
    const searchForm = ref({
        search: props.filters?.search || ''
    })

    // Refs
    const messagesContainer = ref(null)
    const messageInput = ref(null)

    // Computed properties
    const user = computed(() => page.props.auth?.user || null)
    const userAccess = computed(() => page.props.auth?.user?.access || {})

    const filterOptions = computed(() => [
        {
            value: 'all',
            label: 'All',
            activeClass: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
        },
        {
            value: 'unread',
            label: 'Unread',
            activeClass: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
        },
        {
            value: 'archived',
            label: 'Archived',
            activeClass: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
        }
    ])

    // Methods
    const textOnly = (txt) => {
        return txt.replace(/(<([^>]+)>)/ig, '')
    }

    const reset = () => {
        searchForm.value = { search: '' }
    }

    const sendMessage = async () => {
        if (!messageText.value.trim() || isSending.value || !props.chat) return

        isSending.value = true
        const messageData = {
            message: messageText.value,
            user_id: user.value?.id,
            _token: page.props.csrf_token,
            conversation_id: props.chat.id
        }

        const originalMessage = messageText.value
        messageText.value = ''

        // Stop typing indicator
        isTyping.value = false
        broadcastTypingIndicator(false)

        try {
            const response = await axios.post(route('chat.message'), messageData)

            if (response.data && response.data.success) {
                scrollToBottom()
            } else {
                throw new Error(response.data?.message || 'Failed to send message')
            }
        } catch (error) {
            console.error('Admin Chat: Error sending message:', error)
            messageText.value = originalMessage // Restore message on error

            // Show user-friendly error message
            if (error.response?.status === 422) {
                const errors = error.response.data.errors
                const firstError = Object.values(errors)[0]
                alert(firstError ? firstError[0] : 'Please check your message and try again.')
            } else if (error.response?.status === 500) {
                alert('Server error. Please try again later.')
            } else if (error.code === 'NETWORK_ERROR') {
                alert('Network error. Please check your connection.')
            } else {
                alert('Failed to send message. Please try again.')
            }
        } finally {
            isSending.value = false
        }
    }

    const navigateTo = (id) => {
        window.location.href = route('chat.current', id)
    }

    const destroy = (id, onConfirm) => {
        if (onConfirm && typeof onConfirm === 'function') {
            // Use callback pattern for custom confirmation
            onConfirm(() => {
                router.delete(route('chat.destroy', id), {
                    onSuccess: () => {
                        setTimeout(() => { reset() }, 4000)
                    }
                })
            });
        } else {
            // Fallback to browser confirm for backward compatibility
            if (confirm('Are you sure you want to delete this conversation?')) {
                router.delete(route('chat.destroy', id), {
                    onSuccess: () => {
                        setTimeout(() => { reset() }, 4000)
                    }
                })
            }
        }
    }

    const restore = (id, onConfirm) => {
        if (onConfirm && typeof onConfirm === 'function') {
            // Use callback pattern for custom confirmation
            onConfirm(() => {
                router.put(route('chat.restore', id))
            });
        } else {
            // Fallback to browser confirm for backward compatibility
            if (confirm('Are you sure you want to restore this conversation?')) {
                router.put(route('chat.restore', id))
            }
        }
    }

    const scrollToBottom = () => {
        nextTick(() => {
            if (messagesContainer.value) {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
            }
        })
    }

    const formatTime = (date) => {
        if (!date) return ''
        return moment(date).fromNow()
    }

    const formatMessage = (message) => {
        if (!message) return ''

        // If message is an object, try to extract text content
        if (typeof message === 'object') {
            if (message.text) return message.text
            if (message.content) return message.content
            if (message.message) return message.message
            return JSON.stringify(message)
        }

        // Ensure it's a string
        const messageStr = String(message || '')

        // Basic HTML escaping for security
        return messageStr
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;')
            .replace(/\//g, '&#x2F;')
    }

    // UI Methods
    const toggleUserMenu = () => {
        showUserMenu.value = !showUserMenu.value
    }

    const toggleChatInfo = () => {
        showChatInfo.value = !showChatInfo.value
    }

    const toggleChatSettings = () => {
        showChatSettings.value = !showChatSettings.value
    }

    const toggleAttachmentMenu = () => {
        showAttachmentMenu.value = !showAttachmentMenu.value
        // Close emoji picker when opening attachment menu
        if (showAttachmentMenu.value) {
            showEmojiPicker.value = false
        }
    }

    const toggleEmojiPicker = () => {
        showEmojiPicker.value = !showEmojiPicker.value
        // Close attachment menu when opening emoji picker
        if (showEmojiPicker.value) {
            showAttachmentMenu.value = false
        }
    }

    const insertEmoji = (emoji) => {
        if (messageInput.value) {
            const start = messageInput.value.selectionStart
            const end = messageInput.value.selectionEnd
            const text = messageText.value
            messageText.value = text.substring(0, start) + emoji + text.substring(end)
            
            // Set cursor position after emoji
            nextTick(() => {
                messageInput.value.selectionStart = messageInput.value.selectionEnd = start + emoji.length
                messageInput.value.focus()
            })
        }
    }

    const handleFilesUploaded = (attachments) => {
        // Files are automatically added to the conversation via broadcasting
        // Just close the attachment menu
        showAttachmentMenu.value = false
        scrollToBottom()
    }

    const toggleConversationMenu = (conversationId) => {
        showConversationMenu.value = showConversationMenu.value === conversationId ? null : conversationId
    }

    // Filter Methods
    const setFilter = (filter) => {
        currentFilter.value = filter
        // Apply filter logic here
    }

    const clearSearch = () => {
        searchForm.value.search = ''
        reset()
    }

    const handleSearch = () => {
        clearTimeout(searchTimeout.value)
        searchTimeout.value = setTimeout(() => {
            router.get(route('chat.index'), pickBy(searchForm.value), { preserveState: true })
        }, 300)
    }

    // Typing Methods
    const handleTyping = () => {
        isTyping.value = true
        lastTypingTime.value = Date.now()

        // Clear existing timeout
        if (typingTimeout.value) {
            clearTimeout(typingTimeout.value)
        }

        // Set new timeout to stop typing indicator
        typingTimeout.value = setTimeout(() => {
            isTyping.value = false
        }, 1000)

        // Broadcast typing indicator
        broadcastTypingIndicator(true)
    }

    const handleFocus = () => {
        // Handle input focus
    }

    const handleBlur = () => {
        // Stop typing indicator when input loses focus
        isTyping.value = false
        broadcastTypingIndicator(false)
    }

    const broadcastTypingIndicator = (isTyping) => {
        // Typing indicators disabled for now - can be implemented later with direct Echo
    }

    // Auto-resize textarea
    const autoResizeTextarea = () => {
        if (messageInput.value) {
            messageInput.value.style.height = 'auto'
            messageInput.value.style.height = Math.min(messageInput.value.scrollHeight, 120) + 'px'
        }
    }

    // Calculate unread count
    const calculateUnreadCount = () => {
        if (props.conversations && props.conversations.data) {
            unreadCount.value = props.conversations.data.reduce((total, conversation) => {
                return total + (conversation.total_entry || 0)
            }, 0)
        }
    }

    // Pusher/Echo setup
    const setupEcho = () => {

        // Wait for Echo to be available
        const checkEcho = () => {

            if (window.Echo) {
                connectionStatus.value = 'connected'

                // Listen for chat messages if chat is available
                if (props.chat && props.chat.id) {

                    // Use Echo.channel() for public chat (no authentication required)
                    // Force public channel by ensuring no auth configuration
                    const channelName = `chat.${props.chat.id}`

                    // Create channel with explicit public configuration
                    const channel = window.Echo.channel(channelName)

                    // Listen for new chat messages

                    // Add debugging for channel subscription events
                    channel.subscription.bind('pusher:subscription_succeeded', () => {

                    })

                    channel.subscription.bind('pusher:subscription_error', (error) => {
                        console.log('❌ Admin Chat: Channel subscription error:', error)
                    })

                    // Note: channel.subscription.bind_all is not available

                    // Note: channel.listen is not working, using global listener instead

                    // Listen for typing indicators
                    channel.listen('TypingIndicator', (e) => {
                        handleTypingIndicator(e)
                    })

                    // Listen for channel subscription events
                    channel.subscription.bind('pusher:subscription_succeeded', () => {
                    })

                    channel.subscription.bind('pusher:subscription_error', (status) => {
                        console.log('❌ Admin Chat: Channel subscription error:', status)
                    })

                    // Debug: Add channel-specific event debugging
                    channel.subscription.bind('pusher:subscription_succeeded', () => {

                        // Add a test listener to see if ANY events are received
                        channel.subscription.bind('pusher:subscription_error', (error) => {
                            console.log('❌ Admin Chat: Channel subscription error:', error)
                        })

                        // Note: channel.subscription.bind_all is not available
                    })

                    // Use global listener as primary method since channel.listen is not working
                    window.Echo.connector.pusher.bind_global((eventName, data) => {
                        if (eventName === 'NewChatMessage') {

                            // Process the message using the same logic as channel.listen
                            if (data && data.chatMessage && typeof data.chatMessage === 'object') {
                                pushMessage(data.chatMessage)
                            } else {
                                console.warn('Admin Chat: Invalid message structure from global listener:', data)
                            }
                        }
                    })

                }
            } else {
                console.warn('Admin Chat: Echo not ready, retrying...')
                setTimeout(checkEcho, 500)
            }
        }

        checkEcho()
    }

    const pushMessage = (message) => {
        // Ensure message has proper structure and sanitize data
        const sanitizedMessage = sanitizeMessage(message)
        console.log(sanitizedMessage)
        const chat_id = parseInt(sanitizedMessage.conversation_id || 0);

        // Find the conversation in the list
        let chat = props.conversations.data.find(x => x.id === chat_id)

        if (typeof chat === 'object') {
            // Update conversation title with latest message
            chat.title = sanitizedMessage.message
            chat.total_entry = chat.total_entry + 1
            chat.updated_at = sanitizedMessage.created_at

            // If this is the current chat, add message to the chat
            if (!!props.chat && (props.chat.id === chat_id)) {
                // Check if message already exists to prevent duplicates
                const existingMessage = props.chat.messages.find(m => m.id === sanitizedMessage.id)
                if (!existingMessage) {
                    // Ensure messages array exists
                    if (!props.chat.messages) {
                        props.chat.messages = []
                    }

                    props.chat.messages.push(sanitizedMessage)
                    chat.total_entry = 0
                    scrollToBottom()

                    // Mark message as read if it's from another user
                    if (sanitizedMessage.user_id !== user.value?.id) {
                        markMessageAsRead(sanitizedMessage.id, chat_id)
                    }
                }
            }
        } else {
            // Create new conversation entry
            props.conversations.data.unshift({
                'id': chat_id,
                'total_entry': 1,
                'title': sanitizedMessage.message,
                'creator': sanitizedMessage.contact || sanitizedMessage.user,
                'updated_at': sanitizedMessage.created_at,
                'status': 'active'
            })
        }
    }

    const sanitizeMessage = (message) => {
        // Ensure message is an object
        if (!message || typeof message !== 'object') {
            return {
                id: Date.now(),
                message: 'Invalid message',
                conversation_id: 0,
                user_id: null,
                contact_id: null,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
                user: null,
                contact: null
            }
        }

        // Sanitize the message content
        let messageContent = message.message
        if (typeof messageContent === 'object') {
            // If message is an object, try to extract the text content
            if (messageContent.text) {
                messageContent = messageContent.text
            } else if (messageContent.content) {
                messageContent = messageContent.content
            } else if (messageContent.message) {
                messageContent = messageContent.message
            } else {
                // If we can't extract text, stringify the object
                messageContent = JSON.stringify(messageContent)
            }
        }

        // Ensure message content is a string
        if (typeof messageContent !== 'string') {
            messageContent = String(messageContent || '')
        }

        // Basic HTML sanitization
        messageContent = messageContent
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;')
            .replace(/\//g, '&#x2F;')

        return {
            id: message.id || Date.now(),
            message: messageContent,
            conversation_id: message.conversation_id || 0,
            user_id: message.user_id || null,
            contact_id: message.contact_id || null,
            created_at: message.created_at || new Date().toISOString(),
            updated_at: message.updated_at || new Date().toISOString(),
            user: message.user || null,
            contact: message.contact || null,
            is_read: message.is_read || false,
            message_type: message.message_type || 'text'
        }
    }

    const markMessageAsRead = async (messageId, chatId) => {
        const token = document.head.querySelector('meta[name="csrf-token"]')
        try {
            await axios.post(route('chat.mark-read'), {
                message_id: messageId,
                conversation_id: chatId,
                _token: token.content
            })
        } catch (error) {
            console.error('Admin Chat: Error marking message as read:', error)
        }
    }

    const handleTypingIndicator = (data) => {
        if (data && data.user_id && data.user_id !== user.value?.id) {
            isTyping.value = data.is_typing || false
            const typingUser = data.user || { id: data.user_id }

            if (isTyping.value) {
                // Clear existing timeout
                if (typingTimeout.value) {
                    clearTimeout(typingTimeout.value)
                }

                // Set timeout to hide typing indicator
                typingTimeout.value = setTimeout(() => {
                    isTyping.value = false
                }, 3000)
            } else {
                // Stop typing immediately
                isTyping.value = false
                if (typingTimeout.value) {
                    clearTimeout(typingTimeout.value)
                }
            }
        }
    }

    // Watchers
    watch(searchForm, throttle(() => {
        router.get(route('chat'), pickBy(searchForm.value), { preserveState: true })
    }, 150), { deep: true })

    // Lifecycle
    onMounted(() => {
        setupEcho()
        scrollToBottom()

        // Auto-resize textarea on input
        nextTick(() => {
            if (messageInput.value) {
                messageInput.value.addEventListener('input', autoResizeTextarea)
            }
        })

        // Calculate unread count
        calculateUnreadCount()
    })

    onUnmounted(() => {
        // Clean up timeouts
        if (typingTimeout.value) {
            clearTimeout(typingTimeout.value)
        }
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value)
        }

        // Clean up Echo.channel() subscriptions
        if (window.Echo && props.chat) {
            window.Echo.leaveChannel(`chat.${props.chat.id}`)
        }
    })

    return {
        // Reactive data
        messageText,
        isSending,
        isTyping,
        currentFilter,
        showUserMenu,
        showChatInfo,
        showChatSettings,
        showAttachmentMenu,
        showEmojiPicker,
        showConversationMenu,
        searchForm,
        unreadCount,
        connectionStatus,

        // Refs
        messagesContainer,
        messageInput,

        // Computed
        user,
        userAccess,
        filterOptions,

        // Methods
        textOnly,
        reset,
        sendMessage,
        navigateTo,
        destroy,
        restore,
        scrollToBottom,
        formatTime,
        formatMessage,
        toggleUserMenu,
        toggleChatInfo,
        toggleChatSettings,
        toggleAttachmentMenu,
        toggleEmojiPicker,
        insertEmoji,
        handleFilesUploaded,
        toggleConversationMenu,
        setFilter,
        clearSearch,
        handleSearch,
        handleTyping,
        handleFocus,
        handleBlur,
        broadcastTypingIndicator,
        autoResizeTextarea,
        calculateUnreadCount,
        setupEcho,
        pushMessage,
        sanitizeMessage,
        markMessageAsRead,
        handleTypingIndicator
    }
}
