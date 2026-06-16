<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Eye, EyeOff } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const mode = ref(route.query.mode === 'register' ? 'register' : 'login')

const showPassword = ref(false)
const loading = ref(false)
const error = ref('')
const successMsg = ref('')

const username = ref('')
const password = ref('')

const email = ref('')
const regUsername = ref('')
const regPassword = ref('')
const acceptedCgu = ref(false)

const title = computed(() =>
  mode.value === 'login' ? 'Content de te revoir !' : 'Rejoins la meute !',
)

function setMode(next) {
  if (mode.value === next) return
  mode.value = next
  error.value = ''
  successMsg.value = ''
  showPassword.value = false
}

watch(
  () => route.query.mode,
  (m) => {
    mode.value = m === 'register' ? 'register' : 'login'
  },
)

async function submitLogin() {
  if (loading.value) return
  error.value = ''
  if (!username.value || !password.value) {
    error.value = 'Renseigne ton nom d’utilisateur et ton mot de passe.'
    return
  }
  loading.value = true
  try {
    await auth.login(username.value, password.value)
    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : null
    router.push(redirect || { name: 'lobby' })
  } catch (e) {
    error.value = e.message || 'Identifiants invalides'
  } finally {
    loading.value = false
  }
}

async function submitRegister() {
  if (loading.value || !acceptedCgu.value) return
  error.value = ''
  if (!email.value || !regUsername.value || !regPassword.value) {
    error.value = 'Tous les champs sont requis.'
    return
  }
  loading.value = true
  try {
    await auth.signup(email.value, regUsername.value, regPassword.value)
    setMode('login')
    successMsg.value = 'Compte créé ! Connecte-toi pour commencer.'
    username.value = regUsername.value
    email.value = ''
    regUsername.value = ''
    regPassword.value = ''
    acceptedCgu.value = false
  } catch (e) {
    error.value = e.message || "L'inscription a échoué"
  } finally {
    loading.value = false
  }
}

const inputClass =
  'w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 font-nunito text-foret placeholder-gray-400 focus:border-vert focus:outline-none focus:ring-2 focus:ring-vert/40'
</script>

<template>
  <main class="flex min-h-screen flex-col bg-foret">
    <header class="py-6 text-center">
      <h1 class="font-luckiest text-3xl tracking-wide text-vert">DINO MANIA</h1>
    </header>

    <div class="flex flex-1 items-start justify-center px-4 pb-8">
      <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-xl">
        <div class="flex justify-center"><img src="@/assets/dino_green.png" alt="" class="h-20 w-auto" /></div>

        <h2 class="mt-3 text-center font-luckiest text-3xl text-foret">{{ title }}</h2>
        <p v-if="mode === 'register'" class="text-center font-patrick text-lg text-vert">
          crée ton dino en 30 secondes
        </p>

        <div class="mt-5 flex gap-1 rounded-full bg-gray-100 p-1">
          <button
            type="button"
            class="flex-1 rounded-full py-2.5 font-luckiest text-sm tracking-wide transition"
            :class="mode === 'login' ? 'bg-vert text-white shadow' : 'text-gray-400'"
            @click="setMode('login')"
          >
            Connexion
          </button>
          <button
            type="button"
            class="flex-1 rounded-full py-2.5 font-luckiest text-sm tracking-wide transition"
            :class="mode === 'register' ? 'bg-jaune text-white shadow' : 'text-gray-400'"
            @click="setMode('register')"
          >
            Inscription
          </button>
        </div>

        <p v-if="successMsg" class="mt-3 text-center font-nunito text-sm font-semibold text-vert">
          {{ successMsg }}
        </p>

        <div v-if="mode === 'login'" class="mt-5 space-y-3">
          <input
            v-model="username"
            type="text"
            autocomplete="username"
            placeholder="Rex T."
            :class="inputClass"
            @keyup.enter="submitLogin"
          />
          <div class="relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              placeholder="Mot de passe"
              :class="inputClass"
              class="pr-12"
              @keyup.enter="submitLogin"
            />
            <button
              type="button"
              class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-foret"
              :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
              @click="showPassword = !showPassword"
            >
              <EyeOff v-if="showPassword" class="h-5 w-5" />
              <Eye v-else class="h-5 w-5" />
            </button>
          </div>
          <div class="text-right">
            <a href="#" class="font-nunito text-sm text-vert hover:underline">Mot de passe oublié ?</a>
          </div>

          <p v-if="error" class="text-center font-nunito text-sm text-rouge">{{ error }}</p>

          <button
            type="button"
            class="flex w-full items-center justify-center rounded-full bg-jaune py-3.5 font-luckiest text-lg tracking-wide text-white transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="loading"
            @click="submitLogin"
          >
            <span
              v-if="loading"
              class="mr-2 h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"
            />
            {{ loading ? 'Connexion…' : 'SE CONNECTER' }}
          </button>

          <p class="pt-1 text-center font-nunito text-sm text-foret">
            Pas encore de compte ?
            <button type="button" class="font-semibold text-vert hover:underline" @click="setMode('register')">
              Inscris-toi !
            </button>
          </p>
        </div>

        <div v-else class="mt-5 space-y-3">
          <input
            v-model="email"
            type="email"
            autocomplete="email"
            placeholder="Email"
            :class="inputClass"
            @keyup.enter="submitRegister"
          />
          <input
            v-model="regUsername"
            type="text"
            autocomplete="username"
            placeholder="Nom d'utilisateur"
            :class="inputClass"
            @keyup.enter="submitRegister"
          />
          <div class="relative">
            <input
              v-model="regPassword"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="new-password"
              placeholder="Mot de passe"
              :class="inputClass"
              class="pr-12"
              @keyup.enter="submitRegister"
            />
            <button
              type="button"
              class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-foret"
              :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
              @click="showPassword = !showPassword"
            >
              <EyeOff v-if="showPassword" class="h-5 w-5" />
              <Eye v-else class="h-5 w-5" />
            </button>
          </div>

          <label class="flex items-start gap-2 font-nunito text-sm text-foret">
            <input
              v-model="acceptedCgu"
              type="checkbox"
              class="mt-0.5 h-5 w-5 shrink-0 rounded border-gray-300 accent-vert"
            />
            <span>J'accepte les conditions d'utilisation et la politique de confidentialité.</span>
          </label>

          <p v-if="error" class="text-center font-nunito text-sm text-rouge">{{ error }}</p>

          <button
            type="button"
            class="flex w-full items-center justify-center rounded-full bg-vert py-3.5 font-luckiest text-lg tracking-wide text-white transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="loading || !acceptedCgu"
            @click="submitRegister"
          >
            <span
              v-if="loading"
              class="mr-2 h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent"
            />
            {{ loading ? 'Création…' : "S'INSCRIRE" }}
          </button>

          <p class="pt-1 text-center font-nunito text-sm text-foret">
            Déjà un compte ?
            <button type="button" class="font-semibold text-vert hover:underline" @click="setMode('login')">
              Connecte-toi !
            </button>
          </p>
        </div>
      </div>
    </div>

    <footer class="bg-vert py-4 text-center">
      <p class="font-luckiest tracking-wide text-white">DinoQuest — 2026</p>
    </footer>
  </main>
</template>
