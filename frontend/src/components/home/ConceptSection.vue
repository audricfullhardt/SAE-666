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
      <h2 class="mb-8 font-luckiest text-3xl uppercase tracking-wide text-jaune md:text-4xl">
        {{ t('home.concept.title') }}
      </h2>

      <!-- 3 dashed cards: stacked on mobile, row on md+ -->
      <div class="mb-10 flex flex-col gap-4 md:flex-row">
        <div
          v-for="key in paragraphKeys"
          :key="key"
          class="flex-1 rounded-xl border-2 border-dashed border-gray-300 p-5 font-bryndan text-sm leading-relaxed text-gray-700"
        >
          {{ t(key) }}
        </div>
      </div>

      <!-- Stats row -->
      <div class="flex items-center justify-around">
        <div v-for="stat in stats" :key="stat.labelKey" class="flex flex-col items-center gap-1">
          <span :class="['font-luckiest text-5xl tracking-wide', stat.colorClass]">
            {{ stat.value }}
          </span>
          <span class="font-luckiest text-xs uppercase tracking-widest text-gray-500">
            {{ t(stat.labelKey) }}
          </span>
        </div>
      </div>
    </div>
  </section>
</template>
