import { v4 } from "uuid";
import { CheckCircle, AlertCircle, ChevronDown } from "lucide-vue-next";
import { resolveComponent, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderSlot, ssrRenderComponent } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
const _sfc_main = {
  inheritAttrs: false,
  components: {
    ChevronDown,
    AlertCircle,
    CheckCircle
  },
  props: {
    id: {
      type: String,
      default() {
        return `select-input-${v4()}`;
      }
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
      default: "md",
      validator: (value) => ["sm", "md", "lg"].includes(value)
    },
    modelValue: [String, Number, Boolean]
  },
  emits: ["update:modelValue", "focus", "blur", "change"],
  data() {
    return {
      selected: this.modelValue,
      isOpen: false
    };
  },
  watch: {
    selected(selected) {
      this.$emit("update:modelValue", selected);
    },
    modelValue(newValue) {
      if (newValue !== this.selected) {
        this.selected = newValue;
      }
    }
  },
  methods: {
    onFocus() {
      this.isOpen = true;
      this.$emit("focus");
    },
    onBlur() {
      this.isOpen = false;
      this.$emit("blur");
    },
    onChange(event) {
      this.$emit("change", event);
    },
    focus() {
      this.$refs.input.focus();
    },
    select() {
      this.$refs.input.select();
    },
    blur() {
      this.$refs.input.blur();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_ChevronDown = resolveComponent("ChevronDown");
  const _component_AlertCircle = resolveComponent("AlertCircle");
  const _component_CheckCircle = resolveComponent("CheckCircle");
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
  _push(`<div class="relative"><select${ssrRenderAttrs(mergeProps({
    id: $props.id,
    ref: "input"
  }, { ..._ctx.$attrs, class: null }, {
    class: [
      "w-full px-4 py-3 pr-10 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200 appearance-none cursor-pointer",
      $props.error ? "border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500" : "",
      $props.disabled ? "bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed" : "",
      $props.size === "sm" ? "px-3 py-2 text-xs" : "",
      $props.size === "lg" ? "px-4 py-4 text-base" : ""
    ],
    required: $props.required,
    disabled: $props.disabled
  }))}>`);
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  _push(`</select><div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">`);
  _push(ssrRenderComponent(_component_ChevronDown, {
    class: [
      "w-4 h-4 transition-transform duration-200",
      $data.isOpen ? "rotate-180" : "",
      $props.error ? "text-red-500 dark:text-red-400" : "text-slate-400 dark:text-slate-500",
      $props.disabled ? "text-slate-300 dark:text-slate-600" : ""
    ]
  }, null, _parent));
  _push(`</div>`);
  if ($props.isLoading) {
    _push(`<div class="absolute inset-y-0 right-8 flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div></div>`);
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
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SelectInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SelectInput = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SelectInput as S
};
