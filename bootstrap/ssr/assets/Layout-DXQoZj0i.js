import { D as Dropdown, I as Icon } from "./Dropdown-DNX6MmV_.js";
import { _ as _export_sfc, L as Logo, F as FlashMessages } from "./FlashMessages-DUb4hfI8.js";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { getActiveLanguage, loadLanguageAsync } from "laravel-vue-i18n";
import { resolveComponent, mergeProps, withCtx, createVNode, createTextVNode, toDisplayString, createBlock, openBlock, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderClass, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrRenderStyle, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderSlot } from "vue/server-renderer";
import { L as LoadingButton } from "./LoadingButton-Dl5SG5JI.js";
import { BarChart3, FileText, Headphones, ExternalLink, Instagram, Linkedin, Twitter, Facebook, Send, Mail, ArrowRight, Star } from "lucide-vue-next";
import moment from "moment";
const _sfc_main$5 = {
  components: {
    Logo,
    Icon,
    Dropdown,
    Link
  },
  data() {
    var _a, _b;
    return {
      active_menu: "home",
      enable_option: {},
      locale: ((_b = (_a = this.$page.props.auth) == null ? void 0 : _a.user) == null ? void 0 : _b.locale) || this.$page.props.locale
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
  methods: {
    updateLanguage(code) {
      axios.post(this.route("language", code), {}).then((response) => {
        if (response.data) {
          window.location.reload();
        }
      });
    },
    toggleMenu() {
      document.getElementById("isToggle").classList.toggle("open");
      var isOpen = document.getElementById("navigation");
      if (isOpen.style.display === "block") {
        isOpen.style.display = "none";
      } else {
        isOpen.style.display = "block";
      }
    },
    windowScroll() {
      const navbar = document.getElementById("topnav");
      if (navbar != null) {
        if (document.body.scrollTop >= 50 || document.documentElement.scrollTop >= 50) {
          navbar.classList.add("sticky");
        } else {
          navbar.classList.remove("sticky");
        }
      }
    }
  },
  mounted() {
    this.active_menu = this.$page.url.substr(1) || "home";
    this.$nextTick(() => {
      const navbarToggler = document.getElementById("navbarToggler");
      const navbarCollapse = document.getElementById("navbarCollapse");
      if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener("click", () => {
          navbarToggler.classList.toggle("navbarTogglerActive");
          navbarCollapse.classList.toggle("hidden");
        });
      }
    });
  },
  created() {
    if (this.$page.props.enable_options) {
      let options = JSON.parse(this.$page.props.enable_options.value);
      options.forEach((option) => {
        this.enable_option[option.slug] = !!option.value;
      });
    }
    window.addEventListener("scroll", (ev) => {
      ev.preventDefault();
      this.windowScroll();
    });
    if (getActiveLanguage() !== this.$page.props.locale) {
      loadLanguageAsync(this.$page.props.locale);
    }
  }
};
function _sfc_ssrRender$5(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_logo = resolveComponent("logo");
  const _component_dropdown = resolveComponent("dropdown");
  const _component_icon = resolveComponent("icon");
  _push(`<nav${ssrRenderAttrs(mergeProps({
    id: "topnav",
    class: "ud-header absolute top-0 left-0 z-40 flex w-full items-center bg-transparent"
  }, _attrs))}><div id="dropdown"></div><div class="container"><div class="relative -mx-4 flex items-center justify-between"><div class="w-60 max-w-full px-4">`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("home"),
    class: "logo pl-0 mt-2 mb-2"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(ssrRenderComponent(_component_logo, { class: "help-desk-logo" }, null, _parent2, _scopeId));
        _push2(ssrRenderComponent(_component_logo, {
          name: "white",
          class: "help-desk-logo white"
        }, null, _parent2, _scopeId));
      } else {
        return [
          createVNode(_component_logo, { class: "help-desk-logo" }),
          createVNode(_component_logo, {
            name: "white",
            class: "help-desk-logo white"
          })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div><div class="flex w-full items-center justify-between px-4"><div><button id="navbarToggler" class="absolute right-4 top-1/2 block -translate-y-1/2 rounded-lg px-3 py-[6px] ring-primary lg:hidden"><span class="relative my-[6px] block h-[2px] w-[30px] bg-white"></span><span class="relative my-[6px] block h-[2px] w-[30px] bg-white"></span><span class="relative my-[6px] block h-[2px] w-[30px] bg-white"></span></button><nav id="navbarCollapse" class="absolute right-4 top-full hidden w-full max-w-[250px] rounded-lg bg-white py-5 shadow-lg lg:static lg:block lg:w-full lg:max-w-full lg:bg-transparent lg:py-0 lg:px-4 lg:shadow-none xl:px-6"><ul class="blcok lg:flex"><li class="${ssrRenderClass([{ "active": $data.active_menu === "home" }, "group relative"])}">`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("home"),
    class: "ud-menu-scroll mx-8 flex py-2 text-base text-dark group-hover:text-primary lg:mr-0 lg:inline-flex lg:py-6 lg:px-0 lg:text-white lg:group-hover:text-white lg:group-hover:opacity-70"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`${ssrInterpolate(_ctx.$t("Home"))}`);
      } else {
        return [
          createTextVNode(toDisplayString(_ctx.$t("Home")), 1)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</li>`);
  if (!!this.enable_option && this.enable_option.service) {
    _push(`<li class="${ssrRenderClass([{ "active": $data.active_menu === "services" }, "group relative"])}">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("services"),
      class: "ud-menu-scroll mx-8 flex py-2 text-base text-dark group-hover:text-primary lg:mr-0 lg:inline-flex lg:py-6 lg:px-0 lg:text-white lg:group-hover:text-white lg:group-hover:opacity-70"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(_ctx.$t("Services"))}`);
        } else {
          return [
            createTextVNode(toDisplayString(_ctx.$t("Services")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.kb) {
    _push(`<li class="${ssrRenderClass([{ "active": $data.active_menu === "kb" }, "group relative"])}">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("kb"),
      class: "ud-menu-scroll mx-8 flex py-2 text-base text-dark group-hover:text-primary lg:mr-0 lg:inline-flex lg:py-6 lg:px-0 lg:text-white lg:group-hover:text-white lg:group-hover:opacity-70"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(_ctx.$t("Knowledge"))}`);
        } else {
          return [
            createTextVNode(toDisplayString(_ctx.$t("Knowledge")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.faq) {
    _push(`<li class="${ssrRenderClass([{ "active": $data.active_menu === "faq" }, "group relative"])}">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("faq"),
      class: "ud-menu-scroll mx-8 flex py-2 text-base text-dark group-hover:text-primary lg:mr-0 lg:inline-flex lg:py-6 lg:px-0 lg:text-white lg:group-hover:text-white lg:group-hover:opacity-70"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(_ctx.$t("FAQs"))}`);
        } else {
          return [
            createTextVNode(toDisplayString(_ctx.$t("FAQs")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.blog) {
    _push(`<li class="${ssrRenderClass([{ "active": $data.active_menu === "blog" }, "group relative"])}">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("blog"),
      class: "ud-menu-scroll mx-8 flex py-2 text-base text-dark group-hover:text-primary lg:mr-0 lg:inline-flex lg:py-6 lg:px-0 lg:text-white lg:group-hover:text-white lg:group-hover:opacity-70"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(_ctx.$t("Blog"))}`);
        } else {
          return [
            createTextVNode(toDisplayString(_ctx.$t("Blog")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.contact_page) {
    _push(`<li class="${ssrRenderClass([{ "active": $data.active_menu === "contact" }, "group relative"])}">`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("contact"),
      class: "ud-menu-scroll mx-8 flex py-2 text-base text-dark group-hover:text-primary lg:mr-0 lg:inline-flex lg:py-6 lg:px-0 lg:text-white lg:group-hover:text-white lg:group-hover:opacity-70"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`${ssrInterpolate(_ctx.$t("Contact"))}`);
        } else {
          return [
            createTextVNode(toDisplayString(_ctx.$t("Contact")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</li>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</ul></nav></div><div class="flex items-center gap-4"><div class="placement-top-right">`);
  _push(ssrRenderComponent(_component_dropdown, {
    class: "mt-1 rtl:ml-2 language_menu_wrapper",
    placement: "bottom-end"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="flex items-center cursor-pointer group language_menu"${_scopeId}><div class="font-bold mr-1 rtl:mr-0 whitespace-nowrap w-[7rem]"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_icon, {
          class: "w-5 h-5",
          name: $options.selected_language.code
        }, null, _parent2, _scopeId));
        _push2(` <span class="rtl:mr-[6px] language_text"${_scopeId}>${ssrInterpolate($options.selected_language.name)}</span></div>`);
        _push2(ssrRenderComponent(_component_icon, {
          class: "w-5 h-5 drop-down-caret-icon",
          name: "cheveron-down"
        }, null, _parent2, _scopeId));
        _push2(`</div>`);
      } else {
        return [
          createVNode("div", { class: "flex items-center cursor-pointer group language_menu" }, [
            createVNode("div", { class: "font-bold mr-1 rtl:mr-0 whitespace-nowrap w-[7rem]" }, [
              createVNode(_component_icon, {
                class: "w-5 h-5",
                name: $options.selected_language.code
              }, null, 8, ["name"]),
              createTextVNode(),
              createVNode("span", { class: "rtl:mr-[6px] language_text" }, toDisplayString($options.selected_language.name), 1)
            ]),
            createVNode(_component_icon, {
              class: "w-5 h-5 drop-down-caret-icon",
              name: "cheveron-down"
            })
          ])
        ];
      }
    }),
    dropdown: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="py-0 shadow-xl rounded text-sm language_menu_list"${_scopeId}><!--[-->`);
        ssrRenderList($options.languages_except_selected, (language) => {
          _push2(`<div class="flex gap-2 cursor-pointer px-3 py-2 hover:bg-indigo-500 hover:text-white"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_icon, {
            class: "w-5 h-5",
            name: language.code
          }, null, _parent2, _scopeId));
          _push2(` <span class="lang_name rtl:mr-[6px]"${_scopeId}>${ssrInterpolate(language.name)}</span></div>`);
        });
        _push2(`<!--]--></div>`);
      } else {
        return [
          createVNode("div", { class: "py-0 shadow-xl rounded text-sm language_menu_list" }, [
            (openBlock(true), createBlock(Fragment, null, renderList($options.languages_except_selected, (language) => {
              return openBlock(), createBlock("div", {
                key: language.code,
                class: "flex gap-2 cursor-pointer px-3 py-2 hover:bg-indigo-500 hover:text-white",
                onClick: ($event) => $options.updateLanguage(language.code)
              }, [
                createVNode(_component_icon, {
                  class: "w-5 h-5",
                  name: language.code
                }, null, 8, ["name"]),
                createTextVNode(),
                createVNode("span", { class: "lang_name rtl:mr-[6px]" }, toDisplayString(language.name), 1)
              ], 8, ["onClick"]);
            }), 128))
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div>`);
  if (_ctx.$page.props.auth && _ctx.$page.props.auth.user) {
    _push(`<div class="justify-end pr-16 flex lg:pr-0"><div class="dd__wrapper">`);
    _push(ssrRenderComponent(_component_dropdown, {
      class: "mt-1 select_user",
      placement: "bottom-end"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="flex items-center cursor-pointer group"${_scopeId}><div class="mr-1 whitespace-nowrap text-white"${_scopeId}>`);
          if (_ctx.$page.props.auth.user.photo) {
            _push2(`<img class="user_photo w-8 h-8"${ssrRenderAttr("alt", _ctx.$page.props.auth.user.first_name)}${ssrRenderAttr("src", _ctx.$page.props.auth.user.photo)}${_scopeId}>`);
          } else {
            _push2(`<img src="/images/svg/profile.svg" class="w-8 h-8" alt="user profile"${_scopeId}>`);
          }
          _push2(`<span class="hidden"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.first_name)}</span><span class="hidden"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.last_name)}</span></div>`);
          _push2(ssrRenderComponent(_component_icon, {
            class: "w-5 h-5 drop-down-caret-icon",
            name: "cheveron-down"
          }, null, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          return [
            createVNode("div", { class: "flex items-center cursor-pointer group" }, [
              createVNode("div", { class: "mr-1 whitespace-nowrap text-white" }, [
                _ctx.$page.props.auth.user.photo ? (openBlock(), createBlock("img", {
                  key: 0,
                  class: "user_photo w-8 h-8",
                  alt: _ctx.$page.props.auth.user.first_name,
                  src: _ctx.$page.props.auth.user.photo
                }, null, 8, ["alt", "src"])) : (openBlock(), createBlock("img", {
                  key: 1,
                  src: "/images/svg/profile.svg",
                  class: "w-8 h-8",
                  alt: "user profile"
                })),
                createVNode("span", { class: "hidden" }, toDisplayString(_ctx.$page.props.auth.user.first_name), 1),
                createVNode("span", { class: "hidden" }, toDisplayString(_ctx.$page.props.auth.user.last_name), 1)
              ]),
              createVNode(_component_icon, {
                class: "w-5 h-5 drop-down-caret-icon",
                name: "cheveron-down"
              })
            ])
          ];
        }
      }),
      dropdown: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(`<div class="shadow-xl bg-white rounded text-sm"${_scopeId}>`);
          _push2(ssrRenderComponent(_component_Link, {
            class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white",
            href: _ctx.route("dashboard")
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`${ssrInterpolate(_ctx.$t("Dashboard"))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(_ctx.$t("Dashboard")), 1)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_Link, {
            class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white",
            href: _ctx.route("tickets")
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`${ssrInterpolate(_ctx.$t("Tickets"))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(_ctx.$t("Tickets")), 1)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_Link, {
            class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white",
            href: _ctx.route("users.edit.profile")
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`${ssrInterpolate(_ctx.$t("Edit Profile"))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(_ctx.$t("Edit Profile")), 1)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(ssrRenderComponent(_component_Link, {
            class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white w-full text-left",
            href: _ctx.route("logout"),
            method: "delete",
            as: "button"
          }, {
            default: withCtx((_2, _push3, _parent3, _scopeId2) => {
              if (_push3) {
                _push3(`${ssrInterpolate(_ctx.$t("Logout"))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(_ctx.$t("Logout")), 1)
                ];
              }
            }),
            _: 1
          }, _parent2, _scopeId));
          _push2(`</div>`);
        } else {
          return [
            createVNode("div", { class: "shadow-xl bg-white rounded text-sm" }, [
              createVNode(_component_Link, {
                class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white",
                href: _ctx.route("dashboard")
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(_ctx.$t("Dashboard")), 1)
                ]),
                _: 1
              }, 8, ["href"]),
              createVNode(_component_Link, {
                class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white",
                href: _ctx.route("tickets")
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(_ctx.$t("Tickets")), 1)
                ]),
                _: 1
              }, 8, ["href"]),
              createVNode(_component_Link, {
                class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white",
                href: _ctx.route("users.edit.profile")
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(_ctx.$t("Edit Profile")), 1)
                ]),
                _: 1
              }, 8, ["href"]),
              createVNode(_component_Link, {
                class: "block px-6 py-2 hover:bg-indigo-500 hover:text-white w-full text-left",
                href: _ctx.route("logout"),
                method: "delete",
                as: "button"
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(_ctx.$t("Logout")), 1)
                ]),
                _: 1
              }, 8, ["href"])
            ])
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div></div>`);
  } else {
    _push(`<div class="hidden justify-end pr-16 sm:flex lg:pr-0"><a${ssrRenderAttr("href", _ctx.route("login"))} class="signUpBtn rounded-lg bg-white bg-opacity-20 py-3 px-6 text-base font-medium text-white duration-300 ease-in-out hover:bg-opacity-100 hover:text-dark">${ssrInterpolate(_ctx.$t("Login"))}</a></div>`);
  }
  _push(`</div></div></div></div></nav>`);
}
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Landing/TopNav.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const TopNav = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["ssrRender", _sfc_ssrRender$5]]);
const _sfc_main$4 = {
  components: {
    Logo,
    LoadingButton,
    Link,
    Star,
    ArrowRight,
    Mail,
    Send,
    Facebook,
    Twitter,
    Linkedin,
    Instagram,
    ExternalLink,
    Headphones,
    FileText,
    BarChart3
  },
  props: {
    footer: Object
  },
  data() {
    return {
      footer_content: this.footer ? JSON.parse(this.footer.html) : [],
      footer_text: this.footer ? JSON.parse(this.footer.html).text : "Start working with HelpDesk that can provide everything you need to generate awareness, drive traffic, connect.",
      form: this.$inertia.form({
        email: ""
      }),
      enable_option: {}
    };
  },
  methods: {
    subscribe() {
      this.form.post(this.route("subscribe.news"));
      this.form.email = "";
    }
  },
  created() {
    if (this.$page.props.enable_options) {
      let options = JSON.parse(this.$page.props.enable_options.value);
      options.forEach((option) => {
        this.enable_option[option.slug] = !!option.value;
      });
    }
  }
};
function _sfc_ssrRender$4(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Link = resolveComponent("Link");
  const _component_logo = resolveComponent("logo");
  const _component_Facebook = resolveComponent("Facebook");
  const _component_Twitter = resolveComponent("Twitter");
  const _component_Linkedin = resolveComponent("Linkedin");
  const _component_Instagram = resolveComponent("Instagram");
  const _component_ArrowRight = resolveComponent("ArrowRight");
  const _component_Mail = resolveComponent("Mail");
  const _component_loading_button = resolveComponent("loading-button");
  const _component_Send = resolveComponent("Send");
  const _component_Headphones = resolveComponent("Headphones");
  const _component_FileText = resolveComponent("FileText");
  const _component_BarChart3 = resolveComponent("BarChart3");
  _push(`<footer${ssrRenderAttrs(mergeProps({ class: "relative overflow-hidden" }, _attrs))}><div class="absolute inset-0"><div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div><div class="absolute inset-0 bg-gradient-to-br from-primary-600/15 via-transparent to-blue-600/15 animate-pulse"></div><div class="absolute inset-0 opacity-30"><div class="absolute inset-0 bg-grid-pattern bg-center bg-repeat animate-pulse" style="${ssrRenderStyle({ "background-image": "url('/images/patterns/grid.svg')", "mask-image": "radial-gradient(ellipse_at_center,white,transparent_70%)", "animation-duration": "4s" })}"></div></div><div class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-br from-primary-500/20 to-blue-500/10 rounded-full blur-3xl animate-pulse"></div><div class="absolute top-1/3 right-1/4 w-80 h-80 bg-gradient-to-br from-purple-500/15 to-pink-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div><div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-gradient-to-br from-cyan-500/20 to-blue-500/15 rounded-full blur-3xl animate-pulse delay-500"></div><div class="absolute top-20 left-20 w-2 h-2 bg-white/40 rounded-full animate-ping"></div><div class="absolute top-40 right-32 w-3 h-3 bg-primary-300/60 rounded-full animate-ping delay-1000"></div><div class="absolute bottom-32 left-16 w-1 h-1 bg-blue-300/80 rounded-full animate-ping delay-500"></div><div class="absolute top-60 right-1/4 w-2 h-2 bg-purple-300/50 rounded-full animate-ping delay-700"></div></div><div class="relative z-10"><div class="py-16 lg:py-20"><div class="container"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12"><div class="lg:col-span-1"><div class="mb-8">`);
  _push(ssrRenderComponent(_component_Link, {
    href: _ctx.route("home"),
    class: "inline-block mb-8 group"
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
  _push(`<p class="text-slate-300 leading-relaxed mb-8 text-lg">${_ctx.$t($data.footer_text) ?? ""}</p><div class="space-y-4"><h5 class="text-white font-semibold mb-4">Follow Us</h5><div class="flex items-center gap-3"><a href="#" class="group w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl flex items-center justify-center hover:from-blue-700 hover:to-blue-800 transition-all duration-300 hover:scale-110 hover:shadow-xl">`);
  _push(ssrRenderComponent(_component_Facebook, { class: "w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`</a><a href="#" class="group w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl flex items-center justify-center hover:from-sky-600 hover:to-sky-700 transition-all duration-300 hover:scale-110 hover:shadow-xl">`);
  _push(ssrRenderComponent(_component_Twitter, { class: "w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`</a><a href="#" class="group w-12 h-12 bg-gradient-to-br from-blue-700 to-blue-800 rounded-2xl flex items-center justify-center hover:from-blue-800 hover:to-blue-900 transition-all duration-300 hover:scale-110 hover:shadow-xl">`);
  _push(ssrRenderComponent(_component_Linkedin, { class: "w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`</a><a href="#" class="group w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center hover:from-pink-600 hover:to-pink-700 transition-all duration-300 hover:scale-110 hover:shadow-xl">`);
  _push(ssrRenderComponent(_component_Instagram, { class: "w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`</a></div></div></div></div><div><h4 class="text-2xl font-bold text-white mb-8 flex items-center gap-3"><div class="w-2 h-8 bg-gradient-to-b from-primary-500 to-primary-600 rounded-full"></div> ${ssrInterpolate(_ctx.$t("Company"))}</h4><ul class="space-y-4">`);
  if (!!this.enable_option && this.enable_option.show_login) {
    _push(`<li><a${ssrRenderAttr("href", _ctx.route("login"))} class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2"><div class="w-1 h-1 bg-primary-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div><span class="text-lg">${ssrInterpolate(_ctx.$t("Login"))}</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" }, null, _parent));
    _push(`</a></li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.show_login) {
    _push(`<li><a${ssrRenderAttr("href", _ctx.route("register"))} class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2"><div class="w-1 h-1 bg-primary-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div><span class="text-lg">${ssrInterpolate(_ctx.$t("Register"))}</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" }, null, _parent));
    _push(`</a></li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.blog) {
    _push(`<li><a${ssrRenderAttr("href", _ctx.route("blog"))} class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2"><div class="w-1 h-1 bg-primary-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div><span class="text-lg">${ssrInterpolate(_ctx.$t("Blog"))}</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" }, null, _parent));
    _push(`</a></li>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</ul></div><div><h4 class="text-2xl font-bold text-white mb-8 flex items-center gap-3"><div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div> ${ssrInterpolate(_ctx.$t("Useful Links"))}</h4><ul class="space-y-4">`);
  if (!!this.enable_option && this.enable_option.terms_of_services) {
    _push(`<li><a${ssrRenderAttr("href", _ctx.route("terms_service"))} class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2"><div class="w-1 h-1 bg-blue-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div><span class="text-lg">${ssrInterpolate(_ctx.$t("Terms of Services"))}</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" }, null, _parent));
    _push(`</a></li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.privacy_policy) {
    _push(`<li><a${ssrRenderAttr("href", _ctx.route("privacy"))} class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2"><div class="w-1 h-1 bg-blue-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div><span class="text-lg">${ssrInterpolate(_ctx.$t("Privacy Policy"))}</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" }, null, _parent));
    _push(`</a></li>`);
  } else {
    _push(`<!---->`);
  }
  if (!!this.enable_option && this.enable_option.kb) {
    _push(`<li><a${ssrRenderAttr("href", _ctx.route("kb"))} class="group text-slate-300 hover:text-white transition-all duration-300 flex items-center gap-3 py-2"><div class="w-1 h-1 bg-blue-400 rounded-full group-hover:scale-150 transition-transform duration-300"></div><span class="text-lg">${ssrInterpolate(_ctx.$t("Knowledge Base"))}</span>`);
    _push(ssrRenderComponent(_component_ArrowRight, { class: "w-4 h-4 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1" }, null, _parent));
    _push(`</a></li>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</ul></div>`);
  if (!!this.enable_option && this.enable_option.newsletter) {
    _push(`<div><h4 class="text-2xl font-bold text-white mb-8 flex items-center gap-3"><div class="w-2 h-8 bg-gradient-to-b from-green-500 to-green-600 rounded-full"></div> ${ssrInterpolate(_ctx.$t("Newsletter"))}</h4><p class="text-slate-300 mb-8 text-lg">${ssrInterpolate(_ctx.$t("Join our newsletter service."))}</p><form class="space-y-6"><div class="relative group"><div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-blue-500/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div><div class="relative">`);
    _push(ssrRenderComponent(_component_Mail, { class: "w-6 h-6 absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 group-hover:text-primary-400 transition-colors duration-300" }, null, _parent));
    _push(`<input type="email"${ssrRenderAttr("value", $data.form.email)} class="w-full pl-14 pr-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 text-lg" placeholder="Enter your email address" required style="${ssrRenderStyle({ "backdrop-filter": "blur(20px)" })}"></div></div>`);
    _push(ssrRenderComponent(_component_loading_button, {
      loading: $data.form.processing,
      class: "w-full group relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold rounded-2xl hover:from-green-700 hover:to-green-800 transition-all duration-300 hover:scale-105 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-green-300 text-lg",
      type: "submit"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_Send, { class: "w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" }, null, _parent2, _scopeId));
          _push2(`<span${_scopeId}>${ssrInterpolate(_ctx.$t("Subscribe Now"))}</span>`);
        } else {
          return [
            createVNode(_component_Send, { class: "w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" }),
            createVNode("span", null, toDisplayString(_ctx.$t("Subscribe Now")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`<p class="text-xs text-slate-400 text-center"> By subscribing, you agree to our privacy policy and terms of service. </p></form></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div></div><div class="border-t border-white/10 bg-gradient-to-r from-slate-900/50 to-slate-800/50" style="${ssrRenderStyle({ "backdrop-filter": "blur(20px)" })}"><div class="container py-8"><div class="flex flex-col lg:flex-row items-center justify-between gap-8"><div class="text-center lg:text-left"><p class="text-slate-400 text-lg">${_ctx.$t($data.footer_content.copyright) ?? ""}</p></div><div class="flex items-center gap-8"><a href="#" class="group text-slate-400 hover:text-white transition-all duration-300 flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_Headphones, { class: "w-4 h-4 group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`<span class="text-sm font-medium">Support</span></a><a href="#" class="group text-slate-400 hover:text-white transition-all duration-300 flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_FileText, { class: "w-4 h-4 group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`<span class="text-sm font-medium">Documentation</span></a><a href="#" class="group text-slate-400 hover:text-white transition-all duration-300 flex items-center gap-2">`);
  _push(ssrRenderComponent(_component_BarChart3, { class: "w-4 h-4 group-hover:scale-110 transition-transform duration-300" }, null, _parent));
  _push(`<span class="text-sm font-medium">Status</span></a></div></div></div></div></div></footer>`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Landing/FooterSection.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const FooterSection = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$4]]);
const _sfc_main$3 = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed top-1/4 -right-1 z-3" }, _attrs))}><span class="relative inline-block rotate-90"><input type="checkbox" class="checkbox opacity-0 absolute" id="chk"><label class="label bg-slate-900 dark:bg-white shadow dark:shadow-gray-800 cursor-pointer rounded-full flex justify-between items-center p-1 w-14 h-8" for="chk"><i class="uil uil-moon text-[20px] text-yellow-500"></i><i class="uil uil-sun text-[20px] text-yellow-500"></i><span class="ball bg-white dark:bg-slate-900 rounded-full absolute top-[2px] left-[2px] w-7 h-7"></span></label></span></div>`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Landing/Switcher.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const Switcher = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$2 = {
  components: { Logo },
  props: {
    messages: Array,
    isTyping: Boolean,
    typingUser: {
      type: Object,
      default: null
    },
    currentUserId: {
      type: [Number, String],
      default: null
    }
  },
  emits: ["send-message", "close", "typing"],
  data() {
    return {
      message: "",
      uploadedFiles: [],
      isMinimized: false
    };
  },
  methods: {
    getInitials(message) {
      if (!message) return "?";
      if (message.contact_id) {
        return "C";
      }
      return "A";
    },
    getTypingMessage() {
      if (!this.typingUser) return this.$t("Someone is typing...");
      if (this.typingUser.contact_id) {
        return this.$t("Customer is typing...");
      } else if (this.typingUser.user_id) {
        return this.$t("Agent is typing...");
      }
      return this.$t("Someone is typing...");
    },
    formatMessage(message) {
      if (!message) {
        return "";
      }
      if (typeof message === "object") {
        if (message.text) return message.text;
        if (message.content) return message.content;
        if (message.message) return message.message;
        return JSON.stringify(message);
      }
      const messageStr = String(message || "");
      return messageStr.replace(/\n/g, "<br>");
    },
    formatTime(timestamp) {
      if (!timestamp) return "";
      return moment(timestamp).format("h:mm A");
    },
    handleKeyDown(event) {
      if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        this.sendMessage();
      }
    },
    handleTyping() {
      this.$emit("typing", {
        isTyping: true,
        user: {
          id: this.currentUserId,
          contact_id: null,
          // This will be set by the parent component
          user_id: this.currentUserId
        }
      });
    },
    sendMessage() {
      if (this.message.trim() || this.uploadedFiles.length) {
        this.$emit("send-message", {
          message: this.message,
          files: this.uploadedFiles
        });
        this.message = "";
        this.uploadedFiles = [];
      }
    },
    triggerFileUpload() {
      this.$refs.fileInput.click();
    },
    handleFileUpload(event) {
      const files = Array.from(event.target.files);
      this.uploadedFiles.push(...files);
    },
    removeFile(index) {
      this.uploadedFiles.splice(index, 1);
    },
    toggleEmojiPicker() {
      console.log("Emoji picker not implemented yet");
    },
    toggleMinimize() {
      this.isMinimized = !this.isMinimized;
    },
    handleScroll() {
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    }
  },
  watch: {
    messages: {
      handler() {
        this.handleScroll();
      },
      deep: true
    }
  }
};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Logo = resolveComponent("Logo");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-80 h-96 bg-white rounded-lg shadow-2xl flex flex-col" }, _attrs))}><div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-lg px-4 py-3 flex items-center justify-between"><div class="flex items-center"><div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">`);
  _push(ssrRenderComponent(_component_Logo, { class: "w-5 h-5 fill-white" }, null, _parent));
  _push(`</div><div><h3 class="text-white font-semibold text-sm">${ssrInterpolate(_ctx.$t("Live Support"))}</h3><div class="flex items-center"><div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div><span class="text-blue-100 text-xs">${ssrInterpolate(_ctx.$t("Online now"))}</span></div></div></div><div class="flex items-center space-x-2"><button class="text-blue-100 hover:text-white transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button><button class="text-blue-100 hover:text-white transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div><div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">`);
  if (!$props.messages.length) {
    _push(`<div class="text-center py-8"><div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg></div><h4 class="text-gray-700 font-semibold mb-2">${ssrInterpolate(_ctx.$t("Welcome to our support!"))}</h4><p class="text-gray-500 text-sm">${ssrInterpolate(_ctx.$t("We're here to help. Send us a message to get started."))}</p></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<!--[-->`);
  ssrRenderList($props.messages, (message) => {
    _push(`<div class="${ssrRenderClass([message.contact_id ? "justify-start" : "justify-end", "flex"])}"><div class="${ssrRenderClass([message.contact_id ? "flex-row" : "flex-row-reverse", "flex max-w-xs lg:max-w-md"])}"><div class="flex-shrink-0"><div class="${ssrRenderClass([message.contact_id ? "bg-gray-300" : "bg-blue-600", "w-8 h-8 rounded-full flex items-center justify-center"])}"><span class="${ssrRenderClass([message.contact_id ? "text-gray-600" : "text-white", "text-xs font-semibold"])}">${ssrInterpolate($options.getInitials(message))}</span></div></div><div class="${ssrRenderClass([message.contact_id ? "ml-2" : "mr-2", "ml-2 mr-2"])}"><div class="${ssrRenderClass([message.contact_id ? "bg-white shadow-sm" : "bg-blue-600 text-white", "px-4 py-2 rounded-lg"])}"><div class="text-sm">${$options.formatMessage(message.message) ?? ""}</div><div class="${ssrRenderClass([message.contact_id ? "text-gray-500" : "text-blue-100", "flex items-center mt-1"])}"><span class="text-xs">${ssrInterpolate($options.formatTime(message.created_at))}</span>`);
    if (!message.contact_id) {
      _push(`<div class="ml-2">`);
      if (message.status === "sent") {
        _push(`<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>`);
      } else if (message.status === "delivered") {
        _push(`<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div></div></div></div></div>`);
  });
  _push(`<!--]-->`);
  if ($props.isTyping && $props.typingUser && $props.typingUser.id !== $props.currentUserId) {
    _push(`<div class="flex justify-start"><div class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm"><div class="flex space-x-1"><div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div><div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="${ssrRenderStyle({ "animation-delay": "0.1s" })}"></div><div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="${ssrRenderStyle({ "animation-delay": "0.2s" })}"></div></div><span class="text-xs text-gray-500">${ssrInterpolate($options.getTypingMessage())}</span></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="border-t bg-white rounded-b-lg">`);
  if ($data.uploadedFiles.length) {
    _push(`<div class="px-4 py-2 border-b bg-gray-50"><div class="flex items-center space-x-2"><span class="text-xs text-gray-600">${ssrInterpolate(_ctx.$t("Attachments:"))}</span><!--[-->`);
    ssrRenderList($data.uploadedFiles, (file, index) => {
      _push(`<div class="flex items-center space-x-1 bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs"><span>${ssrInterpolate(file.name)}</span><button class="text-blue-600 hover:text-blue-800"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>`);
    });
    _push(`<!--]--></div></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<div class="flex items-end p-4 space-x-2"><button class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg></button><button class="text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button><div class="flex-1 relative"><textarea${ssrRenderAttr("placeholder", _ctx.$t("Type your message..."))} class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:outline-none focus:border-blue-500 text-sm" rows="1">${ssrInterpolate($data.message)}</textarea></div><button${ssrIncludeBooleanAttr(!$data.message.trim() && !$data.uploadedFiles.length) ? " disabled" : ""} class="${ssrRenderClass([
    "p-2 rounded-lg transition-all duration-200",
    $data.message.trim() || $data.uploadedFiles.length ? "bg-blue-600 text-white hover:bg-blue-700" : "bg-gray-200 text-gray-400 cursor-not-allowed"
  ])}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg></button></div><input type="file" multiple accept="image/*,.pdf,.doc,.docx,.txt" class="hidden"></div></div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Chat/EnhancedChatBox.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const EnhancedChatBox = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$1 = {
  components: { Logo },
  props: {
    onSubmit: Function
  },
  data() {
    return {
      form: {
        firstName: "",
        lastName: "",
        email: "",
        subject: ""
      },
      errors: {},
      isSubmitting: false,
      submitError: null
    };
  },
  computed: {
    isFormValid() {
      return this.form.firstName.trim() && this.form.lastName.trim() && this.form.email.trim() && this.isValidEmail(this.form.email) && !this.isSubmitting;
    }
  },
  methods: {
    validateField(field) {
      this.errors[field] = "";
      switch (field) {
        case "firstName":
          if (!this.form.firstName.trim()) {
            this.errors.firstName = this.$t("First name is required");
          } else if (this.form.firstName.trim().length < 2) {
            this.errors.firstName = this.$t("First name must be at least 2 characters");
          }
          break;
        case "lastName":
          if (!this.form.lastName.trim()) {
            this.errors.lastName = this.$t("Last name is required");
          } else if (this.form.lastName.trim().length < 2) {
            this.errors.lastName = this.$t("Last name must be at least 2 characters");
          }
          break;
        case "email":
          if (!this.form.email.trim()) {
            this.errors.email = this.$t("Email is required");
          } else if (!this.isValidEmail(this.form.email)) {
            this.errors.email = this.$t("Please enter a valid email address");
          }
          break;
      }
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    async handleSubmit() {
      Object.keys(this.form).forEach((field) => {
        if (field !== "subject") {
          this.validateField(field);
        }
      });
      if (!this.isFormValid) return;
      this.isSubmitting = true;
      this.submitError = null;
      try {
        await this.onSubmit(this.form);
      } catch (error) {
        this.submitError = error.message || this.$t("Failed to start chat. Please try again.");
      } finally {
        this.isSubmitting = false;
      }
    }
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Logo = resolveComponent("Logo");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-80 h-auto bg-white rounded-lg shadow-2xl" }, _attrs))}><nav class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-lg flex flex-col items-center pt-4 pb-2">`);
  _push(ssrRenderComponent(_component_Logo, {
    class: "block mx-auto max-w-xs fill-white",
    height: "40"
  }, null, _parent));
  _push(`<p class="text-sm font-medium text-blue-100 text-center px-4 mt-2">${ssrInterpolate(_ctx.$t("Let us know who you are, and let's get talking."))}</p></nav><div class="p-6"><form class="space-y-4"><div class="grid grid-cols-2 gap-3"><div><label class="block text-sm font-semibold text-gray-700 mb-1" for="firstName">${ssrInterpolate(_ctx.$t("First name"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.firstName)} class="${ssrRenderClass([
    "w-full px-3 py-2 border rounded-md text-sm transition-colors duration-200",
    $data.errors.firstName ? "border-red-500 focus:border-red-500" : "border-gray-300 focus:border-blue-500"
  ])}" id="firstName" type="text"${ssrRenderAttr("placeholder", _ctx.$t("Enter first name"))}${ssrIncludeBooleanAttr($data.isSubmitting) ? " disabled" : ""}>`);
  if ($data.errors.firstName) {
    _push(`<p class="text-red-500 text-xs mt-1">${ssrInterpolate($data.errors.firstName)}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div><label class="block text-sm font-semibold text-gray-700 mb-1" for="lastName">${ssrInterpolate(_ctx.$t("Last name"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.lastName)} class="${ssrRenderClass([
    "w-full px-3 py-2 border rounded-md text-sm transition-colors duration-200",
    $data.errors.lastName ? "border-red-500 focus:border-red-500" : "border-gray-300 focus:border-blue-500"
  ])}" id="lastName" type="text"${ssrRenderAttr("placeholder", _ctx.$t("Enter last name"))}${ssrIncludeBooleanAttr($data.isSubmitting) ? " disabled" : ""}>`);
  if ($data.errors.lastName) {
    _push(`<p class="text-red-500 text-xs mt-1">${ssrInterpolate($data.errors.lastName)}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div></div><div><label class="block text-sm font-semibold text-gray-700 mb-1" for="email">${ssrInterpolate(_ctx.$t("Email Address"))} <span class="text-red-500">*</span></label><input${ssrRenderAttr("value", $data.form.email)} class="${ssrRenderClass([
    "w-full px-3 py-2 border rounded-md text-sm transition-colors duration-200",
    $data.errors.email ? "border-red-500 focus:border-red-500" : "border-gray-300 focus:border-blue-500"
  ])}" id="email" type="email"${ssrRenderAttr("placeholder", _ctx.$t("Enter email address"))}${ssrIncludeBooleanAttr($data.isSubmitting) ? " disabled" : ""}>`);
  if ($data.errors.email) {
    _push(`<p class="text-red-500 text-xs mt-1">${ssrInterpolate($data.errors.email)}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div><label class="block text-sm font-semibold text-gray-700 mb-1" for="subject">${ssrInterpolate(_ctx.$t("How can we help?"))} <span class="text-gray-400 text-xs">(${ssrInterpolate(_ctx.$t("Optional"))})</span></label><select class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:outline-none"${ssrIncludeBooleanAttr($data.isSubmitting) ? " disabled" : ""}><option value=""${ssrIncludeBooleanAttr(Array.isArray($data.form.subject) ? ssrLooseContain($data.form.subject, "") : ssrLooseEqual($data.form.subject, "")) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Select a topic"))}</option><option value="general"${ssrIncludeBooleanAttr(Array.isArray($data.form.subject) ? ssrLooseContain($data.form.subject, "general") : ssrLooseEqual($data.form.subject, "general")) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("General Inquiry"))}</option><option value="technical"${ssrIncludeBooleanAttr(Array.isArray($data.form.subject) ? ssrLooseContain($data.form.subject, "technical") : ssrLooseEqual($data.form.subject, "technical")) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Technical Support"))}</option><option value="billing"${ssrIncludeBooleanAttr(Array.isArray($data.form.subject) ? ssrLooseContain($data.form.subject, "billing") : ssrLooseEqual($data.form.subject, "billing")) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Billing Question"))}</option><option value="feature"${ssrIncludeBooleanAttr(Array.isArray($data.form.subject) ? ssrLooseContain($data.form.subject, "feature") : ssrLooseEqual($data.form.subject, "feature")) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Feature Request"))}</option><option value="bug"${ssrIncludeBooleanAttr(Array.isArray($data.form.subject) ? ssrLooseContain($data.form.subject, "bug") : ssrLooseEqual($data.form.subject, "bug")) ? " selected" : ""}>${ssrInterpolate(_ctx.$t("Bug Report"))}</option></select></div>`);
  if ($data.submitError) {
    _push(`<div class="bg-red-50 border border-red-200 rounded-md p-3"><p class="text-red-700 text-sm">${ssrInterpolate($data.submitError)}</p></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<button type="submit"${ssrIncludeBooleanAttr($data.isSubmitting || !$options.isFormValid) ? " disabled" : ""} class="${ssrRenderClass([
    "w-full py-2 px-4 rounded-md text-sm font-semibold transition-all duration-200 flex items-center justify-center",
    $data.isSubmitting || !$options.isFormValid ? "bg-gray-300 text-gray-500 cursor-not-allowed" : "bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg"
  ])}">`);
  if ($data.isSubmitting) {
    _push(`<div class="flex items-center"><div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div> ${ssrInterpolate(_ctx.$t("Starting chat..."))}</div>`);
  } else {
    _push(`<div class="flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> ${ssrInterpolate(_ctx.$t("Start Chat"))}</div>`);
  }
  _push(`</button><p class="text-xs text-gray-500 text-center">${ssrInterpolate(_ctx.$t("By starting a chat, you agree to our"))} <a href="/privacy" class="text-blue-600 hover:underline">${ssrInterpolate(_ctx.$t("Privacy Policy"))}</a></p></form></div></div>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Chat/ImprovedChatForm.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const ImprovedChatForm = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {
  components: {
    Icon,
    Logo,
    Link,
    TopNav,
    FooterSection,
    Switcher,
    FlashMessages,
    EnhancedChatBox,
    ImprovedChatForm
  },
  props: {
    title: String,
    footer: Object
  },
  data() {
    return {
      current_mode: "light",
      current_dir: "ltr",
      current_chat: null,
      open_chat: false,
      unread_count: 0,
      chat_id: 0,
      contact_id: 0,
      message: null,
      enable_option: { color_picker: false },
      showConnectionError: false,
      isTyping: false,
      typingUser: null,
      typingTimeout: null,
      init_user: {
        firstName: "",
        lastName: "",
        email: ""
      }
    };
  },
  mounted() {
    console.log("Public Chat: Vue component mounted!");
    if (localStorage.getItem("chat_id")) {
      this.chat_id = localStorage.getItem("chat_id");
      this.contact_id = localStorage.getItem("contact_id");
      axios.get(this.route("chat.conversation", { id: this.chat_id, contact_id: this.contact_id })).then((res) => {
        if (res.data && Object.keys(res.data).length) {
          this.current_chat = res.data;
          this.watchMessage();
        }
      });
    }
  },
  beforeUnmount() {
    if (this.typingTimeout) {
      clearTimeout(this.typingTimeout);
    }
  },
  methods: {
    toggleChat() {
      this.open_chat = !this.open_chat;
    },
    startChat() {
      if (!!this.init_user.email && !!this.init_user.firstName && !!this.init_user.lastName) {
        console.log("Public Chat: Starting chat initialization...");
        const chatData = {
          first_name: this.init_user.firstName,
          last_name: this.init_user.lastName,
          email: this.init_user.email,
          subject: this.init_user.subject || null,
          department: this.init_user.department || null,
          priority: this.init_user.priority || null,
          source: this.init_user.source || null
        };
        console.log("Public Chat: Sending data to backend:", chatData);
        axios.post(this.route("chat.init"), chatData).then((response) => {
          var _a;
          if (response.data && response.data.success && response.data.conversation) {
            this.current_chat = response.data.conversation;
            this.chat_id = this.current_chat.id;
            this.contact_id = this.current_chat.contact_id;
            localStorage.setItem("chat_id", this.chat_id);
            localStorage.setItem("contact_id", this.contact_id);
            console.log("Public Chat: Chat initialized successfully", this.current_chat);
            this.open_chat = true;
            this.watchMessage();
            this.scrollToBottom();
          } else {
            throw new Error(((_a = response.data) == null ? void 0 : _a.message) || "Failed to initialize chat");
          }
        }).catch((error) => {
          var _a, _b;
          console.error("Public Chat: Failed to initialize chat:", error);
          if (((_a = error.response) == null ? void 0 : _a.status) === 422) {
            const errors = error.response.data.errors;
            const firstError = Object.values(errors)[0];
            alert(firstError ? firstError[0] : "Please check your information and try again.");
          } else if (((_b = error.response) == null ? void 0 : _b.status) === 500) {
            alert("Server error. Please try again later.");
          } else if (error.code === "NETWORK_ERROR") {
            alert("Network error. Please check your connection.");
          } else {
            alert("Failed to start chat. Please try again.");
          }
        });
      } else {
        console.warn("Public Chat: Missing required user information");
        alert("Please fill in all required fields (First Name, Last Name, Email)");
      }
    },
    // Enhanced chat methods
    handleStartChat(userData) {
      this.init_user = userData;
      this.startChat();
    },
    handleTyping(typingData) {
      if (typingData && typingData.isTyping) {
        this.broadcastTypingIndicator(typingData);
      }
    },
    broadcastTypingIndicator(typingData) {
      console.log("Public Chat: Typing indicator disabled (direct Echo implementation)");
    },
    handleSendMessage(messageData) {
      if (!messageData.message || !messageData.message.trim()) {
        console.warn("Public Chat: Empty message, not sending");
        return;
      }
      const messagePayload = {
        message: messageData.message,
        contact_id: this.current_chat.contact_id,
        conversation_id: this.current_chat.id
      };
      axios.post(this.route("chat.send_message"), messagePayload).then((response) => {
        var _a;
        if (response.data && response.data.success && response.data.message) {
          console.log("Public Chat: Message sent successfully");
          this.$nextTick(() => {
            const chatContainer = document.querySelector(".chat-messages");
            if (chatContainer) {
              chatContainer.scrollTop = chatContainer.scrollHeight;
            }
          });
        } else {
          throw new Error(((_a = response.data) == null ? void 0 : _a.message) || "Failed to send message");
        }
      }).catch((error) => {
        var _a, _b;
        console.error("Public Chat: Failed to send message:", error);
        if (((_a = error.response) == null ? void 0 : _a.status) === 422) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          alert(firstError ? firstError[0] : "Please check your message and try again.");
        } else if (((_b = error.response) == null ? void 0 : _b.status) === 500) {
          alert("Server error. Please try again later.");
        } else if (error.code === "NETWORK_ERROR") {
          alert("Network error. Please check your connection.");
        } else {
          alert("Failed to send message. Please try again.");
        }
      });
    },
    sendMessage() {
      var vm = this;
      const messageData = {
        message: this.message,
        contact_id: this.current_chat.contact_id,
        conversation_id: this.current_chat.id
      };
      this.message = "";
      axios.post(this.route("chat.send_message"), messageData).then((response) => {
        if (response.data && response.data.success && response.data.message) {
          vm.current_chat.messages.push(response.data.message);
        }
      }).catch((error) => {
        console.log(error);
      });
    },
    watchMessage() {
      console.log("Public Chat: watchMessage method called - using direct Echo connection");
      var vm = this;
      const checkEcho = () => {
        console.log("Public Chat: checkEcho called");
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
          console.log("Public Chat: Echo is ready!");
          window.Echo.connector.pusher.connection.bind("connected", () => {
            console.log("Public Chat: Pusher connected!");
          });
          window.Echo.connector.pusher.connection.bind("disconnected", () => {
            console.log("Public Chat: Pusher disconnected!");
          });
          window.Echo.connector.pusher.connection.bind("error", (error) => {
            console.error("Public Chat: Pusher error:", error);
            vm.showConnectionError = true;
            setTimeout(() => {
              vm.showConnectionError = false;
            }, 5e3);
          });
          if (vm.chat_id) {
            console.log("Public Chat: Setting up direct Echo listeners for conversation:", vm.chat_id);
            const channel = window.Echo.channel(`chat.${vm.chat_id}`);
            console.log("Public Chat: Channel created:", channel);
            console.log("Public Chat: Channel name:", channel.name);
            console.log("Public Chat: Channel subscription:", channel.subscription);
            if (channel.subscription) {
              channel.subscription.bind("pusher:subscription_succeeded", (data) => {
                console.log("Public Chat: Channel subscription succeeded!", data);
              });
              channel.subscription.bind("pusher:subscription_error", (error) => {
                console.error("Public Chat: Channel subscription failed!", error);
              });
              if (channel.subscription.state === "subscribed") {
                console.log("Public Chat: Channel already subscribed!");
              } else {
                console.log("Public Chat: Channel subscription state:", channel.subscription.state);
              }
            }
            channel.listen("NewChatMessage", (e) => {
              console.log("Public Chat: Received message via direct Echo:", e);
              console.log("Public Chat: Event structure:", {
                hasChatMessage: !!e.chatMessage,
                chatMessageType: typeof e.chatMessage,
                chatMessageContent: e.chatMessage
              });
              if (e && e.chatMessage && typeof e.chatMessage === "object") {
                console.log("Public Chat: Processing message:", e.chatMessage);
                const message = {
                  id: e.chatMessage.id,
                  message: e.chatMessage.message || "",
                  conversation_id: e.chatMessage.conversation_id,
                  user_id: e.chatMessage.user_id,
                  contact_id: e.chatMessage.contact_id,
                  created_at: e.chatMessage.created_at,
                  updated_at: e.chatMessage.updated_at,
                  user: e.chatMessage.user,
                  contact: e.chatMessage.contact
                };
                console.log("Public Chat: Adding message to chat:", message);
                vm.current_chat.messages.push(message);
                console.log("Public Chat: Total messages:", vm.current_chat.messages.length);
                vm.$nextTick(() => {
                  const chatContainer = document.querySelector(".chat-messages");
                  if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                  }
                });
              }
            });
            channel.listen("TypingIndicator", (e) => {
              console.log("Public Chat: Received typing indicator via direct Echo:", e);
              if (e && e.user_id && e.user_id !== vm.contact_id) {
                vm.handleTypingIndicator(e);
              }
            });
            console.log("Public Chat: Channel subscription test - listening for any events");
            console.log("Public Chat: Channel subscription status:", {
              hasSubscription: !!channel.subscription,
              subscriptionState: channel.subscription ? channel.subscription.state : "no subscription",
              hasPusher: !!(channel.subscription && channel.subscription.pusher)
            });
            setTimeout(() => {
              console.log("Public Chat: Channel subscription test after 2 seconds:", {
                hasSubscription: !!channel.subscription,
                subscriptionState: channel.subscription ? channel.subscription.state : "no subscription"
              });
            }, 2e3);
            console.log("Public Chat: Direct Echo listeners set up successfully");
          }
        } else {
          console.warn("Public Chat: Echo not ready, retrying...");
          setTimeout(checkEcho, 500);
        }
      };
      checkEcho();
    },
    handleTypingIndicator(data) {
      if (data && data.user_id && data.user_id !== this.contact_id) {
        this.isTyping = data.is_typing || false;
        this.typingUser = data.user || { id: data.user_id };
        if (this.isTyping) {
          if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
          }
          this.typingTimeout = setTimeout(() => {
            this.isTyping = false;
            this.typingUser = null;
          }, 3e3);
        } else {
          this.isTyping = false;
          this.typingUser = null;
          if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
          }
        }
      }
    },
    scrollFunction() {
      const mybutton = document.getElementById("back-to-top");
      if (mybutton != null) {
        if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
          mybutton.classList.add("block");
          mybutton.classList.remove("hidden");
        } else {
          mybutton.classList.add("hidden");
          mybutton.classList.remove("block");
        }
      }
    },
    topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    },
    switchMode() {
      this.current_mode = this.current_mode === "light" ? "dark" : "light";
      localStorage.setItem("current_mode", this.current_mode);
      this.changeTheme();
    },
    changeTheme() {
      const htmlTag = document.getElementsByTagName("html")[0];
      htmlTag.className = this.current_mode;
    },
    changeDir() {
      const htmlTag = document.getElementsByTagName("html")[0];
      htmlTag.dir = this.current_dir;
    },
    actionColorScheme(e) {
      e.preventDefault();
      const that = e.currentTarget;
      const dataExpend = that.getAttribute("data-expend");
      if (dataExpend !== "yes") {
        that.setAttribute("style", "right:-10px !important");
        that.setAttribute("data-expend", "yes");
      } else {
        that.setAttribute("style", "right:-154px !important");
        that.setAttribute("data-expend", "no");
      }
    },
    setColorScheme(e) {
      e.preventDefault();
      const color = e.currentTarget.getAttribute("data-scheme");
      const colors = ["scheme-indigo", "scheme-orange", "scheme-amber", "scheme-yellow", "scheme-lime", "scheme-green", "scheme-cyan", "scheme-sky", "scheme-violet", "scheme-purple", "scheme-fuchsia", "scheme-pink", "scheme-rose"];
      document.getElementsByTagName("body")[0].classList.remove(...colors);
      document.getElementsByTagName("body")[0].classList.add(color);
      localStorage.setItem("scheme", color);
    },
    setColorSchemeFromStorage() {
      if (!!localStorage.getItem("scheme")) {
        const colors = ["scheme-indigo", "scheme-orange", "scheme-amber", "scheme-yellow", "scheme-lime", "scheme-green", "scheme-cyan", "scheme-sky", "scheme-violet", "scheme-purple", "scheme-fuchsia", "scheme-pink", "scheme-rose"];
        document.getElementsByTagName("body")[0].classList.remove(...colors);
        document.getElementsByTagName("body")[0].classList.add(localStorage.getItem("scheme"));
      }
    }
  },
  updated() {
    this.setColorSchemeFromStorage();
  },
  created() {
    this.setColorSchemeFromStorage();
    this.moment = moment;
    const vm = this;
    window.onscroll = function() {
      vm.scrollFunction();
    };
    if (localStorage.getItem("current_dir")) {
      this.current_dir = localStorage.getItem("current_dir");
      this.changeDir();
    }
    if (this.$page.props.enable_options) {
      let options = JSON.parse(this.$page.props.enable_options.value);
      options.forEach((option) => {
        this.enable_option[option.slug] = !!option.value;
      });
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_top_nav = resolveComponent("top-nav");
  const _component_flash_messages = resolveComponent("flash-messages");
  const _component_footer_section = resolveComponent("footer-section");
  const _component_EnhancedChatBox = resolveComponent("EnhancedChatBox");
  const _component_ImprovedChatForm = resolveComponent("ImprovedChatForm");
  _push(`<div${ssrRenderAttrs(mergeProps({
    class: ["text-base text-black dark:text-white dark:bg-slate-900 layout_landing", $data.current_mode],
    dir: _ctx.$page.props.dir
  }, _attrs))}>`);
  _push(ssrRenderComponent(_component_top_nav, null, null, _parent));
  _push(ssrRenderComponent(_component_flash_messages, null, null, _parent));
  ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
  _push(ssrRenderComponent(_component_footer_section, { footer: $props.footer }, null, _parent));
  _push(`<a id="back-to-top" href="javascript:void(0)" class="back-to-top flex fixed hidden bottom-5 right-5 left-auto z-[999] h-10 w-10 items-center justify-center rounded-md bg-primary text-white shadow-md transition duration-300 ease-in-out hover:bg-dark"><span class="mt-[6px] h-3 w-3 rotate-45 border-t border-l border-white"></span></a>`);
  if (!!this.enable_option && this.enable_option.chat) {
    _push(`<div class="chat_public">`);
    if (!!$data.open_chat && !!$data.current_chat) {
      _push(`<div class="chat__box"><div class="flex justify-center items-center">`);
      _push(ssrRenderComponent(_component_EnhancedChatBox, {
        messages: $data.current_chat.messages || [],
        "is-typing": $data.isTyping,
        "typing-user": $data.typingUser,
        "current-user-id": $data.contact_id,
        onSendMessage: $options.handleSendMessage,
        onClose: $options.toggleChat,
        onTyping: $options.handleTyping
      }, null, _parent));
      _push(`</div></div>`);
    } else {
      _push(`<!---->`);
    }
    if (!!$data.open_chat && !$data.current_chat) {
      _push(`<div class="init_chat"><div class="flex justify-center items-center">`);
      _push(ssrRenderComponent(_component_ImprovedChatForm, { onSubmit: $options.handleStartChat }, null, _parent));
      _push(`</div></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<button class="chat_bubble">`);
    if ($data.unread_count) {
      _push(`<span class="notification_badge">${ssrInterpolate($data.unread_count)}</span>`);
    } else {
      _push(`<!---->`);
    }
    if (!$data.open_chat) {
      _push(`<span class="chat__icn hover:scale-125 duration-300"><img src="/images/svg/chat-logo-v2.svg" alt="Live Chat"></span>`);
    } else {
      _push(`<!---->`);
    }
    if (!!$data.open_chat) {
      _push(`<span class="chat__close hover:scale-125 duration-300"><img src="/images/svg/close.svg" alt="Close Chat"></span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<span class="bottom_text">Let&#39;s talk</span></button></div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`<span class="disabled_button"></span></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Shared/Landing/Layout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Layout = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  Layout as L
};
