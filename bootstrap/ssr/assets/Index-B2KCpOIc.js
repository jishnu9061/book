import { Link, Head } from "@inertiajs/vue3";
import { L as Layout } from "./Layout-DwqqF5bk.js";
import { I as Icon } from "./Dropdown-DNX6MmV_.js";
import { ArrowRight, TrendingUp, ChevronRight, UserPlus, BarChart3, Users, Tag, Building2, AlertTriangle, CheckCircle, Clock, PlusCircle } from "lucide-vue-next";
import { resolveComponent, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "moment";
import "laravel-vue-i18n";
import "axios";
import "@heroicons/vue/24/outline";
import "@popperjs/core";
const _sfc_main = {
  metaInfo: { title: "Dashboard" },
  components: {
    Head,
    Icon,
    Link,
    PlusCircle,
    Clock,
    CheckCircle,
    AlertTriangle,
    Building2,
    Tag,
    Users,
    BarChart3,
    UserPlus,
    ChevronRight,
    TrendingUp,
    ArrowRight
  },
  layout: Layout,
  props: {
    auth: Object,
    entries: Array,
    chart_line: Object,
    api_key: String,
    new_tickets: Number,
    total_tickets: Number,
    un_assigned_tickets: Number,
    opened_tickets: Number,
    closed_tickets: Number,
    first_response: Array,
    top_creators: Array,
    last_response: Array,
    top_departments: Array,
    top_types: Array,
    total_customer: Number,
    total_contacts: Number
  },
  data() {
    return {
      errors: [],
      loading: false,
      firstResponse: [],
      lastResponse: [],
      months: []
    };
  },
  created() {
    for (let i = 0; i < this.first_response.length; i++) {
      if (i % 2 === 0) {
        this.firstResponse = [...this.firstResponse, [this.first_response[i], this.first_response[i + 1]]];
      }
    }
    for (let i = 0; i < this.last_response.length; i++) {
      if (i % 2 === 0) {
        this.lastResponse = [...this.lastResponse, [this.last_response[i], this.last_response[i + 1]]];
      }
    }
    this.months = this.chart_line.previousMonths.map((m) => {
      return { "month": m, "value": this.chart_line.months[m] ? this.chart_line.months[m] * 100 / this.chart_line.total + "%" : "0%" };
    });
  },
  methods: {
    goToLink(link) {
      window.location.href = link;
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  var _a, _b, _c, _d, _e, _f, _g, _h, _i;
  const _component_Head = resolveComponent("Head");
  const _component_BarChart3 = resolveComponent("BarChart3");
  const _component_Clock = resolveComponent("Clock");
  const _component_TrendingUp = resolveComponent("TrendingUp");
  const _component_PlusCircle = resolveComponent("PlusCircle");
  const _component_ArrowRight = resolveComponent("ArrowRight");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_AlertTriangle = resolveComponent("AlertTriangle");
  const _component_Building2 = resolveComponent("Building2");
  const _component_vc_donut = resolveComponent("vc-donut");
  const _component_Tag = resolveComponent("Tag");
  const _component_Users = resolveComponent("Users");
  const _component_ChevronRight = resolveComponent("ChevronRight");
  const _component_UserPlus = resolveComponent("UserPlus");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "min-h-screen bg-slate-50 dark:bg-slate-900" }, _attrs))}>`);
  _push(ssrRenderComponent(_component_Head, {
    title: _ctx.$t("Dashboard")
  }, null, _parent));
  _push(`<div class="relative mb-8 overflow-hidden"><div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-3xl p-8 lg:p-12 text-white overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-primary-500/20 via-transparent to-primary-900/20"></div><div class="absolute inset-0 bg-black/5"></div><div class="absolute top-4 right-4 w-32 h-32 bg-white/5 rounded-full blur-xl animate-pulse"></div><div class="absolute bottom-8 left-8 w-24 h-24 bg-primary-300/10 rounded-full blur-lg animate-bounce" style="${ssrRenderStyle({ "animation-delay": "1s" })}"></div><div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/10 rounded-full blur-md animate-pulse" style="${ssrRenderStyle({ "animation-delay": "2s" })}"></div><div class="relative z-10"><div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8"><div class="flex-1"><div class="flex items-center gap-3 mb-4"><div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_BarChart3, { class: "w-6 h-6 text-white" }, null, _parent));
  _push(`</div><div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full"><span class="text-sm font-medium">${ssrInterpolate(_ctx.$t("Dashboard Overview"))}</span></div></div><h1 class="text-4xl lg:text-5xl font-bold mb-4 bg-gradient-to-r from-white to-primary-100 bg-clip-text text-transparent">${ssrInterpolate(_ctx.$t("Welcome back"))}, ${ssrInterpolate($props.auth.user.first_name)}! </h1><p class="text-xl text-primary-100 mb-6 max-w-2xl">${ssrInterpolate(_ctx.$t("Here's what's happening with your support tickets today"))}</p><div class="flex flex-wrap gap-4"><div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full"><div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div><span class="text-sm font-medium">${ssrInterpolate(_ctx.$t("System Online"))}</span></div><div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full">`);
  _push(ssrRenderComponent(_component_Clock, { class: "w-4 h-4" }, null, _parent));
  _push(`<span class="text-sm font-medium">${ssrInterpolate((/* @__PURE__ */ new Date()).toLocaleDateString())}</span></div></div></div><div class="lg:text-right"><div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20"><div class="text-center lg:text-right"><p class="text-primary-200 text-sm font-medium mb-2">${ssrInterpolate(_ctx.$t("Total Tickets"))}</p><p class="text-5xl lg:text-6xl font-bold mb-2 bg-gradient-to-r from-white to-primary-100 bg-clip-text text-transparent">${ssrInterpolate($props.total_tickets)}</p><div class="flex items-center justify-center lg:justify-end gap-2">`);
  _push(ssrRenderComponent(_component_TrendingUp, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm text-green-400 font-medium">${ssrInterpolate(_ctx.$t("Active System"))}</span></div></div></div></div></div></div><div class="absolute top-0 right-0 w-96 h-96 opacity-5"><svg viewBox="0 0 400 400" fill="currentColor" class="w-full h-full"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"></path></pattern></defs><rect width="400" height="400" fill="url(#grid)"></rect></svg></div></div></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"><div class="group cursor-pointer"><div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-blue-500/10 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent dark:from-blue-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="${ssrRenderStyle({ "padding": "1px" })}"><div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div></div><div class="relative z-10"><div class="flex items-center justify-between mb-4"><div class="flex items-center gap-3"><div class="relative w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
  _push(ssrRenderComponent(_component_PlusCircle, { class: "w-6 h-6 text-blue-600 dark:text-blue-400" }, null, _parent));
  _push(`<div class="absolute inset-0 bg-blue-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">${ssrInterpolate(_ctx.$t("New Tickets"))}</p><p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">${ssrInterpolate($props.new_tickets)}</p></div></div><div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">`);
  _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 text-blue-500" }, null, _parent));
  _push(`</div></div><div class="space-y-2"><div class="flex items-center justify-between text-xs"><span class="text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Progress"))}</span><span class="font-semibold text-blue-600 dark:text-blue-400">${ssrInterpolate(parseInt($props.new_tickets * 100 / $props.total_tickets) || 0)}% </span></div><div class="relative"><div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden"><div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden" style="${ssrRenderStyle({ width: `${Math.min($props.new_tickets * 100 / $props.total_tickets, 100)}%` })}"><div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div></div></div></div></div></div></div></div><div class="group cursor-pointer"><div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-orange-500/10 hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-transparent dark:from-orange-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="${ssrRenderStyle({ "padding": "1px" })}"><div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div></div><div class="relative z-10"><div class="flex items-center justify-between mb-4"><div class="flex items-center gap-3"><div class="relative w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
  _push(ssrRenderComponent(_component_Clock, { class: "w-6 h-6 text-orange-600 dark:text-orange-400" }, null, _parent));
  _push(`<div class="absolute inset-0 bg-orange-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">${ssrInterpolate(_ctx.$t("Open Tickets"))}</p><p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">${ssrInterpolate($props.opened_tickets)}</p></div></div><div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">`);
  _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 text-orange-500" }, null, _parent));
  _push(`</div></div><div class="space-y-2"><div class="flex items-center justify-between text-xs"><span class="text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Progress"))}</span><span class="font-semibold text-orange-600 dark:text-orange-400">${ssrInterpolate(parseInt($props.opened_tickets * 100 / $props.total_tickets) || 0)}% </span></div><div class="relative"><div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden"><div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden" style="${ssrRenderStyle({ width: `${Math.min($props.opened_tickets * 100 / $props.total_tickets, 100)}%` })}"><div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div></div></div></div></div></div></div></div><div class="group cursor-pointer"><div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-green-500/10 hover:border-green-300 dark:hover:border-green-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent dark:from-green-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-green-500 to-green-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="${ssrRenderStyle({ "padding": "1px" })}"><div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div></div><div class="relative z-10"><div class="flex items-center justify-between mb-4"><div class="flex items-center gap-3"><div class="relative w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-6 h-6 text-green-600 dark:text-green-400" }, null, _parent));
  _push(`<div class="absolute inset-0 bg-green-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">${ssrInterpolate(_ctx.$t("Closed Tickets"))}</p><p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">${ssrInterpolate($props.closed_tickets)}</p></div></div><div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">`);
  _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 text-green-500" }, null, _parent));
  _push(`</div></div><div class="space-y-2"><div class="flex items-center justify-between text-xs"><span class="text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Progress"))}</span><span class="font-semibold text-green-600 dark:text-green-400">${ssrInterpolate(parseInt($props.closed_tickets * 100 / $props.total_tickets) || 0)}% </span></div><div class="relative"><div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden"><div class="bg-gradient-to-r from-green-500 to-green-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden" style="${ssrRenderStyle({ width: `${Math.min($props.closed_tickets * 100 / $props.total_tickets, 100)}%` })}"><div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div></div></div></div></div></div></div></div>`);
  if ($props.auth.user.role.slug !== "customer") {
    _push(`<div class="group cursor-pointer"><div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-red-500/10 hover:border-red-300 dark:hover:border-red-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-red-50/50 to-transparent dark:from-red-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-red-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="${ssrRenderStyle({ "padding": "1px" })}"><div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div></div><div class="relative z-10"><div class="flex items-center justify-between mb-4"><div class="flex items-center gap-3"><div class="relative w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
    _push(ssrRenderComponent(_component_AlertTriangle, { class: "w-6 h-6 text-red-600 dark:text-red-400" }, null, _parent));
    _push(`<div class="absolute inset-0 bg-red-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300">${ssrInterpolate(_ctx.$t("Unassigned Tickets"))}</p><p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300">${ssrInterpolate($props.un_assigned_tickets)}</p></div></div><div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 text-red-500" }, null, _parent));
    _push(`</div></div><div class="space-y-2"><div class="flex items-center justify-between text-xs"><span class="text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Progress"))}</span><span class="font-semibold text-red-600 dark:text-red-400">${ssrInterpolate(parseInt($props.un_assigned_tickets * 100 / $props.total_tickets) || 0)}% </span></div><div class="relative"><div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden"><div class="bg-gradient-to-r from-red-500 to-red-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden" style="${ssrRenderStyle({ width: `${Math.min($props.un_assigned_tickets * 100 / $props.total_tickets, 100)}%` })}"><div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div></div></div></div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if (["admin", "manager"].includes((_c = (_b = (_a = $props.auth) == null ? void 0 : _a.user) == null ? void 0 : _b.role) == null ? void 0 : _c.slug)) {
    _push(`<div class="mb-8"><div class="flex items-center gap-4 mb-8"><div class="relative"><div class="w-2 h-10 bg-gradient-to-b from-primary-500 to-primary-600 rounded-full"></div><div class="absolute inset-0 w-2 h-10 bg-gradient-to-b from-primary-400 to-primary-500 rounded-full blur-sm opacity-50"></div></div><div><h2 class="text-3xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Analytics Overview"))}</h2><p class="text-slate-600 dark:text-slate-400 mt-1">${ssrInterpolate(_ctx.$t("Comprehensive insights into your support operations"))}</p></div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-8"><div class="group relative"><div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-purple-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 group-hover:scale-105"><div class="flex items-center gap-4 mb-6"><div class="relative w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
    _push(ssrRenderComponent(_component_Building2, { class: "w-7 h-7 text-purple-600 dark:text-purple-400" }, null, _parent));
    _push(`<div class="absolute inset-0 bg-purple-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><h3 class="text-xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Tickets by Department"))}</h3><p class="text-sm text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Distribution across teams"))}</p></div></div><div class="flex justify-center">`);
    _push(ssrRenderComponent(_component_vc_donut, {
      background: "transparent",
      foreground: "grey",
      size: 220,
      unit: "px",
      thickness: 35,
      "has-legend": "",
      "legend-placement": "bottom",
      sections: $props.top_departments,
      total: 100,
      "start-angle": 0,
      "auto-adjust-text-size": true
    }, null, _parent));
    _push(`</div></div></div><div class="group relative"><div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-indigo-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 group-hover:scale-105"><div class="flex items-center gap-4 mb-6"><div class="relative w-14 h-14 bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
    _push(ssrRenderComponent(_component_Tag, { class: "w-7 h-7 text-indigo-600 dark:text-indigo-400" }, null, _parent));
    _push(`<div class="absolute inset-0 bg-indigo-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><h3 class="text-xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Tickets by Type"))}</h3><p class="text-sm text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Issue categorization"))}</p></div></div><div class="flex justify-center">`);
    _push(ssrRenderComponent(_component_vc_donut, {
      background: "transparent",
      foreground: "grey",
      size: 220,
      unit: "px",
      thickness: 35,
      "has-legend": "",
      "legend-placement": "bottom",
      sections: $props.top_types,
      total: 100,
      "start-angle": 0,
      "auto-adjust-text-size": true
    }, null, _parent));
    _push(`</div></div></div><div class="group relative"><div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-emerald-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 group-hover:scale-105"><div class="flex items-center gap-4 mb-6"><div class="relative w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/30 dark:to-emerald-800/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">`);
    _push(ssrRenderComponent(_component_Users, { class: "w-7 h-7 text-emerald-600 dark:text-emerald-400" }, null, _parent));
    _push(`<div class="absolute inset-0 bg-emerald-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div></div><div><h3 class="text-xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Top Ticket Creators"))}</h3><p class="text-sm text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Most active users"))}</p></div></div><div class="flex justify-center">`);
    _push(ssrRenderComponent(_component_vc_donut, {
      background: "transparent",
      foreground: "grey",
      size: 220,
      unit: "px",
      thickness: 35,
      "has-legend": "",
      "legend-placement": "bottom",
      sections: $props.top_creators,
      total: 100,
      "start-angle": 0,
      "auto-adjust-text-size": true
    }, null, _parent));
    _push(`</div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if (["admin", "manager"].includes((_f = (_e = (_d = $props.auth) == null ? void 0 : _d.user) == null ? void 0 : _e.role) == null ? void 0 : _f.slug)) {
    _push(`<div class="mb-8"><div class="flex items-center gap-3 mb-6"><div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div><h2 class="text-2xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Performance Metrics"))}</h2></div><div class="grid grid-cols-1 lg:grid-cols-12 gap-6"><div class="lg:col-span-8"><div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6"><div class="flex items-center justify-between mb-6"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_BarChart3, { class: "w-5 h-5 text-blue-600 dark:text-blue-400" }, null, _parent));
    _push(`</div><h3 class="text-lg font-semibold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Ticket History"))}</h3></div><div class="text-right"><p class="text-2xl font-bold text-slate-900 dark:text-white">${ssrInterpolate($props.chart_line.this_month)}</p><p class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("this month"))}</p><p class="text-xs text-slate-400 dark:text-slate-500">${ssrInterpolate(_ctx.$t("last month"))}: ${ssrInterpolate($props.chart_line.last_month)}</p></div></div>`);
    if ($data.months.length) {
      _push(`<div class="flex items-end justify-between h-32 gap-2"><!--[-->`);
      ssrRenderList($data.months, (cl, index) => {
        _push(`<div class="flex flex-col items-center flex-1"><div class="w-full bg-slate-200 dark:bg-slate-700 rounded-t-lg relative group"><div class="bg-gradient-to-t from-primary-500 to-primary-400 rounded-t-lg transition-all duration-500 hover:from-primary-600 hover:to-primary-500" style="${ssrRenderStyle({ height: cl.value })}"${ssrRenderAttr("title", `${cl.month}: ${cl.value}`)}></div></div><span class="text-xs text-slate-500 dark:text-slate-400 mt-2 text-center">${ssrInterpolate(cl.month)}</span></div>`);
      });
      _push(`<!--]--></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div><div class="lg:col-span-4"><div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6"><div class="flex items-center gap-3 mb-6"><div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_Clock, { class: "w-5 h-5 text-orange-600 dark:text-orange-400" }, null, _parent));
    _push(`</div><h3 class="text-lg font-semibold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Response Times"))}</h3></div><div class="mb-6"><div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("First Response"))}</span><span class="text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Average"))}</span></div><div class="flex items-center gap-2">`);
    if ($data.firstResponse.length) {
      _push(`<!--[-->`);
      ssrRenderList($data.firstResponse, (fr, fri) => {
        _push(`<div class="text-center"><span class="text-2xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(fr[0])}</span><span class="text-xs text-slate-500 dark:text-slate-400 block">${ssrInterpolate(_ctx.$t(fr[1]))}</span></div>`);
      });
      _push(`<!--]-->`);
    } else {
      _push(`<div class="text-center"><span class="text-2xl font-bold text-slate-500 dark:text-slate-400">0</span></div>`);
    }
    _push(`</div></div><div><div class="flex items-center justify-between mb-2"><span class="text-sm font-medium text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Last Response"))}</span><span class="text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Average"))}</span></div><div class="flex items-center gap-2">`);
    if ($data.lastResponse.length) {
      _push(`<!--[-->`);
      ssrRenderList($data.lastResponse, (fr, fri) => {
        _push(`<div class="text-center"><span class="text-2xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(fr[0])}</span><span class="text-xs text-slate-500 dark:text-slate-400 block">${ssrInterpolate(_ctx.$t(fr[1]))}</span></div>`);
      });
      _push(`<!--]-->`);
    } else {
      _push(`<div class="text-center"><span class="text-2xl font-bold text-slate-500 dark:text-slate-400">0</span></div>`);
    }
    _push(`</div></div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if (["admin", "manager"].includes((_i = (_h = (_g = $props.auth) == null ? void 0 : _g.user) == null ? void 0 : _h.role) == null ? void 0 : _i.slug)) {
    _push(`<div class="mb-8"><div class="flex items-center gap-3 mb-6"><div class="w-1 h-8 bg-gradient-to-b from-amber-500 to-amber-600 rounded-full"></div><h2 class="text-2xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("Quick Stats"))}</h2></div><div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div class="group cursor-pointer"><div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 group-hover:scale-105"><div class="flex items-center justify-between"><div class="flex items-center gap-4"><div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_Users, { class: "w-6 h-6 text-indigo-600 dark:text-indigo-400" }, null, _parent));
    _push(`</div><div><p class="text-sm font-medium text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Total Customers"))}</p><p class="text-3xl font-bold text-slate-900 dark:text-white">${ssrInterpolate($props.total_customer)}</p></div></div>`);
    _push(ssrRenderComponent(_component_ChevronRight, { class: "w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors duration-200" }, null, _parent));
    _push(`</div></div></div><div class="group cursor-pointer"><div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg hover:border-cyan-300 dark:hover:border-cyan-600 transition-all duration-300 group-hover:scale-105"><div class="flex items-center justify-between"><div class="flex items-center gap-4"><div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_UserPlus, { class: "w-6 h-6 text-cyan-600 dark:text-cyan-400" }, null, _parent));
    _push(`</div><div><p class="text-sm font-medium text-slate-600 dark:text-slate-400">${ssrInterpolate(_ctx.$t("Total Contacts"))}</p><p class="text-3xl font-bold text-slate-900 dark:text-white">${ssrInterpolate($props.total_contacts)}</p></div></div>`);
    _push(ssrRenderComponent(_component_ChevronRight, { class: "w-5 h-5 text-slate-400 group-hover:text-cyan-500 transition-colors duration-200" }, null, _parent));
    _push(`</div></div></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  if ($data.loading) {
    _push(`<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"><div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-2xl"><div class="flex flex-col items-center gap-4"><div class="relative"><div class="w-16 h-16 border-4 border-primary-200 dark:border-primary-800 rounded-full"></div><div class="absolute top-0 left-0 w-16 h-16 border-4 border-primary-600 border-t-transparent rounded-full animate-spin"></div></div><p class="text-slate-600 dark:text-slate-400 font-medium">${ssrInterpolate(_ctx.$t("Loading dashboard data..."))}</p></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Dashboard/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
