<script setup>
defineProps({
  game: { type: String, required: true },
})
</script>

<template>
  <main class="min-h-svh flex flex-col items-center justify-center gap-3 p-8 text-center">
    <h1 class="text-3xl font-semibold">Mini-jeu : {{ game }}</h1>
    <p class="text-muted-foreground">Mini-jeu 1v1 en temps réel (Mercure).</p>
    <RouterLink to="/jeu" class="text-primary underline">Retour au lobby</RouterLink>
  </main>
</template>
