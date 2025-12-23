<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Priorities Management') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $t('Manage ticket priority levels') }}</p>
            </div>
            <Link
              :href="route('priorities.create')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
            >
              <Plus class="w-4 h-4" />
              {{ $t('Create Priority') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search Section -->
      <div class="mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
          <SearchInput
            v-model="form.search"
            :placeholder="$t('Search priorities...')"
            @reset="reset"
            class="w-full max-w-md"
          />
        </div>
      </div>

      <!-- Priorities Table -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Name') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Created') }}
                </th>
                <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="priority in priorities.data" :key="priority.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mr-3">
                      <AlertTriangle class="w-4 h-4 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ priority.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(priority.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('priorities.edit', priority.id)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </Link>
                    <button
                      @click="deletePriority(priority)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Trash2 class="w-4 h-4" />
                      {{ $t('Delete') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="priorities.data.length === 0">
                <td colspan="3" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <AlertTriangle class="w-12 h-12 text-slate-400 dark:text-slate-500 mb-4" />
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No priorities found') }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-4">{{ $t('Get started by creating your first priority') }}</p>
                    <Link
                      :href="route('priorities.create')"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium"
                    >
                      <Plus class="w-4 h-4" />
                      {{ $t('Create Priority') }}
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-8">
        <Pagination :links="priorities.links" />
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
  </div>


  </template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import Layout from '@/Shared/Layout.vue'
import SearchInput from '@/Shared/SearchInput.vue'
import Pagination from '@/Shared/Pagination.vue'
import {
  Plus,
  AlertTriangle,
  Edit,
  Trash2
} from 'lucide-vue-next'
import moment from 'moment'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

export default {
  metaInfo: { title: 'Priorities' },
  layout: Layout,
  components: {
    Link,
    Head,
    SearchInput,
    Pagination,
    Plus,
    AlertTriangle,
    Edit,
    Trash2,
    DeleteConfirmation,
  },
    props: {
        title: String,
        filters: Object,
        priorities: Object,
    },
  data() {
    return {
        form: {
        search: this.filters.search,
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
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('priorities'), pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
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
    deletePriority(priority) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Priority'),
        message: this.$t('This action cannot be undone.'),
        itemName: priority.name,
        itemType: 'priority',
        deleteUrl: route('priorities.destroy', priority.id),
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
