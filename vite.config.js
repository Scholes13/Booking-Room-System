import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin/dashboard/index.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, 'node_modules'),
            '@': path.resolve(__dirname, 'resources/js')
        }
    },
    server: {
        hmr: {
            host: 'localhost'
        }
    }
});