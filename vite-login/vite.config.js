import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  base: '/~270445/skinscan/',
  build: {
    outDir: 'dist',
    assetsDir: 'react-assets', // 👈 rename build assets folder
  },
  plugins: [react()],
})
