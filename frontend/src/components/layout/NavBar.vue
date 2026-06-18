<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { Menu, X } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import LogoutButton from '@/components/layout/LogoutButton.vue'
import logoUrl from '@/assets/logo.png'

const auth = useAuthStore()
const route = useRoute()
const { t, locale } = useI18n()

const drawerOpen = ref(false)

const links = [
  { to: '/', labelKey: 'nav.home' },
  { to: '/regles', labelKey: 'nav.rules' },
  { to: '/actualites', labelKey: 'nav.news' },
  { to: '/contact', labelKey: 'nav.contact' },
]

function setLocale(l) {
  locale.value = l
  localStorage.setItem('dinoquest_locale', l)
}

watch(
  () => route.path,
  () => {
    drawerOpen.value = false
  },
)
</script>

<template>
  <header class="sticky top-0 z-40 h-16 bg-vert">
    <!-- Mobile: [hamburger | logo centered | user icon]  Desktop: [logo | nav links | JOUER] -->
    <nav class="relative mx-auto flex h-full max-w-6xl items-center justify-between px-4">

      <!-- LEFT: hamburger (mobile only) -->
      <button
        type="button"
        class="flex items-center justify-center rounded-lg p-1.5 text-white transition hover:bg-white/15 md:hidden"
        aria-label="Ouvrir le menu"
        @click="drawerOpen = true"
      >
        <Menu class="h-7 w-7" />
      </button>

      <!-- Logo: absolute-centered on mobile, normal flow first on desktop -->
      <RouterLink
        to="/"
        class="absolute left-1/2 -translate-x-1/2 flex shrink-0 items-center md:static md:translate-x-0 md:order-first"
      >
        <img :src="logoUrl" alt="DinoMania" class="h-12 w-auto" />
      </RouterLink>

      <!-- Desktop nav links (hidden on mobile) -->
      <ul class="hidden items-center gap-6 md:flex">
        <li v-for="link in links" :key="link.to">
          <RouterLink
            :to="link.to"
            class="font-luckiest tracking-wide text-white transition hover:text-white/80"
            exact-active-class="underline decoration-jaune decoration-2 underline-offset-8"
          >
            {{ t(link.labelKey) }}
          </RouterLink>
        </li>
      </ul>

      <!-- RIGHT: user icon (mobile) + desktop CTA -->
      <div class="flex items-center gap-3">
        <!-- Language switcher (desktop) -->
        <div class="hidden items-center gap-1 md:flex">
          <button
            :class="['font-luckiest text-sm tracking-wide transition', locale === 'fr' ? 'text-white' : 'text-white/40 hover:text-white/70']"
            @click="setLocale('fr')"
          >FR</button>
          <span class="text-white/30">|</span>
          <button
            :class="['font-luckiest text-sm tracking-wide transition', locale === 'en' ? 'text-white' : 'text-white/40 hover:text-white/70']"
            @click="setLocale('en')"
          >EN</button>
        </div>
        <!-- Desktop: JOUER / CONNEXION -->
        <RouterLink
          v-if="auth.isAuthenticated"
          to="/game"
          class="hidden rounded-full bg-jaune px-5 py-2 font-luckiest text-sm tracking-wide text-white shadow-sm transition hover:brightness-105 md:inline-flex"
        >
          {{ t('nav.play') }}
        </RouterLink>
        <RouterLink
          v-else
          :to="{ name: 'auth', query: { mode: 'login' } }"
          class="hidden rounded-full bg-jaune px-5 py-2 font-luckiest text-sm tracking-wide text-white shadow-sm transition hover:brightness-105 md:inline-flex"
        >
          {{ t('nav.login') }}
        </RouterLink>
        <LogoutButton class="hidden md:flex" />

        <!-- Mobile: account circle icon (person silhouette in circle) -->
        <RouterLink
          :to="auth.isAuthenticated ? '/compte' : { name: 'auth', query: { mode: 'login' } }"
          class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-white/80 text-white transition hover:bg-white/15 md:hidden"
          aria-label="Mon compte"
        >
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
            <circle cx="12" cy="8" r="4" />
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke-linecap="round" />
          </svg>
        </RouterLink>
      </div>
    </nav>
  </header>

  <!-- Mobile slide-in drawer -->
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
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
            <img src="@/assets/dino_green.png" alt="" class="h-8 w-auto [image-rendering:pixelated]" />
          </div>
          <span v-if="auth.isAuthenticated && auth.username" class="font-bryndan text-lg text-white">
            {{ auth.username }}
          </span>
          <span v-else class="font-bryndan text-lg text-white/70">{{ t('nav.guest') }}</span>
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
            {{ t(link.labelKey) }}
          </RouterLink>
        </li>
      </ul>

      <hr class="my-6 border-white/20" />

      <!-- Language switcher (mobile drawer) -->
      <div class="flex items-center gap-3">
        <button
          :class="['font-luckiest text-xl tracking-wide transition', locale === 'fr' ? 'text-white' : 'text-white/40 hover:text-white/70']"
          @click="setLocale('fr')"
        >FR</button>
        <span class="text-white/30">|</span>
        <button
          :class="['font-luckiest text-xl tracking-wide transition', locale === 'en' ? 'text-white' : 'text-white/40 hover:text-white/70']"
          @click="setLocale('en')"
        >EN</button>
      </div>

      <hr class="my-6 border-white/20" />
      <RouterLink
        v-if="auth.isAuthenticated"
        to="/game"
        class="rounded-full bg-jaune py-3 text-center font-luckiest tracking-wide text-white shadow-sm transition hover:brightness-105"
      >
        {{ t('nav.play') }}
      </RouterLink>
      <RouterLink
        v-else
        :to="{ name: 'auth', query: { mode: 'login' } }"
        class="rounded-full bg-jaune py-3 text-center font-luckiest tracking-wide text-white shadow-sm transition hover:brightness-105"
      >
        {{ t('nav.login') }}
      </RouterLink>

      <div v-if="auth.isAuthenticated" class="mt-auto pt-6">
        <LogoutButton />
      </div>
    </aside>
  </div>
</template>
