<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const b2bFormHref  = 'mailto:contact@dinomania.fr?subject=B2B'
const b2cFormHref  = 'mailto:contact@dinomania.fr?subject=Joueur'
</script>

<template>
  <section class="bg-gray-100 px-6 py-16">
    <div class="mx-auto max-w-2xl">
      <!-- Section title -->
      <h2 class="mb-10 text-center font-luckiest text-3xl uppercase tracking-wide text-foret md:text-4xl">
        {{ t('home.contact.title') }}
      </h2>

      <div class="flex flex-col items-center gap-4">
        <!-- Card: entreprises -->
        <div class="w-full rounded-2xl bg-white p-6 shadow-sm">
          <div class="flex items-start gap-4">
            <!-- avatar circle -->
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xl">
              🏢
            </div>
            <div class="flex flex-col gap-1">
              <p class="font-luckiest text-base uppercase tracking-wide text-foret">
                {{ t('home.contact.entreprises.title') }}
              </p>
              <p class="font-bryndan text-sm text-gray-600">
                {{ t('home.contact.entreprises.text') }}
              </p>
              <a :href="b2bFormHref" class="mt-1 font-bryndan text-sm text-vert underline underline-offset-2">
                {{ t('home.contact.entreprises.link') }}
              </a>
            </div>
          </div>
        </div>

        <!-- Divider -->
        <p class="font-luckiest text-lg uppercase tracking-widest text-gray-400">
          {{ t('home.contact.or') }}
        </p>

        <!-- Card: joueurs -->
        <div class="w-full rounded-2xl bg-white p-6 shadow-sm">
          <div class="flex items-start gap-4">
            <!-- avatar circle -->
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xl">
              🎮
            </div>
            <div class="flex flex-col gap-1">
              <p class="font-luckiest text-base uppercase tracking-wide text-foret">
                {{ t('home.contact.joueurs.title') }}
              </p>
              <p class="font-bryndan text-sm text-gray-600">
                {{ t('home.contact.joueurs.text') }}
              </p>
              <a :href="b2cFormHref" class="mt-1 font-bryndan text-sm text-vert underline underline-offset-2">
                {{ t('home.contact.joueurs.link') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
