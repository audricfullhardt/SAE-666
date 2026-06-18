<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'

const { t, tm } = useI18n()
const route = useRoute()

const tagColors = ['bg-bleu', 'bg-vert', 'bg-jaune', 'bg-rouge', 'bg-vert', 'bg-bleu']

const images = [
  'https://images.unsplash.com/photo-1560148271-00b5e5850812?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1583144006017-74800d22ed08?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1633876204719-dd74580764ea?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1657660501312-6b019ad7776c?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1620836572268-90d2f46c0dbf?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1583144006017-74800d22ed08?w=800&q=80&auto=format&fit=crop',
]

const articles = tm('home.actualites.articles').map((a, i) => ({
  ...a,
  id: i,
  tagColor: tagColors[i] ?? 'bg-vert',
  image: images[i],
}))

const selected = ref(null)

function open(article) { selected.value = article }
function close() { selected.value = null }

onMounted(() => {
  const idx = parseInt(route.query.article)
  if (!isNaN(idx) && articles[idx]) {
    selected.value = articles[idx]
  }
})
</script>

<template>
  <main class="min-h-svh bg-menthe">

    <!-- ── LIST VIEW ───────────────────────────────────── -->
    <div v-if="!selected">

      <!-- Page header -->
      <div class="border-b-2 border-dashed border-black bg-white">
        <div class="mx-auto max-w-4xl px-6 py-10">
          <div class="border-l-4 border-rouge pl-4">
            <h1 class="font-luckiest text-3xl uppercase tracking-wide text-noir">{{ t('home.actualites.title') }}</h1>
          </div>
          <p class="mt-4 font-bryndan text-sm text-gray-500 md:max-w-xl">{{ t('pages.actualites.subtitle') }}</p>
        </div>
      </div>

      <div class="mx-auto max-w-5xl px-6 py-10">

        <!-- Featured article (first) -->
        <button
          class="mb-8 w-full cursor-pointer overflow-hidden rounded-lg bg-white text-left transition hover:brightness-95 active:scale-[0.99] md:flex"
          @click="open(articles[0])"
        >
          <div class="h-52 overflow-hidden md:h-auto md:w-2/5">
            <img :src="articles[0].image" alt="" class="h-full w-full object-cover" />
          </div>
          <div class="flex flex-1 flex-col justify-center p-6">
            <div class="mb-3 flex items-center gap-3">
              <span :class="['inline-flex items-center justify-center px-3 py-1 font-luckiest text-xs uppercase leading-none text-white', articles[0].tagColor]">{{ articles[0].tag }}</span>
              <span class="font-bryndan text-xs text-gray-400">{{ articles[0].date }}</span>
            </div>
            <h2 class="mb-2 font-luckiest text-xl uppercase tracking-wide text-noir">{{ articles[0].title }}</h2>
            <p class="font-bryndan text-xs leading-relaxed text-gray-600 md:text-sm">{{ articles[0].excerpt }}</p>
          </div>
        </button>

        <!-- Grid 2-5 -->
        <div class="grid gap-5 md:grid-cols-2">
          <button
            v-for="article in articles.slice(1, 5)"
            :key="article.id"
            class="flex cursor-pointer overflow-hidden rounded-lg bg-white text-left transition hover:brightness-95 active:scale-[0.99]"
            @click="open(article)"
          >
            <div class="w-28 shrink-0 self-stretch overflow-hidden">
              <img :src="article.image" alt="" class="h-full w-full object-cover" />
            </div>
            <div class="flex flex-1 flex-col justify-center p-4">
              <div class="mb-1 flex items-center gap-2">
                <span :class="['inline-flex items-center justify-center px-3 py-1 font-luckiest text-xs uppercase leading-none text-white', article.tagColor]">{{ article.tag }}</span>
                <span class="font-bryndan text-xs text-gray-400">{{ article.date }}</span>
              </div>
              <h2 class="mb-1 font-luckiest text-base uppercase tracking-wide text-noir">{{ article.title }}</h2>
              <p class="font-bryndan text-xs leading-relaxed text-gray-500 line-clamp-2">{{ article.excerpt }}</p>
            </div>
          </button>
        </div>

        <!-- Last article full-width -->
        <button
          class="mt-5 flex w-full cursor-pointer overflow-hidden rounded-lg bg-white text-left transition hover:brightness-95 active:scale-[0.99]"
          @click="open(articles[5])"
        >
          <div class="w-28 shrink-0 self-stretch overflow-hidden">
            <img :src="articles[5].image" alt="" class="h-full w-full object-cover" />
          </div>
          <div class="flex flex-1 flex-col justify-center p-4">
            <div class="mb-1 flex items-center gap-2">
              <span :class="['inline-flex items-center justify-center px-3 py-1 font-luckiest text-xs uppercase leading-none text-white', articles[5].tagColor]">{{ articles[5].tag }}</span>
              <span class="font-bryndan text-xs text-gray-400">{{ articles[5].date }}</span>
            </div>
            <h2 class="mb-1 font-luckiest text-base uppercase tracking-wide text-noir">{{ articles[5].title }}</h2>
            <p class="font-bryndan text-xs leading-relaxed text-gray-500 line-clamp-2">{{ articles[5].excerpt }}</p>
          </div>
        </button>

      </div>
    </div>

    <!-- ── ARTICLE DETAIL ─────────────────────────────── -->
    <div v-else class="px-6 py-10">
      <div class="mx-auto max-w-3xl">

        <!-- Back -->
        <button
          class="mb-8 flex items-center gap-1.5 font-bryndan text-sm text-gray-500 transition hover:text-noir"
          @click="close"
        >
          <span>←</span> {{ t('pages.actualites.backBtn') }}
        </button>

        <!-- Article header -->
        <div class="mb-6 h-64 overflow-hidden rounded-lg">
          <img :src="selected.image" alt="" class="h-full w-full object-cover" />
        </div>

        <div class="mb-4 flex items-center gap-3">
          <span :class="['inline-flex items-center justify-center px-3 py-1 font-luckiest text-xs uppercase leading-none text-white', selected.tagColor]">{{ selected.tag }}</span>
          <span class="font-bryndan text-xs text-gray-400">{{ selected.date }}</span>
        </div>

        <h1 class="mb-6 font-luckiest text-xl uppercase tracking-wide text-noir md:text-2xl">{{ selected.title }}</h1>

        <div class="rounded-md border border-dashed border-gray-200 bg-white p-6 md:p-8">
          <p
            v-for="(para, i) in selected.body.split('\\n\\n')"
            :key="i"
            class="mb-4 font-bryndan text-sm leading-relaxed text-gray-700 last:mb-0"
          >
            {{ para }}
          </p>
        </div>

      </div>
    </div>

  </main>
</template>
