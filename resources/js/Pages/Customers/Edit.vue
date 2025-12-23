<template>
  <Layout>
    <Head :title="title" />

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
      <div class="px-6 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Customer Management') }}</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ $t('Update customer information and profile') }}</p>
          </div>
          <div class="flex items-center space-x-3">
            <Link
              :href="route('customers')"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            >
              <ArrowLeft class="w-4 h-4 mr-2" />
              {{ $t('Back to Customers') }}
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
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
            <div>
              <select-input
                v-model="form.country_id"
                :error="form.errors.country_id"
                :label="$t('Country')"
                class="w-full"
              >
                <option :value="null">{{ $t('Select country') }}</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select-input>
            </div>
          </div>

          <!-- Address Information -->
          <div>
            <text-input
              v-model="form.address"
              :error="form.errors.address"
              :label="$t('Address')"
              :placeholder="$t('Enter full address')"
              class="w-full"
            />
          </div>

          <!-- Security & Profile -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
              <text-input
                v-model="form.password"
                :error="form.errors.password"
                type="password"
                autocomplete="new-password"
                :label="$t('New Password')"
                :placeholder="$t('Leave blank to keep current password')"
                class="w-full"
              />
            </div>
            <div>
              <file-input
                v-model="form.photo_path"
                :error="form.errors.photo_path"
                type="file"
                accept="image/*"
                :label="$t('Profile Photo')"
                class="w-full"
              />
            </div>
          </div>

          <!-- Photo Preview -->
          <div v-if="user.photo_path" class="flex justify-start">
            <div class="relative">
              <img :src="user.photo_path" class="w-24 h-24 object-cover rounded-full border-2 border-slate-200 dark:border-slate-600" />
              <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200 rounded-full flex items-center justify-center">
                <Eye class="w-6 h-6 text-white opacity-0 hover:opacity-100 transition-opacity duration-200" />
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-8 py-6 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600 flex items-center justify-between">
          <button
            v-if="user.id !== auth.user.id && user_access.customer.delete"
            type="button"
            @click="destroy"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
          >
            <Trash2 class="w-4 h-4 mr-2" />
            {{ $t('Delete Customer') }}
          </button>
          <div v-else></div>
          <loading-button
            :loading="form.processing"
            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            type="submit"
          >
            <Save class="w-4 h-4 mr-2" />
            {{ $t('Update Customer') }}
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
  </Layout>
</template>



<script>
import { Head, Link } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import FileInput from '@/Shared/FileInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import { ArrowLeft, Trash2, Save, Eye } from 'lucide-vue-next'

export default {
  components: {
    FileInput,
      Layout,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    ArrowLeft,
    Trash2,
    Save,
    Eye,
    DeleteConfirmation,
  },
  remember: 'form',
    props: {
        user: Object,
        auth: Object,
        countries: Array,
        cities: Array,
        title: String,
    },
  data() {
    return {
      user_access: this.$page.props.auth?.user?.access || {},
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
      },
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        phone: this.user.phone,
        city: this.user.city,
        address: this.user.address,
        country_id: this.user.country_id,
        password: '',
          photo_path: null
      }),
    }
  },
  created() {
    // this.setDefaultValue(this.countries, 'country_id', 'United States')
  },
  methods: {
    setDefaultValue(arr, key, value){
      const find = arr.find(i=>i.name.match(new RegExp(value + ".*")))
      if(find){
        this.form[key] = find['id']
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
      },
      destroy() {
          this.showDeleteConfirmation({
              title: 'Delete Customer',
              message: 'This action cannot be undone.',
              itemName: this.user.name,
              itemType: 'customer',
              deleteUrl: this.route('customers.destroy', this.customer.id),
              deleteMethod: 'delete'
          });
      },
      update() {
          this.form.post(this.route('customers.update', this.user.id), {
              onSuccess: () => this.form.reset('password', 'photo'),
          })
      },
      restore() {
          if (confirm('Are you sure you want to restore this user?')) {
              this.$inertia.put(this.route('customers.restore', this.user.id))
          }
      },
    },
}
</script>
