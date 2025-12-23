<template>
  <div class="select-edit-input" :class="containerClasses" ref="container">
    <!-- Label -->
    <label v-if="label" :for="inputId" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
      {{ safeTranslate(label) }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Display Mode -->
    <div v-if="!isEditing && displayValue" class="display-mode">
      <div
        class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors"
        @click="handleContainerClick"
      >
        <div class="flex items-center gap-3">
          <div class="flex-1">
            <div class="text-sm font-medium text-slate-900 dark:text-white">
              {{ displayValue }}
            </div>
            <div v-if="selectedItem && selectedItem.description" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
              {{ selectedItem.description }}
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button
            v-if="editable"
            @click.stop="startEditing"
            type="button"
            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
            :title="$t('Edit')"
          >
            <Edit class="w-4 h-4" />
          </button>
          <button
            v-if="clearable && selected"
            @click.stop="clearSelection"
            type="button"
            class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
            :title="$t('Clear')"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Mode -->
    <div v-if="isEditing || !displayValue" class="edit-mode">
      <div class="relative">
        <input
          :id="inputId"
          ref="input"
          v-model="searchQuery"
          class="form-input w-full pr-10"
          :class="inputClasses"
          type="text"
          :placeholder="placeholder"
          :disabled="disabled"
          :readonly="readonly"
          autocomplete="off"
          @input="handleInput"
          @focus="handleFocus"
          @blur="handleBlur"
          @keydown.down.prevent="navigateDown"
          @keydown.up.prevent="navigateUp"
          @keydown.enter.prevent="selectHighlightedItem"
          @keydown.escape.prevent="cancelEditing"
          @keydown.tab="handleTab"
        />

        <!-- Input Icons -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
          <button
            v-if="isEditing && searchQuery"
            @click="clearSearch"
            type="button"
            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
          >
            <X class="w-4 h-4" />
          </button>
          <ChevronDown
            class="w-4 h-4 text-slate-400 transition-transform duration-200"
            :class="{ 'rotate-180': isListVisible }"
          />
        </div>
      </div>

      <!-- Dropdown List -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div
          v-if="isListVisible && filteredItems.length > 0"
          class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg shadow-lg max-h-60 overflow-auto"
        >
          <ul class="py-1">
            <li
              v-for="(item, index) in filteredItems"
              :key="getItemKey(item, index)"
              @click="selectItem(item)"
              @mouseenter="highlightedIndex = index"
              class="px-3 py-2 cursor-pointer transition-colors"
              :class="getItemClasses(item, index)"
            >
              <div class="flex items-center gap-3">
                <div class="flex-1">
                  <div class="text-sm font-medium text-slate-900 dark:text-white">
                    {{ getItemDisplayName(item) }}
                  </div>
                  <div v-if="item.description" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ item.description }}
                  </div>
                </div>
                <div v-if="item.badge" class="flex-shrink-0">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                        :class="getBadgeClasses(item.badge)">
                    {{ item.badge }}
                  </span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </Transition>

      <!-- No Results -->
      <div
        v-if="isListVisible && filteredItems.length === 0 && searchQuery"
        class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg shadow-lg"
      >
        <div class="px-3 py-2 text-sm text-slate-500 dark:text-slate-400 text-center">
          {{ noResultsText }}
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400">
      {{ error }}
    </div>

    <!-- Help Text -->
    <div v-if="helpText && !error" class="mt-2 text-sm text-slate-500 dark:text-slate-400">
      {{ helpText }}
    </div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { Edit, X, ChevronDown } from 'lucide-vue-next'

