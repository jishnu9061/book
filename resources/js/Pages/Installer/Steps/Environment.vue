<template>
  <div class="animate-fade-in-up">
    <div class="text-center mb-8">
      <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
        <Settings class="w-10 h-10 text-white" />
      </div>
      <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
        Environment Configuration
      </h2>
      <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
        Configure your application settings and environment variables.
      </p>
    </div>

    <!-- Configuration Form -->
    <div class="max-w-3xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Application Settings -->
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
            <Globe class="w-5 h-5 mr-2" />
            Application Settings
          </h3>

          <div class="space-y-4">
            <!-- App Name -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Application Name <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.appName"
                type="text"
                placeholder="HelpDesk"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
            </div>

            <!-- App URL -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Application URL <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.appUrl"
                type="url"
                placeholder="https://yourdomain.com"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                The full URL where your application will be accessible
              </p>
            </div>

            <!-- Environment -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Environment <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.appEnv"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @change="updateForm"
              >
                <option value="production">Production</option>
                <option value="staging">Staging</option>
                <option value="local">Local Development</option>
              </select>
            </div>

            <!-- Debug Mode -->
            <div class="flex items-center justify-between p-4 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
              <div>
                <h4 class="font-medium text-slate-900 dark:text-white">Debug Mode</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                  Enable detailed error messages (not recommended for production)
                </p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  v-model="form.appDebug"
                  type="checkbox"
                  class="sr-only peer"
                  @change="updateForm"
                />
                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
              </label>
            </div>
          </div>
        </div>

        <!-- Mail Configuration -->
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
            <Mail class="w-5 h-5 mr-2" />
            Mail Configuration
          </h3>

          <div class="space-y-4">
            <!-- Mail Driver -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                Mail Driver
              </label>
              <select
                v-model="form.mailDriver"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @change="updateForm"
              >
                <option value="smtp">SMTP</option>
                <option value="mailgun">Mailgun</option>
                <option value="ses">Amazon SES</option>
                <option value="mail">PHP Mail</option>
              </select>
            </div>

            <!-- SMTP Settings (shown when SMTP is selected) -->
            <div v-if="form.mailDriver === 'smtp'" class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  SMTP Host
                </label>
                <input
                  v-model="form.mailHost"
                  type="text"
                  placeholder="smtp.gmail.com"
                  class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                  @input="updateForm"
                />
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Port
                  </label>
                  <input
                    v-model="form.mailPort"
                    type="number"
                    placeholder="587"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                    @input="updateForm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Encryption
                  </label>
                  <select
                    v-model="form.mailEncryption"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                    @change="updateForm"
                  >
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                    <option value="">None</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  Username
                </label>
                <input
                  v-model="form.mailUsername"
                  type="text"
                  placeholder="your-email@gmail.com"
                  class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                  @input="updateForm"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  Password
                </label>
                <input
                  v-model="form.mailPassword"
                  type="password"
                  placeholder="Your email password or app password"
                  class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                  @input="updateForm"
                />
              </div>
            </div>

            <!-- From Address -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                From Address
              </label>
              <input
                v-model="form.mailFromAddress"
                type="email"
                placeholder="noreply@yourdomain.com"
                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                @input="updateForm"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Pusher Configuration -->
      <div class="mt-6 bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center">
          <Zap class="w-5 h-5 mr-2" />
          Real-time Features (Pusher)
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              App ID
            </label>
            <input
              v-model="form.pusherAppId"
              type="text"
              placeholder="Your Pusher App ID"
              class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
              @input="updateForm"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              App Key
            </label>
            <input
              v-model="form.pusherAppKey"
              type="text"
              placeholder="Your Pusher App Key"
              class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
              @input="updateForm"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              App Secret
            </label>
            <input
              v-model="form.pusherAppSecret"
              type="password"
              placeholder="Your Pusher App Secret"
              class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
              @input="updateForm"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              App Cluster
            </label>
            <input
              v-model="form.pusherAppCluster"
              type="text"
              placeholder="us2, eu, ap-southeast-1"
              class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
              @input="updateForm"
            />
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
              Default: us2
            </p>
          </div>
        </div>

        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
          Pusher is optional but recommended for real-time notifications and chat features.
          You can configure it later in the admin panel.
        </p>
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
        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
      >
        Continue
        <ArrowRight class="w-4 h-4 ml-2 inline" />
      </button>
    </div>
  </div>
</template>

<script>
import { reactive, watch } from 'vue'
import { Settings, Globe, Mail, Zap, ArrowLeft, ArrowRight } from 'lucide-vue-next'

export default {
  name: 'InstallerEnvironment',
  components: {
    Settings,
    Globe,
    Mail,
    Zap,
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
    const form = reactive({
      appName: props.form.appName || 'HelpDesk',
      appUrl: props.form.appUrl || window.location.origin,
      appEnv: props.form.appEnv || 'production',
      appDebug: props.form.appDebug || false,
      mailDriver: 'smtp',
      mailHost: '',
      mailPort: 587,
      mailEncryption: 'tls',
      mailUsername: '',
      mailPassword: '',
      mailFromAddress: '',
      pusherAppId: '',
      pusherAppKey: '',
      pusherAppSecret: '',
      pusherAppCluster: 'us2'
    })

    const updateForm = () => {
      emit('update:form', { ...form })
    }

    // Watch for form changes
    watch(form, () => {
      updateForm()
    }, { deep: true })

    return {
      form,
      updateForm
    }
  }
}
</script>
