import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Enhanced font scaling for better mobile readability
            fontSize: {
                'xs-mobile': ['0.688rem', { lineHeight: '1rem' }],
                'sm-mobile': ['0.75rem', { lineHeight: '1.25rem' }],
                'base-mobile': ['0.875rem', { lineHeight: '1.5rem' }],
            },

            // Improved mobile breakpoints
            screens: {
                'xs': '375px',     // Small mobile devices
                'sm': '640px',     // Larger mobile devices
                'md': '768px',     // Tablets
                'lg': '1024px',    // Laptops
                'xl': '1280px',    // Desktops
                '2xl': '1536px',   // Large screens
            },

            // Mobile-friendly spacing
            spacing: {
                'mobile-xs': '0.25rem',
                'mobile-sm': '0.5rem',
                'mobile-md': '0.75rem',
                'mobile-lg': '1rem',
            },

            // Enhanced color palette with mobile-friendly contrast
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
                },
                // High contrast mobile colors
                'mobile-text': {
                    'primary': '#1f2937',   // Gray-900
                    'secondary': '#4b5563', // Gray-700
                    'muted': '#6b7280',     // Gray-500
                }
            },

            // Touch-friendly sizing
            width: {
                'mobile-full': '100%',
                'mobile-screen': '100vw',
            },

            // Mobile-optimized border radius
            borderRadius: {
                'mobile-sm': '0.375rem',   // Slightly smaller on mobile
                'mobile-md': '0.5rem',
                'mobile-lg': '0.75rem',
            },

            // Shadow adjustments for mobile
            boxShadow: {
                'mobile-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'mobile-md': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
            },

            // Typography for mobile
            fontFamily: {
                'sans': ['Figtree', ...defaultTheme.fontFamily.sans],
                'mobile': ['Inter', 'system-ui', 'sans-serif'], // More mobile-friendly font
            },
        },
    },

    // Mobile-specific plugins
    plugins: [
        forms,
        typography,
        function ({ addUtilities }) {
            const newUtilities = {
                // Touch target size recommendations
                '.touch-target-sm': {
                    'min-width': '44px',
                    'min-height': '44px',
                },
                '.touch-target-md': {
                    'min-width': '48px',
                    'min-height': '48px',
                },
                '.mobile-scroll-snap': {
                    'scroll-snap-type': 'x mandatory',
                },
                '.mobile-scroll-align': {
                    'scroll-snap-align': 'center',
                }
            };
            addUtilities(newUtilities);
        }
    ],
};
