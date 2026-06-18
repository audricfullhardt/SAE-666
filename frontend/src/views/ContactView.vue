<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import dinoGreen from '@/assets/dino_green.png'
import dinoBleu  from '@/assets/dino_blue.png'

const { t } = useI18n()

const mode = ref('joueurs')

const joueurs  = ref({ name: '', email: '', message: '' })
const business = ref({ company: '', name: '', email: '', phone: '', message: '' })
const sentJ = ref(false)
const sentB = ref(false)

function submitJoueurs() {
  if (!joueurs.value.name || !joueurs.value.email || !joueurs.value.message) return
  sentJ.value = true
}
function submitBusiness() {
  if (!business.value.company || !business.value.email || !business.value.message) return
  sentB.value = true
}

const inputClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 font-bryndan text-sm text-gray-700 placeholder-gray-400 outline-none focus:border-vert focus:bg-white focus:ring-1 focus:ring-vert'
</script>

<template>
  <main class="min-h-svh bg-menthe">

    <!-- Page header -->
    <div class="border-b-2 border-dashed border-black bg-white">
      <div class="mx-auto max-w-4xl px-6 py-10">
        <div class="border-l-4 border-bleu pl-4">
          <h1 class="font-luckiest text-3xl uppercase tracking-wide text-noir">{{ t('pages.contact.title') }}</h1>
        </div>
        <p class="mt-4 font-bryndan text-sm text-gray-500">{{ t('pages.contact.subtitle') }}</p>
      </div>
    </div>

    <div class="mx-auto max-w-lg px-6 py-10">

      <!-- Card -->
      <div class="rounded-lg border border-dashed border-black bg-white p-6">

        <!-- Dino + subtitle -->
        <div class="mb-5 flex justify-center">
          <img
            :src="mode === 'joueurs' ? dinoGreen : dinoBleu"
            alt="" aria-hidden="true"
            class="h-14 w-auto [image-rendering:pixelated] transition-all duration-300"
          />
        </div>

        <!-- Toggle -->
        <div class="mb-6 flex gap-1 rounded-full bg-gray-100 p-1">
          <button
            type="button"
            class="flex-1 rounded-full py-2 font-luckiest text-sm tracking-wide transition"
            :class="mode === 'joueurs' ? 'bg-vert text-white shadow-sm' : 'text-gray-400'"
            @click="mode = 'joueurs'; sentJ = false"
          >
            {{ t('pages.contact.toggleJoueurs') }}
          </button>
          <button
            type="button"
            class="flex-1 rounded-full py-2 font-luckiest text-sm tracking-wide transition"
            :class="mode === 'entreprises' ? 'bg-bleu text-white shadow-sm' : 'text-gray-400'"
            @click="mode = 'entreprises'; sentB = false"
          >
            {{ t('pages.contact.toggleEntreprises') }}
          </button>
        </div>

        <!-- JOUEURS FORM -->
        <div v-if="mode === 'joueurs'">
          <div v-if="sentJ" class="py-6 text-center">
            <p class="font-luckiest text-lg uppercase tracking-wide text-vert">{{ t('pages.contact.joueurs.sent') }}</p>
            <p class="mt-1 font-bryndan text-sm text-gray-500">{{ t('pages.contact.joueurs.sentSub') }}</p>
          </div>
          <div v-else class="space-y-3">
            <input v-model="joueurs.name"    type="text"  :placeholder="t('pages.contact.joueurs.namePlaceholder')" :class="inputClass" />
            <input v-model="joueurs.email"   type="email" :placeholder="t('pages.contact.joueurs.emailPlaceholder')" :class="inputClass" />
            <textarea
              v-model="joueurs.message"
              rows="4"
              :placeholder="t('pages.contact.joueurs.messagePlaceholder')"
              :class="inputClass"
              class="resize-none"
            />
            <button
              type="button"
              class="flex w-full items-center justify-center rounded-full bg-vert py-3 font-luckiest text-sm tracking-wide text-white shadow-[0_4px_0_#019E52] transition active:translate-y-[2px] active:shadow-none hover:brightness-105"
              @click="submitJoueurs"
            >
              <span class="translate-y-[2px]">{{ t('pages.contact.joueurs.submit') }}</span>
            </button>
          </div>
        </div>

        <!-- ENTREPRISES FORM -->
        <div v-else>
          <div v-if="sentB" class="py-6 text-center">
            <p class="font-luckiest text-lg uppercase tracking-wide text-bleu">{{ t('pages.contact.entreprises.sent') }}</p>
            <p class="mt-1 font-bryndan text-sm text-gray-500">{{ t('pages.contact.entreprises.sentSub') }}</p>
          </div>
          <div v-else class="space-y-3">
            <input v-model="business.company" type="text"  :placeholder="t('pages.contact.entreprises.companyPlaceholder')" :class="inputClass" />
            <div class="flex gap-3">
              <input v-model="business.name"  type="text"  :placeholder="t('pages.contact.entreprises.namePlaceholder')" :class="inputClass" />
              <input v-model="business.phone" type="tel"   :placeholder="t('pages.contact.entreprises.phonePlaceholder')" :class="inputClass" />
            </div>
            <input v-model="business.email"   type="email" :placeholder="t('pages.contact.entreprises.emailPlaceholder')" :class="inputClass" />
            <textarea
              v-model="business.message"
              rows="4"
              :placeholder="t('pages.contact.entreprises.messagePlaceholder')"
              :class="inputClass"
              class="resize-none"
            />
            <button
              type="button"
              class="flex w-full items-center justify-center rounded-full bg-bleu py-3 font-luckiest text-sm tracking-wide text-white shadow-[0_4px_0_#0078CC] transition active:translate-y-[2px] active:shadow-none hover:brightness-105"
              @click="submitBusiness"
            >
              <span class="translate-y-[2px]">{{ t('pages.contact.entreprises.submit') }}</span>
            </button>
          </div>
        </div>

      </div>
    </div>
  </main>
</template>

