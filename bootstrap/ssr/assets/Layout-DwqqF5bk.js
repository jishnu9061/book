import { I as Icon, D as Dropdown } from "./Dropdown-DNX6MmV_.js";
import { _ as _export_sfc, L as Logo, F as FlashMessages } from "./FlashMessages-DUb4hfI8.js";
import { Link, usePage } from "@inertiajs/vue3";
import { ChevronDown, Home, LogOut, User, Moon, Sun, ChevronRight, X, Menu } from "lucide-vue-next";
import { resolveComponent, mergeProps, withCtx, createVNode, createBlock, createCommentVNode, toDisplayString, openBlock, useSSRContext, ref, onMounted, unref, createTextVNode, computed, Fragment, renderList } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderClass, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderSlot } from "vue/server-renderer";
import moment from "moment";
import { getActiveLanguage, loadLanguageAsync } from "laravel-vue-i18n";
import axios from "axios";
import { EllipsisVerticalIcon, CheckCircleIcon } from "@heroicons/vue/24/outline";
const _sfc_main$2 = {
  components: {
    Icon,
    Link,
    ChevronDown
  },
  data() {
    return {
      user: null,
      expandedMenus: [],
      menu_items: [
        { "name": "Dashboard", "route": "dashboard", "url": "dashboard", "icon": "dashboard" },
        { "name": "Tickets", "route": "tickets", "url": "tickets", "icon": "ticket" }
      ],
      enable_option: {}
    };
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1);
      currentUrl = currentUrl.replace("dashboard/", "");
      if (urls[0] === "") {
        return currentUrl === "";
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length;
    },
    toggleSubmenu(index) {
      const expandedIndex = this.expandedMenus.indexOf(index);
      if (expandedIndex > -1) {
        this.expandedMenus.splice(expandedIndex, 1);
      } else {
        this.expandedMenus.push(index);
      }
    },
    addActiveClass(e) {
      e.currentTarget.classList.toggle("hover");
    }
  },
  created() {
    var _a;
    try {
      this.user = (_a = this.$page.props.auth) == null ? void 0 : _a.user;
      if (!this.user) {
        console.warn("MainMenu: No user found in auth props");
        return;
      }
      const user_access = this.user.access || {};
      let enable_option = {};
      if (this.$page.props.enable_options && this.$page.props.enable_options.value) {
        try {
          let options = JSON.parse(this.$page.props.enable_options.value);
          options.forEach((option) => {
            enable_option[option.slug] = !!option.value;
          });
        } catch (e) {
          console.warn("MainMenu: Error parsing enable_options:", e);
        }
      }
      const hasAccess = (module, action = "read") => {
        return user_access[module] && user_access[module][action];
      };
      if (enable_option.chat && (hasAccess("chat", "read") || hasAccess("chat", "update") || hasAccess("chat", "create") || hasAccess("chat", "delete"))) {
        this.menu_items.push({ "name": "Chat", "route": "chat", "url": "chat", "icon": "chat" });
      }
      if (enable_option.faq && (hasAccess("faq", "read") || hasAccess("faq", "update") || hasAccess("faq", "create") || hasAccess("faq", "delete"))) {
        this.menu_items.push({ "name": "FAQs", "route": "faqs", "url": "faqs", "icon": "faq" });
      }
      if (enable_option.blog && (hasAccess("blog", "read") || hasAccess("blog", "update") || hasAccess("blog", "create") || hasAccess("blog", "delete"))) {
        this.menu_items.push({ "name": "Blog", "route": "posts", "url": "posts", "icon": "post" });
      }
      if (enable_option.kb && (hasAccess("knowledge_base", "read") || hasAccess("knowledge_base", "update") || hasAccess("knowledge_base", "create") || hasAccess("knowledge_base", "delete"))) {
        this.menu_items.push({ "name": "Knowledge Base", "route": "knowledge_base", "url": "knowledge_base", "icon": "knowledge" });
      }
      if (hasAccess("customer", "read") || hasAccess("customer", "update") || hasAccess("customer", "create") || hasAccess("customer", "delete")) {
        this.menu_items.push({ "name": "Customers", "route": "customers", "url": "customers", "icon": "all_users" });
      }
      if (enable_option.note) {
        this.menu_items.push({ "name": "Notes", "route": "notes", "url": "notes", "icon": "notes" });
      }
      if (enable_option.contact && (hasAccess("contact", "read") || hasAccess("contact", "update") || hasAccess("contact", "create") || hasAccess("contact", "delete"))) {
        this.menu_items.push({ "name": "Contacts", "route": "contacts", "url": "contacts", "icon": "contact" });
      }
      if (enable_option.organization && (hasAccess("organization", "read") || hasAccess("organization", "update") || hasAccess("organization", "create") || hasAccess("organization", "delete"))) {
        this.menu_items.push({ "name": "Organizations", "route": "organizations", "url": "organizations", "icon": "office" });
      }
      if (hasAccess("user", "read") || hasAccess("user", "update") || hasAccess("user", "create") || hasAccess("user", "delete")) {
        this.menu_items.push({ "name": "Manage Users", "route": "users", "url": "users", "icon": "users" });
      }
      const settingSubmenus = [];
      if (this.user.role && this.user.role.slug === "admin") {
        settingSubmenus.push({ "name": "License", "route": "license.settings", "url": "settings/license", "icon": "user_role" });
        settingSubmenus.push({ "name": "User Roles", "route": "roles", "url": "settings/roles", "icon": "user_role" });
      }
      if (hasAccess("global", "read") || hasAccess("global", "update") || hasAccess("global", "create") || hasAccess("global", "delete")) {
        settingSubmenus.push({ "name": "Global", "route": "global", "url": "settings/global", "icon": "global_setting" });
      }
      if (hasAccess("global", "read") || hasAccess("global", "update") || hasAccess("global", "create") || hasAccess("global", "delete")) {
        settingSubmenus.push({ "name": "Custom fields", "route": "tickets.builder", "url": "settings/custom-form", "icon": "form-builder" });
      }
      if (hasAccess("department", "read") || hasAccess("department", "update") || hasAccess("department", "create") || hasAccess("department", "delete")) {
        settingSubmenus.push({ "name": "Departments", "route": "departments", "url": "settings/departments", "icon": "departments" });
      }
      if (hasAccess("category", "read") || hasAccess("category", "update") || hasAccess("category", "create") || hasAccess("category", "delete")) {
        settingSubmenus.push({ "name": "Categories", "route": "categories", "url": "settings/categories", "icon": "category" });
      }
      if (hasAccess("status", "read") || hasAccess("status", "update") || hasAccess("status", "create") || hasAccess("status", "delete")) {
        settingSubmenus.push({ "name": "Status", "route": "statuses", "url": "settings/statuses", "icon": "status" });
      }
      if (hasAccess("priority", "read") || hasAccess("priority", "update") || hasAccess("priority", "create") || hasAccess("priority", "delete")) {
        settingSubmenus.push({ "name": "Priorities", "route": "priorities", "url": "settings/priorities", "icon": "priorities" });
      }
      if (hasAccess("type", "read") || hasAccess("type", "update") || hasAccess("type", "create") || hasAccess("type", "delete")) {
        settingSubmenus.push({ "name": "Types", "route": "types", "url": "settings/types", "icon": "types" });
      }
      if (hasAccess("language", "read") || hasAccess("language", "update") || hasAccess("language", "create") || hasAccess("language", "delete")) {
        settingSubmenus.push({ "name": "Languages", "route": "languages", "url": "settings/languages", "icon": "edit" });
      }
      if (hasAccess("email_template", "read") || hasAccess("email_template", "update") || hasAccess("email_template", "create") || hasAccess("email_template", "delete")) {
        settingSubmenus.push({ "name": "Email Templates", "route": "templates", "url": "settings/templates", "icon": "email" });
      }
      if (hasAccess("smtp", "read") || hasAccess("smtp", "update") || hasAccess("smtp", "create") || hasAccess("smtp", "delete")) {
        settingSubmenus.push({ "name": "SMTP Mail", "route": "settings.smtp", "url": "settings/smtp", "icon": "email_template" });
      }
      if (hasAccess("pusher", "read") || hasAccess("pusher", "update") || hasAccess("pusher", "create") || hasAccess("pusher", "delete")) {
        settingSubmenus.push({ "name": "Pusher Chat", "route": "settings.pusher", "url": "settings/pusher", "icon": "chat" });
      }
      if (this.user.role && this.user.role.slug === "admin") {
        settingSubmenus.push({ "name": "Email to ticket", "route": "settings.piping", "url": "settings/piping", "icon": "ticket" });
      }
      if (settingSubmenus.length) {
        this.menu_items.push({ "name": "Settings", "route": "", "url": "settings", "icon": "settings", "submenu": settingSubmenus });
      }
      if (hasAccess("front_page", "read") || hasAccess("front_page", "update") || hasAccess("front_page", "create") || hasAccess("front_page", "delete")) {
        this.menu_items.push(
          {
            "name": "Front Pages",
            "route": "",
            "url": "front_pages",
            "icon": "gear",
            "submenu": [
              { "name": "Home", "route": "front_pages.page", "url": "front_pages/home", "icon": "page", "param": "home" },
              { "name": "Contact", "route": "front_pages.page", "url": "front_pages/contact", "icon": "page", "param": "contact" },
              { "name": "Services", "route": "front_pages.page", "url": "front_pages/services", "icon": "page", "param": "services" },
              { "name": "Privacy Policy", "route": "front_pages.page", "url": "front_pages/privacy", "icon": "page", "param": "privacy" },
              { "name": "Terms of services", "route": "front_pages.page", "url": "front_pages/terms", "icon": "page", "param": "terms" },
              { "name": "Footer", "route": "front_pages.page", "url": "front_pages/footer", "icon": "page", "param": "footer" }
            ]
          }
        );
      }
      if (this.user.role && this.user.role.slug === "admin") {
        this.menu_items.push({ "name": "System Update", "route": "settings.update", "url": "settings/update", "icon": "archive" });
      }
    } catch (error) {
      console.error("MainMenu: Error in created hook:", error);
      this.menu_items = [
        { "name": "Dashboard", "route": "dashboard", "url": "dashboard", "icon": "dashboard" },
        { "name": "Tickets", "route": "tickets", "url": "tickets", "icon": "ticket" }
      ];
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_icon = resolveComponent("icon");
  const _component_ChevronDown = resolveComponent("ChevronDown");
  _push(`<nav${ssrRenderAttrs(mergeProps({ class: "px-4 py-6 space-y-2" }, _attrs))}><!--[-->`);
  ssrRenderList($data.menu_items, (menu_item, m_index) => {
    _push(`<div class="space-y-1"><div class="${ssrRenderClass([$options.isUrl(menu_item.url) ? "bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800" : "hover:bg-slate-50 dark:hover:bg-slate-700", "group relative rounded-lg border transition-all duration-200"])}">`);
    if (!menu_item.submenu) {
      _push(ssrRenderComponent(_component_Link, {
        href: menu_item.route ? _ctx.route(menu_item.route) : "#",
        class: ["flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200", $options.isUrl(menu_item.url) ? "text-primary-700 dark:text-primary-300" : "text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white"]
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="flex-shrink-0 w-5 h-5 flex items-center justify-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_component_icon, {
              name: menu_item.icon,
              class: "w-5 h-5"
            }, null, _parent2, _scopeId));
            _push2(`</div><span class="flex-1"${_scopeId}>${ssrInterpolate(_ctx.$t(menu_item.name))}</span>`);
            if ($options.isUrl(menu_item.url)) {
              _push2(`<div class="w-2 h-2 bg-primary-600 rounded-full"${_scopeId}></div>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("div", { class: "flex-shrink-0 w-5 h-5 flex items-center justify-center" }, [
                createVNode(_component_icon, {
                  name: menu_item.icon,
                  class: "w-5 h-5"
                }, null, 8, ["name"])
              ]),
              createVNode("span", { class: "flex-1" }, toDisplayString(_ctx.$t(menu_item.name)), 1),
              $options.isUrl(menu_item.url) ? (openBlock(), createBlock("div", {
                key: 0,
                class: "w-2 h-2 bg-primary-600 rounded-full"
              })) : createCommentVNode("", true)
            ];
          }
        }),
        _: 2
      }, _parent));
    } else {
      _push(`<div><button class="${ssrRenderClass([$options.isUrl(menu_item.url) ? "text-primary-700 dark:text-primary-300" : "text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white", "flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 group"])}"><div class="flex items-center gap-3"><div class="flex-shrink-0 w-5 h-5 flex items-center justify-center">`);
      _push(ssrRenderComponent(_component_icon, {
        name: menu_item.icon,
        class: "w-5 h-5"
      }, null, _parent));
      _push(`</div><span class="flex-1 text-left">${ssrInterpolate(_ctx.$t(menu_item.name))}</span></div>`);
      _push(ssrRenderComponent(_component_ChevronDown, {
        class: ["w-4 h-4 transition-transform duration-200", $data.expandedMenus.includes(m_index) ? "rotate-180" : ""]
      }, null, _parent));
      _push(`</button>`);
      if ($data.expandedMenus.includes(m_index)) {
        _push(`<div class="mt-1 ml-8 space-y-1 border-l border-slate-200 dark:border-slate-700 pl-4"><!--[-->`);
        ssrRenderList(menu_item.submenu, (sub_menu_item, s_m_index) => {
          _push(ssrRenderComponent(_component_Link, {
            key: s_m_index,
            href: sub_menu_item.param ? _ctx.route(sub_menu_item.route, sub_menu_item.param) : _ctx.route(sub_menu_item.route),
            class: ["flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors duration-200 group", $options.isUrl(sub_menu_item.url) ? "bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300" : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700"]
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<div class="flex-shrink-0 w-4 h-4 flex items-center justify-center"${_scopeId}>`);
                if (sub_menu_item.icon) {
                  _push2(ssrRenderComponent(_component_icon, {
                    name: sub_menu_item.icon,
                    class: "w-4 h-4"
                  }, null, _parent2, _scopeId));
                } else {
                  _push2(ssrRenderComponent(_component_icon, {
                    name: "dash",
                    class: "w-4 h-4"
                  }, null, _parent2, _scopeId));
                }
                _push2(`</div><span class="flex-1"${_scopeId}>${ssrInterpolate(_ctx.$t(sub_menu_item.name))}</span>`);
                if ($options.isUrl(sub_menu_item.url)) {
                  _push2(`<div class="w-1.5 h-1.5 bg-primary-600 rounded-full"${_scopeId}></div>`);
                } else {
                  _push2(`<!---->`);
                }
              } else {
                return [
                  createVNode("div", { class: "flex-shrink-0 w-4 h-4 flex items-center justify-center" }, [
                    sub_menu_item.icon ? (openBlock(), createBlock(_component_icon, {
                      key: 0,
                      name: sub_menu_item.icon,
                      class: "w-4 h-4"
                    }, null, 8, ["name"])) : (openBlock(), createBlock(_component_icon, {
                      key: 1,
                      name: "dash",
                      class: "w-4 h-4"
                    }))
                  ]),
                  createVNode("span", { class: "flex-1" }, toDisplayString(_ctx.$t(sub_menu_item.name)), 1),
                  $options.isUrl(sub_menu_item.url) ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "w-1.5 h-1.5 bg-primary-600 rounded-full"
                  })) : createCommentVNode("", true)
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    }
    _push(`</div></div>`);
  });
  _push(`<!--]--></nav>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/MainMenu.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const MainMenu = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$1 = {
  __name: "NotificationBell",
  __ssrInlineRender: true,
  setup(__props) {
    const showDropdown = ref(false);
    const showOptionsMenu = ref(false);
    const isBellShaking = ref(false);
    const page = usePage();
    const notifications = ref(page.props.notifications || []);
    const notificationCount = ref(page.props.notification_count || 0);
    onMounted(() => {
      if (window.Echo && page.props.auth.user) {
        window.Echo.private(`App.Models.User.${page.props.auth.user.id}`).notification((notification) => {
          const newNotification = {
            id: notification.id,
            // Pusher doesn't send the UUID, so we need to generate it or get it from the payload
            data: { ...notification },
            read_at: null
          };
          notifications.value.unshift(newNotification);
          notificationCount.value++;
          isBellShaking.value = true;
          setTimeout(() => {
            isBellShaking.value = false;
          }, 700);
        });
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative flex" }, _attrs))}><button type="button" class="relative rounded-full text-gray-600 hover:text-black focus:outline-none"><span class="sr-only">View notifications</span>`);
      _push(ssrRenderComponent(Icon, {
        name: "notification",
        class: ["h-7 w-7", { "animate-bell-shake": isBellShaking.value }],
        "aria-hidden": "true"
      }, null, _parent));
      if (notificationCount.value > 0) {
        _push(`<span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">${ssrInterpolate(notificationCount.value)}</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</button>`);
      if (showDropdown.value) {
        _push(`<div class="absolute right-0 z-10 top-7 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"><div class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 font-bold border-b"><span>Notifications</span><div class="relative"><button class="p-1 rounded-full hover:bg-gray-200">`);
        _push(ssrRenderComponent(unref(EllipsisVerticalIcon), { class: "h-5 w-5" }, null, _parent));
        _push(`</button>`);
        if (showOptionsMenu.value) {
          _push(`<div class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5"><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark all as read</a>`);
          _push(ssrRenderComponent(unref(Link), {
            href: _ctx.route("notifications.index"),
            class: "block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`All Notifications`);
              } else {
                return [
                  createTextVNode("All Notifications")
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
        if (notifications.value.length > 0) {
          _push(`<div class="max-h-96 overflow-y-auto"><!--[-->`);
          ssrRenderList(notifications.value, (notification) => {
            _push(`<a href="#" class="${ssrRenderClass([{ "font-semibold": !notification.read_at }, "block px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 border-b"])}"><p class="text-gray-800">${ssrInterpolate(notification.data.message)}</p><p class="text-xs text-gray-400 mt-1">${ssrInterpolate(_ctx.$t("Ticket"))}: ${ssrInterpolate(notification.data.ticket_subject)}</p></a>`);
          });
          _push(`<!--]--></div>`);
        } else {
          _push(`<div class="px-4 py-5 text-center">`);
          _push(ssrRenderComponent(unref(CheckCircleIcon), { class: "h-8 w-8 text-green-400 mx-auto mb-2" }, null, _parent));
          _push(`<p class="text-sm text-gray-500">You&#39;re all caught up!</p></div>`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/NotificationBell.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  components: {
    NotificationBell: _sfc_main$1,
    Dropdown,
    FlashMessages,
    Icon,
    Logo,
    Link,
    MainMenu,
    Menu,
    X,
    ChevronDown,
    ChevronRight,
    Sun,
    Moon,
    User,
    LogOut,
    Home
  },
  props: {
    title: String
  },
  data() {
    return {
      time: "",
      current_mode: "light",
      modes: ["dark", "light"],
      edit_route: "",
      locale: this.$page.props.auth.user.locale || this.$page.props.settings.default_language,
      mobileMenuOpen: false
    };
  },
  computed: {
    selected_language() {
      if (!this.$page.props.languages || !Array.isArray(this.$page.props.languages)) {
        return { code: "en", name: "English" };
      }
      return this.$page.props.languages.find((language) => language.code === this.$page.props.locale) || { code: "en", name: "English" };
    },
    languages_except_selected() {
      if (!this.$page.props.languages || !Array.isArray(this.$page.props.languages)) {
        return [];
      }
      return this.$page.props.languages.filter((language) => language.code !== this.$page.props.locale);
    }
  },
  setup() {
    const page = usePage();
    const license_invalid = computed(() => page.props.license_invalid);
    return {
      license_invalid
    };
  },
  methods: {
    updateLanguage(code) {
      axios.post(this.route("language", code), {}).then((response) => {
        if (response.data) {
          window.location.reload();
        }
      });
    },
    generateGreetings() {
      const currentHour = this.moment().format("HH");
      if (currentHour >= 3 && currentHour < 12) {
        return "Good Morning";
      } else if (currentHour >= 12 && currentHour < 15) {
        return "Good Noon";
      } else if (currentHour >= 15 && currentHour < 18) {
        return "Good Afternoon";
      } else if (currentHour >= 18 && currentHour < 20) {
        return "Good Evening";
      } else {
        return "Hello";
      }
    },
    switchMode() {
      this.current_mode = this.current_mode === "light" ? "dark" : "light";
      localStorage.setItem("current_mode", this.current_mode);
    },
    detectCurrentUrl() {
      const url = this.$page.url;
      const splitUrl = url.split("/");
      let editString = ["edit", "create"].includes(url.substring(url.lastIndexOf("/") + 1));
      if (!editString) {
        editString = splitUrl[splitUrl.length - 2] === "tickets";
      }
      let editRoute = url.split("/")[2];
      if (["settings", "front_pages"].includes(editRoute)) {
        editRoute = url.split("/")[3];
      }
      this.edit_route = editString ? editRoute : "";
    },
    closeMobileMenu() {
      this.mobileMenuOpen = false;
    }
  },
  updated() {
    this.detectCurrentUrl();
  },
  created() {
    this.moment = moment;
    let vm = this;
    if (localStorage.getItem("current_mode")) {
      this.current_mode = localStorage.getItem("current_mode");
    }
    vm.time = vm.moment().format("MMMM Do YYYY, h:mm A");
    window.setInterval(function() {
      vm.time = vm.moment().format("MMMM Do YYYY, h:mm A");
    }, 1e3);
    this.detectCurrentUrl();
    if (getActiveLanguage() !== this.locale) {
      loadLanguageAsync(this.locale);
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_logo = resolveComponent("logo");
  const _component_X = resolveComponent("X");
  const _component_main_menu = resolveComponent("main-menu");
  const _component_Menu = resolveComponent("Menu");
  const _component_NotificationBell = resolveComponent("NotificationBell");
  const _component_dropdown = resolveComponent("dropdown");
  const _component_icon = resolveComponent("icon");
  const _component_ChevronDown = resolveComponent("ChevronDown");
  const _component_Sun = resolveComponent("Sun");
  const _component_Moon = resolveComponent("Moon");
  const _component_User = resolveComponent("User");
  const _component_LogOut = resolveComponent("LogOut");
  const _component_Home = resolveComponent("Home");
  const _component_ChevronRight = resolveComponent("ChevronRight");
  const _component_flash_messages = resolveComponent("flash-messages");
  _push(`<div${ssrRenderAttrs(mergeProps({
    class: ["min-h-screen bg-slate-50 dark:bg-slate-900 transition-colors duration-300", $data.current_mode],
    dir: _ctx.$page.props.dir
  }, _attrs))}><div id="dropdown"></div><div class="flex h-screen overflow-hidden">`);
  if ($data.mobileMenuOpen) {
    _push(`<div class="fixed inset-0 z-50 lg:hidden"><div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="hidden lg:flex lg:flex-col lg:w-72 lg:shrink-0"><div class="flex items-center justify-between px-6 py-4 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">`);
  _push(ssrRenderComponent(_component_Link, {
    href: "/",
    class: "group"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_logo, { class: "help-desk-logo group-hover:scale-105 transition-transform duration-300" }, null, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_logo, { class: "help-desk-logo group-hover:scale-105 transition-transform duration-300" })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<button class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">`);
  _push(ssrRenderComponent(_component_X, { class: "w-5 h-5 text-slate-600 dark:text-slate-400" }, null, _parent));
  _push(`</button></div><div class="flex-1 overflow-y-auto bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700">`);
  _push(ssrRenderComponent(_component_main_menu, null, null, _parent));
  _push(`</div></div>`);
  if ($data.mobileMenuOpen) {
    _push(`<div class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 lg:hidden"><div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">`);
    _push(ssrRenderComponent(_component_Link, {
      href: "/",
      class: "group"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_logo, { class: "help-desk-logo group-hover:scale-105 transition-transform duration-300" }, null, _parent2, _scopeId));
        } else {
          return [
            createVNode(_component_logo, { class: "help-desk-logo group-hover:scale-105 transition-transform duration-300" })
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`<button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">`);
    _push(ssrRenderComponent(_component_X, { class: "w-5 h-5 text-slate-600 dark:text-slate-400" }, null, _parent));
    _push(`</button></div><div class="overflow-y-auto h-full">`);
    _push(ssrRenderComponent(_component_main_menu, null, null, _parent));
    _push(`</div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="flex-1 flex flex-col overflow-hidden"><header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm"><div class="flex items-center justify-between px-4 py-3 lg:px-6"><div class="flex items-center gap-4"><button class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200">`);
  _push(ssrRenderComponent(_component_Menu, { class: "w-5 h-5 text-slate-600 dark:text-slate-400" }, null, _parent));
  _push(`</button><div class="hidden sm:block"><h1 class="text-lg font-semibold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t($options.generateGreetings()))} <span class="text-primary-600 dark:text-primary-400">${ssrInterpolate(_ctx.$page.props.auth.user.first_name)}!</span></h1><p class="text-sm text-slate-500 dark:text-slate-400">${ssrInterpolate($data.time)}</p></div></div><div class="flex items-center gap-3">`);
  _push(ssrRenderComponent(_component_NotificationBell, null, null, _parent));
  _push(ssrRenderComponent(_component_dropdown, {
    class: "language_menu_wrapper",
    placement: "bottom-end"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_icon, {
          class: "w-5 h-5",
          name: $options.selected_language.code
        }, null, _parent2, _scopeId));
        _push2(`<span class="hidden sm:block text-sm font-medium text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate($options.selected_language.name)}</span>`);
        _push2(ssrRenderComponent(_component_ChevronDown, { class: "w-4 h-4 text-slate-500" }, null, _parent2, _scopeId));
        _push2(`</button>`);
      } else {
        return [
          createVNode("button", { class: "flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200" }, [
            createVNode(_component_icon, {
              class: "w-5 h-5",
              name: $options.selected_language.code
            }, null, 8, ["name"]),
            createVNode("span", { class: "hidden sm:block text-sm font-medium text-slate-700 dark:text-slate-300" }, toDisplayString($options.selected_language.name), 1),
            createVNode(_component_ChevronDown, { class: "w-4 h-4 text-slate-500" })
          ])
        ];
      }
    }),
    dropdown: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="py-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 min-w-[160px]"${_scopeId}><!--[-->`);
        ssrRenderList($options.languages_except_selected, (language) => {
          _push2(`<div class="flex items-center gap-3 px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-colors duration-200"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_icon, {
            class: "w-5 h-5",
            name: language.code
          }, null, _parent2, _scopeId));
          _push2(`<span class="text-sm font-medium text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(language.name)}</span></div>`);
        });
        _push2(`<!--]--></div>`);
      } else {
        return [
          createVNode("div", { class: "py-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 min-w-[160px]" }, [
            (openBlock(true), createBlock(Fragment, null, renderList($options.languages_except_selected, (language) => {
              return openBlock(), createBlock("div", {
                key: language.code,
                class: "flex items-center gap-3 px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer transition-colors duration-200",
                onClick: ($event) => $options.updateLanguage(language.code)
              }, [
                createVNode(_component_icon, {
                  class: "w-5 h-5",
                  name: language.code
                }, null, 8, ["name"]),
                createVNode("span", { class: "text-sm font-medium text-slate-700 dark:text-slate-300" }, toDisplayString(language.name), 1)
              ], 8, ["onClick"]);
            }), 128))
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"${ssrRenderAttr("title", `Switch to ${$data.current_mode === "light" ? "dark" : "light"} mode`)}${ssrRenderAttr("aria-label", `Switch to ${$data.current_mode === "light" ? "dark" : "light"} mode`)}>`);
  if ($data.current_mode === "light") {
    _push(ssrRenderComponent(_component_Sun, { class: "w-5 h-5 text-slate-600 dark:text-slate-400" }, null, _parent));
  } else {
    _push(ssrRenderComponent(_component_Moon, { class: "w-5 h-5 text-slate-600 dark:text-slate-400" }, null, _parent));
  }
  _push(`</button>`);
  _push(ssrRenderComponent(_component_dropdown, {
    class: "select_user",
    placement: "bottom-end"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<button class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"${_scopeId}><div class="relative"${_scopeId}>`);
        if (_ctx.$page.props.auth.user.photo) {
          _push2(`<img class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700"${ssrRenderAttr("alt", _ctx.$page.props.auth.user.first_name)}${ssrRenderAttr("src", _ctx.$page.props.auth.user.photo)}${_scopeId}>`);
        } else {
          _push2(`<div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_User, { class: "w-4 h-4 text-white" }, null, _parent2, _scopeId));
          _push2(`</div>`);
        }
        _push2(`<div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-slate-800"${_scopeId}></div></div><div class="hidden sm:block text-left"${_scopeId}><p class="text-sm font-medium text-slate-900 dark:text-white"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.first_name)} ${ssrInterpolate(_ctx.$page.props.auth.user.last_name)}</p><p class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.email)}</p></div>`);
        _push2(ssrRenderComponent(_component_ChevronDown, { class: "w-4 h-4 text-slate-500" }, null, _parent2, _scopeId));
        _push2(`</button>`);
      } else {
        return [
          createVNode("button", { class: "flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200" }, [
            createVNode("div", { class: "relative" }, [
              _ctx.$page.props.auth.user.photo ? (openBlock(), createBlock("img", {
                key: 0,
                class: "w-8 h-8 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700",
                alt: _ctx.$page.props.auth.user.first_name,
                src: _ctx.$page.props.auth.user.photo
              }, null, 8, ["alt", "src"])) : (openBlock(), createBlock("div", {
                key: 1,
                class: "w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center"
              }, [
                createVNode(_component_User, { class: "w-4 h-4 text-white" })
              ])),
              createVNode("div", { class: "absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-slate-800" })
            ]),
            createVNode("div", { class: "hidden sm:block text-left" }, [
              createVNode("p", { class: "text-sm font-medium text-slate-900 dark:text-white" }, toDisplayString(_ctx.$page.props.auth.user.first_name) + " " + toDisplayString(_ctx.$page.props.auth.user.last_name), 1),
              createVNode("p", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(_ctx.$page.props.auth.user.email), 1)
            ]),
            createVNode(_component_ChevronDown, { class: "w-4 h-4 text-slate-500" })
          ])
        ];
      }
    }),
    dropdown: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="py-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 min-w-[240px]"${_scopeId}><div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700"${_scopeId}><div class="flex items-center gap-3"${_scopeId}>`);
        if (_ctx.$page.props.auth.user.photo) {
          _push2(`<img class="w-12 h-12 rounded-full object-cover"${ssrRenderAttr("alt", _ctx.$page.props.auth.user.first_name)}${ssrRenderAttr("src", _ctx.$page.props.auth.user.photo)}${_scopeId}>`);
        } else {
          _push2(`<div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_User, { class: "w-6 h-6 text-white" }, null, _parent2, _scopeId));
          _push2(`</div>`);
        }
        _push2(`<div${_scopeId}><p class="font-semibold text-slate-900 dark:text-white"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.first_name)} ${ssrInterpolate(_ctx.$page.props.auth.user.last_name)}</p><p class="text-sm text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.email)}</p></div></div></div><div class="py-1"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("users.edit.profile"),
          class: "flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_User, { class: "w-4 h-4" }, null, _parent3, _scopeId2));
              _push3(` ${ssrInterpolate(_ctx.$t("Edit Profile"))}`);
            } else {
              return [
                createVNode(_component_User, { class: "w-4 h-4" }),
                createTextVNode(" " + toDisplayString(_ctx.$t("Edit Profile")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("logout"),
          method: "delete",
          as: "button",
          class: "flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200 w-full text-left"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(ssrRenderComponent(_component_LogOut, { class: "w-4 h-4" }, null, _parent3, _scopeId2));
              _push3(` ${ssrInterpolate(_ctx.$t("Logout"))}`);
            } else {
              return [
                createVNode(_component_LogOut, { class: "w-4 h-4" }),
                createTextVNode(" " + toDisplayString(_ctx.$t("Logout")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div>`);
      } else {
        return [
          createVNode("div", { class: "py-2 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 min-w-[240px]" }, [
            createVNode("div", { class: "px-4 py-3 border-b border-slate-200 dark:border-slate-700" }, [
              createVNode("div", { class: "flex items-center gap-3" }, [
                _ctx.$page.props.auth.user.photo ? (openBlock(), createBlock("img", {
                  key: 0,
                  class: "w-12 h-12 rounded-full object-cover",
                  alt: _ctx.$page.props.auth.user.first_name,
                  src: _ctx.$page.props.auth.user.photo
                }, null, 8, ["alt", "src"])) : (openBlock(), createBlock("div", {
                  key: 1,
                  class: "w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center"
                }, [
                  createVNode(_component_User, { class: "w-6 h-6 text-white" })
                ])),
                createVNode("div", null, [
                  createVNode("p", { class: "font-semibold text-slate-900 dark:text-white" }, toDisplayString(_ctx.$page.props.auth.user.first_name) + " " + toDisplayString(_ctx.$page.props.auth.user.last_name), 1),
                  createVNode("p", { class: "text-sm text-slate-500 dark:text-slate-400" }, toDisplayString(_ctx.$page.props.auth.user.email), 1)
                ])
              ])
            ]),
            createVNode("div", { class: "py-1" }, [
              createVNode(_component_Link, {
                href: _ctx.route("users.edit.profile"),
                class: "flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200"
              }, {
                default: withCtx(() => [
                  createVNode(_component_User, { class: "w-4 h-4" }),
                  createTextVNode(" " + toDisplayString(_ctx.$t("Edit Profile")), 1)
                ]),
                _: 1
              }, 8, ["href"]),
              createVNode(_component_Link, {
                href: _ctx.route("logout"),
                method: "delete",
                as: "button",
                class: "flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200 w-full text-left"
              }, {
                default: withCtx(() => [
                  createVNode(_component_LogOut, { class: "w-4 h-4" }),
                  createTextVNode(" " + toDisplayString(_ctx.$t("Logout")), 1)
                ]),
                _: 1
              }, 8, ["href"])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></header><div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700"><div class="px-4 py-4 lg:px-6"><div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold text-slate-900 dark:text-white">${ssrInterpolate(_ctx.$t($props.title || ""))}</h1><nav class="flex items-center space-x-2 text-sm mt-1">`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("dashboard"),
    class: "text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_Home, { class: "w-4 h-4" }, null, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_Home, { class: "w-4 h-4" })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_ChevronRight, { class: "w-4 h-4 text-slate-400" }, null, _parent));
  if ($data.edit_route) {
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route($data.edit_route),
      class: "text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 capitalize"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate($data.edit_route)}`);
        } else {
          return [
            createTextVNode(toDisplayString($data.edit_route), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
  } else {
    _push(`<!---->`);
  }
  if ($data.edit_route) {
    _push(ssrRenderComponent(_component_ChevronRight, { class: "w-4 h-4 text-slate-400" }, null, _parent));
  } else {
    _push(`<!---->`);
  }
  _push(`<span class="text-slate-900 dark:text-white font-medium">${ssrInterpolate(_ctx.$t($props.title || ""))}</span></nav></div></div></div></div><main class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900">`);
  _push(ssrRenderComponent(_component_flash_messages, null, null, _parent));
  _push(`<div class="p-4 lg:p-6">`);
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  _push(`</div></main></div></div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Layout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Layout = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Layout as L
};
