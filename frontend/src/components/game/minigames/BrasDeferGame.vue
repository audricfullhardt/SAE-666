<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'
import dinoSprite from '@/assets/dino_green.png'
import dinoSprite2 from '@/assets/dino_blue.png'

const props = defineProps({
  playerName: { type: String, default: 'REX' },
  opponentName: { type: String, default: 'TIA' },
})

const emit = defineEmits(['result'])

const DURATION = 15

const showCountdown = ref(true)
const playerScore = ref(0)
const opponentScore = ref(0)
const timer = ref(DURATION)
const finished = ref(false)
const playerDino = ref(null)
const opponentDino = ref(null)
const timerEl = ref(null)

let startTime = 0
let timerId = null
let opponentId = null

// Pourcentage de la barre côté joueur (0-100)
const playerPercent = computed(() => {
  const total = playerScore.value + opponentScore.value
  if (total === 0) return 50
  return Math.round((playerScore.value / total) * 100)
})

const lowTime = computed(() => timer.value <= 5)

function moveDinos() {
  // Le ratio décale les dinos horizontalement
  const ratio = (playerPercent.value - 50) / 50 // -1 .. 1
  if (playerDino.value) gsap.to(playerDino.value, { x: ratio * 40, duration: 0.2 })
  if (opponentDino.value) gsap.to(opponentDino.value, { x: ratio * 40, duration: 0.2 })
}

function tap() {
  if (finished.value || showCountdown.value) return
  playerScore.value++
  if (navigator.vibrate) navigator.vibrate(15)
  moveDinos()
}

function scheduleOpponent() {
  const delay = 300 + Math.random() * 500 // 0.3 à 0.8s
  opponentId = setTimeout(() => {
    if (finished.value) return
    opponentScore.value++
    moveDinos()
    scheduleOpponent()
  }, delay)
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
  finished.value = true
  clearTimers()
  const winner = playerScore.value >= opponentScore.value ? 'local' : 'opponent'
  emit('result', { winner, timeMs: Date.now() - startTime })
}

function clearTimers() {
  clearInterval(timerId)
  clearTimeout(opponentId)
}

function startGame() {
  showCountdown.value = false
  startTime = Date.now()
  scheduleOpponent()
  timerId = setInterval(tick, 1000)
}

onUnmounted(clearTimers)
</script>

<template>
  <div
    class="h-screen w-screen overflow-hidden bg-gradient-to-b from-blue-400 to-blue-700 flex flex-col p-4 select-none relative"
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

    <!-- Barre de progression -->
    <div class="mb-4 shrink-0">
      <div class="flex justify-between font-nunito text-white text-sm font-bold mb-1.5">
        <span>{{ playerName }} {{ playerPercent }}%</span>
        <span>{{ 100 - playerPercent }}% {{ opponentName }}</span>
      </div>
      <div class="h-4 rounded-full overflow-hidden flex bg-rouge">
        <div
          class="bg-vert h-full transition-all duration-200 ease-out"
          :style="{ width: playerPercent + '%' }"
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
