<template>
  <div>
    <Head :title="title" />

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
      <div class="px-6 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('System Updates') }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $t('Check for the latest version and update your system') }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <button
              type="button"
              @click="refreshUpdateInfo"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <RefreshCw :class="{ 'animate-spin': form.processing }" class="w-4 h-4 mr-2" />
              {{ $t('Refresh') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Information Card -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
      <form @submit.prevent="checkForUpdates">
        <div class="p-8">
          <!-- Current Version Info -->
          <div class="mb-8">
            <div class="flex items-center space-x-3 mb-4">
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                <Package class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Current Version') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Your system is currently running') }}</p>
              </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Version') }}</span>
                <span class="text-lg font-bold text-slate-900 dark:text-white">{{ current_version || 'Unknown' }}</span>
              </div>
            </div>
          </div>

          <!-- Update Check Section -->
          <div class="mb-8">
            <div class="flex items-center space-x-3 mb-4">
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                <Download class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Check for Updates') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Click the button below to check for available updates') }}</p>
              </div>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
              <div class="flex items-center">
                <AlertCircle class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" />
                <div>
                  <h4 class="text-sm font-medium text-red-800 dark:text-red-200">{{ $t('Update Check Failed') }}</h4>
                  <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ errorMessage }}</p>
                </div>
              </div>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
              <div class="flex items-center">
                <CheckCircle class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                <div>
                  <h4 class="text-sm font-medium text-green-800 dark:text-green-200">{{ $t('Update Check Complete') }}</h4>
                  <p class="text-sm text-green-700 dark:text-green-300 mt-1">{{ successMessage }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Latest Version Info -->
          <div v-if="latest_version" class="mb-8">
            <div class="flex items-center space-x-3 mb-4">
              <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                <AlertTriangle class="w-6 h-6 text-orange-600 dark:text-orange-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Update Available') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('A new version is available for download') }}</p>
              </div>
            </div>

            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-6 border border-orange-200 dark:border-orange-800">
              <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ $t('Latest Version') }}</span>
                <span class="text-xl font-bold text-orange-900 dark:text-orange-100">{{ latest_version }}</span>
              </div>

              <div class="space-y-4">
                <div>
                  <h4 class="font-semibold text-orange-900 dark:text-orange-100 mb-2">{{ $t('Manual Update Instructions') }}</h4>
                  <div class="space-y-2 text-sm text-orange-800 dark:text-orange-200">
                    <p>1. {{ $t('Download the latest files from CodeCanyon') }}</p>
                    <p>2. {{ $t('Replace the changed files with the downloaded files') }}</p>
                    <p>3. {{ $t('Update the version number in your .env file') }}</p>
                    <p>4. {{ $t('Clear your application cache') }}</p>
                  </div>
                </div>

                <div v-if="changedFiles && changedFiles.length > 0" class="mt-4">
                  <h5 class="font-medium text-orange-900 dark:text-orange-100 mb-2">{{ $t('Changed Files') }}</h5>
                  <div class="bg-white dark:bg-slate-800 rounded border border-orange-200 dark:border-orange-700 max-h-40 overflow-y-auto">
                    <div v-for="file in changedFiles" :key="file" class="px-3 py-2 text-xs font-mono text-slate-700 dark:text-slate-300 border-b border-orange-100 dark:border-orange-800 last:border-b-0">
                      {{ file }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Updates Available -->
          <div v-else-if="!form.processing && hasCheckedForUpdates" class="mb-8">
            <div class="flex items-center space-x-3 mb-4">
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('System Up to Date') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('You are running the latest version') }}</p>
              </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
              <div class="flex items-center">
                <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" />
                <div>
                  <h4 class="font-medium text-green-900 dark:text-green-100">{{ $t('No Updates Available') }}</h4>
                  <p class="text-sm text-green-700 dark:text-green-300 mt-1">{{ $t('Your system is already running the latest version') }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Demo Mode Warning -->
          <div v-if="demo" class="mb-8">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
              <div class="flex items-center">
                <AlertTriangle class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3" />
                <div>
                  <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ $t('Demo Mode') }}</h4>
                  <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">{{ $t('This is a demo environment. Update functionality is limited.') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-8 py-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600 flex items-center justify-between">
          <div class="text-sm text-slate-600 dark:text-slate-400">
            {{ $t('Last checked:') }} {{ lastCheckedAt || $t('Never') }}
          </div>
          <div class="flex items-center space-x-3">
            <loading-button
              :loading="form.processing"
              class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
              type="submit"
            >
              <Download class="w-4 h-4 mr-2" />
              {{ $t('Check for Updates') }}
            </loading-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import {
  Package,
  Download,
  RefreshCw,
  AlertCircle,
  CheckCircle,
  AlertTriangle
} from 'lucide-vue-next'
import axios from 'axios'

export default {
  metaInfo: { title: 'System Updates' },
  components: {
    Head,
    Layout,
    LoadingButton,
    Package,
    Download,
    RefreshCw,
    AlertCircle,
    CheckCircle,
    AlertTriangle
  },
  layout: Layout,
  props: {
    title: String,
    demo: Boolean,
    current_version: {
      type: String,
      required: false
    },
  },
  remember: 'form',
  data() {
    return {
      changedFiles: [],
      latest_version: '',
      errorMessage: '',
      successMessage: '',
      hasCheckedForUpdates: false,
      lastCheckedAt: null,
      form: this.$inertia.form({
        processing: false,
      }),
    }
  },
  methods: {
    checkForUpdates() {
      this.clearMessages();
      this.form.processing = true;
      this.hasCheckedForUpdates = false;

      axios.post(this.route('settings.update.check'), {})
        .then((response) => {
          const data = response.data;

          // Update changed files
          if (data.files && Array.isArray(data.files)) {
            this.changedFiles = data.files;
          }

          // Update latest version
          if (data.version) {
            this.latest_version = data.version;
            this.successMessage = this.$t('Update check completed successfully. A new version is available.');
          } else {
            this.latest_version = '';
            this.successMessage = this.$t('Update check completed. You are running the latest version.');
          }

          this.hasCheckedForUpdates = true;
          this.lastCheckedAt = new Date().toLocaleString();
        })
        .catch((error) => {
          console.error('Update check error:', error);
          this.errorMessage = this.$t('Failed to check for updates. Please try again later.');
          this.hasCheckedForUpdates = true;
        })
        .finally(() => {
          this.form.processing = false;
        });
    },

    refreshUpdateInfo() {
      this.clearMessages();
      this.latest_version = '';
      this.changedFiles = [];
      this.hasCheckedForUpdates = false;
      this.lastCheckedAt = null;
    },

    clearMessages() {
      this.errorMessage = '';
      this.successMessage = '';
    },

    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  },
  created() {
    // Auto-check for updates on page load if not in demo mode
    if (!this.demo && this.current_version) {
      // Small delay to let the page render first
      setTimeout(() => {
        this.checkForUpdates();
      }, 1000);
    }
  }
}
</script>
