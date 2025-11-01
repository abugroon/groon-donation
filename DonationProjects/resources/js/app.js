import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import AppLayout from './Layouts/AppLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'DonationProjects';

document.addEventListener('DOMContentLoaded', () => {
    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => {
            return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')).then((page) => {
                page.default.layout = page.default.layout || AppLayout;
                return page;
            });
        },
        setup({ el, App, props, plugin }) {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .mount(el);
        },
    });
});
