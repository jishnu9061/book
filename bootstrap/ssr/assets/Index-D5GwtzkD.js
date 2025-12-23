import { unref, withCtx, createVNode, createBlock, openBlock, toDisplayString, createTextVNode, createCommentVNode, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderList, ssrRenderClass, ssrInterpolate } from "vue/server-renderer";
import { L as Layout } from "./Layout-DwqqF5bk.js";
import { Head, Link } from "@inertiajs/vue3";
import { EnvelopeIcon, CheckCircleIcon } from "@heroicons/vue/24/solid";
import { P as Pagination } from "./Pagination-DvKmvDq4.js";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "./Dropdown-DNX6MmV_.js";
import "@popperjs/core";
import "lucide-vue-next";
import "moment";
import "laravel-vue-i18n";
import "axios";
import "@heroicons/vue/24/outline";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    notifications: Object
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), { title: "All Notifications" }, null, _parent));
      _push(ssrRenderComponent(Layout, null, {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<h2 class="font-semibold text-xl text-gray-800 leading-tight" data-v-5ec6c5fd${_scopeId}> All Notifications </h2>`);
          } else {
            return [
              createVNode("h2", { class: "font-semibold text-xl text-gray-800 leading-tight" }, " All Notifications ")
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="py-12" data-v-5ec6c5fd${_scopeId}><div class="max-w-4xl mx-auto sm:px-6 lg:px-8" data-v-5ec6c5fd${_scopeId}><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" data-v-5ec6c5fd${_scopeId}><ul role="list" class="divide-y divide-gray-200" data-v-5ec6c5fd${_scopeId}><!--[-->`);
            ssrRenderList(__props.notifications.data, (notification) => {
              _push2(`<li data-v-5ec6c5fd${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("notifications.read", notification.id),
                method: "post",
                as: "button",
                type: "button",
                class: ["block w-full text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition", { "bg-blue-50": !notification.read_at }]
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="flex items-center px-4 py-4 sm:px-6" data-v-5ec6c5fd${_scopeId2}><div class="min-w-0 flex-1 flex items-center" data-v-5ec6c5fd${_scopeId2}><div class="flex-shrink-0" data-v-5ec6c5fd${_scopeId2}>`);
                    if (notification.read_at) {
                      _push3(ssrRenderComponent(unref(EnvelopeIcon), { class: "h-8 w-8 rounded-full text-gray-400" }, null, _parent3, _scopeId2));
                    } else {
                      _push3(`<span class="relative flex h-8 w-8" data-v-5ec6c5fd${_scopeId2}>`);
                      _push3(ssrRenderComponent(unref(EnvelopeIcon), { class: "h-8 w-8 rounded-full text-blue-500" }, null, _parent3, _scopeId2));
                      _push3(`<span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-blue-400 ring-2 ring-white" data-v-5ec6c5fd${_scopeId2}></span></span>`);
                    }
                    _push3(`</div><div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" data-v-5ec6c5fd${_scopeId2}><div data-v-5ec6c5fd${_scopeId2}><p class="${ssrRenderClass([{ "font-bold text-gray-800": !notification.read_at, "text-gray-600": notification.read_at }, "text-sm truncate"])}" data-v-5ec6c5fd${_scopeId2}>${ssrInterpolate(notification.data.message)}</p><p class="mt-2 flex items-center text-sm text-gray-500" data-v-5ec6c5fd${_scopeId2}>${ssrInterpolate(_ctx.$t("Ticket"))}<span data-v-5ec6c5fd${_scopeId2}>: ${ssrInterpolate(notification.data.ticket_subject)}</span></p></div></div></div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                        createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                          createVNode("div", { class: "flex-shrink-0" }, [
                            notification.read_at ? (openBlock(), createBlock(unref(EnvelopeIcon), {
                              key: 0,
                              class: "h-8 w-8 rounded-full text-gray-400"
                            })) : (openBlock(), createBlock("span", {
                              key: 1,
                              class: "relative flex h-8 w-8"
                            }, [
                              createVNode(unref(EnvelopeIcon), { class: "h-8 w-8 rounded-full text-blue-500" }),
                              createVNode("span", { class: "absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-blue-400 ring-2 ring-white" })
                            ]))
                          ]),
                          createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                            createVNode("div", null, [
                              createVNode("p", {
                                class: ["text-sm truncate", { "font-bold text-gray-800": !notification.read_at, "text-gray-600": notification.read_at }]
                              }, toDisplayString(notification.data.message), 3),
                              createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                createTextVNode(toDisplayString(_ctx.$t("Ticket")), 1),
                                createVNode("span", null, ": " + toDisplayString(notification.data.ticket_subject), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</li>`);
            });
            _push2(`<!--]-->`);
            if (__props.notifications.data.length === 0) {
              _push2(`<li class="text-center py-10" data-v-5ec6c5fd${_scopeId}>`);
              _push2(ssrRenderComponent(unref(CheckCircleIcon), { class: "h-12 w-12 text-green-400 mx-auto" }, null, _parent2, _scopeId));
              _push2(`<p class="mt-2 text-sm text-gray-600" data-v-5ec6c5fd${_scopeId}>No notifications yet.</p></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</ul></div><div class="mt-6" data-v-5ec6c5fd${_scopeId}>`);
            _push2(ssrRenderComponent(Pagination, {
              links: __props.notifications.links
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "py-12" }, [
                createVNode("div", { class: "max-w-4xl mx-auto sm:px-6 lg:px-8" }, [
                  createVNode("div", { class: "bg-white overflow-hidden shadow-sm sm:rounded-lg" }, [
                    createVNode("ul", {
                      role: "list",
                      class: "divide-y divide-gray-200"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(__props.notifications.data, (notification) => {
                        return openBlock(), createBlock("li", {
                          key: notification.id
                        }, [
                          createVNode(unref(Link), {
                            href: _ctx.route("notifications.read", notification.id),
                            method: "post",
                            as: "button",
                            type: "button",
                            class: ["block w-full text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition", { "bg-blue-50": !notification.read_at }]
                          }, {
                            default: withCtx(() => [
                              createVNode("div", { class: "flex items-center px-4 py-4 sm:px-6" }, [
                                createVNode("div", { class: "min-w-0 flex-1 flex items-center" }, [
                                  createVNode("div", { class: "flex-shrink-0" }, [
                                    notification.read_at ? (openBlock(), createBlock(unref(EnvelopeIcon), {
                                      key: 0,
                                      class: "h-8 w-8 rounded-full text-gray-400"
                                    })) : (openBlock(), createBlock("span", {
                                      key: 1,
                                      class: "relative flex h-8 w-8"
                                    }, [
                                      createVNode(unref(EnvelopeIcon), { class: "h-8 w-8 rounded-full text-blue-500" }),
                                      createVNode("span", { class: "absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-blue-400 ring-2 ring-white" })
                                    ]))
                                  ]),
                                  createVNode("div", { class: "min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4" }, [
                                    createVNode("div", null, [
                                      createVNode("p", {
                                        class: ["text-sm truncate", { "font-bold text-gray-800": !notification.read_at, "text-gray-600": notification.read_at }]
                                      }, toDisplayString(notification.data.message), 3),
                                      createVNode("p", { class: "mt-2 flex items-center text-sm text-gray-500" }, [
                                        createTextVNode(toDisplayString(_ctx.$t("Ticket")), 1),
                                        createVNode("span", null, ": " + toDisplayString(notification.data.ticket_subject), 1)
                                      ])
                                    ])
                                  ])
                                ])
                              ])
                            ]),
                            _: 2
                          }, 1032, ["href", "class"])
                        ]);
                      }), 128)),
                      __props.notifications.data.length === 0 ? (openBlock(), createBlock("li", {
                        key: 0,
                        class: "text-center py-10"
                      }, [
                        createVNode(unref(CheckCircleIcon), { class: "h-12 w-12 text-green-400 mx-auto" }),
                        createVNode("p", { class: "mt-2 text-sm text-gray-600" }, "No notifications yet.")
                      ])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("div", { class: "mt-6" }, [
                    createVNode(Pagination, {
                      links: __props.notifications.links
                    }, null, 8, ["links"])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Notifications/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-5ec6c5fd"]]);
export {
  Index as default
};
