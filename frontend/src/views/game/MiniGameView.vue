<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import gsap from 'gsap'
import { useGameStore } from '@/stores/game'
import { useMercure } from '@/composables/useMercure'
import QuizGame from '@/components/game/minigames/QuizGame.vue'
import ReflexGame from '@/components/game/minigames/ReflexGame.vue'
import MemoryGame from '@/components/game/minigames/MemoryGame.vue'
import BrasDeferGame from '@/components/game/minigames/BrasDeferGame.vue'
import GobeletsGame from '@/components/game/minigames/GobeletsGame.vue'
import DinoFindGame from '@/components/game/minigames/DinoFindGame.vue'

const route = useRoute()
const router = useRouter()
const game = useGameStore()

const code = route.params.code

const COMPONENTS = {
  quiz: QuizGame,
  reflex: ReflexGame,
  memory: MemoryGame,
  brasdefer: BrasDeferGame,
  gobelets: GobeletsGame,
  dinofind: DinoFindGame,
}

const MINIGAME_LABELS = {
  quiz: 'Quiz Dino',
  reflex: 'Réflexe',
  memory: 'Mémoire',
  brasdefer: 'Bras de fer',
  gobelets: 'Gobelets',
  dinofind: 'Où est le dino ?',
}

const error = ref('')
const spinnerRef = ref(null)
const showDuelModal = ref(false)
const showEndModal = ref(false)
const selectedWinnerId = ref(null)
const endingGame = ref(false)
const specAvatar1 = ref(null)
const specAvatar2 = ref(null)
let resultSent = false
let specTweens = []

const activeType = computed(() => game.activeMinigame?.minigameType ?? null)
const activeComponent = computed(() => COMPONENTS[activeType.value] ?? null)
const minigameLabel = computed(() => MINIGAME_LABELS[activeType.value] ?? activeType.value ?? '')

// activeMinigame peut arriver à plat (Mercure) ou imbriqué (triggerDuel / fetchCurrentMinigame).
const challengerId = computed(
  () => game.activeMinigame?.challenger?.id ?? game.activeMinigame?.challengerId ?? null,
)
const opponentId = computed(
  () => game.activeMinigame?.opponent?.id ?? game.activeMinigame?.opponentId ?? null,
)
const challengerUsername = computed(
  () => game.activeMinigame?.challenger?.username ?? game.activeMinigame?.challengerUsername ?? '',
)
const opponentUsername = computed(
  () => game.activeMinigame?.opponent?.username ?? game.activeMinigame?.opponentUsername ?? '',
)

const myId = computed(() => game.currentPlayer?.id ?? null)
const isChallenger = computed(() => myId.value !== null && myId.value === challengerId.value)
const isOpponent = computed(() => myId.value !== null && myId.value === opponentId.value)
const isDuelist = computed(() => isChallenger.value || isOpponent.value)
const isSpectator = computed(() => !!activeComponent.value && !isDuelist.value)

const playerName = computed(() => game.currentPlayer?.username ?? 'Toi')
// L'adversaire affiché au duelliste est l'autre duelliste (pas un spectateur).
const opponentName = computed(() =>
  isChallenger.value ? opponentUsername.value : challengerUsername.value,
)

// Adversaires possibles = tous les joueurs sauf moi.
const opponents = computed(() =>
  game.players.filter((p) => p.id !== game.currentPlayer?.id),
)

function initial(name) {
  return (name || '?').charAt(0).toUpperCase()
}

async function selectOpponent(player) {
  error.value = ''
  try {
    await game.triggerDuel(code, player.id)
    showDuelModal.value = false
    // Mercure (minigame_triggered) bascule tout le monde vers l'état 2.
  } catch (e) {
    error.value = e.message || 'Impossible de lancer le duel'
  }
}

function openEndModal() {
  error.value = ''
  selectedWinnerId.value = null
  showEndModal.value = true
  // Rafraîchit les positions des joueurs (l'hôte a un JWT, le GET fonctionne).
  game.fetchSession(code)
}

async function confirmEnd() {
  if (!selectedWinnerId.value || endingGame.value) return
  endingGame.value = true
  error.value = ''
  try {
    await game.finishGame(code, selectedWinnerId.value)
    showEndModal.value = false
    // Mercure (game_finished) redirige aussi les autres joueurs.
    router.push(`/game/end/${code}`)
  } catch (e) {
    error.value = e.message || 'Impossible de terminer la partie'
  } finally {
    endingGame.value = false
  }
}

