import { Head, Link } from "@inertiajs/vue3";
import { I as Icon } from "./Dropdown-DNX6MmV_.js";
import pickBy from "lodash/pickBy.js";
import { L as Layout } from "./Layout-DwqqF5bk.js";
import throttle from "lodash/throttle.js";
import mapValues from "lodash/mapValues.js";
import { P as Pagination } from "./Pagination-DvKmvDq4.js";
import { S as SelectInput } from "./SelectInput-BK_r_uc2.js";
import { S as SearchInput } from "./SearchInput-CFu_dYUb.js";
import { S as SelectInputFilter } from "./SelectInputFilter-CK4BgMgJ.js";
import moment from "moment";
import { f as formatDate } from "./dateFormatter-mm75OmPO.js";
import axios from "axios";
import { UserX, Clock, CheckCircle, AlertTriangle, Bookmark, Filter, List, Grid3X3, X, ChevronRight, ChevronDown, ChevronUp, Calendar, UserCheck, User, Download, Upload, Plus, Ticket } from "lucide-vue-next";
import { resolveComponent, mergeProps, withCtx, createVNode, toDisplayString, resolveDynamicComponent, createBlock, openBlock, Fragment, renderList, createCommentVNode, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderClass, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList, ssrRenderVNode } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "@popperjs/core";
import "laravel-vue-i18n";
import "@heroicons/vue/24/outline";
import "uuid";
const _sfc_main = {
  metaInfo: { title: "Tickets" },
  components: {
    SearchInput,
    Icon,
    Link,
    Head,
    Pagination,
    SelectInputFilter,
    SelectInput,
    Ticket,
    Plus,
    Upload,
    Download,
    User,
    UserCheck,
    Calendar,
    Clock,
    ChevronUp,
    ChevronDown,
    ChevronRight,
    X,
    Grid3X3,
    List,
    Filter,
    Bookmark,
    AlertTriangle,
    CheckCircle,
    ClockIcon: Clock,
    UserX
  },
  layout: Layout,
  props: {
    filters: Object,
    tickets: Object,
    assignees: Array,
    auth: Object,
    title: String,
    priorities: Array,
    statuses: Array,
    types: Array,
    categories: Array,
    departments: Array,
    hidden_fields: Object
  },
  remember: "form",
  data() {
    return {
      headers: [
        { name: "Key", value: "id", sort: true },
        { name: "Subject", value: "subject", sort: true },
        { name: "Priority", value: "priority_id", sort: true },
        { name: "Status", value: "status_id", sort: true },
        { name: "Date", value: "created_at", sort: true },
        { name: "Updated", value: "updated_at", sort: true }
      ],
      user_access: this.$page.props.auth.user.access,
      viewMode: "list",
      // 'list' or 'grid'
      activeQuickFilter: null,
      clients: [],
      form: {
        search: this.filters.search,
        limit: this.filters.limit ?? 10,
        customer_id: this.filters.customer_id,
        field: this.filters.field,
        direction: this.filters.direction,
        priority_id: this.filters.priority_id ?? null,
        status_id: this.filters.status_id ?? null,
        type_id: this.filters.type_id ?? null,
        category_id: this.filters.category_id ?? null,
        department_id: this.filters.department_id ?? null,
        date_from: this.filters.date_from ?? null,
        date_to: this.filters.date_to ?? null
      }
    };
  },
  computed: {
    quickFilters() {
      return [
        {
          key: "open",
          label: this.$t("Open"),
          icon: CheckCircle,
          count: this.getOpenTicketsCount(),
          filter: { status_id: this.getOpenStatusId() }
        },
        {
          key: "high_priority",
          label: this.$t("High Priority"),
          icon: AlertTriangle,
          count: this.getHighPriorityCount(),
          filter: { priority_id: this.getHighPriorityId() }
        },
        {
          key: "unassigned",
          label: this.$t("Unassigned"),
          icon: UserX,
          count: this.getUnassignedCount(),
          filter: { assigned_to: null }
        },
        {
          key: "recent",
          label: this.$t("Recent"),
          icon: Clock,
          count: this.getRecentTicketsCount(),
          filter: { date_from: this.getRecentDate() }
        }
      ];
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route("tickets"), pickBy(this.form), { replace: true, preserveState: true });
      }, 150)
    }
  },
  methods: {
    doFilter(e) {
      axios.get(this.route("filter.assignees", { search: e.target.value })).then((res) => {
        this.assignees.splice(0, this.assignees.length, ...res.data);
      });
    },
    doFilterClients(e) {
      axios.get(this.route("filter.clients", { search: e.target.value })).then((res) => {
        this.clients.splice(0, this.clients.length, ...res.data);
      });
    },
    sort(field) {
      this.form.field = field;
      this.form.direction = this.form.direction === "asc" ? "desc" : "asc";
    },
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    uploadImportCSV(e) {
      if (e.target.files.length) {
        this.$inertia.form({ file: e.target.files[0] }).post(this.route("ticket.csv.import"));
      }
    },
    getOpenTicketsCount() {
      var _a;
      return ((_a = this.tickets.data) == null ? void 0 : _a.filter(
        (ticket) => ticket.status && !ticket.status.toLowerCase().includes("closed")
      ).length) || 0;
    },
    getPriorityClass(priority) {
      if (!priority) return "bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300";
      const priorityLower = priority.toLowerCase();
      if (priorityLower.includes("high") || priorityLower.includes("urgent")) {
        return "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300";
      } else if (priorityLower.includes("medium") || priorityLower.includes("normal")) {
        return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300";
      } else if (priorityLower.includes("low")) {
        return "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300";
      }
      return "bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300";
    },
    getStatusClass(status) {
      if (!status) return "bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300";
      const statusLower = status.toLowerCase();
      if (statusLower.includes("open") || statusLower.includes("new")) {
        return "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300";
      } else if (statusLower.includes("pending") || statusLower.includes("waiting")) {
        return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300";
      } else if (statusLower.includes("closed") || statusLower.includes("resolved")) {
        return "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300";
      } else if (statusLower.includes("cancelled") || statusLower.includes("cancelled")) {
        return "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300";
      }
      return "bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300";
    },
    formatDate(date) {
      const locale = this.$t("error") === "error" ? "en" : "zh-tw";
      return formatDate(date, "fromNow", locale);
    },
    getSearchResultsCount() {
      var _a;
      return ((_a = this.tickets.data) == null ? void 0 : _a.length) || 0;
    },
    getActiveFiltersCount() {
      let count = 0;
      if (this.form.search) count++;
      if (this.form.priority_id) count++;
      if (this.form.status_id) count++;
      if (this.form.type_id) count++;
      if (this.form.category_id) count++;
      if (this.form.department_id) count++;
      if (this.form.assigned_to) count++;
      if (this.form.date_from) count++;
      if (this.form.date_to) count++;
      return count;
    },
    getHighPriorityCount() {
      var _a;
      return ((_a = this.tickets.data) == null ? void 0 : _a.filter(
        (ticket) => ticket.priority && (ticket.priority.toLowerCase().includes("high") || ticket.priority.toLowerCase().includes("urgent"))
      ).length) || 0;
    },
    getUnassignedCount() {
      var _a;
      return ((_a = this.tickets.data) == null ? void 0 : _a.filter(
        (ticket) => !ticket.assigned_to
      ).length) || 0;
    },
    getRecentTicketsCount() {
      var _a;
      const recentDate = moment().subtract(7, "days");
      return ((_a = this.tickets.data) == null ? void 0 : _a.filter(
        (ticket) => moment(ticket.created_at).isAfter(recentDate)
      ).length) || 0;
    },
    getOpenStatusId() {
      var _a;
      const openStatus = (_a = this.statuses) == null ? void 0 : _a.find(
        (status) => status.name.toLowerCase().includes("open") || status.name.toLowerCase().includes("new")
      );
      return (openStatus == null ? void 0 : openStatus.id) || null;
    },
    getHighPriorityId() {
      var _a;
      const highPriority = (_a = this.priorities) == null ? void 0 : _a.find(
        (priority) => priority.name.toLowerCase().includes("high") || priority.name.toLowerCase().includes("urgent")
      );
      return (highPriority == null ? void 0 : highPriority.id) || null;
    },
    getRecentDate() {
      return moment().subtract(7, "days").format("YYYY-MM-DD");
    },
    applyQuickFilter(filter) {
      this.activeQuickFilter = this.activeQuickFilter === filter.key ? null : filter.key;
      if (this.activeQuickFilter === filter.key) {
        Object.keys(filter.filter).forEach((key) => {
          this.form[key] = filter.filter[key];
        });
      } else {
        Object.keys(filter.filter).forEach((key) => {
          this.form[key] = null;
        });
      }
    },
    saveFilterPreset() {
      ({
        filters: { ...this.form },
        created_at: (/* @__PURE__ */ new Date()).toISOString()
      });
      alert(this.$t("Filter preset saved successfully!"));
    }
  },
  created() {
    this.moment = moment;
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_Ticket = resolveComponent("Ticket");
  const _component_Grid3X3 = resolveComponent("Grid3X3");
  const _component_List = resolveComponent("List");
  const _component_Upload = resolveComponent("Upload");
  const _component_Download = resolveComponent("Download");
  const _component_Link = resolveComponent("Link");
  const _component_Plus = resolveComponent("Plus");
  const _component_search_input = resolveComponent("search-input");
  const _component_select_input_filter = resolveComponent("select-input-filter");
  const _component_select_input = resolveComponent("select-input");
  const _component_Filter = resolveComponent("Filter");
  const _component_X = resolveComponent("X");
  const _component_Bookmark = resolveComponent("Bookmark");
  const _component_ChevronUp = resolveComponent("ChevronUp");
  const _component_ChevronDown = resolveComponent("ChevronDown");
  const _component_User = resolveComponent("User");
  const _component_UserCheck = resolveComponent("UserCheck");
  const _component_Calendar = resolveComponent("Calendar");
  const _component_Clock = resolveComponent("Clock");
  const _component_ChevronRight = resolveComponent("ChevronRight");
  const _component_pagination = resolveComponent("pagination");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900" }, _attrs))}>`);
  _push(ssrRenderComponent(_component_Head, {
    title: _ctx.$t($props.title)
  }, null, _parent));
  _push(`<div class="relative overflow-hidden"><div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-indigo-600/5 dark:from-blue-400/10 dark:via-purple-400/10 dark:to-indigo-400/10"></div><div class="absolute inset-0" style="${ssrRenderStyle({ "background-image": "radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(147, 51, 234, 0.1) 0%, transparent 50%)" })}"></div><div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50"><div class="px-6 py-8"><div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"><div class="flex-1"><div class="flex items-center gap-4 mb-6"><div class="relative"><div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">`);
  _push(ssrRenderComponent(_component_Ticket, { class: "w-8 h-8 text-white" }, null, _parent));
  _push(`</div><div class="absolute -top-1 -right-1 w-6 h-6 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-white">${ssrInterpolate($props.tickets.total || 0)}</span></div></div><div><h1 class="text-4xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 dark:from-white dark:via-blue-100 dark:to-indigo-100 bg-clip-text text-transparent">${ssrInterpolate(_ctx.$t("Tickets"))}</h1><p class="text-slate-600 dark:text-slate-400 text-lg">${ssrInterpolate(_ctx.$t("Manage and track support tickets efficiently"))}</p></div></div><div class="flex flex-wrap gap-4"><div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200/50 dark:border-blue-700/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300"><div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full animate-pulse"></div><div><span class="text-sm font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate($props.tickets.total || 0)}</span><span class="text-sm text-blue-600 dark:text-blue-400 ml-1">${ssrInterpolate(_ctx.$t("Total Tickets"))}</span></div></div><div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-100/50 dark:from-green-900/20 dark:to-emerald-800/20 rounded-xl border border-green-200/50 dark:border-green-700/50 hover:shadow-lg hover:shadow-green-500/10 transition-all duration-300"><div class="w-3 h-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full animate-pulse"></div><div><span class="text-sm font-semibold text-green-700 dark:text-green-300">${ssrInterpolate($options.getOpenTicketsCount())}</span><span class="text-sm text-green-600 dark:text-green-400 ml-1">${ssrInterpolate(_ctx.$t("Open"))}</span></div></div><div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-orange-50 to-amber-100/50 dark:from-orange-900/20 dark:to-amber-800/20 rounded-xl border border-orange-200/50 dark:border-orange-700/50 hover:shadow-lg hover:shadow-orange-500/10 transition-all duration-300"><div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-amber-600 rounded-full animate-pulse"></div><div><span class="text-sm font-semibold text-orange-700 dark:text-orange-300">${ssrInterpolate($options.getHighPriorityCount())}</span><span class="text-sm text-orange-600 dark:text-orange-400 ml-1">${ssrInterpolate(_ctx.$t("High Priority"))}</span></div></div><div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-purple-50 to-violet-100/50 dark:from-purple-900/20 dark:to-violet-800/20 rounded-xl border border-purple-200/50 dark:border-purple-700/50 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300"><div class="w-3 h-3 bg-gradient-to-r from-purple-500 to-violet-600 rounded-full animate-pulse"></div><div><span class="text-sm font-semibold text-purple-700 dark:text-purple-300">${ssrInterpolate($options.getUnassignedCount())}</span><span class="text-sm text-purple-600 dark:text-purple-400 ml-1">${ssrInterpolate(_ctx.$t("Unassigned"))}</span></div></div></div></div><div class="flex flex-col sm:flex-row gap-4"><div class="flex bg-slate-100 dark:bg-slate-700 rounded-lg p-1"><button class="${ssrRenderClass([
    "flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200",
    $data.viewMode === "grid" ? "bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm" : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
  ])}">`);
  _push(ssrRenderComponent(_component_Grid3X3, { class: "w-4 h-4" }, null, _parent));
  _push(`<span>${ssrInterpolate(_ctx.$t("Grid"))}</span></button><button class="${ssrRenderClass([
    "flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200",
    $data.viewMode === "list" ? "bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm" : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
  ])}">`);
  _push(ssrRenderComponent(_component_List, { class: "w-4 h-4" }, null, _parent));
  _push(`<span>${ssrInterpolate(_ctx.$t("List"))}</span></button></div><div class="flex gap-2"><label for="importCSV" class="group inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md">`);
  _push(ssrRenderComponent(_component_Upload, { class: "w-4 h-4 group-hover:scale-110 transition-transform duration-200" }, null, _parent));
  _push(`<span class="text-sm font-medium">${ssrInterpolate(_ctx.$t("Import"))}</span><input class="hidden" id="importCSV" type="file" accept=".csv"></label><a${ssrRenderAttr("href", _ctx.route("ticket.csv.export"))} class="group inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg transition-all duration-200 hover:shadow-md">`);
  _push(ssrRenderComponent(_component_Download, { class: "w-4 h-4 group-hover:scale-110 transition-transform duration-200" }, null, _parent));
  _push(`<span class="text-sm font-medium">${ssrInterpolate(_ctx.$t("Export"))}</span></a></div>`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("tickets.create"),
    class: "group relative inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 hover:-translate-y-0.5"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"${_scopeId}></div>`);
        _push2(ssrRenderComponent(_component_Plus, { class: "w-4 h-4 relative z-10 group-hover:rotate-90 transition-transform duration-300" }, null, _parent2, _scopeId));
        _push2(`<span class="relative z-10"${_scopeId}>${ssrInterpolate(_ctx.$t("New Ticket"))}</span><div class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-400/20 to-indigo-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"${_scopeId}></div>`);
      } else {
        return [
          createVNode("div", { class: "absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" }),
          createVNode(_component_Plus, { class: "w-4 h-4 relative z-10 group-hover:rotate-90 transition-transform duration-300" }),
          createVNode("span", { class: "relative z-10" }, toDisplayString(_ctx.$t("New Ticket")), 1),
          createVNode("div", { class: "absolute inset-0 rounded-lg bg-gradient-to-r from-blue-400/20 to-indigo-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300" })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div></div><div class="px-6 py-6"><div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6"><div class="flex flex-col lg:flex-row gap-4 mb-6"><div class="flex-1"><div class="relative">`);
  _push(ssrRenderComponent(_component_search_input, {
    modelValue: $data.form.search,
    "onUpdate:modelValue": ($event) => $data.form.search = $event,
    placeholder: _ctx.$t("Search by Key, Subject, Priority, Status, Assign to..."),
    class: "w-full",
    onReset: $options.reset
  }, null, _parent));
  if ($data.form.search) {
    _push(`<div class="absolute right-3 top-1/2 transform -translate-y-1/2"><span class="text-xs text-slate-500 dark:text-slate-400">${ssrInterpolate($options.getSearchResultsCount())} ${ssrInterpolate(_ctx.$t("results"))}</span></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div><div class="flex items-center gap-4"><div class="flex items-center gap-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Show"))}</label><select class="px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"><option value="10"${ssrIncludeBooleanAttr(Array.isArray($data.form.limit) ? ssrLooseContain($data.form.limit, "10") : ssrLooseEqual($data.form.limit, "10")) ? " selected" : ""}>10</option><option value="25"${ssrIncludeBooleanAttr(Array.isArray($data.form.limit) ? ssrLooseContain($data.form.limit, "25") : ssrLooseEqual($data.form.limit, "25")) ? " selected" : ""}>25</option><option value="50"${ssrIncludeBooleanAttr(Array.isArray($data.form.limit) ? ssrLooseContain($data.form.limit, "50") : ssrLooseEqual($data.form.limit, "50")) ? " selected" : ""}>50</option><option value="100"${ssrIncludeBooleanAttr(Array.isArray($data.form.limit) ? ssrLooseContain($data.form.limit, "100") : ssrLooseEqual($data.form.limit, "100")) ? " selected" : ""}>100</option></select><span class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate(_ctx.$t("per page"))}</span></div></div></div><div class="flex flex-wrap gap-2 mb-6"><!--[-->`);
  ssrRenderList($options.quickFilters, (filter) => {
    _push(`<button class="${ssrRenderClass([
      "inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200",
      $data.activeQuickFilter === filter.key ? "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-700" : "bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600"
    ])}">`);
    ssrRenderVNode(_push, createVNode(resolveDynamicComponent(filter.icon), { class: "w-4 h-4" }, null), _parent);
    _push(`<span>${ssrInterpolate(filter.label)}</span><span class="text-xs opacity-75">(${ssrInterpolate(filter.count)})</span></button>`);
  });
  _push(`<!--]--></div><div class="space-y-6"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">`);
  if (!($props.hidden_fields && $props.hidden_fields.includes("user_id")) && $data.user_access.ticket.update) {
    _push(`<div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Client"))}</label>`);
    _push(ssrRenderComponent(_component_select_input_filter, {
      placeholder: _ctx.$t("Client"),
      onInput: $options.doFilterClients,
      onFocus: $options.doFilterClients,
      items: $data.clients,
      modelValue: $data.form.user_id,
      "onUpdate:modelValue": ($event) => $data.form.user_id = $event,
      class: "w-full"
    }, null, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if (!($props.hidden_fields && $props.hidden_fields.includes("ticket_type"))) {
    _push(`<div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Type"))}</label>`);
    _push(ssrRenderComponent(_component_select_input, {
      modelValue: $data.form.type_id,
      "onUpdate:modelValue": ($event) => $data.form.type_id = $event,
      class: "w-full"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<option${ssrRenderAttr("value", null)}${_scopeId}>${ssrInterpolate(_ctx.$t("All Types"))}</option><!--[-->`);
          ssrRenderList($props.types, (s) => {
            _push2(`<option${ssrRenderAttr("value", s.id)}${_scopeId}>${ssrInterpolate(s.name)}</option>`);
          });
          _push2(`<!--]-->`);
        } else {
          return [
            createVNode("option", { value: null }, toDisplayString(_ctx.$t("All Types")), 1),
            (openBlock(true), createBlock(Fragment, null, renderList($props.types, (s) => {
              return openBlock(), createBlock("option", {
                key: s.id,
                value: s.id
              }, toDisplayString(s.name), 9, ["value"]);
            }), 128))
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if (!($props.hidden_fields && $props.hidden_fields.includes("department"))) {
    _push(`<div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Department"))}</label>`);
    _push(ssrRenderComponent(_component_select_input, {
      modelValue: $data.form.department_id,
      "onUpdate:modelValue": ($event) => $data.form.department_id = $event,
      class: "w-full"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<option${ssrRenderAttr("value", null)}${_scopeId}>${ssrInterpolate(_ctx.$t("All Departments"))}</option><!--[-->`);
          ssrRenderList($props.departments, (s) => {
            _push2(`<option${ssrRenderAttr("value", s.id)}${_scopeId}>${ssrInterpolate(s.name)}</option>`);
          });
          _push2(`<!--]-->`);
        } else {
          return [
            createVNode("option", { value: null }, toDisplayString(_ctx.$t("All Departments")), 1),
            (openBlock(true), createBlock(Fragment, null, renderList($props.departments, (s) => {
              return openBlock(), createBlock("option", {
                key: s.id,
                value: s.id
              }, toDisplayString(s.name), 9, ["value"]);
            }), 128))
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Priority"))}</label>`);
  _push(ssrRenderComponent(_component_select_input, {
    modelValue: $data.form.priority_id,
    "onUpdate:modelValue": ($event) => $data.form.priority_id = $event,
    class: "w-full"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<option${ssrRenderAttr("value", null)}${_scopeId}>${ssrInterpolate(_ctx.$t("All Priorities"))}</option><!--[-->`);
        ssrRenderList($props.priorities, (s) => {
          _push2(`<option${ssrRenderAttr("value", s.id)}${_scopeId}>${ssrInterpolate(s.name)}</option>`);
        });
        _push2(`<!--]-->`);
      } else {
        return [
          createVNode("option", { value: null }, toDisplayString(_ctx.$t("All Priorities")), 1),
          (openBlock(true), createBlock(Fragment, null, renderList($props.priorities, (s) => {
            return openBlock(), createBlock("option", {
              key: s.id,
              value: s.id
            }, toDisplayString(s.name), 9, ["value"]);
          }), 128))
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div><div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Status"))}</label>`);
  _push(ssrRenderComponent(_component_select_input, {
    modelValue: $data.form.status_id,
    "onUpdate:modelValue": ($event) => $data.form.status_id = $event,
    class: "w-full"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<option${ssrRenderAttr("value", null)}${_scopeId}>${ssrInterpolate(_ctx.$t("All Statuses"))}</option><!--[-->`);
        ssrRenderList($props.statuses, (s) => {
          _push2(`<option${ssrRenderAttr("value", s.id)}${_scopeId}>${ssrInterpolate(s.name)}</option>`);
        });
        _push2(`<!--]-->`);
      } else {
        return [
          createVNode("option", { value: null }, toDisplayString(_ctx.$t("All Statuses")), 1),
          (openBlock(true), createBlock(Fragment, null, renderList($props.statuses, (s) => {
            return openBlock(), createBlock("option", {
              key: s.id,
              value: s.id
            }, toDisplayString(s.name), 9, ["value"]);
          }), 128))
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div>`);
  if (!($props.hidden_fields && $props.hidden_fields.includes("assigned_to")) && $data.user_access.ticket.update) {
    _push(`<div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Assign To"))}</label>`);
    _push(ssrRenderComponent(_component_select_input_filter, {
      placeholder: _ctx.$t("All Assignees"),
      onInput: $options.doFilter,
      onFocus: $options.doFilter,
      items: $props.assignees,
      modelValue: $data.form.assigned_to,
      "onUpdate:modelValue": ($event) => $data.form.assigned_to = $event,
      class: "w-full"
    }, null, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Created From"))}</label><input${ssrRenderAttr("value", $data.form.date_from)} type="date" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"></div><div class="space-y-2"><label class="text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Created To"))}</label><input${ssrRenderAttr("value", $data.form.date_to)} type="date" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"></div></div></div><div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-200 dark:border-slate-700"><div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">`);
  _push(ssrRenderComponent(_component_Filter, { class: "w-4 h-4" }, null, _parent));
  _push(`<span>${ssrInterpolate($options.getActiveFiltersCount())} ${ssrInterpolate(_ctx.$t("filters active"))}</span></div><div class="flex gap-2"><button class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors duration-200">`);
  _push(ssrRenderComponent(_component_X, { class: "w-4 h-4" }, null, _parent));
  _push(`<span class="text-sm font-medium">${ssrInterpolate(_ctx.$t("Clear All"))}</span></button><button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg transition-colors duration-200">`);
  _push(ssrRenderComponent(_component_Bookmark, { class: "w-4 h-4" }, null, _parent));
  _push(`<span class="text-sm font-medium">${ssrInterpolate(_ctx.$t("Save Preset"))}</span></button></div></div></div></div><div class="px-6 pb-6"><div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">`);
  if ($data.viewMode === "list") {
    _push(`<div><div class="bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700"><div class="grid grid-cols-12 gap-4 text-sm font-semibold text-slate-700 dark:text-slate-300"><div class="col-span-1 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"><span>${ssrInterpolate(_ctx.$t("Key"))}</span><div class="flex flex-col">`);
    _push(ssrRenderComponent(_component_ChevronUp, {
      class: ["w-3 h-3", { "text-blue-600 dark:text-blue-400": $data.form.direction === "asc" && $data.form.field === "id", "text-slate-400": !($data.form.direction === "asc" && $data.form.field === "id") }]
    }, null, _parent));
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-3 h-3 -mt-1", { "text-blue-600 dark:text-blue-400": $data.form.direction === "desc" && $data.form.field === "id", "text-slate-400": !($data.form.direction === "desc" && $data.form.field === "id") }]
    }, null, _parent));
    _push(`</div></div><div class="col-span-4 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"><span>${ssrInterpolate(_ctx.$t("Subject"))}</span><div class="flex flex-col">`);
    _push(ssrRenderComponent(_component_ChevronUp, {
      class: ["w-3 h-3", { "text-blue-600 dark:text-blue-400": $data.form.direction === "asc" && $data.form.field === "subject", "text-slate-400": !($data.form.direction === "asc" && $data.form.field === "subject") }]
    }, null, _parent));
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-3 h-3 -mt-1", { "text-blue-600 dark:text-blue-400": $data.form.direction === "desc" && $data.form.field === "subject", "text-slate-400": !($data.form.direction === "desc" && $data.form.field === "subject") }]
    }, null, _parent));
    _push(`</div></div><div class="col-span-1 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"><span>${ssrInterpolate(_ctx.$t("Priority"))}</span><div class="flex flex-col">`);
    _push(ssrRenderComponent(_component_ChevronUp, {
      class: ["w-3 h-3", { "text-blue-600 dark:text-blue-400": $data.form.direction === "asc" && $data.form.field === "priority_id", "text-slate-400": !($data.form.direction === "asc" && $data.form.field === "priority_id") }]
    }, null, _parent));
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-3 h-3 -mt-1", { "text-blue-600 dark:text-blue-400": $data.form.direction === "desc" && $data.form.field === "priority_id", "text-slate-400": !($data.form.direction === "desc" && $data.form.field === "priority_id") }]
    }, null, _parent));
    _push(`</div></div><div class="col-span-1 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"><span>${ssrInterpolate(_ctx.$t("Status"))}</span><div class="flex flex-col">`);
    _push(ssrRenderComponent(_component_ChevronUp, {
      class: ["w-3 h-3", { "text-blue-600 dark:text-blue-400": $data.form.direction === "asc" && $data.form.field === "status_id", "text-slate-400": !($data.form.direction === "asc" && $data.form.field === "status_id") }]
    }, null, _parent));
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-3 h-3 -mt-1", { "text-blue-600 dark:text-blue-400": $data.form.direction === "desc" && $data.form.field === "status_id", "text-slate-400": !($data.form.direction === "desc" && $data.form.field === "status_id") }]
    }, null, _parent));
    _push(`</div></div><div class="col-span-2 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"><span>${ssrInterpolate(_ctx.$t("Date"))}</span><div class="flex flex-col">`);
    _push(ssrRenderComponent(_component_ChevronUp, {
      class: ["w-3 h-3", { "text-blue-600 dark:text-blue-400": $data.form.direction === "asc" && $data.form.field === "created_at", "text-slate-400": !($data.form.direction === "asc" && $data.form.field === "created_at") }]
    }, null, _parent));
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-3 h-3 -mt-1", { "text-blue-600 dark:text-blue-400": $data.form.direction === "desc" && $data.form.field === "created_at", "text-slate-400": !($data.form.direction === "desc" && $data.form.field === "created_at") }]
    }, null, _parent));
    _push(`</div></div><div class="col-span-2 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"><span>${ssrInterpolate(_ctx.$t("Updated"))}</span><div class="flex flex-col">`);
    _push(ssrRenderComponent(_component_ChevronUp, {
      class: ["w-3 h-3", { "text-blue-600 dark:text-blue-400": $data.form.direction === "asc" && $data.form.field === "updated_at", "text-slate-400": !($data.form.direction === "asc" && $data.form.field === "updated_at") }]
    }, null, _parent));
    _push(ssrRenderComponent(_component_ChevronDown, {
      class: ["w-3 h-3 -mt-1", { "text-blue-600 dark:text-blue-400": $data.form.direction === "desc" && $data.form.field === "updated_at", "text-slate-400": !($data.form.direction === "desc" && $data.form.field === "updated_at") }]
    }, null, _parent));
    _push(`</div></div><div class="col-span-1 flex items-center justify-end"><span class="text-slate-400 dark:text-slate-500">${ssrInterpolate(_ctx.$t("Actions"))}</span></div></div></div><div class="divide-y divide-slate-200 dark:divide-slate-700"><!--[-->`);
    ssrRenderList($props.tickets.data, (ticket) => {
      _push(`<div class="group hover:bg-gradient-to-r hover:from-slate-50 hover:to-blue-50/30 dark:hover:from-slate-700/50 dark:hover:to-blue-900/10 transition-all duration-300">`);
      _push(ssrRenderComponent(_component_Link, {
        href: _ctx.route("tickets.edit", ticket.uid || ticket.id),
        class: "block"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="grid grid-cols-12 gap-4 px-6 py-4"${_scopeId}><div class="col-span-1 flex items-center"${_scopeId}><span class="text-sm font-mono text-blue-600 dark:text-blue-400 font-medium group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-200"${_scopeId}>#${ssrInterpolate(ticket.uid)}</span></div><div class="col-span-4 flex flex-col gap-2"${_scopeId}><h3 class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2"${_scopeId}>${ssrInterpolate(ticket.subject)}</h3><div class="flex flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400"${_scopeId}>`);
            if (ticket.user) {
              _push2(`<span class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_User, { class: "w-3 h-3" }, null, _parent2, _scopeId));
              _push2(` ${ssrInterpolate(ticket.user)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (ticket.assigned_to) {
              _push2(`<span class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_UserCheck, { class: "w-3 h-3" }, null, _parent2, _scopeId));
              _push2(` ${ssrInterpolate(ticket.assigned_to)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-span-1 flex items-center"${_scopeId}><span class="${ssrRenderClass([$options.getPriorityClass(ticket.priority), "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"])}"${_scopeId}>${ssrInterpolate(ticket.priority)}</span></div><div class="col-span-1 flex items-center"${_scopeId}><span class="${ssrRenderClass([$options.getStatusClass(ticket.status), "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"])}"${_scopeId}>${ssrInterpolate(ticket.status)}</span></div><div class="col-span-2 flex items-center"${_scopeId}><div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Calendar, { class: "w-4 h-4" }, null, _parent2, _scopeId));
            _push2(`<span${_scopeId}>${ssrInterpolate($options.formatDate(ticket.created_at))}</span></div></div><div class="col-span-2 flex items-center"${_scopeId}><div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Clock, { class: "w-4 h-4" }, null, _parent2, _scopeId));
            _push2(`<span${_scopeId}>${ssrInterpolate($options.formatDate(ticket.updated_at))}</span></div></div><div class="col-span-1 flex items-center justify-end"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_ChevronRight, { class: "w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "grid grid-cols-12 gap-4 px-6 py-4" }, [
                createVNode("div", { class: "col-span-1 flex items-center" }, [
                  createVNode("span", { class: "text-sm font-mono text-blue-600 dark:text-blue-400 font-medium group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-200" }, "#" + toDisplayString(ticket.uid), 1)
                ]),
                createVNode("div", { class: "col-span-4 flex flex-col gap-2" }, [
                  createVNode("h3", { class: "text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2" }, toDisplayString(ticket.subject), 1),
                  createVNode("div", { class: "flex flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400" }, [
                    ticket.user ? (openBlock(), createBlock("span", {
                      key: 0,
                      class: "flex items-center gap-1"
                    }, [
                      createVNode(_component_User, { class: "w-3 h-3" }),
                      createTextVNode(" " + toDisplayString(ticket.user), 1)
                    ])) : createCommentVNode("", true),
                    ticket.assigned_to ? (openBlock(), createBlock("span", {
                      key: 1,
                      class: "flex items-center gap-1"
                    }, [
                      createVNode(_component_UserCheck, { class: "w-3 h-3" }),
                      createTextVNode(" " + toDisplayString(ticket.assigned_to), 1)
                    ])) : createCommentVNode("", true)
                  ])
                ]),
                createVNode("div", { class: "col-span-1 flex items-center" }, [
                  createVNode("span", {
                    class: ["inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium", $options.getPriorityClass(ticket.priority)]
                  }, toDisplayString(ticket.priority), 3)
                ]),
                createVNode("div", { class: "col-span-1 flex items-center" }, [
                  createVNode("span", {
                    class: ["inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium", $options.getStatusClass(ticket.status)]
                  }, toDisplayString(ticket.status), 3)
                ]),
                createVNode("div", { class: "col-span-2 flex items-center" }, [
                  createVNode("div", { class: "flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400" }, [
                    createVNode(_component_Calendar, { class: "w-4 h-4" }),
                    createVNode("span", null, toDisplayString($options.formatDate(ticket.created_at)), 1)
                  ])
                ]),
                createVNode("div", { class: "col-span-2 flex items-center" }, [
                  createVNode("div", { class: "flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400" }, [
                    createVNode(_component_Clock, { class: "w-4 h-4" }),
                    createVNode("span", null, toDisplayString($options.formatDate(ticket.updated_at)), 1)
                  ])
                ]),
                createVNode("div", { class: "col-span-1 flex items-center justify-end" }, [
                  createVNode(_component_ChevronRight, { class: "w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" })
                ])
              ])
            ];
          }
        }),
        _: 2
      }, _parent));
      _push(`</div>`);
    });
    _push(`<!--]--></div></div>`);
  } else {
    _push(`<div class="p-6"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"><!--[-->`);
    ssrRenderList($props.tickets.data, (ticket) => {
      _push(`<div class="group">`);
      _push(ssrRenderComponent(_component_Link, {
        href: _ctx.route("tickets.edit", ticket.uid || ticket.id),
        class: "block"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-6 hover:shadow-lg hover:shadow-blue-500/10 dark:hover:shadow-blue-400/10 transition-all duration-300 hover:-translate-y-1"${_scopeId}><div class="flex items-start justify-between mb-4"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><span class="text-lg font-mono font-bold text-blue-600 dark:text-blue-400"${_scopeId}>#${ssrInterpolate(ticket.uid)}</span></div><div class="flex gap-2"${_scopeId}><span class="${ssrRenderClass([$options.getPriorityClass(ticket.priority), "inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"])}"${_scopeId}>${ssrInterpolate(ticket.priority)}</span><span class="${ssrRenderClass([$options.getStatusClass(ticket.status), "inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"])}"${_scopeId}>${ssrInterpolate(ticket.status)}</span></div></div><div class="space-y-3"${_scopeId}><h3 class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2"${_scopeId}>${ssrInterpolate(ticket.subject)}</h3><div class="space-y-2 text-xs text-slate-500 dark:text-slate-400"${_scopeId}>`);
            if (ticket.user) {
              _push2(`<div class="flex items-center gap-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_User, { class: "w-3 h-3" }, null, _parent2, _scopeId));
              _push2(`<span${_scopeId}>${ssrInterpolate(ticket.user)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (ticket.assigned_to) {
              _push2(`<div class="flex items-center gap-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_component_UserCheck, { class: "w-3 h-3" }, null, _parent2, _scopeId));
              _push2(`<span${_scopeId}>${ssrInterpolate(ticket.assigned_to)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-600"${_scopeId}><div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_Calendar, { class: "w-3 h-3" }, null, _parent2, _scopeId));
            _push2(`<span${_scopeId}>${ssrInterpolate($options.formatDate(ticket.created_at))}</span></div>`);
            _push2(ssrRenderComponent(_component_ChevronRight, { class: "w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" }, null, _parent2, _scopeId));
            _push2(`</div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-6 hover:shadow-lg hover:shadow-blue-500/10 dark:hover:shadow-blue-400/10 transition-all duration-300 hover:-translate-y-1" }, [
                createVNode("div", { class: "flex items-start justify-between mb-4" }, [
                  createVNode("div", { class: "flex items-center gap-2" }, [
                    createVNode("span", { class: "text-lg font-mono font-bold text-blue-600 dark:text-blue-400" }, "#" + toDisplayString(ticket.uid), 1)
                  ]),
                  createVNode("div", { class: "flex gap-2" }, [
                    createVNode("span", {
                      class: ["inline-flex items-center px-2 py-1 rounded-full text-xs font-medium", $options.getPriorityClass(ticket.priority)]
                    }, toDisplayString(ticket.priority), 3),
                    createVNode("span", {
                      class: ["inline-flex items-center px-2 py-1 rounded-full text-xs font-medium", $options.getStatusClass(ticket.status)]
                    }, toDisplayString(ticket.status), 3)
                  ])
                ]),
                createVNode("div", { class: "space-y-3" }, [
                  createVNode("h3", { class: "text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2" }, toDisplayString(ticket.subject), 1),
                  createVNode("div", { class: "space-y-2 text-xs text-slate-500 dark:text-slate-400" }, [
                    ticket.user ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex items-center gap-2"
                    }, [
                      createVNode(_component_User, { class: "w-3 h-3" }),
                      createVNode("span", null, toDisplayString(ticket.user), 1)
                    ])) : createCommentVNode("", true),
                    ticket.assigned_to ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "flex items-center gap-2"
                    }, [
                      createVNode(_component_UserCheck, { class: "w-3 h-3" }),
                      createVNode("span", null, toDisplayString(ticket.assigned_to), 1)
                    ])) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-600" }, [
                    createVNode("div", { class: "flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400" }, [
                      createVNode(_component_Calendar, { class: "w-3 h-3" }),
                      createVNode("span", null, toDisplayString($options.formatDate(ticket.created_at)), 1)
                    ]),
                    createVNode(_component_ChevronRight, { class: "w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" })
                  ])
                ])
              ])
            ];
          }
        }),
        _: 2
      }, _parent));
      _push(`</div>`);
    });
    _push(`<!--]--></div></div>`);
  }
  if ($props.tickets.data.length === 0) {
    _push(`<div class="px-6 py-16 text-center"><div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_Ticket, { class: "w-10 h-10 text-slate-400" }, null, _parent));
    _push(`</div><h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">${ssrInterpolate(_ctx.$t("No tickets found"))}</h3><p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto">${ssrInterpolate(_ctx.$t("Try adjusting your filters or create a new ticket to get started"))}</p><div class="flex flex-col sm:flex-row gap-3 justify-center">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("tickets.create"),
      class: "inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_Plus, { class: "w-4 h-4" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>${ssrInterpolate(_ctx.$t("Create Ticket"))}</span>`);
        } else {
          return [
            createVNode(_component_Plus, { class: "w-4 h-4" }),
            createVNode("span", null, toDisplayString(_ctx.$t("Create Ticket")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`<button class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-colors duration-200">`);
    _push(ssrRenderComponent(_component_X, { class: "w-4 h-4" }, null, _parent));
    _push(`<span>${ssrInterpolate(_ctx.$t("Clear Filters"))}</span></button></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="mt-8">`);
  _push(ssrRenderComponent(_component_pagination, {
    links: $props.tickets.links
  }, null, _parent));
  _push(`</div></div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Tickets/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Index as default
};
