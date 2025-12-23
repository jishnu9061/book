<template>
    <Head :title="title" />

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
      <div class="px-6 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('FAQ Management') }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $t('Update FAQ information and content') }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <Link
              :href="route('faqs')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4 mr-2" />
              {{ $t('Back to FAQs') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
      <form @submit.prevent="update">
        <div class="p-8 space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <text-input
                v-model="form.name"
                :error="form.errors.name"
                :label="$t('FAQ Title')"
                :placeholder="$t('Enter FAQ title')"
                class="w-full"
              />
            </div>
            <div>
              <select-input
                v-model="form.status"
                :error="form.errors.status"
                :label="$t('Status')"
                class="w-full"
              >
                <option :value="1">{{ $t('Active') }}</option>
                <option :value="0">{{ $t('Inactive') }}</option>
              </select-input>
            </div>
          </div>

          <!-- Content Section -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('FAQ Content') }}
            </label>
            <textarea-input
              id="faqDetails"
              name="content"
              v-model="form.details"
              :error="form.errors.details"
              :rows="8"
              :placeholder="$t('Enter detailed FAQ content')"
              class="w-full"
            />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-8 py-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600 flex items-center justify-between">
          <button
            type="button"
            @click="destroy"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
          >
            <Trash2 class="w-4 h-4 mr-2" />
            {{ $t('Delete FAQ') }}
          </button>
          <loading-button
            :loading="form.processing"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            type="submit"
          >
            <Save class="w-4 h-4 mr-2" />
            {{ $t('Update FAQ') }}
          </loading-button>
        </div>
      </form>
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
import SelectInput from '@/Shared/SelectInput.vue'
import TextareaInput from '@/Shared/TextareaInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { Link, Head } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import { ArrowLeft, Trash2, Save } from 'lucide-vue-next'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    LoadingButton,
    SelectInput,
    TextareaInput,
    TextInput,
    Link,
    Head,
    ArrowLeft,
    Trash2,
    Save,
    DeleteConfirmation
  },
  layout: Layout,
  props: {
    faq: Object,
    title: String,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.faq.name,
        status: this.faq.status,
        details: this.faq.details,
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
      this.form.put(this.route('faqs.update', this.faq.id))
    },
    destroy() {
      this.showDeleteConfirmation({
        title: 'Delete FAQ',
        message: 'This action cannot be undone.',
        itemName: this.faq.name,
        itemType: 'faq',
        deleteUrl: this.route('faqs.destroy', this.faq.id),
        deleteMethod: 'delete'
      });
    },
    restore() {
      if (confirm('Are you sure you want to restore this faq?')) {
        this.$inertia.put(this.route('faqs.restore', this.faq.id))
      }
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
    }
  }
}
</script>
