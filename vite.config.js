import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/guest-layout.css',
                'resources/css/app-layout.css',

                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
