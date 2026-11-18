import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/backoffice.css',
                'resources/css/backoffice-legacy.css',
                'resources/js/backoffice.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
