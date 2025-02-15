import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Navbar from './Components/Navbar.vue';
import Sidebar from './Components/Sidebar.vue';
import Modal from './Components/Modal.vue';
import '@fortawesome/fontawesome-free/css/all.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Criando a aplicação Inertia
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(App, props),
            components: { Navbar, Modal, Sidebar },
        });

        app.use(plugin);
        app.use(ZiggyVue, Ziggy);
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
