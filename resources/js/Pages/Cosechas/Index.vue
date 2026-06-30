<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Cosechas" />
    <div class="mb-8">
      <h2 class="text-3xl text-[#0f5238]">Registro de Cosechas</h2>
      <p class="text-base text-[#404943] mt-1">Captura de datos de recolección diaria.</p>
    </div>
  </AuthenticatedLayout>
</template>
