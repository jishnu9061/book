<template>
  <div class="compact-timeline-item">
    <!-- Comment Item -->
    <div v-if="item.type === 'comment'" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 border border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
          <h4 class="font-medium text-slate-900 dark:text-white text-sm">
            {{ getUserName(item.user) }}
          </h4>
          <span v-if="item.metadata.isInternal" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300">
            {{ $t('Internal') }}
          </span>
        </div>
        <span class="text-xs text-slate-500 dark:text-slate-400">
          {{ moment(item.timestamp).format('MMM D, h:mm A') }}
        </span>
      </div>
      <div class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2">
        {{ stripHtml(item.content) }}
      </div>
    </div>

    <!-- Activity Item -->
    <div v-else-if="item.type === 'activity'" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 border border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
          <h4 class="font-medium text-slate-900 dark:text-white text-sm">
            {{ item.content }}
          </h4>
          <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200">
            {{ $t('System') }}
          </span>
        </div>
        <div class="flex items-center gap-2">
          <span v-if="item.user" class="text-xs text-slate-500 dark:text-slate-400">
            {{ getUserName(item.user) }}
          </span>
          <span class="text-xs text-slate-500 dark:text-slate-400">
            {{ moment(item.timestamp).format('MMM D, h:mm A') }}
          </span>
        </div>
      </div>
      
      <!-- Field Change Summary -->
      <div v-if="item.metadata.fieldName && item.metadata.oldValue !== item.metadata.newValue" class="text-xs text-slate-600 dark:text-slate-400">
        {{ formatFieldName(item.metadata.fieldName) }}: 
        <span class="text-red-600 dark:text-red-400">{{ item.metadata.oldValue || $t('Empty') }}</span>
        <ArrowRight class="w-3 h-3 inline mx-1" />
        <span class="text-green-600 dark:text-green-400">{{ item.metadata.newValue || $t('Empty') }}</span>
      </div>
    </div>

    <!-- Attachment Item -->
    <div v-else-if="item.type === 'attachment'" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-3 border border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
          <h4 class="font-medium text-slate-900 dark:text-white text-sm">
            {{ $t('Attachment Added') }}
          </h4>
          <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ $t('File') }}
          </span>
        </div>
        <div class="flex items-center gap-2">
          <span v-if="item.user" class="text-xs text-slate-500 dark:text-slate-400">
            {{ getUserName(item.user) }}
          </span>
          <span class="text-xs text-slate-500 dark:text-slate-400">
            {{ moment(item.timestamp).format('MMM D, h:mm A') }}
          </span>
        </div>
      </div>
      
      <!-- File Summary -->
      <div class="flex items-center gap-2">
        <File class="w-4 h-4 text-slate-400" />
        <span class="text-sm text-slate-700 dark:text-slate-300 truncate">
          {{ item.metadata.fileName }}
        </span>
        <span class="text-xs text-slate-500 dark:text-slate-400">
          ({{ formatFileSize(item.metadata.fileSize) }})
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import {
  File,
  ArrowRight
} from 'lucide-vue-next'

export default {
  name: 'CompactTimelineItem',
  components: {
    File,
    ArrowRight
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
    getUserName(user) {
      if (!user) return 'Unknown User'
      return `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown User'
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
    stripHtml(html) {
      if (!html) return ''
      const temp = document.createElement('div')
      temp.innerHTML = html
      return temp.textContent || temp.innerText || ''
    }
  }
}
</script>

<style scoped>
.compact-timeline-item {
  @apply relative;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
