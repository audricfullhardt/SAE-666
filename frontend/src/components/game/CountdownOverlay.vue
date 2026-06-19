<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import gsap from 'gsap'

const props = defineProps({
  bgClass: { type: String, default: 'bg-foret' },
})

const emit = defineEmits(['done'])

const steps = ['3', '2', '1', 'GO !']
const index = ref(0)
const numEl = ref(null)
const STEP_MS = 800

let timeouts = []

function animateStep() {
  nextTick(() => {
    if (numEl.value) {
      gsap.fromTo(
        numEl.value,
        { scale: 1.5, opacity: 0 },
        { scale: 1, opacity: 1, duration: 0.8, ease: 'back.out(1.7)' }
      )
    }
  })
}

onMounted(() => {
  animateStep()
  for (let i = 1; i < steps.length; i++) {
    timeouts.push(
      setTimeout(() => {
        index.value = i
        animateStep()
      }, i * STEP_MS)
    )
  }
  timeouts.push(setTimeout(() => emit('done'), steps.length * STEP_MS))
})

onUnmounted(() => timeouts.forEach(clearTimeout))
</script>

<template>
  <div
    :class="bgClass"
    class="absolute inset-0 z-50 flex items-center justify-center select-none"
  >
    <span
      ref="numEl"
      :key="index"
      class="font-luckiest text-white leading-none text-center"
      style="font-size: clamp(3rem, 22vw, 9rem)"
    >
      {{ steps[index] }}
    </span>
  </div>
</template>
