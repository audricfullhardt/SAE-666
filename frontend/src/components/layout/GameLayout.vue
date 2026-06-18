<script setup>
import LogoutButton from '@/components/layout/LogoutButton.vue'
</script>

<template>
  <div class="relative min-h-screen">
    <div class="fixed right-4 top-4 z-50 rounded-full bg-white/90 px-2 py-1 shadow-md backdrop-blur">
      <LogoutButton />
    </div>

    <slot />
  </div>
</template>
