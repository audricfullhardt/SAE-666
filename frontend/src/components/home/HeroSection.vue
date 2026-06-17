<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImg from '@/assets/hero.png'
import dinoImg from '@/assets/dino.svg'

const { t } = useI18n()
const playRoute = '/jeu'
</script>

<template>
  <!-- No overflow-hidden here so the CTA button can bleed out at the bottom -->
  <section class="relative pb-8">
    <!-- Tiled background in its own layer -->
    <div
      class="absolute inset-0"
      :style="{
        backgroundImage: `url(${heroImg})`,
        backgroundRepeat: 'repeat',
        backgroundSize: '180px',
      }"
    />

    <!-- Foreground content -->
    <div class="relative flex min-h-[58vw] flex-col justify-center px-5 py-8 md:min-h-[420px] md:px-16">
      <!-- Dino sprite: right, vertically centred -->
      <img
        :src="dinoImg"
        alt=""
        aria-hidden="true"
        class="absolute right-4 top-1/2 h-36 w-auto -translate-y-1/2 drop-shadow-lg md:h-52"
      />

      <!-- Tagline: left 55% so it never overlaps the dino -->
      <h1 class="w-[55%] font-luckiest text-3xl uppercase leading-tight tracking-wide text-white drop-shadow-md md:text-5xl">
        {{ t('home.hero.tagline') }}
      </h1>
    </div>

    <!-- CTA: absolutely positioned at bottom-center, half overflowing into next section -->
    <div class="absolute bottom-0 left-0 right-0 flex translate-y-1/2 justify-center">
      <RouterLink
        :to="playRoute"
        class="rounded-full bg-jaune px-12 py-3 font-luckiest text-2xl tracking-widest text-white shadow-xl transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>
  </section>
</template>
