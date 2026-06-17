<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImg from '@/assets/hero.png'

const { t } = useI18n()
const playRoute = '/jeu'
</script>

<template>
  <!-- Forest green hero with tiled pixel art background -->
  <section
    class="relative flex min-h-[72vh] flex-col justify-center overflow-hidden bg-foret"
    :style="{
      backgroundImage: `url(${heroImg})`,
      backgroundRepeat: 'repeat',
      backgroundSize: '220px',
    }"
  >
    <!-- dark scrim for legibility -->
    <div class="absolute inset-0 bg-foret/70" />

    <div class="relative z-10 px-6 py-20 md:px-16">
      <!-- Tagline: left-aligned as in mockup -->
      <h1
        class="max-w-xs font-luckiest text-4xl uppercase leading-tight tracking-wide text-white drop-shadow
               md:max-w-2xl md:text-6xl"
      >
        {{ t('home.hero.tagline') }}
      </h1>

      <!-- CTA: left-aligned on mobile -->
      <RouterLink
        :to="playRoute"
        class="mt-8 inline-block rounded-full bg-rouge px-10 py-3 font-luckiest text-xl
               tracking-widest text-white shadow-xl transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>
  </section>
</template>
