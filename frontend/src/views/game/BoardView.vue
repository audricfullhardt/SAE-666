<script setup></script>

<template>
  <main class="min-h-svh flex items-center justify-center p-6">
    <section class="w-full max-w-3xl rounded-lg bg-white p-6 text-left shadow-sm md:p-8">
      <h1 class="font-luckiest text-xl uppercase tracking-wide text-noir md:text-2xl">Plateau</h1>
      <p class="mt-4 font-bryndan text-xs leading-relaxed text-gray-700 md:text-sm">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent varius magna ac odio bibendum,
        a convallis leo varius. Sed suscipit, turpis at faucibus viverra, sem mi suscipit lorem, nec viverra sem neque ut justo.
      </p>
      <p class="mt-3 font-bryndan text-xs leading-relaxed text-gray-700 md:text-sm">
        Cras imperdiet pulvinar massa, nec hendrerit turpis vulputate et. Donec feugiat odio vel faucibus tristique.
      </p>
      <RouterLink to="/jeu" class="mt-5 inline-block font-bryndan text-sm text-vert underline underline-offset-2">
        Retour au lobby
      </RouterLink>
    </section>
  </main>
</template>
