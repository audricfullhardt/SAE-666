<script setup>
import { computed } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import AppLayout from '@/components/layout/AppLayout.vue'
import GameLayout from '@/components/layout/GameLayout.vue'

const route = useRoute()
const layout = computed(() => route.meta.layout || 'app')
</script>

<template>
  <GameLayout v-if="layout === 'game'">
    <RouterView />
  </GameLayout>
  <RouterView v-else-if="layout === 'blank'" />
  <AppLayout v-else>
    <RouterView />
  </AppLayout>
</template>
