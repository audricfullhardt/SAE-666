<script setup>
import NavBar from '@/components/layout/NavBar.vue'
import FooterBar from '@/components/layout/FooterBar.vue'
</script>

<template>
  <div class="min-h-screen bg-menthe">
    <NavBar />
    <slot />
    <FooterBar />
  </div>
</template>
