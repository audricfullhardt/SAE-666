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
  <section class="bg-transparent px-6 py-14">
    <div class="mx-auto max-w-3xl">
      <h2 class="mb-8 text-center font-luckiest text-xl uppercase tracking-wide text-noir md:text-2xl">
        {{ t('home.pointsAchat.title') }}
      </h2>

      <!-- Search bar -->
      <div class="relative mb-6">
        <Search class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('home.pointsAchat.searchPlaceholder')"
          class="w-full rounded-full border border-gray-200 bg-white py-3 pl-12 pr-5 font-bryndan text-xs text-gray-500 shadow-sm outline-none focus:border-vert focus:ring-1 focus:ring-vert md:text-sm"
        />
      </div>

      <!--
        Mobile: map above, white card below with vert bottom border
        Desktop: map left + green card right, combined in one rounded-lg container
      -->
      <div class="md:flex md:overflow-hidden md:rounded-lg md:shadow-md">

        <!-- Map -->
        <div class="h-64 w-full overflow-hidden rounded-lg md:h-80 md:w-[55%] md:rounded-none" style="isolation: isolate;">
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

        <!-- Outlet detail -->
        <div
          v-if="selectedOutlet"
          class="mt-4 rounded-lg border-b-4 border-vert bg-white p-5 md:mt-0 md:flex md:w-[45%] md:flex-col md:justify-center md:rounded-none md:border-b-0 md:bg-vert md:p-8"
        >
          <h3 class="mb-4 font-luckiest text-base uppercase tracking-wide text-noir md:text-white">
            {{ selectedOutlet.name }}
          </h3>
          <ul class="mb-5 flex flex-col gap-2 font-bryndan text-xs md:text-sm">
            <li class="flex items-start gap-2 text-gray-700 md:text-white">
              <MapPin class="mt-0.5 h-4 w-4 shrink-0 text-vert md:text-white" />
              {{ selectedOutlet.address }}
            </li>
            <li class="flex items-center gap-2 text-gray-700 md:text-white">
              <Clock class="h-4 w-4 shrink-0 text-vert md:text-white" />
              {{ selectedOutlet.hours }}
            </li>
            <li class="flex items-center gap-2 text-gray-700 md:text-white">
              <Globe class="h-4 w-4 shrink-0 text-vert md:text-white" />
              {{ selectedOutlet.website }}
            </li>
          </ul>
          <div>
            <a
              :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(selectedOutlet.address)}`"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center rounded-full bg-bleu px-6 py-2 font-luckiest text-base uppercase leading-none tracking-wide text-white shadow-[0_4px_0_#0070BF] transition hover:brightness-110 active:scale-95"
            >
              <span class="translate-y-[3px]">{{ t('home.pointsAchat.goBtn') }}</span>
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>
