<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import gsap from 'gsap'
import { CornerDownLeft } from 'lucide-vue-next'
import { useGameStore } from '@/stores/game'
import { useMercure } from '@/composables/useMercure'
import dinoSprite from '@/assets/dino_green.png'

const route = useRoute()
const router = useRouter()
const game = useGameStore()

const code = route.params.code
const haloRef = ref(null)
let redirected = false

const result = computed(() => game.lastResult)
const winner = computed(() => result.value?.winner ?? null)
const loser = computed(() => result.value?.loser ?? null)

const iWon = computed(
  () => !!winner.value && game.currentPlayer && winner.value.id === game.currentPlayer.id,
)

function initial(name) {
  return (name || '?').charAt(0).toUpperCase()
}

onMounted(async () => {
  subscribe()

  // Rafraîchit les positions (ne fait rien pour un invité sans JWT, le store garde lastResult).
  await game.fetchSession(code).catch(() => null)

  if (haloRef.value) {
    gsap.fromTo(
      haloRef.value,
      { scale: 0.6, opacity: 0 },
      { scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(1.7)' },
    )
    gsap.to(haloRef.value, {
      scale: 1.06,
      duration: 1.1,
      ease: 'sine.inOut',
      repeat: -1,
      yoyo: true,
    })
  }
})

function goToBoard() {
  if (redirected) return
  redirected = true
  game.setActiveMinigame(null)
  router.push(`/game/minigame/${code}`)
}

// Clic manuel : déclenche le retour au plateau pour toute la session, puis redirige.
async function backToBoard() {
  if (redirected) return
  try {
    await game.returnToBoard(code)
  } catch {
    // Même en cas d'échec du broadcast, on ramène localement le joueur au plateau.
  }
  goToBoard()
}

// Un autre joueur a déclenché le retour au plateau → on suit.
function onMercure({ event }) {
  if (event === 'return_to_board') {
    goToBoard()
  }
}

const { subscribe, unsubscribe } = useMercure(`game/${code}`, onMercure)

onUnmounted(() => {
  unsubscribe()
})
</script>

<template>
  <main class="flex min-h-svh flex-col items-center justify-center gap-5 bg-foret px-6 py-10 text-center">
    <h1
      class="font-luckiest text-5xl tracking-wide"
      :class="iWon ? 'text-jaune' : 'text-rouge'"
    >
      {{ iWon ? 'VICTOIRE !' : 'DÉFAITE…' }}
    </h1>

    <div ref="haloRef" class="relative flex items-center justify-center">
      <span
        class="absolute h-32 w-32 rounded-full blur-2xl"
        :class="iWon ? 'bg-vert/60' : 'bg-rouge/50'"
      />
      <img :src="dinoSprite" alt="" class="relative h-28 w-auto drop-shadow-xl [image-rendering:pixelated]" />
    </div>

    <p v-if="winner" class="font-patrick text-2xl text-white">
      {{ winner.username }} remporte le duel
    </p>

    <p v-if="result" class="font-nunito text-sm text-white/60">
      Mini-jeu : {{ result.minigameType }}
      <span v-if="result.timeMs"> · {{ result.timeMs }} ms</span>
    </p>

    <div v-if="winner && loser" class="mt-2 flex w-full max-w-md items-center justify-center gap-5">
      <div class="flex flex-1 flex-col items-center gap-1">
        <span class="font-nunito text-xs font-bold tracking-wide text-white/60">GAGNANT</span>
        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-vert/80 font-luckiest text-white">
          {{ initial(winner.username) }}
        </span>
        <span class="font-luckiest text-vert">{{ winner.username }}</span>
        <span class="font-nunito text-sm font-bold text-vert">case {{ winner.position }} ▲ +2</span>
      </div>

      <span class="font-luckiest text-2xl text-white/70">VS</span>

      <div class="flex flex-1 flex-col items-center gap-1">
        <span class="font-nunito text-xs font-bold tracking-wide text-white/60">PERDANT</span>
        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rouge/70 font-luckiest text-white">
          {{ initial(loser.username) }}
        </span>
        <span class="font-luckiest text-rouge">{{ loser.username }}</span>
        <span class="font-nunito text-sm font-bold text-rouge">case {{ loser.position }} ▼ -2</span>
      </div>
    </div>

    <button
      type="button"
      class="mt-4 inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-3.5 font-luckiest text-lg tracking-wide text-foret shadow-lg transition hover:brightness-95"
      @click="backToBoard"
    >
      <CornerDownLeft class="h-5 w-5" /> Retour au plateau
    </button>
  </main>
</template>
