<script setup>
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <main class="min-h-svh flex flex-col items-center justify-center gap-6 p-8 text-center">
    <h1 class="text-5xl font-bold tracking-tight text-primary">{{ t('app.name') }}</h1>
    <p class="max-w-prose text-muted-foreground">
      Jeu de plateau physique avec interactions numériques. Lancez les dés,
      déplacez-vous, scannez un QR code et affrontez vos adversaires en mini-jeux 1v1.
    </p>
    <nav class="flex flex-wrap items-center justify-center gap-3">
      <RouterLink to="/jeu" class="rounded-md bg-primary px-5 py-2.5 font-medium text-primary-foreground hover:opacity-90">
        {{ t('nav.play') }}
      </RouterLink>
      <RouterLink to="/carte" class="rounded-md border px-5 py-2.5 font-medium hover:bg-accent">
        {{ t('nav.map') }}
      </RouterLink>
      <RouterLink to="/projets" class="rounded-md border px-5 py-2.5 font-medium hover:bg-accent">
        {{ t('nav.projects') }}
      </RouterLink>
      <RouterLink to="/inscription" class="rounded-md border px-5 py-2.5 font-medium hover:bg-accent">
        {{ t('nav.register') }}
      </RouterLink>
    </nav>
  </main>
</template>
