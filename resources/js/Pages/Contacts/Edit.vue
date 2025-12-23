<template>
  <Layout>
    <Head :title="title" />

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
      <div class="px-6 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Contact Management') }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $t('Update contact information and details') }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <Link
              :href="route('contacts')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4 mr-2" />
              {{ $t('Back to Contacts') }}
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
      <form @submit.prevent="update">
        <div class="p-8 space-y-6">
          <!-- Personal Information -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
              <text-input
                v-model="form.first_name"
                :error="form.errors.first_name"
                :label="$t('First Name')"
                :placeholder="$t('Enter first name')"
                class="w-full"
              />
            </div>
            <div>
              <text-input
                v-model="form.last_name"
                :error="form.errors.last_name"
                :label="$t('Last Name')"
                :placeholder="$t('Enter last name')"
                class="w-full"
              />
            </div>
            <div>
              <text-input
                v-model="form.title"
                :error="form.errors.title"
                :label="$t('Job Title')"
                :placeholder="$t('Enter job title')"
                class="w-full"
              />
            </div>
          </div>

          <!-- Organization & Contact -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <select-input
                v-model="form.organization_id"
                :error="form.errors.organization_id"
                :label="$t('Organization')"
                class="w-full"
              >
                <option :value="null">{{ $t('Select organization') }}</option>
                <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
              </select-input>
            </div>
            <div>
              <text-input
                v-model="form.email"
                :error="form.errors.email"
                :label="$t('Email Address')"
                :placeholder="$t('Enter email address')"
                type="email"
                class="w-full"
              />
            </div>
          </div>

          <!-- Contact Information -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <text-input
                v-model="form.phone"
                :error="form.errors.phone"
                :label="$t('Phone Number')"
                :placeholder="$t('Enter phone number')"
                class="w-full"
              />
            </div>
            <div>
              <text-input
                v-model="form.city"
                :error="form.errors.city"
                :label="$t('City')"
                :placeholder="$t('Enter city')"
                class="w-full"
              />
            </div>
          </div>

          <!-- Location Information -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <text-input
                v-model="form.address"
                :error="form.errors.address"
                :label="$t('Address')"
                :placeholder="$t('Enter full address')"
                class="w-full"
              />
            </div>
            <div>
              <select-input
                v-model="form.country"
                :error="form.errors.country"
                :label="$t('Country')"
                class="w-full"
              >
                <option :value="null">{{ $t('Select country') }}</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ $t(c.name) }}</option>
              </select-input>
            </div>
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
            {{ $t('Delete Contact') }}
          </button>
          <loading-button
            :loading="form.processing"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            type="submit"
          >
            <Save class="w-4 h-4 mr-2" />
            {{ $t('Update Contact') }}
          </loading-button>
        </div>
      </form>
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
  </Layout>


  </template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { ArrowLeft, Trash2, Save } from 'lucide-vue-next'

export default {
  components: {
    Head,
      Layout,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    ArrowLeft,
    Trash2,
    Save,
    DeleteConfirmation,
  },
    props: {
      title: String,
        countries: Object,
        cities: Object,
        contact: Object,
        organizations: Object,
    },
  remember: 'form',
  data() {
    return {form: this.$inertia.form({
        first_name: this.contact.first_name,
        last_name: this.contact.last_name,
        title: this.contact.title,
        organization_id: this.contact.organization_id,
        email: this.contact.email,
        phone: this.contact.phone,
        address: this.contact.address,
        city: this.contact.city,
        country: this.contact.country,
      // Delete confirmation
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
      this.form.put(route('contacts.update', this.contact.id))
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
    destroy() {
      this.showDeleteConfirmation({
        title: 'Delete Contact',
        message: 'This action cannot be undone.',
        itemName: `${this.contact.first_name} ${this.contact.last_name}`.trim() || 'this contact',
        itemType: 'contact',
        deleteUrl: this.route('contacts.destroy', this.contact.id),
        deleteMethod: 'delete'
      });
    },
    restore() {
      if (confirm('Are you sure you want to restore this contact?')) {
        this.$inertia.put(route('contacts.restore', this.contact.id))
      }
    }
  }
}
</script>
