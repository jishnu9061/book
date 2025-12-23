<template>
  <div class="notification-manager">
    <!-- Notification Container -->
    <div
      aria-live="assertive"
      class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50"
    >
      <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
        <TransitionGroup
          enter-active-class="transform ease-out duration-300 transition"
          enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
          enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
          leave-active-class="transition ease-in duration-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <NotificationToast
            v-for="notification in notifications"
            :key="notification.id"
            :notification="notification"
            :show="true"
            @close="removeNotification(notification.id)"
            @action="handleNotificationAction"
          />
        </TransitionGroup>
      </div>
    </div>
  </div>
</template>

<script>
import NotificationToast from './NotificationToast.vue'

export default {
  name: 'NotificationManager',
  components: {
    NotificationToast
  },
  data() {
    return {
      notifications: [],
      nextId: 1
    }
  },
  methods: {
    addNotification(notification) {
      const id = this.nextId++
      const newNotification = {
        id,
        type: 'info',
        title: 'Notification',
        message: '',
        duration: 5000,
        actions: [],
        ...notification
      }
      
      this.notifications.push(newNotification)
      
      // Auto-remove after duration
      if (newNotification.duration) {
        setTimeout(() => {
          this.removeNotification(id)
        }, newNotification.duration)
      }
      
      return id
    },
    removeNotification(id) {
      const index = this.notifications.findIndex(n => n.id === id)
      if (index > -1) {
        this.notifications.splice(index, 1)
      }
    },
    clearAll() {
      this.notifications = []
    },
    handleNotificationAction(action) {
      if (action.handler) {
        action.handler(action)
      }
    },
    // Convenience methods
    success(title, message, options = {}) {
      return this.addNotification({
        type: 'success',
        title,
        message,
        ...options
      })
    },
    error(title, message, options = {}) {
      return this.addNotification({
        type: 'error',
        title,
        message,
        duration: 0, // Don't auto-dismiss errors
        ...options
      })
    },
    warning(title, message, options = {}) {
      return this.addNotification({
        type: 'warning',
        title,
        message,
        ...options
      })
    },
    info(title, message, options = {}) {
      return this.addNotification({
        type: 'info',
        title,
        message,
        ...options
      })
    },
    conversation(title, message, options = {}) {
      return this.addNotification({
        type: 'conversation',
        title,
        message,
        ...options
      })
    }
  }
}
</script>

<style scoped>
.notification-manager {
  @apply relative;
}
</style>
