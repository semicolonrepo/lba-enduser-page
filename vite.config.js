import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',

                'resources/css/lba-1/custom.css',
                'resources/js/lba-1/product.js',
            ],
            refresh: true,
        }),
    ],
});
