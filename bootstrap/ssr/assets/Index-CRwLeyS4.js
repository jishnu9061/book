import { L as Layout } from "./Layout-DwqqF5bk.js";
import { ssrRenderAttrs } from "vue/server-renderer";
import { useSSRContext } from "vue";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "./Dropdown-DNX6MmV_.js";
import "@popperjs/core";
import "@inertiajs/vue3";
import "lucide-vue-next";
import "moment";
import "laravel-vue-i18n";
import "axios";
import "@heroicons/vue/24/outline";
const _sfc_main = {
  metaInfo: { title: "Reports" },
  layout: Layout
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(_attrs)}><h1 class="mb-8 font-bold text-3xl">Reports</h1></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Reports/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
