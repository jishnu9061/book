<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Languages Management') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Manage system languages and localization settings') }}
              </p>
            </div>
            <Link
              :href="route('languages.create')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
            >
              <Plus class="w-4 h-4" />
              {{ $t('Add New Language') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search -->
      <div class="mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
          <SearchInput
            v-model="form.search"
            class="w-full max-w-md"
            @reset="reset"
            :placeholder="$t('Search languages...')"
          />
        </div>
      </div>

      <!-- Languages Table -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Language') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Code') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Status') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Created') }}
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr
                v-for="language in filteredLanguages"
                :key="language.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <Link
                    :href="route('languages.edit', language.id)"
                    class="group flex items-center gap-3"
                  >
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center border border-slate-200 dark:border-slate-600">
                        <CountryFlag
                          :code="language.code"
                          class="w-8 h-6 rounded-sm shadow-sm"
                        />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ language.name }}
                      </p>
                      <p v-if="language.native_name" class="text-sm text-slate-500 dark:text-slate-400">
                        {{ language.native_name }}
                      </p>
                    </div>
                  </Link>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                    <Code class="w-3 h-3 mr-1" />
                    {{ language.code.toUpperCase() }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      language.is_active
                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                    ]"
                  >
                    <div
                      :class="[
                        'w-1.5 h-1.5 rounded-full mr-1.5',
                        language.is_active ? 'bg-green-400' : 'bg-red-400'
                      ]"
                    ></div>
                    {{ language.is_active ? $t('Active') : $t('Inactive') }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(language.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('languages.edit', language.id)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </Link>
                    <button
                      @click="deleteLanguage(language)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Trash2 class="w-4 h-4" />
                      {{ $t('Delete') }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredLanguages.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-600">
            <CountryFlag code="en" class="w-12 h-8 rounded-sm shadow-sm" />
          </div>
          <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No languages found') }}</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $t('Get started by adding your first language.') }}</p>
          <Link
            :href="route('languages.create')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
          >
            <Plus class="w-4 h-4" />
            {{ $t('Add Language') }}
          </Link>
        </div>
      </div>
    </div>
  </div>

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmation
      :show="showDeleteModal"
      :title="deleteConfig.title"
      :message="deleteConfig.message"
      :item-name="deleteConfig.itemName"
      :item-type="deleteConfig.itemType"
      :delete-url="deleteConfig.deleteUrl"
      :delete-method="deleteConfig.deleteMethod"
      :delete-data="deleteConfig.deleteData"
      :confirm-button-text="deleteConfig.confirmButtonText"
      :cancel-button-text="deleteConfig.cancelButtonText"
      @close="hideDeleteConfirmation"
      @confirm="confirmDelete"
    />
  </template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import Layout from '@/Shared/Layout.vue'
import SearchInput from '@/Shared/SearchInput.vue'
import CountryFlag from '@/Shared/CountryFlag.vue'
import {
  Plus,
  Globe,
  Edit,
  Code,
  Trash2
} from 'lucide-vue-next'
import moment from 'moment'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

export default {
  layout: Layout,
    components: {
        Layout,
    Head,
    Link,
    SearchInput,
    CountryFlag,
    Plus,
    Globe,
    Edit,
    Code,
    Trash2,
    DeleteConfirmation,
  },
    props: {
        filters: Object,
        title: String,
        languages: Object,
    },
  data() {
    return {
        form: {
          search: this.filters.search || '',
        },
        showDeleteModal: false,
        deleteConfig: {
            title: 'Are you sure?',
            message: 'This action cannot be undone.',
            itemName: 'this item',
            itemType: 'item',
            deleteUrl: '',
            deleteMethod: 'delete',
            deleteData: {},
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }
    }
  },
  computed: {
    filteredLanguages() {
      if (!this.form.search) {
        return this.languages
      }
      return this.languages.filter(language =>
        language.name.toLowerCase().includes(this.form.search.toLowerCase()) ||
        language.code.toLowerCase().includes(this.form.search.toLowerCase()) ||
        (language.native_name && language.native_name.toLowerCase().includes(this.form.search.toLowerCase()))
      )
    }
  },
  methods: {
    search: throttle(function () {
      // Since we're using computed property for filtering, we don't need to make API calls
    }, 300),
    showDeleteConfirmation(config, onConfirmCallback = null) {
      Object.assign(this.deleteConfig, config);
      this.showDeleteModal = true;
      if (onConfirmCallback) {
        this.deleteConfig.onConfirmCallback = onConfirmCallback;
      }
    },
    hideDeleteConfirmation() {
      this.showDeleteModal = false;
      this.deleteConfig.onConfirmCallback = null;
    },
    confirmDelete() {
      if (this.deleteConfig.onConfirmCallback) {
        this.deleteConfig.onConfirmCallback();
      }
      this.hideDeleteConfirmation();
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    deleteLanguage(language) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Language'),
        message: this.$t('This action cannot be undone.'),
        itemName: language.name,
        itemType: 'language',
        deleteUrl: route('languages.delete', language.id),
        deleteMethod: 'delete'
      });
    },
      formatDate(date) {
          if (!date) return 'N/A'

          // Handle invalid dates gracefully
          const momentDate = moment(date)
          if (!momentDate.isValid()) {
              console.warn('Invalid date format:', date)
              return 'Invalid Date'
          }

          return momentDate.format('MMM DD, YYYY')
      }
    },

}
</script>
