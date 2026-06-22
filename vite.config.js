import { defineConfig } from 'vite';
import kiosk from 'kiosk-vite-plugin';

export default defineConfig({
    plugins: [
        kiosk({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
