import { Link, Head } from "@inertiajs/vue3";
import { L as Layout } from "./Layout-DXQoZj0i.js";
import { L as LoadingButton } from "./LoadingButton-Dl5SG5JI.js";
import { Bold, Essentials, Italic, Mention, Paragraph, Undo, Heading, Indent, Link as Link$1, Image, Table, List, MediaEmbed, ImageUpload, BlockQuote, ClassicEditor } from "ckeditor5";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import { _ as _sfc_main$1 } from "./UploadAdapter-D1xerzqJ.js";
import { AlertCircle, Send, X, File, Upload, FileText, CheckCircle, Ticket, User, Settings } from "lucide-vue-next";
import { resolveComponent, withCtx, createBlock, createVNode, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderStyle, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList, ssrRenderDynamicModel } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "./Dropdown-DNX6MmV_.js";
import "@popperjs/core";
import "axios";
import "laravel-vue-i18n";
import "moment";
const _sfc_main = {
  metaInfo: { title: "Open Ticket" },
  layout: Layout,
  components: {
    Settings,
    User,
    LoadingButton,
    Head,
    Link,
    Ckeditor,
    Ticket,
    CheckCircle,
    FileText,
    Upload,
    File,
    X,
    Send,
    AlertCircle
  },
  props: {
    all_categories: Array,
    departments: Array,
    types: Array,
    priorities: {
      type: Array,
      default: []
    },
    title: String,
    hide_ticket_fields: Array,
    auth: Object,
    custom_fields: Object
  },
  remember: "form",
  data() {
    var _a;
    return {
      editor: ClassicEditor,
      accept_terms: false,
      categories: [],
      sub_categories: [],
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
      form: this.$inertia.form({
        first_name: "",
        last_name: "",
        email: "",
        priority_id: ((_a = this.priorities) == null ? void 0 : _a.length) ? this.priorities[1].id : null,
        department_id: null,
        category_id: null,
        sub_category_id: null,
        type_id: null,
        subject: null,
        details: "",
        files: [],
        custom_field: {}
      })
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
      if (!this.form.priority_id) {
        this.validationErrors.priority_id = "Priority is required";
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
        this.validationErrors.details = "Ticket details are required";
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
    // Form Methods
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
    handleServerErrors(errors) {
      Object.keys(errors).forEach((key) => {
        this.validationErrors[key] = errors[key];
      });
      this.scrollToFirstError();
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
            this.validationErrors.details = "Ticket details are required";
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
  const _component_Ticket = resolveComponent("Ticket");
  const _component_CheckCircle = resolveComponent("CheckCircle");
  const _component_FileText = resolveComponent("FileText");
  const _component_User = resolveComponent("User");
  const _component_AlertCircle = resolveComponent("AlertCircle");
  const _component_Settings = resolveComponent("Settings");
  const _component_ckeditor = resolveComponent("ckeditor");
  const _component_Upload = resolveComponent("Upload");
  const _component_File = resolveComponent("File");
  const _component_X = resolveComponent("X");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_Send = resolveComponent("Send");
  _push(`<div${ssrRenderAttrs(_attrs)}>`);
  _push(ssrRenderComponent(_component_Head, { title: "Open Ticket" }, null, _parent));
  _push(`<section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-slate-900 via-primary-900 to-slate-800"><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-transparent to-blue-600/20"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse_at_center,white,transparent_70%)" })}"></div></div><div class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-500/20 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-4 h-4 bg-white/30 rotate-45 animate-spin"></div><div class="absolute top-40 right-32 w-6 h-6 bg-primary-300/40 rounded-full animate-ping"></div><div class="absolute bottom-32 left-16 w-3 h-3 bg-blue-300/50 rotate-12 animate-bounce"></div></div><div class="container relative z-10 py-20"><div class="text-center max-w-4xl mx-auto"><div class="inline-flex items-center gap-2 px-4 py-2 mb-8 text-sm font-medium text-white bg-white/10 rounded-full border border-white/20 shadow-lg" style="${ssrRenderStyle({ "backdrop-filter": "blur(12px)" })}">`);
  _push(ssrRenderComponent(_component_Ticket, { class: "w-4 h-4 text-yellow-300" }, null, _parent));
  _push(`<span>Submit Support Request</span></div><h1 class="mb-6 text-5xl font-bold leading-tight text-white sm:text-6xl lg:text-7xl"> Open a Ticket </h1><p class="mb-10 text-xl leading-relaxed text-white/80 sm:text-2xl max-w-3xl mx-auto"> Need help? Submit a support ticket and our team will get back to you as soon as possible. </p><div class="flex flex-wrap items-center justify-center gap-8 text-white/70"><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Quick Response</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">Expert Support</span></div><div class="flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_CheckCircle, { class: "w-4 h-4 text-green-400" }, null, _parent));
  _push(`<span class="text-sm font-medium">24/7 Available</span></div></div></div></div><div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"><div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center"><div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div></div></div></section><section class="relative py-24 lg:py-32 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900"><div class="absolute inset-0 overflow-hidden"><div class="absolute top-0 left-0 w-full h-full opacity-5" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "background-position": "center", "background-repeat": "repeat" })}"></div><div class="absolute top-1/4 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div><div class="absolute bottom-1/4 left-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div></div><div class="container relative z-10"><div class="text-center mb-16"><div class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-sm font-semibold text-primary bg-primary/10 rounded-full border border-primary/20">`);
  _push(ssrRenderComponent(_component_FileText, { class: "w-4 h-4 text-primary" }, null, _parent));
  _push(` Support Request Form </div><h2 class="mb-6 text-4xl font-bold text-slate-900 dark:text-white sm:text-5xl lg:text-6xl"> Submit Your Request </h2><p class="text-xl leading-relaxed text-slate-600 dark:text-slate-300 max-w-3xl mx-auto"> Fill out the form below with your details and we&#39;ll get back to you as soon as possible. </p></div><div class="max-w-4xl mx-auto"><form enctype="multipart/form-data" class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden"><div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-8"><div class="flex items-center gap-4"><div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_Ticket, { class: "w-8 h-8 text-white" }, null, _parent));
  _push(`</div><div><h3 class="text-3xl font-bold text-white">Support Ticket</h3><p class="text-white/80 text-lg">Please provide as much detail as possible</p></div></div></div><div class="p-8"><div class="mb-12"><div class="flex items-center gap-3 mb-6"><div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_User, { class: "w-5 h-5 text-primary" }, null, _parent));
  _push(`</div><h4 class="text-xl font-semibold text-slate-900 dark:text-white">Personal Information</h4></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("First name"))} <span class="text-red-500">*</span></label><input type="text"${ssrRenderAttr("value", $data.form.first_name)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("first_name") }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}" required>`);
  if ($options.getFieldError("first_name")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
    _push(` ${ssrInterpolate($options.getFieldError("first_name"))}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Last name"))} <span class="text-red-500">*</span></label><input type="text"${ssrRenderAttr("value", $data.form.last_name)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("last_name") }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}" required>`);
  if ($options.getFieldError("last_name")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
    _push(` ${ssrInterpolate($options.getFieldError("last_name"))}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Email Address"))} <span class="text-red-500">*</span></label><input type="email"${ssrRenderAttr("value", $data.form.email)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("email") }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}" required>`);
  if ($options.getFieldError("email")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
    _push(` ${ssrInterpolate($options.getFieldError("email"))}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div></div><div class="mb-6"><div class="flex items-center gap-3 mb-6"><div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_Settings, { class: "w-5 h-5 text-primary" }, null, _parent));
  _push(`</div><h4 class="text-xl font-semibold text-slate-900 dark:text-white">Ticket Details</h4></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Priority"))} <span class="text-red-500">*</span></label><select class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("priority_id") }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}" required><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.priority_id) ? ssrLooseContain($data.form.priority_id, null) : ssrLooseEqual($data.form.priority_id, null)) ? " selected" : ""}>Select Priority</option><!--[-->`);
  ssrRenderList($props.priorities, (s) => {
    _push(`<option${ssrRenderAttr("value", s.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.priority_id) ? ssrLooseContain($data.form.priority_id, s.id) : ssrLooseEqual($data.form.priority_id, s.id)) ? " selected" : ""}>${ssrInterpolate(s.name)}</option>`);
  });
  _push(`<!--]--></select>`);
  if ($options.getFieldError("priority_id")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
    _push(` ${ssrInterpolate($options.getFieldError("priority_id"))}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Type"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.type_id }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.type_id) ? ssrLooseContain($data.form.type_id, null) : ssrLooseEqual($data.form.type_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a type"))}</option><!--[-->`);
  ssrRenderList($props.types, (type) => {
    _push(`<option${ssrRenderAttr("value", type.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.type_id) ? ssrLooseContain($data.form.type_id, type.id) : ssrLooseEqual($data.form.type_id, type.id)) ? " selected" : ""}>${ssrInterpolate(type.name)}</option>`);
  });
  _push(`<!--]--></select>`);
  if ($data.form.errors.type_id) {
    _push(`<p class="text-red-500 text-sm">${ssrInterpolate($data.form.errors.type_id)}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div>`);
  if (!$props.hide_ticket_fields.includes("department")) {
    _push(`<div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Department"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.department_id }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.department_id) ? ssrLooseContain($data.form.department_id, null) : ssrLooseEqual($data.form.department_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a department"))}</option><!--[-->`);
    ssrRenderList($props.departments, (department) => {
      _push(`<option${ssrRenderAttr("value", department.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.department_id) ? ssrLooseContain($data.form.department_id, department.id) : ssrLooseEqual($data.form.department_id, department.id)) ? " selected" : ""}>${ssrInterpolate(department.name)}</option>`);
    });
    _push(`<!--]--></select>`);
    if ($data.form.errors.department_id) {
      _push(`<p class="text-red-500 text-sm">${ssrInterpolate($data.form.errors.department_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if ($data.form.department_id) {
    _push(`<div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Category"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.category_id }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.category_id) ? ssrLooseContain($data.form.category_id, null) : ssrLooseEqual($data.form.category_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a category"))}</option><!--[-->`);
    ssrRenderList($data.categories, (category) => {
      _push(`<option${ssrRenderAttr("value", category.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.category_id) ? ssrLooseContain($data.form.category_id, category.id) : ssrLooseEqual($data.form.category_id, category.id)) ? " selected" : ""}>${ssrInterpolate(category.name)}</option>`);
    });
    _push(`<!--]--></select>`);
    if ($data.form.errors.category_id) {
      _push(`<p class="text-red-500 text-sm">${ssrInterpolate($data.form.errors.category_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  if ($data.form.category_id) {
    _push(`<div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Sub Category"))}</label><select class="${ssrRenderClass([{ "border-red-500": $data.form.errors.sub_category_id }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray($data.form.sub_category_id) ? ssrLooseContain($data.form.sub_category_id, null) : ssrLooseEqual($data.form.sub_category_id, null)) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a sub category"))}</option><!--[-->`);
    ssrRenderList($data.sub_categories, (category) => {
      _push(`<option${ssrRenderAttr("value", category.id)}${ssrIncludeBooleanAttr(Array.isArray($data.form.sub_category_id) ? ssrLooseContain($data.form.sub_category_id, category.id) : ssrLooseEqual($data.form.sub_category_id, category.id)) ? " selected" : ""}>${ssrInterpolate(category.name)}</option>`);
    });
    _push(`<!--]--></select>`);
    if ($data.form.errors.sub_category_id) {
      _push(`<p class="text-red-500 text-sm">${ssrInterpolate($data.form.errors.sub_category_id)}</p>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="mt-6 space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Subject"))} <span class="text-red-500">*</span></label><input type="text"${ssrRenderAttr("value", $data.form.subject)} class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("subject") }, "w-full px-4 py-3 text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300"])}" placeholder="Brief description of your issue" required>`);
  if ($options.getFieldError("subject")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
    _push(` ${ssrInterpolate($options.getFieldError("subject"))}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div><div class="mb-12"><div class="space-y-2"><label class="block text-sm font-medium text-slate-700 dark:text-slate-300">${ssrInterpolate(_ctx.$t("Request Details"))} <span class="text-red-500">*</span></label><div class="${ssrRenderClass([{ "border-red-500": $options.hasFieldError("details") }, "border border-slate-200 dark:border-slate-600 rounded-xl overflow-hidden"])}">`);
  _push(ssrRenderComponent(_component_ckeditor, {
    id: "ticketDetails",
    editor: $data.editor,
    modelValue: $data.form.details,
    "onUpdate:modelValue": ($event) => $data.form.details = $event,
    onBlur: ($event) => $options.validateField("details", $data.form.details),
    onInput: ($event) => $options.clearValidationError("details"),
    config: $data.editorConfig,
    class: "min-h-[200px]"
  }, null, _parent));
  _push(`</div>`);
  if ($options.getFieldError("details")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1">`);
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
  _push(`<div class="mb-12"><div class="flex items-center gap-3 mb-6"><div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_FileText, { class: "w-5 h-5 text-primary" }, null, _parent));
  _push(`</div><h4 class="text-xl font-semibold text-slate-900 dark:text-white">Attachments</h4><span class="text-sm text-slate-500">(Optional)</span></div><input type="file" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf, .zip" class="hidden" multiple="multiple"><div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-8 text-center hover:border-primary-400 transition-colors duration-300 cursor-pointer"><div class="flex flex-col items-center gap-4"><div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-2xl flex items-center justify-center">`);
  _push(ssrRenderComponent(_component_Upload, { class: "w-8 h-8 text-primary-600" }, null, _parent));
  _push(`</div><div><h5 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">${ssrInterpolate(_ctx.$t("Attach files"))}</h5><p class="text-slate-600 dark:text-slate-400">Click to browse or drag and drop files here</p><p class="text-xs text-slate-500 dark:text-slate-500 mt-1">Supports: Images, PDF, DOC, XLS, PPT, TXT, ZIP (Max 10MB each)</p></div></div></div>`);
  if ($data.form.files.length) {
    _push(`<div class="mt-6 space-y-3"><!--[-->`);
    ssrRenderList($data.form.files, (file, fi) => {
      _push(`<div class="${ssrRenderClass([{ "border border-red-500": $options.hasFieldError(`file_${fi}`) }, "flex items-center justify-between bg-slate-50 dark:bg-slate-700 rounded-xl p-4"])}"><div class="flex items-center gap-3">`);
      _push(ssrRenderComponent(_component_File, { class: "w-5 h-5 text-slate-500" }, null, _parent));
      _push(`<div><p class="font-medium text-slate-900 dark:text-white">${ssrInterpolate(file.name)}</p><p class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate($options.getFileSize(file.size))}</p>`);
      if ($options.getFieldError(`file_${fi}`)) {
        _push(`<p class="text-red-500 text-sm flex items-center gap-1 mt-1">`);
        _push(ssrRenderComponent(_component_AlertCircle, { class: "w-3 h-3" }, null, _parent));
        _push(` ${ssrInterpolate($options.getFieldError(`file_${fi}`))}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><button type="button" class="text-red-500 hover:text-red-700 transition-colors duration-300">`);
      _push(ssrRenderComponent(_component_X, { class: "w-5 h-5" }, null, _parent));
      _push(`</button></div>`);
    });
    _push(`<!--]--></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="mb-8"><div class="${ssrRenderClass([{ "border border-red-500": $options.hasFieldError("terms") }, "flex items-start gap-4 p-6 bg-slate-50 dark:bg-slate-700 rounded-2xl"])}"><div class="flex items-center h-5 mt-1"><input id="terms" type="checkbox"${ssrIncludeBooleanAttr(Array.isArray($data.accept_terms) ? ssrLooseContain($data.accept_terms, null) : $data.accept_terms) ? " checked" : ""} class="w-5 h-5 text-primary-600 bg-slate-100 border-slate-300 rounded focus:ring-primary-500 focus:ring-2" required></div><div><label for="terms" class="text-sm font-medium text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t("I agree to the"))} <a${ssrRenderAttr("href", this.route("terms_service"))} target="_blank" class="text-primary-600 hover:text-primary-700 underline font-semibold">${ssrInterpolate(_ctx.$t("terms and conditions"))}</a></label><p class="text-xs text-slate-500 dark:text-slate-400 mt-1">By submitting this ticket, you agree to our terms of service and privacy policy.</p>`);
  if ($options.getFieldError("terms")) {
    _push(`<p class="text-red-500 text-sm flex items-center gap-1 mt-2">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-4 h-4" }, null, _parent));
    _push(` ${ssrInterpolate($options.getFieldError("terms"))}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div></div><div class="flex justify-center">`);
  _push(ssrRenderComponent(_component_loading_button, {
    loading: $data.isSubmitting,
    class: "group relative inline-flex items-center gap-3 px-12 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-2xl hover:from-primary-700 hover:to-primary-800 transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-primary-300 text-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100",
    type: "submit",
    disabled: $data.isSubmitting
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        if (!$data.isSubmitting) {
          _push2(ssrRenderComponent(_component_Send, { class: "w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" }, null, _parent2, _scopeId));
        } else {
          _push2(`<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"${_scopeId}></div>`);
        }
        _push2(`<span${_scopeId}>${ssrInterpolate($data.isSubmitting ? "Submitting..." : _ctx.$t("Submit Ticket"))}</span>`);
      } else {
        return [
          !$data.isSubmitting ? (openBlock(), createBlock(_component_Send, {
            key: 0,
            class: "w-5 h-5 group-hover:translate-x-1 transition-transform duration-300"
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
  _push(`</div>`);
  if (Object.keys($data.validationErrors).length > 0) {
    _push(`<div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl"><div class="flex items-start gap-3">`);
    _push(ssrRenderComponent(_component_AlertCircle, { class: "w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" }, null, _parent));
    _push(`<div><h4 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</h4><ul class="text-sm text-red-700 dark:text-red-300 space-y-1"><!--[-->`);
    ssrRenderList($data.validationErrors, (error, field) => {
      _push(`<li class="flex items-center gap-2"><span class="w-1 h-1 bg-red-500 rounded-full"></span> ${ssrInterpolate(error)}</li>`);
    });
    _push(`<!--]--></ul></div></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></form></div></div></section></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Landing/OpenTicket.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const OpenTicket = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  OpenTicket as default
};
