<script setup>
import NavBar from '@/components/layout/NavBar.vue'
</script>

<template>
  <div class="min-h-screen bg-menthe">
    <NavBar />
    <slot />
  </div>
</template>
