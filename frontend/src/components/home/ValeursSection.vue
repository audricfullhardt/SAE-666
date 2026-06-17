<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <section class="bg-vert px-6 py-14">
    <div class="mx-auto max-w-2xl text-center">
      <!-- Section title -->
      <h2 class="mb-8 font-luckiest text-3xl uppercase tracking-wide text-white md:text-4xl">
        {{ t('home.valeurs.title') }}
      </h2>

      <!-- Body with highlighted keywords via i18n-t named slots -->
      <i18n-t keypath="home.valeurs.text" tag="p" class="font-bryndan text-base leading-relaxed text-white md:text-lg">
        <template #kw1>
          <span class="rounded bg-jaune px-1.5 py-0.5 font-bold text-foret">
            {{ t('home.valeurs.kw1') }}
          </span>
        </template>
        <template #kw2>
          <span class="rounded bg-foret px-1.5 py-0.5 font-bold text-menthe">
            {{ t('home.valeurs.kw2') }}
          </span>
        </template>
        <template #kw3>
          <span class="rounded bg-jaune px-1.5 py-0.5 font-bold text-foret">
            {{ t('home.valeurs.kw3') }}
          </span>
        </template>
      </i18n-t>
    </div>
  </section>
</template>
