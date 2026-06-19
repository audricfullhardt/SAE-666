import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

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
      '/.well-known/mercure': { target: mercureProxy, changeOrigin: true, configure: configureSse },
    },
  },
})

function configureSse(proxy) {
  proxy.on('proxyReq', (proxyReq) => {
    proxyReq.setHeader('Connection', 'keep-alive')
  })
}
