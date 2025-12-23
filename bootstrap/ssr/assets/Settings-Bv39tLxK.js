import { unref, withCtx, createVNode, createBlock, createCommentVNode, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useForm, Head } from "@inertiajs/vue3";
import { L as Layout } from "./Layout-DwqqF5bk.js";
import "./Dropdown-DNX6MmV_.js";
import "./FlashMessages-DUb4hfI8.js";
import "lucide-vue-next";
import "@popperjs/core";
import "moment";
import "laravel-vue-i18n";
import "axios";
import "@heroicons/vue/24/outline";
const _sfc_main = {
  __name: "Settings",
  __ssrInlineRender: true,
  props: {
    activatedDomain: String,
    licenseKey: String,
    title: String,
    error: String,
    auth: Object
  },
  setup(__props) {
    const form = useForm({});
    function deactivate() {
      if (confirm(
        "Are you sure you want to deactivate this license? The application will stop working on this domain until a new license is activated."
      )) {
        form.post(route("license.deactivate"));
      }
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), {
        title: _ctx.$t(__props.title)
      }, null, _parent));
      if (__props.auth.user) {
        _push(ssrRenderComponent(Layout, null, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="p-8"${_scopeId}><div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow"${_scopeId}>`);
              if (__props.error) {
                _push2(`<div class="mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-200"${_scopeId}><h3 class="font-bold"${_scopeId}>Attention Required</h3><p${_scopeId}>${ssrInterpolate(__props.error)}</p></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<h2 class="text-xl font-bold mb-4"${_scopeId}>License Information</h2><div class="space-y-3"${_scopeId}><div${_scopeId}><span class="font-semibold text-gray-600"${_scopeId}>Status:</span><span class="ml-2 text-green-600 font-bold"${_scopeId}>Active</span></div><div${_scopeId}><span class="font-semibold text-gray-600"${_scopeId}>Activated Domain:</span><span class="ml-2"${_scopeId}>${ssrInterpolate(__props.activatedDomain)}</span></div><div${_scopeId}><span class="font-semibold text-gray-600"${_scopeId}>Purchase Code:</span><span class="ml-2 font-mono"${_scopeId}>${ssrInterpolate(__props.licenseKey)}</span></div></div><div class="mt-6 pt-6 border-t"${_scopeId}><h3 class="font-bold text-lg text-red-700"${_scopeId}>Deactivate License</h3><p class="text-sm text-gray-600 mt-2"${_scopeId}> To use your license on a different domain, you must first deactivate it. Once deactivated, this installation will no longer be functional until a valid license is activated again. </p><div class="mt-4"${_scopeId}><button${ssrIncludeBooleanAttr(unref(form).processing) ? " disabled" : ""} class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"${_scopeId}> Deactivate License </button></div></div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "p-8" }, [
                  createVNode("div", { class: "max-w-2xl mx-auto bg-white p-6 rounded-lg shadow" }, [
                    __props.error ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-200"
                    }, [
                      createVNode("h3", { class: "font-bold" }, "Attention Required"),
                      createVNode("p", null, toDisplayString(__props.error), 1)
                    ])) : createCommentVNode("", true),
                    createVNode("h2", { class: "text-xl font-bold mb-4" }, "License Information"),
                    createVNode("div", { class: "space-y-3" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold text-gray-600" }, "Status:"),
                        createVNode("span", { class: "ml-2 text-green-600 font-bold" }, "Active")
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold text-gray-600" }, "Activated Domain:"),
                        createVNode("span", { class: "ml-2" }, toDisplayString(__props.activatedDomain), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold text-gray-600" }, "Purchase Code:"),
                        createVNode("span", { class: "ml-2 font-mono" }, toDisplayString(__props.licenseKey), 1)
                      ])
                    ]),
                    createVNode("div", { class: "mt-6 pt-6 border-t" }, [
                      createVNode("h3", { class: "font-bold text-lg text-red-700" }, "Deactivate License"),
                      createVNode("p", { class: "text-sm text-gray-600 mt-2" }, " To use your license on a different domain, you must first deactivate it. Once deactivated, this installation will no longer be functional until a valid license is activated again. "),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode("button", {
                          onClick: deactivate,
                          disabled: unref(form).processing,
                          class: "px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                        }, " Deactivate License ", 8, ["disabled"])
                      ])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<div><div class="p-8"><div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">`);
        if (__props.error) {
          _push(`<div class="mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-200"><h3 class="font-bold">Attention Required</h3><p>${ssrInterpolate(__props.error)}</p></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<h2 class="text-xl font-bold mb-4">License Information</h2><div class="space-y-3"><div><span class="font-semibold text-gray-600">Status:</span><span class="ml-2 text-green-600 font-bold">Active</span></div><div><span class="font-semibold text-gray-600">Activated Domain:</span><span class="ml-2">${ssrInterpolate(__props.activatedDomain)}</span></div><div><span class="font-semibold text-gray-600">Purchase Code:</span><span class="ml-2 font-mono">${ssrInterpolate(__props.licenseKey)}</span></div></div><div class="mt-6 pt-6 border-t"><h3 class="font-bold text-lg text-red-700">Deactivate License</h3><p class="text-sm text-gray-600 mt-2"> Deactivating your license will release it from this domain (${ssrInterpolate(__props.activatedDomain)}), allowing you to use it on a different domain. Once deactivated, this installation will no longer be functional until a valid license is activated again. </p><div class="mt-4"><button${ssrIncludeBooleanAttr(unref(form).processing) ? " disabled" : ""} class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"> Deactivate License on This Domain </button></div></div></div></div></div>`);
      }
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/License/Settings.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
