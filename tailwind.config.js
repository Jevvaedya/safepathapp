import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                primary: "#cb5bff",
                "primary-dark": "#a13eda", // Ini untuk hover, dll.
                "accent-orange": "#e77938",
                "text-main": "#000000", // Ini untuk teks hitam
                "background-main": "#ffffff", // Ini untuk latar putih
                "header-main": "rgba(203, 91, 255, 0.2)", // Ungu dengan opasitas 80%
                "header-second": "rgba(231, 121, 56, 0.2)", // Oranye dengan opasitas 80%
                "header-light": "rgba(231, 121, 56, 0.8)", // Oranye dengan opasitas 80%
            },
            fontFamily: {
                // Bagian font ini sepertinya sudah bekerja untukmu
                sans: ["Outfit", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
