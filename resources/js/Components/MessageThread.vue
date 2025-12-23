<template>
  <div class="space-y-4">
    <!-- Thread Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
          <MessageCircle class="w-4 h-4 text-white" />
        </div>
        <div>
          <h4 class="font-medium text-slate-900 dark:text-white">{{ $t('Conversation Thread') }}</h4>
          <p class="text-sm text-slate-500 dark:text-slate-400">{{ threadItems.length }} {{ $t('messages') }}</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button @click="toggleThread" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
          <ChevronDown v-if="!isExpanded" class="w-4 h-4" />
          <ChevronUp v-else class="w-4 h-4" />
        </button>
        <button @click="replyToThread" class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/30 transition-colors">
          <Reply class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Thread Content -->
    <div v-if="isExpanded" class="space-y-3">
      <div v-for="(item, index) in threadItems" :key="`${item.type}-${item.id || index}`" class="relative">
        <!-- Message Item -->
        <div class="flex gap-3 group">
          <!-- Avatar -->
          <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium" :class="getAvatarClass(item)">
              <img v-if="item.user?.avatar" :src="item.user.avatar" :alt="item.user.first_name" class="w-8 h-8 rounded-full object-cover">
              <span v-else>{{ getInitials(item.user) }}</span>
            </div>
          </div>

          <!-- Message Content -->
          <div class="flex-1 min-w-0">
            <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4 group-hover:shadow-sm transition-shadow">
              <!-- Message Header -->
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="font-medium text-slate-900 dark:text-white text-sm">
                    {{ getUserName(item.user) }}
                  </span>
                  <span class="text-xs px-2 py-1 rounded-full" :class="getItemTypeBadgeClass(item)">
                    {{ getItemTypeLabel(item) }}
                  </span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-slate-500 dark:text-slate-400">
                    {{ moment(item.created_at).format('MMM D, h:mm A') }}
                  </span>
                  <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="copyMessage(item)" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                      <Copy class="w-3 h-3" />
                    </button>
                    <button v-if="canEdit(item)" @click="editMessage(item)" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                      <Edit class="w-3 h-3" />
                    </button>
                    <button v-if="canDelete(item)" @click="deleteMessage(item)" class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                      <Trash2 class="w-3 h-3" />
                    </button>
                  </div>
                </div>
              </div>

              <!-- Message Body -->
              <div class="space-y-3">
                <!-- Comment Content -->
                <div v-if="item.type === 'comment'" class="prose dark:prose-invert max-w-none text-sm" v-html="item.details"></div>
                
                <!-- Activity Content -->
                <div v-else-if="item.type === 'activity'" class="space-y-2">
                  <p class="text-slate-700 dark:text-slate-300 text-sm">{{ item.description }}</p>
                  
                  <!-- Field Changes -->
                  <div v-if="item.field_name" class="text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-medium">{{ $t('Field') }}:</span> {{ item.field_name }}
                  </div>
                  
                  <!-- Value Changes -->
                  <div v-if="item.old_value || item.new_value" class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    <div v-if="item.old_value" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                      <div class="text-xs font-medium text-red-700 dark:text-red-300 mb-1">{{ $t('Previous') }}</div>
                      <div class="text-sm text-red-900 dark:text-red-100">{{ item.old_value }}</div>
                    </div>
                    <div v-if="item.new_value" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                      <div class="text-xs font-medium text-green-700 dark:text-green-300 mb-1">{{ $t('New') }}</div>
                      <div class="text-sm text-green-900 dark:text-green-100">{{ item.new_value }}</div>
                    </div>
                  </div>
                </div>

                <!-- Attachment Content -->
                <div v-else-if="item.type === 'attachment'" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                  <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <File class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ item.name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ getFileSize(item.size) }}</p>
                  </div>
                  <button @click="downloadAttachment(item)" class="p-1 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <Download class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Message Actions -->
              <div v-if="item.type === 'comment'" class="flex items-center gap-4 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                <button @click="replyToMessage(item)" class="flex items-center gap-1 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                  <Reply class="w-3 h-3" />
                  {{ $t('Reply') }}
                </button>
                <button @click="likeMessage(item)" class="flex items-center gap-1 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                  <Heart :class="{ 'fill-current text-red-500': item.liked }" class="w-3 h-3" />
                  {{ item.likes_count || 0 }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Thread Connection Line -->
        <div v-if="index < threadItems.length - 1" class="absolute left-4 top-8 w-0.5 h-6 bg-slate-200 dark:bg-slate-700"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="threadItems.length === 0" class="text-center py-8">
      <MessageCircle class="w-12 h-12 text-slate-400 mx-auto mb-4" />
      <p class="text-slate-500 dark:text-slate-400">{{ $t('No messages in this thread') }}</p>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import {
  MessageCircle,
  ChevronDown,
  ChevronUp,
  Reply,
  Copy,
  Edit,
  Trash2,
  File,
  Download,
  Heart
} from 'lucide-vue-next'

export default {
  name: 'MessageThread',
  components: {
    MessageCircle,
    ChevronDown,
    ChevronUp,
    Reply,
    Copy,
    Edit,
    Trash2,
    File,
    Download,
    Heart
  },
  props: {
    threadItems: {
      type: Array,
      default: () => []
    },
    isExpanded: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      moment: moment
    }
  },
  methods: {
    toggleThread() {
      this.$emit('toggle-thread')
    },
    replyToThread() {
      this.$emit('reply-to-thread')
    },
    replyToMessage(message) {
      this.$emit('reply-to-message', message)
    },
    editMessage(message) {
      this.$emit('edit-message', message)
    },
    deleteMessage(message) {
      this.$emit('delete-message', message)
    },
    copyMessage(message) {
      let content = ''
      if (message.type === 'comment') {
        content = message.details
      } else if (message.type === 'activity') {
        content = message.description
      }
      
      navigator.clipboard.writeText(content)
      this.$emit('message-copied', message)
    },
    likeMessage(message) {
      this.$emit('like-message', message)
    },
    downloadAttachment(attachment) {
      const link = document.createElement("a")
      link.href = window.location.origin + '/files/' + attachment.path
      link.download = attachment.name
      link.click()
    },
    getFileSize(size) {
      const i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i]
    },
    getInitials(user) {
      if (!user) return 'U'
      return (user.first_name?.charAt(0) || '') + (user.last_name?.charAt(0) || '')
    },
    getUserName(user) {
      if (!user) return this.$t('System')
      return `${user.first_name || ''} ${user.last_name || ''}`.trim() || this.$t('Unknown User')
    },
    getAvatarClass(item) {
      if (item.user?.avatar) return ''
      
      const colors = [
        'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
        'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
        'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300',
        'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300',
        'bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-300'
      ]
      
      if (item.user?.id) {
        return colors[item.user.id % colors.length]
      }
      
      return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300'
    },
    getItemTypeLabel(item) {
      if (item.type === 'comment') return this.$t('Comment')
      if (item.type === 'attachment') return this.$t('Attachment')
      
      const activityLabels = {
        'created': this.$t('Created'),
        'updated': this.$t('Updated'),
        'assigned': this.$t('Assigned'),
        'status_changed': this.$t('Status'),
        'field_changed': this.$t('Field'),
        'sla_breach': this.$t('SLA'),
        'sla_applied': this.$t('SLA')
      }
      
      return activityLabels[item.activity_type] || this.$t('Event')
    },
    getItemTypeBadgeClass(item) {
      if (item.type === 'comment') return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
      if (item.type === 'attachment') return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
      
      const activityBadgeClasses = {
        'created': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
        'updated': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
        'assigned': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300',
        'status_changed': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300',
        'field_changed': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
        'sla_breach': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        'sla_applied': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
      }
      
      return activityBadgeClasses[item.activity_type] || 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300'
    },
    canEdit(item) {
      // Only allow editing comments by the current user
      return item.type === 'comment' && item.user && item.user.id === this.$page.props.auth.user.id
    },
    canDelete(item) {
      // Allow deleting comments by the current user or admins
      return item.type === 'comment' && (
        (item.user && item.user.id === this.$page.props.auth.user.id) ||
        this.$page.props.auth.user.role?.slug === 'admin'
      )
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar */
.thread-container::-webkit-scrollbar {
  width: 4px;
}

.thread-container::-webkit-scrollbar-track {
  background: transparent;
}

.thread-container::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.thread-container::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark .thread-container::-webkit-scrollbar-thumb {
  background: #475569;
}

.dark .thread-container::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}
</style>
