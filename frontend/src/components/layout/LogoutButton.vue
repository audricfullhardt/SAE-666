<script setup>
import { useRouter } from 'vue-router'
import { LogOut } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()

function handleLogout() {
  auth.logout()
  router.push({ name: 'auth', query: { mode: 'login' } })
}
</script>

<template>
  <div v-if="auth.isAuthenticated" class="flex items-center gap-2">
    <span v-if="auth.username" class="font-patrick text-sm text-foret">
      {{ auth.username }}
    </span>
    <button
      type="button"
      class="flex items-center gap-1.5 rounded-full bg-rouge px-3 py-1.5 font-luckiest text-xs tracking-wide text-white shadow-sm transition hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-rouge/40"
      @click="handleLogout"
    >
      <LogOut class="h-3.5 w-3.5" />
      Déconnexion
    </button>
  </div>
</template>
