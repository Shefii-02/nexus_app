import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  plugins: [
    react(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'masked-icon.svg'],
      manifest: {
        name: 'Nexus Learning Admin',
        short_name: 'Nexus Admin',
        description: 'Nexus Learning admin dashboard',
        theme_color: '#0f172a',
        background_color: '#0f172a',
        display: 'standalone',
        start_url: '/',
        scope: '/',
        orientation: 'portrait',
        icons: [
          { src: 'pwa-192x192.png', sizes: '192x192', type: 'image/png' },
          { src: 'pwa-512x512.png', sizes: '512x512', type: 'image/png' },
          { src: 'pwa-512x512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' },
        ],
      },
      workbox: {
        // No runtime caching at all — every request (API, navigation,
        // assets) always goes to the network. The SW exists only to
        // satisfy install criteria, not to serve anything offline.
        globPatterns: [], // don't precache the app shell either
        navigateFallback: null,
      },
      devOptions: {
        enabled: false,
        type: 'module',
      },
    }),
  ],
})