export default {
  name: 'SelectEditInput',
  components: {
    Edit,
    X,
    ChevronDown
  },
  inheritAttrs: false,
  props: {
    // Core props
    modelValue: {
      type: [String, Number, Boolean, Object],
      default: null
    },
    items: {
      type: Array,
      default: () => []
    },
    placeholder: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: ''
    },

    // Behavior props
    editable: {
      type: Boolean,
      default: true
    },
    clearable: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    readonly: {
      type: Boolean,
      default: false
    },
    required: {
      type: Boolean,
      default: false
    },

    // Display props
    itemKey: {
      type: String,
      default: 'id'
    },
    itemLabel: {
      type: String,
      default: 'name'
    },
    itemDescription: {
      type: String,
      default: 'description'
    },
    itemBadge: {
      type: String,
      default: 'badge'
    },

    // Styling props
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
      type: String,
      default: 'default',
      validator: (value) => ['default', 'filled', 'outlined'].includes(value)
    },

    // Text props
    noResultsText: {
      type: String,
      default: 'No results found'
    },
    helpText: {
      type: String,
      default: ''
    },
    error: {
      type: String,
      default: ''
    },

    // Advanced props
    searchable: {
      type: Boolean,
      default: true
    },
    filterMethod: {
      type: Function,
      default: null
    },
    debounceDelay: {
      type: Number,
      default: 300
    }
  },
  emits: ['update:modelValue', 'change', 'focus', 'blur', 'search', 'clear'],
  data() {
    return {
      isEditing: false,
      isListVisible: false,
      searchQuery: '',
      highlightedIndex: -1,
      selectedItem: null,
      debounceTimer: null
    }
  },
  computed: {
    inputId() {
      return `select-edit-input-${uuid()}`
    },
    containerClasses() {
      return [
        'relative',
        {
          'opacity-50 pointer-events-none': this.disabled,
          'has-error': this.error
        }
      ]
    },
    inputClasses() {
      return [
        {
          'border-red-300 focus:border-red-500 focus:ring-red-500': this.error,
          'border-slate-300 focus:border-blue-500 focus:ring-blue-500': !this.error,
          'text-sm': this.size === 'sm',
          'text-base': this.size === 'md',
          'text-lg': this.size === 'lg'
        }
      ]
    },
    displayValue() {
      if (this.selectedItem) {
        return this.getItemDisplayName(this.selectedItem)
      }
      return this.searchQuery || ''
    },
    filteredItems() {
      if (!this.searchable || !this.searchQuery) {
        return this.items
      }

      if (this.filterMethod) {
        return this.filterMethod(this.items, this.searchQuery)
      }

      return this.items.filter(item => {
        const searchTerm = this.searchQuery.toLowerCase()
        const displayName = this.getItemDisplayName(item).toLowerCase()
        const description = (item[this.itemDescription] || '').toLowerCase()

        return displayName.includes(searchTerm) || description.includes(searchTerm)
      })
    }
  },
  watch: {
    modelValue: {
      handler(newValue) {
        this.updateSelectedItem(newValue)
      },
      immediate: true
    },
    items: {
      handler() {
        this.updateSelectedItem(this.modelValue)
      },
      deep: true
    }
  },
  mounted() {
    document.addEventListener('click', this.handleOutsideClick)
    this.updateSelectedItem(this.modelValue)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleOutsideClick)
    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer)
    }
  },
  methods: {
    // Utility methods
    safeTranslate(key) {
      try {
        if (!key || typeof key !== 'string') {
          return key || ''
        }
        return this.$t(key)
      } catch (error) {
        console.warn('Translation error for key:', key, error)
        return key || ''
      }
    },
    getItemKey(item, index) {
      return item[this.itemKey] || item.id || index
    },
    getItemDisplayName(item) {
      if (!item) return ''
      return item[this.itemLabel] || item.name || item.label || item.text || String(item.id) || ''
    },
    getItemClasses(item, index) {
      return [
        {
          'bg-blue-50 dark:bg-blue-900/20 text-blue-900 dark:text-blue-100': index === this.highlightedIndex,
          'bg-slate-50 dark:bg-slate-700': index !== this.highlightedIndex,
          'hover:bg-slate-100 dark:hover:bg-slate-600': index !== this.highlightedIndex
        }
      ]
    },
    getBadgeClasses(badge) {
      // You can customize badge colors based on badge content
      if (typeof badge === 'object') {
        return badge.class || 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
      }
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
    },

    // Event handlers
    handleInput(event) {
      this.searchQuery = event.target.value
      this.isListVisible = true
      this.highlightedIndex = -1

      // Debounce search
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer)
      }
      this.debounceTimer = setTimeout(() => {
        this.$emit('search', this.searchQuery)
      }, this.debounceDelay)
    },
    handleFocus() {
      this.isListVisible = true
      this.$emit('focus')
    },
    handleBlur() {
      // Delay to allow click events to fire
      setTimeout(() => {
        if (!this.isListVisible) {
          this.finishEditing()
        }
      }, 150)
      this.$emit('blur')
    },
    handleOutsideClick(event) {
      if (this.$el && !this.$el.contains(event.target)) {
        this.isListVisible = false
        this.finishEditing()
      }
    },
    handleTab() {
      if (this.highlightedIndex >= 0 && this.filteredItems[this.highlightedIndex]) {
        this.selectItem(this.filteredItems[this.highlightedIndex])
      } else {
        this.finishEditing()
      }
    },

    // Navigation methods
    navigateDown() {
      if (this.filteredItems.length === 0) return
      this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredItems.length - 1)
    },
    navigateUp() {
      if (this.filteredItems.length === 0) return
      this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1)
    },
    selectHighlightedItem() {
      if (this.highlightedIndex >= 0 && this.filteredItems[this.highlightedIndex]) {
        this.selectItem(this.filteredItems[this.highlightedIndex])
      }
    },

    // Selection methods
    selectItem(item) {
      if (!item) return

      this.selectedItem = item
      this.searchQuery = this.getItemDisplayName(item)
      this.isListVisible = false
      this.highlightedIndex = -1

      this.$emit('update:modelValue', item[this.itemKey] || item.id)
      this.$emit('change', item)

      this.finishEditing()
    },
    clearSelection() {
      this.selectedItem = null
      this.searchQuery = ''
      this.$emit('update:modelValue', null)
      this.$emit('change', null)
      this.$emit('clear')
    },
    clearSearch() {
      this.searchQuery = ''
      this.highlightedIndex = -1
      this.$nextTick(() => {
        this.$refs.input?.focus()
      })
    },

    // Edit mode methods
    handleContainerClick(event) {
      // Only start editing if the click wasn't on a button
      if (!event.target.closest('button')) {
        this.startEditing()
      }
    },
    startEditing() {
      console.log('startEditing called', {
        editable: this.editable,
        disabled: this.disabled,
        readonly: this.readonly,
        displayValue: this.displayValue
      })

      if (!this.editable || this.disabled || this.readonly) {
        console.log('startEditing blocked due to conditions')
        return
      }

      this.isEditing = true
      this.searchQuery = this.displayValue
      console.log('startEditing - isEditing set to true, searchQuery:', this.searchQuery)

      this.$nextTick(() => {
        this.$refs.input?.focus()
        this.$refs.input?.select()
      })
    },
    finishEditing() {
      this.isEditing = false
      this.isListVisible = false
      this.highlightedIndex = -1
    },
    cancelEditing() {
      this.searchQuery = this.displayValue
      this.finishEditing()
    },

    // Data methods
    updateSelectedItem(value) {
      if (!value) {
        this.selectedItem = null
        return
      }

      this.selectedItem = this.items.find(item =>
        (item[this.itemKey] || item.id) === value
      ) || null
    }
  }
}
</script>

<style scoped>
.select-edit-input {
  @apply relative;
}

.display-mode {
  @apply transition-all duration-200;
}

.edit-mode {
  @apply transition-all duration-200;
}

.form-input {
  @apply block w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white;
}

.form-input:disabled {
  @apply bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed;
}

.has-error .form-input {
  @apply border-red-300 focus:border-red-500 focus:ring-red-500;
}

/* Custom scrollbar for dropdown */
.max-h-60::-webkit-scrollbar {
  width: 6px;
}

.max-h-60::-webkit-scrollbar-track {
  @apply bg-slate-100 dark:bg-slate-700 rounded;
}

.max-h-60::-webkit-scrollbar-thumb {
  @apply bg-slate-300 dark:bg-slate-500 rounded;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
  @apply bg-slate-400 dark:bg-slate-400;
}
</style>
