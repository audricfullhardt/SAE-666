<script setup>
import { useI18n } from 'vue-i18n'
import dinoImg from '@/assets/dino_green.png'

const { t } = useI18n()

const contactEmail = 'contact@dinomania.fr'
const contactHref = `mailto:${contactEmail}`
</script>

<template>
  <section class="bg-menthe px-6 py-16">
    <div class="mx-auto flex max-w-2xl flex-col items-center gap-6 text-center">
      <!-- Section title -->
      <h2 class="font-luckiest text-3xl uppercase tracking-wide text-foret md:text-4xl">
        {{ t('home.contact.title') }}
      </h2>

      <!-- Dino mascot -->
      <img
        :src="dinoImg"
        alt="Dino mascotte"
        class="h-28 w-auto drop-shadow-md md:h-36"
      />

      <!-- Body text -->
      <p class="max-w-sm font-bryndan text-base leading-relaxed text-gray-700 md:text-lg">
        {{ t('home.contact.text') }}
      </p>

      <!-- CTA button -->
      <a
        :href="contactHref"
        class="rounded-full bg-rouge px-12 py-3 font-luckiest text-xl uppercase tracking-widest
               text-white shadow-lg transition hover:brightness-110 active:scale-95"
      >
        {{ t('home.contact.cta') }}
      </a>
    </div>
  </section>
</template>
