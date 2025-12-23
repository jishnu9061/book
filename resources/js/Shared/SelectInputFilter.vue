<template>
  <div class="relative" :class="$attrs.class" ref="sel__filter">
    <!-- Label -->
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
      {{ label }}
    </label>

    <!-- Input Container -->
    <div class="relative">
      <div class="relative">
        <input
          :id="id"
          ref="input"
          :class="[
            'w-full px-4 py-3 pl-4 pr-10 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200',
            error ? 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500' : '',
            isListVisible ? 'rounded-b-none border-b-0' : ''
          ]"
          type="text"
          v-bind="{ ...$attrs, class: null }"
          :placeholder="placeholder"
          :value="selectedValue"
          @input="onInput"
          @focus="onFocus"
          @keydown.down.prevent="onArrowDown"
          @keydown.up.prevent="onArrowUp"
          @keydown.enter.prevent="selectCurrentSelection"
          @keydown.escape.prevent="closeDropdown"
          autocomplete="off"
        />

        <!-- Search Icon -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
          <Search class="w-4 h-4 text-slate-400 dark:text-slate-500" />
        </div>

        <!-- Clear Button -->
        <button
          v-if="selectedValue && !isListVisible"
          @click="clearSelection"
          class="absolute inset-y-0 right-8 flex items-center pr-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200"
        >
          <X class="w-4 h-4" />
        </button>
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
          v-if="isListVisible && items.length"
          class="absolute z-50 w-full mt-0 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 border-t-0 rounded-b-lg shadow-lg max-h-60 overflow-y-auto"
        >
          <div class="py-1">
            <div
              v-for="(item, index) in items"
              :key="item.id || index"
              :class="[
                'px-4 py-3 text-sm cursor-pointer transition-colors duration-150 flex items-center justify-between group',
                currentIndex === index
                  ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                  : 'text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-600'
              ]"
              @click="selectItem(item)"
              @mouseenter="currentIndex = index"
            >
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-blue-500 transition-colors duration-200"></div>
                <span class="font-medium">{{ item.name }}</span>
              </div>
              <div v-if="item.email" class="text-xs text-slate-500 dark:text-slate-400">
                {{ item.email }}
              </div>
            </div>
          </div>
        </div>
      </Transition>

      <!-- No Results State -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div
          v-if="isListVisible && items.length === 0 && selectedValue"
          class="absolute z-50 w-full mt-0 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 border-t-0 rounded-b-lg shadow-lg"
        >
          <div class="px-4 py-6 text-center">
            <Search class="w-8 h-8 mx-auto mb-2 text-slate-400 dark:text-slate-500" />
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('No results found') }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $t('Try a different search term') }}</p>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
      <AlertCircle class="w-4 h-4 flex-shrink-0" />
      <span>{{ error }}</span>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="absolute inset-y-0 right-3 flex items-center">
      <div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div>
    </div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { Search, X, AlertCircle } from 'lucide-vue-next'

export default {
  inheritAttrs: false,
  components: {
    Search,
    X,
    AlertCircle,
  },
  props: {
    placeholder: {
      type: String,
      default: '',
    },
    onInput: {
      type: Function,
    },
    items: {
      type: Array,
      default: () => []
    },
    id: {
      type: String,
      default() {
        return `select-input-filter-${uuid()}`
      },
    },
    error: String,
    label: String,
    modelValue: [String, Number, Boolean],
    isLoading: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'focus', 'blur', 'clear'],
  data() {
    return {
      selectedValue: '',
      selected: this.modelValue,
      isListVisible: false,
      currentIndex: -1,
    }
  },
  watch: {
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
    modelValue(newValue) {
      if (newValue !== this.selected) {
        this.selected = newValue
        // Find the selected item and update selectedValue
        const selectedItem = this.items.find(item => item.id === newValue)
        if (selectedItem) {
          this.selectedValue = selectedItem.name
        } else {
          this.selectedValue = ''
        }
      }
    },
    items: {
      handler() {
        // Reset current index when items change
        this.currentIndex = -1
      },
      deep: true
    }
  },
  mounted() {
    document.addEventListener('click', this.close)
    // Set initial selected value if modelValue is provided
    if (this.modelValue) {
      const selectedItem = this.items.find(item => item.id === this.modelValue)
      if (selectedItem) {
        this.selectedValue = selectedItem.name
      }
    }
  },
  beforeUnmount() {
    document.removeEventListener('click', this.close)
  },
  methods: {
    close(e) {
      if (!this.$el.contains(e.target)) {
        this.isListVisible = false
        this.currentIndex = -1
      }
    },
    onFocus() {
      this.isListVisible = true
      this.$emit('focus')
    },
    onBlur() {
      this.$emit('blur')
    },
    selectItem(item) {
      this.$refs.input.value = item.name
      this.selected = item.id
      this.selectedValue = item.name
      this.isListVisible = false
      this.currentIndex = -1
    },
    clearSelection() {
      this.$refs.input.value = ''
      this.selected = null
      this.selectedValue = ''
      this.isListVisible = false
      this.currentIndex = -1
      this.$emit('clear')
      this.$emit('update:modelValue', null)
    },
    onArrowDown() {
      if (!this.isListVisible) {
        this.isListVisible = true
        return
      }

      if (this.currentIndex < this.items.length - 1) {
        this.currentIndex++
      } else {
        this.currentIndex = 0
      }
    },
    onArrowUp() {
      if (!this.isListVisible) {
        this.isListVisible = true
        return
      }

      if (this.currentIndex > 0) {
        this.currentIndex--
      } else {
        this.currentIndex = this.items.length - 1
      }
    },
    selectCurrentSelection() {
      if (this.isListVisible && this.currentIndex >= 0 && this.items[this.currentIndex]) {
        this.selectItem(this.items[this.currentIndex])
      }
    },
    closeDropdown() {
      this.isListVisible = false
      this.currentIndex = -1
    },
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
  },
}
</script>
