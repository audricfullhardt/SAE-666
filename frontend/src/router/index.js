import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useGameStore } from '@/stores/game'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/HomeView.vue'),
  },
  {
    path: '/carte',
    name: 'map',
    component: () => import('@/views/MapView.vue'),
  },
  {
    path: '/projets',
    name: 'projects',
    component: () => import('@/views/ProjectsView.vue'),
  },
  {
    path: '/auth',
    name: 'auth',
    component: () => import('@/views/auth/AuthView.vue'),
    meta: { guestOnly: true, layout: 'blank' },
  },
  {
    path: '/inscription',
    name: 'register',
    redirect: () => ({ name: 'auth', query: { mode: 'register' } }),
  },
  {
    path: '/connexion',
    name: 'login',
    redirect: () => ({ name: 'auth', query: { mode: 'login' } }),
  },
  {
    path: '/compte',
    name: 'account',
    component: () => import('@/views/auth/AccountView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/game',
    name: 'lobby',
    component: () => import('@/views/game/LobbyView.vue'),
    meta: { layout: 'game' },
  },
  {
    path: '/game/waiting/:code',
    name: 'game-waiting',
    component: () => import('@/views/game/WaitingView.vue'),
    meta: { layout: 'game', requiresSession: true },
  },
  {
    path: '/game/minigame/:code',
    name: 'game-minigame',
    component: () => import('@/views/game/MiniGameView.vue'),
    meta: { layout: 'game', requiresSession: true },
  },
  {
    path: '/game/result/:code',
    name: 'game-result',
    component: () => import('@/views/game/ResultView.vue'),
    meta: { layout: 'game', requiresSession: true },
  },
  {
    path: '/game/end/:code',
    name: 'game-end',
    component: () => import('@/views/game/EndView.vue'),
    meta: { layout: 'game', requiresSession: true },
  },
  {
    path: '/jeu/test-mini-jeux',
    name: 'minigame-test',
    component: () => import('@/views/game/MinigameTestView.vue'),
    meta: { layout: 'blank' },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { top: 0 }
  },
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'auth', query: { mode: 'login', redirect: to.fullPath } }
  }
  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'account' }
  }
  if (to.meta.requiresSession) {
    const game = useGameStore()
    // L'accès est autorisé dès qu'une session est présente dans le store (restaurée depuis
    // localStorage au refresh). Un invité est identifié par son gameToken, pas par un JWT :
    // sessionCode et gameToken étant persistés ensemble, l'invité reste admis après refresh.
    if (!game.sessionCode && !game.gameToken) {
      return { name: 'lobby' }
    }
  }
})

export default router
