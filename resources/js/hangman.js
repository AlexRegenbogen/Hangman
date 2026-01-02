import "./bootstrap";

import "../css/hangman.css";
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import ToastPlugin from 'vue-toast-notification';
//import 'vue-toast-notification/dist/theme-default.css';
import 'vue-toast-notification/dist/theme-bootstrap.css';

const app = createInertiaApp({
    id: 'hangman',
    resolve: name => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ToastPlugin)
            .mount(el)
    },
})
