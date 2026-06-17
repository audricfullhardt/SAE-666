<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImg from '@/assets/hero.png'
import dinoImg from '@/assets/dino.svg'

const { t } = useI18n()
const playRoute = '/jeu'
</script>

<template>
  <!-- Tiled jungle background, no scrim: leaves are naturally dark -->
  <section
    class="relative flex min-h-[72vw] flex-col justify-end overflow-hidden md:min-h-[520px]"
    :style="{
      backgroundImage: `url(${heroImg})`,
      backgroundRepeat: 'repeat',
      backgroundSize: '180px',
    }"
  >
    <!-- Dino character: right side, vertically centered -->
    <img
      :src="dinoImg"
      alt=""
      aria-hidden="true"
      class="absolute right-4 top-1/2 -translate-y-1/2 h-40 w-auto drop-shadow-lg md:h-56"
    />

    <!-- Tagline: left side, leaves room for dino -->
    <div class="relative z-10 px-5 pb-4 md:px-16">
      <h1
        class="w-[58%] font-luckiest text-4xl uppercase leading-[1.1] tracking-wide text-white drop-shadow-md
               md:w-1/2 md:text-6xl"
      >
        {{ t('home.hero.tagline') }}
      </h1>
    </div>

    <!-- JOUER button: centered at bottom, yellow pill -->
    <div class="relative z-10 flex justify-center pb-0 translate-y-1/2">
      <RouterLink
        :to="playRoute"
        class="rounded-full bg-jaune px-14 py-3 font-luckiest text-2xl tracking-widest
               text-white shadow-xl transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>

    <!-- Spacer so the button doesn't get clipped -->
    <div class="h-8" />
  </section>
</template>
