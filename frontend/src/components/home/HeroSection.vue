<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImg from '@/assets/hero.png'
import dinoImg from '@/assets/dino.svg'

const { t } = useI18n()
const playRoute = '/jeu'
</script>

<template>
  <!-- Mobile: 3/2 landscape ratio — Desktop: 80vh with room for the button -->
  <section class="relative w-full aspect-[3/2] pb-8 md:aspect-auto md:h-[80vh]">
    <!-- Background: cover the full section, no tiling -->
    <div
      class="absolute inset-0"
      :style="{
        backgroundImage: `url(${heroImg})`,
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
      }"
    />

    <!-- Foreground content stretches to fill the whole section -->
    <div class="relative flex h-full flex-col justify-center px-6 py-12 md:px-20">
      <!-- Dino sprite: right side, vertically centred -->
      <img
        :src="dinoImg"
        alt=""
        aria-hidden="true"
        class="absolute right-6 top-1/2 h-48 w-auto -translate-y-1/2 drop-shadow-xl md:h-72"
      />

      <!-- Tagline: left ~55% so it never overlaps dino -->
      <h1 class="w-[55%] font-luckiest text-titre1 uppercase leading-tight tracking-wide text-white drop-shadow-md md:text-titre1-md">
        {{ t('home.hero.tagline') }}
      </h1>
    </div>

    <!-- CTA: centred, half-overflowing into next section -->
    <div class="absolute bottom-0 left-0 right-0 flex translate-y-1/2 justify-center">
      <RouterLink
        :to="playRoute"
        class="rounded-full bg-jaune px-14 py-4 font-luckiest text-btn tracking-widest text-white shadow-xl transition hover:brightness-110 active:scale-95 md:text-titre4-md"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>
  </section>
</template>
