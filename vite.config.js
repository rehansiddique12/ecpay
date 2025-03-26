import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        origin: 'http://localhost', // Ensures it loads from the public path with the domain
    },
    build: {
        outDir: 'public/build', // Store assets in `public/build`
        emptyOutDir: true,
    },
    base: '/build/', // Ensures Vite uses `/build/` inside `public`
});
