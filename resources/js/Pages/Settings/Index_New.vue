<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="title" />
    
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $t('Global Settings') }}</h1>
              <p class="mt-2 text-slate-600 dark:text-slate-400">{{ $t('Configure your helpdesk system settings') }}</p>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-xl flex items-center justify-center">
                <Settings class="w-6 h-6 text-primary-600 dark:text-primary-400" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="update">
        <!-- Tab Navigation -->
        <div class="mb-8">
          <nav class="flex space-x-8" aria-label="Tabs">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                activeTab === tab.id
                  ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                  : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-300',
                'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
              ]"
            >
              <component :is="tab.icon" class="w-5 h-5 inline-block mr-2" />
              {{ tab.name }}
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8">
          <!-- General Settings Tab -->
          <div v-show="activeTab === 'general'" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('General Settings') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Basic application configuration') }}</p>
              </div>
              <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <text-input 
                    v-model="form.app_name" 
                    :error="form.errors.app_name" 
                    :label="$t('App Name')" 
                    :placeholder="$t('Enter application name')"
                  />
                  <text-input 
                    v-model="form.site_key" 
                    :error="form.errors.site_key" 
                    :label="$t('Google ReCaptcha Site Key')" 
                    :placeholder="$t('Enter reCAPTCHA site key')"
                  />
                  <select-input 
                    v-model="form.default_language" 
                    :error="form.errors.default_language" 
                    :label="$t('Default Language')"
                  >
                    <option v-for="l in languages" :key="l.id" :value="l.code">{{ l.name }}</option>
                  </select-input>
                </div>
              </div>
            </div>
          </div>

          <!-- Branding Tab -->
          <div v-show="activeTab === 'branding'" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Branding & Assets') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Upload logos and customize your brand') }}</p>
              </div>
              <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  <!-- Logo Upload -->
                  <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Main Logo') }}</label>
                    <div class="space-y-3">
                      <file-input 
                        v-model="form.logo" 
                        :error="form.errors.logo" 
                        type="file" 
                        accept="image/png,image/jpeg,image/jpg" 
                        :label="$t('Upload Logo')"
                      />
                      <div v-if="form.main_logo" class="relative">
                        <img :src="form.main_logo" class="w-32 h-16 object-contain border border-slate-200 dark:border-slate-600 rounded-lg" />
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                          <Check class="w-4 h-4 text-white" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- White Logo Upload -->
                  <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('White Logo') }}</label>
                    <div class="space-y-3">
                      <file-input 
                        v-model="form.logo_white" 
                        :error="form.errors.logo_white" 
                        type="file" 
                        accept="image/png,image/jpeg,image/jpg" 
                        :label="$t('Upload White Logo')"
                      />
                      <div v-if="form.main_logo_white" class="relative">
                        <img :src="form.main_logo_white" class="w-32 h-16 object-contain border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-800" />
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                          <Check class="w-4 h-4 text-white" />
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Favicon Upload -->
                  <div class="space-y-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Favicon') }}</label>
                    <div class="space-y-3">
                      <file-input 
                        v-model="form.favicon" 
                        :error="form.errors.favicon" 
                        type="file" 
                        accept="image/png,image/ico" 
                        :label="$t('Upload Favicon')"
                      />
                      <div v-if="form.main_favicon" class="relative">
                        <img :src="form.main_favicon" class="w-8 h-8 object-contain border border-slate-200 dark:border-slate-600 rounded" />
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                          <Check class="w-4 h-4 text-white" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Features Tab -->
          <div v-show="activeTab === 'features'" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Feature Settings') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Enable or disable system features') }}</p>
              </div>
              <div class="p-6 space-y-6">
                <!-- Enable Options -->
                <div>
                  <h4 class="text-md font-medium text-slate-900 dark:text-white mb-4">{{ $t('Enable Options') }}</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(option, ni) in form.enable_options" :key="ni" class="flex items-center p-4 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                      <label :for="option.slug" class="flex items-center cursor-pointer w-full">
                        <div class="relative">
                          <input 
                            :id="option.slug" 
                            type="checkbox" 
                            v-model="option.value" 
                            class="sr-only"
                          />
                          <div class="w-10 h-6 bg-slate-200 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="option.value ? 'bg-primary-500' : ''">
                            <div class="w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 mt-1" :class="option.value ? 'translate-x-5' : 'translate-x-1'"></div>
                          </div>
                        </div>
                        <div class="ml-3">
                          <div class="text-sm font-medium text-slate-900 dark:text-white">{{ option.name }}</div>
                        </div>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Email Notifications -->
                <div>
                  <h4 class="text-md font-medium text-slate-900 dark:text-white mb-4">{{ $t('Email Notifications') }}</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(notification, ni) in form.email_notifications" :key="ni" class="flex items-center p-4 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
                      <label :for="notification.slug" class="flex items-center cursor-pointer w-full">
                        <div class="relative">
                          <input 
                            :id="notification.slug" 
                            type="checkbox" 
                            v-model="notification.value" 
                            class="sr-only"
                          />
                          <div class="w-10 h-6 bg-slate-200 dark:bg-slate-600 rounded-full shadow-inner transition-colors duration-200" :class="notification.value ? 'bg-primary-500' : ''">
                            <div class="w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 mt-1" :class="notification.value ? 'translate-x-5' : 'translate-x-1'"></div>
                          </div>
                        </div>
                        <div class="ml-3">
                          <div class="text-sm font-medium text-slate-900 dark:text-white">{{ notification.name }}</div>
                        </div>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Default Recipient -->
                <div>
                  <select-input 
                    v-model="form.default_recipient" 
                    :error="form.errors.default_recipient" 
                    :label="$t('Default Email Recipient for customer ticket')"
                  >
                    <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                  </select-input>
                </div>
              </div>
            </div>
          </div>

          <!-- Ticket Fields Tab -->
          <div v-show="activeTab === 'ticket-fields'" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Ticket Field Configuration') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Configure which fields to show and require') }}</p>
              </div>
              <div class="p-6 space-y-6">
                <!-- Hide Ticket Fields -->
                <div>
                  <h4 class="text-md font-medium text-slate-900 dark:text-white mb-4">{{ $t('Hide ticket fields') }}</h4>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <label v-for="htf in hide_ticket_fields" :key="htf" class="flex items-center p-3 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200 cursor-pointer">
                      <input 
                        :id="'htf_'+htf" 
                        v-model="form.hide_ticket_fields" 
                        :value="htf" 
                        type="checkbox" 
                        class="w-4 h-4 text-primary-600 bg-slate-100 border-slate-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
                      />
                      <span class="ml-2 text-sm text-slate-700 dark:text-slate-300 capitalize">{{ htf.replace(/_/g, ' ') }}</span>
                    </label>
                  </div>
                </div>

                <!-- Required Ticket Fields -->
                <div>
                  <h4 class="text-md font-medium text-slate-900 dark:text-white mb-4">{{ $t('Required fields to submit ticket') }}</h4>
                  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <label v-for="rtf in required_ticket_fields" :key="rtf" class="flex items-center p-3 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200 cursor-pointer">
                      <input 
                        @change="checkRequiredFields($event)" 
                        :id="'rtf_'+rtf" 
                        v-model="form.required_ticket_fields" 
                        :value="rtf" 
                        type="checkbox" 
                        class="w-4 h-4 text-primary-600 bg-slate-100 border-slate-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600"
                      />
                      <span class="ml-2 text-sm text-slate-700 dark:text-slate-300 capitalize">{{ rtf.replace(/_/g, ' ') }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Customization Tab -->
          <div v-show="activeTab === 'customization'" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Custom CSS') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Add custom styles to your helpdesk') }}</p>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <!-- CSS Editor -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Custom CSS Code') }}</label>
                    <textarea-input 
                      v-model="form.custom_css" 
                      :error="form.errors.custom_css" 
                      :rows="20" 
                      :placeholder="$t('Enter your custom CSS here...')"
                      class="font-mono text-sm"
                    />
                  </div>
                  
                  <!-- Live Preview -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Live Preview') }}</label>
                    <div class="border border-slate-200 dark:border-slate-600 rounded-lg p-4 bg-slate-50 dark:bg-slate-700 min-h-[400px]">
                      <div class="text-sm text-slate-600 dark:text-slate-400 mb-4">{{ $t('Preview of your custom styles:') }}</div>
                      <div class="space-y-2">
                        <div class="p-3 bg-white dark:bg-slate-800 rounded border" :style="form.custom_css">
                          <div class="font-semibold text-slate-900 dark:text-white">{{ $t('Sample Ticket') }}</div>
                          <div class="text-sm text-slate-600 dark:text-slate-400">{{ $t('This is how your custom styles will look') }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Tab -->
          <div v-show="activeTab === 'system'" class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
              <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('System Configuration') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Cron job and system settings') }}</p>
              </div>
              <div class="p-6 space-y-6">
                <!-- Cron Job Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                  <div class="flex items-start">
                    <div class="flex-shrink-0">
                      <Info class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ $t('Cron Job Instructions') }}</h4>
                      <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p class="mb-3">{{ $t('To send emails without delay, set up a cron job to run every 2-3 minutes:') }}</p>
                        <div class="bg-slate-800 text-green-400 p-3 rounded font-mono text-sm mb-3">
                          */2 * * * * /usr/bin/php artisan queue:work --queue=high,default --stop-when-empty
                        </div>
                        <p class="mb-3">{{ $t('For shared hosting (cPanel), use this URL-based approach:') }}</p>
                        <div class="bg-slate-800 text-green-400 p-3 rounded font-mono text-sm mb-3">
                          */2 * * * * wget -q -O - https://yourdomain.com/cron/queue_work >/dev/null 2>&1
                        </div>
                        <p class="text-sm font-medium">{{ $t('After setting up cron, enable queue processing by adding to your .env file:') }}</p>
                        <div class="bg-slate-800 text-green-400 p-3 rounded font-mono text-sm mt-2">
                          QUEUE_ENABLE=true
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="mt-8 flex justify-end">
          <loading-button 
            :loading="form.processing" 
            class="btn-indigo px-8 py-3 text-lg font-medium"
            type="submit"
          >
            <Save class="w-5 h-5 mr-2" />
            {{ $t('Save Settings') }}
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import Pagination from '@/Shared/Pagination.vue'
import TextInput from '@/Shared/TextInput.vue'
import TextareaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import FileInput from '@/Shared/FileInput.vue'
import { 
  Settings, 
  Check, 
  Save, 
  Info,
  Globe,
  Palette,
  ToggleLeft,
  FileText,
  Wrench,
  Monitor
} from 'lucide-vue-next'

