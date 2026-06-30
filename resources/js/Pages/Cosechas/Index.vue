<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import UnderConstruction from '@/Components/UnderConstruction.vue'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Cosechas" />
    <UnderConstruction section-name="Cosechas" :progress="75" />
  </AuthenticatedLayout>
</template>
