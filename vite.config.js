import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Code splitting and optimization
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['chart.js'],
                    'tailwind': ['tailwindcss'],
                },
            },
        },
        // Minify CSS and JS
        minify: 'terser',
        cssMinify: true,
        // Generate source maps for production debugging
        sourcemap: process.env.NODE_ENV === 'production' ? false : true,
        // Chunk size warning limit
        chunkSizeWarningLimit: 1000,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['chart.js'],
    },
});
