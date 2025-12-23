import { v4 } from "uuid";
import { CheckCircle, AlertCircle, EyeOff, Eye } from "lucide-vue-next";
import { resolveComponent, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
const _sfc_main = {
  inheritAttrs: false,
  components: {
    Eye,
    EyeOff,
    AlertCircle,
    CheckCircle
  },
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${v4()}`;
      }
    },
    type: {
      type: String,
      default: "text"
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
    placeholder: String,
    modelValue: [String, Number],
    autocomplete: String,
    size: {
      type: String,
      default: "md",
      validator: (value) => ["sm", "md", "lg"].includes(value)
    },
    showPasswordToggle: {
      type: Boolean,
      default: true
    },
    showCharacterCount: {
      type: Boolean,
      default: false
    },
    maxLength: {
      type: Number,
      default: null
    }
  },
  emits: ["update:modelValue", "focus", "blur", "keydown", "enter"],
  data() {
    return {
      showPassword: false,
      currentType: this.type
    };
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
      this.$emit("update:modelValue", event.target.value);
    },
    onFocus(event) {
      this.$emit("focus", event);
    },
    onBlur(event) {
      this.$emit("blur", event);
    },
    onKeydown(event) {
      this.$emit("keydown", event);
      if (event.key === "Enter") {
        this.$emit("enter", event);
      }
    },
    togglePassword() {
      this.showPassword = !this.showPassword;
      this.currentType = this.showPassword ? "text" : "password";
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
      this.$emit("update:modelValue", "");
      this.focus();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Eye = resolveComponent("Eye");
  const _component_EyeOff = resolveComponent("EyeOff");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_AlertCircle = resolveComponent("AlertCircle");
  _push(`<div${ssrRenderAttrs(mergeProps({
    class: ["relative", _ctx.$attrs.class]
  }, _attrs))}>`);
  if ($props.label) {
    _push(`<label${ssrRenderAttr("for", $props.id)} class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">${ssrInterpolate($props.label)} `);
    if ($props.required) {
      _push(`<span class="text-red-500 ml-1">*</span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</label>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="relative"><input${ssrRenderAttrs(mergeProps({
    id: $props.id,
    ref: "input"
  }, { ..._ctx.$attrs, class: null }, {
    class: [
      "w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200",
      $props.error ? "border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500" : "",
      $props.disabled ? "bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed" : "",
      $props.size === "sm" ? "px-3 py-2 text-xs" : "",
      $props.size === "lg" ? "px-4 py-4 text-base" : "",
      $props.type === "password" ? "pr-10" : ""
    ],
    type: $data.currentType,
    placeholder: $props.placeholder,
    value: $props.modelValue,
    disabled: $props.disabled,
    required: $props.required,
    autocomplete: $props.autocomplete
  }))}>`);
  if ($props.type === "password" && $props.showPasswordToggle) {
    _push(`<button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200">`);
    if (!$data.showPassword) {
      _push(ssrRenderComponent(_component_Eye, { class: "w-4 h-4" }, null, _parent));
    } else {
      _push(ssrRenderComponent(_component_EyeOff, { class: "w-4 h-4" }, null, _parent));
    }
    _push(`</button>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.isLoading) {
    _push(`<div class="absolute inset-y-0 right-3 flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.successMessage && !$props.error) {
    _push(`<div class="absolute inset-y-0 right-3 flex items-center">`);
    _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-500" }, null, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if ($props.helperText && !$props.error) {
    _push(`<div class="mt-2 text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate($props.helperText)}</div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.error) {
    _push(`<div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4 flex-shrink-0" }, null, _parent));
    _push(`<span>${ssrInterpolate($props.error)}</span></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.successMessage && !$props.error) {
    _push(`<div class="mt-2 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">`);
    _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 flex-shrink-0" }, null, _parent));
    _push(`<span>${ssrInterpolate($props.successMessage)}</span></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.showCharacterCount && $props.maxLength) {
    _push(`<div class="mt-1 text-xs text-slate-500 dark:text-slate-400 text-right">${ssrInterpolate($options.characterCount)}/${ssrInterpolate($props.maxLength)}</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/TextInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const TextInput = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  TextInput as T
};