async function onResult({ winner, timeMs }) {
  if (resultSent) return
  resultSent = true
  const meId = myId.value
  // L'adversaire du duel = l'autre duelliste (challenger ou opponent).
  const otherId = isChallenger.value ? opponentId.value : challengerId.value
  const winnerId = winner === 'local' ? meId : otherId
  const loserId = winner === 'local' ? otherId : meId
  const minigameType = activeType.value

  try {
    const payload = await game.submitResult(winnerId, loserId)
    game.setLastResult({ ...payload, minigameType, timeMs })
  } catch {
    // Un autre client a peut-être déjà résolu le duel : on suit l'event Mercure duel_resolved.
  }
  router.push(`/game/result/${code}`)
}

function onMercure({ event, data }) {
  if (event === 'minigame_triggered') {
    resultSent = false
    game.setActiveMinigame(data)
  } else if (event === 'duel_resolved') {
    game.setLastResult({ ...data, minigameType: activeType.value })
    router.push(`/game/result/${code}`)
  } else if (event === 'game_finished') {
    game.setEndResult(data)
    router.push(`/game/end/${code}`)
  }
}

const { subscribe, unsubscribe } = useMercure(`game/${code}`, onMercure)

// Pulse GSAP alterné des deux avatars de l'écran spectateur.
function stopSpecPulse() {
  specTweens.forEach((t) => t.kill())
  specTweens = []
}
watch(isSpectator, (spectating) => {
  stopSpecPulse()
  if (!spectating) return
  nextTick(() => {
    if (specAvatar1.value) {
      specTweens.push(
        gsap.to(specAvatar1.value, { scale: 1.12, duration: 0.7, ease: 'sine.inOut', repeat: -1, yoyo: true }),
      )
    }
    if (specAvatar2.value) {
      specTweens.push(
        gsap.to(specAvatar2.value, {
          scale: 1.12,
          duration: 0.7,
          ease: 'sine.inOut',
          repeat: -1,
          yoyo: true,
          delay: 0.7,
        }),
      )
    }
  })
})

// Polling fallback si Mercure ne répond pas : récupère le mini-jeu courant.
let pollTimer = null
function poll() {
  game.fetchCurrentMinigame()
}

onMounted(async () => {
  // Resynchronise les joueurs depuis l'API après un éventuel refresh.
  const data = await game.fetchSession(code)
  if (data?.notFound) {
    // Session expirée / introuvable → on nettoie et on renvoie au lobby.
    game.reset()
    router.push('/game')
    return
  }
  subscribe()
  poll()
  pollTimer = setInterval(poll, 3000)
  if (spinnerRef.value) {
    gsap.to(spinnerRef.value, { rotation: 360, duration: 1, ease: 'none', repeat: -1 })
  }
})

onUnmounted(() => {
  unsubscribe()
  if (pollTimer) clearInterval(pollTimer)
  stopSpecPulse()
})
</script>

