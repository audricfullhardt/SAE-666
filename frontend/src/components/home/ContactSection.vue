<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/** Two contact card definitions */
const cards = [
  {
    icon: '🏢',
    titleKey: 'home.contact.entreprises.title',
    textKey:  'home.contact.entreprises.text',
    linkKey:  'home.contact.entreprises.link',
    href: '/contact?type=b2b',
  },
  {
    icon: '🎮',
    titleKey: 'home.contact.joueurs.title',
    textKey:  'home.contact.joueurs.text',
    linkKey:  'home.contact.joueurs.link',
    href: '/contact?type=b2c',
  },
]
</script>

<template>
  <section class="bg-gray-100 px-6 py-16">
    <div class="mx-auto max-w-md">
      <!-- Section title -->
      <h2 class="mb-10 text-center font-luckiest text-3xl uppercase tracking-wide text-foret md:text-4xl">
        {{ t('home.contact.title') }}
      </h2>

      <!-- Two cards with ou divider -->
      <div class="flex flex-col items-stretch gap-0">
        <template v-for="(card, i) in cards" :key="card.titleKey">
          <!-- Contact card -->
          <div class="flex items-start gap-4 rounded-2xl bg-white p-6 shadow-sm">
            <!-- Icon circle -->
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-100 text-2xl">
              {{ card.icon }}
            </div>
            <!-- Text -->
            <div>
              <p class="mb-1 font-luckiest text-base uppercase tracking-wide text-foret">
                {{ t(card.titleKey) }}
              </p>
              <p class="mb-3 font-bryndan text-sm leading-relaxed text-gray-600">
                {{ t(card.textKey) }}
              </p>
              <a
                :href="card.href"
                class="font-bryndan text-sm font-semibold text-foret underline underline-offset-2 hover:text-vert"
              >
                {{ t(card.linkKey) }}
              </a>
            </div>
          </div>

          <!-- ou divider between cards -->
          <div v-if="i < cards.length - 1" class="flex items-center justify-center py-3">
            <span class="font-bryndan text-sm text-gray-400">{{ t('home.contact.or') }}</span>
          </div>
        </template>
      </div>

      <!-- DISCUTONS CTA -->
      <div class="mt-8 flex justify-center">
        <a
          href="/contact"
          class="rounded-full bg-rouge px-14 py-3 font-luckiest text-xl uppercase tracking-widest
                 text-white shadow-lg transition hover:brightness-110 active:scale-95"
        >
          {{ t('home.contact.cta') }}
        </a>
      </div>
    </div>
  </section>
</template>
