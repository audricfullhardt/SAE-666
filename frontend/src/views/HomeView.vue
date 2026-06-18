<script setup>
import { useI18n } from 'vue-i18n'
import HeroSection from '@/components/home/HeroSection.vue'
import ConceptSection from '@/components/home/ConceptSection.vue'
import ValeursSection from '@/components/home/ValeursSection.vue'
import EquipeSection from '@/components/home/EquipeSection.vue'
import ActualitesSection from '@/components/home/ActualitesSection.vue'
import PointsAchatSection from '@/components/home/PointsAchatSection.vue'
import ContactSection from '@/components/home/ContactSection.vue'

const { t } = useI18n()
</script>

<template>
  <div>
    <HeroSection />
    <ConceptSection />
    <ValeursSection />
    <EquipeSection />
    <ActualitesSection />
    <PointsAchatSection />
    <ContactSection />

    <!-- Footer -->
    <div class="border-t-2 border-dashed border-rouge" />
    <footer class="bg-vert py-5 text-center">
      <p class="font-luckiest tracking-wide text-white">{{ t('home.footer.copy') }}</p>
    </footer>
  </div>
</template>
