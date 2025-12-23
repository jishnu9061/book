<template>
  <div class="animate-fade-in-up">
    <div class="text-center mb-8">
      <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
        <CheckCircle class="w-12 h-12 text-white" />
      </div>
      <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">
        🎉 Installation Complete!
      </h2>
      <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        Congratulations! Your HelpDesk system has been successfully installed and is ready to use.
      </p>
    </div>

    <!-- Success Summary -->
    <div class="max-w-4xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Installation Summary -->
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
            <Clipboard class="w-5 h-5 mr-2" />
            Installation Summary
          </h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-slate-600 dark:text-slate-400">Application Name:</span>
              <span class="font-medium text-slate-900 dark:text-white">HelpDesk</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-600 dark:text-slate-400">Environment:</span>
              <span class="font-medium text-slate-900 dark:text-white">Production</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-600 dark:text-slate-400">Database:</span>
              <span class="font-medium text-slate-900 dark:text-white">Configured</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-600 dark:text-slate-400">Admin Account:</span>
              <span class="font-medium text-slate-900 dark:text-white">Created</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-600 dark:text-slate-400">Installation Time:</span>
              <span class="font-medium text-slate-900 dark:text-white">{{ installationTime }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
            <Zap class="w-5 h-5 mr-2" />
            Quick Actions
          </h3>
          <div class="space-y-3">
            <button 
              @click="goToLogin"
              class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group"
            >
              <div class="flex items-center">
                <LogIn class="w-4 h-4 mr-3 text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400" />
                <span class="text-slate-700 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">Access Admin Dashboard</span>
              </div>
              <ArrowRight class="w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400" />
            </button>

            <button 
              @click="viewDocumentation"
              class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-600 transition-colors group"
            >
              <div class="flex items-center">
                <BookOpen class="w-4 h-4 mr-3 text-slate-500 group-hover:text-green-600 dark:group-hover:text-green-400" />
                <span class="text-slate-700 dark:text-slate-300 group-hover:text-green-600 dark:group-hover:text-green-400">View Documentation</span>
              </div>
              <ArrowRight class="w-4 h-4 text-slate-400 group-hover:text-green-600 dark:group-hover:text-green-400" />
            </button>

            <button 
              @click="configureSettings"
              class="w-full flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 transition-colors group"
            >
              <div class="flex items-center">
                <Settings class="w-4 h-4 mr-3 text-slate-500 group-hover:text-purple-600 dark:group-hover:text-purple-400" />
                <span class="text-slate-700 dark:text-slate-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">Configure Settings</span>
              </div>
              <ArrowRight class="w-4 h-4 text-slate-400 group-hover:text-purple-600 dark:group-hover:text-purple-400" />
            </button>
          </div>
        </div>
      </div>

      <!-- Next Steps -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4 flex items-center">
          <Lightbulb class="w-5 h-5 mr-2" />
          Next Steps
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-3">
            <h4 class="font-medium text-blue-800 dark:text-blue-200">Immediate Actions</h4>
            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
              <li>• Log in to your admin dashboard</li>
              <li>• Configure your email settings</li>
              <li>• Set up your first department</li>
              <li>• Create user roles and permissions</li>
            </ul>
          </div>
          <div class="space-y-3">
            <h4 class="font-medium text-blue-800 dark:text-blue-200">Recommended Setup</h4>
            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
              <li>• Configure ticket categories and priorities</li>
              <li>• Set up email templates</li>
              <li>• Enable real-time notifications</li>
              <li>• Customize your helpdesk appearance</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Support Information -->
      <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
          <HelpCircle class="w-5 h-5 mr-2" />
          Need Help?
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="text-center">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
              <BookOpen class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h4 class="font-medium text-slate-900 dark:text-white mb-1">Documentation</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">Comprehensive guides and tutorials</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
              <MessageSquare class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <h4 class="font-medium text-slate-900 dark:text-white mb-1">Support Forum</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">Community support and discussions</p>
          </div>
          <div class="text-center">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
              <Mail class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <h4 class="font-medium text-slate-900 dark:text-white mb-1">Email Support</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">Direct support via email</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-center items-center mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
      <button 
        @click="$emit('finish')"
        class="px-12 py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl text-lg"
      >
        <LogIn class="w-5 h-5 mr-2 inline" />
        Go to Dashboard
      </button>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8">
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Thank you for choosing HelpDesk! We hope you enjoy using our system.
      </p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { 
  CheckCircle, Clipboard, Zap, LogIn, BookOpen, Settings, 
  Lightbulb, HelpCircle, MessageSquare, Mail, ArrowRight 
} from 'lucide-vue-next'

export default {
  name: 'InstallerComplete',
  components: {
    CheckCircle,
    Clipboard,
    Zap,
    LogIn,
    BookOpen,
    Settings,
    Lightbulb,
    HelpCircle,
    MessageSquare,
    Mail,
    ArrowRight
  },
  emits: ['finish'],
  setup() {
    const installationTime = ref('2 minutes 30 seconds')

    const goToLogin = () => {
      window.location.href = '/login'
    }

    const viewDocumentation = () => {
      // Open documentation in new tab
      window.open('https://helpdesk-docs.example.com', '_blank')
    }

    const configureSettings = () => {
      // Redirect to settings page
      window.location.href = '/dashboard/settings'
    }

    onMounted(() => {
      // Calculate actual installation time
      const startTime = localStorage.getItem('installationStartTime')
      if (startTime) {
        const duration = Date.now() - parseInt(startTime)
        const minutes = Math.floor(duration / 60000)
        const seconds = Math.floor((duration % 60000) / 1000)
        installationTime.value = `${minutes} minutes ${seconds} seconds`
        localStorage.removeItem('installationStartTime')
      }
    })

    return {
      installationTime,
      goToLogin,
      viewDocumentation,
      configureSettings
    }
  }
}
</script>
