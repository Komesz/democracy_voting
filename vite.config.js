import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "tailwindcss";


export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/src/css/main.css',
                'resources/src/main.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [tailwindcss()],
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: ['resources/src/main.js', "resources/src/css/main.css"],
        },
    },
    server: {
        https: false,
        host: true,
        strictPort: true,
        port: 3009,
        hmr: {host: 'localhost', protocol: 'ws'},
        watch: {
            usePolling:true,
        }
    }
});
