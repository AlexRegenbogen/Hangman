import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import * as path from "path";
import {resolve} from "path";
import fs from "fs";
import {homedir} from "os";
import {coverageConfigDefaults} from 'vitest/config'
import tailwindcss from '@tailwindcss/vite'

const host = "hangman.test";
export default defineConfig({
    server: detectServerConfig(host),
    plugins: [
        laravel({
            input: [
                "resources/css/hangman.css",
                "resources/js/hangman.js",
            ],
            refresh: true
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                }
            }
        }),
        tailwindcss(),
    ],
    test: {
        coverage: {
            reportsDirectory: './storage/coverage_frontend',
            exclude: [
                ...coverageConfigDefaults.exclude,
                '**/storage/**',
                '**/storage/coverage/**',
                '**/devtools/**',
                '**/node_modules/**',
                '**/vendor/**'
            ]
        },
        exclude: [
            '**/storage/**',
            '**/storage/coverage/**',
            '**/devtools/**',
            '**/node_modules/**',
            '**/vendor/**'
        ],
    },
    resolve: {
        alias: {
            "@": "/resources/js",
            "ziggy-js": path.resolve("vendor/tightenco/ziggy"),
        }
    },
    optimizeDeps: {
        include: ["@fawmi/vue-google-maps", "fast-deep-equal"]
    }
});

function detectServerConfig(host) {
    let keyPath = resolve(homedir(), `.valet/Certificates/${host}.key`)
    let certificatePath = resolve(homedir(), `.valet/Certificates/${host}.crt`)

    if (!fs.existsSync(keyPath)) {
        return {}
    }

    if (!fs.existsSync(certificatePath)) {
        return {}
    }

    return {
        hmr: {host},
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
        watch: {
            // https://vitejs.dev/config/server-options.html#server-watch
            usePolling: true
        }
    }
}
