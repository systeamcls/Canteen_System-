import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [
    laravel({
      input: [
          'resources/css/canteen2.css',
          'resources/js/app.jsx',
          'resources/js/app.js',
          'resources/css/app.css',
          'resources/js/cart.js',
          'resources/js/canteen.js',
          'resources/js/cart.js'
      ],
      refresh: true,
    }),
    react(),
  ],
})
