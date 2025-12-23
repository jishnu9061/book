<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Edit Category') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $t('Update category details and settings') }}</p>
            </div>
            <Link
              :href="route('categories')"
              class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4" />
              {{ $t('Back to Categories') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <form @submit.prevent="update">
          <!-- Form Fields -->
          <div class="p-8 space-y-6">
            <!-- Category Name -->
            <div>
              <TextInput
                v-model="form.name"
                :error="form.errors.name"
                :label="$t('Category Name')"
                :placeholder="$t('Enter category name')"
                required
                class="w-full"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Description') }}
              </label>
              <textarea
                v-model="form.description"
                :placeholder="$t('Enter category description (optional)')"
                rows="3"
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400"
              ></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ form.errors.description }}
              </p>
            </div>

            <!-- Department and Parent Selection -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Department Selection -->
              <div v-if="!form.parent_id">
                <SelectInput
                  v-model="form.department_id"
                  :error="form.errors.department_id"
                  :label="$t('Department')"
                  :placeholder="$t('Select department')"
                  class="w-full"
                >
                  <option :value="null">{{ $t('No Department') }}</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </SelectInput>
              </div>

              <!-- Parent Category Selection -->
              <div v-if="!form.department_id">
                <SelectInput
                  v-model="form.parent_id"
                  :error="form.errors.parent_id"
                  :label="$t('Parent Category')"
                  :placeholder="$t('Select parent category')"
                  class="w-full"
                >
                  <option :value="null">{{ $t('No Parent') }}</option>
                  <option v-for="cat in availableParentCategories" :key="cat.id" :value="cat.id">
                    {{ cat.name }}
                  </option>
                </SelectInput>
              </div>
            </div>

            <!-- Color Selection -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                {{ $t('Category Color') }}
              </label>
              <div class="flex items-center gap-4">
                <!-- Color Preview -->
                <div 
                  class="w-12 h-12 rounded-lg border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center"
                  :style="{ backgroundColor: form.color || '#6366f1' }"
                >
                  <Tag class="w-6 h-6 text-white" />
                </div>
                
                <!-- Color Input -->
                <div class="flex-1">
                  <input
                    v-model="form.color"
                    type="color"
                    class="w-full h-12 rounded-lg border border-slate-300 dark:border-slate-600 cursor-pointer"
                  />
                  <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    {{ $t('Choose a color to represent this category') }}
                  </p>
                </div>
              </div>
              <p v-if="form.errors.color" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ form.errors.color }}
              </p>
            </div>

            <!-- Category Type Info -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <Info class="w-5 h-5 text-blue-500 mt-0.5" />
                <div>
                  <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-1">
                    {{ $t('Category Type') }}
                  </h4>
                  <p class="text-sm text-slate-600 dark:text-slate-400">
                    <span v-if="form.parent_id">
                      {{ $t('This is a subcategory. It will be grouped under its parent category.') }}
                    </span>
                    <span v-else-if="form.department_id">
                      {{ $t('This is a department-specific category. It will only be available for the selected department.') }}
                    </span>
                    <span v-else>
                      {{ $t('This is a general category. It will be available for all departments.') }}
                    </span>
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
              {{ $t('Delete Category') }}
            </button>
            
            <div class="flex items-center gap-3">
              <Link
                :href="route('categories')"
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
                {{ $t('Update Category') }}
              </LoadingButton>
            </div>
          </div>
        </form>
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
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { Link, Head } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import {
  ArrowLeft,
  Tag,
  Trash2,
  Save,
  Info
} from 'lucide-vue-next'

export default {
  layout: Layout,
  metaInfo() {
    return { title: this.form.name || 'Edit Category' }
  },
  components: {
    Layout,
    SelectInput,
    LoadingButton,
    TextInput,
    Link,
    Head,
    DeleteConfirmation,
    ArrowLeft,
    Tag,
    Trash2,
    Save,
    Info
  },
  props: {
    title: String,
    category: Object,
    categories: { 
      type: Array, 
      default: () => [] 
    },
    departments: { 
      type: Array, 
      default: () => [] 
    },
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.category?.name || '',
        description: this.category?.description || '',
        color: this.category?.color || '#6366f1',
        department_id: this.category?.department_id || null,
        parent_id: this.category?.parent_id || null,
      }),
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
    availableParentCategories() {
      if (!this.categories) return []
      
      // Filter out the current category and its subcategories to prevent circular references
      return this.categories.filter(cat => 
        cat.id !== this.category?.id && 
        !cat.parent_id && 
        cat.department_id === this.form.department_id
      )
    }
  },
  methods: {
    update() {
      this.form.put(this.route('categories.update', this.category.id), {
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
        title: this.$t('Delete Category'),
        message: this.$t('This action cannot be undone. This will also delete all subcategories and reassign their tickets.'),
        itemName: this.category.name,
        itemType: 'category',
        deleteUrl: this.route('categories.destroy', this.category.id),
        deleteMethod: 'delete',
        onConfirmCallback: () => {
          this.$inertia.delete(this.route('categories.destroy', this.category.id), {
            onSuccess: () => {
              this.$inertia.visit(this.route('categories'))
            },
            onError: (errors) => {
              console.error('Delete error:', errors)
            }
          })
        }
      })
    },
    restore() {
      if (confirm(this.$t('Are you sure you want to restore this category?'))) {
        this.$inertia.put(this.route('categories.restore', this.category.id))
      }
    }
  }
}
</script>
