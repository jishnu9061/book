<template>
    <Layout>
        <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
            <Head :title="$t(title)" />

            <div class="flex max-h-[calc(100vh-215px)]">
                <!-- Sidebar -->
                <div class="w-1/3 bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border-r border-slate-200/50 dark:border-slate-700/50 flex flex-col shadow-lg">
                    <!-- User Profile with Enhanced Design -->
                    <div class="p-4 border-b border-slate-200/50 dark:border-slate-700/50 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 dark:from-slate-700/50 dark:to-slate-600/50">
                        <div class="flex items-center gap-3">
                            <div class="relative group">
                                <img v-if="user?.photo" :src="user.photo" alt="" class="w-12 h-12 rounded-full object-cover ring-2 ring-white dark:ring-slate-700 shadow-lg">
                                <div v-else class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-700 shadow-lg">
                                    <User class="w-6 h-6 text-white" />
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full shadow-sm">
                                    <div class="w-full h-full bg-green-400 rounded-full animate-pulse"></div>
                                </div>
                                <div class="absolute inset-0 rounded-full bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                    <Camera class="w-4 h-4 text-white" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-900 dark:text-white truncate">{{ user?.first_name }} {{ user?.last_name }}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ $t(user?.role?.name || 'User') }}
                                </div>
                            </div>
                            <button
                                @click="toggleUserMenu"
                                class="p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200"
                            >
                                <MoreVertical class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Enhanced Search with Filters -->
                    <div class="p-4 border-b border-slate-200/50 dark:border-slate-700/50">
                        <div class="space-y-3">
                            <!-- Search Input -->
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                                <input
                                    v-model="searchForm.search"
                                    type="text"
                                    :placeholder="$t('Search conversations...')"
                                    class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                                    @input="handleSearch"
                                />
                                <button
                                    v-if="searchForm.search"
                                    @click="clearSearch"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                >
                                    <X class="w-4 h-4" />
                                </button>
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex gap-2">
                                <button
                                    v-for="filterOption in filterOptions"
                                    :key="filterOption.value"
                                    @click="setFilter(filterOption.value)"
                                    :class="[
                                        'px-3 py-1 text-xs rounded-full transition-all duration-200',
                                        currentFilter === filterOption.value
                                            ? filterOption.activeClass
                                            : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600'
                                    ]"
                                >
                                    {{ $t(filterOption.label) }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Conversations List -->
                    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent">
                        <div v-if="conversations.data.length === 0" class="p-8 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <MessageCircle class="w-8 h-8 text-slate-400 dark:text-slate-500" />
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">{{ $t('No conversations yet') }}</p>
                            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">{{ $t('Start a new conversation') }}</p>
                        </div>
                        <div v-else class="space-y-1 p-2">
                            <div
                                v-for="conversation in conversations.data"
                                :key="conversation.id"
                                @click="navigateTo(conversation.id)"
                                :class="[
                                    'group flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all duration-300 transform hover:scale-[1.02]',
                                    currentChat && currentChat.id === conversation.id
                                        ? 'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 shadow-md'
                                        : 'hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-sm'
                                ]"
                            >
                                <div class="relative">
                                    <div class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center shadow-lg">
                                        <MessageCircle class="w-6 h-6 text-white" />
                                    </div>
                                    <div v-if="conversation.total_entry > 0" class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold animate-pulse shadow-lg">
                                        {{ conversation.total_entry > 99 ? '99+' : conversation.total_entry }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="font-semibold text-slate-900 dark:text-white truncate">{{ conversation.title || 'New Conversation' }}</div>
                                        <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                            <Clock class="w-3 h-3" />
                                            {{ formatTime ? formatTime(conversation.updated_at) : conversation.updated_at }}
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-slate-500 dark:text-slate-400 truncate flex items-center gap-2">
                                            <User class="w-3 h-3" />
                                            {{ conversation.creator || 'Unknown' }}
                                        </div>
                                        <div v-if="conversation.is_archived" class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                                            <Archive class="w-3 h-3" />
                                            {{ $t('Archived') }}
                                        </div>
                                    </div>
                                    <div v-if="conversation.last_message" class="text-xs text-slate-400 dark:text-slate-500 truncate mt-1">
                                        {{ conversation.last_message }}
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <button
                                        @click.stop="toggleConversationMenu(conversation.id)"
                                        class="p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 opacity-0 group-hover:opacity-100 transition-all duration-200"
                                    >
                                        <MoreVertical class="w-4 h-4" />
                                    </button>
                                    <div v-if="conversation.is_pinned" class="text-yellow-500">
                                        <Pin class="w-3 h-3" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="conversations.links" class="p-4 border-t border-slate-200 dark:border-slate-700">
                        <pagination class="mt-4" :links="conversations.links" />
                    </div>
                </div>

                <!-- Enhanced Chat Area -->
                <div class="flex-1 flex flex-col bg-white/50 dark:bg-slate-800/50">
                    <!-- Loading state when chat ID is in URL but chat is not loaded yet -->
                    <div v-if="isLoadingChat" class="flex-1 flex items-center justify-center">
                        <div class="text-center max-w-md mx-auto px-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-700 dark:to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">{{ $t('Loading conversation...') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400">{{ $t('Please wait while we load the chat') }}</p>
                        </div>
                    </div>
                    <!-- No chat selected state -->
                    <div v-else-if="!currentChat" class="flex-1 flex items-center justify-center">
                        <div class="text-center max-w-md mx-auto px-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <MessageCircle class="w-10 h-10 text-slate-400 dark:text-slate-500" />
                            </div>
                            <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">{{ $t('Select a conversation') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $t('Choose a conversation from the sidebar to start chatting') }}</p>
                            <Link :href="route('chat.create')" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <Plus class="w-5 h-5" />
                                <span>{{ $t('Start New Chat') }}</span>
                            </Link>
                        </div>
                    </div>
                    <div v-else class="flex-1 flex flex-col">
                        <!-- Enhanced Chat Header -->
                        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center shadow-lg">
                                            <MessageCircle class="w-6 h-6 text-white" />
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full">
                                            <div class="w-full h-full bg-green-400 rounded-full animate-pulse"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-slate-900 dark:text-white">{{ (currentChat && currentChat.title) || 'New Conversation' }}</h3>
                                        <div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span>{{ $t('Active now') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">

                                    <!-- Chat Actions -->
                                    <div class="flex items-center gap-1">
                                        <button
                                            @click="currentChat && destroy(currentChat.id)"
                                            class="p-2 text-slate-400 hover:text-red-500 transition-colors duration-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                                            :title="$t('Delete conversation')"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Messages Area -->
                        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent messages-container relative" style="max-height: calc(100vh - 300px);">
                            <div v-if="currentChat && currentChat.messages && currentChat.messages.length === 0" class="text-center py-12">
                                <div class="w-16 h-16 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <MessageCircle class="w-8 h-8 text-slate-400 dark:text-slate-500" />
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 font-medium">{{ $t('No messages yet') }}</p>
                                <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">{{ $t('Start the conversation') }}</p>
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="(message, index) in (currentChat && currentChat.messages ? currentChat.messages : [])"
                                    :key="message.id"
                                    :class="[
                                        'flex gap-3 group message-item',
                                        message.user_id === user?.id ? 'justify-end' : 'justify-start',
                                        message.isDeleting ? 'deleting' : ''
                                    ]"
                                >
                                    <!-- Avatar for other users -->
                                    <div v-if="message.user_id !== user?.id" class="w-10 h-10 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <User class="w-5 h-5 text-white" />
                                    </div>

                                    <!-- Message Content -->
                                    <div class="flex flex-col max-w-xs lg:max-w-md">
                                        <!-- Message Bubble -->
                                        <div
                                            :class="[
                                                'px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 group-hover:shadow-md relative',
                                                message.user_id === user?.id
                                                    ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white'
                                                    : 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-600'
                                            ]"
                                        >
                                            <!-- Message Text with Rich Formatting -->
                                            <div class="text-sm leading-relaxed message-content" v-html="formatMessage ? formatMessage(message.message) : message.message"></div>

                                            <!-- Message Actions (on hover) -->
                                            <div class="absolute -right-2 -top-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <div class="flex gap-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-600 p-1">
                                                    <button
                                                        @click="replyToMessageEnhanced(message)"
                                                        class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                                                        :title="$t('Reply')"
                                                    >
                                                        <Reply class="w-3 h-3" />
                                                    </button>
                                                    <button
                                                        @click="copyMessageEnhanced(message)"
                                                        class="p-1.5 text-slate-400 hover:text-green-600 dark:hover:text-green-400 transition-colors duration-200"
                                                        :title="$t('Copy')"
                                                    >
                                                        <Copy class="w-3 h-3" />
                                                    </button>
                                                    <button
                                                        v-if="message.user_id === user?.id"
                                                        @click="editMessageEnhanced(message)"
                                                        class="p-1.5 text-slate-400 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors duration-200"
                                                        :title="$t('Edit')"
                                                    >
                                                        <Edit class="w-3 h-3" />
                                                    </button>
                                                    <button
                                                        v-if="message.user_id === user?.id"
                                                        @click="deleteMessageEnhanced(message)"
                                                        class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                                                        :title="$t('Delete')"
                                                    >
                                                        <Trash2 class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Message Footer -->
                                            <div class="flex items-center justify-between mt-2">
                                                <div :class="[
                                                    'text-xs flex items-center gap-1',
                                                    message.user_id === user?.id ? 'text-blue-100' : 'text-slate-400 dark:text-slate-500'
                                                ]">
                                                    <Clock class="w-3 h-3" />
                                                    {{ formatTime ? formatTime(message.created_at) : message.created_at }}
                                                    <span v-if="message.edited_at" class="text-xs opacity-75">({{ $t('edited') }})</span>
                                                </div>
                                                <div v-if="message.user_id === user?.id" class="flex items-center gap-1">
                                                    <div v-if="message.is_read" class="w-3 h-3 text-blue-200">
                                                        <CheckCheck class="w-3 h-3" />
                                                    </div>
                                                    <div v-else-if="message.is_delivered" class="w-3 h-3 text-blue-200">
                                                        <Check class="w-3 h-3" />
                                                    </div>
                                                    <div v-else class="w-3 h-3 text-blue-200">
                                                        <Clock class="w-3 h-3" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reply Context -->
                                        <div v-if="message.reply_to" class="mt-2 ml-4 p-2 bg-slate-50 dark:bg-slate-800 rounded-lg border-l-2 border-blue-500">
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $t('Replying to') }}</div>
                                            <div class="text-sm text-slate-700 dark:text-slate-300 truncate">{{ message.reply_to.message }}</div>
                                        </div>

                                        <!-- Message Reactions -->
                                        <div v-if="message.reactions && message.reactions.length > 0" class="mt-2 flex flex-wrap gap-1">
                                            <button
                                                v-for="reaction in message.reactions"
                                                :key="reaction.emoji"
                                                @click="toggleReaction(message, reaction.emoji)"
                                                class="px-2 py-1 text-xs bg-slate-100 dark:bg-slate-700 rounded-full hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors duration-200"
                                            >
                                                {{ reaction.emoji }} {{ reaction.count }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Avatar for current user -->
                                    <div v-if="message.user_id === user?.id" class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <User class="w-5 h-5 text-white" />
                                    </div>
                                </div>

                            </div>

                            <!-- Scroll to Bottom Button -->
                            <div v-if="showScrollToBottom" class="absolute bottom-4 right-4 z-10">
                                <button
                                    @click="scrollToBottomEnhanced"
                                    class="p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                                    :title="$t('Scroll to bottom')"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Enhanced Message Input -->
                        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border-t border-slate-200/50 dark:border-slate-700/50 p-4 shadow-lg">
                            <div class="space-y-3">
                                <!-- Reply Context -->
                                <div v-if="replyingTo" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg border-l-4 border-blue-500">
                                    <div class="flex-1">
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $t('Replying to') }}</div>
                                        <div class="text-sm text-slate-700 dark:text-slate-300 truncate">{{ replyingTo.message }}</div>
                                    </div>
                                    <button
                                        @click="cancelReplyEnhanced"
                                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200"
                                    >
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>


                                <!-- Input Area -->
                                <div class="flex gap-3">
                                    <!-- Attachment Button -->
                                    <button
                                        @click="toggleAttachmentMenu"
                                        class="p-3 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"
                                        :title="$t('Attach file')"
                                    >
                                        <Paperclip class="w-5 h-5" />
                                    </button>

                                    <!-- Message Input -->
                                    <div class="flex-1 relative">
                                        <textarea
                                            v-model="messageText"
                                            @keydown.enter.prevent="sendMessageEnhanced"
                                            @keydown.enter.shift.exact="messageText += '\n'"
                                            @focus="handleFocus"
                                            @blur="handleBlur"
                                            :placeholder="replyingTo ? $t('Type a reply...') : $t('Type a message...')"
                                            class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200 resize-none shadow-sm"
                                            rows="1"
                                            ref="messageInput"
                                        ></textarea>


                                        <!-- Emoji Button -->
                                        <button
                                            @click="toggleEmojiPicker"
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200"
                                            :title="$t('Add emoji')"
                                        >
                                            <Smile class="w-4 h-4" />
                                        </button>
                                    </div>

                                    <!-- Send Button -->
                                    <button
                                        @click="sendMessageEnhanced"
                                        :disabled="!messageText.trim() || isSending"
                                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none"
                                    >
                                        <div v-if="isSending" class="flex items-center gap-2">
                                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                            <span class="hidden sm:inline">{{ $t('Sending...') }}</span>
                                        </div>
                                        <div v-else class="flex items-center gap-2">
                                            <Send class="w-4 h-4" />
                                            <span class="hidden sm:inline">{{ $t('Send') }}</span>
                                        </div>
                                    </button>
                                </div>

                                <!-- Character Count -->
                                <div class="flex justify-between items-center text-xs text-slate-400 dark:text-slate-500 px-2">
                                    <div class="flex items-center gap-4">
                                        <span>{{ messageText.length }}/1000</span>
                                        <span v-if="messageText.length > 800" class="text-yellow-500">{{ $t('Message getting long') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs">Enter</kbd>
                                        <span>{{ $t('to send') }}</span>
                                        <kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs">Shift+Enter</kbd>
                                        <span>{{ $t('for new line') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachment Upload Component -->
                        <AttachmentUpload
                            v-if="currentChat"
                            :show-attachment-menu="showAttachmentMenu"
                            :conversation-id="currentChat && currentChat.id"
                            @close="showAttachmentMenu = false"
                            @files-uploaded="handleFilesUploaded"
                        />

                        <!-- Emoji Picker Component -->
                        <EmojiPicker
                            :show-emoji-picker="showEmojiPicker"
                            @close="showEmojiPicker = false"
                            @emoji-selected="insertEmoji"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { watch, nextTick, ref, onMounted, onUnmounted, computed } from 'vue'
import {
    MessageCircle,
    User,
    Plus,
    Send,
    Trash2,
    Search,
    Camera,
    MoreVertical,
    X,
    Clock,
    Archive,
    Pin,
    Paperclip,
    Smile,
    Check,
    CheckCheck,
    Reply,
    Copy,
    Edit
} from 'lucide-vue-next'

// Components
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import Pagination from '@/Shared/Pagination.vue'
import AttachmentUpload from '@/Components/Chat/AttachmentUpload.vue'
import EmojiPicker from '@/Components/Chat/EmojiPicker.vue'
import { useChat } from '@/composables/useChat'

// Props
const props = defineProps({
    filters: Object,
    conversations: Object,
    chat: Object,
    title: String
})

// Composables
const {
    // Reactive data
    messageText,
    isSending,
    currentFilter,
    showUserMenu,
    showAttachmentMenu,
    showEmojiPicker,
    showConversationMenu,
    searchForm,
    unreadCount,
    connectionStatus,
    replyingTo,

    // Refs
    messagesContainer,
    messageInput,

    // Computed
    user,
    userAccess,
    filterOptions,
    chat,

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
    toggleAttachmentMenu,
    toggleEmojiPicker,
    insertEmoji,
    handleFilesUploaded,
    toggleConversationMenu,
    setFilter,
    clearSearch,
    handleSearch,
    handleFocus,
    handleBlur,
    autoResizeTextarea,
    calculateUnreadCount,
    setupEcho,
    pushMessage,
    sanitizeMessage,
    markMessageAsRead,
    // New methods for enhanced features
    replyToMessage,
    cancelReply,
    copyMessage,
    editMessage,
    deleteMessage,
    toggleReaction
} = useChat(props)

// Template references are available from the composable

// Get current route and page data
const page = usePage()
const currentRoute = computed(() => page.url)

// Extract chat ID from URL if present
const chatIdFromUrl = computed(() => {
    const url = currentRoute.value
    const match = url.match(/\/chat\/(\d+)/)
    return match ? parseInt(match[1]) : null
})

// Better chat state management
const currentChat = computed(() => {
    // Try to get chat from composable first, then from props
    try {
        return (chat && chat.value) || props.chat
    } catch (error) {
        console.warn('Error accessing chat from composable:', error)
        return props.chat
    }
})

const isLoadingChat = computed(() => {
    try {
        return chatIdFromUrl.value && !currentChat.value
    } catch (error) {
        console.warn('Error in isLoadingChat:', error)
        return false
    }
})

// Auto-navigate to chat if ID is in URL but chat prop is not set
watch([chatIdFromUrl, () => props.chat], ([chatId, chat], [prevChatId, prevChat]) => {

    if (chatId && !chat && chatId !== prevChatId) {
        console.log('Auto-navigating to chat:', chatId)
        // Use the navigateTo function from the composable to load the chat
        if (navigateTo && typeof navigateTo === 'function') {
            navigateTo(chatId)
        } else {
            console.warn('navigateTo function not available, trying direct navigation')
            // Fallback: Direct Inertia navigation
            router.visit(`/dashboard/chat/${chatId}`, {
                method: 'get',
                preserveState: true,
                preserveScroll: true
            })
        }
    }
}, { immediate: true })

// Override the composable methods with enhanced functionality
const copyMessageEnhanced = (message) => {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(message.message).then(() => {
            // You could add a toast notification here
            console.log('Message copied to clipboard')
        }).catch(err => {
            console.error('Failed to copy message: ', err)
        })
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea')
        textArea.value = message.message
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        console.log('Message copied to clipboard (fallback)')
    }
}

const editMessageEnhanced = (message) => {
    // Set the message text to the input for editing
    messageText.value = message.message
    // Focus the input
    if (messageInput.value) {
        messageInput.value.focus()
    }
    // You could add a visual indicator that we're editing
    console.log('Editing message:', message.id)
}

const deleteMessageEnhanced = (message) => {
    if (confirm('Are you sure you want to delete this message?')) {
        try {
            // Add visual feedback - mark message as deleting
            message.isDeleting = true

            // Call the composable's delete method if available
            if (deleteMessage && typeof deleteMessage === 'function') {
                deleteMessage(message.id)
            } else {
                // Fallback: Remove message from local state
                if (chat.value && chat.value.messages) {
                    const messageIndex = chat.value.messages.findIndex(m => m.id === message.id)
                    if (messageIndex !== -1) {
                        // Add a small delay for visual feedback
                        setTimeout(() => {
                            chat.value.messages.splice(messageIndex, 1)
                            console.log('Message deleted from UI:', message.id)
                        }, 300)
                    }
                }
            }
        } catch (error) {
            console.error('Error deleting message:', error)
            // Remove deleting state
            message.isDeleting = false
            // Fallback: Remove from UI anyway
            if (chat.value && chat.value.messages) {
                const messageIndex = chat.value.messages.findIndex(m => m.id === message.id)
                if (messageIndex !== -1) {
                    chat.value.messages.splice(messageIndex, 1)
                }
            }
        }
    }
}

const replyToMessageEnhanced = (message) => {
    try {
        // Call the composable's reply method if available
        if (replyToMessage && typeof replyToMessage === 'function') {
            replyToMessage(message)
        } else {
            // Fallback: Set reply context manually
            replyingTo.value = {
                id: message.id,
                message: message.message,
                user: message.user || { name: 'Unknown User' },
                user_id: message.user_id
            }
        }

        // Focus the input after a short delay to ensure DOM is updated
        setTimeout(() => {
            if (messageInput.value) {
                messageInput.value.focus()
                // Scroll to input area
                messageInput.value.scrollIntoView({ behavior: 'smooth', block: 'center' })
            }
        }, 100)

        console.log('Replying to message:', message.id)
    } catch (error) {
        console.error('Error setting reply context:', error)
        // Fallback: Set basic reply context
        replyingTo.value = {
            id: message.id,
            message: message.message,
            user: { name: 'Unknown User' },
            user_id: message.user_id
        }
    }
}

const cancelReplyEnhanced = () => {
    try {
        // Call the composable's cancel reply method if available
        if (cancelReply && typeof cancelReply === 'function') {
            cancelReply()
        } else {
            // Fallback: Clear reply context manually
            replyingTo.value = null
        }
        console.log('Reply cancelled')
    } catch (error) {
        console.error('Error cancelling reply:', error)
        // Fallback: Clear reply context anyway
        replyingTo.value = null
    }
}

// Enhanced send message function that handles replies
const sendMessageEnhanced = () => {
    try {
        if (!messageText.value.trim()) {
            return
        }

        // Call the composable's send message method
        if (sendMessage && typeof sendMessage === 'function') {
            sendMessage()
        } else {
            // Fallback: Basic message sending
            console.log('Sending message:', messageText.value)
            if (replyingTo.value) {
                console.log('Replying to:', replyingTo.value.id)
            }
            // Clear the input
            messageText.value = ''
            // Clear reply context if it exists
            if (replyingTo.value) {
                replyingTo.value = null
            }
        }
    } catch (error) {
        console.error('Error sending message:', error)
    }
}

// Scroll to bottom functionality
const showScrollToBottom = ref(false)

const scrollToBottomEnhanced = () => {
    try {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
            showScrollToBottom.value = false
        }
    } catch (error) {
        console.warn('Error scrolling to bottom:', error)
    }
}

// Check if user is near bottom of scroll
const checkScrollPosition = () => {
    try {
        if (messagesContainer.value) {
            const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value
            const isNearBottom = scrollHeight - scrollTop - clientHeight < 100
            showScrollToBottom.value = !isNearBottom
        }
    } catch (error) {
        console.warn('Error checking scroll position:', error)
    }
}

// Watch for new messages and auto-scroll if user is at bottom
watch(() => currentChat?.messages?.length, () => {
    nextTick(() => {
        if (messagesContainer.value) {
            const { scrollTop, scrollHeight, clientHeight } = messagesContainer.value
            const isAtBottom = scrollHeight - scrollTop - clientHeight < 50
            if (isAtBottom) {
                scrollToBottomEnhanced()
            }
        }
    })
}, { deep: true })

// Add scroll event listener
onMounted(() => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.addEventListener('scroll', checkScrollPosition)
        }
    })
})

