<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const { t } = useI18n()

const articles = ref([
  { id: 1, titleKey: 'home.actualites.articles.0.title', excerptKey: 'home.actualites.articles.0.excerpt', image: null, href: '#' },
  { id: 2, titleKey: 'home.actualites.articles.1.title', excerptKey: 'home.actualites.articles.1.excerpt', image: null, href: '#' },
  { id: 3, titleKey: 'home.actualites.articles.2.title', excerptKey: 'home.actualites.articles.2.excerpt', image: null, href: '#' },
])

const currentIndex = ref(0)
const currentArticle = computed(() => articles.value[currentIndex.value])

function prev() {
  currentIndex.value = (currentIndex.value - 1 + articles.value.length) % articles.value.length
}
function next() {
  currentIndex.value = (currentIndex.value + 1) % articles.value.length
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

          <div class="min-w-0 flex-1 overflow-hidden rounded-2xl">
            <div class="h-48 w-full overflow-hidden bg-gray-200">
              <img v-if="currentArticle.image" :src="currentArticle.image" :alt="t(currentArticle.titleKey)" class="h-full w-full object-cover" />
              <div v-else class="flex h-full w-full items-center justify-center bg-gray-300">
                <span class="text-5xl">🦕</span>
              </div>
            </div>
            <div class="bg-white p-4">
              <h3 class="mb-1 font-luckiest text-base text-foret">{{ t(currentArticle.titleKey) }}</h3>
              <p class="mb-3 font-bryndan text-xs leading-relaxed text-gray-600 md:text-sm">{{ t(currentArticle.excerptKey) }}</p>
              <a :href="currentArticle.href" class="font-bryndan text-xs text-vert hover:underline md:text-sm">{{ t('home.actualites.readMore') }}</a>
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
        <div
          v-for="article in articles"
          :key="article.id"
          class="overflow-hidden rounded-2xl"
        >
          <div class="h-44 w-full overflow-hidden bg-gray-200">
            <img v-if="article.image" :src="article.image" :alt="t(article.titleKey)" class="h-full w-full object-cover" />
            <div v-else class="flex h-full w-full items-center justify-center bg-gray-300">
              <span class="text-5xl">🦕</span>
            </div>
          </div>
          <div class="bg-white p-4">
            <h3 class="mb-1 font-luckiest text-base text-foret">{{ t(article.titleKey) }}</h3>
            <p class="mb-3 font-bryndan text-xs leading-relaxed text-gray-600 md:text-sm">{{ t(article.excerptKey) }}</p>
            <a :href="article.href" class="font-bryndan text-xs text-vert hover:underline md:text-sm">{{ t('home.actualites.readMore') }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
