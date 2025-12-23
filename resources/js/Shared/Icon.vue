<template>
  <CountryFlag v-if="isFlagIcon" :code="name" :iconClass="iconClass" />
  <LucideIcon v-else :name="mappedIconName" :class="iconClass" />
</template>

<script>
import LucideIcon from '@/Shared/LucideIcon.vue';
import CountryFlag from '@/Shared/CountryFlag.vue';

export default {
  components: {
    LucideIcon,
    CountryFlag,
  },
  props: {
    name: {
      type: String,
      required: true,
    },
    iconClass: {
      type: String,
      default: '',
    },
  },
  computed: {
    isFlagIcon() {
      const flagCodes = ['en', 'de', 'fr', 'es', 'it', 'nl', 'cn', 'bd', 'sa', 'tr', 'se', 'pt', 'he', 'lt', 'pl', 'ur'];
      return flagCodes.includes(this.name.toLowerCase());
    },
    mappedIconName() {
      return this.mapLegacyIcon(this.name);
    },
  },
  methods: {
    mapLegacyIcon(iconName) {
      const iconMap = {
        // Core navigation and UI
        'apple': 'Apple',
        'book': 'Book',
        'cheveron-down': 'ChevronDown',
        'cheveron-right': 'ChevronRight',
        'location': 'MapPin',
        'office': 'Building',
        'shopping-cart': 'ShoppingCart',
        'store-front': 'Store',
        'trash': 'Trash2',
        
        // Ticket system icons
        'service': 'LifeBuoy',
        'category': 'Tag',
        'status': 'CheckCircle',
        'ticket': 'Ticket',
        'contact': 'Users',
        'faq': 'HelpCircle',
        'chat': 'MessageSquare',
        'knowledge': 'BookOpen',
        'home': 'Home',
        'clock': 'Clock',
        'settings': 'Settings',
        'dashboard': 'LayoutDashboard',
        'edit': 'Edit',
        'up': 'ArrowUp',
        'down': 'ArrowDown',
        'tick': 'Check',
        'cross': 'X',
        'close': 'X',
        'file': 'FileText',
        'users': 'Users',
        'all_users': 'Users',
        'types': 'List',
        'notes': 'Notebook',
        'plus': 'Plus',
        'dash': 'Minus',
        'check': 'Check',
        'post': 'FileText',
        'no_image': 'ImageOff',
        'to_up': 'ArrowUpToLine',
        'angle-up': 'ChevronUp',
        'gear': 'Settings',
        'phone': 'Phone',
        'email': 'Mail',
        'user': 'User',
        'security': 'Shield',
        'airplay': 'Airplay',
        'compass': 'Compass',
        'aperture': 'Aperture',
        'camera': 'Camera',
        'palette': 'Palette',
        'login': 'LogIn',
        'page': 'File',
        'send': 'Send',
        'send_plan': 'Send',
        'user_role': 'UserCog',
        'global_setting': 'Globe',
        'image': 'Image',
        'notification': 'Bell',
        
        // Language flags (using generic flag icon)
        'en': 'Flag',
        'de': 'Flag',
        'fr': 'Flag',
        'es': 'Flag',
        'it': 'Flag',
        'nl': 'Flag',
        'bd': 'Flag',
        'pl': 'Flag',
        'pt': 'Flag',
        'sa': 'Flag',
        'se': 'Flag',
        'tr': 'Flag',
        'ur': 'Flag',
        'lt': 'Flag',
        
        // Additional icons
        'archive': 'Archive',
        'form-builder': 'FormInput',
        'email_template': 'MailOpen',
        'pending_users': 'UserMinus',
      };
      
      return iconMap[iconName] || iconName.charAt(0).toUpperCase() + iconName.slice(1);
    },
  },
};
</script>