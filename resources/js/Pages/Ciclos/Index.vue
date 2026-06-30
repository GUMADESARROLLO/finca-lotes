<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  ciclos: Array,
  destacados: Array,
  stats: Object,
})

const search = ref('')
const filtroEstado = ref('')
const filtroLote = ref('')

const ciclosFiltrados = computed(() => {
  return props.ciclos.filter(c => {
    if (filtroEstado.value && c.estado !== filtroEstado.value) return false
    if (search.value) {
      const q = search.value.toLowerCase()
      const nombre = (c.lote?.nombre + ' ' + c.cultivo?.nombre).toLowerCase()
      if (!nombre.includes(q)) return false
    }
    return true
  })
})

const lotesUnicos = computed(() => {
  const nombres = [...new Set(props.ciclos.map(c => c.lote?.nombre).filter(Boolean))]
  return nombres.sort()
})

const formatDate = (d) => {
  if (!d) return '—'
  return new Date(d).toLocaleDateString('es-NI', { day: 'numeric', month: 'short', year: 'numeric' })
}

const estadoBadge = (estado) => {
  if (estado === 'activo') return { label: 'EN CURSO', cls: 'bg-[#2d6a4f] text-[#a8e7c5]' }
  if (estado === 'cosechado') return { label: 'COMPLETADO', cls: 'bg-[#e4e3db] text-[#404943]' }
  return { label: 'PLANIFICADO', cls: 'bg-[#e4e3db] text-[#404943]' }
}

const cardStatus = (estado) => {
  if (estado === 'activo') return { border: 'border-[#0f5238]', label: 'Activo', barColor: 'bg-[#0f5238]' }
  if (estado === 'cosechado') return { border: 'border-[#bfc9c1]', label: 'Cosechado', barColor: 'bg-[#bfc9c1]' }
  return { border: 'border-[#bfc9c1]', label: 'Planificado', barColor: 'bg-[#bfc9c1]' }
}

