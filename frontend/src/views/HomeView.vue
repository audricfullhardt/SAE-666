<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet'
import L from 'leaflet'

const teams = [
  {
    name: 'Pôle développement',
    border: 'border-bleu',
    title: 'text-bleu',
    badge: 'bg-bleu text-white',
    members: [
      { firstName: 'Audric', role: 'Développeur Backend', photo: null },
      { firstName: 'Lucy', role: 'Développeuse Frontend', photo: null },
    ],
  },
  {
    name: 'Pôle communication',
    border: 'border-jaune',
    title: 'text-jaune',
    badge: 'bg-jaune text-foret',
    members: [
      { firstName: 'Théo', role: 'Communication', photo: null },
      { firstName: 'Arthur', role: 'Communication', photo: null },
      { firstName: 'Valentin', role: 'Communication', photo: null },
      { firstName: 'Théo', role: 'Communication', photo: null },
    ],
  },
  {
    name: 'Pôle création',
    border: 'border-rouge',
    title: 'text-rouge',
    badge: 'bg-rouge text-white',
    members: [
      { firstName: 'Tessa', role: 'Création Graphique', photo: null },
      { firstName: 'Killian', role: 'Création Graphique', photo: null },
    ],
  },
]

const outlets = ref([])
const selected = ref(null)
const search = ref('')

const center = ref([46.6, 2.4])
const zoom = ref(5)

const markerIcon = L.divIcon({
  className: 'dino-marker',
  html: '<span style="display:block;width:18px;height:18px;border-radius:9999px;background:#01BF63;border:3px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.4)"></span>',
  iconSize: [18, 18],
  iconAnchor: [9, 9],
})

const filteredOutlets = computed(() => {
  const q = search.value.trim().toLowerCase()
  if (!q) return outlets.value
  return outlets.value.filter(
    (o) =>
      (o.city || '').toLowerCase().includes(q) || (o.name || '').toLowerCase().includes(q),
  )
})

function selectOutlet(outlet) {
  selected.value = outlet
  if (outlet.latitude != null && outlet.longitude != null) {
    center.value = [outlet.latitude, outlet.longitude]
    zoom.value = 13
  }
}

function directionsUrl(outlet) {
  return `https://www.google.com/maps/search/?api=1&query=${outlet.latitude},${outlet.longitude}`
}

onMounted(async () => {
  try {
    const res = await fetch('/api/outlets')
    if (!res.ok) return
    outlets.value = await res.json()
    const first = outlets.value.find((o) => o.latitude != null && o.longitude != null)
    if (first) {
      center.value = [first.latitude, first.longitude]
      zoom.value = 12
    }
  } catch {
  }
})
</script>

