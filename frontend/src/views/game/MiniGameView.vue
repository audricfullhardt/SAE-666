<script setup>
defineProps({
  game: { type: String, required: true },
})
</script>

<template>
  <main class="min-h-svh flex items-center justify-center p-6">
    <section class="w-full max-w-3xl rounded-lg bg-white p-6 text-left shadow-sm md:p-8">
      <h1 class="font-luckiest text-xl uppercase tracking-wide text-noir md:text-2xl">Mini-jeu : {{ game }}</h1>
      <p class="mt-4 font-bryndan text-xs leading-relaxed text-gray-700 md:text-sm">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non lectus vitae mauris vulputate luctus.
        Proin vitae dolor id orci dictum hendrerit.
      </p>
      <p class="mt-3 font-bryndan text-xs leading-relaxed text-gray-700 md:text-sm">
        Vivamus euismod dui vel ipsum sagittis, ut faucibus erat viverra. Praesent facilisis arcu sit amet nisi molestie,
        in pretium leo interdum.
      </p>
      <RouterLink to="/jeu" class="mt-5 inline-block font-bryndan text-sm text-vert underline underline-offset-2">
        Retour au lobby
      </RouterLink>
    </section>
  </main>
</template>
