<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import heroImg from '@/assets/hero.png'
import dinoImg from '@/assets/dino_blue.png'

const { t } = useI18n()
</script>

<template>
  <!-- ── MOBILE ────────────────────────────────────────────────────────── -->
  <section
    class="md:hidden relative w-full"
    style="aspect-ratio: 3/2;"
  >
    <div
      class="absolute inset-0"
      :style="{
        backgroundImage: `url(${heroImg})`,
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
      }"
    />
    <div class="relative flex h-full flex-col px-6 pt-10">
      <!-- Title + dino -->
      <div class="relative flex flex-1 items-center">
        <h1 class="w-[55%] font-luckiest text-2xl uppercase leading-tight tracking-wide text-white drop-shadow-md">
          {{ t('home.hero.tagline') }}
        </h1>
        <img
          :src="dinoImg"
          alt=""
          aria-hidden="true"
          class="absolute right-0 top-1/2 h-40 w-auto -translate-y-1/2 drop-shadow-xl [image-rendering:pixelated]"
        />
      </div>
    </div>
    <!-- Buttons: half overflowing into next section -->
    <div class="absolute bottom-0 left-0 right-0 z-10 flex translate-y-1/2 justify-center gap-4 px-6">
      <RouterLink
        to="/jeu"
        class="inline-flex items-center rounded-full bg-jaune px-6 py-2 font-luckiest text-base uppercase leading-none tracking-wide text-white shadow-[0_4px_0_#CC8C00] transition hover:brightness-110 active:scale-95"
      >
        <span class="translate-y-[3px]">{{ t('home.hero.cta') }}</span>
      </RouterLink>
      <RouterLink
        to="/regles"
        class="inline-flex items-center rounded-full border-2 border-noir bg-white px-6 py-2 font-luckiest text-base uppercase leading-none tracking-wide text-noir shadow-[0_4px_0_rgba(0,0,0,0.15)] transition hover:bg-gray-50 active:scale-95"
      >
        <span class="translate-y-[3px]">{{ t('home.hero.rules') }}</span>
      </RouterLink>
    </div>
  </section>

  <!-- ── DESKTOP ───────────────────────────────────────────────────────── -->
  <section class="hidden md:flex min-h-[55vh] items-center gap-12 bg-transparent px-20 py-10 md:border-b-2 md:border-dashed md:border-rouge">
    <!-- Left: title + buttons -->
    <div class="flex flex-1 flex-col gap-8">
      <h1 class="font-luckiest text-[42px] uppercase leading-tight tracking-wide text-noir">
        {{ t('home.hero.tagline') }}
      </h1>
      <div class="flex gap-4">
        <RouterLink
          to="/jeu"
          class="inline-flex items-center rounded-full bg-jaune px-6 py-2 font-luckiest text-base uppercase leading-none tracking-wide text-white shadow-[0_4px_0_#CC8C00] transition hover:brightness-110 active:scale-95"
        >
          <span class="translate-y-[3px]">{{ t('home.hero.cta') }}</span>
        </RouterLink>
        <RouterLink
          to="/regles"
          class="inline-flex items-center rounded-full border-2 border-noir bg-white px-6 py-2 font-luckiest text-base uppercase leading-none tracking-wide text-noir shadow-[0_4px_0_rgba(0,0,0,0.15)] transition hover:bg-gray-50 active:scale-95"
        >
          <span class="translate-y-[3px]">{{ t('home.hero.rules') }}</span>
        </RouterLink>
      </div>
    </div>
    <!-- Right: hero image card with dino -->
    <div
      class="relative w-[45%] overflow-hidden rounded-lg"
      :style="{
        aspectRatio: '16/9',
        backgroundImage: `url(${heroImg})`,
        backgroundRepeat: 'no-repeat',
        backgroundSize: 'cover',
        backgroundPosition: 'center',
      }"
    >
      <img
        :src="dinoImg"
        alt=""
        aria-hidden="true"
        class="absolute right-6 bottom-6 h-36 w-auto drop-shadow-xl [image-rendering:pixelated]"
      />
    </div>
  </section>
</template>
