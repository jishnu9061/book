<template>
    <div class="relative" :class="$attrs.class">
        <!-- Label -->
        <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ label }}
        </label>

        <!-- Search Container -->
        <div class="relative">
            <!-- Search Icon -->
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <Search class="w-4 h-4 text-slate-400 dark:text-slate-500" />
            </div>

            <!-- Input Field -->
            <input
                :id="id"
                ref="input"
                v-bind="{ ...$attrs, class: null }"
                :class="[
                    'w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200',
                    error ? 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500' : '',
                    disabled ? 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : '',
                    size === 'sm' ? 'pl-8 pr-8 py-2 text-xs' : '',
                    size === 'lg' ? 'pl-12 pr-12 py-4 text-base' : ''
                ]"
                type="text"
                name="search"
                :placeholder="placeholder || $t('Search...')"
                :value="modelValue"
                :disabled="disabled"
                :autocomplete="autocomplete"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                @keydown="onKeydown"
            />

            <!-- Clear Button -->
            <button
                v-if="!disableReset && modelValue && !disabled"
                type="button"
                @click="clearSearch"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200 group"
            >
                <X class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" />
            </button>

            <!-- Loading Spinner -->
            <div v-if="isLoading" class="absolute inset-y-0 right-3 flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div>
            </div>

            <!-- Search Results Count -->
            <div v-if="showResultsCount && resultsCount !== null" class="absolute inset-y-0 right-3 flex items-center">
                <span class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-600 px-2 py-1 rounded-full">
                    {{ resultsCount }}
                </span>
            </div>
        </div>

        <!-- Helper Text -->
        <div v-if="helperText && !error" class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            {{ helperText }}
        </div>

        <!-- Error Message -->
        <div v-if="error" class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
            <AlertCircle class="w-4 h-4 flex-shrink-0" />
            <span>{{ error }}</span>
        </div>

        <!-- Search Suggestions (if provided) -->
        <div v-if="showSuggestions && suggestions.length > 0 && isFocused" class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            <div class="py-1">
                <div
                    v-for="(suggestion, index) in suggestions"
                    :key="index"
                    :class="[
                        'px-4 py-3 text-sm cursor-pointer transition-colors duration-150 flex items-center gap-3',
                        selectedSuggestion === index
                            ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                            : 'text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-600'
                    ]"
                    @click="selectSuggestion(suggestion)"
                    @mouseenter="selectedSuggestion = index"
                >
                    <Search class="w-4 h-4 text-slate-400" />
                    <span>{{ suggestion }}</span>
                </div>
            </div>
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
        id: {
            type: String,
            default() {
                return `search-input-${uuid()}`
            },
        },
        modelValue: String,
        label: String,
        placeholder: String,
        helperText: String,
        error: String,
        disabled: {
            type: Boolean,
            default: false,
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
        disableReset: {
            type: Boolean,
            default: false,
        },
        autocomplete: {
            type: String,
            default: 'off',
        },
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['sm', 'md', 'lg'].includes(value)
        },
        showResultsCount: {
            type: Boolean,
            default: false,
        },
        resultsCount: {
            type: Number,
            default: null,
        },
        showSuggestions: {
            type: Boolean,
            default: false,
        },
        suggestions: {
            type: Array,
            default: () => []
        },
        debounceMs: {
            type: Number,
            default: 300,
        },
    },
    emits: ['update:modelValue', 'reset', 'focus', 'blur', 'keydown', 'enter', 'search', 'suggestion-select'],
    data() {
        return {
            isFocused: false,
            selectedSuggestion: -1,
            debounceTimer: null,
        }
    },
    watch: {
        modelValue(newValue) {
            if (this.debounceMs > 0) {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.$emit('search', newValue);
                }, this.debounceMs);
            } else {
                this.$emit('search', newValue);
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }
    },
    methods: {
        onInput(event) {
            this.$emit('update:modelValue', event.target.value);
            this.selectedSuggestion = -1;
        },
        onFocus(event) {
            this.isFocused = true;
            this.$emit('focus', event);
        },
        onBlur(event) {
            // Delay blur to allow suggestion clicks
            setTimeout(() => {
                this.isFocused = false;
                this.selectedSuggestion = -1;
                this.$emit('blur', event);
            }, 150);
        },
        onKeydown(event) {
            this.$emit('keydown', event);
            
            if (event.key === 'Enter') {
                if (this.selectedSuggestion >= 0 && this.suggestions[this.selectedSuggestion]) {
                    this.selectSuggestion(this.suggestions[this.selectedSuggestion]);
                } else {
                    this.$emit('enter', event);
                }
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                this.selectedSuggestion = Math.min(this.selectedSuggestion + 1, this.suggestions.length - 1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                this.selectedSuggestion = Math.max(this.selectedSuggestion - 1, -1);
            } else if (event.key === 'Escape') {
                this.isFocused = false;
                this.selectedSuggestion = -1;
                this.$refs.input.blur();
            }
        },
        clearSearch() {
            this.$emit('update:modelValue', '');
            this.$emit('reset');
            this.selectedSuggestion = -1;
            this.focus();
        },
        selectSuggestion(suggestion) {
            this.$emit('update:modelValue', suggestion);
            this.$emit('suggestion-select', suggestion);
            this.isFocused = false;
            this.selectedSuggestion = -1;
        },
        handleClickOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.isFocused = false;
                this.selectedSuggestion = -1;
            }
        },
        focus() {
            this.$refs.input.focus();
        },
        blur() {
            this.$refs.input.blur();
        },
        select() {
            this.$refs.input.select();
        },
    },
}
</script>
