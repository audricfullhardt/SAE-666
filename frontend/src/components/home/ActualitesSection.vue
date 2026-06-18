<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const { t, tm } = useI18n()
const router = useRouter()

const images = [
  'https://images.unsplash.com/photo-1560148271-00b5e5850812?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1583144006017-74800d22ed08?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1633876204719-dd74580764ea?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1657660501312-6b019ad7776c?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1620836572268-90d2f46c0dbf?w=800&q=80&auto=format&fit=crop',
  'https://images.unsplash.com/photo-1583144006017-74800d22ed08?w=800&q=80&auto=format&fit=crop',
]

const tagColors = ['bg-bleu', 'bg-vert', 'bg-jaune', 'bg-rouge', 'bg-vert', 'bg-bleu']

const articles = computed(() =>
  tm('home.actualites.articles').map((a, i) => ({
    ...a,
    id: i,
    image: images[i],
    tagColor: tagColors[i] ?? 'bg-vert',
  }))
)

const currentIndex = ref(0)
const currentArticle = computed(() => articles.value[currentIndex.value])

function prev() {
  currentIndex.value = (currentIndex.value - 1 + articles.value.length) % articles.value.length
}
function next() {
  currentIndex.value = (currentIndex.value + 1) % articles.value.length
}

function goToArticle(id) {
  router.push({ path: '/actualites', query: { article: id } })
}
</script>

<template>
  <section class="bg-vert px-6 py-14">
    <div class="mx-auto max-w-5xl">
      <h2 class="mb-8 inline-block -rotate-[4deg] font-luckiest text-xl uppercase tracking-wide text-white md:text-2xl">
        {{ t('home.actualites.title') }}
      </h2>

      <!-- MOBILE: carousel -->
      <div class="md:hidden">
        <div class="flex items-center gap-2">
          <button @click="prev" aria-label="Article précédent" class="flex-shrink-0 p-2 transition active:scale-95">
            <ChevronLeft class="h-5 w-5 text-white" />
          </button>

          <div class="min-w-0 flex-1 cursor-pointer overflow-hidden rounded-lg text-left" @click="goToArticle(currentArticle.id)">
            <div class="h-48 w-full overflow-hidden rounded-t-lg">
              <img :src="currentArticle.image" :alt="currentArticle.title" class="h-full w-full object-cover" />
            </div>
            <div class="rounded-b-lg bg-white p-4">
              <div class="mb-2 flex items-center gap-2">
                <span :class="['inline-flex items-center justify-center px-3 py-1 font-luckiest text-xs uppercase leading-none text-white', currentArticle.tagColor]">{{ currentArticle.tag }}</span>
                <span class="font-bryndan text-xs text-gray-400">{{ currentArticle.date }}</span>
              </div>
              <h3 class="mb-1 font-luckiest text-base text-foret">{{ currentArticle.title }}</h3>
              <p class="mb-3 font-bryndan text-xs leading-relaxed text-gray-600">{{ currentArticle.excerpt }}</p>
              <button class="font-bryndan text-xs text-vert hover:underline" @click="goToArticle(currentArticle.id)">{{ t('home.actualites.readMore') }}</button>
            </div>
          </div>

          <button @click="next" aria-label="Article suivant" class="flex-shrink-0 p-2 transition active:scale-95">
            <ChevronRight class="h-5 w-5 text-white" />
          </button>
        </div>

        <!-- Dots -->
        <div class="mt-4 flex justify-center gap-2">
          <button
            v-for="(_, i) in articles"
            :key="i"
            :aria-label="`Article ${i + 1}`"
            :class="['h-2 rounded-full transition-all', i === currentIndex ? 'w-6 bg-vert' : 'w-2 bg-gray-300']"
            @click="currentIndex = i"
          />
        </div>
      </div>

      <!-- DESKTOP: 3-column grid -->
      <div class="hidden md:grid md:grid-cols-3 md:gap-6">
        <button
          v-for="article in articles.slice(0, 3)"
          :key="article.id"
          class="cursor-pointer overflow-hidden rounded-lg text-left transition hover:brightness-95 active:scale-[0.99]"
          @click="goToArticle(article.id)"
        >
          <div class="h-44 w-full overflow-hidden rounded-t-lg">
            <img :src="article.image" :alt="article.title" class="h-full w-full object-cover" />
          </div>
          <div class="rounded-b-lg bg-white p-4">
            <div class="mb-2 flex items-center gap-2">
              <span :class="['inline-flex items-center justify-center px-3 py-1 font-luckiest text-xs uppercase leading-none text-white', article.tagColor]">{{ article.tag }}</span>
              <span class="font-bryndan text-xs text-gray-400">{{ article.date }}</span>
            </div>
            <h3 class="mb-1 font-luckiest text-base text-foret">{{ article.title }}</h3>
            <p class="mb-3 font-bryndan text-xs leading-relaxed text-gray-600">{{ article.excerpt }}</p>
            <button class="font-bryndan text-xs text-vert hover:underline" @click.stop="goToArticle(article.id)">{{ t('home.actualites.readMore') }}</button>
          </div>
        </button>
      </div>
    </div>
  </section>
</template>
