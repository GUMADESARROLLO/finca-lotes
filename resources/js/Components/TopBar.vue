<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user
</script>

<template>
  <header class="ml-64 flex justify-between items-center px-12 w-[calc(100%-16rem)] sticky top-0 z-40 bg-[#fbf9f1] h-16" style="box-shadow: 0 4px 12px rgba(146, 76, 0, 0.08);">
    <div class="flex items-center gap-4">
      <div class="relative flex items-center">
        <span class="material-symbols-outlined absolute left-3 text-[#707973]">search</span>
        <input class="pl-10 pr-4 py-1.5 bg-[#f6f4ec] border-none rounded-full text-base focus:ring-2 focus:ring-[#2d6a4f] w-64" placeholder="Buscar registros..." type="text" />
      </div>
    </div>

    <div class="flex items-center gap-6">
      <div class="flex items-center gap-1 bg-[#0f5238]/10 px-2 py-1 rounded-full">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0f5238] opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0f5238]"></span>
        </span>
        <span class="text-xs font-medium text-[#0f5238]">Online</span>
      </div>

      <div class="flex items-center gap-4">
        <button class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors">cloud_done</button>
        <button class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors">sync</button>
        <div class="relative">
          <button class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors">notifications</button>
          <span class="absolute top-1 right-1 w-2 h-2 bg-[#ba1a1a] rounded-full"></span>
        </div>
        <div class="h-8 w-8 rounded-full overflow-hidden border border-[#bfc9c1] bg-[#2d6a4f]/20 flex items-center justify-center text-[#0f5238] font-bold text-sm">
          {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
        </div>
      </div>
    </div>
  </header>
</template>
