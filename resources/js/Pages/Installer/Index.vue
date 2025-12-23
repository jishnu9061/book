<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-400/20 to-pink-400/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Container -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="w-full max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="inline-block">
            <img src="/images/logo.png" alt="HelpDesk Logo" class="h-16 w-auto mx-auto mb-4" />
          </div>
          <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">
            Welcome to HelpDesk
          </h1>
          <p class="text-lg text-slate-600 dark:text-slate-400">
            Let's get your helpdesk system up and running in just a few steps
          </p>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8">
          <div class="flex items-center justify-center space-x-4">
            <div 
              v-for="(step, index) in steps" 
              :key="step.id"
              class="flex items-center"
            >
              <!-- Step Circle -->
              <div 
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300',
                  getStepStatus(index) === 'completed' 
                    ? 'bg-green-500 text-white' 
                    : getStepStatus(index) === 'current'
                    ? 'bg-blue-500 text-white'
                    : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400'
                ]"
              >
                <Check v-if="getStepStatus(index) === 'completed'" class="w-5 h-5" />
                <span v-else>{{ index + 1 }}</span>
              </div>
              
              <!-- Step Label -->
              <div class="ml-3 hidden sm:block">
                <p 
                  :class="[
                    'text-sm font-medium transition-colors',
                    getStepStatus(index) === 'current' 
                      ? 'text-blue-600 dark:text-blue-400' 
                      : getStepStatus(index) === 'completed'
                      ? 'text-green-600 dark:text-green-400'
                      : 'text-slate-500 dark:text-slate-400'
                  ]"
                >
                  {{ step.title }}
                </p>
              </div>
              
              <!-- Connector Line -->
              <div 
                v-if="index < steps.length - 1"
                :class="[
                  'w-16 h-0.5 mx-4 transition-colors duration-300',
                  getStepStatus(index) === 'completed' 
                    ? 'bg-green-500' 
                    : 'bg-slate-200 dark:bg-slate-700'
                ]"
              ></div>
            </div>
          </div>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-white/20 dark:border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">
          <!-- Step Content -->
          <div class="p-8">
            <!-- Step 1: Welcome & Requirements -->
            <div v-if="currentStep === 0">
              <InstallerWelcome @next="nextStep" />
            </div>

            <!-- Step 2: License Verification -->
            <div v-if="currentStep === 1">
              <InstallerLicense 
                v-model:form="installData.license"
                @next="nextStep" 
                @back="prevStep"
              />
            </div>

            <!-- Step 3: Environment Configuration -->
            <div v-if="currentStep === 2">
              <InstallerEnvironment 
                v-model:form="installData.environment"
                @next="nextStep" 
                @back="prevStep"
              />
            </div>

            <!-- Step 4: Database Configuration -->
            <div v-if="currentStep === 3">
              <InstallerDatabase 
                v-model:form="installData.database"
                @next="nextStep" 
                @back="prevStep"
              />
            </div>

            <!-- Step 5: Admin Setup -->
            <div v-if="currentStep === 4">
              <InstallerAdmin 
                v-model:form="installData.admin"
                @next="nextStep" 
                @back="prevStep"
              />
            </div>

            <!-- Step 6: Installation Progress -->
            <div v-if="currentStep === 5">
              <InstallerProgress 
                :install-data="installData"
                @complete="nextStep"
                @error="handleInstallationError"
              />
            </div>

            <!-- Step 7: Completion -->
            <div v-if="currentStep === 6">
              <InstallerComplete @finish="finishInstallation" />
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Need help? Check our 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">installation guide</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed } from 'vue'
import { Check } from 'lucide-vue-next'
import InstallerWelcome from './Steps/Welcome.vue'
import InstallerLicense from './Steps/License.vue'
import InstallerEnvironment from './Steps/Environment.vue'
import InstallerDatabase from './Steps/Database.vue'
import InstallerAdmin from './Steps/Admin.vue'
import InstallerProgress from './Steps/Progress.vue'
import InstallerComplete from './Steps/Complete.vue'

export default {
  name: 'InstallerIndex',
  components: {
    Check,
    InstallerWelcome,
    InstallerLicense,
    InstallerEnvironment,
    InstallerDatabase,
    InstallerAdmin,
    InstallerProgress,
    InstallerComplete,
  },
  setup() {
    const currentStep = ref(0)
    
    const steps = [
      { id: 'welcome', title: 'Welcome' },
      { id: 'license', title: 'License' },
      { id: 'environment', title: 'Environment' },
      { id: 'database', title: 'Database' },
      { id: 'admin', title: 'Admin Setup' },
      { id: 'install', title: 'Installing' },
      { id: 'complete', title: 'Complete' }
    ]

    const installData = reactive({
      license: {
        purchaseCode: '',
        verified: false
      },
      environment: {
        appName: 'HelpDesk',
        appUrl: window.location.origin,
        appEnv: 'production',
        appDebug: false
      },
      database: {
        connection: 'mysql',
        host: 'localhost',
        port: 3306,
        name: '',
        username: '',
        password: '',
        tested: false
      },
      admin: {
        firstName: '',
        lastName: '',
        email: '',
        password: '',
        confirmPassword: ''
      }
    })

    const getStepStatus = (index) => {
      if (index < currentStep.value) return 'completed'
      if (index === currentStep.value) return 'current'
      return 'pending'
    }

    const nextStep = () => {
      if (currentStep.value < steps.length - 1) {
        currentStep.value++
      }
    }

    const prevStep = () => {
      if (currentStep.value > 0) {
        currentStep.value--
      }
    }

    const handleInstallationError = (error) => {
      console.error('Installation error:', error)
      
      // Show user-friendly error message
      const errorMessage = error.message || 'An unexpected error occurred during installation'
      
      // Create error notification
      const errorDiv = document.createElement('div')
      errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-md'
      errorDiv.innerHTML = `
        <div class="flex items-start">
          <div class="flex-1">
            <h4 class="font-semibold">Installation Error</h4>
            <p class="text-sm mt-1">${errorMessage}</p>
            <p class="text-xs mt-2 opacity-75">Please check the browser console for more details.</p>
          </div>
          <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
            ×
          </button>
        </div>
      `
      document.body.appendChild(errorDiv)
      
      // Auto-remove after 10 seconds
      setTimeout(() => {
        if (errorDiv.parentElement) {
          errorDiv.remove()
        }
      }, 10000)
    }

    const finishInstallation = () => {
      // Redirect to login or dashboard
      window.location.href = '/login'
    }

    return {
      currentStep,
      steps,
      installData,
      getStepStatus,
      nextStep,
      prevStep,
      handleInstallationError,
      finishInstallation
    }
  }
}
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out;
}

/* Glassmorphism effect */
.backdrop-blur-xl {
  backdrop-filter: blur(20px);
}

/* Smooth transitions */
* {
  transition: all 0.3s ease-in-out;
}
</style>
