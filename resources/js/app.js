import './bootstrap'
import '../css/app.scss'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { i18nVue } from 'laravel-vue-i18n'

// VueQuill configuration is handled in VueQuillConfig.js

import Donut from 'vue-css-donut-chart'
import 'vue-css-donut-chart/dist/vcdonut.css'


// Configure Inertia.js with CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

createInertiaApp({
    title: title => title ? `${title}` : '',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })

        // Add global error handler
        app.config.errorHandler = (err, vm, info) => {
            console.log(err)
            console.error('Vue Error:', err, info)
            // Don't let i18n errors crash the app
            if (err.message && err.message.includes('replace')) {
                console.warn('i18n error caught and handled:', err.message)
                console.warn('This usually means a translation key is missing or undefined')
                return
            }
        }

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(i18nVue, {
                lang: props.initialPage.props.locale || 'en',
                resolve: async lang => {
                    try {
                        const langs = import.meta.glob('../../lang/*.json')
                        const langFile = langs[`../../lang/${lang}.json`]
                        if (langFile) {
                            const data = await langFile()
                            // Ensure the data is properly formatted
                            return data && typeof data === 'object' ? data : {}
                        }
                        // Fallback to English if language not found
                        const fallbackData = await langs['../../lang/en.json']()
                        return fallbackData && typeof fallbackData === 'object' ? fallbackData : {}
                    } catch (error) {
                        console.warn(`Failed to load language ${lang}:`, error)
                        // Return empty object as fallback
                        return {}
                    }
                },
            })
            .use(Donut)
            .mount(el)
    },
    progress: {
        delay: 100,
        color: '#3c4858',
        includeCSS: true,
        showSpinner: true,
    },
})
