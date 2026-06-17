<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import heroImg from '@/assets/hero.png'
import logoImg from '@/assets/logo.png'

const { t } = useI18n()
const auth = useAuthStore()

const playRoute = '/jeu'
</script>

<template>
  <section
    class="relative flex min-h-[62vh] flex-col items-center justify-center overflow-hidden bg-foret"
    :style="{ backgroundImage: `url(${heroImg})`, backgroundSize: 'cover', backgroundPosition: 'center' }"
  >
    <!-- dark overlay so text is readable -->
    <div class="absolute inset-0 bg-foret/55" />

    <div class="relative z-10 flex flex-col items-center gap-6 px-6 py-20 text-center">
      <!-- Brand logo -->
      <img :src="logoImg" alt="DinoMania" class="h-16 w-auto md:h-24" />

      <!-- Tagline -->
      <h1
        class="max-w-xs font-luckiest text-3xl uppercase leading-tight tracking-wide text-white
               md:max-w-xl md:text-5xl"
      >
        {{ t('home.hero.tagline') }}
      </h1>

      <!-- CTA -->
      <RouterLink
        :to="playRoute"
        class="mt-2 rounded-full bg-rouge px-10 py-3 font-luckiest text-xl tracking-widest
               text-white shadow-lg transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.hero.cta') }}
      </RouterLink>
    </div>
  </section>
</template>
