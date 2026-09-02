import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                "outline": "#737783",
                "surface": "#f9f9ff",
                "surface-container-highest": "#e2e2e9",
                "on-secondary-container": "#fefcff",
                "on-primary": "#ffffff",
                "primary-fixed": "#d7e2ff",
                "secondary-container": "#0070ea",
                "on-surface-variant": "#424751",
                "on-surface": "#191c21",
                "surface-container-lowest": "#ffffff",
                "surface-container": "#ededf5",
                "surface-container-high": "#e7e8ef",
                "error-container": "#ffdad6",
                "on-error-container": "#93000a",
                "primary": "#003f83",
                "primary-container": "#1a56a6",
                "on-primary-container": "#b7ceff",
                "background": "#f9f9ff",
                "outline-variant": "#c2c6d3",
                "surface-variant": "#e2e2e9",
            },
            fontFamily: {
                sans: ['Hanken Grotesk', ...defaultTheme.fontFamily.sans],
                'headline-md': ['Hanken Grotesk'],
                'headline-lg': ['Hanken Grotesk'],
                'body-sm': ['Hanken Grotesk'],
                'body-lg': ['Hanken Grotesk'],
                'label-bold': ['Hanken Grotesk']
            }
        },
    },
    plugins: [],
};