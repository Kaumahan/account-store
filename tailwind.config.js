/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue", // Critical for your Vue storefront!
  ],
  theme: {
    extend: {
      // You can add custom brand colors for your shop here later
    },
  },
  plugins: [],
}