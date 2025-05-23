import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/scss/admin-style.scss',
                'resources/scss/navbar.scss',
                'resources/scss/dashboard.scss',
                'resources/scss/profile.scss',
                'resources/scss/hoi-dong/chi-tiet.scss'
            ],
            refresh: true,
        }),
    ],
});
