import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { terser } from "rollup-plugin-terser";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/game.js'
            ],
            refresh: true,
        }),
    ],
    rollupInputOptions: {
        plugins: [terser()],
    },
});