<template>
  <main class="flex min-h-svh flex-col bg-foret">
    <!-- ÉTAT 2 — mini-jeu en cours (challenger / opponent uniquement) -->
    <component
      :is="activeComponent"
      v-if="activeComponent && isDuelist"
      :player-name="playerName"
      :opponent-name="opponentName"
      @result="onResult"
    />

    <!-- ÉTAT 2 bis — écran spectateur -->
    <div
      v-else-if="isSpectator"
      class="flex flex-1 flex-col items-center justify-center gap-5 px-6 py-10 text-center"
    >
      <h1 class="font-luckiest text-4xl tracking-wide text-jaune">DUEL EN COURS !</h1>
      <p class="font-patrick text-2xl text-vert">
        {{ challengerUsername }} VS {{ opponentUsername }}
      </p>

      <span class="rounded-full bg-vert px-5 py-2 font-luckiest tracking-wide text-white">
        {{ minigameLabel }}
      </span>

      <div class="mt-2 flex w-full max-w-md items-center justify-center gap-6">
        <div class="flex flex-1 flex-col items-center gap-2">
          <span
            ref="specAvatar1"
            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-vert/80 font-luckiest text-2xl text-white"
          >
            {{ initial(challengerUsername) }}
          </span>
          <span class="font-luckiest text-white">{{ challengerUsername }}</span>
        </div>

        <span class="font-luckiest text-2xl text-white/70">VS</span>

        <div class="flex flex-1 flex-col items-center gap-2">
          <span
            ref="specAvatar2"
            class="flex h-16 w-16 items-center justify-center rounded-2xl bg-rouge/70 font-luckiest text-2xl text-white"
          >
            {{ initial(opponentUsername) }}
          </span>
          <span class="font-luckiest text-white">{{ opponentUsername }}</span>
        </div>
      </div>

      <div class="mt-4 flex items-center gap-3">
        <div class="h-6 w-6 animate-spin rounded-full border-2 border-white/20 border-t-vert" />
        <p class="font-nunito text-sm text-white/60">En attente du résultat…</p>
      </div>
    </div>

    <!-- ÉTAT 1 — en attente de duel -->
    <div v-else class="flex flex-1 flex-col items-center justify-center gap-6 px-6 text-center">
      <p class="font-luckiest text-2xl text-white">En attente d'un duel…</p>

      <div ref="spinnerRef" class="h-14 w-14 rounded-full border-4 border-white/20 border-t-vert" />

      <button
        type="button"
        class="rounded-full bg-jaune px-8 py-4 font-luckiest text-xl tracking-wide text-white shadow-lg transition hover:brightness-105"
        @click="showDuelModal = true"
      >
        ⚔ Duel !
      </button>

      <button
        v-if="game.isHost"
        type="button"
        class="rounded-full bg-rouge px-6 py-2.5 font-luckiest tracking-wide text-white shadow-md transition hover:brightness-105"
        @click="openEndModal"
      >
        🏁 Fin de jeu
      </button>

      <p v-if="error" class="font-nunito text-sm text-rouge">{{ error }}</p>
    </div>

    <!-- Modale de sélection d'adversaire -->
    <div
      v-if="showDuelModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6"
      @click.self="showDuelModal = false"
    >
      <div class="w-full max-w-sm rounded-3xl bg-foret p-6 shadow-2xl ring-1 ring-white/10">
        <p class="text-center font-luckiest text-xl text-white">Choisis ton adversaire</p>

        <ul class="mt-4 flex flex-col gap-2">
          <li v-for="p in opponents" :key="p.id">
            <button
              type="button"
              class="flex w-full items-center gap-3 rounded-2xl bg-black/25 px-3 py-3 text-left transition hover:bg-black/40"
              @click="selectOpponent(p)"
            >
              <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-vert/80 font-luckiest text-white">
                {{ initial(p.username) }}
              </span>
              <span class="flex-1 font-luckiest text-white">{{ p.username }}</span>
            </button>
          </li>
          <li v-if="!opponents.length" class="py-4 text-center font-nunito text-sm text-white/50">
            Aucun autre joueur dans la partie.
          </li>
        </ul>

        <button
          type="button"
          class="mt-4 w-full rounded-full bg-white/10 px-6 py-2.5 font-nunito text-sm text-white transition hover:bg-white/20"
          @click="showDuelModal = false"
        >
          Annuler
        </button>
      </div>
    </div>

    <!-- Modale "Fin de jeu" — déclaration du gagnant (hôte) -->
    <div
      v-if="showEndModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6"
      @click.self="showEndModal = false"
    >
      <div class="w-full max-w-sm rounded-3xl bg-foret p-6 shadow-2xl ring-1 ring-white/10">
        <p class="text-center font-luckiest text-2xl text-jaune">QUI A GAGNÉ ?</p>
        <p class="mt-1 text-center font-patrick text-lg text-white">
          Sélectionne le joueur qui a atteint l'arrivée
        </p>

        <ul class="mt-4 flex max-h-72 flex-col gap-2 overflow-y-auto">
          <li v-for="p in game.players" :key="p.id">
            <button
              type="button"
              class="flex w-full items-center gap-3 rounded-2xl px-3 py-3 text-left transition"
              :class="
                selectedWinnerId === p.id
                  ? 'bg-vert/30 ring-2 ring-vert'
                  : 'bg-black/25 hover:bg-black/40'
              "
              @click="selectedWinnerId = p.id"
            >
              <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-vert/80 font-luckiest text-white">
                {{ initial(p.username) }}
              </span>
              <span class="flex-1 font-luckiest text-white">{{ p.username }}</span>
              <span class="font-nunito text-sm text-vert">Case {{ p.position }}</span>
            </button>
          </li>
          <li v-if="!game.players.length" class="py-4 text-center font-nunito text-sm text-white/50">
            Aucun joueur dans la partie.
          </li>
        </ul>

        <p v-if="error" class="mt-3 text-center font-nunito text-sm text-rouge">{{ error }}</p>

        <div class="mt-4 flex flex-col gap-2">
          <button
            type="button"
            class="w-full rounded-full bg-vert px-6 py-3 font-luckiest tracking-wide text-white transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!selectedWinnerId || endingGame"
            @click="confirmEnd"
          >
            {{ endingGame ? 'Fin de partie…' : 'Confirmer' }}
          </button>
          <button
            type="button"
            class="w-full rounded-full bg-white/10 px-6 py-2.5 font-luckiest tracking-wide text-white/80 transition hover:bg-white/20"
            @click="showEndModal = false"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>
  </main>
</template>
