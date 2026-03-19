import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/pages/login.css',
                'resources/css/pages/home.css',
                'resources/css/pages/admin.css',
                'resources/js/app.js',
                'resources/js/pages/login.js',
                'resources/js/pages/home.js',
                'resources/js/pages/admin.js',
            ],
            refresh: true,
        }),
    ],
});
