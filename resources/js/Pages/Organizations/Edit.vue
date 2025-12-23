<template>
  <div>
    <Head :title="title" />

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
      <div class="px-6 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Organization Management') }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $t('Update organization information and details') }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <Link
              :href="route('organizations')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4 mr-2" />
              {{ $t('Back to Organizations') }}
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
                :label="$t('Organization Name')"
                :placeholder="$t('Enter organization name')"
                class="w-full"
              />
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
                v-model="form.postal_code"
                :error="form.errors.postal_code"
                :label="$t('Postal Code')"
                :placeholder="$t('Enter postal code')"
                class="w-full"
              />
            </div>
          </div>

          <!-- Address Information -->
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
                v-model="form.region"
                :error="form.errors.region"
                :label="$t('Province/State')"
                :placeholder="$t('Enter province or state')"
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
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
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
            {{ $t('Delete Organization') }}
          </button>
          <loading-button
            :loading="form.processing"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            type="submit"
          >
            <Save class="w-4 h-4 mr-2" />
            {{ $t('Update Organization') }}
          </loading-button>
        </div>
      </form>
    </div>

    <!-- Contacts Section -->
    <div class="mt-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Organization Contacts') }}</h2>
        <Link
          :href="route('contacts.create')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
        >
          <Plus class="w-4 h-4 mr-2" />
          {{ $t('Add Contact') }}
        </Link>
      </div>

      <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Name') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('City') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Phone') }}
                </th>
                <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="contact in organization.contacts" :key="contact.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <Link class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300" :href="route('contacts.edit', contact.id)">
                    {{ contact.name }}
                  </Link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                  {{ contact.city }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                  {{ contact.phone }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link
                    :href="route('contacts.edit', contact.id)"
                    class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                  >
                    <ChevronRight class="w-4 h-4" />
                  </Link>
                </td>
              </tr>
              <tr v-if="organization.contacts.length === 0">
                <td class="px-6 py-8 text-center text-slate-500 dark:text-slate-400" colspan="4">
                  <div class="flex flex-col items-center">
                    <Users class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-4" />
                    <p class="text-lg font-medium">{{ $t('No contacts found') }}</p>
                    <p class="text-sm">{{ $t('Get started by adding a new contact to this organization') }}</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
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
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { ArrowLeft, Trash2, Save, Plus, ChevronRight, Users } from 'lucide-vue-next'

export default {
    layout: Layout,
  components: {
    Head,
    Layout,
    Icon,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    ArrowLeft,
    Trash2,
    Save,
    Plus,
    ChevronRight,
    Users,
    DeleteConfirmation,
  },
    props: {
        countries: Array,
        cities: Array,
        organization: Object,
        title: String,
    },
  remember: 'form',
  data() {
    return {form: this.$inertia.form({
        name: this.organization.name,
        email: this.organization.email,
        phone: this.organization.phone,
        address: this.organization.address,
        city: this.organization.city,
        region: this.organization.region,
        country: this.organization.country,
        postal_code: this.organization.postal_code,
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
      this.form.put(route('organizations.update', this.organization.id))
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
        title: 'Delete Organization',
        message: 'This action cannot be undone.',
        itemName: this.organization.name,
        itemType: 'organization',
        deleteUrl: this.route('organizations.destroy', this.organization.id),
        deleteMethod: 'delete'
      });
    },
      restore() {
          if (confirm('Are you sure you want to restore this organization?')) {
              this.$inertia.put(route('organizations.restore', this.organization.id))
          }
      },
    },
}
</script>
