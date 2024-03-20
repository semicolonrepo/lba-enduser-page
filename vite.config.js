import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',

                'resources/css/qty-voucher.css',
                'resources/js/qty-voucher.js',

                'resources/css/lba-1/custom.css',
                'resources/js/lba-1/product.js',

                'resources/css/lba-2/custom.css',
                'resources/js/lba-2/product.js',
            ],
            refresh: true,
        }),
    ],
});
