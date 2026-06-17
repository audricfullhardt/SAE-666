<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import logoImg from '@/assets/logo.png'
import heroImg from '@/assets/hero.png'

const { t } = useI18n()
const playRoute = '/jeu'
</script>

<template>
  <section class="relative flex min-h-[70vh] flex-col items-center justify-center overflow-hidden bg-foret">
    <!-- subtle radial glow for depth -->
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_#015c30_0%,_#013D22_70%)]" />

    <!-- pixel art hero image – centered, natural size, gently faded at edges -->
    <img
      :src="heroImg"
      alt=""
      aria-hidden="true"
      class="absolute inset-0 m-auto h-auto w-auto max-w-none opacity-20"
    />

    <div class="relative z-10 flex flex-col items-center gap-6 px-6 py-20 text-center">
      <!-- Brand logo -->
      <img :src="logoImg" alt="DinoMania" class="h-20 w-auto drop-shadow-lg md:h-28" />

      <!-- Tagline -->
      <h1
        class="max-w-xs font-luckiest text-3xl uppercase leading-tight tracking-wide text-white drop-shadow
               md:max-w-2xl md:text-5xl"
      >
        {{ t('home.hero.tagline') }}
      </h1>

      <!-- CTA -->
      <RouterLink
        :to="playRoute"
        class="mt-2 rounded-full bg-rouge px-10 py-3 font-luckiest text-xl tracking-widest
               text-white shadow-xl transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>
  </section>
</template>
