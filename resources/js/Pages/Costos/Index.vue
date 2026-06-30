<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import UnderConstruction from '@/Components/UnderConstruction.vue'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Costos" />
    <UnderConstruction section-name="Costos" :progress="75" />
  </AuthenticatedLayout>
</template>
