import { L as Layout } from "./Layout-DXQoZj0i.js";
import { Head } from "@inertiajs/vue3";
import { User, Ticket, MessageCircle, FileText, ExternalLink, AlertCircle, Check, X, Minus, Plus, Edit, Share, Download, Filter, Search, Bell, Calendar, MessageSquare, HelpCircle, Phone, Mail, Smartphone, Lock, Globe, Headphones, Clock, BarChart3, Users, Zap, Shield, Settings, Info, ArrowDown, ArrowRight, CheckCircle, Star } from "lucide-vue-next";
import sanitizeHtml from "sanitize-html";
import { resolveComponent, createVNode, resolveDynamicComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderList, ssrRenderVNode } from "vue/server-renderer";
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
    Star,
    CheckCircle,
    ArrowRight,
    ArrowDown,
    Info,
    Settings,
    Shield,
    Zap,
    Users,
    BarChart3,
    Clock,
    Headphones,
    Globe,
    Lock,
    Smartphone,
    Mail,
    Phone,
    HelpCircle,
    MessageSquare,
    Calendar,
    Bell,
    Search,
    Filter,
    Download,
    Share,
    Edit,
    Plus,
    Minus,
    X,
    Check,
    AlertCircle,
    ExternalLink,
    FileText,
    MessageCircle,
    Ticket,
    User
  },
  props: {
    data: Object
  },
  data() {
    return {
      page: JSON.parse(this.data.html)
    };
  },
  methods: {
    getServiceIcon(iconName) {
      const iconMap = {
        "ticket": Ticket,
        "user": User,
        "users": Users,
        "settings": Settings,
        "shield": Shield,
        "zap": Zap,
        "bar-chart": BarChart3,
        "clock": Clock,
        "headphones": Headphones,
        "globe": Globe,
        "lock": Lock,
        "smartphone": Smartphone,
        "mail": Mail,
        "phone": Phone,
        "help-circle": HelpCircle,
        "message-square": MessageSquare,
        "calendar": Calendar,
        "bell": Bell,
        "search": Search,
        "filter": Filter,
        "download": Download,
        "share": Share,
        "edit": Edit,
        "plus": Plus,
        "minus": Minus,
        "x": X,
        "check": Check,
        "alert-circle": AlertCircle,
        "info": Info,
        "external-link": ExternalLink,
        "star": Star,
        "file": FileText,
        "message": MessageCircle
      };
      return iconMap[iconName] || HelpCircle;
    },
    sanitizeHtml
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_Star = resolveComponent("Star");
  const _component_ArrowDown = resolveComponent("ArrowDown");
  const _component_ArrowRight = resolveComponent("ArrowRight");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_Info = resolveComponent("Info");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_Head, { title: "Services" }, null, _parent));
  _push(`<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800"><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse at center, white, transparent 70%)" })}"></div></div><div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div><div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div><div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div></div><div class="container relative z-10 py-20"><div class="text-center max-w-4xl mx-auto"><div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white bg-opacity-10 rounded-full border border-white border-opacity-20 shadow-lg" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-4 h-4 text-yellow-300" }, null, _parent));
  _push(`<span>Professional Services</span></div><h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">${ssrInterpolate(_ctx.$t($props.data.title))}</h1><p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto"> Discover our comprehensive range of professional services designed to help your business thrive and succeed in today&#39;s competitive landscape. </p><div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12"><a href="#services" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-white bg-opacity-20 border border-white border-opacity-30 rounded-2xl transition-all duration-300 hover:bg-white hover:text-slate-900 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}"><span>Explore Services</span>`);
  _push(ssrRenderComponent(_component_ArrowDown, { class: "w-5 h-5 transition-transform group-hover:translate-y-1" }, null, _parent));
  _push(`</a><a href="/contact" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-2xl transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-primary-300"><span>Get Started</span>`);
  _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 transition-transform group-hover:translate-x-1" }, null, _parent));
  _push(`</a></div><div class="flex flex-wrap items-center justify-center gap-8 text-white/70"><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Expert Team</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">24/7 Support</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Proven Results</span></div></div></div></div><div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"><div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center"><div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div></div></div></section><section id="services" class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 left-0 w-full h-full opacity-5" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "background-position": "center", "background-repeat": "repeat" })}"></div><div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div><div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-20"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` Our Services </div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl"> What We Offer </h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto"> We provide comprehensive solutions tailored to meet your business needs and drive success. </p></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"><!--[-->`);
  ssrRenderList($data.page.services, (service, index) => {
    _push(`<div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="relative mb-6"><div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">`);
    ssrRenderVNode(_push, createVNode(resolveDynamicComponent($options.getServiceIcon(service.icon)), { class: "w-8 h-8 text-white" }, null), _parent);
    _push(`</div><div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 animate-pulse"></div><div class="absolute -bottom-1 -left-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 animate-ping"></div></div><div class="relative z-10"><h4 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">${ssrInterpolate(service.name)}</h4><p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-6">${ssrInterpolate(service.details)}</p><div class="flex items-center text-primary-600 font-semibold group-hover:text-primary-700 transition-colors duration-300"><span class="text-sm">Learn More</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" }, null, _parent));
    _push(`</div></div><div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-primary/20 transition-all duration-300"></div></div>`);
  });
  _push(`<!--]--></div><div class="text-center mt-20"><div class="bg-gradient-to-r from-primary-600 to-blue-600 rounded-3xl p-12 text-white"><h3 class="text-3xl font-bold mb-4">Ready to Get Started?</h3><p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto"> Let&#39;s discuss how our services can help your business grow and succeed. </p><div class="flex flex-col sm:flex-row items-center justify-center gap-4"><a href="/contact" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-primary-600 font-semibold rounded-2xl hover:bg-white/90 transition-all duration-300 hover:scale-105 hover:shadow-xl"><span>Contact Us</span>`);
  _push(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5" }, null, _parent));
  _push(`</a><a href="/" class="inline-flex items-center gap-3 px-8 py-4 bg-white/20 text-white font-semibold rounded-2xl hover:bg-white/30 transition-all duration-300 hover:scale-105" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}"><span>Learn More</span>`);
  _push(ssrRenderComponent(_component_Info, { class: "w-5 h-5" }, null, _parent));
  _push(`</a></div></div></div></div></section></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Landing/Services.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Services = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Services as default
};
