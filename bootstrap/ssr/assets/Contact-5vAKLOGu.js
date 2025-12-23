import { L as Layout } from "./Layout-DXQoZj0i.js";
import { Head } from "@inertiajs/vue3";
import { MapPin, Mail, Phone, Send, MessageCircle, ArrowRight, CheckCircle, Star } from "lucide-vue-next";
import sanitizeHtml from "sanitize-html";
import vueRecaptcha from "vue3-recaptcha2";
import { resolveComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr } from "vue/server-renderer";
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
    vueRecaptcha,
    Head,
    Star,
    CheckCircle,
    ArrowRight,
    MessageCircle,
    Send,
    Phone,
    Mail,
    MapPin
  },
  props: {
    data: Object,
    site_key: String
  },
  data() {
    return {
      disabled: true,
      page: JSON.parse(this.data.html),
      form: this.$inertia.form({
        name: "",
        email: "",
        phone: "",
        message: ""
      })
    };
  },
  methods: {
    recaptchaVerified(response) {
      this.disabled = false;
    },
    recaptchaExpired() {
      this.$refs.vueRecaptcha.reset();
    },
    recaptchaFailed() {
    },
    recaptchaError(reason) {
      console.log(reason);
    },
    sanitizeHtml,
    store() {
      this.form.post(this.route("contact.send"), {
        onSuccess: () => {
          this.form.reset();
        }
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_MessageCircle = resolveComponent("MessageCircle");
  const _component_Send = resolveComponent("Send");
  const _component_Phone = resolveComponent("Phone");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_MapPin = resolveComponent("MapPin");
  const _component_Mail = resolveComponent("Mail");
  const _component_vue_recaptcha = resolveComponent("vue-recaptcha");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_Head, { title: "Contact" }, null, _parent));
  _push(`<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800"><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse at center, white, transparent 70%)" })}"></div></div><div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div><div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div><div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div></div><div class="container relative z-10 py-20"><div class="text-center max-w-4xl mx-auto"><div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white bg-opacity-10 rounded-full border border-white border-opacity-20 shadow-lg" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
  _push(ssrRenderComponent(_component_MessageCircle, { class: "w-4 h-4 text-yellow-300" }, null, _parent));
  _push(`<span>Get in Touch</span></div><h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">${ssrInterpolate(_ctx.$t($props.data.title))}</h1><p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto"> We&#39;re here to help! Reach out to our team for any questions, support, or to discuss how we can assist your business. </p><div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12"><a href="#contact-form" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-white bg-opacity-20 border border-white border-opacity-30 rounded-2xl transition-all duration-300 hover:bg-white hover:text-slate-900 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}"><span>Send Message</span>`);
  _push(ssrRenderComponent(_component_Send, { class: "w-5 h-5 transition-transform group-hover:translate-x-1" }, null, _parent));
  _push(`</a><a href="#contact-info" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-2xl transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-primary-300"><span>Contact Info</span>`);
  _push(ssrRenderComponent(_component_Phone, { class: "w-5 h-5 transition-transform group-hover:translate-x-1" }, null, _parent));
  _push(`</a></div><div class="flex flex-wrap items-center justify-center gap-8 text-white/70"><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Quick Response</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">24/7 Support</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Expert Team</span></div></div></div></div><div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"><div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center"><div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div></div></div></section><section id="contact-info" class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 left-0 w-full h-full opacity-5" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "background-position": "center", "background-repeat": "repeat" })}"></div><div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div><div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="grid lg:grid-cols-2 gap-16 items-start"><div class="space-y-8"><div><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_MessageCircle, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` Contact Information </div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">${ssrInterpolate($data.page.content_text)}</h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300">${ssrInterpolate($data.page.content_details)}</p></div><div class="space-y-6"><div class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-slate-700"><div class="flex items-start gap-4"><div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">`);
  _push(ssrRenderComponent(_component_MapPin, { class: "w-7 h-7 text-white" }, null, _parent));
  _push(`</div><div class="flex-1"><h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Our Location</h4><p class="text-slate-600 dark:text-slate-300 leading-relaxed">${$options.sanitizeHtml($data.page.location) ?? ""}</p></div></div></div><div class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-slate-700"><div class="flex items-start gap-4"><div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">`);
  _push(ssrRenderComponent(_component_Phone, { class: "w-7 h-7 text-white" }, null, _parent));
  _push(`</div><div class="flex-1"><h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Phone Number</h4><p class="text-slate-600 dark:text-slate-300"><a${ssrRenderAttr("href", "tel:" + $data.page.phone)} class="hover:text-primary-600 transition-colors duration-300 font-medium">${ssrInterpolate($data.page.phone)}</a></p></div></div></div><div class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 dark:border-slate-700"><div class="flex items-start gap-4"><div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">`);
  _push(ssrRenderComponent(_component_Mail, { class: "w-7 h-7 text-white" }, null, _parent));
  _push(`</div><div class="flex-1"><h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Email Address</h4><p class="text-slate-600 dark:text-slate-300"><a${ssrRenderAttr("href", "mailto:" + $data.page.email)} class="hover:text-primary-600 transition-colors duration-300 font-medium">${ssrInterpolate($data.page.email)}</a></p></div></div></div></div></div><div class="lg:pl-8"><div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden"><div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-8"><div class="flex items-center gap-4"><div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_Send, { class: "w-8 h-8 text-white" }, null, _parent));
  _push(`</div><div><h3 class="text-3xl font-bold text-white">Send us a Message</h3><p class="text-white/80 text-lg">We&#39;ll get back to you within 24 hours</p></div></div></div><div class="p-8"><form method="post" class="space-y-6"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name <span class="text-red-500">*</span></label><input type="text"${ssrRenderAttr("value", $data.form.name)} placeholder="Enter your full name" class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300" required></div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email Address <span class="text-red-500">*</span></label><input type="email"${ssrRenderAttr("value", $data.form.email)} placeholder="Enter your email address" class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300" required></div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone Number</label><input type="tel"${ssrRenderAttr("value", $data.form.phone)} placeholder="Enter your phone number" class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"></div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Message <span class="text-red-500">*</span></label><textarea rows="6" placeholder="Tell us how we can help you..." class="w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 resize-none" required>${ssrInterpolate($data.form.message)}</textarea></div><div class="flex justify-center items-center py-4">`);
  if ($props.site_key) {
    _push(ssrRenderComponent(_component_vue_recaptcha, {
      sitekey: $props.site_key,
      size: "normal",
      theme: "light",
      onVerify: $options.recaptchaVerified,
      onExpire: $options.recaptchaExpired,
      onFail: $options.recaptchaFailed,
      onError: $options.recaptchaError,
      ref: "vueRecaptcha"
    }, null, _parent));
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="pt-4"><button${ssrIncludeBooleanAttr($data.disabled && $props.site_key) ? " disabled" : ""} type="submit" class="w-full inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-800 transition-all duration-300 hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">`);
  _push(ssrRenderComponent(_component_Send, { class: "w-5 h-5" }, null, _parent));
  _push(`<span>Send Message</span></button></div></form></div></div></div></div></div></section></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Landing/Contact.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Contact = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Contact as default
};
