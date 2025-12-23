import { Link, Head } from "@inertiajs/vue3";
import { _ as _export_sfc, L as Logo } from "./FlashMessages-DUb4hfI8.js";
import { L as Layout } from "./Layout-DXQoZj0i.js";
import { ExternalLink, Info, AlertCircle, Check, X, Minus, Plus, Edit, Share, Download, Filter, Search, Bell, Calendar, MessageSquare, HelpCircle, Phone, Mail, Smartphone, Lock, Globe, Headphones, Clock, BarChart3, Users, Zap, Shield, Send, Trash2, Upload, FileText, Settings, User, Star, Ticket, MessageCircle, ArrowRight, CheckCircle } from "lucide-vue-next";
import { T as TextInput } from "./TextInput-BtIGrMWH.js";
import { S as SelectInput } from "./SelectInput-BK_r_uc2.js";
import { L as LoadingButton } from "./LoadingButton-Dl5SG5JI.js";
import sanitizeHtml from "sanitize-html";
import { Bold, Essentials, Italic, Mention, Paragraph, Undo, Heading, Indent, Link as Link$1, Image, Table, List, MediaEmbed, ImageUpload, BlockQuote, ClassicEditor } from "ckeditor5";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import { _ as _sfc_main$1 } from "./UploadAdapter-D1xerzqJ.js";
import { resolveComponent, withCtx, createVNode, toDisplayString, resolveDynamicComponent, createBlock, openBlock, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrRenderList, ssrInterpolate, ssrRenderAttr, ssrRenderVNode, ssrRenderClass, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderDynamicModel } from "vue/server-renderer";
import "./Dropdown-DNX6MmV_.js";
import "@popperjs/core";
import "axios";
import "laravel-vue-i18n";
import "moment";
import "uuid";
const _sfc_main = {
  metaInfo: { title: "Home" },
  layout: Layout,
  components: {
    Logo,
    LoadingButton,
    SelectInput,
    TextInput,
    Head,
    Link,
    Ckeditor,
    Star,
    CheckCircle,
    ArrowRight,
    MessageCircle,
    Ticket,
    StarIcon: Star,
    User,
    Settings,
    FileText,
    Upload,
    Trash2,
    Send,
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
    Info,
    ExternalLink
  },
  props: {
    all_categories: { required: true },
    departments: Array,
    types: Array,
    title: String,
    priorities: {
      type: Array,
      default: []
    },
    page: Object,
    auth: Object,
    custom_fields: Object,
    hide_ticket_fields: Array,
    require_login: Boolean
  },
  remember: "form",
  data() {
    var _a;
    return {
      editor: ClassicEditor,
      accept_terms: false,
      html: JSON.parse(this.page.html),
      validationErrors: {},
      isSubmitting: false,
      editorConfig: {
        plugins: [Bold, Essentials, Italic, Mention, Paragraph, Undo, Heading, Indent, Link$1, Image, Table, List, MediaEmbed, ImageUpload, BlockQuote],
        toolbar: ["heading", "|", "bold", "italic", "link", "bulletedList", "numberedList", "|", "outdent", "indent", "|", "insertTable", "blockQuote", "|", "imageUpload", "mediaEmbed", "|", "undo", "redo"],
        table: {
          toolbar: ["tableColumn", "tableRow", "mergeTableCells"]
        },
        extraPlugins: [function uploader(editor) {
          editor.plugins.get("FileRepository").createUploadAdapter = (loader) => {
            return new _sfc_main$1(loader);
          };
        }]
      },
      categories: [],
      sub_categories: [],
      form: this.$inertia.form({
        first_name: "",
        last_name: "",
        email: "",
        department_id: null,
        priority_id: ((_a = this.priorities) == null ? void 0 : _a.length) ? this.priorities[1].id : null,
        category_id: null,
        sub_category_id: null,
        type_id: null,
        subject: null,
        details: "",
        files: [],
        custom_field: {}
      }),
      sanitizeHtml
    };
  },
  methods: {
    // Validation Methods
    validateForm() {
      this.validationErrors = {};
      let isValid = true;
      if (!this.form.first_name || this.form.first_name.trim() === "") {
        this.validationErrors.first_name = "First name is required";
        isValid = false;
      } else if (this.form.first_name.length < 2) {
        this.validationErrors.first_name = "First name must be at least 2 characters";
        isValid = false;
      }
      if (!this.form.last_name || this.form.last_name.trim() === "") {
        this.validationErrors.last_name = "Last name is required";
        isValid = false;
      } else if (this.form.last_name.length < 2) {
        this.validationErrors.last_name = "Last name must be at least 2 characters";
        isValid = false;
      }
      if (!this.form.email || this.form.email.trim() === "") {
        this.validationErrors.email = "Email address is required";
        isValid = false;
      } else if (!this.isValidEmail(this.form.email)) {
        this.validationErrors.email = "Please enter a valid email address";
        isValid = false;
      }
      if (!this.form.subject || this.form.subject.trim() === "") {
        this.validationErrors.subject = "Subject is required";
        isValid = false;
      } else if (this.form.subject.length < 5) {
        this.validationErrors.subject = "Subject must be at least 5 characters";
        isValid = false;
      }
      if (!this.form.details || this.form.details.trim() === "") {
        this.validationErrors.details = "Request details are required";
        isValid = false;
      } else if (this.form.details.length < 10) {
        this.validationErrors.details = "Please provide more details (at least 10 characters)";
        isValid = false;
      }
      this.custom_fields.forEach((field) => {
        if (field.required && (!this.form.custom_field[field.name] || this.form.custom_field[field.name].trim() === "")) {
          this.validationErrors[`custom_field_${field.name}`] = `${field.label} is required`;
          isValid = false;
        }
      });
      if (this.form.files.length > 0) {
        this.form.files.forEach((file, index) => {
          if (file.size > 10 * 1024 * 1024) {
            this.validationErrors[`file_${index}`] = `File "${file.name}" is too large. Maximum size is 10MB`;
            isValid = false;
          }
        });
      }
      if (!this.accept_terms) {
        this.validationErrors.terms = "You must accept the terms and conditions";
        isValid = false;
      }
      return isValid;
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    clearValidationError(field) {
      if (this.validationErrors[field]) {
        delete this.validationErrors[field];
      }
    },
    getFieldError(field) {
      return this.validationErrors[field] || this.form.errors[field];
    },
    hasFieldError(field) {
      return !!(this.validationErrors[field] || this.form.errors[field]);
    },
    scrollToFirstError() {
      this.$nextTick(() => {
        const firstError = document.querySelector(".border-red-500, .text-red-500");
        if (firstError) {
          firstError.scrollIntoView({
            behavior: "smooth",
            block: "center"
          });
          firstError.focus();
        }
      });
    },
    showSuccessMessage() {
      alert("Ticket submitted successfully! We will get back to you soon.");
    },
    handleServerErrors(errors) {
      Object.keys(errors).forEach((key) => {
        this.validationErrors[key] = errors[key];
      });
      this.scrollToFirstError();
    },
    getFeatureIcon(iconName) {
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
    getCategories() {
      this.categories = this.all_categories.filter((cat) => cat.department_id === this.form.department_id);
      this.form.category_id = null;
      this.form.sub_category_id = null;
      this.sub_categories = [];
      if (this.$refs.category) {
        this.$refs.category.selected = null;
      }
      if (this.$refs.sub_category) {
        this.$refs.sub_category.selected = null;
      }
      this.clearValidationError("category_id");
      this.clearValidationError("sub_category_id");
    },
    getSubCategories() {
      this.sub_categories = this.all_categories.filter((cat) => cat.parent_id === this.form.category_id);
      this.form.sub_category_id = null;
      if (this.$refs.sub_category) {
        this.$refs.sub_category.selected = null;
      }
      this.clearValidationError("sub_category_id");
    },
    uploadFiles() {
      this.form.files = this.$refs.files.files;
    },
    store() {
      this.validationErrors = {};
      if (!this.validateForm()) {
        this.scrollToFirstError();
        return;
      }
      this.isSubmitting = true;
      this.form.post(this.route("ticket_store"), {
        onSuccess: () => {
          this.form.reset();
          this.accept_terms = false;
          this.validationErrors = {};
          this.isSubmitting = false;
          this.showSuccessMessage();
        },
        onError: (errors) => {
          this.isSubmitting = false;
          this.handleServerErrors(errors);
        },
        onFinish: () => {
          this.isSubmitting = false;
        }
      });
    },
    fileInputChange(e) {
      let selectedFiles = e.target.files;
      let validFiles = [];
      let hasErrors = false;
      for (let i = 0; i < selectedFiles.length; i++) {
        const file = selectedFiles[i];
        if (file.size > 10 * 1024 * 1024) {
          this.validationErrors[`file_${i}`] = `File "${file.name}" is too large. Maximum size is 10MB`;
          hasErrors = true;
          continue;
        }
        const allowedTypes = [
          "image/jpeg",
          "image/png",
          "image/gif",
          "image/webp",
          "application/pdf",
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "application/vnd.ms-excel",
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "application/vnd.ms-powerpoint",
          "application/vnd.openxmlformats-officedocument.presentationml.presentation",
          "text/plain",
          "application/zip"
        ];
        if (!allowedTypes.includes(file.type)) {
          this.validationErrors[`file_${i}`] = `File "${file.name}" type is not supported`;
          hasErrors = true;
          continue;
        }
        validFiles.push(file);
      }
      if (hasErrors) {
        this.scrollToFirstError();
      }
      this.form.files = [...this.form.files, ...validFiles];
    },
    fileRemove(image, index) {
      this.form.files.splice(index, 1);
      delete this.validationErrors[`file_${index}`];
    },
    getFileSize(size) {
      const i = Math.floor(Math.log(size) / Math.log(1024));
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + " " + ["B", "kB", "MB", "GB", "TB"][i];
    },
    fileBrowse() {
      this.$refs.file.click();
    },
    // Real-time validation
    validateField(field, value) {
      this.clearValidationError(field);
      switch (field) {
        case "first_name":
          if (!value || value.trim() === "") {
            this.validationErrors.first_name = "First name is required";
          } else if (value.length < 2) {
            this.validationErrors.first_name = "First name must be at least 2 characters";
          }
          break;
        case "last_name":
          if (!value || value.trim() === "") {
            this.validationErrors.last_name = "Last name is required";
          } else if (value.length < 2) {
            this.validationErrors.last_name = "Last name must be at least 2 characters";
          }
          break;
        case "email":
          if (!value || value.trim() === "") {
            this.validationErrors.email = "Email address is required";
          } else if (!this.isValidEmail(value)) {
            this.validationErrors.email = "Please enter a valid email address";
          }
          break;
        case "subject":
          if (!value || value.trim() === "") {
            this.validationErrors.subject = "Subject is required";
          } else if (value.length < 5) {
            this.validationErrors.subject = "Subject must be at least 5 characters";
          }
          break;
        case "details":
          if (!value || value.trim() === "") {
            this.validationErrors.details = "Request details are required";
          } else if (value.length < 10) {
            this.validationErrors.details = "Please provide more details (at least 10 characters)";
          }
          break;
      }
    }
  },
  created() {
    if (this.auth.user) {
      this.form.first_name = this.auth.user.first_name;
      this.form.last_name = this.auth.user.last_name;
      this.form.email = this.auth.user.email;
    }
    for (let cf_c = 0; cf_c < this.custom_fields.length; cf_c++) {
      if (this.custom_fields[cf_c.name]) {
        this.form.custom_field[this.custom_fields[cf_c.name]] = "";
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_Star = resolveComponent("Star");
  const _component_Link = resolveComponent("Link");
  const _component_ArrowRight = resolveComponent("ArrowRight");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_MessageCircle = resolveComponent("MessageCircle");
  const _component_Ticket = resolveComponent("Ticket");
  const _component_User = resolveComponent("User");
  const _component_AlertCircle = resolveComponent("AlertCircle");
  const _component_Settings = resolveComponent("Settings");
  const _component_ckeditor = resolveComponent("ckeditor");
  const _component_FileText = resolveComponent("FileText");
  const _component_Upload = resolveComponent("Upload");
  const _component_Trash2 = resolveComponent("Trash2");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_Send = resolveComponent("Send");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_Head, {
    title: _ctx.$t("Home")
  }, null, _parent));
  _push(`<section id="home" class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800"><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse at center, white, transparent 70%)" })}"></div></div><div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div><div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div><div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div></div><div class="container relative z-10 py-20"><div class="grid lg:grid-cols-2 gap-12 items-center"><div class="text-center lg:text-left"><div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white bg-opacity-10 rounded-full border border-white border-opacity-20 shadow-lg" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-4 h-4 text-yellow-300" }, null, _parent));
  _push(`<span>Trusted by 10,000+ companies</span></div><h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl">${$data.sanitizeHtml($data.html.sections[0].title) ?? ""}</h1><p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-2xl mx-auto lg:mx-0">${$data.sanitizeHtml($data.html.sections[0].details) ?? ""}</p><div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-12"><!--[-->`);
  ssrRenderList($data.html.sections[0].buttons, (button, bi) => {
    _push(ssrRenderComponent(_component_Link, {
      key: bi,
      href: button.link,
      class: "group relative inline-flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-white bg-opacity-20 border border-white border-opacity-30 rounded-2xl transition-all duration-300 hover:bg-white hover:text-slate-900 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30",
      style: { "backdrop-filter": "blur(12px)" }
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<span${_scopeId}>${ssrInterpolate(button.text)}</span>`);
          _push2(ssrRenderComponent(_component_ArrowRight, { class: "w-5 h-5 transition-transform group-hover:translate-x-1" }, null, _parent2, _scopeId));
        } else {
          return [
            createVNode("span", null, toDisplayString(button.text), 1),
            createVNode(_component_ArrowRight, { class: "w-5 h-5 transition-transform group-hover:translate-x-1" })
          ];
        }
      }),
      _: 2
    }, _parent));
  });
  _push(`<!--]--></div><div class="flex flex-wrap items-center justify-center lg:justify-start gap-8 text-white/70"><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Free 14-day trial</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">No credit card required</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">24/7 support</span></div></div></div><div class="relative"><div class="relative group"><div class="absolute -inset-8 bg-gradient-to-r from-primary-500/20 to-blue-500/20 rounded-3xl blur-2xl group-hover:blur-3xl transition-all duration-700"></div><div class="relative bg-white bg-opacity-5 rounded-3xl p-1 border border-white border-opacity-10 shadow-2xl" style="${ssrRenderStyle({ "backdrop-filter": "blur(4px)" })}"><div class="relative overflow-hidden rounded-2xl">`);
  if ($data.html.sections[0].image) {
    _push(`<img${ssrRenderAttr("src", $data.html.sections[0].image)} alt="HelpDesk Dashboard" class="w-full h-auto transition-transform duration-700 group-hover:scale-105">`);
  } else {
    _push(`<img src="/landing/images/dashboard-helpdesk.png" alt="HelpDesk Dashboard" class="w-full h-auto transition-transform duration-700 group-hover:scale-105">`);
  }
  _push(`</div></div></div><div class="absolute -top-6 -right-6 w-20 h-20 bg-gradient-to-br from-primary-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-xl animate-bounce">`);
  _push(ssrRenderComponent(_component_MessageCircle, { class: "w-8 h-8 text-white" }, null, _parent));
  _push(`</div><div class="absolute -bottom-6 -left-6 w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-xl animate-pulse">`);
  _push(ssrRenderComponent(_component_Ticket, { class: "w-6 h-6 text-white" }, null, _parent));
  _push(`</div><div class="absolute top-1/2 -left-8 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg animate-ping">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-white" }, null, _parent));
  _push(`</div></div></div></div><div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"><div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center"><div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div></div></div></section><section class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500/5 rounded-full blur-3xl"></div><div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div><div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-20"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` ${ssrInterpolate($data.html.sections[1].tagline)}</div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">${ssrInterpolate($data.html.sections[1].title)}</h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">${$data.html.sections[1].details ?? ""}</p></div>`);
  if ($data.html.sections[1]) {
    _push(`<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20"><!--[-->`);
    ssrRenderList($data.html.sections[1].features, (feature, fi) => {
      _push(`<div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-100 dark:border-slate-700 overflow-hidden"><div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div><div class="relative mb-6"><div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">`);
      ssrRenderVNode(_push, createVNode(resolveDynamicComponent($options.getFeatureIcon(feature.icon)), { class: "w-8 h-8 text-white" }, null), _parent);
      _push(`</div><div class="absolute -top-1 -right-1 w-4 h-4 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 animate-pulse"></div><div class="absolute -bottom-1 -left-1 w-3 h-3 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-500 animate-ping"></div></div><div class="relative z-10"><h4 class="mb-4 text-xl font-bold text-slate-900 dark:text-white group-hover:text-primary-600 transition-colors duration-300">${ssrInterpolate(feature.title)}</h4><p class="text-slate-600 dark:text-slate-300 leading-relaxed">${feature.details ?? ""}</p></div><div class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-primary/20 transition-all duration-300"></div></div>`);
    });
    _push(`<!--]--></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="bg-gradient-to-r from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-900 rounded-3xl p-12 shadow-2xl"><div class="text-center mb-12"><h3 class="text-3xl font-bold text-white mb-4">Trusted by Industry Leaders</h3><p class="text-slate-300 text-lg">Join thousands of companies that trust our platform</p></div><div class="grid grid-cols-2 md:grid-cols-4 gap-8"><div class="text-center group"><div class="text-5xl font-bold text-white mb-2 group-hover:text-primary-400 transition-colors duration-300">10K+</div><div class="text-slate-300 font-medium">Happy Customers</div><div class="w-12 h-1 bg-gradient-to-r from-primary-500 to-blue-500 mx-auto mt-2 rounded-full"></div></div><div class="text-center group"><div class="text-5xl font-bold text-white mb-2 group-hover:text-primary-400 transition-colors duration-300">99.9%</div><div class="text-slate-300 font-medium">Uptime</div><div class="w-12 h-1 bg-gradient-to-r from-green-500 to-emerald-500 mx-auto mt-2 rounded-full"></div></div><div class="text-center group"><div class="text-5xl font-bold text-white mb-2 group-hover:text-primary-400 transition-colors duration-300">24/7</div><div class="text-slate-300 font-medium">Support</div><div class="w-12 h-1 bg-gradient-to-r from-purple-500 to-pink-500 mx-auto mt-2 rounded-full"></div></div><div class="text-center group"><div class="text-5xl font-bold text-white mb-2 group-hover:text-primary-400 transition-colors duration-300">4.9★</div><div class="text-slate-300 font-medium">Rating</div><div class="w-12 h-1 bg-gradient-to-r from-yellow-500 to-orange-500 mx-auto mt-2 rounded-full"></div></div></div></div></div></section><section class="relative py-24 lg:py-32 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 left-0 w-full h-full opacity-5" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "background-position": "center", "background-repeat": "repeat" })}"></div><div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div><div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-20"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` Customer Stories </div><h2 class="mb-6 text-4xl font-bold text-white sm:text-5xl lg:text-6xl"> What Our Customers Say </h2><p class="text-xl leading-relaxed text-slate-300 max-w-3xl mx-auto"> Don&#39;t just take our word for it. See what industry leaders have to say about our platform. </p></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"><div class="group bg-white bg-opacity-5 rounded-3xl p-8 border border-white border-opacity-10 hover:border-primary hover:border-opacity-30 transition-all duration-300 hover:scale-105" style="${ssrRenderStyle({ "backdrop-filter": "blur(4px)" })}"><div class="flex items-center gap-1 mb-4">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(`</div><p class="text-slate-300 mb-6 leading-relaxed"> &quot;This platform has revolutionized our customer support. The ticket management system is intuitive and our response times have improved by 60%.&quot; </p><div class="flex items-center gap-4"><div class="w-12 h-12 bg-gradient-to-r from-primary-500 to-blue-500 rounded-full flex items-center justify-center"><span class="text-white font-bold text-lg">JS</span></div><div><div class="text-white font-semibold">John Smith</div><div class="text-slate-400 text-sm">CTO, TechCorp</div></div></div></div><div class="group bg-white bg-opacity-5 rounded-3xl p-8 border border-white border-opacity-10 hover:border-primary hover:border-opacity-30 transition-all duration-300 hover:scale-105" style="${ssrRenderStyle({ "backdrop-filter": "blur(4px)" })}"><div class="flex items-center gap-1 mb-4">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(`</div><p class="text-slate-300 mb-6 leading-relaxed"> &quot;The analytics and reporting features give us incredible insights into our support performance. Highly recommended!&quot; </p><div class="flex items-center gap-4"><div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center"><span class="text-white font-bold text-lg">MJ</span></div><div><div class="text-white font-semibold">Maria Johnson</div><div class="text-slate-400 text-sm">Support Manager, InnovateLab</div></div></div></div><div class="group bg-white bg-opacity-5 rounded-3xl p-8 border border-white border-opacity-10 hover:border-primary hover:border-opacity-30 transition-all duration-300 hover:scale-105" style="${ssrRenderStyle({ "backdrop-filter": "blur(4px)" })}"><div class="flex items-center gap-1 mb-4">`);
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(ssrRenderComponent(_component_Star, { class: "w-5 h-5 text-yellow-400" }, null, _parent));
  _push(`</div><p class="text-slate-300 mb-6 leading-relaxed"> &quot;Implementation was seamless and the support team was fantastic. Our customers love the new experience.&quot; </p><div class="flex items-center gap-4"><div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center"><span class="text-white font-bold text-lg">DR</span></div><div><div class="text-white font-semibold">David Rodriguez</div><div class="text-slate-400 text-sm">CEO, StartupXYZ</div></div></div></div></div></div></section>`);
  if ($data.html.sections[2].enable_ticket_section) {
    _push(`<section class="relative py-24 lg:py-32 bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900" id="ticketSubmit"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 right-0 w-96 h-96 bg-primary-500/5 rounded-full blur-3xl"></div><div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-20"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
    _push(ssrRenderComponent(_component_Ticket, { class: "w-4 h-4 text-primary" }, null, _parent));
    _push(` Get Support </div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl">${ssrInterpolate(_ctx.$t("Create a ticket"))}</h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto"> Get help from our support team. Fill out the form below and we&#39;ll get back to you as soon as possible. </p></div><div class="max-w-4xl mx-auto"><form class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" enctype="multipart/form-data"><div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-8"><div class="flex items-center gap-4"><div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_Ticket, { class: "w-8 h-8 text-white" }, null, _parent));
    _push(`</div><div><h3 class="text-3xl font-bold text-white">${ssrInterpolate(_ctx.$t("Submit Your Request"))}</h3><p class="text-white/80 text-lg">We&#39;re here to help you resolve any issues quickly</p></div></div></div><div class="p-8 space-y-8"><div class="space-y-6"><div class="flex items-center gap-3 mb-6"><div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_User, { class: "w-5 h-5 text-primary" }, null, _parent));
    _push(`</div><h4 class="text-xl font-semibold text-slate-900 dark:text-white">Personal Information</h4></div><div class="grid grid-cols-1 md:grid-cols-3 gap-6"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("First name"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.first_name)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("first_name") }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"${ssrRenderAttr("placeholder", _ctx.$t("Enter your first name"))} required>`);
    if ($options.getFieldError("first_name")) {
      _push(`<p class="text-sm text-red-500 flex items-center gap-1">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
      _push(` ${ssrInterpolate($options.getFieldError("first_name"))}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Last name"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.last_name)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("last_name") }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"${ssrRenderAttr("placeholder", _ctx.$t("Enter your last name"))} required>`);
    if ($options.getFieldError("last_name")) {
      _push(`<p class="text-sm text-red-500 flex items-center gap-1">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
      _push(` ${ssrInterpolate($options.getFieldError("last_name"))}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Email Address"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.email)} type="email" class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("email") }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"${ssrRenderAttr("placeholder", _ctx.$t("Enter your email address"))} required>`);
    if ($options.getFieldError("email")) {
      _push(`<p class="text-sm text-red-500 flex items-center gap-1">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
      _push(` ${ssrInterpolate($options.getFieldError("email"))}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div></div><div class="space-y-6"><div class="flex items-center gap-3 mb-6"><div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_Settings, { class: "w-5 h-5 text-primary" }, null, _parent));
    _push(`</div><h4 class="text-xl font-semibold text-slate-900 dark:text-white">Ticket Details</h4></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Priority"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.priority_id }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.priority_id) ? ssrLooseContain($data.form.priority_id, null) : ssrLooseEqual($data.form.priority_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select Priority"))}</option><!--[-->`);
    ssrRenderList($props.priorities, (s) => {
      _push(`<option${ssrRenderAttr("value", s.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.priority_id) ? ssrLooseContain($data.form.priority_id, s.id) : ssrLooseEqual($data.form.priority_id, s.id)) ? " selected" : ""}>${ssrInterpolate(s.name)}</option>`);
    });
    _push(`<!--]--></select>`);
    if ($data.form.errors.priority_id) {
      _push(`<p class="text-sm text-red-500">${ssrInterpolate($data.form.errors.priority_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Type"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.type_id }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.type_id) ? ssrLooseContain($data.form.type_id, null) : ssrLooseEqual($data.form.type_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a type"))}</option><!--[-->`);
    ssrRenderList($props.types, (type) => {
      _push(`<option${ssrRenderAttr("value", type.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.type_id) ? ssrLooseContain($data.form.type_id, type.id) : ssrLooseEqual($data.form.type_id, type.id)) ? " selected" : ""}>${ssrInterpolate(type.name)}</option>`);
    });
    _push(`<!--]--></select>`);
    if ($data.form.errors.type_id) {
      _push(`<p class="text-sm text-red-500">${ssrInterpolate($data.form.errors.type_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Department"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.department_id }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.department_id) ? ssrLooseContain($data.form.department_id, null) : ssrLooseEqual($data.form.department_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a department"))}</option><!--[-->`);
    ssrRenderList($props.departments, (department) => {
      _push(`<option${ssrRenderAttr("value", department.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.department_id) ? ssrLooseContain($data.form.department_id, department.id) : ssrLooseEqual($data.form.department_id, department.id)) ? " selected" : ""}>${ssrInterpolate(department.name)}</option>`);
    });
    _push(`<!--]--></select>`);
    if ($data.form.errors.department_id) {
      _push(`<p class="text-sm text-red-500">${ssrInterpolate($data.form.errors.department_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
    if ($data.form.department_id) {
      _push(`<div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Category"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.category_id }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.category_id) ? ssrLooseContain($data.form.category_id, null) : ssrLooseEqual($data.form.category_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a category"))}</option><!--[-->`);
      ssrRenderList($data.categories, (category) => {
        _push(`<option${ssrRenderAttr("value", category.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.category_id) ? ssrLooseContain($data.form.category_id, category.id) : ssrLooseEqual($data.form.category_id, category.id)) ? " selected" : ""}>${ssrInterpolate(category.name)}</option>`);
      });
      _push(`<!--]--></select>`);
      if ($data.form.errors.category_id) {
        _push(`<p class="text-sm text-red-500">${ssrInterpolate($data.form.errors.category_id)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
    if ($data.form.category_id) {
      _push(`<div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Sub Category"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.sub_category_id }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.sub_category_id) ? ssrLooseContain($data.form.sub_category_id, null) : ssrLooseEqual($data.form.sub_category_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a sub category"))}</option><!--[-->`);
      ssrRenderList($data.sub_categories, (category) => {
        _push(`<option${ssrRenderAttr("value", category.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.sub_category_id) ? ssrLooseContain($data.form.sub_category_id, category.id) : ssrLooseEqual($data.form.sub_category_id, category.id)) ? " selected" : ""}>${ssrInterpolate(category.name)}</option>`);
      });
      _push(`<!--]--></select>`);
      if ($data.form.errors.sub_category_id) {
        _push(`<p class="text-sm text-red-500">${ssrInterpolate($data.form.errors.sub_category_id)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Subject"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.subject)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("subject") }, "w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent dark:bg-slate-700 dark:text-white transition-all duration-200"])}"${ssrRenderAttr("placeholder", _ctx.$t("Brief description of your issue"))} required>`);
    if ($options.getFieldError("subject")) {
      _push(`<p class="text-sm text-red-500 flex items-center gap-1">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
      _push(` ${ssrInterpolate($options.getFieldError("subject"))}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Request Details"))} <span class="text-red-500">*</span></label><div class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("details") }, "border border-slate-300 dark:border-slate-600 rounded-xl overflow-hidden"])}">`);
    _push(ssrRenderComponent(_component_ckeditor, {
      id: "ticketDetails",
      editor: $data.editor,
      modelValue: $data.form.details,
      "onUpdate:modelValue": ($event) => $data.form.details = $event,
      onBlur: ($event) => $options.validateField("details", $data.form.details),
      onInput: ($event) => $options.clearValidationError("details"),
      config: $data.editorConfig,
      class: "focus:ring-2 focus:ring-primary"
    }, null, _parent));
    _push(`</div>`);
    if ($options.getFieldError("details")) {
      _push(`<p class="text-sm text-red-500 flex items-center gap-1">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
      _push(` ${ssrInterpolate($options.getFieldError("details"))}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div>`);
    if ($props.custom_fields.length) {
      _push(`<div class="mb-12"><h4 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3"><div class="w-2 h-8 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div> Additional Information </h4><div class="grid grid-cols-1 md:grid-cols-2 gap-6"><!--[-->`);
      ssrRenderList($props.custom_fields, (ticket_field) => {
        _push(`<div class="space-y-2"><label${ssrRenderAttr("for", !["checkbox"].includes(ticket_field.type) ? "ticket_field_" + ticket_field.id : null)} class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(ticket_field.label)} `);
        if (ticket_field.required) {
          _push(`<span class="text-red-500">*</span>`);
        } else {
          _push(`<span class="text-slate-500 text-xs">(optional)</span>`);
        }
        _push(`</label>`);
        if (["text", "email", "number"].includes(ticket_field.type)) {
          _push(`<input${ssrRenderDynamicModel(ticket_field.type, $data.form.custom_field[ticket_field.name], null)}${ssrRenderAttr("type", ticket_field.type)}${ssrRenderAttr("id", "ticket_field_" + ticket_field.id)}${ssrRenderAttr("placeholder", ticket_field.placeholder)}${ssrIncludeBooleanAttr(ticket_field.required) ? " required" : ""} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError(`custom_field_${ticket_field.name}`) }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}">`);
        } else {
          _push(`<!---->`);
        }
        if (ticket_field.type === "textarea") {
          _push(`<textarea${ssrRenderAttr("id", "ticket_field_" + ticket_field.id)} rows="3" class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError(`custom_field_${ticket_field.name}`) }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 resize-none"])}"${ssrRenderAttr("placeholder", ticket_field.placeholder)}>${ssrInterpolate($data.form.custom_field[ticket_field.name])}</textarea>`);
        } else {
          _push(`<!---->`);
        }
        if ($options.getFieldError(`custom_field_${ticket_field.name}`)) {
          _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
          _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
          _push(` ${ssrInterpolate($options.getFieldError(`custom_field_${ticket_field.name}`))}</p>`);
        } else if (ticket_field.hint) {
          _push(`<p class="text-slate-500 dark:text-slate-400 text-sm">${ssrInterpolate(ticket_field.hint)}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--></div></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="space-y-6"><div class="flex items-center gap-3 mb-6"><div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">`);
    _push(ssrRenderComponent(_component_FileText, { class: "w-5 h-5 text-primary" }, null, _parent));
    _push(`</div><h4 class="text-xl font-semibold text-slate-900 dark:text-white">Attachments</h4><span class="text-sm text-slate-500">(Optional)</span></div><input type="file" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf, .zip" class="hidden" multiple="multiple"><div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-12 text-center hover:border-primary transition-all duration-300 group cursor-pointer"><div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-primary/20 transition-colors duration-300">`);
    _push(ssrRenderComponent(_component_Upload, { class: "w-8 h-8 text-primary" }, null, _parent));
    _push(`</div><h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">${ssrInterpolate(_ctx.$t("Choose Files"))}</h5><p class="text-slate-500 dark:text-slate-400">or drag and drop files here</p><p class="text-sm text-slate-400 mt-2">Supports: Images, PDF, DOC, XLS, PPT, TXT, ZIP</p></div>`);
    if ($data.form.files.length) {
      _push(`<div class="space-y-3"><!--[-->`);
      ssrRenderList($data.form.files, (file, fi) => {
        _push(`<div class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError(`file_${fi}`) }, "flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600"])}"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">`);
        _push(ssrRenderComponent(_component_FileText, { class: "w-5 h-5 text-primary" }, null, _parent));
        _push(`</div><div><div class="font-medium text-slate-900 dark:text-white">${ssrInterpolate(file.name)}</div><div class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate($options.getFileSize(file.size))}</div>`);
        if ($options.getFieldError(`file_${fi}`)) {
          _push(`<p class="text-red-500 text-sm flex items-center gap-1 mt-1">`);
          _push(ssrRenderComponent(_component_AlertCircle, { class: "w-3 h-3" }, null, _parent));
          _push(` ${ssrInterpolate($options.getFieldError(`file_${fi}`))}</p>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div><button type="button" class="text-red-500 hover:text-red-700 transition-colors duration-200 p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">`);
        _push(ssrRenderComponent(_component_Trash2, { class: "w-5 h-5 text-current" }, null, _parent));
        _push(`</button></div>`);
      });
      _push(`<!--]--></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="${ssrRenderClass([{ "border border-red-500": $options.hasFieldError("terms") }, "flex items-start gap-3 p-6 bg-slate-50 dark:bg-slate-700 rounded-xl"])}"><input id="terms" type="checkbox"${ssrIncludeBooleanAttr(Array.isArray($data.accept_terms) ? ssrLooseContain($data.accept_terms, null) : $data.accept_terms) ? " checked" : ""} class="mt-1 w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary dark:border-slate-600 dark:bg-slate-800" required><div><label for="terms" class="text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("I agree with the"))} <a${ssrRenderAttr("href", this.route("terms_service"))} target="_blank" class="text-primary hover:underline font-medium">${ssrInterpolate(_ctx.$t("terms and conditions"))}</a><span class="text-red-500">*</span></label>`);
    if ($options.getFieldError("terms")) {
      _push(`<p class="text-red-500 text-sm flex items-center gap-1 mt-2">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
      _push(` ${ssrInterpolate($options.getFieldError("terms"))}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div></div><div class="bg-slate-50 dark:bg-slate-700 px-8 py-6">`);
    if (Object.keys($data.validationErrors).length > 0) {
      _push(`<div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl"><div class="flex items-start gap-3">`);
      _push(ssrRenderComponent(_component_AlertCircle, { class: "w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" }, null, _parent));
      _push(`<div><h4 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</h4><ul class="text-sm text-red-700 dark:text-red-300 space-y-1"><!--[-->`);
      ssrRenderList($data.validationErrors, (error, field) => {
        _push(`<li class="flex items-center gap-2"><span class="w-1 h-1 bg-red-500 rounded-full"></span> ${ssrInterpolate(error)}</li>`);
      });
      _push(`<!--]--></ul></div></div></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="flex justify-end">`);
    _push(ssrRenderComponent(_component_loading_button, {
      loading: $data.isSubmitting,
      class: "inline-flex items-center gap-3 px-10 py-4 bg-gradient-to-r from-primary to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-300 hover:scale-105 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100",
      type: "submit",
      disabled: $data.isSubmitting
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          if (!$data.isSubmitting) {
            _push2(ssrRenderComponent(_component_Send, { class: "w-5 h-5 text-white" }, null, _parent2, _scopeId));
          } else {
            _push2(`<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"${_scopeId}></div>`);
          }
          _push2(`<span${_scopeId}>${ssrInterpolate($data.isSubmitting ? "Submitting..." : _ctx.$t("Submit Ticket"))}</span>`);
        } else {
          return [
            !$data.isSubmitting ? (openBlock(), createBlock(_component_Send, {
              key: 0,
              class: "w-5 h-5 text-white"
            })) : (openBlock(), createBlock("div", {
              key: 1,
              class: "w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"
            })),
            createVNode("span", null, toDisplayString($data.isSubmitting ? "Submitting..." : _ctx.$t("Submit Ticket")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div></div></form></div></div></section>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Landing/Home.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Home = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Home as default
};
