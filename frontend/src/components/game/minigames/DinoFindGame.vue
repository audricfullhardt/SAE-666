<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
})

const emit = defineEmits(['result'])

const DURATION = 30
const COUNT = 28

const showCountdown = ref(true)
const dinos = ref([]) // { id, emoji, isTarget, top, left, size, hue }
const timer = ref(DURATION)
const found = ref(false)
const wrongFlash = ref(false)
const circleEl = ref(null)
const dinoEls = ref({})

const distractorEmojis = ['🦕', '🦖']
const distractorHues = [120, 200, 280, 40, 320] // teintes variées (jamais rouge)

let startTime = 0
let timerId = null
let opponentId = null
let flashId = null

function setDinoEl(el, id) {
  if (el) dinoEls.value[id] = el
}

function rand(min, max) {
  return min + Math.random() * (max - min)
}

// Placement sans superposition : on rejette toute position trop proche d'un dino déjà placé
function placeWithoutOverlap(placed, minDist) {
  for (let attempt = 0; attempt < 60; attempt++) {
    const top = rand(13, 92)
    const left = rand(5, 93)
    const ok = placed.every((p) => {
      const dx = p.left - left
      const dy = p.top - top
      return Math.sqrt(dx * dx + dy * dy) >= minDist
    })
    if (ok) return { top, left }
  }
  // En dernier recours, on place quand même (densité élevée)
  return { top: rand(13, 92), left: rand(5, 93) }
}

function buildDinos() {
  const list = []
  const minDist = 11
  // dino cible : T-Rex rouge
  const targetPos = placeWithoutOverlap(list, minDist)
  list.push({
    id: 0,
    emoji: '🦖',
    isTarget: true,
    top: targetPos.top,
    left: targetPos.left,
    size: rand(1.4, 1.7),
    hue: 0,
  })
  for (let i = 1; i < COUNT; i++) {
    const pos = placeWithoutOverlap(list, minDist)
    list.push({
      id: i,
      emoji: distractorEmojis[Math.floor(Math.random() * distractorEmojis.length)],
      isTarget: false,
      top: pos.top,
      left: pos.left,
      size: rand(1.1, 1.5),
      hue: distractorHues[Math.floor(Math.random() * distractorHues.length)],
    })
  }
  dinos.value = list
}

function dinoStyle(d) {
  // Le T-Rex cible est rouge ; les distracteurs reçoivent une rotation de teinte
  const filter = d.isTarget
    ? 'sepia(1) saturate(8) hue-rotate(-20deg) brightness(1.05)'
    : `sepia(1) saturate(5) hue-rotate(${d.hue}deg)`
  return {
    top: d.top + '%',
    left: d.left + '%',
    fontSize: d.size + 'rem',
    filter,
  }
}

function tapDino(d) {
  if (found.value || showCountdown.value) return
  if (d.isTarget) {
    found.value = true
    clearTimers()
    nextTick(() => {
      const el = dinoEls.value[d.id]
      if (el && circleEl.value) {
        const rect = el.getBoundingClientRect()
        const parent = el.offsetParent.getBoundingClientRect()
        gsap.set(circleEl.value, {
          top: rect.top - parent.top + rect.height / 2,
          left: rect.left - parent.left + rect.width / 2,
          opacity: 1,
        })
        gsap.fromTo(
          circleEl.value,
          { scale: 0, rotation: -90 },
          { scale: 1, rotation: 0, duration: 0.4, ease: 'back.out(2)' }
        )
      }
    })
    setTimeout(() => emit('result', { winner: 'local', timeMs: Date.now() - startTime }), 600)
  } else {
    wrongFlash.value = true
    clearTimeout(flashId)
    flashId = setTimeout(() => (wrongFlash.value = false), 250)
  }
}

function loseByTimeout() {
  if (found.value) return
  found.value = true
  clearTimers()
  emit('result', { winner: 'opponent', timeMs: Date.now() - startTime })
}

function clearTimers() {
  clearInterval(timerId)
  clearTimeout(opponentId)
  clearTimeout(flashId)
}

function startGame() {
  showCountdown.value = false
  startTime = Date.now()
  // L'adversaire trouve entre 5 et 20s
  opponentId = setTimeout(loseByTimeout, rand(5000, 20000))
  timerId = setInterval(() => {
    timer.value--
    if (timer.value <= 0) loseByTimeout()
  }, 1000)
}

onMounted(buildDinos)
onUnmounted(clearTimers)
</script>

<template>
  <div
    class="h-screen w-screen overflow-hidden bg-foret flex flex-col p-4 select-none relative"
    :class="{ 'ring-4 ring-rouge ring-inset': wrongFlash }"
    style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0 12px, transparent 12px 24px)"
  >
    <CountdownOverlay v-if="showCountdown" bg-class="bg-foret" @done="startGame" />

    <!-- Header -->
    <header class="flex items-center justify-between z-10 shrink-0">
      <div class="flex items-center gap-2">
        <span class="text-2xl" style="filter: sepia(1) saturate(8) hue-rotate(-20deg)">🦖</span>
        <span class="font-luckiest text-white text-sm leading-tight">TROUVE LE<br />T-REX ROUGE</span>
      </div>
      <span class="w-12 h-12 rounded-full bg-jaune flex items-center justify-center font-luckiest text-xl text-foret">
        {{ String(Math.max(timer, 0)).padStart(2, '0') }}
      </span>
    </header>

    <!-- Terrain de jeu -->
    <div class="flex-1 min-h-0 relative">
      <button
        v-for="d in dinos"
        :key="d.id"
        :ref="(el) => setDinoEl(el, d.id)"
        @click="tapDino(d)"
        class="absolute -translate-x-1/2 -translate-y-1/2 leading-none active:scale-90 transition-transform"
        :style="dinoStyle(d)"
      >
        {{ d.emoji }}
      </button>

      <!-- Cercle de validation -->
      <div
        ref="circleEl"
        class="absolute w-16 h-16 rounded-full border-4 border-jaune -translate-x-1/2 -translate-y-1/2 pointer-events-none opacity-0"
      />
    </div>

    <!-- Footer -->
    <footer class="text-center text-vert font-nunito z-10 shrink-0">
      👆 Touche le bon dino pour gagner
    </footer>
  </div>
</template>
