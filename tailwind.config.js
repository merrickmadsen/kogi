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
                kogi: {
                    brown: '#3D2B1F',
                    orange: '#E8650A',
                    cream: '#F9F5F0',
                    tan: '#D4A574',
                    sage: '#7C8C6E',
                }
            }
        },
    },

    plugins: [forms],
};
