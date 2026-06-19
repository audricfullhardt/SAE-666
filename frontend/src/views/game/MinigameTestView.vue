<script setup>
import { ref } from 'vue'
import QuizGame from '@/components/game/minigames/QuizGame.vue'
import ReflexGame from '@/components/game/minigames/ReflexGame.vue'
import MemoryGame from '@/components/game/minigames/MemoryGame.vue'
import BrasDeferGame from '@/components/game/minigames/BrasDeferGame.vue'
import GobeletsGame from '@/components/game/minigames/GobeletsGame.vue'
import DinoFindGame from '@/components/game/minigames/DinoFindGame.vue'

const games = [
  { key: 'quiz', label: '1 · Quiz', component: QuizGame },
  { key: 'reflex', label: '2 · Réflexe', component: ReflexGame },
  { key: 'memory', label: '3 · Mémoire', component: MemoryGame },
  { key: 'brasdefer', label: '4 · Bras de fer', component: BrasDeferGame },
  { key: 'gobelets', label: '5 · Gobelets', component: GobeletsGame },
  { key: 'dinofind', label: '6 · Où est le dino', component: DinoFindGame },
]

const current = ref(null)
const lastResult = ref(null)
const playKey = ref(0)

function play(key) {
  lastResult.value = null
  current.value = key
  playKey.value++
}

function onResult(payload) {
  lastResult.value = payload
}

function restart() {
  playKey.value++
  lastResult.value = null
}

function back() {
  current.value = null
  lastResult.value = null
}
</script>

<template>
  <div v-if="!current" class="min-h-screen w-full bg-menthe flex flex-col items-center justify-center gap-4 p-8">
    <h1 class="font-luckiest text-foret text-4xl">DinoQuest — Mini-jeux</h1>
    <p class="font-nunito text-foret/70">Joueur : Rex · Adversaire : Tia</p>
    <div class="grid grid-cols-2 gap-3 w-full max-w-md mt-4">
      <button
        v-for="g in games"
        :key="g.key"
        @click="play(g.key)"
        class="bg-vert text-white font-luckiest text-lg py-5 rounded-2xl shadow-md hover:brightness-110 transition"
      >
        {{ g.label }}
      </button>
    </div>
  </div>

  <div v-else class="relative">
    <component
      :is="games.find((g) => g.key === current).component"
      :key="playKey"
      player-name="Rex"
      opponent-name="Tia"
      @result="onResult"
    />

    <div class="fixed top-2 left-1/2 -translate-x-1/2 z-50 flex gap-2">
      <button @click="back" class="bg-black/70 text-white font-nunito text-xs px-3 py-1.5 rounded-full">
        ← Menu
      </button>
      <button @click="restart" class="bg-black/70 text-white font-nunito text-xs px-3 py-1.5 rounded-full">
        ↻ Rejouer
      </button>
    </div>

    <div
      v-if="lastResult"
      class="fixed inset-0 z-40 bg-black/60 flex items-center justify-center"
      @click="restart"
    >
      <div class="bg-white rounded-3xl p-8 text-center shadow-2xl">
        <p class="font-luckiest text-3xl" :class="lastResult.winner === 'local' ? 'text-vert' : 'text-rouge'">
          {{ lastResult.winner === 'local' ? 'VICTOIRE !' : 'DÉFAITE' }}
        </p>
        <p class="font-nunito text-foret/70 mt-2">timeMs : {{ lastResult.timeMs }}</p>
        <button @click.stop="restart" class="mt-4 bg-vert text-white font-luckiest px-6 py-2 rounded-full">
          Rejouer
        </button>
      </div>
    </div>
  </div>
</template>
