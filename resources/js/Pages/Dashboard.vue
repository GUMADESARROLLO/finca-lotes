<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Panel de Control" />
    <div class="mb-8">
      <h2 class="text-3xl text-[#0f5238]">Panel de Control</h2>
      <p class="text-base text-[#404943] mt-1">Resumen operativo del ciclo actual.</p>
    </div>
    <div class="flex gap-4">
      <Link :href="route('lotes.index')" class="bg-white p-6 rounded-lg shadow-sm border border-[#bfc9c1]/30 flex-1 text-center hover:bg-[#f6f4ec] transition-colors">
        <span class="material-symbols-outlined text-3xl text-[#0f5238]">potted_plant</span>
        <p class="text-sm text-[#404943] mt-2">Lotes</p>
      </Link>
      <Link :href="route('ciclos.index')" class="bg-white p-6 rounded-lg shadow-sm border border-[#bfc9c1]/30 flex-1 text-center hover:bg-[#f6f4ec] transition-colors">
        <span class="material-symbols-outlined text-3xl text-[#0f5238]">autorenew</span>
        <p class="text-sm text-[#404943] mt-2">Ciclos</p>
      </Link>
      <Link :href="route('costos.index')" class="bg-white p-6 rounded-lg shadow-sm border border-[#bfc9c1]/30 flex-1 text-center hover:bg-[#f6f4ec] transition-colors">
        <span class="material-symbols-outlined text-3xl text-[#0f5238]">payments</span>
        <p class="text-sm text-[#404943] mt-2">Costos</p>
      </Link>
      <Link :href="route('cosechas.index')" class="bg-white p-6 rounded-lg shadow-sm border border-[#bfc9c1]/30 flex-1 text-center hover:bg-[#f6f4ec] transition-colors">
        <span class="material-symbols-outlined text-3xl text-[#0f5238]">agriculture</span>
        <p class="text-sm text-[#404943] mt-2">Cosechas</p>
      </Link>
    </div>
  </AuthenticatedLayout>
</template>
