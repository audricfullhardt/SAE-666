<script setup>
import { useI18n } from 'vue-i18n'
import dinoImg from '@/assets/dino.svg'

const { t } = useI18n()

const poleColorMap = {
  dev:      { titleClass: 'text-rouge', borderClass: 'border-rouge', badgeClass: 'bg-rouge' },
  com:      { titleClass: 'text-jaune', borderClass: 'border-jaune', badgeClass: 'bg-jaune' },
  creation: { titleClass: 'text-bleu',  borderClass: 'border-bleu',  badgeClass: 'bg-bleu'  },
}

const poles = [
  {
    id: 'dev',
    members: [
      { name: 'Audric', roleKey: 'home.equipe.roles.backend',  photo: null },
      { name: 'Lucy',   roleKey: 'home.equipe.roles.frontend', photo: null },
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
      <!-- Mixed-font title -->
      <h2 class="mb-10 text-center">
        <span class="font-bryndan text-3xl text-rouge">Notre </span>
        <span class="font-luckiest text-3xl uppercase tracking-wide text-rouge">Équipe</span>
      </h2>

      <!-- Poles: stacked mobile, 3-col desktop -->
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
        <div
          v-for="pole in poles"
          :key="pole.id"
          :class="['flex-1 rounded-xl border-2 border-dashed p-5', poleColorMap[pole.id].borderClass]"
        >
          <!-- Pole heading -->
          <div class="mb-4 flex items-center gap-2">
            <img :src="dinoImg" alt="" aria-hidden="true" class="h-7 w-7 object-contain" />
            <h3 :class="['font-luckiest text-base uppercase tracking-wide', poleColorMap[pole.id].titleClass]">
              {{ t(`home.equipe.poles.${pole.id}`) }}
            </h3>
          </div>

          <!-- 2-col member grid -->
          <div class="grid grid-cols-2 gap-4">
            <div
              v-for="member in pole.members"
              :key="member.name + member.roleKey"
              class="flex flex-col items-center gap-2"
            >
              <!-- Photo or coloured placeholder -->
              <div v-if="member.photo" class="h-24 w-full overflow-hidden rounded-lg">
                <img :src="member.photo" :alt="member.name" class="h-full w-full object-cover" />
              </div>
              <div
                v-else
                :class="['h-24 w-full rounded-lg', poleColorMap[pole.id].badgeClass]"
              />

              <!-- Name badge -->
              <span :class="['rounded-full px-3 py-0.5 font-luckiest text-xs uppercase tracking-wide text-white', poleColorMap[pole.id].badgeClass]">
                {{ member.name }}
              </span>

              <!-- Role -->
              <span class="text-center font-bryndan text-xs text-gray-600">
                {{ t(member.roleKey) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
