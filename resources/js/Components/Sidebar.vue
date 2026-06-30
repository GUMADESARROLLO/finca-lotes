<script setup>
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

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
  <aside class="hidden md:flex flex-col h-full fixed left-0 top-0 z-50 py-4 bg-[#f6f4ec] h-screen w-64 rounded-r-lg shadow-sm">
    <div class="px-4 mb-8">
      <div class="flex items-center gap-2">
        <Link :href="route('dashboard')" class="w-10 h-10 bg-[#0f5238] rounded-lg flex items-center justify-center">
          <ApplicationLogo class="w-6 h-6" />
        </Link>
        <div>
          <h1 class="text-xl font-black text-[#0f5238]">Finca</h1>
          <p class="text-sm text-[#404943] opacity-70">Gestión Agrícola</p>
        </div>
      </div>
    </div>

    <nav class="flex-1 space-y-1 px-2">
      <Link
        v-for="item in navItems"
        :key="item.route"
        :href="route(item.route)"
        class="flex items-center gap-4 px-4 py-2 text-sm rounded-lg transition-all duration-300"
        :class="currentRoute().startsWith(item.route.split('.')[0])
          ? 'bg-[#2d6a4f] text-[#a8e7c5] shadow-sm'
          : 'text-[#404943] hover:text-[#1b1c17] hover:bg-[#eae8e0]'"
      >
        <span class="material-symbols-outlined">{{ item.icon }}</span>
        <span>{{ item.name }}</span>
      </Link>
    </nav>

    <div class="px-4 mb-4">
      <button class="w-full py-2 bg-[#0f5238] text-white rounded-lg text-sm flex items-center justify-center gap-1 hover:shadow-md transition-all">
        <span class="material-symbols-outlined text-[20px]">add</span>
        Nuevo Registro
      </button>
    </div>

    <div class="border-t border-[#bfc9c1]/30 pt-4 px-2">
      <Link class="flex items-center gap-4 px-4 py-2 text-sm text-[#404943] hover:text-[#1b1c17] hover:bg-[#eae8e0] rounded-lg transition-all" :href="route('profile.edit')">
        <span class="material-symbols-outlined">settings</span>
        <span>Ajustes</span>
      </Link>
      <Link class="flex items-center gap-4 px-4 py-2 text-sm text-[#404943] hover:text-[#1b1c17] hover:bg-[#eae8e0] rounded-lg transition-all" :href="route('logout')" method="post" as="button">
        <span class="material-symbols-outlined text-[#ba1a1a]">logout</span>
        <span class="text-[#ba1a1a]">Cerrar Sesión</span>
      </Link>
    </div>
  </aside>
</template>
