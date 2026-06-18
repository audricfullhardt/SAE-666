import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// Cibles des proxies dev : services Docker (web/mercure) quand Vite tourne dans le conteneur,
// localhost sinon (Vite lancé sur l'hôte). Aucune IP en dur côté client : le navigateur parle
// toujours à Vite (même origine), Vite relaie vers le hub Mercure et l'API.
const apiProxy = process.env.VITE_API_PROXY || 'http://localhost:8000'
const mercureProxy = process.env.VITE_MERCURE_PROXY || 'http://localhost:3000'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    host: true,
    proxy: {
      '/api': { target: apiProxy, changeOrigin: true },
      // SSE Mercure : connexion longue durée, on coupe le buffering du proxy.
      '/.well-known/mercure': { target: mercureProxy, changeOrigin: true, configure: configureSse },
    },
  },
})

// Désactive le timeout/buffering pour le flux SSE (sinon la connexion est coupée).
function configureSse(proxy) {
  proxy.on('proxyReq', (proxyReq) => {
    proxyReq.setHeader('Connection', 'keep-alive')
  })
}
