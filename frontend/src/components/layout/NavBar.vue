<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { Menu, X } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'
import LogoutButton from '@/components/layout/LogoutButton.vue'
import logoUrl from '@/assets/logo.png'

const auth = useAuthStore()
const route = useRoute()

const drawerOpen = ref(false)

const links = [
  { to: '/', label: 'Accueil' },
  { to: '/regles', label: 'Règles' },
  { to: '/classement', label: 'Classement' },
  { to: '/actualites', label: 'Actualités' },
  { to: '/contact', label: 'Contact' },
]

watch(
  () => route.path,
  () => {
    drawerOpen.value = false
  },
)
</script>

<template>
  <header class="sticky top-0 z-40 h-16 bg-vert">
    <nav class="mx-auto flex h-full max-w-6xl items-center justify-between gap-4 px-4">
      <RouterLink to="/" class="order-last flex shrink-0 items-center md:order-first">
        <img :src="logoUrl" alt="DinoMania" class="h-12 w-auto" />
      </RouterLink>

      <ul class="hidden items-center gap-6 md:flex">
        <li v-for="link in links" :key="link.to">
          <RouterLink
            :to="link.to"
            class="font-luckiest tracking-wide text-white transition hover:text-white/80"
            exact-active-class="underline decoration-jaune decoration-2 underline-offset-8"
          >
            {{ link.label }}
          </RouterLink>
        </li>
      </ul>

      <div class="hidden items-center gap-3 md:flex">
        <RouterLink
          v-if="auth.isAuthenticated"
          to="/jeu"
          class="rounded-full bg-jaune px-5 py-2 font-luckiest text-sm tracking-wide text-white shadow-sm transition hover:brightness-105"
        >
          JOUER
        </RouterLink>
        <RouterLink
          v-else
          :to="{ name: 'auth', query: { mode: 'login' } }"
          class="rounded-full bg-jaune px-5 py-2 font-luckiest text-sm tracking-wide text-white shadow-sm transition hover:brightness-105"
        >
          CONNEXION
        </RouterLink>
        <LogoutButton />
      </div>

      <button
        type="button"
        class="flex items-center justify-center rounded-lg p-1.5 text-white transition hover:bg-white/15 md:hidden"
        aria-label="Ouvrir le menu"
        @click="drawerOpen = true"
      >
        <Menu class="h-7 w-7" />
      </button>
    </nav>
  </header>

  <div
    class="fixed inset-0 z-50 md:hidden"
    :class="drawerOpen ? '' : 'pointer-events-none'"
  >
    <div
      class="absolute inset-0 bg-black/50 transition-opacity duration-300"
      :class="drawerOpen ? 'opacity-100' : 'opacity-0'"
      @click="drawerOpen = false"
    />

    <aside
      class="absolute left-0 top-0 flex h-full w-72 max-w-[80%] flex-col bg-foret p-5 shadow-2xl transition-transform duration-300 ease-out"
      :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-lg">
            <img src="@/assets/dino_green.png" alt="" class="h-20 w-auto" />
          </div>
          <span v-if="auth.isAuthenticated && auth.username" class="font-patrick text-lg text-white">
            {{ auth.username }}
          </span>
          <span v-else class="font-patrick text-lg text-white/70">Invité</span>
        </div>
        <button
          type="button"
          class="rounded-lg p-1.5 text-white transition hover:bg-white/15"
          aria-label="Fermer le menu"
          @click="drawerOpen = false"
        >
          <X class="h-6 w-6" />
        </button>
      </div>

      <ul class="mt-8 flex flex-col gap-5">
        <li v-for="link in links" :key="link.to">
          <RouterLink
            :to="link.to"
            class="font-luckiest text-xl tracking-wide text-white transition hover:text-jaune"
            exact-active-class="text-jaune"
          >
            {{ link.label }}
          </RouterLink>
        </li>
      </ul>

      <hr class="my-6 border-white/20" />

      <RouterLink
        v-if="auth.isAuthenticated"
        to="/jeu"
        class="rounded-full bg-jaune py-3 text-center font-luckiest tracking-wide text-white shadow-sm transition hover:brightness-105"
      >
        JOUER !
      </RouterLink>
      <RouterLink
        v-else
        :to="{ name: 'auth', query: { mode: 'login' } }"
        class="rounded-full bg-jaune py-3 text-center font-luckiest tracking-wide text-white shadow-sm transition hover:brightness-105"
      >
        CONNEXION
      </RouterLink>

      <div v-if="auth.isAuthenticated" class="mt-auto pt-6">
        <LogoutButton />
      </div>
    </aside>
  </div>
</template>
