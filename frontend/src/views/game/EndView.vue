<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import gsap from 'gsap'
import { RotateCcw, House } from 'lucide-vue-next'
import { useGameStore } from '@/stores/game'
import { useMercure } from '@/composables/useMercure'
import dinoSprite from '@/assets/dino_green.png'

const route = useRoute()
const router = useRouter()
const game = useGameStore()

const code = route.params.code
const haloRef = ref(null)

const winner = computed(() => game.endWinner)

const ranking = computed(() =>
  [...game.players].sort((a, b) => (b.position ?? 0) - (a.position ?? 0)),
)

const winnerUsername = computed(
  () => winner.value?.username ?? ranking.value[0]?.username ?? '',
)

function isWinner(player) {
  return winner.value ? player.id === winner.value.id : false
}

function initial(name) {
  return (name || '?').charAt(0).toUpperCase()
}

function rankLabel(index) {
  return index === 0 ? '1er' : `${index + 1}ème`
}

function onMercure({ event, data }) {
  if (event === 'game_finished') {
    game.setEndResult(data)
  }
}

const { subscribe, unsubscribe } = useMercure(`game/${code}`, onMercure)

onMounted(async () => {
  subscribe()
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

onUnmounted(() => {
  unsubscribe()
})

function replay() {
  game.reset()
  router.push('/game')
}

function goHome() {
  game.reset()
  router.push('/')
}
</script>

<template>
  <main class="flex min-h-svh flex-col items-center justify-center gap-5 bg-foret px-6 py-10 text-center">
    <h1 class="font-luckiest text-5xl tracking-wide text-jaune">PARTIE TERMINÉE !</h1>

    <p v-if="winnerUsername" class="font-patrick text-2xl text-white">
      {{ winnerUsername }} remporte la partie !
    </p>

    <div ref="haloRef" class="relative flex items-center justify-center">
      <span class="absolute h-32 w-32 rounded-full bg-jaune/60 blur-2xl" />
      <img :src="dinoSprite" alt="" class="relative h-28 w-auto drop-shadow-xl [image-rendering:pixelated]" />
    </div>

    <ul class="mt-2 flex w-full max-w-md flex-col gap-2">
      <li
        v-for="(p, i) in ranking"
        :key="p.id"
        class="flex items-center gap-3 rounded-2xl px-3 py-3 transition"
        :class="isWinner(p) ? 'bg-jaune/20 ring-2 ring-jaune' : 'bg-black/25'"
      >
        <span
          class="font-luckiest"
          :class="isWinner(p) ? 'text-2xl text-jaune' : 'text-base text-white/70'"
        >
          {{ rankLabel(i) }}
        </span>
        <span
          class="flex items-center justify-center rounded-xl font-luckiest text-white"
          :class="isWinner(p) ? 'h-12 w-12 bg-jaune/80 text-xl' : 'h-9 w-9 bg-vert/80'"
        >
          {{ initial(p.username) }}
        </span>
        <span
          class="flex-1 text-left font-luckiest"
          :class="isWinner(p) ? 'text-xl text-jaune' : 'text-white'"
        >
          {{ p.username }}
        </span>
        <span class="font-nunito text-sm text-vert">Case {{ p.position }}</span>
      </li>
      <li v-if="!ranking.length" class="py-4 text-center font-nunito text-sm text-white/50">
        Aucun joueur à afficher.
      </li>
    </ul>

    <div class="mt-4 flex w-full max-w-md flex-col gap-3">
      <button
        type="button"
        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-jaune px-8 py-3.5 font-luckiest text-lg tracking-wide text-white shadow-lg transition hover:brightness-105"
        @click="replay"
      >
        <RotateCcw class="h-5 w-5" /> Rejouer
      </button>
      <button
        type="button"
        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-white px-8 py-3.5 font-luckiest text-lg tracking-wide text-foret shadow-lg transition hover:brightness-95"
        @click="goHome"
      >
        <House class="h-5 w-5" /> Accueil
      </button>
    </div>
  </main>
</template>
