<script setup>
import { ref, computed, onUnmounted } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'
import { useGameStore } from '@/stores/game'
import { useMercure } from '@/composables/useMercure'
import dinoSprite from '@/assets/dino_green.png'
import dinoSprite2 from '@/assets/dino_blue.png'

const props = defineProps({
  playerName: { type: String, default: 'REX' },
  opponentName: { type: String, default: 'TIA' },
  playerId: { type: Number, default: null },
  opponentId: { type: Number, default: null },
  sessionCode: { type: String, default: '' },
})

const emit = defineEmits(['result'])

const game = useGameStore()

const DURATION = 15
const MAX_TAPS = 100

const showCountdown = ref(true)
const playerTaps = ref(0)
const opponentTaps = ref(0)
const timer = ref(DURATION)
const finished = ref(false)
const playerDino = ref(null)
const opponentDino = ref(null)
const timerEl = ref(null)

let timerId = null
let tapThrottle = null

// Jauge tug-of-war : la frontière bleu/rouge dépend de l'écart de taps (les deux réactives).
// 50/50 au départ ; le bleu (joueur) gagne du terrain à droite, le rouge (adversaire) à gauche.
const playerPercent = computed(() => {
  const p = 50 + 50 * ((playerTaps.value - opponentTaps.value) / MAX_TAPS)
  return Math.max(0, Math.min(100, p))
})
const opponentPercent = computed(() => 100 - playerPercent.value)

const lowTime = computed(() => timer.value <= 5)

function moveDinos() {
  const ratio = (playerPercent.value - 50) / 50 // -1 .. 1
  if (playerDino.value) gsap.to(playerDino.value, { x: ratio * 40, duration: 0.2 })
  if (opponentDino.value) gsap.to(opponentDino.value, { x: ratio * 40, duration: 0.2 })
}

// Envoi des taps via Mercure, throttlé à un POST toutes les 100 ms maximum.
function broadcastTap() {
  if (!props.sessionCode || props.playerId == null) return
  if (tapThrottle) return
  tapThrottle = setTimeout(() => {
    tapThrottle = null
    game.sendTap(props.sessionCode, props.playerId, playerTaps.value)
  }, 100)
}

function tap() {
  if (finished.value || showCountdown.value) return
  playerTaps.value++
  if (navigator.vibrate) navigator.vibrate(15)
  moveDinos()
  broadcastTap()
}

function pulseTimer() {
  if (timerEl.value) {
    gsap.fromTo(timerEl.value, { scale: 1.25 }, { scale: 1, duration: 0.5, ease: 'power2.out' })
  }
}

function tick() {
  timer.value--
  if (timer.value <= 5 && timer.value > 0) pulseTimer()
  if (timer.value <= 0) finish()
}

function finish() {
  if (finished.value) return
  finished.value = true
  clearTimers()
  // Dernier envoi pour synchroniser le score final, puis on tranche.
  if (props.sessionCode && props.playerId != null) {
    game.sendTap(props.sessionCode, props.playerId, playerTaps.value)
  }
  emit('result', {
    winner: playerTaps.value > opponentTaps.value ? 'local' : 'opponent',
    timeMs: playerTaps.value,
  })
}

function clearTimers() {
  clearInterval(timerId)
  clearTimeout(tapThrottle)
  tapThrottle = null
}

function startGame() {
  showCountdown.value = false
  timerId = setInterval(tick, 1000)
}

// Taps de l'adversaire en temps réel.
function onMercure({ event, data }) {
  if (event === 'player_tap' && data.playerId === props.opponentId) {
    opponentTaps.value = data.taps
  }
}
const { subscribe, unsubscribe } = useMercure(`game/${props.sessionCode}`, onMercure)
if (props.sessionCode) subscribe()

onUnmounted(() => {
  clearTimers()
  unsubscribe()
})
</script>

<template>
  <div
    class="h-[100dvh] w-screen overflow-hidden bg-gradient-to-b from-blue-400 to-blue-700 flex flex-col p-4 select-none relative"
  >
    <CountdownOverlay v-if="showCountdown" bg-class="bg-gradient-to-b from-blue-400 to-blue-700" @done="startGame" />

    <!-- Titre -->
    <h1 class="font-luckiest text-white text-2xl text-center shrink-0">BRAS DE FER</h1>

    <!-- Timer central -->
    <div class="flex justify-center my-3 shrink-0">
      <div
        ref="timerEl"
        class="w-20 h-20 rounded-full flex items-center justify-center shadow-lg"
        :class="lowTime ? 'bg-rouge' : 'bg-white'"
      >
        <span
          class="font-luckiest text-4xl"
          :class="lowTime ? 'text-white' : 'text-foret'"
        >
          {{ Math.max(timer, 0) }}
        </span>
      </div>
    </div>

    <!-- Arène -->
    <div class="flex-1 min-h-0 flex items-center justify-center gap-4">
      <div ref="playerDino" class="text-7xl"><img :src="dinoSprite" alt="" class="relative h-28 w-auto drop-shadow-xl [image-rendering:pixelated]" /></div>
      <span class="font-luckiest text-white text-2xl">VS</span>
      <div ref="opponentDino" class="text-7xl -scale-x-100"><img :src="dinoSprite2" alt="" class="relative h-28 w-auto drop-shadow-xl [image-rendering:pixelated]" /></div>
    </div>

    <!-- Jauge d'opposition temps réel : bleu (joueur) vs rouge (adversaire), frontière mobile -->
    <div class="mb-4 shrink-0">
      <div class="flex justify-between font-luckiest text-white text-sm mb-1.5">
        <span>{{ playerName }} <span class="text-bleu">{{ playerTaps }}</span></span>
        <span><span class="text-rouge">{{ opponentTaps }}</span> {{ opponentName }}</span>
      </div>
      <div class="h-6 rounded-full overflow-hidden flex">
        <div
          class="h-full bg-bleu transition-all duration-100 ease-out"
          :style="{ width: playerPercent + '%' }"
        />
        <div
          class="h-full bg-rouge transition-all duration-100 ease-out"
          :style="{ width: opponentPercent + '%' }"
        />
      </div>
    </div>

    <!-- Bouton TAPE -->
    <button
      @pointerdown="tap"
      :disabled="finished"
      class="w-full bg-jaune text-foret font-luckiest text-4xl py-5 rounded-full shadow-xl active:scale-95 transition-transform disabled:opacity-60 shrink-0"
    >
      TAPE !
    </button>
  </div>
</template>
