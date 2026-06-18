<script setup>
import { useI18n } from 'vue-i18n'
import dinoBleu  from '@/assets/dino_blue.png'
import dinoJaune from '@/assets/dino_yellow.png'
import dinoRouge from '@/assets/dino_red.png'
import photoAudric  from '@/assets/audric.png'
import photoLucy    from '@/assets/lucy.png'
import photoTheoL   from '@/assets/theo_laine.png'
import photoTheoM   from '@/assets/theo_metens.jpg'
import photoArthur  from '@/assets/arthur.png'
import photoValou   from '@/assets/valou.png'
import photoTessa   from '@/assets/tessa.png'

const { t } = useI18n()

const poleColorMap = {
  dev:      { titleClass: 'text-bleu',  borderClass: 'border-bleu',  badgeClass: 'bg-bleu',  dinoSrc: dinoBleu  },
  com:      { titleClass: 'text-jaune', borderClass: 'border-jaune', badgeClass: 'bg-jaune', dinoSrc: dinoJaune },
  creation: { titleClass: 'text-rouge', borderClass: 'border-rouge', badgeClass: 'bg-rouge', dinoSrc: dinoRouge },
}

const poles = [
  {
    id: 'dev',
    members: [
      { name: 'Audric', roleKey: 'home.equipe.roles.backend',  photo: photoAudric },
      { name: 'Lucy',   roleKey: 'home.equipe.roles.frontend', photo: photoLucy   },
    ],
  },
  {
    id: 'com',
    members: [
      { name: 'Théo',   roleKey: 'home.equipe.roles.com', photo: photoTheoL  },
      { name: 'Arthur', roleKey: 'home.equipe.roles.com', photo: photoArthur },
      { name: 'Valou',  roleKey: 'home.equipe.roles.com', photo: photoValou  },
      { name: 'Théo',   roleKey: 'home.equipe.roles.com', photo: photoTheoM  },
    ],
  },
  {
    id: 'creation',
    members: [
      { name: 'Tessa',   roleKey: 'home.equipe.roles.graphiste', photo: photoTessa },
      { name: 'Killian', roleKey: 'home.equipe.roles.graphiste', photo: null       },
    ],
  },
]
</script>

<template>
  <section class="bg-transparent px-6 py-14">
    <div class="mx-auto max-w-5xl">

      <!-- Title: right-aligned, black -->
      <h2 class="mb-8 inline-block -rotate-[4deg] text-right font-luckiest text-xl uppercase tracking-wide text-noir md:text-2xl">
        {{ t('home.equipe.title') }}
      </h2>

      <!--
        Mobile: stacked (dev, com, création)
        Desktop: 2-col grid — left col: dev (row 1) + création (row 2) | right col: com (rows 1-2)
      -->
      <div class="flex flex-col gap-8 md:grid md:grid-cols-2 md:items-stretch md:gap-6">

        <!-- DEV — left col row 1 on desktop -->
        <div class="md:col-start-1 md:row-start-1">
          <div class="flex items-center gap-2 pl-1">
            <img :src="poleColorMap.dev.dinoSrc" alt="" aria-hidden="true" class="h-12 w-12 object-contain [image-rendering:pixelated]" />
            <h3 :class="['font-luckiest text-base uppercase tracking-wide', poleColorMap.dev.titleClass]">
              {{ t('home.equipe.poles.dev') }}
            </h3>
          </div>
          <div :class="['mt-1 rounded-md border-2 border-dashed p-4', poleColorMap.dev.borderClass]">
            <div class="grid grid-cols-2 gap-3">
              <div v-for="member in poles[0].members" :key="member.name" class="flex flex-col items-center gap-4">
                <div class="relative w-full rounded-lg" style="aspect-ratio: 3/4;">
                  <img v-if="member.photo" :src="member.photo" :alt="member.name" class="h-full w-full rounded-lg object-cover object-top" />
                  <div v-else class="h-full w-full rounded-lg bg-gray-200" />
                  <div class="absolute inset-x-0 bottom-0 flex translate-y-1/2 justify-center">
                    <span :class="['inline-flex items-center justify-center px-4 py-1 font-luckiest text-base uppercase leading-none text-white', poleColorMap.dev.badgeClass]">
                      {{ member.name }}
                    </span>
                  </div>
                </div>
                <span class="text-center font-bryndan text-xs text-gray-600 md:text-sm">{{ t(member.roleKey) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- COM — right col rows 1-2 on desktop -->
        <div class="flex flex-col md:col-start-2 md:row-start-1 md:row-span-2">
          <div class="flex items-center gap-2 pl-1">
            <img :src="poleColorMap.com.dinoSrc" alt="" aria-hidden="true" class="h-12 w-12 object-contain [image-rendering:pixelated]" />
            <h3 :class="['font-luckiest text-base uppercase tracking-wide', poleColorMap.com.titleClass]">
              {{ t('home.equipe.poles.com') }}
            </h3>
          </div>
          <div :class="['mt-1 flex-1 rounded-md border-2 border-dashed p-4', poleColorMap.com.borderClass]">
            <div class="grid h-full grid-cols-2 grid-rows-2 content-between gap-x-3 gap-y-12">
              <div v-for="member in poles[1].members" :key="member.name + member.roleKey" class="flex flex-col items-center gap-4">
                <div class="relative w-full rounded-lg" style="aspect-ratio: 3/4;">
                  <img v-if="member.photo" :src="member.photo" :alt="member.name" class="h-full w-full rounded-lg object-cover object-top" />
                  <div v-else class="h-full w-full rounded-lg bg-gray-200" />
                  <div class="absolute inset-x-0 bottom-0 flex translate-y-1/2 justify-center">
                    <span :class="['inline-flex items-center justify-center px-4 py-1 font-luckiest text-base uppercase leading-none text-white', poleColorMap.com.badgeClass]">
                      {{ member.name }}
                    </span>
                  </div>
                </div>
                <span class="text-center font-bryndan text-xs text-gray-600 md:text-sm">{{ t(member.roleKey) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- CRÉATION — left col row 2 on desktop -->
        <div class="flex flex-col md:col-start-1 md:row-start-2">
          <div class="flex items-center gap-2 pl-1">
            <img :src="poleColorMap.creation.dinoSrc" alt="" aria-hidden="true" class="h-12 w-12 object-contain [image-rendering:pixelated]" />
            <h3 :class="['font-luckiest text-base uppercase tracking-wide', poleColorMap.creation.titleClass]">
              {{ t('home.equipe.poles.creation') }}
            </h3>
          </div>
          <div :class="['mt-1 rounded-md border-2 border-dashed p-4', poleColorMap.creation.borderClass]">
            <div class="grid grid-cols-2 gap-3">
              <div v-for="member in poles[2].members" :key="member.name" class="flex flex-col items-center gap-4">
                <div class="relative w-full rounded-lg" style="aspect-ratio: 3/4;">
                  <img v-if="member.photo" :src="member.photo" :alt="member.name" class="h-full w-full rounded-lg object-cover object-top" />
                  <div v-else class="h-full w-full rounded-lg bg-gray-200" />
                  <div class="absolute inset-x-0 bottom-0 flex translate-y-1/2 justify-center">
                    <span :class="['inline-flex items-center justify-center px-4 py-1 font-luckiest text-base uppercase leading-none text-white', poleColorMap.creation.badgeClass]">
                      {{ member.name }}
                    </span>
                  </div>
                </div>
                <span class="text-center font-bryndan text-xs text-gray-600 md:text-sm">{{ t(member.roleKey) }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>
