<script setup>
import { inject, ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const sidebarOpen = inject('sidebarOpen')
const toggleSidebar = inject('toggleSidebar')
const page = usePage()
const user = page.props.auth.user

const showMenu = ref(false)
const isDark = ref(document.documentElement.classList.contains('dark'))

const toggleDark = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark')
  localStorage.setItem('finca-theme', isDark.value ? 'dark' : 'light')
}

const toggleMenu = () => { showMenu.value = !showMenu.value }

const closeMenu = (e) => {
  if (!e.target.closest('.user-menu')) showMenu.value = false
}

onMounted(() => document.addEventListener('click', closeMenu))
onUnmounted(() => document.removeEventListener('click', closeMenu))
</script>

<template>
  <header
    class="flex justify-between items-center w-full sticky top-0 z-40 bg-surface h-16 transition-all duration-300"
    style="box-shadow: 0 4px 12px rgba(146, 76, 0, 0.08);"
  >
    <div class="flex items-center gap-4 pl-4 md:pl-6">
      <button
        @click="toggleSidebar"
        class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high p-2 rounded-full transition-colors active:scale-95"
      >
        {{ sidebarOpen ? 'menu_open' : 'menu' }}
      </button>
    </div>

    <div class="flex items-center gap-4 pr-4 md:pr-8">
      <div class="flex items-center gap-1 bg-primary/10 px-2 py-1 rounded-full">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
        </span>
        <span class="text-xs font-medium text-primary">Online</span>
      </div>

      <div class="flex items-center gap-2">
        <button @click="toggleDark" class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high dark:hover:bg-[#2a2a4a] p-2 rounded-full transition-colors" :title="isDark ? 'Modo claro' : 'Modo oscuro'">
          {{ isDark ? 'light_mode' : 'dark_mode' }}
        </button>
        <button class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high p-2 rounded-full transition-colors">sync</button>
        <div class="relative">
          <button class="material-symbols-outlined text-on-surface-variant hover:bg-surface-container-high p-2 rounded-full transition-colors">notifications</button>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full"></span>
        </div>

        <div class="relative user-menu">
          <button @click.stop="toggleMenu"
            class="h-8 w-8 rounded-full overflow-hidden border border-outline-variant bg-primary-container/20 flex items-center justify-center text-primary font-bold text-sm shrink-0 cursor-pointer hover:bg-primary-container/30 transition-colors">
            {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
          </button>

          <Transition name="fade">
            <div v-if="showMenu"
              class="absolute right-0 top-full mt-2 w-64 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant overflow-hidden z-50">
              <div class="p-4 border-b border-surface-container-high">
                <p class="text-sm font-semibold text-on-surface truncate">{{ user?.name || 'Usuario' }}</p>
                <p class="text-xs text-outline truncate">{{ user?.email || '' }}</p>
              </div>
              <div class="p-2 space-y-1">
                <Link :href="route('profile.edit')"
                  class="flex items-center gap-3 px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors">
                  <span class="material-symbols-outlined text-[18px]">settings</span>
                  <span>Ajustes</span>
                </Link>
                <Link :href="route('logout')" method="post" as="button"
                  class="flex items-center gap-3 px-3 py-2 text-sm text-error hover:bg-error-container rounded-lg transition-colors w-full text-left">
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
