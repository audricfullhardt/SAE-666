<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import dinoYellow from '@/assets/dino_yellow.png'
import dinoGreen from '@/assets/dino_green.png'

const { t } = useI18n()
const auth = useAuthStore()

const players = ref([])
const loading = ref(true)
const error = ref(null)

const medalColors = {
  1: { bg: '#FFB000', label: 'or' },
  2: { bg: '#9CA3AF', label: 'argent' },
  3: { bg: '#CD7F32', label: 'bronze' },
}

const podium = computed(() => {
  const [first, second, third] = players.value
  return { first, second, third }
})

const hasPlayers = computed(() => players.value.length > 0)

function initial(username) {
  return (username || '?').charAt(0).toUpperCase()
}

function isCurrentUser(username) {
  return auth.username && auth.username === username
}

async function fetchLeaderboard() {
  loading.value = true
  error.value = null
  try {
    const res = await fetch('/api/leaderboard')
    if (!res.ok) throw new Error('Erreur serveur')
    players.value = await res.json()
  } catch (e) {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(fetchLeaderboard)
</script>

<template>
  <div>
    <!-- HERO -->
    <section class="bg-foret px-6 py-16 text-center">
      <div class="mx-auto max-w-3xl">
        <h1 class="font-luckiest text-5xl uppercase tracking-wide text-white sm:text-6xl md:text-7xl">
          {{ t('pages.classement.title') }}
        </h1>
        <p class="mt-3 font-patrick text-xl text-vert sm:text-2xl">
          {{ t('pages.classement.subtitle') }}
        </p>
        <img
          :src="dinoYellow"
          alt=""
          aria-hidden="true"
          class="mx-auto mt-6 h-28 w-28 object-contain [image-rendering:pixelated] sm:h-32 sm:w-32"
        />
      </div>
    </section>

    <!-- LOADING -->
    <section v-if="loading" class="flex items-center justify-center bg-menthe px-6 py-24">
      <div class="h-14 w-14 animate-spin rounded-full border-4 border-vert border-t-transparent" />
    </section>

    <!-- ERROR -->
    <section v-else-if="error" class="bg-menthe px-6 py-24 text-center">
      <p class="font-patrick text-2xl text-rouge">{{ t('pages.classement.error') }}</p>
      <button
        class="mt-6 rounded-full bg-vert px-8 py-3 font-luckiest uppercase tracking-wide text-white transition hover:brightness-110"
        @click="fetchLeaderboard"
      >
        {{ t('pages.classement.retry') }}
      </button>
    </section>

    <!-- EMPTY -->
    <section v-else-if="!hasPlayers" class="bg-menthe px-6 py-24 text-center">
      <img
        :src="dinoGreen"
        alt=""
        aria-hidden="true"
        class="mx-auto h-28 w-28 object-contain [image-rendering:pixelated]"
      />
      <p class="mt-6 font-patrick text-2xl text-foret">
        {{ t('pages.classement.empty') }}
      </p>
    </section>

    <template v-else>
      <!-- PODIUM -->
      <section class="bg-menthe px-6 py-16">
        <div class="mx-auto flex max-w-2xl items-end justify-center gap-3 sm:gap-6">
          <!-- 2ème -->
          <div v-if="podium.second" class="flex w-1/3 flex-col items-center pb-6">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full font-luckiest text-2xl text-white sm:h-20 sm:w-20"
              :style="{ backgroundColor: medalColors[2].bg }"
            >
              {{ initial(podium.second.username) }}
            </div>
            <p class="mt-2 text-center font-luckiest text-sm text-foret sm:text-base">
              {{ podium.second.username }}
            </p>
            <p class="font-nunito text-xs text-gray-600">{{ podium.second.wins }} {{ t('pages.classement.victories') }}</p>
            <div
              class="mt-2 flex w-full items-center justify-center rounded-t-lg py-4 font-luckiest text-2xl text-white"
              :style="{ backgroundColor: medalColors[2].bg }"
            >
              2
            </div>
          </div>

          <!-- 1er -->
          <div v-if="podium.first" class="flex w-1/3 flex-col items-center">
            <span class="text-3xl">👑</span>
            <div
              class="flex h-20 w-20 items-center justify-center rounded-full font-luckiest text-3xl text-white sm:h-24 sm:w-24"
              :style="{ backgroundColor: medalColors[1].bg }"
            >
              {{ initial(podium.first.username) }}
            </div>
            <p class="mt-2 text-center font-luckiest text-base text-foret sm:text-lg">
              {{ podium.first.username }}
            </p>
            <p class="font-nunito text-xs text-gray-600">{{ podium.first.wins }} {{ t('pages.classement.victories') }}</p>
            <div
              class="mt-2 flex w-full items-center justify-center rounded-t-lg py-8 font-luckiest text-3xl text-white"
              :style="{ backgroundColor: medalColors[1].bg }"
            >
              1
            </div>
          </div>

          <!-- 3ème -->
          <div v-if="podium.third" class="flex w-1/3 flex-col items-center pb-10">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full font-luckiest text-2xl text-white sm:h-20 sm:w-20"
              :style="{ backgroundColor: medalColors[3].bg }"
            >
              {{ initial(podium.third.username) }}
            </div>
            <p class="mt-2 text-center font-luckiest text-sm text-foret sm:text-base">
              {{ podium.third.username }}
            </p>
            <p class="font-nunito text-xs text-gray-600">{{ podium.third.wins }} {{ t('pages.classement.victories') }}</p>
            <div
              class="mt-2 flex w-full items-center justify-center rounded-t-lg py-2 font-luckiest text-2xl text-white"
              :style="{ backgroundColor: medalColors[3].bg }"
            >
              3
            </div>
          </div>
        </div>
      </section>

      <!-- TABLEAU COMPLET -->
      <section class="bg-white px-6 py-16">
        <div class="mx-auto max-w-4xl">
          <h2 class="mb-8 text-center font-luckiest text-3xl uppercase tracking-wide text-foret sm:text-4xl">
            {{ t('pages.classement.allPlayers') }}
          </h2>

          <!-- Desktop table -->
          <div class="hidden overflow-hidden rounded-xl border-2 border-menthe md:block">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-foret font-luckiest text-sm uppercase tracking-wide text-white">
                  <th class="px-4 py-3 text-left">{{ t('pages.classement.table.rank') }}</th>
                  <th class="px-4 py-3 text-left">{{ t('pages.classement.table.player') }}</th>
                  <th class="px-4 py-3 text-center">{{ t('pages.classement.table.wins') }}</th>
                  <th class="px-4 py-3 text-center">{{ t('pages.classement.table.losses') }}</th>
                  <th class="px-4 py-3 text-center">{{ t('pages.classement.table.games') }}</th>
                  <th class="px-4 py-3 text-left">{{ t('pages.classement.table.winrate') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="player in players"
                  :key="player.username"
                  class="border-t border-menthe"
                  :class="isCurrentUser(player.username) ? 'bg-vert/10' : 'bg-white'"
                >
                  <td class="px-4 py-3">
                    <span
                      v-if="medalColors[player.rank]"
                      class="inline-flex h-8 w-8 items-center justify-center rounded-full font-luckiest text-sm text-white"
                      :style="{ backgroundColor: medalColors[player.rank].bg }"
                    >
                      {{ player.rank }}
                    </span>
                    <span v-else class="font-nunito font-bold text-gray-400">{{ player.rank }}</span>
                  </td>
                  <td class="px-4 py-3 font-luckiest text-foret">{{ player.username }}</td>
                  <td class="px-4 py-3 text-center font-nunito font-bold text-vert">{{ player.wins }}</td>
                  <td class="px-4 py-3 text-center font-nunito font-bold text-rouge">{{ player.losses }}</td>
                  <td class="px-4 py-3 text-center font-nunito text-gray-600">{{ player.total }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                      <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-gray-200">
                        <div
                          class="h-full rounded-full"
                          :class="player.winrate > 50 ? 'bg-vert' : 'bg-rouge'"
                          :style="{ width: player.winrate + '%' }"
                        />
                      </div>
                      <span class="w-12 font-nunito text-sm text-gray-600">{{ player.winrate }}%</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile cards -->
          <div class="space-y-3 md:hidden">
            <div
              v-for="player in players"
              :key="player.username"
              class="rounded-xl border-2 p-4"
              :class="isCurrentUser(player.username) ? 'border-vert bg-vert/10' : 'border-menthe bg-white'"
            >
              <div class="flex items-center gap-3">
                <span
                  v-if="medalColors[player.rank]"
                  class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full font-luckiest text-white"
                  :style="{ backgroundColor: medalColors[player.rank].bg }"
                >
                  {{ player.rank }}
                </span>
                <span v-else class="w-9 shrink-0 text-center font-nunito font-bold text-gray-400">
                  {{ player.rank }}
                </span>
                <span class="flex-1 font-luckiest text-lg text-foret">{{ player.username }}</span>
              </div>

              <div class="mt-3 flex items-center gap-4 font-nunito text-sm">
                <span class="font-bold text-vert">{{ player.wins }}{{ t('pages.classement.winsShort') }}</span>
                <span class="font-bold text-rouge">{{ player.losses }}{{ t('pages.classement.lossesShort') }}</span>
                <span class="text-gray-600">{{ player.total }} {{ t('pages.classement.games') }}</span>
              </div>

              <div class="mt-2 flex items-center gap-2">
                <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-gray-200">
                  <div
                    class="h-full rounded-full"
                    :class="player.winrate > 50 ? 'bg-vert' : 'bg-rouge'"
                    :style="{ width: player.winrate + '%' }"
                  />
                </div>
                <span class="w-12 font-nunito text-sm text-gray-600">{{ player.winrate }}%</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>

    <!-- CTA -->
    <section class="bg-vert px-6 py-16 text-center">
      <h2 class="font-luckiest text-3xl uppercase tracking-wide text-white sm:text-4xl">
        {{ t('pages.classement.ctaTitle') }}
      </h2>
      <RouterLink
        to="/game"
        class="mt-6 inline-block rounded-full bg-jaune px-10 py-4 font-luckiest text-xl uppercase tracking-wide text-foret transition hover:brightness-110"
      >
        {{ t('pages.classement.ctaButton') }}
      </RouterLink>
    </section>
  </div>
</template>
