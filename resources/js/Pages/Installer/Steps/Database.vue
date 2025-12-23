<template>
  <div class="animate-fade-in-up">
    <div class="text-center mb-8">
      <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
        <Database class="w-10 h-10 text-white" />
      </div>
      <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
        Database Configuration
      </h2>
      <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        Configure your database connection settings. Make sure your database is created and accessible.
      </p>
    </div>

    <!-- Database Form -->
    <div class="max-w-2xl mx-auto">
      <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6 mb-6">
        <div class="space-y-4">
          <!-- Database Type -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              Database Type <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.connection"
              class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
              @change="updateForm"
            >
              <option value="mysql">MySQL</option>
              <option value="pgsql">PostgreSQL</option>
              <option value="sqlite">SQLite</option>
            </select>
          </div>

          <!-- SQLite Configuration -->
          <div v-if="form.connection === 'sqlite'" class="space-y-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
              <div class="flex items-start">
                <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" />
                <div>
                  <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">SQLite Database</h4>
                  <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                    SQLite will create a database file automatically. No additional configuration needed.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- MySQL/PostgreSQL Configuration -->
          <div v-else class="space-y-4">
            <!-- Host -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Database Host <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.host"
                type="text"
                placeholder="localhost"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
            </div>

            <!-- Port -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Database Port <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.port"
                type="number"
                :placeholder="form.connection === 'mysql' ? '3306' : '5432'"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
            </div>

            <!-- Database Name -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Database Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="helpdesk_db"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Make sure this database exists on your server
              </p>
            </div>

            <!-- Username -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Database Username <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.username"
                type="text"
                placeholder="root"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Database Password
              </label>
              <input
                v-model="form.password"
                type="password"
                placeholder="Your database password"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Connection Test -->
      <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Test Connection</h3>
          <button
            @click="testConnection"
            :disabled="!canTestConnection || connectionStatus === 'testing'"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-all duration-200',
              canTestConnection && connectionStatus !== 'testing'
                ? 'bg-blue-600 hover:bg-blue-700 text-white'
                : 'bg-slate-300 dark:bg-slate-600 text-slate-500 dark:text-slate-400 cursor-not-allowed'
            ]"
          >
            <div v-if="connectionStatus === 'testing'" class="flex items-center">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
              Testing...
            </div>
            <div v-else class="flex items-center">
              <Wifi class="w-4 h-4 mr-2" />
              Test Connection
            </div>
          </button>
        </div>

        <!-- Connection Status -->
        <div v-if="connectionStatus === 'success'" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
          <div class="flex items-center">
            <Check class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
            <div>
              <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Connection Successful</h4>
              <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                Database connection is working properly. You can proceed with the installation.
              </p>
            </div>
          </div>
        </div>

        <div v-else-if="connectionStatus === 'failed'" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
          <div class="flex items-center">
            <X class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" />
            <div>
              <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Connection Failed</h4>
              <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                {{ connectionError || 'Please check your database settings and try again.' }}
              </p>
            </div>
          </div>
        </div>

        <div v-else-if="connectionStatus === ''" class="text-center py-8">
          <Database class="w-12 h-12 text-slate-400 mx-auto mb-3" />
          <p class="text-slate-500 dark:text-slate-400">
            Click "Test Connection" to verify your database settings
          </p>
        </div>
      </div>

      <!-- Help Section -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
        <div class="flex items-start">
          <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" />
          <div>
            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Database Setup Tips</h4>
            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
              <li>• Create a new database for your HelpDesk installation</li>
              <li>• Use a dedicated database user with appropriate permissions</li>
              <li>• Make sure your database server is running and accessible</li>
              <li>• For shared hosting, use the credentials provided by your host</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
      <button
        @click="$emit('back')"
        class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
      >
        <ArrowLeft class="w-4 h-4 mr-2 inline" />
        Back
      </button>

      <button
        @click="$emit('next')"
        :disabled="connectionStatus !== 'success'"
        :class="[
          'px-8 py-3 rounded-lg font-semibold transition-all duration-200 transform',
          connectionStatus === 'success'
            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white hover:scale-105 shadow-lg hover:shadow-xl'
            : 'bg-slate-300 dark:bg-slate-600 text-slate-500 dark:text-slate-400 cursor-not-allowed'
        ]"
      >
        Continue
        <ArrowRight class="w-4 h-4 ml-2 inline" />
      </button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue'
import { Database, Check, X, Info, Wifi, ArrowLeft, ArrowRight } from 'lucide-vue-next'
import { installerApiRequest, handleApiResponse } from '../../../Utils/InstallerApi.js'

export default {
  name: 'InstallerDatabase',
  components: {
    Database,
    Check,
    X,
    Info,
    Wifi,
    ArrowLeft,
    ArrowRight
  },
  props: {
    form: {
      type: Object,
      required: true
    }
  },
  emits: ['next', 'back', 'update:form'],
  setup(props, { emit }) {
    const connectionStatus = ref('') // '', 'testing', 'success', 'failed'
    const connectionError = ref('')

    const form = reactive({
      connection: props.form.connection || 'mysql',
      host: props.form.host || 'localhost',
      port: props.form.port || 3306,
      name: props.form.name || '',
      username: props.form.username || '',
      password: props.form.password || ''
    })

    const canTestConnection = computed(() => {
      if (form.connection === 'sqlite') return true
      return form.host && form.port && form.name && form.username
    })

    const updateForm = () => {
      emit('update:form', { ...form })
    }

    const testConnection = async () => {
      if (!canTestConnection.value) return

      connectionStatus.value = 'testing'
      connectionError.value = ''

      try {
        const response = await installerApiRequest('/install/test-database', {
          body: JSON.stringify(form)
        })

        const data = await handleApiResponse(response)

        if (data.success) {
          connectionStatus.value = 'success'
          emit('update:form', { ...form, tested: true })
        } else {
          connectionStatus.value = 'failed'
          connectionError.value = data.message || 'Connection failed'
        }
      } catch (error) {
        connectionStatus.value = 'failed'
        connectionError.value = 'Network error. Please try again.'
        console.error('Database test error:', error)
      }
    }

    // Watch for form changes
    watch(form, () => {
      updateForm()
      // Reset connection status when form changes
      if (connectionStatus.value) {
        connectionStatus.value = ''
        connectionError.value = ''
      }
    }, { deep: true })

    return {
      form,
      connectionStatus,
      connectionError,
      canTestConnection,
      updateForm,
      testConnection
    }
  }
}
</script>