onUnmounted(() => {
    if (messagesContainer.value) {
        messagesContainer.value.removeEventListener('scroll', checkScrollPosition)
    }
})
</script>

<style scoped>
/* Custom scrollbar styles */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgb(203 213 225);
    border-radius: 3px;
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgb(71 85 105);
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background-color: rgb(148 163 184);
}

.dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background-color: rgb(100 116 139);
}

/* Custom animations */
@keyframes pulse-glow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

/* Message bubble animations */
.message-enter-active {
    transition: all 0.3s ease-out;
}

.message-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

.message-leave-active {
    transition: all 0.2s ease-in;
}

.message-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}


/* Glassmorphism effect */
.glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.dark .glass {
    background: rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Hover effects */
.hover-lift {
    transition: transform 0.2s ease-in-out;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

/* Focus styles */
.focus-ring:focus {
    outline: none;
    ring: 2px;
    ring-color: rgb(59 130 246);
    ring-offset: 2px;
}

/* Custom kbd styles */
kbd {
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
    font-size: 0.75rem;
    line-height: 1;
}

/* Message item animations */
.message-item {
    transition: all 0.3s ease-out;
}

.message-item:hover {
    transform: translateY(-1px);
}

/* Message content formatting */
.message-content {
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.message-content strong {
    font-weight: 600;
}

.message-content em {
    font-style: italic;
}

.message-content code {
    background-color: rgba(0, 0, 0, 0.1);
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
    font-size: 0.875em;
}

.dark .message-content code {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Message actions hover effect */
.message-actions {
    transition: all 0.2s ease-in-out;
}

/* Reaction buttons */
.reaction-button {
    transition: all 0.2s ease-in-out;
}

.reaction-button:hover {
    transform: scale(1.1);
}


/* Reply context styling */
.reply-context {
    border-left: 4px solid rgb(59 130 246);
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, transparent 100%);
}

/* Enhanced scrollbar for messages */
.messages-container::-webkit-scrollbar {
    width: 8px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(148, 163, 184, 0.3);
    border-radius: 4px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background-color: rgba(148, 163, 184, 0.5);
}


/* Message status indicators */
.message-status {
    transition: all 0.2s ease-in-out;
}

.message-status.sent {
    color: rgba(59, 130, 246, 0.7);
}

.message-status.delivered {
    color: rgba(34, 197, 94, 0.7);
}

.message-status.read {
    color: rgba(34, 197, 94, 1);
}

/* Message deleting state */
.message-item.deleting {
    opacity: 0.5;
    transform: scale(0.95);
    transition: all 0.3s ease-in-out;
}

.message-item.deleting .message-content {
    text-decoration: line-through;
    opacity: 0.7;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .message-actions {
        opacity: 1 !important;
    }
}
</style>
