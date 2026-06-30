<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
  sectionName: { type: String, default: 'esta sección' },
  progress: { type: Number, default: 75 },
})

const goBack = () => router.visit(route('dashboard'))
</script>

<template>
  <div class="relative overflow-hidden flex items-center justify-center min-h-[70vh]">
    <div class="absolute w-[500px] h-[500px] top-[-10%] right-[-10%] bg-[#0f5238] rounded-full opacity-[0.15]" style="filter: blur(80px);"></div>
    <div class="absolute w-[400px] h-[400px] bottom-[-5%] left-[-5%] bg-[#924c00] rounded-full opacity-[0.15]" style="filter: blur(80px);"></div>

    <div class="max-w-2xl px-8 text-center" style="animation: fadeInUp 0.8s ease-out forwards;">
      <div class="relative inline-block mb-8">
        <div class="absolute inset-0 scale-150 border border-[#0f5238]/10 rounded-full animate-ping opacity-50"></div>
        <div class="absolute inset-0 scale-125 border border-[#924c00]/10 rounded-full"></div>
        <img alt="Gato trabajando" class="relative w-72 h-72 object-contain mx-auto drop-shadow-2xl" src="/img/gato-trabajando.png" />
      </div>

      <div class="space-y-4">
        <h2 class="text-3xl md:text-5xl text-[#0f5238] font-bold tracking-tight">Estamoss trabajando en algo grande</h2>
        <p class="text-lg text-[#404943] max-w-lg mx-auto">
          Esta sección de <span class="font-bold text-[#0f5238]">Finca</span> estará disponible muy pronto. Gracias por tu paciencia mientras cultivamos mejores herramientas para ti.
        </p>
      </div>

      <div class="mt-8 flex flex-col items-center gap-2">
        <div class="w-64 h-2 bg-[#e4e3db] rounded-full overflow-hidden">
          <div class="h-full bg-[#2d6a4f] rounded-full transition-all duration-1000 ease-out" :style="{ width: progress + '%' }"></div>
        </div>
        <div class="flex items-center gap-1 text-sm text-[#924c00]">
          <span class="material-symbols-outlined text-[18px] animate-spin">sync</span>
          <span>Progreso del desarrollo: {{ progress }}%</span>
        </div>
      </div>

      <div class="mt-8">
        <button @click="goBack"
          class="inline-flex items-center gap-2 px-8 py-3 border border-[#0f5238] text-[#0f5238] rounded-full text-sm hover:bg-[#0f5238]/5 transition-colors cursor-pointer">
          <span class="material-symbols-outlined">arrow_back</span>
          Regresar al Dashboard
        </button>
      </div>
    </div>

    <div class="absolute top-20 right-40 opacity-20" style="animation: bounce 3s infinite;">
      <span class="material-symbols-outlined text-5xl text-[#0f5238]">agriculture</span>
    </div>
    <div class="absolute bottom-40 right-20 opacity-20" style="animation: bounce 4s infinite;">
      <span class="material-symbols-outlined text-4xl text-[#924c00]">potted_plant</span>
    </div>
    <div class="absolute top-1/2 left-20 opacity-20 animate-pulse">
      <span class="material-symbols-outlined text-6xl text-[#2d6a4f]">water_drop</span>
    </div>
  </div>
</template>

<style scoped>
@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}
@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
</style>
