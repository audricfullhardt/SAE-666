<script setup>
import { computed, ref, watch } from 'vue'
import { LMap, LMarker, LPopup, LTileLayer } from '@vue-leaflet/vue-leaflet'
import { divIcon } from 'leaflet'
import 'leaflet/dist/leaflet.css'

const stores = [
  {
    id: 1,
    name: 'E.Leclerc Metz',
    address: '94 Rue aux Arènes, 57000 Metz',
    hours: 'Lun-Sam 08:30-20:30 · Dim fermé',
    lat: 49.1039,
    lng: 6.1797,
  },
  {
    id: 2,
    name: 'E.Leclerc Thionville',
    address: '11 Route d’Arlon, 57100 Thionville',
    hours: 'Lun-Sam 08:30-20:00 · Dim fermé',
    lat: 49.3606,
    lng: 6.1508,
  },
  {
    id: 3,
    name: 'E.Leclerc Nancy',
    address: '5 Rue de la Sapinière, 54520 Laxou',
    hours: 'Lun-Sam 08:30-20:30 · Dim fermé',
    lat: 48.6954,
    lng: 6.1252,
  },
  {
    id: 4,
    name: 'E.Leclerc Strasbourg',
    address: '45 Route du Rhin, 67100 Strasbourg',
    hours: 'Lun-Sam 08:30-20:30 · Dim fermé',
    lat: 48.5719,
    lng: 7.7875,
  },
  {
    id: 5,
    name: 'Cultura Metz',
    address: 'Voie Romaine, 57280 Semécourt',
    hours: 'Lun-Sam 10:00-20:00 · Dim fermé',
    lat: 49.1935,
    lng: 6.1417,
  },
  {
    id: 6,
    name: 'Cultura Nancy',
    address: '2 Rue du Bois Briand, 54320 Nancy',
    hours: 'Lun-Sam 10:00-20:00 · Dim fermé',
    lat: 48.6786,
    lng: 6.2097,
  },
  {
    id: 7,
    name: 'Fnac Metz',
    address: 'Centre Saint-Jacques, 11 Place du Forum, 57000 Metz',
    hours: 'Lun-Sam 10:00-19:30 · Dim fermé',
    lat: 49.1189,
    lng: 6.1753,
  },
  {
    id: 8,
    name: 'Fnac Nancy',
    address: '2 Rue Saint-Jean, 54000 Nancy',
    hours: 'Lun-Sam 10:00-19:30 · Dim fermé',
    lat: 48.6928,
    lng: 6.1818,
  },
  {
    id: 9,
    name: 'JouéClub Metz',
    address: 'Zone Actisud, 57130 Jouy-aux-Arches',
    hours: 'Lun-Sam 09:30-19:00 · Dim fermé',
    lat: 49.0604,
    lng: 6.0837,
  },
  {
    id: 10,
    name: 'King Jouet Strasbourg',
    address: 'Rue du Commerce, 67550 Vendenheim',
    hours: 'Lun-Sam 10:00-19:00 · Dim fermé',
    lat: 48.6616,
    lng: 7.7184,
  },
]

const mapRef = ref(null)
const zoom = ref(8)
const center = ref([48.8, 6.5])
const query = ref('')
const selectedStore = ref(stores[0])

const normalize = (value) =>
  value
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')

const rankedStores = computed(() => {
  const text = normalize(query.value.trim())
  if (!text) {
    return stores
  }

  const scored = stores
    .map((store) => {
      const haystackName = normalize(store.name)
      const haystackAddress = normalize(store.address)

      let score = 0
      if (haystackName.startsWith(text)) score += 30
      if (haystackName.includes(text)) score += 20
      if (haystackAddress.includes(text)) score += 10

      return { store, score }
    })
    .filter((item) => item.score > 0)
    .sort((a, b) => b.score - a.score)
    .map((item) => item.store)

  return scored
})

watch(rankedStores, (results) => {
  if (!results.length) return

  const best = results[0]
  selectedStore.value = best

  const map = mapRef.value?.leafletObject
  if (map) {
    map.setView([best.lat, best.lng], 13, { animate: true })
  }
}, { immediate: true })

function selectStore(store) {
  selectedStore.value = store
  const map = mapRef.value?.leafletObject
  if (map) {
    map.setView([store.lat, store.lng], 13, { animate: true })
  }
}

