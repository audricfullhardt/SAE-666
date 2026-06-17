<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import logoImg from '@/assets/logo.png'
import heroImg from '@/assets/hero.png'

const { t } = useI18n()
const playRoute = '/jeu'
</script>

<template>
  <!--
    hero.png is a pixel-art terrain tile → repeat it to fill the section like a game map.
  -->
  <section
    class="relative flex min-h-[60vh] flex-col items-center justify-center overflow-hidden bg-foret"
    :style="{ backgroundImage: `url(${heroImg})`, backgroundRepeat: 'repeat', backgroundSize: '220px auto' }"
  >
    <!-- dark scrim so text stays legible over the tile pattern -->
    <div class="absolute inset-0 bg-foret/60" />

    <div class="relative z-10 flex w-full max-w-lg flex-col items-start gap-5 px-6 py-16 md:max-w-2xl md:items-center md:text-center">
      <!-- Brand logo -->
      <img :src="logoImg" alt="DinoMania" class="h-16 w-auto drop-shadow-lg md:h-24" />

      <!-- Tagline -->
      <h1 class="font-luckiest text-4xl uppercase leading-tight tracking-wide text-white drop-shadow md:text-5xl">
        {{ t('home.hero.tagline') }}
      </h1>

      <!-- CTA -->
      <RouterLink
        :to="playRoute"
        class="rounded-full bg-rouge px-10 py-3 font-luckiest text-xl tracking-widest
               text-white shadow-xl transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>
  </section>
</template>
