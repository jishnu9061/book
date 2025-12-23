<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Notes Management') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Create and manage your personal notes') }}
              </p>
            </div>
            <button
              @click="addNew"
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
            >
              <Plus class="w-4 h-4" />
              {{ $t('Create New Note') }}
            </button>
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
            v-model="searchFilter.search"
            class="w-full max-w-md"
            :placeholder="$t('Search notes...')"
            :disableReset="true"
          />
        </div>
      </div>

      <!-- Notes Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="note in notes.data"
          :key="note.id"
          class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-md transition-all duration-200 cursor-pointer group"
          @click="updateNote(note)"
        >
          <div class="p-6 h-full flex flex-col">
            <!-- Note Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors truncate">
                  {{ note.name }}
                </h3>
              </div>
              <button
                @click.stop="updateNote(note)"
                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg"
              >
                <Edit class="w-4 h-4" />
              </button>
            </div>

            <!-- Note Content -->
            <div class="flex-1 mb-4">
              <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-6">
                {{ note.details }}
              </p>
            </div>

            <!-- Note Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
              <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <Calendar class="w-3 h-3" />
                <span>{{ formatDate(note.created_at) }}</span>
              </div>
              <div class="flex items-center gap-1">
                <button
                  @click.stop="updateNote(note)"
                  class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors duration-150"
                >
                  <Edit class="w-3 h-3" />
                </button>
                <button
                  @click.stop="destroy(note)"
                  class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors duration-150"
                >
                  <Trash2 class="w-3 h-3" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="notes.data.length === 0" class="text-center py-12">
        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
          <StickyNote class="w-8 h-8 text-slate-400 dark:text-slate-500" />
        </div>
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No notes found') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $t('Get started by creating your first note.') }}</p>
        <button
          @click="addNew"
          class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
        >
          <Plus class="w-4 h-4" />
          {{ $t('Create Note') }}
        </button>
      </div>

      <!-- Pagination -->
      <div class="mt-8">
        <Pagination :links="notes.links" />
      </div>
    </div>

    <!-- Note Form Modal -->
    <div v-if="note_form" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="note_form = false"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="store(update_id)">
            <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full">
                  <h3 class="text-lg leading-6 font-medium text-slate-900 dark:text-white mb-4">
                    {{ update_id ? $t('Edit Note') : $t('Create Note') }}
                  </h3>

                  <div class="space-y-4">
                    <TextInput
                      v-model="form.name"
                      :error="form.errors.name"
                      :label="$t('Note Title')"
                      :placeholder="$t('Enter note title...')"
                      required
                    />

                    <TextareaInput
                      v-model="form.details"
                      :error="form.errors.details"
                      :rows="6"
                      :label="$t('Note Details')"
                      :placeholder="$t('Enter note details...')"
                      required
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
              >
                {{ update_id ? $t('Update Note') : $t('Create Note') }}
              </button>

              <button
                type="button"
                @click="note_form = false"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
              >
                {{ $t('Cancel') }}
              </button>

              <button
                v-if="form.id"
                type="button"
                @click="destroy()"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-red-300 dark:border-red-600 shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
              >
                {{ $t('Delete Note') }}
              </button>
            </div>
          </form>
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
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import SearchInput from '@/Shared/SearchInput.vue'
import TextInput from '@/Shared/TextInput.vue'
import TextareaInput from '@/Shared/TextareaInput.vue'
import Pagination from '@/Shared/Pagination.vue'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import {
  Plus,
  Edit,
  Trash2,
  Calendar,
  StickyNote
} from 'lucide-vue-next'
import moment from 'moment'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'

export default {
  layout: Layout,
  components: {
    Head,
    SearchInput,
    TextInput,
    TextareaInput,
    Pagination,
    DeleteConfirmation,
    Plus,
    Edit,
    Trash2,
    Calendar,
    StickyNote
  },
  props: {
    notes: Object,
    filters: Object,
    title: String
  },
  data() {
    return {
      note_form: false,
      update_id: null,
      searchFilter: {
        search: this.filters.search || ''
      },
      form: {
        name: '',
        details: '',
        id: null,
        errors: {}
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
    addNew() {
      this.form = {
        name: '',
        details: '',
        id: null,
        errors: {}
      }
      this.update_id = null
      this.note_form = true
    },
    updateNote(note) {
      this.form = {
        name: note.name,
        details: note.details,
        id: note.id,
        errors: {}
      }
      this.update_id = note.id
      this.note_form = true
    },
    store(id) {
      if (id) {
        this.$inertia.post(route('notes.save', id), this.form, {
          onSuccess: () => {
            this.note_form = false
            this.form = { name: '', details: '', id: null, errors: {} }
            this.update_id = null
          },
          onError: (errors) => {
            this.form.errors = errors
          }
        })
      } else {
        this.$inertia.post(route('notes.save'), this.form, {
          onSuccess: () => {
            this.note_form = false
            this.form = { name: '', details: '', id: null, errors: {} }
            this.update_id = null
          },
          onError: (errors) => {
            this.form.errors = errors
          }
        })
      }
    },
    destroy(note = null) {
      const noteId = note ? note.id : this.form.id
      const noteName = note ? note.name : this.form.name
      this.showDeleteConfirmation({
        title: this.$t('Delete Note'),
        message: this.$t('This action cannot be undone.'),
        itemName: noteName || 'this note',
        itemType: 'note',
        deleteUrl: route('notes.delete', noteId),
        deleteMethod: 'delete'
      });
    },
    showDeleteConfirmation(config) {
      this.deleteConfig = {
        ...this.deleteConfig,
        ...config
      };
      this.showDeleteModal = true;
    },
    hideDeleteConfirmation() {
      this.showDeleteModal = false;
    },
    confirmDelete() {
      this.hideDeleteConfirmation();
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
    'searchFilter.search': throttle(function (value) {
      this.$inertia.get(route('notes'), pickBy(this.searchFilter), { preserveState: true })
    }, 300)
  }
}
</script>

<style scoped>
.line-clamp-6 {
  display: -webkit-box;
  -webkit-line-clamp: 6;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
