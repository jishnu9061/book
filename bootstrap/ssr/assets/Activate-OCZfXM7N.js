import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
const _sfc_main = {
  __name: "Activate",
  __ssrInlineRender: true,
  props: {
    errors: Object,
    message: String
  },
  setup(__props) {
    const props = __props;
    const form = useForm({
      purchase_code: ""
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "min-h-screen flex items-center justify-center bg-gray-100" }, _attrs))}><div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md"><h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Activate Your Application</h2>`);
      if (props.message) {
        _push(`<div class="mb-4 p-3 rounded-md bg-red-100 text-red-700">${ssrInterpolate(props.message)}</div>`);
      } else {
        _push(`<!---->`);
      }
      if (unref(form).hasErrors) {
        _push(`<div class="mb-4 p-3 rounded-md bg-red-100 text-red-700"><!--[-->`);
        ssrRenderList(unref(form).errors, (error) => {
          _push(`<div>${ssrInterpolate(error)}</div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<form><div><label for="purchase_code" class="block text-sm font-medium text-gray-700"> Envato Purchase Code </label><input${ssrRenderAttr("value", unref(form).purchase_code)} id="purchase_code" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"></div><div class="mt-6"><button type="submit"${ssrIncludeBooleanAttr(unref(form).processing) ? " disabled" : ""} class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"> Activate License </button></div></form></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/License/Activate.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
