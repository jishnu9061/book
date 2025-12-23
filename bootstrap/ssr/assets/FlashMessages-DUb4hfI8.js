import { mergeProps, useSSRContext, resolveComponent } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { X, AlertCircle, CheckCircle } from "lucide-vue-next";
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$1 = {
  props: {
    name: String
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  if ($props.name === "white") {
    _push(`<img${ssrRenderAttrs(mergeProps({
      class: "logo w-[195px] h-auto",
      src: "/images/logo_white.png",
      alt: "Logo"
    }, _attrs))}>`);
  } else {
    _push(`<img${ssrRenderAttrs(mergeProps({
      class: "logo w-[195px] h-auto",
      src: "/images/logo.png",
      alt: "Logo"
    }, _attrs))}>`);
  }
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Logo.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Logo = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    CheckCircle,
    AlertCircle,
    X
  },
  data() {
    return {
      show: true
    };
  },
  watch: {
    "$page.props.flash": {
      handler() {
        this.show = true;
      },
      deep: true
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_X = resolveComponent("X");
  const _component_AlertCircle = resolveComponent("AlertCircle");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed top-28 z-[9999] left-0 right-0 max-w-lg m-auto" }, _attrs))}>`);
  if (_ctx.$page.props.flash && _ctx.$page.props.flash.success && $data.show) {
    _push(`<div class="mb-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 shadow-sm"><div class="flex items-start"><div class="flex-shrink-0">`);
    _push(ssrRenderComponent(_component_CheckCircle, { class: "w-5 h-5 text-green-600 dark:text-green-400" }, null, _parent));
    _push(`</div><div class="ml-3 flex-1"><p class="text-sm font-medium text-green-800 dark:text-green-200">${ssrInterpolate(_ctx.$page.props.flash.success)}</p></div><div class="ml-auto pl-3"><button class="inline-flex rounded-md bg-green-50 dark:bg-green-900/20 p-1.5 text-green-500 hover:bg-green-100 dark:hover:bg-green-900/40 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-green-900/20 transition-colors duration-200">`);
    _push(ssrRenderComponent(_component_X, { class: "w-4 h-4" }, null, _parent));
    _push(`</button></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if (_ctx.$page.props.flash && (_ctx.$page.props.flash.error || Object.keys(_ctx.$page.props.errors).length > 0) && $data.show) {
    _push(`<div class="mb-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 shadow-sm"><div class="flex items-start"><div class="flex-shrink-0">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-5 h-5 text-red-600 dark:text-red-400" }, null, _parent));
    _push(`</div><div class="ml-3 flex-1"><h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">${ssrInterpolate(Object.keys(_ctx.$page.props.errors).length > 0 ? "Validation Error" : "Error")}</h3><div class="text-sm text-red-700 dark:text-red-300">`);
    if (_ctx.$page.props.flash.error) {
      _push(`<p>${ssrInterpolate(_ctx.$page.props.flash.error)}</p>`);
    } else {
      _push(`<div>`);
      if (Object.keys(_ctx.$page.props.errors).length === 1) {
        _push(`<p class="mb-2"> There is one form error that needs to be corrected. </p>`);
      } else {
        _push(`<p class="mb-2"> There are ${ssrInterpolate(Object.keys(_ctx.$page.props.errors).length)} form errors that need to be corrected. </p>`);
      }
      _push(`<ul class="list-disc list-inside space-y-1"><!--[-->`);
      ssrRenderList(_ctx.$page.props.errors, (error, field) => {
        _push(`<li><span class="font-medium">${ssrInterpolate(field)}:</span> ${ssrInterpolate(error)}</li>`);
      });
      _push(`<!--]--></ul></div>`);
    }
    _push(`</div></div><div class="ml-auto pl-3"><button class="inline-flex rounded-md bg-red-50 dark:bg-red-900/20 p-1.5 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50 dark:focus:ring-offset-red-900/20 transition-colors duration-200">`);
    _push(ssrRenderComponent(_component_X, { class: "w-4 h-4" }, null, _parent));
    _push(`</button></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/FlashMessages.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const FlashMessages = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  FlashMessages as F,
  Logo as L,
  _export_sfc as _
};
