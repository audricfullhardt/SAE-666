<script setup>
import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { Search, MapPin, Clock, Globe } from 'lucide-vue-next'
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
import 'leaflet/dist/leaflet.css'

const { t } = useI18n()

const outlets = ref([
  {
    id: 1,
    name: 'E.Leclerc Rosières-près-Troyes',
    address: 'Avenue du Général de Gaulle, 10410 Saint-Parres-aux-Tertres',
    hours: 'Lun-Sam 08:30-20:30 · Dim fermé',
    website: 'www.e.leclerc',
    lat: 48.3033,
    lng: 4.1189,
  },
  {
    id: 2,
    name: 'Fnac Strasbourg',
    address: '22 Place Kléber, 67000 Strasbourg',
    hours: 'Lun-Sam 10:00-19:30 · Dim fermé',
    website: 'www.fnac.com',
    lat: 48.5846,
    lng: 7.7458,
  },
  {
    id: 3,
    name: 'Cultura Metz',
    address: 'Voie Romaine, 57280 Semécourt',
    hours: 'Lun-Sam 10:00-20:00 · Dim fermé',
    website: 'www.cultura.com',
    lat: 49.1935,
    lng: 6.1417,
  },
  {
    id: 4,
    name: 'Fnac Nancy',
    address: '2 Rue Saint-Jean, 54000 Nancy',
    hours: 'Lun-Sam 10:00-19:30 · Dim fermé',
    website: 'www.fnac.com',
    lat: 48.6928,
    lng: 6.1818,
  },
  {
    id: 5,
    name: 'King Jouet Vendenheim',
    address: '1 Boulevard des Enseignes, 67550 Vendenheim',
    hours: 'Lun-Sam 10:00-19:00 · Dim fermé',
    website: 'www.king-jouet.com',
    lat: 48.6638,
    lng: 7.7123,
  },
  {
    id: 6,
    name: 'Cultura Bordeaux Bègles',
    address: 'Centre Rives d’Arcins, Rocade Sortie 20, 33130 Bègles',
    hours: 'Lun-Sam 10:00-20:00 · Dim fermé',
    website: 'www.cultura.com',
    lat: 44.8047,
    lng: -0.5455,
  },
  {
    id: 7,
    name: 'Fnac Lyon Bellecour',
    address: '85 Rue de la République, 69002 Lyon',
    hours: 'Lun-Sam 10:00-19:30 · Dim fermé',
    website: 'www.fnac.com',
    lat: 45.7597,
    lng: 4.8359,
  },
  {
    id: 8,
    name: 'E.Leclerc Blagnac',
    address: '2 Allée Emile Zola, 31700 Blagnac',
    hours: 'Lun-Sam 08:30-20:30 · Dim fermé',
    website: 'www.e.leclerc',
    lat: 43.6385,
    lng: 1.3901,
  },
  {
    id: 9,
    name: 'Fnac Lille',
    address: '20 Rue Saint-Nicolas, 59800 Lille',
    hours: 'Lun-Sam 10:00-19:30 · Dim fermé',
    website: 'www.fnac.com',
    lat: 50.6369,
    lng: 3.0625,
  },
  {
    id: 10,
    name: 'Cultura Nantes Atlantis',
    address: '6 Place Océane, 44800 Saint-Herblain',
    hours: 'Lun-Sam 10:00-20:00 · Dim fermé',
    website: 'www.cultura.com',
    lat: 47.2184,
    lng: -1.6267,
  },
])

const searchQuery = ref('')
const selectedOutlet = ref(outlets.value[0])
const mapCenter = ref([48.8, 6.5])
const mapZoom = ref(8)

const normalize = (value) =>
  value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')

const rankedOutlets = computed(() => {
  const text = normalize(searchQuery.value.trim())
  if (!text) return outlets.value

  return outlets.value
    .map((outlet) => {
      const name = normalize(outlet.name)
      const address = normalize(outlet.address)
      let score = 0
      if (name.startsWith(text)) score += 30
      if (name.includes(text)) score += 20
      if (address.includes(text)) score += 10
      return { outlet, score }
    })
    .filter((item) => item.score > 0)
    .sort((a, b) => b.score - a.score)
    .map((item) => item.outlet)
})

const filteredOutlets = computed(() =>
  searchQuery.value.trim() ? rankedOutlets.value : outlets.value,
)

watch(filteredOutlets, (results) => {
  if (!results.length) {
    selectedOutlet.value = null
    return
  }

  const best = results[0]
  selectedOutlet.value = best
  mapCenter.value = [best.lat, best.lng]
  mapZoom.value = searchQuery.value.trim() ? 13 : 8
}, { immediate: true })

function selectOutlet(outlet) {
  selectedOutlet.value = outlet
  mapCenter.value = [outlet.lat, outlet.lng]
  mapZoom.value = 13
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

        <div
          v-else
          class="mt-4 rounded-lg border-b-4 border-gray-300 bg-white p-5 md:mt-0 md:flex md:w-[45%] md:flex-col md:justify-center md:rounded-none md:border-b-0 md:bg-vert md:p-8"
        >
          <p class="font-bryndan text-sm text-gray-500 md:text-white/80">Aucun résultat pour cette recherche.</p>
        </div>

      </div>
    </div>
  </section>
</template>
