<template>
  <div class="conversation-search">
    <!-- Search Header -->
    <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center gap-3">
        <div class="flex-1 relative">
          <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
          <input
            v-model="searchQuery"
            @input="handleSearch"
            @keydown.enter="performSearch"
            type="text"
            class="w-full pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
            :placeholder="$t('Search conversations and messages...')"
          />
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
        <button @click="toggleAdvancedSearch" class="px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
          <Filter class="w-4 h-4 mr-1" />
          {{ $t('Filters') }}
        </button>
      </div>
    </div>

    <!-- Advanced Search Filters -->
    <div v-if="showAdvancedSearch" class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Date Range -->
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Date Range') }}</label>
          <select v-model="filters.dateRange" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
            <option value="">{{ $t('All Time') }}</option>
            <option value="today">{{ $t('Today') }}</option>
            <option value="week">{{ $t('This Week') }}</option>
            <option value="month">{{ $t('This Month') }}</option>
            <option value="custom">{{ $t('Custom Range') }}</option>
          </select>
        </div>

        <!-- Conversation Type -->
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Type') }}</label>
          <select v-model="filters.type" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
            <option value="">{{ $t('All Types') }}</option>
            <option value="internal">{{ $t('Internal') }}</option>
            <option value="customer">{{ $t('Customer') }}</option>
          </select>
        </div>

        <!-- Author -->
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Author') }}</label>
          <select v-model="filters.author" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
            <option value="">{{ $t('All Authors') }}</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select>
        </div>

        <!-- Has Attachments -->
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Attachments') }}</label>
          <select v-model="filters.hasAttachments" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
            <option value="">{{ $t('All Messages') }}</option>
            <option value="true">{{ $t('With Attachments') }}</option>
            <option value="false">{{ $t('Without Attachments') }}</option>
          </select>
        </div>
      </div>

      <!-- Custom Date Range -->
      <div v-if="filters.dateRange === 'custom'" class="mt-4 grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('From Date') }}</label>
          <input v-model="filters.fromDate" type="date" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('To Date') }}</label>
          <input v-model="filters.toDate" type="date" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white" />
        </div>
      </div>

      <!-- Filter Actions -->
      <div class="mt-4 flex justify-end gap-2">
        <button @click="clearFilters" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
          {{ $t('Clear Filters') }}
        </button>
        <button @click="applyFilters" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
          {{ $t('Apply Filters') }}
        </button>
      </div>
    </div>

    <!-- Search Results -->
    <div class="flex-1 overflow-y-auto">
      <!-- Loading State -->
      <div v-if="searching" class="flex items-center justify-center py-8">
        <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
        <span class="ml-2 text-slate-600 dark:text-slate-400">{{ $t('Searching...') }}</span>
      </div>

      <!-- No Results -->
      <div v-else-if="!searching && searchResults.length === 0 && hasSearched" class="text-center py-8">
        <Search class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No results found') }}</h3>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('Try adjusting your search terms or filters') }}</p>
      </div>

      <!-- Search Results -->
      <div v-else-if="searchResults.length > 0" class="p-4 space-y-4">
        <!-- Results Summary -->
        <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
          <span>{{ searchResults.length }} {{ $t('results found') }}</span>
          <div class="flex items-center gap-2">
            <span>{{ $t('Sort by') }}</span>
            <select v-model="sortBy" @change="sortResults" class="px-2 py-1 border border-slate-300 dark:border-slate-600 rounded text-xs dark:bg-slate-700 dark:text-white">
              <option value="relevance">{{ $t('Relevance') }}</option>
              <option value="date">{{ $t('Date') }}</option>
              <option value="author">{{ $t('Author') }}</option>
            </select>
          </div>
        </div>

        <!-- Results List -->
        <div class="space-y-3">
          <div
            v-for="result in searchResults"
            :key="result.id"
            class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
            @click="openResult(result)"
          >
            <!-- Result Header -->
            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-600">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    <MessageCircle class="w-4 h-4" />
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ result.conversation?.subject || 'Untitled Conversation' }}</h4>
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                      <span>{{ result.author?.name || 'Unknown' }}</span>
                      <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                      <span>{{ formatDate(result.created_at) }}</span>
                      <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium" :class="getTypeClass(result.conversation?.type)">
                        {{ result.conversation?.type === 'internal' ? $t('Internal') : $t('Customer') }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-xs text-slate-500 dark:text-slate-400">{{ formatTime(result.created_at) }}</span>
                  <ChevronRight class="w-4 h-4 text-slate-400" />
                </div>
              </div>
            </div>

            <!-- Result Content -->
            <div class="px-4 py-3">
              <div class="text-sm text-slate-700 dark:text-slate-300">
                <span v-html="highlightSearchTerm(result.content, searchQuery)"></span>
              </div>
              
              <!-- Attachments Indicator -->
              <div v-if="result.attachments?.length" class="mt-2 flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                <Paperclip class="w-3 h-3" />
                <span>{{ result.attachments.length }} {{ $t('attachments') }}</span>
              </div>
            </div>

            <!-- Result Actions -->
            <div class="px-4 py-2 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                  <span>{{ $t('Conversation') }} #{{ result.conversation?.id }}</span>
                  <span v-if="result.reply_count > 0">{{ result.reply_count }} {{ $t('replies') }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <button @click.stop="copyLink(result)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                    {{ $t('Copy Link') }}
                  </button>
                  <button @click.stop="bookmarkResult(result)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                    {{ $t('Bookmark') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More -->
        <div v-if="hasMoreResults" class="flex justify-center pt-4">
          <button @click="loadMoreResults" :disabled="loadingMore" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors disabled:opacity-50">
            <Loader2 v-if="loadingMore" class="w-4 h-4 mr-2 animate-spin" />
            {{ $t('Load More Results') }}
          </button>
        </div>
      </div>

      <!-- Initial State -->
      <div v-else-if="!hasSearched" class="text-center py-8">
        <Search class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('Search Conversations') }}</h3>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('Enter a search term to find messages and conversations') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Search,
  X,
  Filter,
  MessageCircle,
  ChevronRight,
  Paperclip,
  Loader2
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'ConversationSearch',
  components: {
    Search,
    X,
    Filter,
    MessageCircle,
    ChevronRight,
    Paperclip,
    Loader2
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    },
    availableUsers: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      moment: moment,
      searchQuery: '',
      searching: false,
      hasSearched: false,
      loadingMore: false,
      hasMoreResults: false,
      showAdvancedSearch: false,
      sortBy: 'relevance',
      searchResults: [],
      filters: {
        dateRange: '',
        type: '',
        author: '',
        hasAttachments: '',
        fromDate: '',
        toDate: ''
      },
      searchTimeout: null,
      // Sample search results for demonstration
      sampleResults: [
        {
          id: 1,
          content: 'This is a sample message about the ticket issue. We need to investigate the problem further.',
          author: { name: 'John Doe' },
          created_at: new Date().toISOString(),
          conversation: {
            id: 1,
            subject: 'Sample Conversation',
            type: 'internal'
          },
          attachments: [],
          reply_count: 2
        },
        {
          id: 2,
          content: 'I have attached the log files for your review. Please let me know if you need any additional information.',
          author: { name: 'Jane Smith' },
          created_at: new Date(Date.now() - 86400000).toISOString(),
          conversation: {
            id: 2,
            subject: 'Log Files Discussion',
            type: 'customer'
          },
          attachments: [{ name: 'error.log' }],
          reply_count: 1
        },
        {
          id: 3,
          content: 'The issue has been resolved. The customer confirmed that everything is working correctly now.',
          author: { name: 'Mike Johnson' },
          created_at: new Date(Date.now() - 172800000).toISOString(),
          conversation: {
            id: 3,
            subject: 'Resolution Update',
            type: 'internal'
          },
          attachments: [],
          reply_count: 0
        }
      ]
    }
  },
  methods: {
    handleSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      
      this.searchTimeout = setTimeout(() => {
        if (this.searchQuery.trim().length >= 2) {
          this.performSearch()
        } else {
          this.clearSearch()
        }
      }, 300)
    },
    
    async performSearch() {
      if (!this.searchQuery.trim()) return
      
      this.searching = true
      this.hasSearched = true
      
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // Filter sample results based on search query
        this.searchResults = this.sampleResults.filter(result =>
          result.content.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          result.conversation.subject.toLowerCase().includes(this.searchQuery.toLowerCase())
        )
        
        // Apply additional filters
        this.applyFilters()
        
      } catch (error) {
        console.error('Error performing search:', error)
      } finally {
        this.searching = false
      }
    },
    
    clearSearch() {
      this.searchQuery = ''
      this.searchResults = []
      this.hasSearched = false
      this.clearFilters()
    },
    
    toggleAdvancedSearch() {
      this.showAdvancedSearch = !this.showAdvancedSearch
    },
    
    applyFilters() {
      let filtered = [...this.searchResults]
      
      // Apply type filter
      if (this.filters.type) {
        filtered = filtered.filter(result => result.conversation.type === this.filters.type)
      }
      
      // Apply author filter
      if (this.filters.author) {
        filtered = filtered.filter(result => result.author.id === this.filters.author)
      }
      
      // Apply attachments filter
      if (this.filters.hasAttachments === 'true') {
        filtered = filtered.filter(result => result.attachments && result.attachments.length > 0)
      } else if (this.filters.hasAttachments === 'false') {
        filtered = filtered.filter(result => !result.attachments || result.attachments.length === 0)
      }
      
      // Apply date range filter
      if (this.filters.dateRange) {
        const now = new Date()
        let startDate = null
        
        switch (this.filters.dateRange) {
          case 'today':
            startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate())
            break
          case 'week':
            startDate = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
            break
          case 'month':
            startDate = new Date(now.getFullYear(), now.getMonth(), 1)
            break
          case 'custom':
            if (this.filters.fromDate) {
              startDate = new Date(this.filters.fromDate)
            }
            break
        }
        
        if (startDate) {
          filtered = filtered.filter(result => new Date(result.created_at) >= startDate)
        }
        
        if (this.filters.toDate) {
          const endDate = new Date(this.filters.toDate)
          endDate.setHours(23, 59, 59, 999)
          filtered = filtered.filter(result => new Date(result.created_at) <= endDate)
        }
      }
      
      this.searchResults = filtered
    },
    
    clearFilters() {
      this.filters = {
        dateRange: '',
        type: '',
        author: '',
        hasAttachments: '',
        fromDate: '',
        toDate: ''
      }
      this.performSearch()
    },
    
    sortResults() {
      switch (this.sortBy) {
        case 'date':
          this.searchResults.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          break
        case 'author':
          this.searchResults.sort((a, b) => a.author.name.localeCompare(b.author.name))
          break
        case 'relevance':
        default:
          // Keep original order for relevance
          break
      }
    },
    
    loadMoreResults() {
      this.loadingMore = true
      // Simulate loading more results
      setTimeout(() => {
        this.loadingMore = false
        this.hasMoreResults = false
      }, 1000)
    },
    
    openResult(result) {
      this.$emit('result-selected', result)
    },
    
    copyLink(result) {
      const url = `${window.location.origin}/conversations/${result.conversation.id}#message-${result.id}`
      navigator.clipboard.writeText(url)
      // Show success notification
    },
    
    bookmarkResult(result) {
      // Bookmark functionality
      console.log('Bookmarking result:', result)
    },
    
    highlightSearchTerm(text, term) {
      if (!term) return text
      
      const regex = new RegExp(`(${term})`, 'gi')
      return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-800 px-1 rounded">$1</mark>')
    },
    
    getTypeClass(type) {
      return type === 'internal'
        ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300'
        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
    },
    
    formatDate(date) {
      return moment(date).format('MMM D, YYYY')
    },
    
    formatTime(date) {
      return moment(date).format('h:mm A')
    }
  }
}
</script>

<style scoped>
.conversation-search {
  @apply flex flex-col h-full;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  @apply bg-slate-100 dark:bg-slate-700;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  @apply bg-slate-300 dark:bg-slate-600 rounded-full;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  @apply bg-slate-400 dark:bg-slate-500;
}
</style>
