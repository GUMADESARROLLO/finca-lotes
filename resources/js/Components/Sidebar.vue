<script setup>
import { inject } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const sidebarOpen = inject('sidebarOpen')
const toggleSidebar = inject('toggleSidebar')
const currentRoute = () => route().current()

const navItems = [
  { name: 'Panel Principal', icon: 'dashboard', route: 'dashboard' },
  { name: 'Lotes', icon: 'potted_plant', route: 'lotes.index' },
  { name: 'Ciclos de Cultivo', icon: 'autorenew', route: 'ciclos.index' },
  { name: 'Costos', icon: 'payments', route: 'costos.index' },
  { name: 'Cosechas', icon: 'agriculture', route: 'cosechas.index' },
]
</script>

<template>
  <aside
    :class="[
      'flex flex-col h-full fixed left-0 top-0 z-50 py-4 bg-[#f6f4ec] h-screen w-64 rounded-r-lg shadow-sm transition-transform duration-300',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <div class="px-4 mb-8">
      <div class="flex items-center gap-2">
        <Link :href="route('dashboard')" class="w-10 h-10 bg-[#0f5238] rounded-lg flex items-center justify-center shrink-0">
          <ApplicationLogo class="w-6 h-6" />
        </Link>
        <div class="min-w-0">
          <h1 class="text-xl font-black text-[#0f5238] truncate">Finca</h1>
          <p class="text-sm text-[#404943] opacity-70 truncate">Gestión Agrícola</p>
        </div>
      </div>
    </div>

    <nav class="flex-1 space-y-1 px-2 overflow-y-auto">
      <Link
        v-for="item in navItems"
        :key="item.route"
        :href="route(item.route)"
        class="flex items-center gap-4 px-4 py-2 text-sm rounded-lg transition-all duration-300"
        :class="currentRoute().startsWith(item.route.split('.')[0])
          ? 'bg-[#2d6a4f] text-[#a8e7c5] shadow-sm'
          : 'text-[#404943] hover:text-[#1b1c17] hover:bg-[#eae8e0]'"
      >
        <span class="material-symbols-outlined shrink-0">{{ item.icon }}</span>
        <span class="truncate">{{ item.name }}</span>
      </Link>
    </nav>

    <div class="text-center text-xs text-[#707973] border-t border-[#bfc9c1]/30 pt-3 pb-1 px-4 mt-auto">
      <p>Finca <span class="font-medium">v2.4.0</span></p>
      <p class="text-[10px]">Sistema de Gestión Agrícola</p>
    </div>
  </aside>
</template>
