<script setup>
import { useI18n } from 'vue-i18n'
import dinoSvg    from '@/assets/dino_yellow.png'
import dinoGreen  from '@/assets/dino_green.png'
import dinoBleu   from '@/assets/dino_blue.png'

const { t } = useI18n()
</script>

<template>
  <section class="border-t-2 border-dashed border-rouge bg-transparent px-6 py-12">
    <div class="mx-auto flex max-w-3xl items-center gap-6">

      <div class="flex shrink-0 flex-col items-center">
        <img :src="dinoGreen" alt="" aria-hidden="true" class="h-20 w-auto [image-rendering:pixelated]" />
        <img :src="dinoBleu"  alt="" aria-hidden="true" class="h-20 w-auto [image-rendering:pixelated]" />
        <img :src="dinoSvg"   alt="" aria-hidden="true" class="h-20 w-auto [image-rendering:pixelated]" />
      </div>

      <div class="flex flex-1 flex-col items-end gap-4 text-right">
        <h2 class="inline-block rotate-[4deg] font-luckiest text-xl uppercase tracking-wide text-noir md:text-2xl">
          {{ t('home.contact.title') }}
        </h2>
        <p class="font-bryndan text-xs leading-relaxed text-noir md:text-sm">
          {{ t('home.contact.body') }}
        </p>
        <a
          href="/contact"
          class="inline-flex items-center rounded-full bg-rouge px-6 py-2 font-luckiest text-base uppercase leading-none tracking-wide text-white shadow-[0_4px_0_#C42B20] transition hover:brightness-110 active:scale-95"
        >
          <span class="translate-y-[3px]">{{ t('home.contact.cta') }}</span>
        </a>
      </div>

    </div>
  </section>
</template>
