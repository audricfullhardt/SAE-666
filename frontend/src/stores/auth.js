import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const TOKEN_KEY = 'dinoquest_token'

function parseJwt(value) {
  try {
    const base64 = value.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')
    const json = decodeURIComponent(
      atob(base64)
        .split('')
        .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
        .join(''),
    )
    return JSON.parse(json)
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem(TOKEN_KEY) || null)
  const user = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  const username = computed(() => {
    if (user.value?.username) return user.value.username
    if (!token.value) return null
    const payload = parseJwt(token.value)
    return payload?.username ?? payload?.sub ?? null
  })

  function setToken(value) {
    token.value = value
    if (value) {
      localStorage.setItem(TOKEN_KEY, value)
    } else {
      localStorage.removeItem(TOKEN_KEY)
    }
  }

  async function login(username, password) {
    const res = await fetch('/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password }),
    })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) {
      throw new Error(data.message || data.error || 'Identifiants invalides')
    }
    setToken(data.token)
    return data
  }

  async function signup(email, username, password) {
    const res = await fetch('/api/auth/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, username, password }),
    })
    const data = await res.json().catch(() => ({}))
    if (!res.ok) {
      throw new Error(data.error || data.message || "L'inscription a échoué")
    }
    return data
  }

  function logout() {
    setToken(null)
    user.value = null
  }

  return { token, user, username, isAuthenticated, setToken, login, signup, logout }
})
