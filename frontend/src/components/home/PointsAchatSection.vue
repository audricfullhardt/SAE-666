<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Search, MapPin, Clock, Globe } from 'lucide-vue-next'
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
import 'leaflet/dist/leaflet.css'

const { t } = useI18n()

/** Outlet data – swap for API results when backend is ready */
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

/** Map initial view */
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
      <!-- Section title -->
      <h2 class="mb-8 text-center font-luckiest text-3xl uppercase tracking-wide text-gray-800 md:text-4xl">
        {{ t('home.pointsAchat.title') }}
      </h2>

      <!-- Search bar -->
      <div class="relative mb-5">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('home.pointsAchat.searchPlaceholder')"
          class="w-full rounded-xl border border-gray-200 py-2.5 pl-10 pr-4 font-bryndan
                 text-sm text-gray-700 outline-none focus:border-vert focus:ring-1 focus:ring-vert"
        />
      </div>

      <!-- Map + detail – always stacked (map first, detail card below), side-by-side on desktop -->
      <div class="flex flex-col gap-5 md:flex-row md:items-start">
        <!-- Leaflet map -->
        <div class="h-64 flex-1 overflow-hidden rounded-xl border border-gray-200 md:h-80">
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

        <!-- Selected outlet detail card -->
        <div
          v-if="selectedOutlet"
          class="w-full rounded-xl border border-gray-200 bg-gray-50 p-5 shadow-sm md:w-64"
        >
          <h3 class="mb-3 font-luckiest text-base uppercase tracking-wide text-foret">
            {{ selectedOutlet.name }}
          </h3>
          <ul class="mb-4 flex flex-col gap-2 font-bryndan text-sm text-gray-600">
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
            class="block w-full rounded-xl bg-vert py-2 text-center font-luckiest
                   text-sm uppercase tracking-wide text-white transition hover:brightness-110"
          >
            {{ t('home.pointsAchat.goBtn') }}
          </a>
        </div>
      </div>
    </div>
  </section>
</template>
