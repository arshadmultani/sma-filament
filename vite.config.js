import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/ar.css',
                'resources/js/ar.js',
                'resources/js/ar-compiler.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
