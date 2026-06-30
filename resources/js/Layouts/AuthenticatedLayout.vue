<script setup>
import { ref, provide } from 'vue'
import Sidebar from '@/Components/Sidebar.vue'
import TopBar from '@/Components/TopBar.vue'
import { Link } from '@inertiajs/vue3'

const sidebarOpen = ref(true)
provide('sidebarOpen', sidebarOpen)

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}
provide('toggleSidebar', toggleSidebar)

const currentRoute = () => route().current()

const mobileNav = [
  { name: 'Inicio', icon: 'dashboard', route: 'dashboard' },
  { name: 'Lotes', icon: 'potted_plant', route: 'lotes.index' },
  { name: 'Costos', icon: 'payments', route: 'costos.index' },
  { name: 'Cosecha', icon: 'agriculture', route: 'cosechas.index' },
  { name: 'Ajustes', icon: 'settings', route: 'profile.edit' },
]
</script>

<template>
  <div class="min-h-screen bg-[#fbf9f1]">
    <Sidebar />

    <main :class="['min-h-screen pb-20 md:pb-0 transition-all duration-300', sidebarOpen ? 'md:ml-64' : 'md:ml-0']">
      <TopBar @toggle-sidebar="toggleSidebar" />

      <div :class="['px-4 pt-4 md:pt-6 transition-all duration-300', sidebarOpen ? 'md:px-12' : 'md:px-8']">
        <slot />
      </div>
    </main>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-[#fbf9f1] flex justify-around items-center z-50" style="box-shadow: 0 -2px 12px rgba(146, 76, 0, 0.08);">
      <Link
        v-for="item in mobileNav"
        :key="item.route"
        :href="route(item.route)"
        class="flex flex-col items-center gap-1"
        :class="currentRoute() === item.route ? 'text-[#0f5238]' : 'text-[#404943]'"
      >
        <span class="material-symbols-outlined" :class="{ 'font-variation-settings': 'FILL 1' }">{{ item.icon }}</span>
        <span class="text-[10px] font-medium">{{ item.name }}</span>
      </Link>
    </nav>
  </div>
</template>
