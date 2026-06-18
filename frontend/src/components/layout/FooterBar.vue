<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<template>
  <div class="border-t-2 border-dashed border-rouge" />
  <footer class="bg-vert py-5 text-center">
    <p class="font-luckiest tracking-wide text-white">{{ t('home.footer.copy') }}</p>
  </footer>
</template>
