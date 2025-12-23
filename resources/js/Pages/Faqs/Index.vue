<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
        <Head :title="$t(title)" />

        <!-- Enhanced Header -->
        <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('FAQs Management') }}</h1>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                {{ $t('Manage frequently asked questions and their status') }}
                            </p>
                        </div>
                        <Link
                            :href="route('faqs.create')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm hover:shadow-md"
                        >
                            <Plus class="w-4 h-4" />
                            {{ $t('Create New FAQ') }}
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
                                :placeholder="$t('Search FAQs...')"
                            />
                        </div>
                        <div class="flex gap-2">
                            <select
                                v-model="form.status"
                                class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                @change="search"
                            >
                                <option value="">{{ $t('All Status') }}</option>
                                <option value="1">{{ $t('Active') }}</option>
                                <option value="0">{{ $t('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQs Table -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">
                                {{ $t('FAQ') }}
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
                            v-for="faq in faqs.data"
                            :key="faq.id"
                            class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-150"
                        >
                            <td class="px-6 py-4">
                                <Link
                                    :href="route('faqs.edit', faq.id)"
                                    class="group flex items-center gap-3"
                                >
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <HelpCircle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ faq.name }}
                                        </p>
                                        <p v-if="faq.description" class="text-sm text-slate-500 dark:text-slate-400 truncate">
                                            {{ faq.description }}
                                        </p>
                                    </div>
                                </Link>
                            </td>
                            <td class="px-6 py-4">
                  <span
                      :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      faq.status
                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                    ]"
                  >
                    <div
                        :class="[
                        'w-1.5 h-1.5 rounded-full mr-1.5',
                        faq.status ? 'bg-green-400' : 'bg-red-400'
                      ]"
                    ></div>
                    {{ faq.status ? $t('Active') : $t('Inactive') }}
                  </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ formatDate(faq.created_at) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="route('faqs.edit', faq.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors duration-150"
                                    >
                                        <Edit class="w-4 h-4" />
                                        {{ $t('Edit') }}
                                    </Link>
                                    <button
                                        @click="deleteFaq(faq)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                        {{ $t('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="faqs.data.length === 0" class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <HelpCircle class="w-8 h-8 text-slate-400 dark:text-slate-500" />
                    </div>
                    <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No FAQs found') }}</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">{{ $t('Get started by creating your first FAQ.') }}</p>
                    <Link
                        :href="route('faqs.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                    >
                        <Plus class="w-4 h-4" />
                        {{ $t('Create FAQ') }}
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <Pagination :links="faqs.links" />
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
    HelpCircle,
    Edit,
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
        HelpCircle,
        Edit,
        Trash2,
        DeleteConfirmation,
    },
    props: {
        faqs: Object,
        filters: Object,
        title: String
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                status: this.filters.status || '',
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
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.search()
            }, 300)
        }
    },
    methods: {
        search: throttle(function () {
            this.$inertia.get(route('faqs'), pickBy(this.form), { preserveState: true })
        }, 300),
        reset() {
            this.form = mapValues(this.form, () => null)
            this.$inertia.get(route('faqs'))
        },
        deleteFaq(faq) {
            this.showDeleteConfirmation({
                title: this.$t('Delete FAQ'),
                message: this.$t('This action cannot be undone.'),
                itemName: faq.question,
                itemType: 'faq',
                deleteUrl: route('faqs.destroy', faq.id),
                deleteMethod: 'delete'
            });
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
        formatDate(date) {
            if (!date) return 'N/A'

            // Handle invalid dates gracefully
            const momentDate = moment(date)
            if (!momentDate.isValid()) {
                console.warn('Invalid date format:', date)
                return 'Invalid Date'
            }

            return momentDate.format('MMM DD, YYYY')
        },

    }
}
</script>
