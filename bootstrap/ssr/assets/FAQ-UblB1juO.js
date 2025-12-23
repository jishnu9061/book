import { L as Layout } from "./Layout-DXQoZj0i.js";
import { Head } from "@inertiajs/vue3";
import sanitizeHtml from "sanitize-html";
import throttle from "lodash/throttle.js";
import pickBy from "lodash/pickBy.js";
import mapValues from "lodash/mapValues.js";
import { FileText, ArrowRight, ChevronDown, CheckCircle, Search, HelpCircle } from "lucide-vue-next";
import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "./Dropdown-DNX6MmV_.js";
import "@popperjs/core";
import "axios";
import "laravel-vue-i18n";
import "./LoadingButton-Dl5SG5JI.js";
import "moment";
const _sfc_main = {
  layout: Layout,
  components: {
    Head,
    HelpCircle,
    Search,
    CheckCircle,
    ChevronDown,
    ArrowRight,
    FileText
  },
  props: {
    faqs: Object,
    title: String,
    filters: Object
  },
  data() {
    return {
      form: {
        search: this.filters.search
      }
    };
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route("faq"), pickBy(this.form), { preserveState: true });
      }, 150)
    }
  },
  methods: {
    toggleActive(index) {
      this.faqs.data[index].active = !this.faqs.data[index].active;
    },
    sanitizeHtml,
    reset() {
      this.form = mapValues(this.form, () => null);
    }
  },
  created() {
    if (this.faqs.data.length) {
      this.faqs.data[0].active = true;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_HelpCircle = resolveComponent("HelpCircle");
  const _component_Search = resolveComponent("Search");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_ChevronDown = resolveComponent("ChevronDown");
  const _component_ArrowRight = resolveComponent("ArrowRight");
  const _component_FileText = resolveComponent("FileText");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_Head, { title: "FAQ" }, null, _parent));
  _push(`<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800"><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse_at_center,white,transparent_70%)" })}"></div></div><div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div><div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div><div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div></div><div class="container relative z-10 py-20"><div class="text-center max-w-4xl mx-auto"><div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white/10 rounded-full border border-white/20 shadow-lg" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
  _push(ssrRenderComponent(_component_HelpCircle, { class: "w-4 h-4 text-yellow-300" }, null, _parent));
  _push(`<span>Frequently Asked Questions</span></div><h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">${ssrInterpolate(_ctx.$t("Frequently Asked Questions"))}</h1><p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto"> Find answers to the most common questions about our platform, features, and services. </p><div class="max-w-2xl mx-auto mb-12"><div class="relative group"><div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-blue-500/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div><div class="relative">`);
  _push(ssrRenderComponent(_component_Search, { class: "w-6 h-6 absolute left-6 top-1/2 transform -translate-y-1/2 text-slate-400 group-hover:text-primary-400 transition-colors duration-300" }, null, _parent));
  _push(`<input type="search"${ssrRenderAttr("value", $data.form.search)} class="w-full pl-16 pr-6 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 text-lg"${ssrRenderAttr("placeholder", _ctx.$t("Search your query in the FAQ items..."))} style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}"></div></div></div><div class="flex flex-wrap items-center justify-center gap-8 text-white/70"><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Quick Answers</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Updated Daily</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Expert Verified</span></div></div></div></div><div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"><div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center"><div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div></div></div></section><section class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 left-0 w-full h-full opacity-5" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "background-position": "center", "background-repeat": "repeat" })}"></div><div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div><div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-16"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_HelpCircle, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` FAQ Section </div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl"> Common Questions </h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto"> Browse through our frequently asked questions to find quick answers to your queries. </p></div><div class="max-w-4xl mx-auto"><div class="space-y-4"><!--[-->`);
  ssrRenderList($props.faqs.data, (faq, fi) => {
    _push(`<div class="group bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-slate-700 overflow-hidden"><button type="button" class="${ssrRenderClass([{ "bg-primary/5 dark:bg-primary/10": faq.active }, "w-full p-6 text-left focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all duration-300"])}"><div class="flex items-center justify-between"><div class="flex items-center gap-4 flex-1"><div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">`);
    _push(ssrRenderComponent(_component_HelpCircle, { class: "w-5 h-5 text-white" }, null, _parent));
    _push(`</div><h3 class="text-lg font-semibold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">${ssrInterpolate(faq.name)}</h3></div><div class="flex-shrink-0 ml-4">`);
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-6 h-6 text-slate-500 transition-all duration-300", { "rotate-180": faq.active }]
    }, null, _parent));
    _push(`</div></div></button><div class="${ssrRenderClass([faq.active ? "max-h-96 opacity-100" : "max-h-0 opacity-0", "overflow-hidden transition-all duration-300 ease-in-out"])}"><div class="px-6 pb-6"><div class="pl-14"><div class="prose prose-slate dark:prose-invert max-w-none"><div class="text-slate-600 dark:text-slate-300 leading-relaxed">${$options.sanitizeHtml(faq.details) ?? ""}</div></div></div></div></div></div>`);
  });
  _push(`<!--]--></div></div><div class="text-center mt-16"><div class="bg-gradient-to-r from-primary-600 to-blue-600 rounded-3xl p-12 text-white"><h3 class="text-3xl font-bold mb-4">Still Have Questions?</h3><p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto"> Can&#39;t find what you&#39;re looking for? Our support team is here to help you. </p><div class="flex flex-col sm:flex-row items-center justify-center gap-4"><a href="/contact" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-primary-600 font-semibold rounded-2xl hover:bg-white/90 transition-all duration-300 hover:scale-105 hover:shadow-xl"><span>Contact Support</span>`);
  _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5" }, null, _parent));
  _push(`</a><a href="/kb" class="inline-flex items-center gap-3 px-8 py-4 bg-white/20 text-white font-semibold rounded-2xl hover:bg-white/30 transition-all duration-300 hover:scale-105" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}"><span>Browse Knowledge Base</span>`);
  _push(ssrRenderComponent(_component_FileText, { class: "w-5 h-5" }, null, _parent));
  _push(`</a></div></div></div></div></section></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Landing/FAQ.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const FAQ = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  FAQ as default
};
