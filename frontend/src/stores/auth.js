import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const TOKEN_KEY = 'dinoquest_token'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem(TOKEN_KEY) || null)
  const user = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  function setToken(value) {
    token.value = value
    if (value) {
      localStorage.setItem(TOKEN_KEY, value)
    } else {
      localStorage.removeItem(TOKEN_KEY)
    }
  }

  async function login(email, password) {
    const res = await fetch('/api/login_check', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username: email, password }),
    })
    if (!res.ok) throw new Error('Identifiants invalides')
    const data = await res.json()
    setToken(data.token)
    return data
  }

  function logout() {
    setToken(null)
    user.value = null
  }

  return { token, user, isAuthenticated, setToken, login, logout }
})
