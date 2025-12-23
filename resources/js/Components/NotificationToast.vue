<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-100"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="max-w-sm w-full bg-white dark:bg-slate-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="getIconClass(notification.type)">
              <component :is="getIcon(notification.type)" class="w-5 h-5 text-white" />
            </div>
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="text-sm font-medium text-slate-900 dark:text-white">
              {{ notification.title }}
            </p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
              {{ notification.message }}
            </p>
            <div v-if="notification.actions?.length" class="mt-3 flex space-x-2">
              <button
                v-for="action in notification.actions"
                :key="action.label"
                @click="handleAction(action)"
                class="text-sm font-medium"
                :class="getActionClass(action.type)"
              >
                {{ action.label }}
              </button>
            </div>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button
              @click="close"
              class="bg-white dark:bg-slate-800 rounded-md inline-flex text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <span class="sr-only">{{ $t('Close') }}</span>
              <X class="h-5 w-5" />
            </button>
          </div>
        </div>
      </div>
      <!-- Progress Bar -->
      <div v-if="notification.duration" class="h-1 bg-slate-200 dark:bg-slate-700">
        <div 
          class="h-1 transition-all duration-100 ease-linear"
          :class="getProgressBarClass(notification.type)"
          :style="{ width: `${progress}%` }"
        ></div>
      </div>
    </div>
  </Transition>
</template>

<script>
import {
  CheckCircle,
  XCircle,
  AlertTriangle,
  Info,
  X,
  ExternalLink,
  MessageCircle
} from 'lucide-vue-next'

export default {
  name: 'NotificationToast',
  components: {
    CheckCircle,
    XCircle,
    AlertTriangle,
    Info,
    X,
    ExternalLink,
    MessageCircle
  },
  props: {
    notification: {
      type: Object,
      required: true
    },
    show: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      progress: 100,
      interval: null
    }
  },
  mounted() {
    if (this.notification.duration) {
      this.startProgress()
    }
  },
  beforeUnmount() {
    if (this.interval) {
      clearInterval(this.interval)
    }
  },
  methods: {
    startProgress() {
      const duration = this.notification.duration || 5000
      const interval = 50
      const decrement = (interval / duration) * 100
      
      this.interval = setInterval(() => {
        this.progress -= decrement
        if (this.progress <= 0) {
          this.close()
        }
      }, interval)
    },
    close() {
      this.$emit('close')
    },
    handleAction(action) {
      this.$emit('action', action)
      if (action.closeOnClick !== false) {
        this.close()
      }
    },
    getIcon(type) {
      const icons = {
        success: CheckCircle,
        error: XCircle,
        warning: AlertTriangle,
        info: Info,
        conversation: MessageCircle
      }
      return icons[type] || Info
    },
    getIconClass(type) {
      const classes = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
        conversation: 'bg-purple-500'
      }
      return classes[type] || 'bg-blue-500'
    },
    getActionClass(type) {
      const classes = {
        primary: 'text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300',
        secondary: 'text-slate-600 hover:text-slate-500 dark:text-slate-400 dark:hover:text-slate-300',
        danger: 'text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300'
      }
      return classes[type] || classes.secondary
    },
    getProgressBarClass(type) {
      const classes = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
        conversation: 'bg-purple-500'
      }
      return classes[type] || 'bg-blue-500'
    }
  }
}
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
