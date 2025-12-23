import { v4 } from "uuid";
import { AlertCircle, X, Search } from "lucide-vue-next";
import { resolveComponent, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderComponent, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
const _sfc_main = {
  inheritAttrs: false,
  components: {
    Search,
    X,
    AlertCircle
  },
  props: {
    placeholder: {
      type: String,
      default: ""
    },
    onInput: {
      type: Function
    },
    items: {
      type: Array,
      default: () => []
    },
    id: {
      type: String,
      default() {
        return `select-input-filter-${v4()}`;
      }
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
  emits: ["update:modelValue", "focus", "blur", "clear"],
  data() {
    return {
      selectedValue: "",
      selected: this.modelValue,
      isListVisible: false,
      currentIndex: -1
    };
  },
  watch: {
    selected(selected) {
      this.$emit("update:modelValue", selected);
    },
    modelValue(newValue) {
      if (newValue !== this.selected) {
        this.selected = newValue;
        const selectedItem = this.items.find((item) => item.id === newValue);
        if (selectedItem) {
          this.selectedValue = selectedItem.name;
        } else {
          this.selectedValue = "";
        }
      }
    },
    items: {
      handler() {
        this.currentIndex = -1;
      },
      deep: true
    }
  },
  mounted() {
    document.addEventListener("click", this.close);
    if (this.modelValue) {
      const selectedItem = this.items.find((item) => item.id === this.modelValue);
      if (selectedItem) {
        this.selectedValue = selectedItem.name;
      }
    }
  },
  beforeUnmount() {
    document.removeEventListener("click", this.close);
  },
  methods: {
    close(e) {
      if (!this.$el.contains(e.target)) {
        this.isListVisible = false;
        this.currentIndex = -1;
      }
    },
    onFocus() {
      this.isListVisible = true;
      this.$emit("focus");
    },
    onBlur() {
      this.$emit("blur");
    },
    selectItem(item) {
      this.$refs.input.value = item.name;
      this.selected = item.id;
      this.selectedValue = item.name;
      this.isListVisible = false;
      this.currentIndex = -1;
    },
    clearSelection() {
      this.$refs.input.value = "";
      this.selected = null;
      this.selectedValue = "";
      this.isListVisible = false;
      this.currentIndex = -1;
      this.$emit("clear");
      this.$emit("update:modelValue", null);
    },
    onArrowDown() {
      if (!this.isListVisible) {
        this.isListVisible = true;
        return;
      }
      if (this.currentIndex < this.items.length - 1) {
        this.currentIndex++;
      } else {
        this.currentIndex = 0;
      }
    },
    onArrowUp() {
      if (!this.isListVisible) {
        this.isListVisible = true;
        return;
      }
      if (this.currentIndex > 0) {
        this.currentIndex--;
      } else {
        this.currentIndex = this.items.length - 1;
      }
    },
    selectCurrentSelection() {
      if (this.isListVisible && this.currentIndex >= 0 && this.items[this.currentIndex]) {
        this.selectItem(this.items[this.currentIndex]);
      }
    },
    closeDropdown() {
      this.isListVisible = false;
      this.currentIndex = -1;
    },
    focus() {
      this.$refs.input.focus();
    },
    select() {
      this.$refs.input.select();
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Search = resolveComponent("Search");
  const _component_X = resolveComponent("X");
  const _component_AlertCircle = resolveComponent("AlertCircle");
  _push(`<div${ssrRenderAttrs(mergeProps({
    class: ["relative", _ctx.$attrs.class],
    ref: "sel__filter"
  }, _attrs))}>`);
  if ($props.label) {
    _push(`<label${ssrRenderAttr("for", $props.id)} class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">${ssrInterpolate($props.label)}</label>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="relative"><div class="relative"><input${ssrRenderAttrs(mergeProps({
    id: $props.id,
    ref: "input",
    class: [
      "w-full px-4 py-3 pl-4 pr-10 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200",
      $props.error ? "border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500" : "",
      $data.isListVisible ? "rounded-b-none border-b-0" : ""
    ],
    type: "text"
  }, { ..._ctx.$attrs, class: null }, {
    placeholder: $props.placeholder,
    value: $data.selectedValue,
    autocomplete: "off"
  }))}><div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">`);
  _push(ssrRenderComponent(_component_Search, { class: "w-4 h-4 text-slate-400 dark:text-slate-500" }, null, _parent));
  _push(`</div>`);
  if ($data.selectedValue && !$data.isListVisible) {
    _push(`<button class="absolute inset-y-0 right-8 flex items-center pr-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors duration-200">`);
    _push(ssrRenderComponent(_component_X, { class: "w-4 h-4" }, null, _parent));
    _push(`</button>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if ($data.isListVisible && $props.items.length) {
    _push(`<div class="absolute z-50 w-full mt-0 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 border-t-0 rounded-b-lg shadow-lg max-h-60 overflow-y-auto"><div class="py-1"><!--[-->`);
    ssrRenderList($props.items, (item, index) => {
      _push(`<div class="${ssrRenderClass([
        "px-4 py-3 text-sm cursor-pointer transition-colors duration-150 flex items-center justify-between group",
        $data.currentIndex === index ? "bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300" : "text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-600"
      ])}"><div class="flex items-center gap-3"><div class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-blue-500 transition-colors duration-200"></div><span class="font-medium">${ssrInterpolate(item.name)}</span></div>`);
      if (item.email) {
        _push(`<div class="text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(item.email)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    });
    _push(`<!--]--></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($data.isListVisible && $props.items.length === 0 && $data.selectedValue) {
    _push(`<div class="absolute z-50 w-full mt-0 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 border-t-0 rounded-b-lg shadow-lg"><div class="px-4 py-6 text-center">`);
    _push(ssrRenderComponent(_component_Search, { class: "w-8 h-8 mx-auto mb-2 text-slate-400 dark:text-slate-500" }, null, _parent));
    _push(`<p class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("No results found"))}</p><p class="text-xs text-slate-400 dark:text-slate-500 mt-1">${ssrInterpolate(_ctx.$t("Try a different search term"))}</p></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if ($props.error) {
    _push(`<div class="mt-2 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4 flex-shrink-0" }, null, _parent));
    _push(`<span>${ssrInterpolate($props.error)}</span></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($props.isLoading) {
    _push(`<div class="absolute inset-y-0 right-3 flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-2 border-slate-300 border-t-blue-600"></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/SelectInputFilter.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const SelectInputFilter = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  SelectInputFilter as S
};
