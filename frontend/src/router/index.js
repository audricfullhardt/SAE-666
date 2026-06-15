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
    path: '/inscription',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
  },
  {
    path: '/connexion',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { guestOnly: true },
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
  },
  {
    path: '/jeu/plateau',
    name: 'board',
    component: () => import('@/views/game/BoardView.vue'),
  },
  {
    // Ouvert via QR code, ex : /jeu/mini-jeu/quiz-dinos?match=ABC
    path: '/jeu/mini-jeu/:game',
    name: 'mini-game',
    component: () => import('@/views/game/MiniGameView.vue'),
    props: true,
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
    return { name: 'login', query: { redirect: to.fullPath } }
  }
  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'account' }
  }
})

export default router
