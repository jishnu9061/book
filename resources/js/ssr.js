import { createSSRApp, h } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { i18nVue } from 'laravel-vue-i18n'

const appName = import.meta.env.VITE_APP_NAME || 'HelpDesk'

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ App, props, plugin }) {
            return createSSRApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, {
                    ...page.props.ziggy,
                    location: new URL(page.props.ziggy.location),
                })
                .use(i18nVue, {
                    lang: page.props.locale || 'en',
                    resolve: lang => {
                        try {
                            const langs = import.meta.glob('../../lang/*.json', { eager: true });
                            const langFile = langs[`../../lang/${lang}.json`];
                            if (langFile && langFile.default) {
                                return langFile.default;
                            }
                            // Fallback to English
                            const fallbackFile = langs['../../lang/en.json'];
                            return fallbackFile && fallbackFile.default ? fallbackFile.default : {};
                        } catch (error) {
                            console.warn(`Failed to load language ${lang} in SSR:`, error);
                            return {};
                        }
                    },
                });
        },
    })
)
