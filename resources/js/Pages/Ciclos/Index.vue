<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Ciclos" />
    <div class="mb-8">
      <h2 class="text-3xl text-[#0f5238]">Ciclos de Cultivo</h2>
      <p class="text-base text-[#404943] mt-1">Seguimiento de ciclos de siembra y cosecha.</p>
    </div>
  </AuthenticatedLayout>
</template>
