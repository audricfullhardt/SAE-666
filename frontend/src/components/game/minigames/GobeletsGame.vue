<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
})

const emit = defineEmits(['result'])

// phases: idle | reveal | shuffle | choose | done
const showCountdown = ref(true)
const phase = ref('idle')
const result = ref(null) // 'win' | 'lose'
const eggVisible = ref(false)

// Les 3 gobelets ont la MÊME couleur (jaune). id fixe + slot courant (0,1,2)
const cups = ref([
  { id: 0, slot: 0 },
  { id: 1, slot: 1 },
  { id: 2, slot: 2 },
])
const eggCupId = ref(1) // l'œuf est sous le gobelet du milieu au départ

const arena = ref(null)
const cupEls = ref({})
let slotWidth = 0

let timeoutIds = []
let startTime = 0

function setCupEl(el, id) {
  if (el) cupEls.value[id] = el
}

function later(fn, ms) {
  const id = setTimeout(fn, ms)
  timeoutIds.push(id)
  return id
}

function xForSlot(slot) {
  return slot * slotWidth
}

function placeCups() {
  cups.value.forEach((cup) => {
    const el = cupEls.value[cup.id]
    if (el) gsap.set(el, { x: xForSlot(cup.slot) })
  })
}

function liftCup(id, up) {
  const el = cupEls.value[id]
  if (el) gsap.to(el, { y: up ? -70 : 0, duration: 0.4, ease: 'power2.out' })
}

function startSequence() {
  phase.value = 'reveal'
  // Phase 1 — Révélation : soulever le gobelet du milieu pour montrer l'œuf
  eggVisible.value = true
  liftCup(eggCupId.value, true)
  later(() => {
    liftCup(eggCupId.value, false)
    // L'œuf est caché dès que le gobelet est reposé
    later(() => (eggVisible.value = false), 250)
    later(startShuffle, 600)
  }, 2000)
}

function swapSlots(idA, idB, duration) {
  const cupA = cups.value.find((c) => c.id === idA)
  const cupB = cups.value.find((c) => c.id === idB)
  const tmp = cupA.slot
  cupA.slot = cupB.slot
  cupB.slot = tmp
  gsap.to(cupEls.value[idA], { x: xForSlot(cupA.slot), duration, ease: 'power1.inOut' })
  gsap.to(cupEls.value[idB], { x: xForSlot(cupB.slot), duration, ease: 'power1.inOut' })
}

function startShuffle() {
  phase.value = 'shuffle'
  const swaps = 5
  let t = 0
  for (let i = 0; i < swaps; i++) {
    const duration = Math.max(0.6 - i * 0.1, 0.25) // de plus en plus rapide
    later(() => {
      const ids = [0, 1, 2]
      const a = ids.splice(Math.floor(Math.random() * 3), 1)[0]
      const b = ids[Math.floor(Math.random() * ids.length)]
      swapSlots(a, b, duration)
    }, t)
    t += duration * 1000 + 150
  }
  later(() => {
    phase.value = 'choose'
  }, t)
}

function chooseCup(cup) {
  if (phase.value !== 'choose') return
  phase.value = 'done'
  const playerWin = cup.id === eggCupId.value
  eggVisible.value = true
  liftCup(cup.id, true)
  if (!playerWin) liftCup(eggCupId.value, true)

  // L'adversaire répond 500ms après, bon 60% du temps
  const opponentWin = Math.random() < 0.6

  later(() => {
    let winner
    if (playerWin && !opponentWin) winner = 'local'
    else if (!playerWin && opponentWin) winner = 'opponent'
    else winner = playerWin ? 'local' : 'opponent' // égalité → résultat du joueur tranche
    result.value = playerWin ? 'win' : 'lose'

    if (playerWin) {
      gsap.to(cupEls.value[cup.id], { y: -90, rotation: 8, duration: 0.5, yoyo: true, repeat: 1 })
    }
    later(() => emit('result', { winner, timeMs: Date.now() - startTime }), 800)
  }, 500)
}

function startGame() {
  showCountdown.value = false
  startTime = Date.now()
  startSequence()
}

onMounted(() => {
  // Mesure de la largeur d'un slot après le rendu
  requestAnimationFrame(() => {
    if (arena.value) {
      slotWidth = arena.value.clientWidth / 3
      placeCups()
    }
  })
})

onUnmounted(() => {
  timeoutIds.forEach(clearTimeout)
  gsap.killTweensOf(Object.values(cupEls.value))
})
</script>

<template>
  <div class="h-screen w-screen overflow-hidden bg-foret flex flex-col items-center p-4 select-none relative">
    <CountdownOverlay v-if="showCountdown" bg-class="bg-foret" @done="startGame" />

    <!-- Titre -->
    <h1 class="font-luckiest text-white text-2xl text-center mt-2 shrink-0">OÙ EST L'ŒUF ?</h1>
    <p class="font-patrick text-vert text-base mt-1 text-center shrink-0">suis-le bien... ne te trompe pas !</p>

    <!-- Maman dino -->
    <div class="flex flex-col items-center mt-4 shrink-0">
      <div class="text-5xl">🦖</div>
      <p class="font-nunito text-white text-sm mt-2">la maman dino a pondu...</p>
    </div>

    <!-- Statut de phase -->
    <p class="font-patrick text-vert text-base mt-3 h-6 shrink-0">
      <span v-if="phase === 'reveal'">Regarde bien où est l'œuf !</span>
      <span v-else-if="phase === 'shuffle'">Ça mélange...</span>
      <span v-else-if="phase === 'choose'">À toi ! Choisis un gobelet</span>
      <span v-else-if="result === 'win'" class="text-jaune">Gagné ! 🎉</span>
      <span v-else-if="result === 'lose'" class="text-rouge">Perdu...</span>
    </p>

    <!-- Arène des gobelets -->
    <div class="flex-1 min-h-0 w-full flex items-center justify-center">
      <div ref="arena" class="relative w-full max-w-md" style="height: min(40vh, 12rem)">
        <div
          v-for="cup in cups"
          :key="cup.id"
          :ref="(el) => setCupEl(el, cup.id)"
          @click="chooseCup(cup)"
          class="absolute bottom-0 left-0 cursor-pointer"
          style="width: 33.333%"
        >
          <div class="relative flex flex-col items-center">
            <!-- Œuf : uniquement sous le bon gobelet, caché pendant le mélange, sous le gobelet en z-index -->
            <div
              v-if="cup.id === eggCupId"
              v-show="eggVisible"
              class="absolute bottom-2 text-4xl z-0"
            >
              🥚
            </div>
            <!-- Gobelet (toujours au-dessus de l'œuf) -->
            <div
              class="relative z-10 rounded-b-lg rounded-t-2xl shadow-lg flex items-start justify-center pt-3 bg-jaune"
              style="width: min(22vw, 5rem); height: min(30vw, 7rem)"
            >
              <span class="font-luckiest text-white text-xl">{{ cup.id + 1 }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bouton info -->
    <button
      class="bg-white text-foret font-luckiest px-8 py-3 rounded-full shadow-lg disabled:opacity-60 shrink-0"
      disabled
    >
      {{ phase === 'choose' ? 'CHOISIS UN GOBELET' : 'REGARDE...' }}
    </button>
  </div>
</template>