function goToMaps(store) {
  const destination = encodeURIComponent(`${store.name}, ${store.address}`)
  window.open(`https://www.google.com/maps/search/?api=1&query=${destination}`, '_blank')
}

function markerFor(store) {
  const isActive = selectedStore.value?.id === store.id
  const size = isActive ? 22 : 16
  return divIcon({
    className: 'custom-pin',
    html: `<span style="display:block;width:${size}px;height:${size}px;border-radius:999px;background:#01BF63;border:2px solid white;box-shadow:0 0 0 2px rgba(0,0,0,0.25)"></span>`,
    iconSize: [size, size],
    iconAnchor: [size / 2, size / 2],
  })
}
</script>

<template>
  <main class="h-[calc(100svh-64px)] overflow-hidden bg-menthe">
    <section class="grid h-full grid-cols-1 md:grid-cols-[340px_1fr]">

      <aside class="flex h-full flex-col border-b-2 border-dashed border-black bg-white md:border-b-0 md:border-r-2">
        <div class="border-b-2 border-dashed border-black p-4">
          <h1 class="font-luckiest text-lg uppercase tracking-wide text-foret">Points de vente</h1>
          <input
            v-model="query"
            type="text"
            placeholder="Rechercher une ville ou un magasin"
            class="mt-3 w-full rounded-md border border-gray-300 px-3 py-2 font-bryndan text-sm outline-none focus:border-vert"
          />
        </div>

        <div class="flex-1 overflow-y-auto p-4">
          <div v-if="selectedStore" class="rounded-md border border-dashed border-black bg-gray-50 p-3">
            <p class="font-luckiest text-sm uppercase tracking-wide text-foret">{{ selectedStore.name }}</p>
            <p class="mt-1 font-bryndan text-xs text-gray-700">{{ selectedStore.address }}</p>
            <p class="mt-1 font-bryndan text-xs text-gray-500">{{ selectedStore.hours }}</p>
            <button
              class="mt-3 inline-flex items-center rounded-full bg-vert px-4 py-2 font-luckiest text-xs uppercase tracking-wide text-white shadow-[0_4px_0_#01934D] transition active:translate-y-[2px] active:shadow-none"
              @click="goToMaps(selectedStore)"
            >
              Y aller
            </button>
          </div>

          <div class="mt-4 space-y-2">
            <button
              v-for="store in rankedStores"
              :key="store.id"
              class="w-full rounded-md border border-gray-200 bg-white p-3 text-left transition hover:border-vert"
              :class="selectedStore?.id === store.id ? 'border-vert bg-vert/5' : ''"
              @click="selectStore(store)"
            >
              <p class="font-luckiest text-sm uppercase tracking-wide text-foret">{{ store.name }}</p>
              <p class="mt-1 font-bryndan text-xs text-gray-600">{{ store.address }}</p>
            </button>

            <p v-if="rankedStores.length === 0" class="font-bryndan text-sm text-gray-500">
              Aucun résultat pour cette recherche.
            </p>
          </div>
        </div>
      </aside>

      <div class="h-full w-full">
        <l-map
          ref="mapRef"
          v-model:zoom="zoom"
          :center="center"
          :use-global-leaflet="false"
          class="h-full w-full"
        >
          <l-tile-layer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution="&copy; OpenStreetMap contributors"
          />
          <l-marker
            v-for="store in stores"
            :key="store.id"
            :lat-lng="[store.lat, store.lng]"
            :icon="markerFor(store)"
            @click="selectStore(store)"
          >
            <l-popup>
              <div class="max-w-[220px]">
                <p class="font-luckiest text-sm uppercase tracking-wide text-foret">{{ store.name }}</p>
                <p class="mt-1 font-bryndan text-xs text-gray-700">{{ store.address }}</p>
                <p class="mt-1 font-bryndan text-xs text-gray-500">{{ store.hours }}</p>
                <button
                  class="mt-2 rounded-full bg-vert px-3 py-1 font-luckiest text-xs uppercase text-white"
                  @click="goToMaps(store)"
                >
                  Y aller
                </button>
              </div>
            </l-popup>
          </l-marker>
        </l-map>
      </div>
    </section>
  </main>
</template>
