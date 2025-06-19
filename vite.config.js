import { defineConfig } from "vite";
import laravel, { refreshPaths } from "laravel-vite-plugin";

export default defineConfig({
    base: '/build/',
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: [
                ...refreshPaths,
                "app/Livewire/**",
                "app/Filament/**",
                "app/Providers/**",
            ],
        }),
    ],
});
