import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { splitVendorChunkPlugin } from 'vite';
import compression from 'vite-plugin-compression';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        splitVendorChunkPlugin(),
        compression({
            algorithm: 'gzip',
            ext: '.gz',
        }),
        compression({
            algorithm: 'brotliCompress',
            ext: '.br',
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
});
