import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { createI18n } from 'vue-i18n';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';
import { initializeLocale } from './composables/useLocale';
import en from './locales/en.json';
import ptBR from './locales/pt-BR.json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const savedLocale =
    (typeof window !== 'undefined'
        ? localStorage.getItem('locale')
        : null) ?? 'pt-BR';

const i18n = createI18n({
    legacy: false,
    locale: savedLocale,
    fallbackLocale: 'pt-BR',
    messages: {
        en,
        'pt-BR': ptBR,
    },
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Initialize theme and locale on page load
initializeTheme();
initializeLocale();
