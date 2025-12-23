import { Head, Link } from "@inertiajs/vue3";
import { I as Icon } from "./Dropdown-DNX6MmV_.js";
import pickBy from "lodash/pickBy.js";
import { L as Layout } from "./Layout-DwqqF5bk.js";
import throttle from "lodash/throttle.js";
import mapValues from "lodash/mapValues.js";
import { P as Pagination } from "./Pagination-DvKmvDq4.js";
import { S as SearchInput } from "./SearchInput-CFu_dYUb.js";
import moment from "moment";
import axios from "axios";
import { Check, Smile, Paperclip, Info, Pin, Archive, Clock, X, MoreVertical, Camera, Settings, Search, AlertCircle, WifiOff, Wifi, Trash2, Send, Plus, User, MessageCircle } from "lucide-vue-next";
import { resolveComponent, mergeProps, withCtx, createVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderClass, ssrRenderList, ssrRenderStyle, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./FlashMessages-DUb4hfI8.js";
import "@popperjs/core";
import "laravel-vue-i18n";
import "@heroicons/vue/24/outline";
import "uuid";
const _sfc_main = {
  metaInfo: { title: "Chat" },
  components: {
    SearchInput,
    Icon,
    Link,
    Head,
    Pagination,
    MessageCircle,
    User,
    Plus,
    Send,
    Trash2,
    Wifi,
    WifiOff,
    AlertCircle,
    Search,
    Settings,
    Camera,
    MoreVertical,
    X,
    Clock,
    Archive,
    Pin,
    Info,
    Paperclip,
    Smile,
    Check
  },
  layout: Layout,
  props: {
    filters: Object,
    conversations: Object,
    chat: Object,
    title: String
  },
  remember: "form",
  data() {
    return {
      message: "",
      user: this.$page.props.auth.user,
      user_access: this.$page.props.auth.user.access,
      form: {
        search: this.filters.search
      },
      isSending: false,
      connectionStatus: "connected",
      // 'connected', 'connecting', 'disconnected', 'error'
      echo: null,
      isTyping: false,
      unreadCount: 0,
      filter: "all",
      // 'all', 'unread', 'archived'
      showSearch: false,
      showSettings: false,
      showUserMenu: false,
      showChatInfo: false,
      showChatSettings: false,
      showAttachmentMenu: false,
      showEmojiPicker: false,
      showConversationMenu: null,
      typingTimeout: null,
      lastTypingTime: null
    };
  },
  computed: {
    connectionStatusClass() {
      const statusClasses = {
        connected: "bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-300",
        connecting: "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300",
        disconnected: "bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300",
        error: "bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300"
      };
      return statusClasses[this.connectionStatus] || statusClasses.disconnected;
    },
    connectionStatusDotClass() {
      const dotClasses = {
        connected: "bg-green-500",
        connecting: "bg-yellow-500 animate-pulse",
        disconnected: "bg-red-500",
        error: "bg-red-500"
      };
      return dotClasses[this.connectionStatus] || dotClasses.disconnected;
    },
    connectionStatusText() {
      const statusTexts = {
        connected: this.$t("Connected"),
        connecting: this.$t("Connecting..."),
        disconnected: this.$t("Disconnected"),
        error: this.$t("Connection Error")
      };
      return statusTexts[this.connectionStatus] || this.$t("Unknown");
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route("chat"), pickBy(this.form), { preserveState: true });
      }, 150)
    }
  },
  methods: {
    textOnly(txt) {
      return txt.replace(/(<([^>]+)>)/ig, "");
    },
    reset() {
      this.form = mapValues(this.form, () => null);
    },
    async sendMessage() {
      var _a, _b, _c;
      if (!this.message.trim() || this.isSending || !this.chat) return;
      this.isSending = true;
      const messageData = {
        message: this.message,
        user_id: this.user.id,
        _token: this.$page.props.csrf_token,
        conversation_id: this.chat.id
      };
      const originalMessage = this.message;
      this.message = "";
      this.isTyping = false;
      this.broadcastTypingIndicator(false);
      try {
        const response = await axios.post(this.route("chat.message"), messageData);
        if (response.data && response.data.success) {
          console.log("Admin Chat: Message sent successfully:", response.data);
          this.scrollToBottom();
        } else {
          console.log("ok");
          throw new Error(((_a = response.data) == null ? void 0 : _a.message) || "Failed to send message");
        }
      } catch (error) {
        console.error("Admin Chat: Error sending message:", error);
        this.message = originalMessage;
        if (((_b = error.response) == null ? void 0 : _b.status) === 422) {
          const errors = error.response.data.errors;
          const firstError = Object.values(errors)[0];
          alert(firstError ? firstError[0] : "Please check your message and try again.");
        } else if (((_c = error.response) == null ? void 0 : _c.status) === 500) {
          alert("Server error. Please try again later.");
        } else if (error.code === "NETWORK_ERROR") {
          alert("Network error. Please check your connection.");
        } else {
          alert("Failed to send message. Please try again.");
        }
        this.connectionStatus = "error";
        setTimeout(() => {
          this.connectionStatus = "connected";
        }, 3e3);
      } finally {
        this.isSending = false;
      }
    },
    navigateTo(id) {
      window.location.href = this.route("chat.current", id);
    },
    destroy(id) {
      if (confirm(this.$t("Are you sure you want to delete this conversation?"))) {
        this.$inertia.delete(this.route("chat.destroy", id), {
          onSuccess: () => {
            setTimeout(() => {
              this.reset();
            }, 4e3);
          }
        });
      }
    },
    restore(id) {
      if (confirm(this.$t("Are you sure you want to restore this conversation?"))) {
        this.$inertia.put(this.route("chat.restore", id));
      }
    },
    pushMessage(message) {
      console.log("Admin Chat: Processing new message:", message);
      console.log("Admin Chat: Current chat ID:", this.chat ? this.chat.id : "No chat selected");
      console.log("Admin Chat: Message conversation ID:", message.conversation_id);
      const sanitizedMessage = this.sanitizeMessage(message);
      console.log("Admin Chat: Sanitized message:", sanitizedMessage);
      let chat = this.conversations.data.find((x) => x.id === sanitizedMessage.conversation_id);
      if (typeof chat === "object") {
        chat.title = sanitizedMessage.message;
        chat.total_entry = chat.total_entry + 1;
        chat.updated_at = sanitizedMessage.created_at;
        if (!!this.chat && this.chat.id === sanitizedMessage.conversation_id) {
          console.log("Admin Chat: Adding message to current chat");
          const existingMessage = this.chat.messages.find((m) => m.id === sanitizedMessage.id);
          if (!existingMessage) {
            console.log("Admin Chat: Message is new, adding to chat");
            this.chat.messages.push(sanitizedMessage);
            chat.total_entry = 0;
            this.scrollToBottom();
            console.log("Admin Chat: Message added, total messages:", this.chat.messages.length);
            if (sanitizedMessage.user_id !== this.user.id) {
              this.markMessageAsRead(sanitizedMessage.id);
            }
          } else {
            console.log("Admin Chat: Message already exists, skipping");
          }
        } else {
          console.log("Admin Chat: Message not for current chat, skipping display");
        }
      } else {
        this.conversations.data.unshift({
          "id": sanitizedMessage.conversation_id,
          "total_entry": 1,
          "title": sanitizedMessage.message,
          "creator": sanitizedMessage.contact || sanitizedMessage.user,
          "updated_at": sanitizedMessage.created_at,
          "status": "active"
        });
      }
    },
    sanitizeMessage(message) {
      if (!message || typeof message !== "object") {
        console.error("Admin Chat: Invalid message object:", message);
        return {
          id: Date.now(),
          message: "Invalid message",
          conversation_id: 0,
          user_id: null,
          contact_id: null,
          created_at: (/* @__PURE__ */ new Date()).toISOString(),
          updated_at: (/* @__PURE__ */ new Date()).toISOString(),
          user: null,
          contact: null
        };
      }
      let messageContent = message.message;
      if (typeof messageContent === "object") {
        if (messageContent.text) {
          messageContent = messageContent.text;
        } else if (messageContent.content) {
          messageContent = messageContent.content;
        } else if (messageContent.message) {
          messageContent = messageContent.message;
        } else {
          messageContent = JSON.stringify(messageContent);
        }
      }
      if (typeof messageContent !== "string") {
        messageContent = String(messageContent || "");
      }
      messageContent = messageContent.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#x27;").replace(/\//g, "&#x2F;");
      return {
        id: message.id || Date.now(),
        message: messageContent,
        conversation_id: message.conversation_id || 0,
        user_id: message.user_id || null,
        contact_id: message.contact_id || null,
        created_at: message.created_at || (/* @__PURE__ */ new Date()).toISOString(),
        updated_at: message.updated_at || (/* @__PURE__ */ new Date()).toISOString(),
        user: message.user || null,
        contact: message.contact || null,
        is_read: message.is_read || false,
        message_type: message.message_type || "text"
      };
    },
    async markMessageAsRead(messageId) {
      try {
        await axios.post(this.route("chat.mark-read"), {
          message_id: messageId,
          _token: this.$page.props.csrf_token
        });
      } catch (error) {
        console.error("Admin Chat: Error marking message as read:", error);
      }
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },
    formatTime(date) {
      if (!date) return "";
      return this.$t("error") === "error" ? moment(date).fromNow() : moment(date).locale("zh-tw").fromNow();
    },
    formatMessage(message) {
      if (!message) return "";
      if (typeof message === "object") {
        if (message.text) return message.text;
        if (message.content) return message.content;
        if (message.message) return message.message;
        return JSON.stringify(message);
      }
      const messageStr = String(message || "");
      return messageStr.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#x27;").replace(/\//g, "&#x2F;");
    },
    setupEcho() {
      console.log("Admin Chat: setupEcho method called - using direct Echo connection");
      const checkEcho = () => {
        console.log("Admin Chat: checkEcho called");
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
          console.log("Admin Chat: Echo is ready!");
          this.connectionStatus = "connected";
          window.Echo.connector.pusher.connection.bind("connected", () => {
            console.log("Admin Chat: Pusher connected!");
            this.connectionStatus = "connected";
          });
          window.Echo.connector.pusher.connection.bind("disconnected", () => {
            console.log("Admin Chat: Pusher disconnected!");
            this.connectionStatus = "disconnected";
          });
          window.Echo.connector.pusher.connection.bind("error", (error) => {
            console.error("Admin Chat: Pusher error:", error);
            this.connectionStatus = "error";
          });
          if (this.chat && this.chat.id) {
            console.log("Admin Chat: Setting up direct Echo listeners for conversation:", this.chat.id);
            const channel = window.Echo.channel(`chat.${this.chat.id}`);
            console.log("Admin Chat: Channel created:", channel);
            console.log("Admin Chat: Channel name:", channel.name);
            console.log("Admin Chat: Channel subscription:", channel.subscription);
            if (channel.subscription) {
              channel.subscription.bind("pusher:subscription_succeeded", (data) => {
                console.log("Admin Chat: Channel subscription succeeded!", data);
              });
              channel.subscription.bind("pusher:subscription_error", (error) => {
                console.error("Admin Chat: Channel subscription failed!", error);
              });
              if (channel.subscription.state === "subscribed") {
                console.log("Admin Chat: Channel already subscribed!");
              } else {
                console.log("Admin Chat: Channel subscription state:", channel.subscription.state);
              }
            }
            channel.listen("NewChatMessage", (e) => {
              console.log("Admin Chat: Received chat message via direct Echo:", e);
              console.log("Admin Chat: Event structure:", {
                hasChatMessage: !!e.chatMessage,
                chatMessageType: typeof e.chatMessage,
                chatMessageContent: e.chatMessage
              });
              if (e && e.chatMessage && typeof e.chatMessage === "object") {
                console.log("Admin Chat: Calling pushMessage with:", e.chatMessage);
                this.pushMessage(e.chatMessage);
              } else {
                console.warn("Admin Chat: Invalid message structure:", e);
              }
            });
            channel.listen("TypingIndicator", (e) => {
              console.log("Admin Chat: Received typing indicator via direct Echo:", e);
              this.handleTypingIndicator(e);
            });
            console.log("Admin Chat: Channel subscription test - listening for any events");
            console.log("Admin Chat: Channel subscription status:", {
              hasSubscription: !!channel.subscription,
              subscriptionState: channel.subscription ? channel.subscription.state : "no subscription",
              hasPusher: !!(channel.subscription && channel.subscription.pusher)
            });
            setTimeout(() => {
              console.log("Admin Chat: Channel subscription test after 2 seconds:", {
                hasSubscription: !!channel.subscription,
                subscriptionState: channel.subscription ? channel.subscription.state : "no subscription"
              });
            }, 2e3);
            console.log("Admin Chat: Direct Echo listeners set up successfully");
          }
        } else {
          console.warn("Admin Chat: Echo not ready, retrying...");
          setTimeout(checkEcho, 500);
        }
      };
      checkEcho();
    },
    handleTypingIndicator(data) {
      if (data && data.user_id && data.user_id !== this.user.id) {
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
    // Enhanced UI Methods
    toggleSearch() {
      this.showSearch = !this.showSearch;
    },
    toggleSettings() {
      this.showSettings = !this.showSettings;
    },
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },
    toggleChatInfo() {
      this.showChatInfo = !this.showChatInfo;
    },
    toggleChatSettings() {
      this.showChatSettings = !this.showChatSettings;
    },
    toggleAttachmentMenu() {
      this.showAttachmentMenu = !this.showAttachmentMenu;
    },
    toggleEmojiPicker() {
      this.showEmojiPicker = !this.showEmojiPicker;
    },
    toggleConversationMenu(conversationId) {
      this.showConversationMenu = this.showConversationMenu === conversationId ? null : conversationId;
    },
    // Filter Methods
    setFilter(filter) {
      this.filter = filter;
    },
    clearSearch() {
      this.form.search = "";
      this.reset();
    },
    handleSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.$inertia.get(this.route("chat"), pickBy(this.form), { preserveState: true });
      }, 300);
    },
    // Typing Methods
    handleTyping() {
      this.isTyping = true;
      this.lastTypingTime = Date.now();
      if (this.typingTimeout) {
        clearTimeout(this.typingTimeout);
      }
      this.typingTimeout = setTimeout(() => {
        this.isTyping = false;
      }, 1e3);
      this.broadcastTypingIndicator(true);
    },
    handleFocus() {
    },
    handleBlur() {
      this.isTyping = false;
      this.broadcastTypingIndicator(false);
    },
    broadcastTypingIndicator(isTyping) {
      console.log("Admin Chat: Typing indicator disabled (direct Echo implementation)");
    },
    // Auto-resize textarea
    autoResizeTextarea() {
      const textarea = this.$refs.messageInput;
      if (textarea) {
        textarea.style.height = "auto";
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + "px";
      }
    },
    // Calculate unread count
    calculateUnreadCount() {
      if (this.conversations && this.conversations.data) {
        this.unreadCount = this.conversations.data.reduce((total, conversation) => {
          return total + (conversation.total_entry || 0);
        }, 0);
      }
    },
    // Check if error is expected and should be filtered
    isExpectedError(error) {
      if (!error) return true;
      const errorMessage = error.message || error.data && error.data.message || "";
      const errorType = error.type || "";
      const expectedErrors = [
        "PusherError",
        "Connection failed",
        "Authentication failed",
        "Authorization failed",
        "Invalid key",
        "Connection refused"
      ];
      return expectedErrors.some(
        (expected) => errorMessage.includes(expected) || errorType.includes(expected)
      );
    }
  },
  created() {
    this.moment = moment;
  },
  mounted() {
    console.log("Admin Chat: Vue component mounted!");
    this.setupEcho();
    this.scrollToBottom();
    this.$nextTick(() => {
      const textarea = this.$refs.messageInput;
      if (textarea) {
        textarea.addEventListener("input", this.autoResizeTextarea);
      }
    });
    this.calculateUnreadCount();
  },
  beforeUnmount() {
    if (window.Echo && this.chat) {
      console.log("Admin Chat: Leaving channel on unmount");
      window.Echo.leaveChannel(`chat.${this.chat.id}`);
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_User = resolveComponent("User");
  const _component_Camera = resolveComponent("Camera");
  const _component_MoreVertical = resolveComponent("MoreVertical");
  const _component_Search = resolveComponent("Search");
  const _component_X = resolveComponent("X");
  const _component_MessageCircle = resolveComponent("MessageCircle");
  const _component_Clock = resolveComponent("Clock");
  const _component_Archive = resolveComponent("Archive");
  const _component_Pin = resolveComponent("Pin");
  const _component_pagination = resolveComponent("pagination");
  const _component_Link = resolveComponent("Link");
  const _component_Plus = resolveComponent("Plus");
  const _component_Info = resolveComponent("Info");
  const _component_Settings = resolveComponent("Settings");
  const _component_Trash2 = resolveComponent("Trash2");
  const _component_Check = resolveComponent("Check");
  const _component_Paperclip = resolveComponent("Paperclip");
  const _component_Smile = resolveComponent("Smile");
  const _component_Send = resolveComponent("Send");
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900" }, _attrs))} data-v-dad648b3>`);
  _push(ssrRenderComponent(_component_Head, {
    title: _ctx.$t($props.title)
  }, null, _parent));
  _push(`<div class="flex max-h-[calc(100vh-215px)]" data-v-dad648b3><div class="w-1/3 bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border-r border-slate-200/50 dark:border-slate-700/50 flex flex-col shadow-lg" data-v-dad648b3><div class="p-4 border-b border-slate-200/50 dark:border-slate-700/50 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 dark:from-slate-700/50 dark:to-slate-600/50" data-v-dad648b3><div class="flex items-center gap-3" data-v-dad648b3><div class="relative group" data-v-dad648b3>`);
  if ($data.user.photo) {
    _push(`<img${ssrRenderAttr("src", $data.user.photo)} alt="" class="w-12 h-12 rounded-full object-cover ring-2 ring-white dark:ring-slate-700 shadow-lg" data-v-dad648b3>`);
  } else {
    _push(`<div class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-700 shadow-lg" data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_User, { class: "w-6 h-6 text-white" }, null, _parent));
    _push(`</div>`);
  }
  _push(`<div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full shadow-sm" data-v-dad648b3><div class="w-full h-full bg-green-400 rounded-full animate-pulse" data-v-dad648b3></div></div><div class="absolute inset-0 rounded-full bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center" data-v-dad648b3>`);
  _push(ssrRenderComponent(_component_Camera, { class: "w-4 h-4 text-white" }, null, _parent));
  _push(`</div></div><div class="flex-1 min-w-0" data-v-dad648b3><div class="font-semibold text-slate-900 dark:text-white truncate" data-v-dad648b3>${ssrInterpolate($data.user.first_name)} ${ssrInterpolate($data.user.last_name)}</div><div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1" data-v-dad648b3><div class="w-2 h-2 bg-green-500 rounded-full" data-v-dad648b3></div> ${ssrInterpolate(_ctx.$t($data.user.role.name))}</div></div><button class="p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200" data-v-dad648b3>`);
  _push(ssrRenderComponent(_component_MoreVertical, { class: "w-4 h-4" }, null, _parent));
  _push(`</button></div></div><div class="p-4 border-b border-slate-200/50 dark:border-slate-700/50" data-v-dad648b3><div class="space-y-3" data-v-dad648b3><div class="relative" data-v-dad648b3>`);
  _push(ssrRenderComponent(_component_Search, { class: "absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" }, null, _parent));
  _push(`<input${ssrRenderAttr("value", $data.form.search)} type="text"${ssrRenderAttr("placeholder", _ctx.$t("Search conversations..."))} class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200" data-v-dad648b3>`);
  if ($data.form.search) {
    _push(`<button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_X, { class: "w-4 h-4" }, null, _parent));
    _push(`</button>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="flex gap-2" data-v-dad648b3><button class="${ssrRenderClass([
    "px-3 py-1 text-xs rounded-full transition-all duration-200",
    $data.filter === "all" ? "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300" : "bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600"
  ])}" data-v-dad648b3>${ssrInterpolate(_ctx.$t("All"))}</button><button class="${ssrRenderClass([
    "px-3 py-1 text-xs rounded-full transition-all duration-200",
    $data.filter === "unread" ? "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300" : "bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600"
  ])}" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Unread"))}</button><button class="${ssrRenderClass([
    "px-3 py-1 text-xs rounded-full transition-all duration-200",
    $data.filter === "archived" ? "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300" : "bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600"
  ])}" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Archived"))}</button></div></div></div><div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent" data-v-dad648b3>`);
  if ($props.conversations.data.length === 0) {
    _push(`<div class="p-8 text-center" data-v-dad648b3><div class="w-16 h-16 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mx-auto mb-4" data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_MessageCircle, { class: "w-8 h-8 text-slate-400 dark:text-slate-500" }, null, _parent));
    _push(`</div><p class="text-slate-500 dark:text-slate-400 font-medium" data-v-dad648b3>${ssrInterpolate(_ctx.$t("No conversations yet"))}</p><p class="text-sm text-slate-400 dark:text-slate-500 mt-1" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Start a new conversation"))}</p></div>`);
  } else {
    _push(`<div class="space-y-1 p-2" data-v-dad648b3><!--[-->`);
    ssrRenderList($props.conversations.data, (conversation) => {
      _push(`<div class="${ssrRenderClass([
        "group flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-all duration-300 transform hover:scale-[1.02]",
        $props.chat && $props.chat.id === conversation.id ? "bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 shadow-md" : "hover:bg-slate-50 dark:hover:bg-slate-700 hover:shadow-sm"
      ])}" data-v-dad648b3><div class="relative" data-v-dad648b3><div class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center shadow-lg" data-v-dad648b3>`);
      _push(ssrRenderComponent(_component_MessageCircle, { class: "w-6 h-6 text-white" }, null, _parent));
      _push(`</div>`);
      if (conversation.total_entry > 0) {
        _push(`<div class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold animate-pulse shadow-lg" data-v-dad648b3>${ssrInterpolate(conversation.total_entry > 99 ? "99+" : conversation.total_entry)}</div>`);
      } else {
        _push(`<!---->`);
      }
      if (conversation.is_typing) {
        _push(`<div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center" data-v-dad648b3><div class="w-2 h-2 bg-white rounded-full animate-pulse" data-v-dad648b3></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex-1 min-w-0" data-v-dad648b3><div class="flex items-center justify-between mb-1" data-v-dad648b3><div class="font-semibold text-slate-900 dark:text-white truncate" data-v-dad648b3>${ssrInterpolate(conversation.title || "New Conversation")}</div><div class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1" data-v-dad648b3>`);
      _push(ssrRenderComponent(_component_Clock, { class: "w-3 h-3" }, null, _parent));
      _push(` ${ssrInterpolate($options.formatTime(conversation.updated_at))}</div></div><div class="flex items-center justify-between" data-v-dad648b3><div class="text-sm text-slate-500 dark:text-slate-400 truncate flex items-center gap-2" data-v-dad648b3>`);
      _push(ssrRenderComponent(_component_User, { class: "w-3 h-3" }, null, _parent));
      _push(` ${ssrInterpolate(conversation.creator || "Unknown")}</div>`);
      if (conversation.is_archived) {
        _push(`<div class="text-xs text-yellow-600 dark:text-yellow-400 flex items-center gap-1" data-v-dad648b3>`);
        _push(ssrRenderComponent(_component_Archive, { class: "w-3 h-3" }, null, _parent));
        _push(` ${ssrInterpolate(_ctx.$t("Archived"))}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (conversation.last_message) {
        _push(`<div class="text-xs text-slate-400 dark:text-slate-500 truncate mt-1" data-v-dad648b3>${ssrInterpolate(conversation.last_message)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex flex-col items-end gap-1" data-v-dad648b3><button class="p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 opacity-0 group-hover:opacity-100 transition-all duration-200" data-v-dad648b3>`);
      _push(ssrRenderComponent(_component_MoreVertical, { class: "w-4 h-4" }, null, _parent));
      _push(`</button>`);
      if (conversation.is_pinned) {
        _push(`<div class="text-yellow-500" data-v-dad648b3>`);
        _push(ssrRenderComponent(_component_Pin, { class: "w-3 h-3" }, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    });
    _push(`<!--]--></div>`);
  }
  _push(`</div>`);
  if ($props.conversations.links) {
    _push(`<div class="p-4 border-t border-slate-200 dark:border-slate-700" data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_pagination, {
      class: "mt-4",
      links: $props.conversations.links
    }, null, _parent));
    _push(`</div>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="flex-1 flex flex-col bg-white/50 dark:bg-slate-800/50" data-v-dad648b3>`);
  if (!$props.chat) {
    _push(`<div class="flex-1 flex items-center justify-center" data-v-dad648b3><div class="text-center max-w-md mx-auto px-6" data-v-dad648b3><div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mx-auto mb-6" data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_MessageCircle, { class: "w-10 h-10 text-slate-400 dark:text-slate-500" }, null, _parent));
    _push(`</div><h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Select a conversation"))}</h3><p class="text-slate-500 dark:text-slate-400 mb-6" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Choose a conversation from the sidebar to start chatting"))}</p>`);
    _push(ssrRenderComponent(_component_Link, {
      href: _ctx.route("chat.create"),
      class: "inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
    }, {
      default: withCtx((_, _push2, _parent2, _scopeId) => {
        if (_push2) {
          _push2(ssrRenderComponent(_component_Plus, { class: "w-5 h-5" }, null, _parent2, _scopeId));
          _push2(`<span data-v-dad648b3${_scopeId}>${ssrInterpolate(_ctx.$t("Start New Chat"))}</span>`);
        } else {
          return [
            createVNode(_component_Plus, { class: "w-5 h-5" }),
            createVNode("span", null, toDisplayString(_ctx.$t("Start New Chat")), 1)
          ];
        }
      }),
      _: 1
    }, _parent));
    _push(`</div></div>`);
  } else {
    _push(`<div class="flex-1 flex flex-col" data-v-dad648b3><div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50 p-4 shadow-sm" data-v-dad648b3><div class="flex items-center justify-between" data-v-dad648b3><div class="flex items-center gap-3" data-v-dad648b3><div class="relative" data-v-dad648b3><div class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center shadow-lg" data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_MessageCircle, { class: "w-6 h-6 text-white" }, null, _parent));
    _push(`</div><div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full" data-v-dad648b3><div class="w-full h-full bg-green-400 rounded-full animate-pulse" data-v-dad648b3></div></div></div><div data-v-dad648b3><h3 class="font-semibold text-slate-900 dark:text-white" data-v-dad648b3>${ssrInterpolate($props.chat.title || "New Conversation")}</h3><div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1" data-v-dad648b3><div class="w-2 h-2 bg-green-500 rounded-full" data-v-dad648b3></div><span data-v-dad648b3>${ssrInterpolate(_ctx.$t("Active now"))}</span></div></div></div><div class="flex items-center gap-2" data-v-dad648b3>`);
    if ($data.isTyping) {
      _push(`<div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400" data-v-dad648b3><div class="flex gap-1" data-v-dad648b3><div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" data-v-dad648b3></div><div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="${ssrRenderStyle({ "animation-delay": "0.1s" })}" data-v-dad648b3></div><div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="${ssrRenderStyle({ "animation-delay": "0.2s" })}" data-v-dad648b3></div></div><span data-v-dad648b3>${ssrInterpolate(_ctx.$t("Typing..."))}</span></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="flex items-center gap-1" data-v-dad648b3><button class="p-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"${ssrRenderAttr("title", _ctx.$t("Chat info"))} data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_Info, { class: "w-4 h-4" }, null, _parent));
    _push(`</button><button class="p-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"${ssrRenderAttr("title", _ctx.$t("Chat settings"))} data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_Settings, { class: "w-4 h-4" }, null, _parent));
    _push(`</button><button class="p-2 text-slate-400 hover:text-red-500 transition-colors duration-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"${ssrRenderAttr("title", _ctx.$t("Delete conversation"))} data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_Trash2, { class: "w-4 h-4" }, null, _parent));
    _push(`</button></div></div></div></div><div class="flex-1 overflow-y-auto p-6 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-600 scrollbar-track-transparent" data-v-dad648b3>`);
    if ($props.chat.messages && $props.chat.messages.length === 0) {
      _push(`<div class="text-center py-12" data-v-dad648b3><div class="w-16 h-16 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center mx-auto mb-6" data-v-dad648b3>`);
      _push(ssrRenderComponent(_component_MessageCircle, { class: "w-8 h-8 text-slate-400 dark:text-slate-500" }, null, _parent));
      _push(`</div><p class="text-slate-500 dark:text-slate-400 font-medium" data-v-dad648b3>${ssrInterpolate(_ctx.$t("No messages yet"))}</p><p class="text-sm text-slate-400 dark:text-slate-500 mt-1" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Start the conversation"))}</p></div>`);
    } else {
      _push(`<div class="max-h-[410px] overflow-y-auto" data-v-dad648b3><!--[-->`);
      ssrRenderList($props.chat.messages, (message, index) => {
        _push(`<div class="${ssrRenderClass([
          "flex gap-3 group",
          message.user_id === $data.user.id ? "justify-end" : "justify-start"
        ])}" data-v-dad648b3>`);
        if (message.user_id !== $data.user.id) {
          _push(`<div class="w-10 h-10 bg-gradient-to-br from-slate-400 to-slate-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg" data-v-dad648b3>`);
          _push(ssrRenderComponent(_component_User, { class: "w-5 h-5 text-white" }, null, _parent));
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="${ssrRenderClass([
          "max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm transition-all duration-200 group-hover:shadow-md",
          message.user_id === $data.user.id ? "bg-gradient-to-r from-blue-500 to-blue-600 text-white" : "bg-white dark:bg-slate-700 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-600"
        ])}" data-v-dad648b3><div class="text-sm leading-relaxed" data-v-dad648b3>${$options.formatMessage(message.message) ?? ""}</div><div class="flex items-center justify-between mt-2" data-v-dad648b3><div class="${ssrRenderClass([
          "text-xs flex items-center gap-1",
          message.user_id === $data.user.id ? "text-blue-100" : "text-slate-400 dark:text-slate-500"
        ])}" data-v-dad648b3>`);
        _push(ssrRenderComponent(_component_Clock, { class: "w-3 h-3" }, null, _parent));
        _push(` ${ssrInterpolate($options.formatTime(message.created_at))}</div>`);
        if (message.user_id === $data.user.id) {
          _push(`<div class="flex items-center gap-1" data-v-dad648b3>`);
          if (message.is_delivered) {
            _push(`<div class="w-3 h-3 text-blue-200" data-v-dad648b3>`);
            _push(ssrRenderComponent(_component_Check, { class: "w-3 h-3" }, null, _parent));
            _push(`</div>`);
          } else {
            _push(`<div class="w-3 h-3 text-blue-200" data-v-dad648b3>`);
            _push(ssrRenderComponent(_component_Clock, { class: "w-3 h-3" }, null, _parent));
            _push(`</div>`);
          }
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
        if (message.user_id === $data.user.id) {
          _push(`<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg" data-v-dad648b3>`);
          _push(ssrRenderComponent(_component_User, { class: "w-5 h-5 text-white" }, null, _parent));
          _push(`</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--></div>`);
    }
    _push(`</div><div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border-t border-slate-200/50 dark:border-slate-700/50 p-4 shadow-lg" data-v-dad648b3><div class="space-y-3" data-v-dad648b3>`);
    if ($data.isTyping) {
      _push(`<div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 px-2" data-v-dad648b3><div class="flex gap-1" data-v-dad648b3><div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" data-v-dad648b3></div><div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="${ssrRenderStyle({ "animation-delay": "0.1s" })}" data-v-dad648b3></div><div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="${ssrRenderStyle({ "animation-delay": "0.2s" })}" data-v-dad648b3></div></div><span data-v-dad648b3>${ssrInterpolate(_ctx.$t("Someone is typing..."))}</span></div>`);
    } else {
      _push(`<!---->`);
    }
    _push(`<div class="flex gap-3" data-v-dad648b3><button class="p-3 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"${ssrRenderAttr("title", _ctx.$t("Attach file"))} data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_Paperclip, { class: "w-5 h-5" }, null, _parent));
    _push(`</button><div class="flex-1 relative" data-v-dad648b3><textarea${ssrRenderAttr("placeholder", _ctx.$t("Type a message..."))} class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200 resize-none shadow-sm" rows="1" data-v-dad648b3>${ssrInterpolate($data.message)}</textarea><button class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200"${ssrRenderAttr("title", _ctx.$t("Add emoji"))} data-v-dad648b3>`);
    _push(ssrRenderComponent(_component_Smile, { class: "w-4 h-4" }, null, _parent));
    _push(`</button></div><button${ssrIncludeBooleanAttr(!$data.message.trim() || $data.isSending) ? " disabled" : ""} class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:transform-none" data-v-dad648b3>`);
    if ($data.isSending) {
      _push(`<div class="flex items-center gap-2" data-v-dad648b3><div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" data-v-dad648b3></div><span class="hidden sm:inline" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Sending..."))}</span></div>`);
    } else {
      _push(`<div class="flex items-center gap-2" data-v-dad648b3>`);
      _push(ssrRenderComponent(_component_Send, { class: "w-4 h-4" }, null, _parent));
      _push(`<span class="hidden sm:inline" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Send"))}</span></div>`);
    }
    _push(`</button></div><div class="flex justify-between items-center text-xs text-slate-400 dark:text-slate-500 px-2" data-v-dad648b3><div class="flex items-center gap-4" data-v-dad648b3><span data-v-dad648b3>${ssrInterpolate($data.message.length)}/1000</span>`);
    if ($data.message.length > 800) {
      _push(`<span class="text-yellow-500" data-v-dad648b3>${ssrInterpolate(_ctx.$t("Message getting long"))}</span>`);
    } else {
      _push(`<!---->`);
    }
    _push(`</div><div class="flex items-center gap-2" data-v-dad648b3><kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs" data-v-dad648b3>Enter</kbd><span data-v-dad648b3>${ssrInterpolate(_ctx.$t("to send"))}</span><kbd class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-xs" data-v-dad648b3>Shift+Enter</kbd><span data-v-dad648b3>${ssrInterpolate(_ctx.$t("for new line"))}</span></div></div></div></div></div>`);
  }
  _push(`</div></div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Chat/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender], ["__scopeId", "data-v-dad648b3"]]);
export {
  Index as default
};
