/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx}',
    ],
    theme: {
        extend: {
            colors: {
                navy: '#071329',
                'navy-2': '#0b1a36',
                primary: '#1769FF',
                sky: '#45C8FF',
                violet: '#7653F6',
                snow: '#F7F9FF',
            },
            fontFamily: {
                sans: ['Vazirmatn', 'system-ui', 'sans-serif'],
                mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
            },
        },
    },
    plugins: [],
};