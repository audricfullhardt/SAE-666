<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/** Static stat items – value shown large, label below */
const stats = [
  { value: '2–6', labelKey: 'home.concept.stats.players', colorClass: 'text-rouge' },
  { value: '7–77', labelKey: 'home.concept.stats.age',     colorClass: 'text-bleu'  },
  { value: "20'",  labelKey: 'home.concept.stats.time',    colorClass: 'text-vert'  },
]

/** Three concept paragraph keys */
const paragraphKeys = [
  'home.concept.p1',
  'home.concept.p2',
  'home.concept.p3',
]
</script>

<template>
  <section class="bg-white px-6 py-14">
    <div class="mx-auto max-w-3xl">
      <!-- Section title -->
      <h2 class="mb-8 font-luckiest text-3xl uppercase tracking-wide text-jaune md:text-4xl">
        {{ t('home.concept.title') }}
      </h2>

      <!-- Cards with dashed border -->
      <div class="mb-10 flex flex-col gap-4 md:flex-row">
        <div
          v-for="key in paragraphKeys"
          :key="key"
          class="flex-1 rounded-xl border-2 border-dashed border-gray-300 p-5
                 font-patrick text-sm leading-relaxed text-gray-700"
        >
          {{ t(key) }}
        </div>
      </div>

      <!-- Stats row -->
      <div class="flex items-center justify-around gap-4">
        <div
          v-for="stat in stats"
          :key="stat.labelKey"
          class="flex flex-col items-center"
        >
          <span :class="['font-luckiest text-4xl tracking-wide md:text-5xl', stat.colorClass]">
            {{ stat.value }}
          </span>
          <span class="mt-1 font-luckiest text-xs uppercase tracking-widest text-gray-500 md:text-sm">
            {{ t(stat.labelKey) }}
          </span>
        </div>
      </div>
    </div>
  </section>
</template>
