import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'navy': {
                    50: '#f0f3f8',
                    100: '#d9e1ed',
                    200: '#b3c3db',
                    300: '#8da5c9',
                    400: '#6687b7',
                    500: '#4a6997',
                    600: '#3b547a',
                    700: '#2c3f5c',
                    800: '#1d2a3e',
                    900: '#0e1520',
                },
                'accent': {
                    'blue': '#3b82f6',
                    'green': '#10b981',
                    'red': '#ef4444',
                    'yellow': '#f59e0b',
                }
            },
        },
    },

    plugins: [forms],
};
