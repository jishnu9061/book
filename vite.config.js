import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader';
import i18n from 'laravel-vue-i18n/vite';
import { nodePolyfills } from 'vite-plugin-node-polyfills';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5173,
        https: false, // Use HTTP for local development
        hmr: {
            host: '127.0.0.1',
            port: 5173,
            protocol: 'ws', // Use WebSocket instead of WSS
        },
        watch: {
            ignored: [
                '**/public/build/**',
                '**/node_modules/**',
                '**/storage/**',
                '**/vendor/**',
                '**/*.log'
            ]
        }
    },
    plugins: [
        nodePolyfills({
            // optional settings
            protocolImports: true,
        }),
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        svgLoader(),
        i18n(),
    ],
    optimizeDeps: {
        include: [
            '@inertiajs/vue3',
            'vue',
            'axios',
            'moment',
            'lodash',
            'pusher-js',
            'laravel-echo',
            'vue-css-donut-chart',
            'lucide-vue-next',
            'vue-sweetalert2',
            'qalendar',
            '@vuepic/vue-datepicker',
            'sanitize-html',
            'uuid',
            'source-map-js'
        ],
        esbuildOptions: {
            define: {
                global: 'globalThis',
            }
        }
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            'source-map': 'source-map-js',
        }
    },
    build: {
        commonjsOptions: {
            transformMixedEsModules: true,
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', '@inertiajs/vue3'],
                    ui: ['lucide-vue-next', 'vue-sweetalert2', 'qalendar'],
                    utils: ['moment', 'lodash', 'axios', 'sanitize-html', 'uuid']
                }
            }
        }
    },
    define: {
        global: 'globalThis',
    },
});
