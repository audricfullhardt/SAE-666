<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'
import { useGameStore } from '@/stores/game'
import { useMercure } from '@/composables/useMercure'
import dinoGreen from '@/assets/dino_green.png'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
  playerId: { type: Number, default: null },
  opponentId: { type: Number, default: null },
  sessionCode: { type: String, default: '' },
})

const emit = defineEmits(['result'])

const game = useGameStore()

const ROUNDS = 5
const WINS_NEEDED = 3
const PENALTY = 99999 // temps « perdu » (tap trop tôt ou timeout adversaire)

// phases: idle | waiting | active | answered | reveal
const showCountdown = ref(true)
const phase = ref('idle')
const round = ref(1)
const playerWins = ref(0)
const opponentWins = ref(0)
const playerTime = ref(null) // ms de la manche en cours
const opponentTime = ref(null)
const tooEarly = ref(false)
const roundWinner = ref(null) // 'local' | 'opponent'
const dinoEl = ref(null)
const haloEl = ref(null)

let appearTime = 0
let appearId = null
let revealId = null
let oppTimeoutId = null
let haloTween = null
let waitingOpponent = false
const opponentQueue = [] // temps reçus en avance

function fmt(ms) {
  if (ms === null) return '—'
  if (ms >= PENALTY) return 'trop tôt'
  return (ms / 1000).toFixed(2) + 's'
}

function clearTimers() {
  clearTimeout(appearId)
  clearTimeout(revealId)
  clearTimeout(oppTimeoutId)
}

function startGame() {
  showCountdown.value = false
  startRound()
}

function startRound() {
  playerTime.value = null
  opponentTime.value = null
  tooEarly.value = false
  roundWinner.value = null
  waitingOpponent = false
  phase.value = 'waiting'
  const delay = 1000 + Math.random() * 3000 // 1 à 4s
  appearId = setTimeout(showDino, delay)
}

function showDino() {
  phase.value = 'active'
  appearTime = Date.now()
  requestAnimationFrame(() => {
    if (dinoEl.value) {
      gsap.fromTo(
        dinoEl.value,
        { opacity: 0, scale: 0.4 },
        { opacity: 1, scale: 1, duration: 0.25, ease: 'back.out(2)' },
      )
    }
  })
}

function sendReaction(ms) {
  if (props.sessionCode && props.playerId != null) {
    game.sendTap(props.sessionCode, props.playerId, ms)
  }
}

function tap() {
  if (phase.value === 'answered' || phase.value === 'reveal' || phase.value === 'done') return

  if (phase.value === 'waiting') {
    // Tapé trop tôt → manche perdue (temps de pénalité)
    clearTimeout(appearId)
    tooEarly.value = true
    playerTime.value = PENALTY
    phase.value = 'answered'
    sendReaction(PENALTY)
    awaitOpponent()
    return
  }

  if (phase.value === 'active' && playerTime.value === null) {
    playerTime.value = Date.now() - appearTime
    phase.value = 'answered'
    sendReaction(playerTime.value)
    awaitOpponent()
  }
}

// Récupère le temps de l'adversaire (en file s'il est déjà arrivé), sinon attend 5 s max.
function awaitOpponent() {
  if (opponentQueue.length) {
    opponentTime.value = opponentQueue.shift()
    resolveRound()
    return
  }
  waitingOpponent = true
  oppTimeoutId = setTimeout(() => {
    if (!waitingOpponent) return
    waitingOpponent = false
    opponentTime.value = PENALTY // pas de réponse → l'adversaire perd la manche
    resolveRound()
  }, 5000)
}

function resolveRound() {
  phase.value = 'reveal'
  roundWinner.value = playerTime.value <= opponentTime.value ? 'local' : 'opponent'
  if (roundWinner.value === 'local') playerWins.value++
  else opponentWins.value++

  revealId = setTimeout(() => {
    if (
      playerWins.value >= WINS_NEEDED ||
      opponentWins.value >= WINS_NEEDED ||
      round.value >= ROUNDS
    ) {
      finishMatch()
    } else {
      round.value++
      startRound()
    }
  }, 1500)
}

