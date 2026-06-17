<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const stats = [
  { value: '2–6',  labelKey: 'home.concept.stats.players', colorClass: 'text-rouge' },
  { value: '7–77', labelKey: 'home.concept.stats.age',     colorClass: 'text-bleu'  },
  { value: "20'",  labelKey: 'home.concept.stats.time',    colorClass: 'text-vert'  },
]

const paragraphKeys = ['home.concept.p1', 'home.concept.p2', 'home.concept.p3']
</script>

<template>
  <!-- pt-10 absorbs the hero CTA button that bleeds into this section -->
  <section class="bg-white px-6 pb-14 pt-12">
    <div class="mx-auto max-w-3xl">
      <h2 class="mb-8 mt-8 font-luckiest text-titre1 uppercase tracking-wide text-noir md:text-titre1-md">
        {{ t('home.concept.title') }}
      </h2>

      <!-- Single big dashed card -->
      <div class="mb-10 rounded-xl border-2 border-dashed border-gray-300 p-6 font-bryndan text-body leading-relaxed text-gray-700 md:text-body-md">
        <p v-for="key in paragraphKeys" :key="key" class="mb-3 last:mb-0">
          {{ t(key) }}
        </p>
      </div>

      <!-- Stats row -->
      <div class="flex items-center justify-around">
        <div v-for="stat in stats" :key="stat.labelKey" class="flex flex-col items-center gap-1">
          <span :class="['font-luckiest text-titre1 tracking-wide md:text-titre1-md', stat.colorClass]">
            {{ stat.value }}
          </span>
          <span class="font-bryndan text-titre5 uppercase tracking-widest text-gray-500">
            {{ t(stat.labelKey) }}
          </span>
        </div>
      </div>
    </div>
  </section>
</template>
