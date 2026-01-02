import "./bootstrap";

import "../css/hangman.css";
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import ToastPlugin from 'vue-toast-notification';
//import 'vue-toast-notification/dist/theme-default.css';
import 'vue-toast-notification/dist/theme-bootstrap.css';

import {createI18n} from "vue-i18n";
import en from '@/Locales/en.json';
import nl from '@/Locales/nl.json';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        en,
        nl
    }
})
const app = createInertiaApp({
    id: 'hangman',
    resolve: name => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(ToastPlugin)
            .mount(el)
    },
})
