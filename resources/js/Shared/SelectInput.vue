<template>
    <div class="relative" :class="$attrs.class">
        <!-- Label -->
        <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <!-- Select Container -->
        <div class="relative">
            <select 
                :id="id" 
                ref="input" 
                v-model="selected" 
                v-bind="{ ...$attrs, class: null }" 
                :class="[
                    'w-full px-4 py-3 pr-10 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200 appearance-none cursor-pointer',
                    error ? 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500' : '',
                    disabled ? 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : '',
                    size === 'sm' ? 'px-3 py-2 text-xs' : '',
                    size === 'lg' ? 'px-4 py-4 text-base' : ''
                ]"
                :required="required"
                :disabled="disabled"
                @focus="onFocus"
                @blur="onBlur"
                @change="onChange"
            >
                <slot />
            </select>

            <!-- Custom Dropdown Arrow -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <ChevronDown 
                    :class="[
                        'w-4 h-4 transition-transform duration-200',
                        isOpen ? 'rotate-180' : '',
                        error ? 'text-red-500 dark:text-red-400' : 'text-slate-400 dark:text-slate-500',
                        disabled ? 'text-slate-300 dark:text-slate-600' : ''
                    ]"
                />
            </div>

            <!-- Loading Spinner -->
            <div v-if="isLoading" class="absolute inset-y-0 right-8 flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div>
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

        <!-- Success Message -->
        <div v-if="successMessage && !error" class="mt-2 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
            <CheckCircle class="w-4 h-4 flex-shrink-0" />
            <span>{{ successMessage }}</span>
        </div>
    </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { ChevronDown, AlertCircle, CheckCircle } from 'lucide-vue-next'

export default {
    inheritAttrs: false,
    components: {
        ChevronDown,
        AlertCircle,
        CheckCircle,
    },
    props: {
        id: {
            type: String,
            default() {
                return `select-input-${uuid()}`
            },
        },
        error: String,
        label: String,
        helperText: String,
        successMessage: String,
        required: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        isLoading: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['sm', 'md', 'lg'].includes(value)
        },
        modelValue: [String, Number, Boolean],
    },
    emits: ['update:modelValue', 'focus', 'blur', 'change'],
    data() {
        return {
            selected: this.modelValue,
            isOpen: false,
        }
    },
    watch: {
        selected(selected) {
            this.$emit('update:modelValue', selected)
        },
        modelValue(newValue) {
            if (newValue !== this.selected) {
                this.selected = newValue
            }
        }
    },
    methods: {
        onFocus() {
            this.isOpen = true
            this.$emit('focus')
        },
        onBlur() {
            this.isOpen = false
            this.$emit('blur')
        },
        onChange(event) {
            this.$emit('change', event)
        },
        focus() {
            this.$refs.input.focus()
        },
        select() {
            this.$refs.input.select()
        },
        blur() {
            this.$refs.input.blur()
        },
    },
}
</script>
