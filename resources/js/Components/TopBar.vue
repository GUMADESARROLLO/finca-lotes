<script setup>
import { inject, ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = inject('sidebarOpen')
const toggleSidebar = inject('toggleSidebar')
const page = usePage()
const user = page.props.auth.user

const showMenu = ref(false)

const toggleMenu = () => { showMenu.value = !showMenu.value }

const closeMenu = (e) => {
  if (!e.target.closest('.user-menu')) showMenu.value = false
}

onMounted(() => document.addEventListener('click', closeMenu))
onUnmounted(() => document.removeEventListener('click', closeMenu))
</script>

<template>
  <header
    class="flex justify-between items-center w-full sticky top-0 z-40 bg-[#fbf9f1] h-16 transition-all duration-300"
    style="box-shadow: 0 4px 12px rgba(146, 76, 0, 0.08);"
  >
    <div class="flex items-center gap-4 pl-4 md:pl-6">
      <button
        @click="toggleSidebar"
        class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors active:scale-95"
      >
        {{ sidebarOpen ? 'menu_open' : 'menu' }}
      </button>
    </div>

    <div class="flex items-center gap-4 pr-4 md:pr-8">
      <div class="flex items-center gap-1 bg-[#0f5238]/10 px-2 py-1 rounded-full">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0f5238] opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-[#0f5238]"></span>
        </span>
        <span class="text-xs font-medium text-[#0f5238]">Online</span>
      </div>

      <div class="flex items-center gap-2">
        <button class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors">cloud_done</button>
        <button class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors">sync</button>
        <div class="relative">
          <button class="material-symbols-outlined text-[#404943] hover:bg-[#eae8e0] p-2 rounded-full transition-colors">notifications</button>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#ba1a1a] rounded-full"></span>
        </div>

        <div class="relative user-menu">
          <button @click.stop="toggleMenu"
            class="h-8 w-8 rounded-full overflow-hidden border border-[#bfc9c1] bg-[#2d6a4f]/20 flex items-center justify-center text-[#0f5238] font-bold text-sm shrink-0 cursor-pointer hover:bg-[#2d6a4f]/30 transition-colors">
            {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
          </button>

          <Transition name="fade">
            <div v-if="showMenu"
              class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-lg border border-[#bfc9c1] overflow-hidden z-50">
              <div class="p-4 border-b border-[#eae8e0]">
                <p class="text-sm font-semibold text-[#1b1c17] truncate">{{ user?.name || 'Usuario' }}</p>
                <p class="text-xs text-[#707973] truncate">{{ user?.email || '' }}</p>
              </div>
              <div class="p-2 space-y-1">
                <Link :href="route('profile.edit')"
                  class="flex items-center gap-3 px-3 py-2 text-sm text-[#404943] hover:bg-[#f6f4ec] rounded-lg transition-colors">
                  <span class="material-symbols-outlined text-[18px]">settings</span>
                  <span>Ajustes</span>
                </Link>
                <Link :href="route('logout')" method="post" as="button"
                  class="flex items-center gap-3 px-3 py-2 text-sm text-[#ba1a1a] hover:bg-[#ffdad6] rounded-lg transition-colors w-full text-left">
                  <span class="material-symbols-outlined text-[18px]">logout</span>
                  <span>Cerrar Sesión</span>
                </Link>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease, transform 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
