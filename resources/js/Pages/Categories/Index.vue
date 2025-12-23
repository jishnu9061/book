<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Categories Management') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $t('Manage ticket categories and subcategories') }}</p>
            </div>
            <Link
              :href="route('categories.create')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
            >
              <Plus class="w-4 h-4" />
              {{ $t('Create Category') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search and Filter Section -->
      <div class="mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <SearchInput
              v-model="form.search"
              :placeholder="$t('Search categories...')"
              @reset="reset"
              class="w-full sm:max-w-md"
            />
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
              <span>{{ $t('Total') }}: {{ categories.total || 0 }}</span>
              <span class="text-slate-300 dark:text-slate-600">•</span>
              <span>{{ $t('Showing') }}: {{ categories.data.length }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Categories Table -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Name') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Department') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Parent') }}
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
              <!-- Loading State -->
              <tr v-if="isLoading">
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-slate-500 dark:text-slate-400">{{ $t('Loading categories...') }}</p>
                  </div>
                </td>
              </tr>
              <!-- Categories List -->
              <tr 
                v-else-if="processedCategories.length > 0"
                v-for="category in processedCategories" 
                :key="category.id" 
                class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                      <Tag class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ category.parent ? '— ' : '' }}{{ category.name }}
                      </div>
                      <div v-if="category.description" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        {{ category.description }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    v-if="category.department" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400"
                  >
                    {{ category.department.name }}
                  </span>
                  <span v-else class="text-slate-400 dark:text-slate-500">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    v-if="category.parent" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"
                  >
                    {{ category.parent.name }}
                  </span>
                  <span v-else class="text-slate-400 dark:text-slate-500">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(category.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('categories.edit', category.id)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </Link>
                    <button
                      @click="deleteCategory(category)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Trash2 class="w-4 h-4" />
                      {{ $t('Delete') }}
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Empty State -->
              <tr v-else-if="!isLoading && processedCategories.length === 0">
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <Tag class="w-12 h-12 text-slate-400 dark:text-slate-500 mb-4" />
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No categories found') }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-4">{{ $t('Get started by creating your first category') }}</p>
                    <Link
                      :href="route('categories.create')"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 font-medium"
                    >
                      <Plus class="w-4 h-4" />
                      {{ $t('Create Category') }}
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
        <Pagination :links="categories.links" />
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
  Tag,
  Edit,
  Trash2
} from 'lucide-vue-next'
import moment from 'moment'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

export default {
  metaInfo: { title: 'Categories' },
  layout: Layout,
  components: {
    Link,
    Layout,
    Head,
    SearchInput,
    Pagination,
    Plus,
    Tag,
    Edit,
    Trash2,
    DeleteConfirmation,
  },
  props: {
    filters: Object,
    categories: Object,
    title: String,
  },
  data() {
    return {
      form: {
        search: this.filters?.search || '',
      },
      isLoading: false,
      // Delete confirmation
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
    processedCategories() {
      if (!this.categories?.data) return []
      
      // Get parent categories first
      const parentCategories = this.categories.data.filter(cat => !cat.parent)
      const processedCats = [...parentCategories]
      
      // Add subcategories after their parent
      this.categories.data.forEach(category => {
        if (category.parent) {
          const parentIndex = processedCats.findIndex(cat => cat.id === category.parent.id)
          if (parentIndex > -1) {
            processedCats.splice(parentIndex + 1, 0, category)
          }
        }
      })
      
      return processedCats
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.isLoading = true
        this.$inertia.get(this.route('categories'), pickBy(this.form), { 
          preserveState: true,
          replace: true,
          onFinish: () => {
            this.isLoading = false
          }
        })
      }, 150),
    },
  },
  methods: {
    showDeleteConfirmation(config, onConfirmCallback = null) {
      Object.assign(this.deleteConfig, config)
      this.showDeleteModal = true
      if (onConfirmCallback) {
        this.deleteConfig.onConfirmCallback = onConfirmCallback
      }
    },
    hideDeleteConfirmation() {
      this.showDeleteModal = false
      this.deleteConfig.onConfirmCallback = null
    },
    confirmDelete() {
      if (this.deleteConfig.onConfirmCallback) {
        this.deleteConfig.onConfirmCallback()
      }
      this.hideDeleteConfirmation()
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    deleteCategory(category) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Category'),
        message: this.$t('This action cannot be undone. This will also delete all subcategories and reassign their tickets.'),
        itemName: category.name,
        itemType: 'category',
        deleteUrl: route('categories.destroy', category.id),
        deleteMethod: 'delete',
        onConfirmCallback: () => {
          this.isLoading = true
          this.$inertia.delete(route('categories.destroy', category.id), {
            onFinish: () => {
              this.isLoading = false
            },
            onError: (errors) => {
              console.error('Delete error:', errors)
              this.isLoading = false
            }
          })
        }
      })
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
  }
}
</script>
