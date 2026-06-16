import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

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
    path: '/jeu',
    name: 'lobby',
    component: () => import('@/views/game/LobbyView.vue'),
    meta: { layout: 'game' },
  },
  {
    path: '/jeu/plateau',
    name: 'board',
    component: () => import('@/views/game/BoardView.vue'),
    meta: { layout: 'game' },
  },
  {
    path: '/jeu/mini-jeu/:game',
    name: 'mini-game',
    component: () => import('@/views/game/MiniGameView.vue'),
    props: true,
    meta: { layout: 'game' },
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
})

export default router
