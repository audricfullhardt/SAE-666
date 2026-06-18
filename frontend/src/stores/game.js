import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

export const useGameStore = defineStore('game', () => {
  const auth = useAuthStore()

  const sessionCode = ref(null)
  const sessionId = ref(null)
  const players = ref([])
  const currentPlayer = ref(null)
  const gameToken = ref(null)
  const isHost = ref(false)
  const status = ref('waiting')
  const activeMinigame = ref(null)
  // Résultat du dernier duel résolu (alimente la page de résultat, y compris pour les invités sans JWT).
  const lastResult = ref(null)
  // Gagnant déclaré en fin de partie (alimente EndView, y compris pour les invités sans JWT).
  const endWinner = ref(null)

  const STORAGE_KEY = 'dinomania_game'

  // Sauvegarde les données clés pour survivre à un refresh.
  function persist() {
    localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify({
        sessionCode: sessionCode.value,
        sessionId: sessionId.value,
        currentPlayer: currentPlayer.value,
        gameToken: gameToken.value,
        isHost: isHost.value,
        status: status.value,
      }),
    )
  }

  // Restauration immédiate depuis localStorage à l'initialisation du store.
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved) {
    try {
      const data = JSON.parse(saved)
      sessionCode.value = data.sessionCode
      sessionId.value = data.sessionId
      currentPlayer.value = data.currentPlayer
      gameToken.value = data.gameToken
      isHost.value = data.isHost
      status.value = data.status
    } catch {
      localStorage.removeItem(STORAGE_KEY)
    }
  }

  // En 1v1, l'adversaire est simplement l'autre joueur du duel.
  const opponent = computed(
    () => players.value.find((p) => currentPlayer.value && p.id !== currentPlayer.value.id) ?? null,
  )

  // Le joueur ayant rejoint (invité OU connecté) est identifié côté backend par son gameToken
  // (user = null pour tout joueur passé par /join). L'hôte, lui, est rattaché au JWT.
  // On privilégie donc X-Game-Token dès qu'il est disponible, sinon le Bearer JWT.
  function authHeaders(json = false) {
    const headers = {}
    if (json) headers['Content-Type'] = 'application/json'
    if (gameToken.value) {
      headers['X-Game-Token'] = gameToken.value
    } else if (auth.token) {
      headers['Authorization'] = `Bearer ${auth.token}`
    }
    return headers
  }

  async function parse(res) {
    return res.json().catch(() => ({}))
  }

  async function createSession() {
    const res = await fetch('/api/game/create', {
      method: 'POST',
      headers: authHeaders(),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible de créer la salle')

    sessionId.value = data.sessionId
    sessionCode.value = data.code
    gameToken.value = null
    isHost.value = true
    status.value = 'waiting'
    persist()
    await fetchSession(data.code)
    return sessionCode.value
  }

  async function joinSession(code, username) {
    const normalized = code.toUpperCase()
    const name = (username || auth.username || '').trim()
    const res = await fetch('/api/game/join', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code: normalized, username: name }),
    })
    const data = await parse(res)
    if (!res.ok) {
      if (res.status === 404) throw new Error('Salle introuvable')
      if (res.status === 409) throw new Error(data.error || 'Pseudo déjà pris')
      throw new Error(data.error || 'Impossible de rejoindre la salle')
    }

    sessionId.value = data.sessionId
    sessionCode.value = normalized
    gameToken.value = data.gameToken
    isHost.value = false
    status.value = 'waiting'
    players.value = data.players ?? []
    currentPlayer.value = players.value.find((p) => p.username === data.username) ?? null
    persist()
    return sessionCode.value
  }

  async function fetchSession(code) {
    // GET /api/game/{code} nécessite un JWT : un invité ne peut pas l'appeler.
    // - 404 → la session n'existe plus : on le signale pour que la vue fasse reset() + redirect.
    // - 401/403 → invité sans droit de lecture : on garde l'état restauré (Mercure + /join).
    const res = await fetch(`/api/game/${code}`, { headers: authHeaders() })
    if (res.status === 404) return { notFound: true }
    if (!res.ok) return null
    const data = await parse(res)

    sessionId.value = data.sessionId
    sessionCode.value = data.code
    status.value = data.status
    players.value = data.players ?? []

    if (currentPlayer.value) {
      currentPlayer.value =
        players.value.find((p) => p.id === currentPlayer.value.id) ?? currentPlayer.value
    } else if (isHost.value) {
      currentPlayer.value = players.value.find((p) => p.isHost) ?? null
    } else if (auth.username) {
      currentPlayer.value = players.value.find((p) => p.username === auth.username) ?? null
    }
    persist()
    return data
  }

  async function setReady(code) {
    const res = await fetch(`/api/game/${code}/ready`, {
      method: 'PATCH',
      headers: authHeaders(true),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible de passer prêt')
    if (currentPlayer.value) currentPlayer.value.isReady = true
    const me = players.value.find((p) => p.id === data.playerId)
    if (me) me.isReady = true
    persist()
    return data
  }

  async function startGame(code) {
    const res = await fetch(`/api/game/${code}/start`, {
      method: 'POST',
      headers: authHeaders(),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible de lancer la partie')
    status.value = 'playing'
    if (data.players) players.value = data.players
    persist()
    return data
  }

  // L'hôte termine la partie en désignant le gagnant.
  async function finishGame(code, winnerId) {
    const res = await fetch(`/api/game/${code}/finish`, {
      method: 'POST',
      headers: authHeaders(true),
      body: JSON.stringify({ winnerId }),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible de terminer la partie')
    status.value = 'finished'
    endWinner.value = data.winner ?? null
    persist()
    return data
  }

  // Alimente l'état de fin depuis l'event Mercure game_finished { winnerId, winnerUsername, players }.
  function setEndResult(data) {
    if (!data) return
    endWinner.value = {
      id: data.winnerId ?? data.winner?.id ?? null,
      username: data.winnerUsername ?? data.winner?.username ?? '',
      position: data.winner?.position ?? null,
    }
    if (Array.isArray(data.players)) players.value = data.players
    status.value = 'finished'
  }

  // Demande le retour au plateau pour toute la session (broadcast Mercure return_to_board).
  async function returnToBoard(code) {
    const res = await fetch(`/api/game/${code}/return`, {
      method: 'POST',
      headers: authHeaders(),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible de revenir au plateau')
    return data
  }

  // Déclenche un duel contre un adversaire choisi ; le backend tire le mini-jeu au sort.
  async function triggerDuel(code, opponentId) {
    const res = await fetch(`/api/game/${code}/scan`, {
      method: 'POST',
      headers: authHeaders(true),
      body: JSON.stringify({
        challengerId: currentPlayer.value?.id,
        opponentId,
      }),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible de lancer le duel')
    activeMinigame.value = data
    return data
  }

  async function fetchCurrentMinigame() {
    const res = await fetch(`/api/game/${sessionCode.value}/minigame/current`, {
      headers: authHeaders(),
    })
    if (!res.ok) return null
    const data = await parse(res)
    activeMinigame.value = data.minigame === null ? null : data
    return activeMinigame.value
  }

  async function submitResult(winnerId, loserId) {
    const res = await fetch(`/api/game/${sessionCode.value}/minigame/result`, {
      method: 'POST',
      headers: authHeaders(true),
      body: JSON.stringify({ winnerId, loserId }),
    })
    const data = await parse(res)
    if (!res.ok) throw new Error(data.error || 'Impossible d’enregistrer le résultat')
    return data
  }

  // Ajoute un joueur reçu via Mercure ; renvoie true si réellement ajouté (pour l'animation).
  function addPlayer(player) {
    if (players.value.some((p) => p.id === player.id)) return false
    players.value.push({ isReady: false, isHost: false, position: 0, ...player })
    persist()
    return true
  }

  function setPlayerReady(id) {
    const player = players.value.find((p) => p.id === id)
    if (player) player.isReady = true
    if (currentPlayer.value?.id === id) currentPlayer.value.isReady = true
    persist()
  }

  function setActiveMinigame(payload) {
    activeMinigame.value = payload
  }

  function setLastResult(payload) {
    lastResult.value = payload
  }

  function reset() {
    sessionCode.value = null
    sessionId.value = null
    players.value = []
    currentPlayer.value = null
    gameToken.value = null
    isHost.value = false
    status.value = 'waiting'
    activeMinigame.value = null
    lastResult.value = null
    endWinner.value = null
    localStorage.removeItem(STORAGE_KEY)
  }

  return {
    sessionCode,
    sessionId,
    players,
    currentPlayer,
    gameToken,
    isHost,
    status,
    activeMinigame,
    lastResult,
    endWinner,
    opponent,
    createSession,
    joinSession,
    fetchSession,
    setReady,
    startGame,
    finishGame,
    returnToBoard,
    triggerDuel,
    addPlayer,
    setPlayerReady,
    fetchCurrentMinigame,
    submitResult,
    setActiveMinigame,
    setLastResult,
    setEndResult,
    reset,
  }
})
