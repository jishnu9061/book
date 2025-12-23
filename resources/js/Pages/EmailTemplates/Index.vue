<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $t('Email Templates Management') }}</h1>
              <p class="mt-2 text-slate-600 dark:text-slate-400">{{ $t('Manage email templates and notifications') }}</p>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 dark:from-teal-900/30 dark:to-teal-800/30 rounded-xl flex items-center justify-center">
                <Mail class="w-6 h-6 text-teal-600 dark:text-teal-400" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search Section -->
      <div class="mb-8">
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
          <div class="w-full sm:w-96">
            <SearchInput
              v-model="form.search"
              :placeholder="$t('Search templates...')"
              @reset="reset"
            />
          </div>
        </div>
      </div>

      <!-- Templates Table -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Name') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Slug') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Details') }}
                </th>
                <th class="px-6 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Created') }}
                </th>
                <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                  {{ $t('Actions') }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
              <tr v-for="template in templates.data" :key="template.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-3">
                      <Mail class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ template.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                    {{ template.slug }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate">
                    {{ template.details }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(template.created_at) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link
                      :href="route('templates.edit', template.id)"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </Link>
                  </div>
                </td>
              </tr>
              <tr v-if="templates.data.length === 0">
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <Mail class="w-12 h-12 text-slate-400 dark:text-slate-500 mb-4" />
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No templates found') }}</h3>
                    <p class="text-slate-500 dark:text-slate-400">{{ $t('Email templates are managed by the system') }}</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-8">
        <Pagination :links="templates.links" />
      </div>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import SearchInput from '@/Shared/SearchInput.vue'
import Pagination from '@/Shared/Pagination.vue'
import {
  Mail,
  Edit
} from 'lucide-vue-next'
import moment from 'moment'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

export default {
  metaInfo: { title: 'Email Templates' },
  layout: Layout,
  components: {
    Link,
    Head,
    SearchInput,
    Pagination,
    Mail,
    Edit,
  },
  props: {
    title: String,
    filters: Object,
    templates: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('templates'), pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
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
}
</script>
