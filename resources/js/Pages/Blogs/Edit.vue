<template>
  <Layout>
      <Head :title="$t(title)" />

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
      <div class="px-6 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Blog Post Management') }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $t('Update blog post information and content') }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <Link
              :href="route('posts')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4 mr-2" />
              {{ $t('Back to Posts') }}
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
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
              <text-input
                v-model="form.title"
                :error="form.errors.title"
                :label="$t('Post Title')"
                :placeholder="$t('Enter post title')"
                class="w-full"
              />
            </div>
            <div>
              <select-input
                v-model="form.type_id"
                :error="form.errors.type_id"
                :label="$t('Post Type')"
                :required="true"
                class="w-full"
              >
                <option :value="null">{{ $t('Select type') }}</option>
                <option v-for="s in types" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select-input>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <select-input
                v-model="form.is_active"
                :error="form.errors.is_active"
                :label="$t('Status')"
                :required="true"
                class="w-full"
              >
                <option :value="1">{{ $t('Published') }}</option>
                <option :value="0">{{ $t('Draft') }}</option>
              </select-input>
            </div>
            <div>
              <file-input
                v-model="form.image"
                :error="form.errors.image"
                type="file"
                accept="image/*"
                :label="$t('Feature Image')"
                class="w-full"
              />
            </div>
          </div>

          <!-- Image Preview -->
          <div v-if="post.image" class="flex justify-start">
            <div class="relative">
              <img :src="post.image" class="w-32 h-32 object-cover rounded-lg border border-slate-200 dark:border-slate-600" />
              <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                <Eye class="w-6 h-6 text-white opacity-0 hover:opacity-100 transition-opacity duration-200" />
              </div>
            </div>
          </div>

          <!-- Content Section -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
              {{ $t('Post Content') }}
            </label>
            <RichEditor v-model="form.details" class="mt-1" />
            <div v-if="form.errors.details" class="text-red-600 text-sm mt-1">{{ form.errors.details }}</div>
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
            {{ $t('Delete Post') }}
          </button>
          <loading-button
            :loading="form.processing"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            type="submit"
          >
            <Save class="w-4 h-4 mr-2" />
            {{ $t('Update Post') }}
          </loading-button>
        </div>
      </form>
    </div>
  </Layout>

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
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import TextareaInput from '@/Shared/TextareaInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { Link, Head } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import FileInput from '@/Shared/FileInput.vue'

import RichEditor from '@/Shared/RichEditor.vue';
import { ArrowLeft, Trash2, Save, Eye } from 'lucide-vue-next';

export default {
  components: {
    Layout,
    LoadingButton,
    SelectInput,
    TextareaInput,
    TextInput,
    Link,
    Head,
    FileInput,
    RichEditor,
    ArrowLeft,
    Trash2,
    Save,
    Eye,
    DeleteConfirmation,
  },
  props: {
    title: String,
    types: Array,
      post: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        title: this.post?.title,
        type_id: this.post.type_id,
        is_active: this.post.is_active,
        image: null,
        details: this.post.details,
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
  methods: {
    update() {
      this.form.post(this.route('posts.update', this.post.id), {
        onSuccess: () => this.form.reset('image'),
      })
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
      created(){
        console.log(this.title)
      },
    destroy() {
      this.showDeleteConfirmation({
        title: 'Delete Post',
        message: 'This action cannot be undone.',
        itemName: this.post.title,
        itemType: 'post',
        deleteUrl: this.route('posts.destroy', this.post.id),
        deleteMethod: 'delete'
      });
    }
  }
}
</script>
