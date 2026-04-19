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
                kk: {
                    canvas: '#f5f4f0',
                    'nav-from': '#115e59',
                    'nav-to': '#0f766e',
                    'nav-fg': '#ecfdf5',
                    'nav-muted': '#99f6e4',
                    sidebar: '#f7fdf9',
                    'sidebar-border': '#bbf7d0',
                    'sidebar-active': '#d1fae5',
                    'sidebar-hover': '#ecfdf5',
                    'sidebar-text': '#292524',
                    'sidebar-muted': '#57534e',
                    accent: '#0d9488',
                    'accent-soft': '#ccfbf1',
                    border: '#a7f3d0',
                    'border-strong': '#34d399',
                    'active-strip-bg': '#f0fdfa',
                    'active-strip-border': '#99f6e4',
                    'active-strip-text': '#134e4a',
                    'modal-from': '#115e59',
                    'modal-to': '#047857',
                    surface: '#ffffff',
                    'surface-muted': '#fafaf9',
                },
            },
        },
    },

    plugins: [forms],
};
