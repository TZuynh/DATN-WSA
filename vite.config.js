import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/scss/app.scss',
                'resources/scss/admin-style.scss',
                'resources/scss/navbar.scss',
                'resources/scss/dashboard.scss',
                'resources/scss/profile.scss',
                'resources/scss/giangvien/sinh-vien.scss'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 3000,
        strictPort: true,
        cors: {
            origin: [
                'http://admin.project.test',
                'http://giangvien.project.test',
                'http://localhost:3000'
            ],
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            allowedHeaders: ['*'],
            credentials: true,
        },
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            port: 3000,
        },
    },
});
