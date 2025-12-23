<template>
  <svg 
    :class="iconClass" 
    viewBox="0 0 24 16" 
    xmlns="http://www.w3.org/2000/svg"
    :width="size"
    :height="size * 0.67"
  >
    <g v-if="flagSvg" v-html="flagSvg"></g>
    <g v-else>
      <!-- Fallback flag icon -->
      <rect width="24" height="16" fill="#e5e7eb" stroke="#9ca3af" stroke-width="0.5"/>
      <text x="12" y="10" text-anchor="middle" font-size="8" fill="#6b7280">?</text>
    </g>
  </svg>
</template>

<script>
export default {
  name: 'CountryFlag',
  props: {
    code: {
      type: String,
      required: true,
    },
    iconClass: {
      type: String,
      default: 'w-6 h-4',
    },
    size: {
      type: Number,
      default: 24,
    },
  },
  computed: {
    flagSvg() {
      return this.getFlagSvg(this.code);
    },
  },
  methods: {
    getFlagSvg(code) {
      const flags = {
        // English (US Flag)
        'en': `
          <rect width="24" height="16" fill="#B22234"/>
          <rect y="1.23" width="24" height="1.23" fill="#FFFFFF"/>
          <rect y="3.69" width="24" height="1.23" fill="#FFFFFF"/>
          <rect y="6.15" width="24" height="1.23" fill="#FFFFFF"/>
          <rect y="8.62" width="24" height="1.23" fill="#FFFFFF"/>
          <rect y="11.08" width="24" height="1.23" fill="#FFFFFF"/>
          <rect y="13.54" width="24" height="1.23" fill="#FFFFFF"/>
          <rect width="9.6" height="8.62" fill="#3C3B6E"/>
          <g fill="#FFFFFF">
            <circle cx="1.6" cy="1.6" r="0.4"/>
            <circle cx="3.2" cy="1.6" r="0.4"/>
            <circle cx="4.8" cy="1.6" r="0.4"/>
            <circle cx="6.4" cy="1.6" r="0.4"/>
            <circle cx="8" cy="1.6" r="0.4"/>
            <circle cx="2.4" cy="2.4" r="0.4"/>
            <circle cx="4" cy="2.4" r="0.4"/>
            <circle cx="5.6" cy="2.4" r="0.4"/>
            <circle cx="7.2" cy="2.4" r="0.4"/>
            <circle cx="1.6" cy="3.2" r="0.4"/>
            <circle cx="3.2" cy="3.2" r="0.4"/>
            <circle cx="4.8" cy="3.2" r="0.4"/>
            <circle cx="6.4" cy="3.2" r="0.4"/>
            <circle cx="8" cy="3.2" r="0.4"/>
            <circle cx="2.4" cy="4" r="0.4"/>
            <circle cx="4" cy="4" r="0.4"/>
            <circle cx="5.6" cy="4" r="0.4"/>
            <circle cx="7.2" cy="4" r="0.4"/>
            <circle cx="1.6" cy="4.8" r="0.4"/>
            <circle cx="3.2" cy="4.8" r="0.4"/>
            <circle cx="4.8" cy="4.8" r="0.4"/>
            <circle cx="6.4" cy="4.8" r="0.4"/>
            <circle cx="8" cy="4.8" r="0.4"/>
            <circle cx="2.4" cy="5.6" r="0.4"/>
            <circle cx="4" cy="5.6" r="0.4"/>
            <circle cx="5.6" cy="5.6" r="0.4"/>
            <circle cx="7.2" cy="5.6" r="0.4"/>
            <circle cx="1.6" cy="6.4" r="0.4"/>
            <circle cx="3.2" cy="6.4" r="0.4"/>
            <circle cx="4.8" cy="6.4" r="0.4"/>
            <circle cx="6.4" cy="6.4" r="0.4"/>
            <circle cx="8" cy="6.4" r="0.4"/>
            <circle cx="2.4" cy="7.2" r="0.4"/>
            <circle cx="4" cy="7.2" r="0.4"/>
            <circle cx="5.6" cy="7.2" r="0.4"/>
            <circle cx="7.2" cy="7.2" r="0.4"/>
            <circle cx="1.6" cy="8" r="0.4"/>
            <circle cx="3.2" cy="8" r="0.4"/>
            <circle cx="4.8" cy="8" r="0.4"/>
            <circle cx="6.4" cy="8" r="0.4"/>
            <circle cx="8" cy="8" r="0.4"/>
          </g>
        `,
        
        // German Flag
        'de': `
          <rect width="24" height="5.33" fill="#000000"/>
          <rect y="5.33" width="24" height="5.33" fill="#DD0000"/>
          <rect y="10.67" width="24" height="5.33" fill="#FFCE00"/>
        `,
        
        // French Flag
        'fr': `
          <rect width="8" height="16" fill="#002395"/>
          <rect x="8" width="8" height="16" fill="#FFFFFF"/>
          <rect x="16" width="8" height="16" fill="#ED2939"/>
        `,
        
        // Spanish Flag
        'es': `
          <rect width="24" height="4" fill="#C60B1E"/>
          <rect y="4" width="24" height="8" fill="#FFC400"/>
          <rect y="12" width="24" height="4" fill="#C60B1E"/>
          <rect x="2" y="6" width="4" height="4" fill="#C60B1E"/>
          <rect x="2.5" y="6.5" width="3" height="3" fill="#FFC400"/>
        `,
        
        // Italian Flag
        'it': `
          <rect width="8" height="16" fill="#009246"/>
          <rect x="8" width="8" height="16" fill="#FFFFFF"/>
          <rect x="16" width="8" height="16" fill="#CE2B37"/>
        `,
        
        // Dutch Flag
        'nl': `
          <rect width="24" height="5.33" fill="#AE1C28"/>
          <rect y="5.33" width="24" height="5.33" fill="#FFFFFF"/>
          <rect y="10.67" width="24" height="5.33" fill="#21468B"/>
        `,
        
        // Chinese Flag
        'cn': `
          <rect width="24" height="16" fill="#DE2910"/>
          <g fill="#FFDE00">
            <polygon points="4,3 5.5,7 9.5,7 6.5,9.5 8,13.5 4,11 0,13.5 1.5,9.5 -1.5,7 2.5,7"/>
            <circle cx="6" cy="2" r="0.5"/>
            <circle cx="8" cy="3" r="0.3"/>
            <circle cx="9" cy="5" r="0.3"/>
            <circle cx="8" cy="7" r="0.3"/>
            <circle cx="6" cy="8" r="0.3"/>
            <circle cx="4" cy="7" r="0.3"/>
            <circle cx="3" cy="5" r="0.3"/>
            <circle cx="4" cy="3" r="0.3"/>
          </g>
        `,
        
        // Bengali Flag
        'bd': `
          <rect width="24" height="16" fill="#006A4E"/>
          <circle cx="9" cy="8" r="4" fill="#F42A41"/>
        `,
        
        // Arabic (Saudi Arabia) Flag
        'sa': `
          <rect width="24" height="16" fill="#006C35"/>
          <g fill="#FFFFFF">
            <path d="M2,8 L22,8 L22,9 L2,9 Z"/>
            <path d="M2,7 L22,7 L22,8 L2,8 Z"/>
            <path d="M2,9 L22,9 L22,10 L2,10 Z"/>
            <path d="M2,10 L22,10 L22,11 L2,11 Z"/>
          </g>
          <g fill="#FFFFFF">
            <path d="M8,4 L9,4 L9,12 L8,12 Z"/>
            <path d="M7,5 L8,5 L8,11 L7,11 Z"/>
            <path d="M9,5 L10,5 L10,11 L9,11 Z"/>
            <path d="M6,6 L7,6 L7,10 L6,10 Z"/>
            <path d="M10,6 L11,6 L11,10 L10,10 Z"/>
          </g>
        `,
        
        // Turkish Flag
        'tr': `
          <rect width="24" height="16" fill="#E30A17"/>
          <g fill="#FFFFFF">
            <circle cx="8" cy="8" r="3"/>
            <circle cx="8" cy="8" r="2" fill="#E30A17"/>
            <polygon points="8,6 9,7 8,8 7,7"/>
          </g>
        `,
        
        // Swedish Flag
        'se': `
          <rect width="24" height="16" fill="#006AA7"/>
          <rect x="7" width="2" height="16" fill="#FECC00"/>
          <rect y="7" width="24" height="2" fill="#FECC00"/>
        `,
        
        // Portuguese Flag
        'pt': `
          <rect width="12" height="16" fill="#046A38"/>
          <rect x="12" width="12" height="16" fill="#DA020E"/>
          <g fill="#FFD100">
            <circle cx="6" cy="8" r="2"/>
            <rect x="5" y="7" width="2" height="2"/>
          </g>
        `,
        
        // Hebrew (Israel) Flag
        'he': `
          <rect width="24" height="16" fill="#FFFFFF"/>
          <rect y="7" width="24" height="2" fill="#0038B8"/>
          <rect y="7" width="24" height="2" fill="#0038B8"/>
          <g fill="#0038B8">
            <polygon points="12,4 10,8 14,8"/>
            <polygon points="12,12 10,8 14,8"/>
          </g>
        `,
        
        // Lithuanian Flag
        'lt': `
          <rect width="24" height="5.33" fill="#FDB913"/>
          <rect y="5.33" width="24" height="5.33" fill="#006A44"/>
          <rect y="10.67" width="24" height="5.33" fill="#C1272D"/>
        `,
        
        // Polish Flag
        'pl': `
          <rect width="24" height="8" fill="#FFFFFF"/>
          <rect y="8" width="24" height="8" fill="#DC143C"/>
        `,
        
        // Urdu (Pakistan) Flag
        'ur': `
          <rect width="24" height="16" fill="#01411C"/>
          <rect width="8" height="16" fill="#FFFFFF"/>
          <g fill="#FFFFFF">
            <circle cx="4" cy="8" r="2"/>
            <polygon points="4,6 5,7 4,8 3,7"/>
          </g>
        `,
      };
      
      return flags[code.toLowerCase()] || null;
    },
  },
};
</script>
