<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Edit Department') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $t('Update department details and settings') }}</p>
            </div>
            <Link
              :href="route('departments')"
              class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4" />
              {{ $t('Back to Departments') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Department Information -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form @submit.prevent="update">
              <!-- Form Fields -->
              <div class="p-8 space-y-6">
                <!-- Department Name -->
                <div>
                  <TextInput
                    v-model="form.name"
                    :error="form.errors.name"
                    :label="$t('Department Name')"
                    :placeholder="$t('Enter department name')"
                    required
                    class="w-full"
                  />
                </div>

                <!-- Department Info -->
                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4">
                  <div class="flex items-start gap-3">
                    <Info class="w-5 h-5 text-blue-500 mt-0.5" />
                    <div>
                      <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-1">
                        {{ $t('Department Information') }}
                      </h4>
                      <p class="text-sm text-slate-600 dark:text-slate-400">
                        {{ $t('Currently, departments only support basic name configuration. Additional features like description, email, and settings can be added by extending the database schema.') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="px-8 py-4 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600 flex items-center justify-between">
                <button
                  type="button"
                  @click="destroy"
                  class="inline-flex items-center gap-2 px-4 py-2 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 font-medium"
                >
                  <Trash2 class="w-4 h-4" />
                  {{ $t('Delete') }}
                </button>

                <div class="flex items-center gap-3">
                  <Link
                    :href="route('departments')"
                    class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors duration-200 font-medium"
                  >
                    {{ $t('Cancel') }}
                  </Link>
                  <LoadingButton
                    :loading="form.processing"
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm hover:shadow-md"
                  >
                    <Save class="w-4 h-4" />
                    {{ $t('Update') }}
                  </LoadingButton>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Department Stats & Info -->
        <div class="space-y-6">
          <!-- Department Stats -->
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Department Information') }}</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Department ID') }}</span>
                <span class="text-lg font-semibold text-slate-900 dark:text-white">{{ department.id }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Name') }}</span>
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ department.name }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Status') }}</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                  {{ $t('Active') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Quick Actions') }}</h3>
            <div class="space-y-3">
              <Link
                :href="route('users')"
                class="flex items-center gap-3 p-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg transition-colors duration-200"
              >
                <Users class="w-5 h-5" />
                {{ $t('View Agents') }}
              </Link>
              <Link
                :href="route('categories')"
                class="flex items-center gap-3 p-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg transition-colors duration-200"
              >
                <Tag class="w-5 h-5" />
                {{ $t('Manage Categories') }}
              </Link>
              <Link
                :href="route('tickets')"
                class="flex items-center gap-3 p-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg transition-colors duration-200"
              >
                <Ticket class="w-5 h-5" />
                {{ $t('View Tickets') }}
              </Link>
            </div>
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
  </div>
</template>

<script>
import Layout from '@/Shared/Layout.vue'
import { Link, Head } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import {
  ArrowLeft,
  Trash2,
  Save,
  Users,
  Tag,
  Ticket,
  Info
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  layout: Layout,
  metaInfo() {
    return { title: this.form.name || 'Edit Department' }
  },
  components: {
    Layout,
    LoadingButton,
    TextInput,
    Link,
    Head,
    DeleteConfirmation,
    ArrowLeft,
    Trash2,
    Save,
    Users,
    Tag,
    Ticket,
    Info
  },
  props: {
    department: Object,
    title: String,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.department?.name || '',
      }),
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
    update() {
      this.form.put(this.route('departments.update', this.department.id), {
        onSuccess: () => {
          // Optional: Show success message
        },
        onError: (errors) => {
          console.error('Update error:', errors)
        }
      })
    },
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
    destroy() {
      this.showDeleteConfirmation({
        title: this.$t('Delete Department'),
        message: this.$t('This action cannot be undone. This will also reassign all tickets and remove all agents from this department.'),
        itemName: this.department.name,
        itemType: 'department',
        deleteUrl: this.route('departments.destroy', this.department.id),
        deleteMethod: 'delete',
        onConfirmCallback: () => {
          this.$inertia.delete(this.route('departments.destroy', this.department.id), {
            onSuccess: () => {
              this.$inertia.visit(this.route('departments'))
            },
            onError: (errors) => {
              console.error('Delete error:', errors)
            }
          })
        }
      })
    },
    restore() {
      if (confirm(this.$t('Are you sure you want to restore this department?'))) {
        this.$inertia.put(this.route('departments.restore', this.department.id))
      }
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
