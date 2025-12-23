<template>
  <div class="ticket-timeline">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white flex items-center gap-2">
          <MessageCircle class="w-5 h-5" />
          {{ $t('Ticket Activity Log') }}
        </h2>
        <div class="flex items-center gap-2">
          <button @click="toggleTimelineView" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Toggle View')">
            <Eye v-if="timelineView === 'compact'" class="w-4 h-4" />
            <List v-else class="w-4 h-4" />
          </button>
          <button @click="refreshTimeline" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Refresh')">
            <RefreshCw class="w-4 h-4" />
          </button>
          <button @click="toggleFilter" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Filter')">
            <Filter class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Filter Panel -->
    <div v-if="showFilter" class="px-6 py-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
      <div class="flex flex-wrap gap-4">
        <div class="flex items-center gap-2">
          <input 
            v-model="filters.comments" 
            type="checkbox" 
            id="filter-comments" 
            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
          >
          <label for="filter-comments" class="text-sm text-slate-700 dark:text-slate-300">{{ $t('Comments') }}</label>
        </div>
        <div class="flex items-center gap-2">
          <input 
            v-model="filters.activities" 
            type="checkbox" 
            id="filter-activities" 
            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
          >
          <label for="filter-activities" class="text-sm text-slate-700 dark:text-slate-300">{{ $t('System Events') }}</label>
        </div>
        <div class="flex items-center gap-2">
          <input 
            v-model="filters.attachments" 
            type="checkbox" 
            id="filter-attachments" 
            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
          >
          <label for="filter-attachments" class="text-sm text-slate-700 dark:text-slate-300">{{ $t('Attachments') }}</label>
        </div>
      </div>
    </div>

    <!-- Timeline Content -->
    <div class="p-6">
      <!-- Timeline View -->
      <div v-if="timelineView === 'timeline'" class="relative">
        <!-- Timeline Line -->
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-slate-200 dark:bg-slate-700"></div>
        
        <!-- Timeline Items -->
        <div class="space-y-6">
          <div v-for="(item, index) in filteredTimelineItems" :key="`${item.type}-${item.id || index}`" class="relative">
            <!-- Timeline Dot -->
            <div class="absolute left-4 w-4 h-4 rounded-full border-2 border-white dark:border-slate-800 shadow-sm" :class="getTimelineDotClass(item.type)">
              <div class="w-full h-full rounded-full" :class="getTimelineDotInnerClass(item.type)"></div>
            </div>
            
            <!-- Timeline Content -->
            <div class="ml-12">
              <TimelineItem :item="item" :moment="moment" />
            </div>
          </div>
        </div>
      </div>
      
      <!-- Compact View -->
      <div v-else class="space-y-4">
        <div v-for="(item, index) in filteredTimelineItems" :key="`${item.type}-${item.id || index}`" class="flex gap-4">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" :class="getTimelineAvatarClass(item.type)">
              <component :is="getTimelineIcon(item.type)" class="w-5 h-5" />
            </div>
          </div>
          <div class="flex-1">
            <CompactTimelineItem :item="item" :moment="moment" />
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-if="filteredTimelineItems.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
        <MessageCircle class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <p>{{ $t('No activity yet') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import TimelineItem from './TimelineItem.vue'
import CompactTimelineItem from './CompactTimelineItem.vue'
import {
  MessageCircle,
  Eye,
  List,
  RefreshCw,
  Filter,
  User,
  Settings,
  Paperclip,
  CheckCircle,
  AlertTriangle,
  Clock,
  Edit,
  Trash2,
  Tag,
  Star
} from 'lucide-vue-next'

export default {
  name: 'TicketTimeline',
  components: {
    TimelineItem,
    CompactTimelineItem,
    MessageCircle,
    Eye,
    List,
    RefreshCw,
    Filter,
    User,
    Settings,
    Paperclip,
    CheckCircle,
    AlertTriangle,
    Clock,
    Edit,
    Trash2,
    Tag,
    Star
  },
  props: {
    comments: {
      type: Array,
      default: () => []
    },
    activities: {
      type: Array,
      default: () => []
    },
    attachments: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      moment: moment,
      timelineView: 'timeline', // 'timeline' or 'compact'
      showFilter: false,
      filters: {
        comments: true,
        activities: true,
        attachments: true
      }
    }
  },
  computed: {
    timelineItems() {
      const items = []
      
      // Add comments
      this.comments.forEach(comment => {
        items.push({
          type: 'comment',
          id: comment.id,
          timestamp: comment.created_at,
          user: comment.user,
          content: comment.details,
          metadata: {
            isInternal: comment.is_internal || false
          }
        })
      })
      
      // Add activities
      this.activities.forEach(activity => {
        items.push({
          type: 'activity',
          id: activity.id,
          timestamp: activity.created_at,
          user: activity.user,
          content: activity.description,
          metadata: {
            activityType: activity.activity_type,
            fieldName: activity.field_name,
            oldValue: activity.old_value,
            newValue: activity.new_value,
            metadata: activity.metadata
          }
        })
      })
      
      // Add attachments
      this.attachments.forEach(attachment => {
        items.push({
          type: 'attachment',
          id: attachment.id,
          timestamp: attachment.created_at,
          user: attachment.user,
          content: attachment.name,
          metadata: {
            fileName: attachment.name,
            fileSize: attachment.size,
            filePath: attachment.path
          }
        })
      })
      
      // Sort by timestamp
      return items.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp))
    },
    filteredTimelineItems() {
      return this.timelineItems.filter(item => {
        switch (item.type) {
          case 'comment':
            return this.filters.comments
          case 'activity':
            return this.filters.activities
          case 'attachment':
            return this.filters.attachments
          default:
            return true
        }
      })
    }
  },
  methods: {
    toggleTimelineView() {
      this.timelineView = this.timelineView === 'timeline' ? 'compact' : 'timeline'
    },
    refreshTimeline() {
      // Emit event to parent to refresh data
      this.$emit('refresh')
    },
    toggleFilter() {
      this.showFilter = !this.showFilter
    },
    getTimelineDotClass(type) {
      const baseClass = 'bg-white dark:bg-slate-800'
      switch (type) {
        case 'comment':
          return `${baseClass} border-blue-500`
        case 'activity':
          return `${baseClass} border-slate-500`
        case 'attachment':
          return `${baseClass} border-green-500`
        default:
          return `${baseClass} border-slate-400`
      }
    },
    getTimelineDotInnerClass(type) {
      switch (type) {
        case 'comment':
          return 'bg-blue-500'
        case 'activity':
          return 'bg-slate-500'
        case 'attachment':
          return 'bg-green-500'
        default:
          return 'bg-slate-400'
      }
    },
    getTimelineAvatarClass(type) {
      switch (type) {
        case 'comment':
          return 'bg-gradient-to-br from-blue-500 to-blue-600'
        case 'activity':
          return 'bg-gradient-to-br from-slate-500 to-slate-600'
        case 'attachment':
          return 'bg-gradient-to-br from-green-500 to-green-600'
        default:
          return 'bg-gradient-to-br from-slate-400 to-slate-500'
      }
    },
    getTimelineIcon(type) {
      switch (type) {
        case 'comment':
          return MessageCircle
        case 'activity':
          return Settings
        case 'attachment':
          return Paperclip
        default:
          return User
      }
    }
  }
}
</script>

<style scoped>
.ticket-timeline {
  @apply relative;
}
</style>