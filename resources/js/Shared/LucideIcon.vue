<template>
  <component :is="iconComponent" :class="iconClass" />
</template>

<script>
import { defineAsyncComponent } from 'vue';

export default {
  name: 'LucideIcon',
  props: {
    name: {
      type: String,
      required: true
    },
    class: {
      type: String,
      default: ''
    }
  },
  computed: {
    iconComponent() {
      const iconName = this.name.charAt(0).toUpperCase() + this.name.slice(1);
      try {
        return defineAsyncComponent(() =>
          import(`lucide-vue-next`).then((module) => {
            if (module[iconName]) {
              return module[iconName];
            }
            // Fallback to HelpCircle if icon not found
            return module['HelpCircle'] || module['HelpCircle'];
          }).catch(() => {
            // Ultimate fallback - return a simple div component
            return {
              template: '<div class="w-5 h-5 bg-gray-300 rounded"></div>'
            };
          })
        );
      } catch (error) {
        console.warn(`Failed to load icon: ${iconName}`, error);
        return {
          template: '<div class="w-5 h-5 bg-gray-300 rounded"></div>'
        };
      }
    },
    iconClass() {
      return this.class || 'w-5 h-5'
    }
  }
}
</script>