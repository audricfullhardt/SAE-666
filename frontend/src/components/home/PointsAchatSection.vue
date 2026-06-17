<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Search, MapPin, Clock, Globe } from 'lucide-vue-next'
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
import 'leaflet/dist/leaflet.css'

const { t } = useI18n()

const outlets = ref([
  {
    id: 1,
    name: 'Intermarché de Troyes',
    address: 'Adresse, adresse, 10000 Adresse',
    hours: 'Ouvert 7/7 24h/24',
    website: 'example.website.com',
    lat: 48.2973,
    lng: 4.0744,
  },
  {
    id: 2,
    name: 'Carrefour Troyes Nord',
    address: '10 rue Exemple, 10000 Troyes',
    hours: 'Ouvert 7/7 8h–22h',
    website: 'example.website.com',
    lat: 48.3150,
    lng: 4.0800,
  },
])

const searchQuery = ref('')
const selectedOutlet = ref(outlets.value[0])
const mapCenter = ref([48.2973, 4.0744])
const mapZoom = ref(13)

const filteredOutlets = computed(() =>
  outlets.value.filter((o) =>
    o.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    o.address.toLowerCase().includes(searchQuery.value.toLowerCase()),
  ),
)

function selectOutlet(outlet) {
  selectedOutlet.value = outlet
  mapCenter.value = [outlet.lat, outlet.lng]
}
</script>

<template>
  <section class="bg-white px-6 py-14">
    <div class="mx-auto max-w-3xl">
      <h2 class="mb-8 text-center font-luckiest text-titre1 uppercase tracking-wide text-foret md:text-titre1-md">
        {{ t('home.pointsAchat.title') }}
      </h2>

      <!-- Search bar -->
      <div class="relative mb-4">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('home.pointsAchat.searchPlaceholder')"
          class="w-full rounded-full border border-gray-200 bg-white py-2.5 pl-10 pr-4 font-bryndan text-body text-gray-700 shadow-sm outline-none focus:border-vert focus:ring-1 focus:ring-vert"
        />
      </div>

      <!-- Map: full width on mobile -->
      <div class="mb-4 h-56 w-full overflow-hidden rounded-2xl border border-gray-200 md:h-80">
        <LMap :center="mapCenter" :zoom="mapZoom" class="h-full w-full">
          <LTileLayer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution="© OpenStreetMap contributors"
          />
          <LMarker
            v-for="outlet in filteredOutlets"
            :key="outlet.id"
            :lat-lng="[outlet.lat, outlet.lng]"
            @click="selectOutlet(outlet)"
          >
            <LPopup>{{ outlet.name }}</LPopup>
          </LMarker>
        </LMap>
      </div>

      <!-- Outlet detail card: full width on mobile, side-by-side on desktop -->
      <div v-if="selectedOutlet" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:flex md:items-start md:gap-6">
        <div class="flex-1">
          <h3 class="mb-3 font-luckiest text-titre3 uppercase tracking-wide text-foret">
            {{ selectedOutlet.name }}
          </h3>
          <ul class="mb-4 flex flex-col gap-2 font-bryndan text-body text-gray-600">
            <li class="flex items-start gap-2">
              <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-vert" />
              {{ selectedOutlet.address }}
            </li>
            <li class="flex items-center gap-2">
              <Clock class="h-4 w-4 shrink-0 text-vert" />
              {{ selectedOutlet.hours }}
            </li>
            <li class="flex items-center gap-2">
              <Globe class="h-4 w-4 shrink-0 text-vert" />
              {{ selectedOutlet.website }}
            </li>
          </ul>
          <a
            :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(selectedOutlet.address)}`"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-block rounded-xl bg-vert px-6 py-2 font-luckiest text-btn uppercase tracking-wide text-white transition hover:brightness-110"
          >
            {{ t('home.pointsAchat.goBtn') }}
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
