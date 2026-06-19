<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import gsap from 'gsap'
import { MousePointerClick } from 'lucide-vue-next'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'
import dinoGreen from '@/assets/dino_green.png'
import dinoBlue from '@/assets/dino_blue.png'
import dinoRed from '@/assets/dino_red.png'
import dinoYellow from '@/assets/dino_yellow.png'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
})

const emit = defineEmits(['result'])

const DURATION = 30
const COUNT = 80

const showCountdown = ref(true)
const dinos = ref([])
const timer = ref(DURATION)
const found = ref(false)
const wrongFlash = ref(false)
const circleEl = ref(null)
const zoneEl = ref(null)
const dinoEls = ref({})

const targetSprite = dinoRed
const distractorSprites = [dinoGreen, dinoBlue, dinoYellow]

const MIN_DIST_PX = 25

let startTime = 0
let timerId = null
let flashId = null

function setDinoEl(el, id) {
  if (el) dinoEls.value[id] = el
}

function rand(min, max) {
  return min + Math.random() * (max - min)
}

function placeWithoutOverlap(placed, width, height) {
  for (let attempt = 0; attempt < 150; attempt++) {
    const top = rand(4, 96)
    const left = rand(4, 96)
    const ok = placed.every((p) => {
      const dx = ((p.left - left) / 100) * width
      const dy = ((p.top - top) / 100) * height
      return Math.sqrt(dx * dx + dy * dy) >= MIN_DIST_PX
    })
    if (ok) return { top, left }
  }
  return { top: rand(4, 96), left: rand(4, 96) }
}

function pickSizeClass() {
  const r = Math.random()
  if (r < 0.5) return 'h-4'
  if (r < 0.85) return 'h-5'
  return 'h-6'
}

function buildDinos() {
  const rect = zoneEl.value?.getBoundingClientRect()
  const width = rect?.width || window.innerWidth
  const height = rect?.height || window.innerHeight

  const list = []
  const targetPos = placeWithoutOverlap(list, width, height)
  list.push({
    id: 0,
    sprite: targetSprite,
    isTarget: true,
    top: targetPos.top,
    left: targetPos.left,
    sizeClass: 'h-5',
  })
  for (let i = 1; i < COUNT; i++) {
    const pos = placeWithoutOverlap(list, width, height)
    list.push({
      id: i,
      sprite: distractorSprites[Math.floor(Math.random() * distractorSprites.length)],
      isTarget: false,
      top: pos.top,
      left: pos.left,
      sizeClass: pickSizeClass(),
    })
  }
  dinos.value = list
}

function dinoStyle(d) {
  return {
    top: d.top + '%',
    left: d.left + '%',
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
  clearTimeout(flashId)
}

function startGame() {
  showCountdown.value = false
  startTime = Date.now()
  timerId = setInterval(() => {
    timer.value--
    if (timer.value <= 0) loseByTimeout()
  }, 1000)
}

onMounted(() => {
  nextTick(buildDinos)
})
onUnmounted(clearTimers)
</script>

<template>
  <div
    class="h-[100dvh] w-screen overflow-hidden bg-foret select-none relative flex flex-col"
    :class="{ 'ring-4 ring-rouge ring-inset': wrongFlash }"
    style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0 12px, transparent 12px 24px)"
  >
    <CountdownOverlay v-if="showCountdown" bg-class="bg-foret" @done="startGame" />

    <header class="flex items-center justify-between p-4 z-10 shrink-0">
      <div class="flex items-center gap-2">
        <img :src="dinoRed" alt="" class="h-8 w-auto [image-rendering:pixelated]" />
        <span class="font-luckiest text-white text-sm leading-tight">TROUVE LE<br />DINO ROUGE</span>
      </div>
      <span class="w-12 h-12 rounded-full bg-jaune flex items-center justify-center font-luckiest text-xl text-foret">
        {{ String(Math.max(timer, 0)).padStart(2, '0') }}
      </span>
    </header>

    <div ref="zoneEl" class="flex-1 min-h-0 relative overflow-hidden">
      <button
        v-for="d in dinos"
        :key="d.id"
        :ref="(el) => setDinoEl(el, d.id)"
        @click="tapDino(d)"
        class="absolute -translate-x-1/2 -translate-y-1/2 leading-none active:scale-90 transition-transform"
        :style="dinoStyle(d)"
      >
        <img
          :src="d.sprite"
          alt=""
          :class="d.sizeClass"
          class="w-auto [image-rendering:pixelated]"
        />
      </button>

      <div
        ref="circleEl"
        class="absolute w-16 h-16 rounded-full border-4 border-jaune -translate-x-1/2 -translate-y-1/2 pointer-events-none opacity-0"
      />
    </div>

    <footer class="flex items-center justify-center gap-2 p-4 text-center text-vert font-nunito z-10 shrink-0">
      <MousePointerClick class="h-5 w-5" />
      Touche le bon dino pour gagner
    </footer>
  </div>
</template>
