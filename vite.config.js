import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/bootstrap.js',
                'resources/js/form.js',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': 'bootstrap',
        },
    },
    optimizeDeps: {
        include: ['quill', 'highlight.js'],
    },
    build: {
        rollupOptions: {
            external: [],
            output: {
                manualChunks: {
                    quill: ['quill'],
                    highlight: ['highlight.js'],
                },
            },
        },
    },
}); 