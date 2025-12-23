<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Knowledge Base Management') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Manage your knowledge base articles and documentation') }}
              </p>
            </div>
            <Link
              :href="route('knowledge_base.create')"
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
            >
              <Plus class="w-4 h-4" />
              {{ $t('Create Knowledge Base') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search and Filters -->
      <div class="mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <SearchInput
                v-model="form.search"
                class="w-full"
                @reset="reset"
                :placeholder="$t('Search knowledge base...')"
              />
            </div>
            <div class="flex gap-2">
              <select
                v-model="form.type"
                class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @change="search"
              >
                <option value="">{{ $t('All Types') }}</option>
                <option value="article">{{ $t('Article') }}</option>
                <option value="tutorial">{{ $t('Tutorial') }}</option>
                <option value="guide">{{ $t('Guide') }}</option>
                <option value="faq">{{ $t('FAQ') }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Knowledge Base Table -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Article') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Type') }}
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
                v-for="knowledge in knowledge_base.data"
                :key="knowledge.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <Link
                    :href="route('knowledge_base.edit', knowledge.id)"
                    class="group flex items-center gap-3"
                  >
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                      <BookOpen class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ knowledge.title }}
                      </p>
                      <p v-if="knowledge.description" class="text-sm text-slate-500 dark:text-slate-400 truncate">
                        {{ knowledge.description }}
                      </p>
                    </div>
                  </Link>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      getTypeColor(knowledge.type)
                    ]"
                  >
                    <Tag class="w-3 h-3 mr-1" />
                    {{ knowledge.type || $t('Article') }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(knowledge.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('knowledge_base.edit', knowledge.id)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </Link>
                    <button
                      @click="deleteKnowledge(knowledge)"
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
        <div v-if="knowledge_base.data.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <BookOpen class="w-8 h-8 text-slate-400 dark:text-slate-500" />
          </div>
          <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No knowledge base articles found') }}</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $t('Get started by creating your first knowledge base article.') }}</p>
          <Link
            :href="route('knowledge_base.create')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
          >
            <Plus class="w-4 h-4" />
            {{ $t('Create Article') }}
          </Link>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        <Pagination :links="knowledge_base.links" />
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
import { Head, Link } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import Layout from '@/Shared/Layout.vue'
import SearchInput from '@/Shared/SearchInput.vue'
import Pagination from '@/Shared/Pagination.vue'
import {
  Plus,
  BookOpen,
  Edit,
  Trash2,
  Tag
} from 'lucide-vue-next'
import moment from 'moment'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

export default {
  layout: Layout,
  components: {
    Head,
    Link,
    SearchInput,
    Pagination,
    Plus,
    BookOpen,
    Edit,
    Trash2,
    Tag,
    DeleteConfirmation,
  },
  props: {
    knowledge_base: Object,
    filters: Object,
    title: String
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        type: this.filters.type || '',
      },
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
  methods: {
    search: throttle(function () {
      this.$inertia.get(route('knowledge_base'), pickBy(this.form), { preserveState: true })
    }, 300),
    reset() {
      this.form = mapValues(this.form, () => null)
      this.$inertia.get(route('knowledge_base'))
    },
    deleteKnowledge(knowledge) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Knowledge'),
        message: this.$t('This action cannot be undone.'),
        itemName: knowledge.title,
        itemType: 'knowledge',
        deleteUrl: route('knowledge_base.destroy', knowledge.id),
        deleteMethod: 'delete'
      });
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
    getTypeColor(type) {
      const colors = {
        article: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        tutorial: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        guide: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        faq: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'
      }
      return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
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
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.search()
      }, 300)
    }
  }
}
</script>