export default {
  metaInfo: { title: 'Global Settings' },
  components: {
    Icon,
    Link,
    Head,
    FileInput,
    Pagination,
    TextInput,
    TextareaInput,
    SelectInput,
    LoadingButton,
    Settings,
    Check,
    Save,
    Info,
    Globe,
    Palette,
    ToggleLeft,
    FileText,
    Wrench,
    Monitor,
  },
  layout: Layout,
  props: {
      title: String,
      site_key: String,
      settings: Object,
      languages: Array,
      users: Array,
      pusher: Boolean,
  },
  data() {
    return {
        activeTab: 'general',
        tabs: [
          { id: 'general', name: 'General', icon: Globe },
          { id: 'branding', name: 'Branding', icon: Palette },
          { id: 'features', name: 'Features', icon: ToggleLeft },
          { id: 'ticket-fields', name: 'Ticket Fields', icon: FileText },
          { id: 'customization', name: 'Customization', icon: Monitor },
          { id: 'system', name: 'System', icon: Wrench },
        ],
        form: this.$inertia.form({
            app_name: this.settings.app_name.value,
            logo: null,
            site_key: this.site_key,
            default_recipient: this.settings.default_recipient?this.settings.default_recipient.value:1,
            logo_white: null,
            favicon: null,
            main_logo: '/images/logo.png',
            main_logo_white: '/images/logo_white.png',
            main_favicon: '/favicon.png',
            default_language: this.settings.default_language.value,
            footer_text: this.settings.footer_text.value,
            custom_css: typeof this.settings.custom_css !== 'undefined' && this.settings.custom_css ? this.settings.custom_css.value : null,
            email_notifications: this.settings.email_notifications.value.map(en=>{return {'name': en.name,'slug': en.slug,'value': !!en.value}}),
            enable_options: this.settings.enable_options.value.map(eo=>{return {'name': eo.name,'slug': eo.slug,'value': !!eo.value}}),
            hide_ticket_fields: this.settings.hide_ticket_fields && this.settings.hide_ticket_fields.value ? this.settings.hide_ticket_fields.value : [],
            required_ticket_fields: this.settings.required_ticket_fields && this.settings.required_ticket_fields.value ? this.settings.required_ticket_fields.value : [],
        }),
        hide_ticket_fields: [ 'department', 'category', 'sub_category', 'ticket_type', 'assigned_to'],
        required_ticket_fields: [ 'department', 'category', 'sub_category', 'ticket_type', 'assigned_to'],
    }
  },
    created() {
    },
    methods: {
        checkRequiredFields(e){
            if(e.target.checked && ['category', 'sub_category'].includes(e.target.value)){
                if(!this.form.required_ticket_fields.includes('category')){
                    this.form.required_ticket_fields.push('category')
                }
                if(!this.form.required_ticket_fields.includes('department')){
                    this.form.required_ticket_fields.push('department')
                }
            }
            if(!e.target.checked && ['department'].includes(e.target.value)){
                if(this.form.required_ticket_fields.includes('category')){
                    this.removeElement(this.form.required_ticket_fields, 'category')
                }
                if(this.form.required_ticket_fields.includes('sub_category')){
                    this.removeElement(this.form.required_ticket_fields, 'sub_category')
                }
            }
            if(!e.target.checked && ['category'].includes(e.target.value)){
                if(this.form.required_ticket_fields.includes('sub_category')){
                    this.removeElement(this.form.required_ticket_fields, 'sub_category')
                }
            }
        },
        removeElement(obj, el){
            const index = obj.indexOf(el)
            obj.splice(index, 1)
        },
      update() {
          const vm = this;
          const enableChat = this.form.enable_options.find(o=>o.slug='chat')
          if(!!enableChat.value && !this.pusher){
              alert('Please setup the pusher configuration to enable chat.');
              return
          }
          this.form.post(this.route('global.update'), {
              onSuccess: () => {
                  const successMessage = vm.$page.props.flash.success
                  this.form.logo = null
                  this.form.logo_white = null
                  if(successMessage){
                      window.location.reload()
                  }
              }
          })
      },
  },
}
</script>
