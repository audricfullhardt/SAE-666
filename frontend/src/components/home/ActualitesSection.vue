<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const { t } = useI18n()

/**
 * Articles list – replace with API data when available.
 * Each article: { id, titleKey, excerptKey, image, href }
 */
const articles = ref([
  {
    id: 1,
    titleKey:   'home.actualites.articles.0.title',
    excerptKey: 'home.actualites.articles.0.excerpt',
    image: null,
    href: '#',
  },
  {
    id: 2,
    titleKey:   'home.actualites.articles.1.title',
    excerptKey: 'home.actualites.articles.1.excerpt',
    image: null,
    href: '#',
  },
  {
    id: 3,
    titleKey:   'home.actualites.articles.2.title',
    excerptKey: 'home.actualites.articles.2.excerpt',
    image: null,
    href: '#',
  },
])

const currentIndex = ref(0)

const currentArticle = computed(() => articles.value[currentIndex.value])

function prev() {
  currentIndex.value =
    (currentIndex.value - 1 + articles.value.length) % articles.value.length
}

function next() {
  currentIndex.value = (currentIndex.value + 1) % articles.value.length
}
</script>

<template>
  <section class="bg-menthe px-6 py-14">
    <div class="mx-auto max-w-3xl">
      <!-- Section title -->
      <h2 class="mb-8 font-luckiest text-3xl uppercase tracking-wide text-vert md:text-4xl">
        {{ t('home.actualites.title') }}
      </h2>

      <!-- Carousel -->
      <div class="relative flex items-center gap-3">
        <!-- Prev arrow -->
        <button
          @click="prev"
          aria-label="Article précédent"
          class="flex-shrink-0 rounded-full bg-white p-2 shadow transition hover:bg-gray-100 active:scale-95"
        >
          <ChevronLeft class="h-5 w-5 text-gray-700" />
        </button>

        <!-- Card -->
        <div class="flex-1 overflow-hidden rounded-2xl bg-white shadow-md">
          <!-- Article image -->
          <div class="h-44 w-full overflow-hidden bg-gray-200 md:h-60">
            <img
              v-if="currentArticle.image"
              :src="currentArticle.image"
              :alt="t(currentArticle.titleKey)"
              class="h-full w-full object-cover"
            />
            <div v-else class="flex h-full items-center justify-center">
              <span class="font-luckiest text-4xl text-gray-400">🦕</span>
            </div>
          </div>

          <!-- Article body -->
          <div class="p-5">
            <h3 class="mb-2 font-luckiest text-xl text-foret">
              {{ t(currentArticle.titleKey) }}
            </h3>
            <p class="mb-4 font-patrick text-sm leading-relaxed text-gray-600">
              {{ t(currentArticle.excerptKey) }}
            </p>
            <a
              :href="currentArticle.href"
              class="font-luckiest text-sm text-vert underline-offset-2 hover:underline"
            >
              {{ t('home.actualites.readMore') }}
            </a>
          </div>
        </div>

        <!-- Next arrow -->
        <button
          @click="next"
          aria-label="Article suivant"
          class="flex-shrink-0 rounded-full bg-white p-2 shadow transition hover:bg-gray-100 active:scale-95"
        >
          <ChevronRight class="h-5 w-5 text-gray-700" />
        </button>
      </div>

      <!-- Dots indicator -->
      <div class="mt-4 flex justify-center gap-2">
        <button
          v-for="(_, i) in articles"
          :key="i"
          :aria-label="`Article ${i + 1}`"
          :class="[
            'h-2 w-2 rounded-full transition-all',
            i === currentIndex ? 'w-5 bg-vert' : 'bg-gray-300',
          ]"
          @click="currentIndex = i"
        />
      </div>
    </div>
  </section>
</template>
