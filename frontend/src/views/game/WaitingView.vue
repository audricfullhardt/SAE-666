<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import gsap from 'gsap'
import { useGameStore } from '@/stores/game'
import { useMercure } from '@/composables/useMercure'

const route = useRoute()
const router = useRouter()
const game = useGameStore()

const code = route.params.code
const copied = ref(false)
const error = ref('')

const MAX_PLAYERS = 6

const players = computed(() => game.players)
const emptySlots = computed(() => Math.max(0, MAX_PLAYERS - players.value.length))

const meReady = computed(() => game.currentPlayer?.isReady === true)
const notReadyCount = computed(() => players.value.filter((p) => !p.isReady).length)
const allReady = computed(() => players.value.length >= 2 && notReadyCount.value === 0)

function initial(name) {
  return (name || '?').charAt(0).toUpperCase()
}

async function copyCode() {
  try {
    await navigator.clipboard.writeText(code)
    copied.value = true
    setTimeout(() => (copied.value = false), 1500)
  } catch {
    /* presse-papier indisponible */
  }
}

async function markReady() {
  error.value = ''
  try {
    await game.setReady(code)
  } catch (e) {
    error.value = e.message || 'Impossible de passer prêt'
  }
}

async function launch() {
  error.value = ''
  try {
    await game.startGame(code)
    router.push(`/game/minigame/${code}`)
  } catch (e) {
    error.value = e.message || 'Impossible de lancer la partie'
  }
}

function onMercure({ event, data }) {
  if (event === 'player_joined') {
    if (game.addPlayer(data)) {
      nextTick(() => {
        const el = document.querySelector(`[data-player="${data.id}"]`)
        if (el) gsap.from(el, { opacity: 0, y: 12, duration: 0.4, ease: 'power2.out' })
      })
    }
  } else if (event === 'player_ready') {
    game.setPlayerReady(data.id ?? data.playerId)
  } else if (event === 'game_started') {
    router.push(`/game/minigame/${code}`)
  }
}

const { subscribe, unsubscribe } = useMercure(`game/${code}`, onMercure)

// Fallback : le backend ne publie pas encore les events de lobby sur Mercure.
// L'hôte (et les joueurs connectés) rafraîchissent l'état via GET ; un invité s'appuie
// sur les données initiales de /join et sur Mercure quand il sera branché.
let pollTimer = null
async function poll() {
  const data = await game.fetchSession(code)
  if (data?.notFound) {
    // Session expirée / introuvable → on nettoie et on renvoie au lobby.
    game.reset()
    router.push('/game')
    return
  }
  if (data && data.status === 'playing') {
    router.push(`/game/minigame/${code}`)
  }
}

onMounted(() => {
  subscribe()
  // Resynchronise les joueurs après un refresh (no-op silencieux pour un invité sans JWT).
  poll()
  pollTimer = setInterval(poll, 2000)
})

onUnmounted(() => {
  unsubscribe()
  if (pollTimer) clearInterval(pollTimer)
})
</script>

<template>
  <main class="flex min-h-svh flex-col bg-foret px-6 py-8">
    <header class="text-center">
      <p class="font-nunito text-sm text-white/70">Code de la salle</p>
      <p class="font-luckiest text-5xl tracking-[0.2em] text-white">{{ code }}</p>
      <button
        type="button"
        class="mt-2 rounded-full bg-black/30 px-4 py-1.5 font-nunito text-sm text-white/90 transition hover:bg-black/40"
        @click="copyCode"
      >
        📋 {{ copied ? 'Copié !' : 'Copier & partager' }}
      </button>
    </header>

    <section class="mx-auto mt-6 w-full max-w-md flex-1">
      <p class="font-luckiest text-sm tracking-wide text-white/80">
        Joueurs · {{ players.length }}/{{ MAX_PLAYERS }}
      </p>

      <ul class="mt-3 flex flex-col gap-2">
        <li
          v-for="p in players"
          :key="p.id"
          :data-player="p.id"
          class="flex items-center gap-3 rounded-2xl bg-black/25 px-3 py-3"
        >
          <span
            class="flex h-9 w-9 items-center justify-center rounded-xl bg-vert/80 font-luckiest text-white"
          >
            {{ initial(p.username) }}
          </span>
          <span class="flex-1 font-luckiest text-white">{{ p.username }}</span>
          <span v-if="p.isHost" class="rounded-full bg-jaune px-2 py-0.5 font-nunito text-xs font-bold text-white">
            HÔTE
          </span>
          <span
            v-if="p.isReady"
            class="rounded-full bg-vert px-3 py-1 font-luckiest text-xs tracking-wide text-white"
          >
            PRÊT
          </span>
          <span v-else class="rounded-full bg-white/15 px-3 py-1 font-luckiest text-xs text-white/60">…</span>
        </li>

        <li
          v-for="n in emptySlots"
          :key="`empty-${n}`"
          class="flex items-center gap-3 rounded-2xl border border-dashed border-white/20 px-3 py-3"
        >
          <span class="h-9 w-9 rounded-xl bg-white/10" />
          <span class="font-nunito text-sm text-white/40">En attente d'un joueur…</span>
        </li>
      </ul>
    </section>

    <footer class="mx-auto w-full max-w-md space-y-3">
      <p v-if="error" class="text-center font-nunito text-sm text-rouge">{{ error }}</p>

      <button
        type="button"
        class="w-full rounded-full bg-vert px-6 py-3.5 font-luckiest text-lg tracking-wide text-white transition hover:brightness-105 disabled:opacity-60"
        :disabled="meReady"
        @click="markReady"
      >
        {{ meReady ? 'PRÊT ✓' : 'JE SUIS PRÊT' }}
      </button>

      <button
        v-if="game.isHost"
        type="button"
        class="w-full rounded-full bg-jaune px-6 py-3.5 font-luckiest text-lg tracking-wide text-white transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-50"
        :disabled="!allReady"
        @click="launch"
      >
        ▶ {{ allReady ? 'Lancer la partie' : `${notReadyCount} joueur(s) pas prêt(s)` }}
      </button>
      <p v-if="game.isHost" class="text-center font-nunito text-xs text-white/50">
        Seul l'hôte peut démarrer · 2 joueurs minimum
      </p>
    </footer>
  </main>
</template>
