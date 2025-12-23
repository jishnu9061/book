<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t('Customers')" />
    
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Customers Management') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Manage your customer accounts and information') }}
              </p>
            </div>
            <Link 
              :href="route('customers.create')" 
              class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
            >
              <Plus class="w-4 h-4" />
              {{ $t('Create New Customer') }}
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
                :placeholder="$t('Search customers...')"
              />
            </div>
            <div class="flex gap-2">
              <select 
                v-model="form.city" 
                class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @change="search"
              >
                <option value="">{{ $t('All Cities') }}</option>
                <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Customers Table -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Customer') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Contact Info') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Location') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Status') }}
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
                v-for="user in users.data" 
                :key="user.id" 
                class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150"
              >
                <td class="px-6 py-4">
                  <Link 
                    :href="route('customers.edit', user.id)"
                    class="group flex items-center gap-3"
                  >
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <img 
                          v-if="user.photo" 
                          :src="user.photo" 
                          :alt="user.name"
                          class="w-full h-full object-cover"
                        />
                        <User v-else class="w-5 h-5 text-slate-400 dark:text-slate-500" />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ user.name }}
                      </p>
                      <p v-if="user.company" class="text-sm text-slate-500 dark:text-slate-400">
                        {{ user.company }}
                      </p>
                    </div>
                  </Link>
                </td>
                <td class="px-6 py-4">
                  <div class="space-y-1">
                    <div class="flex items-center gap-2 text-sm text-slate-900 dark:text-white">
                      <Mail class="w-4 h-4 text-slate-400" />
                      <a :href="`mailto:${user.email}`" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        {{ user.email }}
                      </a>
                    </div>
                    <div v-if="user.phone" class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                      <Phone class="w-4 h-4" />
                      <a :href="`tel:${user.phone}`" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        {{ user.phone }}
                      </a>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <MapPin class="w-4 h-4" />
                    <span>{{ user.city || $t('Not specified') }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span 
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      user.email_verified_at 
                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' 
                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                    ]"
                  >
                    <div 
                      :class="[
                        'w-1.5 h-1.5 rounded-full mr-1.5',
                        user.email_verified_at ? 'bg-green-400' : 'bg-yellow-400'
                      ]"
                    ></div>
                    {{ user.email_verified_at ? $t('Verified') : $t('Pending') }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link 
                      :href="route('customers.edit', user.id)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </Link>
                    <button 
                      @click="deleteCustomer(user)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Trash2 class="w-4 h-4" />
                      {{ $t('Delete') }}
                    </button>
                    <button 
                      @click="viewTickets(user)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Ticket class="w-4 h-4" />
                      {{ $t('Tickets') }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="users.data.length === 0" class="text-center py-12">
          <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <Users class="w-8 h-8 text-slate-400 dark:text-slate-500" />
          </div>
          <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No customers found') }}</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $t('Get started by creating your first customer account.') }}</p>
          <Link 
            :href="route('customers.create')" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
          >
            <Plus class="w-4 h-4" />
            {{ $t('Create Customer') }}
          </Link>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        <Pagination :links="users.links" />
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
  User, 
  Edit, 
  Mail, 
  Phone, 
  MapPin, 
  Ticket,
  Users,
  Trash2
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
    User,
    Edit,
    Mail,
    Phone,
    MapPin,
    Ticket,
    Users,
    Trash2,
    DeleteConfirmation,
  },
  props: {
    users: Object,
    filters: Object,
    cities: Array,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        city: this.filters.city || '',
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
      this.$inertia.get(route('customers'), pickBy(this.form), { preserveState: true })
    }, 300),
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
    reset() {
      this.form = mapValues(this.form, () => null)
      this.$inertia.get(route('customers'))
    },
    viewTickets(user) {
      this.$inertia.get(route('tickets'), { customer: user.id })
    },
    deleteCustomer(user) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Customer'),
        message: this.$t('This action cannot be undone.'),
        itemName: user.name || user.email,
        itemType: 'customer',
        deleteUrl: route('customers.destroy', user.id),
        deleteMethod: 'delete'
      });
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