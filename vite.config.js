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
        host: 'localhost',
        port: 5173, // Port mặc định của Vite
        strictPort: true, // Không tự động tìm port khác nếu port bị chiếm
        cors: true, // Bật CORS cho tất cả domain trong development
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
});
