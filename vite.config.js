import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // server: {
    //     cors: {
    //         credentials: true,
    //         origin: [
    //             /^https?:\/\/.*\.test(:\d+)?$/,
    //         ],
    //     },
    //     host: 'laravel.test',
    //     port: 3000,
    //     origin: 'http://laravel.test:5173',
    //     strictPort: false,
    // },
});
