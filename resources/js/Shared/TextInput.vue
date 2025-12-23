<template>
    <div class="relative" :class="$attrs.class">
        <!-- Label -->
        <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <!-- Input Container -->
        <div class="relative">
            <!-- Input Field -->
            <input 
                :id="id" 
                ref="input" 
                v-bind="{ ...$attrs, class: null }" 
                :class="[
                    'w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200',
                    error ? 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500' : '',
                    disabled ? 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : '',
                    size === 'sm' ? 'px-3 py-2 text-xs' : '',
                    size === 'lg' ? 'px-4 py-4 text-base' : '',
                    type === 'password' ? 'pr-10' : ''
                ]"
                :type="currentType" 
                :placeholder="placeholder" 
                :value="modelValue" 
                :disabled="disabled"
                :required="required"
                :autocomplete="autocomplete"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                @keydown="onKeydown"
            />

            <!-- Password Toggle Button -->
            <button
                v-if="type === 'password' && showPasswordToggle"
                type="button"
                @click="togglePassword"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200"
            >
                <Eye v-if="!showPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
            </button>

            <!-- Loading Spinner -->
            <div v-if="isLoading" class="absolute inset-y-0 right-3 flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div>
            </div>

            <!-- Success Icon -->
            <div v-if="successMessage && !error" class="absolute inset-y-0 right-3 flex items-center">
                <CheckCircle class="w-4 h-4 text-green-500" />
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

        <!-- Character Count (for textarea) -->
        <div v-if="showCharacterCount && maxLength" class="mt-1 text-xs text-slate-500 dark:text-slate-400 text-right">
            {{ characterCount }}/{{ maxLength }}
        </div>
    </div>
</template>

<script>
import { v4 as uuid } from 'uuid'
import { Eye, EyeOff, AlertCircle, CheckCircle } from 'lucide-vue-next'

export default {
    inheritAttrs: false,
    components: {
        Eye,
        EyeOff,
        AlertCircle,
        CheckCircle,
    },
    props: {
        id: {
            type: String,
            default() {
                return `text-input-${uuid()}`
            },
        },
        type: {
            type: String,
            default: 'text',
        },
        error: String,
        label: String,
        helperText: String,
        successMessage: String,
        required: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
        placeholder: String,
        modelValue: [String, Number],
        autocomplete: String,
        size: {
            type: String,
            default: 'md',
            validator: (value) => ['sm', 'md', 'lg'].includes(value)
        },
        showPasswordToggle: {
            type: Boolean,
            default: true,
        },
        showCharacterCount: {
            type: Boolean,
            default: false,
        },
        maxLength: {
            type: Number,
            default: null,
        },
    },
    emits: ['update:modelValue', 'focus', 'blur', 'keydown', 'enter'],
    data() {
        return {
            showPassword: false,
            currentType: this.type,
        }
    },
    computed: {
        characterCount() {
            return this.modelValue ? String(this.modelValue).length : 0;
        },
        isOverLimit() {
            return this.maxLength && this.characterCount > this.maxLength;
        }
    },
    watch: {
        type(newType) {
            this.currentType = newType;
        }
    },
    methods: {
        onInput(event) {
            this.$emit('update:modelValue', event.target.value);
        },
        onFocus(event) {
            this.$emit('focus', event);
        },
        onBlur(event) {
            this.$emit('blur', event);
        },
        onKeydown(event) {
            this.$emit('keydown', event);
            
            if (event.key === 'Enter') {
                this.$emit('enter', event);
            }
        },
        togglePassword() {
            this.showPassword = !this.showPassword;
            this.currentType = this.showPassword ? 'text' : 'password';
        },
        focus() {
            this.$refs.input.focus();
        },
        select() {
            this.$refs.input.select();
        },
        setSelectionRange(start, end) {
            this.$refs.input.setSelectionRange(start, end);
        },
        blur() {
            this.$refs.input.blur();
        },
        clear() {
            this.$emit('update:modelValue', '');
            this.focus();
        },
    },
}
</script>
