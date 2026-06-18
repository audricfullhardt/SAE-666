<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<template>
  <section class="bg-vert px-6 py-14">
    <div class="mx-auto max-w-2xl text-center">
      <h2 class="mb-6 font-luckiest text-xl uppercase tracking-wide text-white md:text-2xl">
        {{ t('home.valeurs.title') }}
      </h2>

      <i18n-t
        keypath="home.valeurs.text"
        tag="p"
        class="font-bryndan text-xs leading-relaxed text-white md:text-sm"
      >
        <template #kw1>
          <span class="bg-rouge px-1 font-bold text-white text-xl md:text-2xl">{{ t('home.valeurs.kw1') }}</span>
        </template>
        <template #kw2>
          <span class="bg-jaune px-1 font-bold text-white text-xl md:text-2xl">{{ t('home.valeurs.kw2') }}</span>
        </template>
        <template #kw3>
          <span class="bg-bleu px-1 font-bold text-white text-xl md:text-2xl">{{ t('home.valeurs.kw3') }}</span>
        </template>
      </i18n-t>
    </div>
  </section>
</template>
