<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import gsap from 'gsap'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'
import dinoGreen from '@/assets/dino_green.png'
import dinoBlue from '@/assets/dino_blue.png'
import dinoRed from '@/assets/dino_red.png'
import dinoYellow from '@/assets/dino_yellow.png'
import logo from '@/assets/logo.png'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
})

const emit = defineEmits(['result'])

// 4 paires (8 cartes) + 1 carte bonus révélée gratuitement = grille 3x3.
// Chaque paire = un sprite dino distinct ; la carte bonus = le logo (visuellement à part).
const pairSymbols = [dinoGreen, dinoBlue, dinoRed, dinoYellow]
const bonusSymbol = logo
const TOTAL_PAIRS = pairSymbols.length // 4

const showCountdown = ref(true)
const cards = ref([]) // { id, symbol, flipped, matched, bonus }
const flippedIds = ref([])
const lock = ref(true) // verrouillé tant que le décompte n'est pas fini
const playerPairs = ref(0)
const opponentPairs = ref(0)
const cardRefs = ref({})

let startTime = 0
let opponentId = null
let flipBackId = null

function setCardRef(el, id) {
  if (el) cardRefs.value[id] = el
}

function shuffle(arr) {
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[arr[i], arr[j]] = [arr[j], arr[i]]
  }
  return arr
}

function buildBoard() {
  const deck = [...pairSymbols, ...pairSymbols].map((symbol, i) => ({
    id: i,
    symbol,
    flipped: false,
    matched: false,
    bonus: false,
  }))
  // Carte bonus : révélée dès le départ, ne compte pas dans les paires
  deck.push({
    id: deck.length,
    symbol: bonusSymbol,
    flipped: true,
    matched: true,
    bonus: true,
  })
  cards.value = shuffle(deck)
}

function flipAnim(id) {
  const el = cardRefs.value[id]
  if (el) gsap.fromTo(el, { rotateY: -90 }, { rotateY: 0, duration: 0.3, ease: 'power2.out' })
}

function flip(card) {
  if (lock.value || card.flipped || card.matched || card.bonus) return
  card.flipped = true
  nextTick(() => flipAnim(card.id))
  flippedIds.value.push(card.id)

  if (flippedIds.value.length === 2) {
    lock.value = true
    const [a, b] = flippedIds.value.map((id) => cards.value.find((c) => c.id === id))
    if (a.symbol === b.symbol) {
      a.matched = true
      b.matched = true
      playerPairs.value++
      flippedIds.value = []
      lock.value = false
      checkEnd()
    } else {
      flipBackId = setTimeout(() => {
        a.flipped = false
        b.flipped = false
        flippedIds.value = []
        lock.value = false
      }, 1000)
    }
  }
}

function opponentTurn() {
  if (playerPairs.value + opponentPairs.value >= TOTAL_PAIRS) return
  opponentPairs.value++
  checkEnd()
  if (playerPairs.value + opponentPairs.value < TOTAL_PAIRS) {
    opponentId = setTimeout(opponentTurn, 3000 + Math.random() * 2000)
  }
}

function checkEnd() {
  const half = TOTAL_PAIRS / 2 // 2
  if (
    playerPairs.value + opponentPairs.value >= TOTAL_PAIRS ||
    playerPairs.value > half ||
    opponentPairs.value > half
  ) {
    finish()
  }
}

function finish() {
  clearTimers()
  const winner = playerPairs.value >= opponentPairs.value ? 'local' : 'opponent'
  emit('result', { winner, timeMs: Date.now() - startTime })
}

function clearTimers() {
  clearTimeout(opponentId)
  clearTimeout(flipBackId)
}

function startGame() {
  showCountdown.value = false
  lock.value = false
  startTime = Date.now()
  opponentId = setTimeout(opponentTurn, 3000 + Math.random() * 2000)
}

onMounted(buildBoard)
onUnmounted(clearTimers)
</script>

<template>
  <div class="h-screen w-screen overflow-hidden bg-vert flex flex-col p-4 select-none relative">
    <CountdownOverlay v-if="showCountdown" bg-class="bg-vert" @done="startGame" />

    <!-- Header -->
    <header class="flex items-start justify-between shrink-0">
      <div>
        <h1 class="font-luckiest text-white text-2xl">Mémoire</h1>
        <p class="font-nunito text-white/90 text-sm mt-1">
          paires trouvées : {{ playerPairs }}/{{ TOTAL_PAIRS }}
        </p>
      </div>
      <span class="font-nunito text-white/90 text-sm bg-foret/40 rounded-full px-3 py-1">
        {{ opponentName }} : {{ opponentPairs }}
      </span>
    </header>

    <!-- Grille 3x3 -->
    <div class="flex-1 min-h-0 flex items-center justify-center py-4">
      <div class="grid grid-cols-3 gap-3 w-full max-w-sm aspect-square">
        <button
          v-for="card in cards"
          :key="card.id"
          :ref="(el) => setCardRef(el, card.id)"
          @click="flip(card)"
          class="aspect-square rounded-2xl flex items-center justify-center text-4xl font-luckiest transition-colors"
          :class="
            card.bonus
              ? 'bg-jaune/80 ring-2 ring-white'
              : card.matched
                ? 'bg-vert/40 ring-2 ring-white/50'
                : card.flipped
                  ? 'bg-white'
                  : 'bg-foret text-jaune'
          "
        >
          <img
            v-if="card.flipped || card.matched"
            :src="card.symbol"
            alt=""
            class="h-12 w-auto [image-rendering:pixelated]"
          />
          <span v-else>?</span>
        </button>
      </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-white font-nunito shrink-0">
      À toi de jouer, {{ playerName }} !
    </footer>
  </div>
</template>
