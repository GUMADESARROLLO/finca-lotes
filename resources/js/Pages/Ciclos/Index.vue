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
  if (estado === 'activo') return { label: 'EN CURSO', cls: 'bg-primary-container text-on-primary-container' }
  if (estado === 'cosechado') return { label: 'COMPLETADO', cls: 'bg-surface-container-highest text-on-surface-variant' }
  return { label: 'PLANIFICADO', cls: 'bg-surface-container-highest text-on-surface-variant' }
}

const cardStatus = (estado) => {
  if (estado === 'activo') return { border: 'border-primary', label: 'Activo', barColor: 'bg-primary' }
  if (estado === 'cosechado') return { border: 'border-outline-variant', label: 'Cosechado', barColor: 'bg-[#bfc9c1]' }
  return { border: 'border-outline-variant', label: 'Planificado', barColor: 'bg-[#bfc9c1]' }
}

const progressAlert = (progreso, estado) => {
  if (estado !== 'activo') return 'text-primary'
  if (progreso < 30) return 'text-secondary'
  return 'text-primary'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Ciclos de Cultivo" />

    <div class="w-full space-y-6">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <nav class="flex items-center gap-1 text-on-surface-variant mb-1">
            <span class="text-xs">Dashboard</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="text-xs text-primary font-bold">Ciclos de Cultivo</span>
          </nav>
          <h2 class="text-3xl text-on-surface">Gestión de Siembra y Cosecha</h2>
          <p class="text-base text-on-surface-variant">Monitoreo y administración centralizada de todos los lotes ({{ stats.total }} ciclos).</p>
        </div>
        <Link :href="route('ciclos.create')" class="bg-primary text-white px-6 py-2 rounded-lg text-sm flex items-center gap-2 hover:brightness-110 active:scale-95 transition-all self-start" style="box-shadow: 0px 4px 12px rgba(188, 108, 37, 0.08);">
          <span class="material-symbols-outlined">add_circle</span>
          Nuevo Ciclo
        </Link>
      </div>

      <section v-if="destacados.length" class="space-y-2">
        <div class="flex items-center justify-between">
          <h3 class="text-xl text-on-surface">Ciclos Destacados / Activos</h3>
          <a class="text-primary text-sm hover:underline" href="#">Ver todos los activos</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div v-for="c in destacados" :key="c.id"
            class="bg-surface-container-lowest p-4 rounded-xl border border-outline-variant cursor-pointer group transition-all hover:shadow-md"
            :class="cardStatus(c.estado).border"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <p class="text-xs text-on-surface-variant uppercase font-bold tracking-wider">{{ c.lote?.nombre }}</p>
                <h4 class="text-sm text-on-surface">{{ c.cultivo?.nombre }}</h4>
              </div>
              <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">star</span>
            </div>
            <div class="space-y-1">
              <div class="flex justify-between text-xs">
                <span class="text-on-surface-variant">Etapa: {{ c.etapa }}</span>
                <span class="font-bold" :class="progressAlert(c.progreso, c.estado)">{{ c.progreso }}%</span>
              </div>
              <div class="w-full bg-surface-container rounded-full h-1.5 overflow-hidden">
                <div class="h-full transition-all duration-500 rounded-full"
                  :class="cardStatus(c.estado).barColor"
                  :style="{ width: c.progreso + '%' }">
                </div>
              </div>
              <p class="text-xs text-on-surface-variant mt-1">Próxima cosecha: {{ formatDate(c.fecha_cosecha_estimada) }}</p>
            </div>
          </div>
        </div>
      </section>

      <div class="flex flex-col flex-1 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xl text-on-surface">Gestión e Historial de Ciclos</h3>
        </div>
        <div class="bg-surface-container-lowest rounded-xl shadow-sm flex flex-col flex-1 overflow-hidden border border-outline-variant">
          <div class="p-4 border-b border-outline-variant space-y-4">
            <div class="flex flex-wrap items-center gap-4">
              <div class="relative flex-1 min-w-[280px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input v-model="search" type="text" placeholder="Buscar por lote, cultivo o responsable..."
                  class="w-full bg-surface-container-low border border-outline-variant rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" />
              </div>
              <div class="flex items-center gap-2">
                <select v-model="filtroEstado" class="bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none">
                  <option value="">Todos los Estados</option>
                  <option value="activo">En curso</option>
                  <option value="planificado">Planificado</option>
                  <option value="cosechado">Completado</option>
                </select>
                <select v-model="filtroLote" class="bg-surface-container-low border border-outline-variant rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none">
                  <option value="">Todos los Lotes</option>
                  <option v-for="l in lotesUnicos" :key="l" :value="l">{{ l }}</option>
                </select>
              </div>
            </div>
          </div>

          <div class="overflow-auto flex-1 min-h-[400px]" style="scrollbar-width: thin; scrollbar-color: #bfc9c1 transparent;">
            <table class="w-full text-left border-collapse min-w-[900px]">
              <thead class="bg-surface-container-low sticky top-0 z-10 border-b border-outline-variant">
                <tr>
                  <th class="p-4 text-xs text-on-surface-variant w-[18%]">Lote</th>
                  <th class="p-4 text-xs text-on-surface-variant w-[15%]">Cultivo</th>
                  <th class="p-4 text-xs text-on-surface-variant w-[22%]">Progreso</th>
                  <th class="p-4 text-xs text-on-surface-variant w-[12%]">Inicio</th>
                  <th class="p-4 text-xs text-on-surface-variant w-[12%]">Cosecha Est.</th>
                  <th class="p-4 text-xs text-on-surface-variant w-[13%]">Estado</th>
                  <th class="p-4 text-xs text-on-surface-variant w-[8%] text-right">Acción</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#bfc9c1]">
                <tr v-for="c in ciclosFiltrados" :key="c.id"
                  class="hover:bg-surface-container-low transition-colors"
                  :class="{ 'opacity-60': c.estado === 'cosechado' }"
                >
                  <td class="p-4"><span class="text-sm text-on-surface font-bold">{{ c.lote?.nombre || '—' }}</span></td>
                  <td class="p-4 text-sm">{{ c.cultivo?.nombre || '—' }}</td>
                  <td class="p-4">
                    <div class="space-y-1">
                      <div class="flex justify-between text-[11px] text-on-surface-variant">
                        <span class="font-bold">{{ c.etapa }}</span>
                        <span>{{ c.progreso }}%</span>
                      </div>
                      <div class="w-full bg-surface-container rounded-full h-1.5">
                        <div class="h-full rounded-full" :class="c.estado === 'cosechado' ? 'bg-[#bfc9c1]' : 'bg-primary'" :style="{ width: c.progreso + '%' }"></div>
                      </div>
                    </div>
                  </td>
                  <td class="p-4 text-sm text-on-surface-variant">{{ formatDate(c.fecha_siembra) }}</td>
                  <td class="p-4 text-sm font-bold text-on-surface">{{ formatDate(c.fecha_cosecha_estimada) }}</td>
                  <td class="p-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold" :class="estadoBadge(c.estado).cls">
                      {{ estadoBadge(c.estado).label }}
                    </span>
                  </td>
                  <td class="p-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                      <Link :href="route('ciclos.show', c.id)" class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors text-xl">visibility</Link>
                      <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors text-xl">more_vert</button>
                    </div>
                  </td>
                </tr>
                <tr v-if="!ciclosFiltrados.length">
                  <td colspan="7" class="p-8 text-center text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-3xl mb-2 block">search_off</span>
                    No se encontraron ciclos con los filtros actuales.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="p-4 border-t border-outline-variant bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-on-surface-variant">
              Mostrando <span class="font-bold">{{ ciclosFiltrados.length }}</span> de <span class="font-bold">{{ ciclos.length }}</span> ciclos
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