function finishMatch() {
  phase.value = 'done'
  clearTimers()
  emit('result', {
    winner: playerWins.value >= WINS_NEEDED ? 'local' : 'opponent',
    timeMs: playerWins.value,
  })
}

function onMercure({ event, data }) {
  if (event !== 'player_tap' || data.playerId !== props.opponentId) return
  if (waitingOpponent) {
    waitingOpponent = false
    clearTimeout(oppTimeoutId)
    opponentTime.value = data.taps
    resolveRound()
  } else {
    opponentQueue.push(data.taps)
  }
}
const { subscribe, unsubscribe } = useMercure(`game/${props.sessionCode}`, onMercure)
if (props.sessionCode) subscribe()

onMounted(() => {
  requestAnimationFrame(() => {
    if (haloEl.value) {
      haloTween = gsap.to(haloEl.value, {
        scale: 1.15,
        opacity: 0.85,
        duration: 1,
        repeat: -1,
        yoyo: true,
        ease: 'sine.inOut',
      })
    }
  })
})

onUnmounted(() => {
  clearTimers()
  if (haloTween) haloTween.kill()
  unsubscribe()
})

const roundLabel = computed(() => `Manche ${Math.min(round.value, ROUNDS)}/${ROUNDS}`)
</script>

<template>
  <div
    class="h-[100dvh] w-screen overflow-hidden bg-foret flex flex-col items-center p-4 select-none cursor-pointer relative"
    @pointerdown="tap"
  >
    <CountdownOverlay v-if="showCountdown" bg-class="bg-foret" @done="startGame" />

    <!-- Score de manches -->
    <div class="shrink-0 text-center">
      <p class="font-luckiest text-white text-2xl">
        {{ playerName }} <span class="text-vert">{{ playerWins }}</span>
        -
        <span class="text-rouge">{{ opponentWins }}</span> {{ opponentName }}
      </p>
      <p class="font-patrick text-vert text-base mt-1">{{ roundLabel }} · tape dès que le dino apparaît</p>
    </div>

    <!-- Zone centrale -->
    <div class="flex-1 min-h-0 w-full flex items-center justify-center relative">
      <div
        ref="haloEl"
        class="absolute rounded-full bg-vert/40 blur-xl"
        style="width: min(55vw, 14rem); height: min(55vw, 14rem)"
      />
      <div class="relative flex flex-col items-center justify-center text-center">
        <div v-if="phase === 'waiting'" class="font-patrick text-white/70 text-2xl">Prêt...</div>

        <div v-else-if="phase === 'active'" ref="dinoEl">
          <img :src="dinoGreen" alt="" class="h-28 w-auto [image-rendering:pixelated]" />
        </div>

        <div v-else-if="phase === 'answered'" class="flex flex-col items-center gap-1">
          <p v-if="tooEarly" class="font-luckiest text-rouge text-4xl">TROP TÔT !</p>
          <p v-else class="font-luckiest text-white text-3xl">{{ fmt(playerTime) }}</p>
          <p class="font-patrick text-white/60 text-lg">En attente de l'adversaire...</p>
        </div>

        <div v-else-if="phase === 'reveal'" class="flex flex-col items-center gap-2">
          <p
            class="font-luckiest text-3xl"
            :class="roundWinner === 'local' ? 'text-vert' : 'text-rouge'"
          >
            {{ roundWinner === 'local' ? 'Manche gagnée !' : 'Manche perdue' }}
          </p>
          <p class="font-nunito text-white/90">
            {{ playerName }} {{ fmt(playerTime) }} · {{ opponentName }} {{ fmt(opponentTime) }}
          </p>
        </div>
      </div>
    </div>

    <div class="shrink-0 h-4" />
  </div>
</template>
