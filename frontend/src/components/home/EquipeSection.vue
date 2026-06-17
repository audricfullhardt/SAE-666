<script setup>
import { useI18n } from 'vue-i18n'
import dinoImg from '@/assets/dino.svg'

const { t } = useI18n()

/**
 * Static pole color maps – full class strings so Tailwind purge keeps them.
 * titleClass  : section title colour
 * borderClass : dashed border around the pole card
 * badgeClass  : member name pill background
 */
const poleColorMap = {
  dev:      { titleClass: 'text-rouge', borderClass: 'border-rouge', badgeClass: 'bg-rouge'  },
  com:      { titleClass: 'text-jaune', borderClass: 'border-jaune', badgeClass: 'bg-jaune'  },
  creation: { titleClass: 'text-bleu',  borderClass: 'border-bleu',  badgeClass: 'bg-bleu'   },
}

const poles = [
  {
    id: 'dev',
    members: [
      { name: 'Audric', roleKey: 'home.equipe.roles.backend',   photo: null },
      { name: 'Lucy',   roleKey: 'home.equipe.roles.frontend',  photo: null },
    ],
  },
  {
    id: 'com',
    members: [
      { name: 'Théo',   roleKey: 'home.equipe.roles.com', photo: null },
      { name: 'Arthur', roleKey: 'home.equipe.roles.com', photo: null },
    ],
  },
  {
    id: 'creation',
    members: [
      { name: 'Prénom', roleKey: 'home.equipe.roles.graphiste', photo: null },
      { name: 'Prénom', roleKey: 'home.equipe.roles.graphiste', photo: null },
    ],
  },
]
</script>

<template>
  <section class="bg-white px-6 py-14">
    <div class="mx-auto max-w-3xl">
      <!-- Section title -->
      <h2 class="mb-10 text-center font-luckiest text-3xl uppercase tracking-wide text-gray-800 md:text-4xl">
        {{ t('home.equipe.title') }}
      </h2>

      <!-- Poles – stack on mobile, 3-col on large screens -->
      <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:gap-6">
        <div
          v-for="pole in poles"
          :key="pole.id"
          :class="[
            'flex-1 rounded-xl border-2 border-dashed p-5',
            poleColorMap[pole.id].borderClass,
          ]"
        >
          <!-- Pole heading with dino pixel icon -->
          <div class="mb-5 flex items-center gap-2">
            <img :src="dinoImg" alt="" class="h-8 w-8 object-contain" aria-hidden="true" />
            <h3 :class="['font-luckiest text-lg uppercase tracking-wide', poleColorMap[pole.id].titleClass]">
              {{ t(`home.equipe.poles.${pole.id}`) }}
            </h3>
          </div>

          <!-- Members grid -->
          <div class="grid grid-cols-2 gap-4">
            <div
              v-for="member in pole.members"
              :key="member.name + member.roleKey"
              class="flex flex-col items-center gap-2"
            >
              <!-- Photo or initial avatar -->
              <div v-if="member.photo" class="h-24 w-24 overflow-hidden rounded-lg">
                <img :src="member.photo" :alt="member.name" class="h-full w-full object-cover" />
              </div>
              <div
                v-else
                :class="[
                  'flex h-24 w-24 items-center justify-center rounded-lg text-3xl font-luckiest text-white',
                  poleColorMap[pole.id].badgeClass,
                ]"
              >
                {{ member.name[0] }}
              </div>

              <!-- Name badge -->
              <span
                :class="[
                  'rounded-full px-3 py-0.5 font-luckiest text-xs text-white uppercase tracking-wide',
                  poleColorMap[pole.id].badgeClass,
                ]"
              >
                {{ member.name }}
              </span>

              <!-- Role -->
              <span class="text-center font-patrick text-xs text-gray-600">
                {{ t(member.roleKey) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