const progressAlert = (progreso, estado) => {
  if (estado !== 'activo') return 'text-[#0f5238]'
  if (progreso < 30) return 'text-[#924c00]'
  return 'text-[#0f5238]'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Ciclos de Cultivo" />

    <div class="w-full space-y-6">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <nav class="flex items-center gap-1 text-[#404943] mb-1">
            <span class="text-xs">Dashboard</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="text-xs text-[#0f5238] font-bold">Ciclos de Cultivo</span>
          </nav>
          <h2 class="text-3xl text-[#1b1c17]">Gestión de Siembra y Cosecha</h2>
          <p class="text-base text-[#404943]">Monitoreo y administración centralizada de todos los lotes ({{ stats.total }} ciclos).</p>
        </div>
        <Link :href="route('ciclos.create')" class="bg-[#0f5238] text-white px-6 py-2 rounded-lg text-sm flex items-center gap-2 hover:brightness-110 active:scale-95 transition-all self-start" style="box-shadow: 0px 4px 12px rgba(188, 108, 37, 0.08);">
          <span class="material-symbols-outlined">add_circle</span>
          Nuevo Ciclo
        </Link>
      </div>

      <section v-if="destacados.length" class="space-y-2">
        <div class="flex items-center justify-between">
          <h3 class="text-xl text-[#1b1c17]">Ciclos Destacados / Activos</h3>
          <a class="text-[#0f5238] text-sm hover:underline" href="#">Ver todos los activos</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="c in destacados" :key="c.id"
            class="bg-white p-4 rounded-xl border border-[#bfc9c1] cursor-pointer group transition-all hover:shadow-md"
            :class="cardStatus(c.estado).border"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <p class="text-xs text-[#404943] uppercase font-bold tracking-wider">{{ c.lote?.nombre }}</p>
                <h4 class="text-sm text-[#1b1c17]">{{ c.cultivo?.nombre }}</h4>
              </div>
              <span class="material-symbols-outlined text-[#0f5238] group-hover:scale-110 transition-transform">star</span>
            </div>
            <div class="space-y-1">
              <div class="flex justify-between text-xs">
                <span class="text-[#404943]">Etapa: {{ c.etapa }}</span>
                <span class="font-bold" :class="progressAlert(c.progreso, c.estado)">{{ c.progreso }}%</span>
              </div>
              <div class="w-full bg-[#f0eee6] rounded-full h-1.5 overflow-hidden">
                <div class="h-full transition-all duration-500 rounded-full"
                  :class="cardStatus(c.estado).barColor"
                  :style="{ width: c.progreso + '%' }">
                </div>
              </div>
              <p class="text-xs text-[#404943] mt-1">Próxima cosecha: {{ formatDate(c.fecha_cosecha_estimada) }}</p>
            </div>
          </div>
        </div>
      </section>

      <div class="flex flex-col flex-1 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xl text-[#1b1c17]">Gestión e Historial de Ciclos</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm flex flex-col flex-1 overflow-hidden border border-[#bfc9c1]">
          <div class="p-4 border-b border-[#bfc9c1] space-y-4">
            <div class="flex flex-wrap items-center gap-4">
              <div class="relative flex-1 min-w-[280px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#707973]">search</span>
                <input v-model="search" type="text" placeholder="Buscar por lote, cultivo o responsable..."
                  class="w-full bg-[#f6f4ec] border border-[#bfc9c1] rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-[#0f5238] focus:border-[#0f5238] transition-all outline-none" />
              </div>
              <div class="flex items-center gap-2">
                <select v-model="filtroEstado" class="bg-[#f6f4ec] border border-[#bfc9c1] rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#0f5238] outline-none">
                  <option value="">Todos los Estados</option>
                  <option value="activo">En curso</option>
                  <option value="planificado">Planificado</option>
                  <option value="cosechado">Completado</option>
                </select>
                <select v-model="filtroLote" class="bg-[#f6f4ec] border border-[#bfc9c1] rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-[#0f5238] outline-none">
                  <option value="">Todos los Lotes</option>
                  <option v-for="l in lotesUnicos" :key="l" :value="l">{{ l }}</option>
                </select>
              </div>
            </div>
          </div>

          <div class="overflow-auto flex-1 min-h-[400px]" style="scrollbar-width: thin; scrollbar-color: #bfc9c1 transparent;">
            <table class="w-full text-left border-collapse min-w-[900px]">
              <thead class="bg-[#f6f4ec] sticky top-0 z-10 border-b border-[#bfc9c1]">
                <tr>
                  <th class="p-4 text-xs text-[#404943] w-[18%]">Lote</th>
                  <th class="p-4 text-xs text-[#404943] w-[15%]">Cultivo</th>
                  <th class="p-4 text-xs text-[#404943] w-[22%]">Progreso</th>
                  <th class="p-4 text-xs text-[#404943] w-[12%]">Inicio</th>
                  <th class="p-4 text-xs text-[#404943] w-[12%]">Cosecha Est.</th>
                  <th class="p-4 text-xs text-[#404943] w-[13%]">Estado</th>
                  <th class="p-4 text-xs text-[#404943] w-[8%] text-right">Acción</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#bfc9c1]">
                <tr v-for="c in ciclosFiltrados" :key="c.id"
                  class="hover:bg-[#f6f4ec] transition-colors"
                  :class="{ 'opacity-60': c.estado === 'cosechado' }"
                >
                  <td class="p-4"><span class="text-sm text-[#1b1c17] font-bold">{{ c.lote?.nombre || '—' }}</span></td>
                  <td class="p-4 text-sm">{{ c.cultivo?.nombre || '—' }}</td>
                  <td class="p-4">
                    <div class="space-y-1">
                      <div class="flex justify-between text-[11px] text-[#404943]">
                        <span class="font-bold">{{ c.etapa }}</span>
                        <span>{{ c.progreso }}%</span>
                      </div>
                      <div class="w-full bg-[#f0eee6] rounded-full h-1.5">
                        <div class="h-full rounded-full" :class="c.estado === 'cosechado' ? 'bg-[#bfc9c1]' : 'bg-[#0f5238]'" :style="{ width: c.progreso + '%' }"></div>
                      </div>
                    </div>
                  </td>
                  <td class="p-4 text-sm text-[#404943]">{{ formatDate(c.fecha_siembra) }}</td>
                  <td class="p-4 text-sm font-bold text-[#1b1c17]">{{ formatDate(c.fecha_cosecha_estimada) }}</td>
                  <td class="p-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold" :class="estadoBadge(c.estado).cls">
                      {{ estadoBadge(c.estado).label }}
                    </span>
                  </td>
                  <td class="p-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                      <Link :href="route('ciclos.show', c.id)" class="material-symbols-outlined text-[#404943] hover:text-[#0f5238] transition-colors text-xl">visibility</Link>
                      <button class="material-symbols-outlined text-[#404943] hover:text-[#0f5238] transition-colors text-xl">more_vert</button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!ciclosFiltrados.length">
                  <td colspan="7" class="p-8 text-center text-sm text-[#404943]">
                    <span class="material-symbols-outlined text-3xl mb-2 block">search_off</span>
                    No se encontraron ciclos con los filtros actuales.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="p-4 border-t border-[#bfc9c1] bg-[#f6f4ec] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-[#404943]">
              Mostrando <span class="font-bold">{{ ciclosFiltrados.length }}</span> de <span class="font-bold">{{ ciclos.length }}</span> ciclos
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
