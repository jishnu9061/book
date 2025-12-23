<template>
    <div class="text-base text-black dark:text-white dark:bg-slate-900 layout_landing" :class="currentMode" :dir="$page.props.dir">
        <!-- Start Navbar -->
        <top-nav />
        <!-- End Navbar -->

        <!--success message -->
        <flash-messages />
        <!--success message -->

        <!--main content-->
        <slot />
        <!--main content-->
        <!-- footer -->
        <footer-section :footer="footer" />
        <!-- footer -->

        <!-- Back To Top Start -->
        <a id="back-to-top" @click="topFunction" href="javascript:void(0)" class="back-to-top flex fixed hidden bottom-5 right-5 left-auto z-[999] h-10 w-10 items-center justify-center rounded-md bg-primary text-white shadow-md transition duration-300 ease-in-out hover:bg-dark">
            <span class="mt-[6px] h-3 w-3 rotate-45 border-t border-l border-white"></span>
        </a>
        <!-- Back To Top End -->

        <!-- Enhanced Public Chat -->
        <div class="chat_public" v-if="!!enableOption && enableOption.chat">
            <!-- Enhanced Chat Box -->
            <div v-if="!!openChat && !!currentChat" class="chat__box">
                <div class="flex justify-center items-center">
                    <EnhancedChatBox
                        :messages="currentChat.messages || []"
                        :current-user-id="contactId"
                        @send-message="handleSendMessage"
                        @close="toggleChat"
                    />
                </div>
            </div>

            <!-- Enhanced Chat Form -->
            <div v-if="!!openChat && !currentChat" class="init_chat">
                <div class="flex justify-center items-center">
                    <ImprovedChatForm @submit="handleStartChat" />
                </div>
            </div>

            <!-- Enhanced Chat Button -->
            <button class="chat_bubble" @click="toggleChat">
                <span v-if="unreadCount" class="notification_badge">{{ unreadCount }}</span>
                <span class="chat__icn hover:scale-125 duration-300" v-if="!openChat">
                    <img src="/images/svg/chat-logo-v2.svg" alt="Live Chat" />
                </span>
                <span class="chat__close hover:scale-125 duration-300" v-if="!!openChat">
                    <img src="/images/svg/close.svg" alt="Close Chat" />
                </span>
                <span class="bottom_text">Let's talk</span>
            </button>
        </div>
        <!-- Enhanced Public Chat -->

        <span class="disabled_button"></span>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import moment from 'moment'

// Components
import Icon from '@/Shared/Icon.vue'
import Logo from '@/Shared/Logo.vue'
import TopNav from '@/Shared/Landing/TopNav.vue'
import FooterSection from '@/Shared/Landing/FooterSection.vue'
import Switcher from '@/Shared/Landing/Switcher.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import EnhancedChatBox from '@/Components/Chat/EnhancedChatBox.vue'
import ImprovedChatForm from '@/Components/Chat/ImprovedChatForm.vue'

// Composables
import { usePublicChat } from '@/composables/usePublicChat'
import { useTheme } from '@/composables/useTheme'
import { nextTick, onMounted, onUnmounted } from "vue";

// Props
const props = defineProps({
    title: String,
    footer: Object,
})

// Composables
const page = usePage()

// Chat functionality
const {
    currentChat,
    openChat,
    unreadCount,
    chatId,
    contactId,
    message,
    initUser,
    toggleChat,
    startChat,
    handleStartChat,
    handleSendMessage,
    sendMessage,
    watchMessage,
    initializeFromStorage
} = usePublicChat()

// Theme functionality
const {
    currentMode,
    currentDir,
    enableOption,
    scrollFunction,
    topFunction,
    switchMode,
    changeTheme,
    changeDir,
    actionColorScheme,
    setColorScheme,
    setColorSchemeFromStorage,
    initializeTheme,
    setupScrollListener
} = useTheme()

// Global moment for compatibility
window.moment = moment

// Setup Echo channel listener only if Echo is available and chatId exists
let channel = null

onMounted(() => {
    // Only setup Echo channel if Echo is available
    if (window.Echo && chatId.value) {
        try {
            channel = window.Echo.channel(`chat.${chatId.value}`)
            
            // Listen for new messages
            channel.listen('NewChatMessage', (e) => {
                console.log('Public Chat: Received message via Echo:', e)

                if (e && e.chatMessage && typeof e.chatMessage === 'object') {
                    console.log('Public Chat: Processing message:', e.chatMessage)

                    // Ensure the message has the required structure
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

                    console.log('Public Chat: Adding message to chat:', messageObj)

                    // Initialize messages array if it doesn't exist
                    if (currentChat.value && !currentChat.value.messages) {
                        currentChat.value.messages = []
                    }

                    if (currentChat.value) {
                        currentChat.value.messages.push(messageObj)
                        console.log('Public Chat: Total messages:', currentChat.value.messages.length)

                        // Auto-scroll to bottom when new message arrives
                        nextTick(() => {
                            const chatContainer = document.querySelector('.chat-messages')
                            if (chatContainer) {
                                chatContainer.scrollTop = chatContainer.scrollHeight
                            }
                        })
                    }
                } else {
                    console.warn('Public Chat: Invalid message structure received:', e)
                }
            })
        } catch (error) {
            console.error('Public Chat: Failed to setup Echo channel:', error)
        }
    } else {
        console.warn('Public Chat: Echo not available or chatId not set')
    }
})

onUnmounted(() => {
    // Clean up Echo channel when component is unmounted
    if (channel) {
        try {
            window.Echo.leave(`chat.${chatId.value}`)
            channel = null
        } catch (error) {
            console.error('Public Chat: Failed to leave Echo channel:', error)
        }
    }
})

</script>
