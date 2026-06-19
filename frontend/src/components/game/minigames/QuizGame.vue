<script setup>
import { ref, computed, onUnmounted } from 'vue'
import { Check } from 'lucide-vue-next'
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

const TOTAL = 5

function shuffle(arr) {
  const a = [...arr]
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[a[i], a[j]] = [a[j], a[i]]
  }
  return a
}

const quiz = shuffle(questions).slice(0, TOTAL)
const total = quiz.length

const showCountdown = ref(true)
const index = ref(0)
const score = ref(0)
const selected = ref(null)
const locked = ref(false)
const timer = ref(10)

let countdownId = null
let nextId = null

const current = computed(() => quiz[index.value])

function clearTimers() {
  clearInterval(countdownId)
  clearTimeout(nextId)
}

function startQuestion() {
  selected.value = null
  locked.value = false
  timer.value = 10

  countdownId = setInterval(() => {
    timer.value--
    if (timer.value <= 0) {
      clearInterval(countdownId)
      handleTimeout()
    }
  }, 1000)
}

function handleTimeout() {
  if (locked.value) return
  locked.value = true
  scheduleNext()
}

function choose(i) {
  if (locked.value) return
  clearInterval(countdownId)
  selected.value = i
  locked.value = true
  if (i === current.value.correct) score.value++
  scheduleNext()
}

function scheduleNext() {
  nextId = setTimeout(() => {
    if (index.value < total - 1) {
      index.value++
      startQuestion()
    } else {
      finish()
    }
  }, 1000)
}

function finish() {
  clearTimers()
  emit('result', { winner: 'local', timeMs: score.value })
}

function answerClass(i) {
  if (!locked.value) return `${colors[i]} hover:brightness-110`
  if (i === current.value.correct) return 'bg-vert'
  if (i === selected.value) return 'bg-gray-400'
  return 'bg-gray-400/60'
}

function startGame() {
  showCountdown.value = false
  startQuestion()
}

onUnmounted(clearTimers)
</script>

<template>
  <div class="h-[100dvh] w-screen overflow-hidden bg-bleu flex flex-col p-4 select-none relative">
    <CountdownOverlay v-if="showCountdown" bg-class="bg-bleu" @done="startGame" />

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

    <div class="bg-white rounded-2xl shadow-lg p-6 my-6">
      <p class="font-luckiest text-foret text-xl text-center leading-snug">
        {{ current.text }}
      </p>
    </div>

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
  </div>
</template>
