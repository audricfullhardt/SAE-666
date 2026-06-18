<script setup>
import { useI18n } from 'vue-i18n'

const { t, tm } = useI18n()

const chapterColors = [
  { borderLeft: 'border-l-rouge', numClass: 'text-rouge' },
  { borderLeft: 'border-l-bleu',  numClass: 'text-bleu'  },
  { borderLeft: 'border-l-jaune', numClass: 'text-jaune' },
  { borderLeft: 'border-l-vert',  numClass: 'text-vert'  },
]

const chapters = tm('pages.regles.chapters').map((ch, i) => ({
  ...ch,
  ...chapterColors[i],
  index: i + 1,
}))

const stats = [
  { value: '2–4', labelKey: 'pages.regles.players', colorClass: 'text-rouge', bg: 'bg-rouge/10' },
  { value: "20'", labelKey: 'pages.regles.time',    colorClass: 'text-vert',  bg: 'bg-vert/10'  },
  { value: '7–77', labelKey: 'pages.regles.age',     colorClass: 'text-bleu',  bg: 'bg-bleu/10'  },
]
</script>

<template>
  <main class="min-h-svh bg-menthe">

    <!-- Page header -->
    <div class="border-b-2 border-dashed border-black bg-white">
      <div class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-l-4 border-vert pl-4">
          <h1 class="font-luckiest text-3xl uppercase tracking-wide text-noir">{{ t('pages.regles.title') }}</h1>
        </div>
        <p class="mt-4 font-bryndan text-sm leading-relaxed text-gray-500 md:max-w-xl">
          {{ t('pages.regles.intro') }}
        </p>
        <!-- Stats pills -->
        <div class="mt-5 flex flex-wrap gap-2">
          <div
            v-for="stat in stats"
            :key="stat.labelKey"
            :class="['flex items-center gap-2 rounded-full px-4 py-1.5', stat.bg]"
          >
            <span :class="['font-luckiest text-lg leading-none', stat.colorClass]">{{ stat.value }}</span>
            <span class="font-bryndan text-xs uppercase tracking-wider text-gray-600">{{ t(stat.labelKey) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Chapters -->
    <div class="mx-auto max-w-4xl px-6 py-10">
      <div class="flex flex-col gap-4">
        <div
          v-for="ch in chapters"
          :key="ch.index"
          :class="['rounded-md border-l-4 bg-white p-6', ch.borderLeft]"
        >
          <div class="mb-3 flex items-baseline gap-3">
            <span :class="['font-luckiest text-3xl leading-none', ch.numClass]">{{ ch.index }}</span>
            <h2 class="font-luckiest text-base uppercase tracking-wide text-noir md:text-lg">{{ ch.title }}</h2>
          </div>
          <p class="font-bryndan text-sm leading-relaxed text-gray-600">{{ ch.body }}</p>
        </div>
      </div>
    </div>

  </main>
</template>