<template>
  <div>
    <section
      class="px-6 py-12"
      style="background-color: #013d22; background-image: repeating-linear-gradient(45deg, rgba(1, 191, 99, 0.1) 0, rgba(1, 191, 99, 0.1) 12px, transparent 12px, transparent 24px)"
    >
      <div class="flex items-center justify-between gap-4">
        <h1 class="font-luckiest text-4xl leading-tight text-white">
          une aventure<br />préhistorique<br />en duel !
        </h1>
        <div class="shrink-0 text-6xl">🦖</div>
      </div>
      <div class="mt-8 flex justify-center">
        <RouterLink
          to="/jeu"
          class="rounded-full bg-jaune px-10 py-3 font-luckiest text-xl tracking-wide text-white shadow-md transition hover:brightness-105"
        >
          JOUER !
        </RouterLink>
      </div>
    </section>

    <section class="bg-menthe px-6 py-12">
      <h2 class="font-luckiest text-2xl text-foret">Notre concept</h2>
      <div class="mt-4 rounded-2xl border-2 border-dashed border-gray-300 bg-white p-5">
        <p class="font-nunito text-foret/80">
          Lorem ipsum lorem ipsum lorem ipsum Lorem ipsum lorem ipsum lorem ipsum Lorem ipsum
          lorem ipsum lorem ipsum Lorem ipsum lorem ipsum lorem ipsum Lorem ipsum lorem ipsum
          lorem ipsum
        </p>
      </div>
      <div class="mt-8 flex justify-around text-center">
        <div>
          <p class="font-luckiest text-4xl text-vert">2-6</p>
          <p class="font-nunito text-sm text-foret">joueurs</p>
        </div>
        <div>
          <p class="font-luckiest text-4xl text-jaune">7-77</p>
          <p class="font-nunito text-sm text-foret">ans</p>
        </div>
        <div>
          <p class="font-luckiest text-4xl text-rouge">20'</p>
          <p class="font-nunito text-sm text-foret">par partie</p>
        </div>
      </div>
    </section>

    <section class="bg-vert px-6 py-12 text-center">
      <h2 class="font-luckiest text-2xl text-white">Nos valeurs</h2>
      <p class="mx-auto mt-5 max-w-xl font-nunito leading-loose text-white">
        Un jeu
        <span class="rounded-full bg-jaune px-2 py-0.5 font-semibold text-foret">pédagogique</span>
        pour apprendre en s'amusant, une aventure
        <span class="rounded-full bg-bleu px-2 py-0.5 font-semibold text-white">inter-générationnelle</span>
        qui rassemble petits et grands autour d'un moment résolument
        <span class="rounded-full bg-rouge px-2 py-0.5 font-semibold text-white">fun !</span>
      </p>
    </section>

    <section class="bg-menthe px-6 py-12">
      <h2 class="text-center font-luckiest text-2xl text-foret">Notre équipe</h2>
      <div class="mt-8 space-y-8">
        <div
          v-for="team in teams"
          :key="team.name"
          class="relative rounded-2xl border-2 border-dashed p-5"
          :class="team.border"
        >
          <span class="absolute -left-2 -top-3 text-2xl">🦖</span>
          <h3 class="font-luckiest text-lg" :class="team.title">{{ team.name }}</h3>
          <div class="mt-4 grid grid-cols-2 gap-4">
            <div v-for="member in team.members" :key="member.firstName + member.role">
              <div class="aspect-square overflow-hidden rounded-2xl bg-gray-200">
                <img
                  v-if="member.photo"
                  :src="member.photo"
                  :alt="member.firstName"
                  class="h-full w-full object-cover"
                />
              </div>
              <div
                class="mx-auto -mt-3 w-fit rounded-full px-4 py-1 text-center font-luckiest text-sm shadow"
                :class="team.badge"
              >
                {{ member.firstName }}
              </div>
              <p class="mt-1 text-center font-nunito text-xs text-foret/70">{{ member.role }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-vert px-6 py-12">
      <h2 class="text-center font-luckiest text-2xl text-white">Points d'achat</h2>

      <div class="relative mt-6">
        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-gray-400">🔍</span>
        <input
          v-model="search"
          type="text"
          placeholder="Rechercher une ville..."
          class="w-full rounded-full bg-white py-3 pl-11 pr-4 font-nunito text-foret placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-jaune"
        />
      </div>

      <div class="mt-4 h-64 overflow-hidden rounded-2xl">
        <LMap :zoom="zoom" :center="center" :use-global-leaflet="false" style="height: 100%; width: 100%">
          <LTileLayer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution="&copy; OpenStreetMap"
          />
          <LMarker
            v-for="o in filteredOutlets"
            :key="o.id"
            :lat-lng="[o.latitude, o.longitude]"
            :icon="markerIcon"
            @click="selectOutlet(o)"
          />
        </LMap>
      </div>

      <div v-if="selected" class="mt-4 rounded-2xl bg-white p-5">
        <h3 class="font-luckiest text-lg text-foret">{{ selected.name }}</h3>
        <p class="mt-2 font-nunito text-sm text-foret/80">
          📍 {{ [selected.address, selected.zipCode, selected.city].filter(Boolean).join(', ') }}
        </p>
        <p v-if="selected.phone" class="mt-1 font-nunito text-sm text-foret/80">
          📞 {{ selected.phone }}
        </p>
        <p v-if="selected.website" class="mt-1 font-nunito text-sm text-foret/80">
          🌐
          <a
            :href="selected.website"
            target="_blank"
            rel="noopener"
            class="text-bleu hover:underline"
            >{{ selected.website }}</a
          >
        </p>
        <a
          :href="directionsUrl(selected)"
          target="_blank"
          rel="noopener"
          class="mt-4 inline-block rounded-full bg-bleu px-6 py-2 font-luckiest text-sm tracking-wide text-white transition hover:brightness-105"
        >
          Y aller
        </a>
      </div>
    </section>

    <section class="bg-menthe px-6 py-12 text-center">
      <h2 class="font-luckiest text-2xl text-foret">Nous contacter</h2>
      <p class="mx-auto mt-4 max-w-md font-nunito text-foret">
        Tu es un joueur ? Une entreprise ? Tu souhaites nous faire part d'une idée, d'une
        suggestion, ou juste discuter ?
      </p>
      <RouterLink
        to="/contact"
        class="mt-6 inline-block rounded-full bg-rouge px-8 py-3 font-luckiest tracking-wide text-white shadow-md transition hover:brightness-105"
      >
        NOUS ÉCRIRE
      </RouterLink>
    </section>

    <footer class="bg-vert py-5 text-center">
      <p class="font-luckiest tracking-wide text-white">NotreAgence - 2026</p>
    </footer>
  </div>
</template>
