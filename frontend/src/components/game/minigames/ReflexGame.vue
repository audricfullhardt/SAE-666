<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'
import dinoGreen from '@/assets/dino_green.png'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
})

const emit = defineEmits(['result'])

// phases: idle | waiting | active | done
const showCountdown = ref(true)
const phase = ref('idle')
const playerTime = ref(null) // ms
const opponentTime = ref(null) // ms
const tooEarly = ref(false)
const dinoEl = ref(null)
const haloEl = ref(null)

let appearTime = 0
let appearId = null
let opponentId = null
let resultId = null
let haloTween = null

function clearAll() {
  clearTimeout(appearId)
  clearTimeout(opponentId)
  clearTimeout(resultId)
  if (haloTween) haloTween.kill()
}

function startGame() {
  showCountdown.value = false
  startWaiting()
}

function startWaiting() {
  phase.value = 'waiting'
  const delay = 1000 + Math.random() * 3000 // 1 à 4s
  appearId = setTimeout(showDino, delay)
}

function showDino() {
  phase.value = 'active'
  appearTime = Date.now()
  // L'adversaire réagit entre 200 et 800ms après l'apparition
  const oppDelay = 200 + Math.random() * 600
  opponentId = setTimeout(() => {
    if (opponentTime.value === null) opponentTime.value = Math.round(oppDelay)
    maybeFinish()
  }, oppDelay)

  // Animation d'apparition du dino
  requestAnimationFrame(() => {
    if (dinoEl.value) {
      gsap.fromTo(
        dinoEl.value,
        { opacity: 0, scale: 0.4 },
        { opacity: 1, scale: 1, duration: 0.25, ease: 'back.out(2)' }
      )
    }
  })
}

function tap() {
  if (phase.value === 'waiting') {
    // Tapé trop tôt → défaite automatique
    tooEarly.value = true
    phase.value = 'done'
    clearAll()
    resultId = setTimeout(() => {
      emit('result', { winner: 'opponent', timeMs: 0 })
    }, 1200)
    return
  }
  if (phase.value !== 'active' || playerTime.value !== null) return
  playerTime.value = Date.now() - appearTime
  maybeFinish()
}

function maybeFinish() {
  if (playerTime.value === null || opponentTime.value === null) return
  phase.value = 'done'
  clearAll()
  const winner = playerTime.value <= opponentTime.value ? 'local' : 'opponent'
  resultId = setTimeout(() => {
    emit('result', { winner, timeMs: playerTime.value })
  }, 1200)
}

function fmt(ms) {
  if (ms === null) return null
  return (ms / 1000).toFixed(2) + 's'
}

onMounted(() => {
  // Halo pulsant
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

onUnmounted(clearAll)
</script>

<template>
  <div
    class="h-screen w-screen overflow-hidden bg-foret flex flex-col items-center p-4 select-none cursor-pointer relative"
    @pointerdown="tap"
  >
    <CountdownOverlay v-if="showCountdown" bg-class="bg-foret" @done="startGame" />

    <!-- Titre -->
    <h1 class="font-luckiest text-white text-2xl mt-2">TAPE !</h1>
    <p class="font-patrick text-vert text-base mt-1">dès que le dino apparaît</p>

    <!-- Zone centrale -->
    <div class="flex-1 min-h-0 w-full flex items-center justify-center relative">
      <div
        ref="haloEl"
        class="absolute rounded-full bg-vert/40 blur-xl"
        style="width: min(55vw, 14rem); height: min(55vw, 14rem)"
      />
      <div class="relative flex flex-col items-center justify-center text-center">
        <div v-if="phase === 'waiting'" class="font-patrick text-white/70 text-2xl">
          Prêt...
        </div>
        <div v-else-if="tooEarly" class="font-luckiest text-rouge text-4xl">
          TROP TÔT !
        </div>
        <div v-else-if="phase === 'active' || phase === 'done'" ref="dinoEl">
          <img :src="dinoGreen" alt="" class="h-28 w-auto [image-rendering:pixelated]" />
        </div>
      </div>
    </div>

    <!-- Footer scores -->
    <div class="w-full grid grid-cols-2 gap-3">
      <div class="bg-vert rounded-2xl p-4 text-center">
        <p class="font-nunito text-foret text-xs font-bold uppercase truncate">{{ playerName }}</p>
        <p class="font-luckiest text-2xl mt-1" :class="playerTime !== null ? 'text-foret' : 'text-foret/40'">
          {{ fmt(playerTime) || 'Attente...' }}
        </p>
      </div>
      <div class="bg-vert rounded-2xl p-4 text-center">
        <p class="font-nunito text-foret text-xs font-bold uppercase truncate">{{ opponentName }}</p>
        <p class="font-luckiest text-2xl mt-1" :class="opponentTime !== null ? 'text-foret' : 'text-foret/40'">
          {{ fmt(opponentTime) || 'Attente...' }}
        </p>
      </div>
    </div>
  </div>
</template>
