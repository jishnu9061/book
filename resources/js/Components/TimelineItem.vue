<template>
  <div class="timeline-item">
    <!-- Comment Item -->
    <div v-if="item.type === 'comment'" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
            {{ getUserInitials(item.user) }}
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <h4 class="font-medium text-slate-900 dark:text-white text-sm">
                {{ getUserName(item.user) }}
              </h4>
              <span v-if="item.metadata.isInternal" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300">
                {{ $t('Internal') }}
              </span>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400">
              {{ moment(item.timestamp).format('MMM D, YYYY h:mm A') }}
            </span>
          </div>
          <div class="prose dark:prose-invert prose-sm max-w-none">
            <div v-html="item.content"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Item -->
    <div v-else-if="item.type === 'activity'" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-gradient-to-br from-slate-500 to-slate-600 rounded-full flex items-center justify-center text-white">
            <component :is="getActivityIcon(item.metadata.activityType)" class="w-4 h-4" />
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <h4 class="font-medium text-slate-900 dark:text-white text-sm">
                {{ item.content }}
              </h4>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200">
                {{ $t('System Event') }}
              </span>
            </div>
            <div class="flex items-center gap-2">
              <span v-if="item.user" class="text-xs text-slate-500 dark:text-slate-400">
                {{ $t('by') }} {{ getUserName(item.user) }}
              </span>
              <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ moment(item.timestamp).format('MMM D, YYYY h:mm A') }}
              </span>
            </div>
          </div>
          
          <!-- Field Change Details -->
          <div v-if="item.metadata.fieldName && item.metadata.oldValue !== item.metadata.newValue" class="mt-3 p-3 bg-white dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-600">
            <div class="text-xs text-slate-500 dark:text-slate-400 mb-2">{{ $t('Field Changed') }}: {{ formatFieldName(item.metadata.fieldName) }}</div>
            <div class="flex items-center gap-4">
              <div class="flex-1">
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $t('From') }}</div>
                <div class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded">
                  {{ item.metadata.oldValue || $t('Empty') }}
                </div>
              </div>
              <div class="flex-shrink-0 text-slate-400 dark:text-slate-500">
                <ArrowRight class="w-4 h-4" />
              </div>
              <div class="flex-1">
                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $t('To') }}</div>
                <div class="text-sm text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">
                  {{ item.metadata.newValue || $t('Empty') }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Attachment Item -->
    <div v-else-if="item.type === 'attachment'" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white">
            <Paperclip class="w-4 h-4" />
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <h4 class="font-medium text-slate-900 dark:text-white text-sm">
                {{ $t('Attachment Added') }}
              </h4>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ $t('File') }}
              </span>
            </div>
            <div class="flex items-center gap-2">
              <span v-if="item.user" class="text-xs text-slate-500 dark:text-slate-400">
                {{ $t('by') }} {{ getUserName(item.user) }}
              </span>
              <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ moment(item.timestamp).format('MMM D, YYYY h:mm A') }}
              </span>
            </div>
          </div>
          
          <!-- File Details -->
          <div class="mt-3 p-3 bg-white dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-600">
            <div class="flex items-center gap-3">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                  <File class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
                  {{ item.metadata.fileName }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400">
                  {{ formatFileSize(item.metadata.fileSize) }}
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button @click="previewFile(item.metadata)" class="p-1 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" :title="$t('Preview')">
                  <Eye class="w-4 h-4" />
                </button>
                <button @click="downloadFile(item.metadata)" class="p-1 text-slate-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" :title="$t('Download')">
                  <Download class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import {
  MessageCircle,
  Settings,
  Paperclip,
  File,
  Eye,
  Download,
  ArrowRight,
  User,
  Edit,
  Trash2,
  Tag,
  Star,
  CheckCircle,
  AlertTriangle,
  Clock
} from 'lucide-vue-next'

export default {
  name: 'TimelineItem',
  components: {
    MessageCircle,
    Settings,
    Paperclip,
    File,
    Eye,
    Download,
    ArrowRight,
    User,
    Edit,
    Trash2,
    Tag,
    Star,
    CheckCircle,
    AlertTriangle,
    Clock
  },
  props: {
    item: {
      type: Object,
      required: true
    },
    moment: {
      type: Function,
      required: true
    }
  },
  methods: {
    getUserInitials(user) {
      if (!user) return 'U'
      const firstName = user.first_name || ''
      const lastName = user.last_name || ''
      return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'U'
    },
    getUserName(user) {
      if (!user) return 'Unknown User'
      return `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown User'
    },
    getActivityIcon(activityType) {
      const iconMap = {
        'created': User,
        'updated': Edit,
        'assigned': User,
        'status_changed': Settings,
        'field_changed': Edit,
        'commented': MessageCircle,
        'attachment_added': Paperclip,
        'sla_breach': AlertTriangle,
        'sla_applied': CheckCircle,
        'deleted': Trash2,
        'tagged': Tag,
        'favorited': Star
      }
      return iconMap[activityType] || Settings
    },
    formatFieldName(fieldName) {
      const fieldMap = {
        'status_id': 'Status',
        'priority_id': 'Priority',
        'assigned_to': 'Assignment',
        'department_id': 'Department',
        'category_id': 'Category',
        'type_id': 'Type',
        'subject': 'Subject',
        'details': 'Description',
        'due_date': 'Due Date',
        'impact_level': 'Impact Level',
        'urgency_level': 'Urgency Level',
        'source': 'Source',
        'tags': 'Tags'
      }
      return fieldMap[fieldName] || fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
    },
    formatFileSize(size) {
      if (!size) return '0 B'
      const i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i]
    },
    previewFile(metadata) {
      // Implement file preview functionality
      console.log('Preview file:', metadata)
    },
    downloadFile(metadata) {
      // Implement file download functionality
      const link = document.createElement('a')
      link.href = window.location.origin + '/files/' + metadata.filePath
      link.download = metadata.fileName
      link.click()
    }
  }
}
</script>

<style scoped>
.timeline-item {
  @apply relative;
}
</style>
