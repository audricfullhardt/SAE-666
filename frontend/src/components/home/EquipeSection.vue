<script setup>
import { useI18n } from 'vue-i18n'
import dinoImg from '@/assets/dino.svg'

const { t } = useI18n()

/**
 * members: flat list rendered in a staggered 2-column grid.
 * badgeClass must be full strings for Tailwind JIT.
 */
const members = [
  { name: 'Audric', roleKey: 'home.equipe.roles.backend',   photo: null, badgeClass: 'bg-rouge' },
  { name: 'Lucy',   roleKey: 'home.equipe.roles.frontend',  photo: null, badgeClass: 'bg-vert'  },
  { name: 'Théo',   roleKey: 'home.equipe.roles.com',       photo: null, badgeClass: 'bg-jaune' },
  { name: 'Arthur', roleKey: 'home.equipe.roles.com',       photo: null, badgeClass: 'bg-jaune' },
  { name: 'Prénom', roleKey: 'home.equipe.roles.graphiste', photo: null, badgeClass: 'bg-bleu'  },
  { name: 'Prénom', roleKey: 'home.equipe.roles.graphiste', photo: null, badgeClass: 'bg-bleu'  },
]
</script>

<template>
  <section class="bg-white px-6 py-14">
    <div class="mx-auto max-w-2xl">
      <!-- Section title -->
      <h2 class="mb-10 text-center font-luckiest text-3xl uppercase tracking-wide text-rouge md:text-4xl">
        {{ t('home.equipe.title') }}
      </h2>

      <!--
        Staggered 2-column grid matching the mockup:
        col-1 (odd items): mt-0   — sits higher
        col-2 (even items): mt-10 — sits lower
      -->
      <div class="grid grid-cols-2 gap-x-6 gap-y-8">
        <div
          v-for="(member, i) in members"
          :key="member.name + member.roleKey + i"
          :class="['flex flex-col items-center gap-2', i % 2 === 1 ? 'mt-10' : 'mt-0']"
        >
          <!-- floating dino icon top-right of photo -->
          <div class="relative">
            <div v-if="member.photo" class="h-32 w-32 overflow-hidden rounded-xl shadow-md">
              <img :src="member.photo" :alt="member.name" class="h-full w-full object-cover" />
            </div>
            <div
              v-else
              :class="[
                'flex h-32 w-32 items-center justify-center rounded-xl text-4xl font-luckiest text-white shadow-md',
                member.badgeClass,
              ]"
            >
              {{ member.name[0] }}
            </div>
            <!-- pixel dino accent -->
            <img
              :src="dinoImg"
              alt=""
              aria-hidden="true"
              class="absolute -right-4 -top-4 h-9 w-9 object-contain"
            />
          </div>

          <!-- Name badge -->
          <span
            :class="[
              'rounded-full px-4 py-1 font-luckiest text-xs uppercase tracking-wide text-white',
              member.badgeClass,
            ]"
          >
            {{ member.name }}
          </span>

          <!-- Role -->
          <span class="text-center font-bryndan text-xs text-gray-500">
            {{ t(member.roleKey) }}
          </span>
        </div>
      </div>
    </div>
  </section>
</template>
