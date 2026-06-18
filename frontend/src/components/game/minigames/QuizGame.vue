<script setup>
import { ref, computed, onUnmounted } from 'vue'
import { Check, Zap } from 'lucide-vue-next'
import CountdownOverlay from '@/components/game/CountdownOverlay.vue'

const props = defineProps({
  playerName: { type: String, default: 'Toi' },
  opponentName: { type: String, default: 'Adversaire' },
})

const emit = defineEmits(['result'])

const questions = [
  {
    text: 'Quel dinosaure était le plus grand ?',
    answers: ['T-Rex', 'Argentino', 'Vélociraptor', 'Stégo'],
    correct: 1,
  },
  {
    text: 'Que mangeait le Tricératops ?',
    answers: ['Des plantes', 'De la viande', 'Des poissons', 'Des insectes'],
    correct: 0,
  },
  {
    text: 'Quel dino pouvait voler ?',
    answers: ['Diplodocus', 'Ptérodactyle', 'T-Rex', 'Ankylosaure'],
    correct: 1,
  },
  {
    text: 'Combien de cornes avait le Tricératops ?',
    answers: ['1', '2', '3', '5'],
    correct: 2,
  },
  {
    text: 'Quelle ère pour les dinosaures ?',
    answers: ['Paléozoïque', 'Mésozoïque', 'Cénozoïque', 'Précambrien'],
    correct: 1,
  },
]

const shapes = ['◆', '●', '▲', '■']
const colors = ['bg-rouge', 'bg-vert', 'bg-jaune', 'bg-[#8B5CF6]']

const showCountdown = ref(true)
const startTime = ref(0)
const index = ref(0)
const selected = ref(null) // index chosen by player
const locked = ref(false) // answer revealed, waiting next
const timer = ref(10)
const playerScore = ref(0)
const opponentScore = ref(0)
const playerAnswered = ref(false)
const opponentAnswered = ref(false)
const answeredCount = ref(0)

let countdownId = null
let opponentId = null
let nextId = null

const current = computed(() => questions[index.value])
const total = questions.length

function clearTimers() {
  clearInterval(countdownId)
  clearTimeout(opponentId)
  clearTimeout(nextId)
}

function scheduleOpponent() {
  // L'adversaire répond après un délai aléatoire de 2 à 4s (70% de réussite)
  const delay = 2000 + Math.random() * 2000
  opponentId = setTimeout(() => {
    opponentAnswered.value = true
    answeredCount.value++
    if (Math.random() < 0.7) opponentScore.value++
    maybeAdvance()
  }, delay)
}

function startQuestion() {
  selected.value = null
  locked.value = false
  timer.value = 10
  playerAnswered.value = false
  opponentAnswered.value = false
  answeredCount.value = 0

  countdownId = setInterval(() => {
    timer.value--
    if (timer.value <= 0) {
      clearInterval(countdownId)
      if (!playerAnswered.value) handleTimeout()
    }
  }, 1000)

  scheduleOpponent()
}

function handleTimeout() {
  locked.value = true
  playerAnswered.value = true
  answeredCount.value++
  maybeAdvance()
}

function choose(i) {
  if (locked.value || playerAnswered.value) return
  clearInterval(countdownId)
  selected.value = i
  locked.value = true
  playerAnswered.value = true
  answeredCount.value++
  if (i === current.value.correct) playerScore.value++
  maybeAdvance()
}

function maybeAdvance() {
  // On attend que les deux aient répondu pour passer à la suite
  if (!playerAnswered.value || !opponentAnswered.value) return
  nextId = setTimeout(() => {
    if (index.value < total - 1) {
      index.value++
      startQuestion()
    } else {
      finish()
    }
  }, 1500)
}

function finish() {
  clearTimers()
  const winner = playerScore.value >= opponentScore.value ? 'local' : 'opponent'
  emit('result', { winner, timeMs: Date.now() - startTime.value })
}

function answerClass(i) {
  if (!locked.value) return `${colors[i]} hover:brightness-110`
  if (i === current.value.correct) return 'bg-vert'
  if (i === selected.value) return 'bg-gray-400'
  return 'bg-gray-400/60'
}

function startGame() {
  showCountdown.value = false
  startTime.value = Date.now()
  startQuestion()
}

onUnmounted(clearTimers)
</script>

<template>
  <div class="h-screen w-screen overflow-hidden bg-bleu flex flex-col p-4 select-none relative">
    <CountdownOverlay v-if="showCountdown" bg-class="bg-bleu" @done="startGame" />

    <!-- Header -->
    <header class="flex items-center justify-between">
      <span class="bg-white text-bleu font-nunito font-bold px-4 py-1.5 rounded-full text-sm">
        Question {{ index + 1 }}/{{ total }}
      </span>
      <span
        class="w-12 h-12 rounded-full bg-white flex items-center justify-center font-luckiest text-xl"
        :class="timer <= 3 ? 'text-rouge' : 'text-foret'"
      >
        {{ String(timer).padStart(2, '0') }}
      </span>
    </header>

    <!-- Question -->
    <div class="bg-white rounded-2xl shadow-lg p-6 my-6">
      <p class="font-luckiest text-foret text-xl text-center leading-snug">
        {{ current.text }}
      </p>
    </div>

    <!-- Answers -->
    <div class="grid grid-cols-2 gap-3 flex-1 content-center">
      <button
        v-for="(answer, i) in current.answers"
        :key="i"
        @click="choose(i)"
        :disabled="locked"
        class="relative rounded-2xl p-4 min-h-[110px] flex items-center justify-center text-white font-luckiest text-lg transition shadow-md"
        :class="answerClass(i)"
      >
        <span class="absolute top-2 left-3 text-sm opacity-80">{{ shapes[i] }}</span>
        <span>{{ answer }}</span>
        <Check
          v-if="locked && i === current.correct"
          class="absolute top-2 right-3 h-5 w-5"
        />
      </button>
    </div>

    <!-- Footer -->
    <footer class="flex items-center justify-center gap-2 text-center text-white font-nunito mt-6">
      <Zap class="h-5 w-5" />
      {{ answeredCount }} joueur{{ answeredCount > 1 ? 's' : '' }} {{ answeredCount > 1 ? 'ont' : 'a' }} répondu
    </footer>
  </div>
</template>
