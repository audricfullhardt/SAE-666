<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import gsap from 'gsap'
import { useAuthStore } from '@/stores/auth'
import { useGameStore } from '@/stores/game'
import logo from '@/assets/logo.png'
import dinoSprite from '@/assets/dino_green.png'

const router = useRouter()
const auth = useAuthStore()
const game = useGameStore()

const dinoRef = ref(null)

const code = ref(['', '', '', '', '', ''])
const inputRefs = ref([])
const guestName = ref('')
const error = ref('')
const loading = ref(false)

const fullCode = computed(() => code.value.join(''))
const codeComplete = computed(() => fullCode.value.length === 6)

onMounted(() => {
  game.reset()
  if (dinoRef.value) {
    gsap.to(dinoRef.value, {
      y: -18,
      duration: 0.6,
      ease: 'sine.inOut',
      repeat: -1,
      yoyo: true,
    })
  }
})

function onCodeInput(index, event) {
  const raw = (event.target.value || '').toUpperCase().replace(/[^A-Z0-9]/g, '')
  code.value[index] = raw.slice(-1)
  error.value = ''
  if (code.value[index] && index < 5) {
    inputRefs.value[index + 1]?.focus()
  }
}

function onCodeKeydown(index, event) {
  if (event.key === 'Backspace' && !code.value[index] && index > 0) {
    inputRefs.value[index - 1]?.focus()
  }
}

async function createRoom() {
  if (loading.value) return
  if (!auth.isAuthenticated) {
    router.push({ name: 'auth', query: { mode: 'login', redirect: '/game' } })
    return
  }
  loading.value = true
  error.value = ''
  try {
    const created = await game.createSession()
    router.push(`/game/waiting/${created}`)
  } catch (e) {
    error.value = e.message || 'Impossible de créer la salle'
  } finally {
    loading.value = false
  }
}

async function joinRoom() {
  if (loading.value) return
  if (!codeComplete.value) {
    error.value = 'Le code doit faire 4 caractères'
    return
  }
  if (!auth.isAuthenticated && !guestName.value.trim()) {
    error.value = 'Choisis un pseudo pour rejoindre'
    return
  }
  loading.value = true
  error.value = ''
  try {
    const joined = await game.joinSession(
      fullCode.value,
      auth.isAuthenticated ? undefined : guestName.value.trim(),
    )
    router.push(`/game/waiting/${joined}`)
  } catch (e) {
    error.value = e.message || 'Impossible de rejoindre la salle'
  } finally {
    loading.value = false
  }
}

const needsName = computed(() => !auth.isAuthenticated && codeComplete.value)
watch(codeComplete, (done) => {
  if (done && needsName.value) {
    requestAnimationFrame(() => document.getElementById('guest-name')?.focus())
  }
})
</script>

<template>
  <main class="flex min-h-svh flex-col items-center justify-center gap-6 bg-foret px-6 py-10 text-center">
    <img :src="logo" alt="DinoMania" class="h-16 w-auto" />

    <p class="font-patrick text-xl text-vert">Prêt pour un duel ?</p>

    <img ref="dinoRef" :src="dinoSprite" alt="" class="h-24 w-auto drop-shadow-lg [image-rendering:pixelated]" />

    <div class="flex w-full max-w-sm flex-col gap-4">
      <button
        type="button"
        class="rounded-full bg-jaune px-6 py-4 font-luckiest text-xl tracking-wide text-white shadow-lg transition hover:brightness-105 disabled:opacity-60"
        :disabled="loading"
        @click="createRoom"
      >
        + Créer une salle
        <span class="block font-nunito text-xs font-semibold normal-case opacity-90">tu deviens l'hôte</span>
      </button>

      <div class="rounded-3xl border border-white/10 bg-black/20 p-4">
        <p class="font-luckiest text-sm tracking-wide text-white/90">Rejoindre avec un code</p>
        <div class="mt-3 flex justify-center gap-2">
          <input
            v-for="(c, i) in code"
            :key="i"
            :ref="(el) => (inputRefs[i] = el)"
            :value="code[i]"
            maxlength="1"
            inputmode="text"
            autocapitalize="characters"
            class="h-12 w-[min(13vw,3rem)] rounded-2xl bg-white text-center font-luckiest text-2xl uppercase text-foret outline-none focus:ring-2 focus:ring-vert"
            @input="onCodeInput(i, $event)"
            @keydown="onCodeKeydown(i, $event)"
            @keyup.enter="joinRoom"
          />
        </div>

        <input
          v-if="needsName"
          id="guest-name"
          v-model="guestName"
          type="text"
          placeholder="Ton pseudo"
          class="mt-3 w-full rounded-2xl bg-white px-4 py-3 text-center font-nunito text-foret outline-none focus:ring-2 focus:ring-vert"
          @keyup.enter="joinRoom"
        />

        <button
          type="button"
          class="mt-3 w-full rounded-full bg-vert px-6 py-3 font-luckiest tracking-wide text-white transition hover:brightness-105 disabled:opacity-50"
          :disabled="loading || !codeComplete"
          @click="joinRoom"
        >
          Rejoindre
        </button>
      </div>

      <p v-if="error" class="font-nunito text-sm text-rouge">{{ error }}</p>
    </div>
  </main>
</template>
