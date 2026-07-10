import path from 'path';
import { fileURLToPath } from 'url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
  base: '/app/',
  plugins: [
    react(),
    tailwindcss(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.png'],
      manifest: {
        name: 'ZANUPF Constitution',
        short_name: 'ZANUPF',
        description: 'Constitution, Academy, and member portal',
        theme_color: '#020617',
        background_color: '#020617',
        display: 'standalone',
        start_url: '/app/',
        scope: '/app/',
        icons: [
          {
            src: '/app/icon-192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: '/app/icon-512.png',
            sizes: '512x512',
            type: 'image/png',
          },
        ],
      },
      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg,jpg,woff2,webmanifest}'],
        importScripts: ['push-sw.js'],
        maximumFileSizeToCacheInBytes: 8 * 1024 * 1024,
        cleanupOutdatedCaches: true,
        clientsClaim: true,
        skipWaiting: true,
        navigateFallback: '/app/index.html',
        navigateFallbackDenylist: [/^\/api\//],
        runtimeCaching: [
          {
            urlPattern: ({ request }) => request.mode === 'navigate',
            handler: 'NetworkFirst',
            options: {
              cacheName: 'app-navigations',
              networkTimeoutSeconds: 4,
            },
          },
          {
            urlPattern: ({ url }) => url.pathname === '/app/index.html',
            handler: 'NetworkFirst',
            options: {
              cacheName: 'app-shell-html',
              networkTimeoutSeconds: 4,
            },
          },
        ],
      },
    }),
  ],
    resolve: {
    alias: {
      '@mobile-data': path.resolve(__dirname, '../mobile/src/data'),
    },
  },
  build: {
    outDir: path.resolve(__dirname, '../backend/public/app'),
    emptyOutDir: true,
  },
  server: {
    host: true,
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8081',
        changeOrigin: true,
      },
    },
  },
});
