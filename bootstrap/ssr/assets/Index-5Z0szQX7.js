import { L as Layout } from "./Layout-DXQoZj0i.js";
import { P as Pagination } from "./Pagination-DjAJP-OE.js";
import { Head, Link } from "@inertiajs/vue3";
import moment from "moment";
import { User, ArrowRight, Calendar, CheckCircle, PenTool } from "lucide-vue-next";
import { resolveComponent, withCtx, createTextVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "./Dropdown-DNX6MmV_.js";
import "@popperjs/core";
import "axios";
import "laravel-vue-i18n";
import "./LoadingButton-Dl5SG5JI.js";
const _sfc_main = {
  layout: Layout,
  props: {
    posts: Object,
    title: String
  },
  components: {
    Pagination,
    Link,
    Head,
    PenTool,
    CheckCircle,
    Calendar,
    ArrowRight,
    User
  },
  created() {
    this.moment = moment;
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_PenTool = resolveComponent("PenTool");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_Calendar = resolveComponent("Calendar");
  const _component_ArrowRight = resolveComponent("ArrowRight");
  const _component_Link = resolveComponent("Link");
  const _component_User = resolveComponent("User");
  const _component_pagination = resolveComponent("pagination");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_Head, {
    title: _ctx.$t($props.title)
  }, null, _parent));
  _push(`<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800"><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse_at_center,white,transparent_70%)" })}"></div></div><div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div><div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div><div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div></div><div class="container relative z-10 py-20"><div class="text-center max-w-4xl mx-auto"><div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white/10 rounded-full border border-white/20 shadow-lg" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
  _push(ssrRenderComponent(_component_PenTool, { class: "w-4 h-4 text-yellow-300" }, null, _parent));
  _push(`<span>Latest News &amp; Insights</span></div><h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">${ssrInterpolate(_ctx.$t("Blogs & News"))}</h1><p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto"> Stay updated with the latest news, insights, and trends in customer support and technology. </p><div class="flex flex-wrap items-center justify-center gap-8 text-white/70"><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Expert Insights</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Industry News</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Weekly Updates</span></div></div></div></div><div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"><div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center"><div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div></div></div></section><section class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 left-0 w-full h-full opacity-5" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "background-position": "center", "background-repeat": "repeat" })}"></div><div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div><div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-16"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_PenTool, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` Latest Articles </div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl"> Our Latest Posts </h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto"> Discover insights, tips, and industry news from our expert team. </p></div>`);
  if ($props.posts.data.length) {
    _push(`<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16"><!--[-->`);
    ssrRenderList($props.posts.data, (post) => {
      _push(`<div class="group bg-white dark:bg-slate-800 rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden"><div class="relative overflow-hidden"><div class="aspect-w-16 aspect-h-9">`);
      if (post.image) {
        _push(`<img${ssrRenderAttr("src", post.image)}${ssrRenderAttr("alt", post.title)} class="w-full h-48 object-cover transition-transform duration-700 group-hover:scale-110">`);
      } else {
        _push(`<div class="w-full h-48 bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">`);
        _push(ssrRenderComponent(_component_PenTool, { class: "w-16 h-16 text-white/50" }, null, _parent));
        _push(`</div>`);
      }
      _push(`</div><div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div><div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-white bg-white/20 rounded-full" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
      _push(ssrRenderComponent(_component_Calendar, { class: "w-3 h-3" }, null, _parent));
      _push(` ${ssrInterpolate(_ctx.moment(post.updated_at).format("MMM DD, YYYY"))}</div><div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0"><div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
      _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 text-white" }, null, _parent));
      _push(`</div></div></div><div class="p-8"><h3 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">`);
      _push(ssrRenderComponent(_component_Link, {
        href: _ctx.route("blog.details", post.id),
        class: "hover:underline"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(post.title)}`);
          } else {
            return [
              createTextVNode(toDisplayString(post.title), 1)
            ];
          }
        }),
        _: 2
      }, _parent));
      _push(`</h3><p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">${ssrInterpolate(post.details.length < 120 ? post.details : post.details.substring(0, 120) + "...")}</p><div class="flex items-center justify-between"><a${ssrRenderAttr("href", _ctx.route("blog.details", post.id))} class="flex items-center text-primary-600 font-semibold group-hover:text-primary-700 transition-colors duration-300"><span class="text-sm">Read More</span>`);
      _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" }, null, _parent));
      _push(`</a><div class="flex items-center gap-2 text-slate-500 text-sm">`);
      _push(ssrRenderComponent(_component_User, { class: "w-4 h-4" }, null, _parent));
      _push(`<span>Admin</span></div></div></div></div>`);
    });
    _push(`<!--]--></div>`);
  } else {
    _push(`<div class="text-center py-16"><div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">`);
    _push(ssrRenderComponent(_component_PenTool, { class: "w-12 h-12 text-slate-400" }, null, _parent));
    _push(`</div><h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">No Blog Posts Found</h3><p class="text-slate-600 dark:text-slate-300 mb-8">Check back later for new articles and insights.</p></div>`);
  }
  _push(`<div class="flex justify-center">`);
  _push(ssrRenderComponent(_component_pagination, {
    class: "mt-6",
    links: $props.posts.links
  }, null, _parent));
  _push(`</div></div></section></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Landing/Blog/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
