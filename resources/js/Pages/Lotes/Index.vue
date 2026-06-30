<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, onMounted, ref, onUnmounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({ lotes: Array, pagination: Object })
const mapContainer = ref(null)
let map = null

const borderColor = (ciclo) => {
  if (!ciclo) return 'border-[#bfc9c1]'
  if (ciclo.estado === 'activo') return 'border-[#0f5238]'
  if (ciclo.estado === 'cosechado') return 'border-[#fda055]'
  return 'border-[#bfc9c1]'
}

const badgeInfo = (ciclo) => {
  if (!ciclo) return { label: 'Sin actividad', icon: 'remove_circle', cls: 'bg-[#e4e3db] text-[#404943]' }
  if (ciclo.estado === 'activo') return { label: 'En crecimiento', icon: 'eco', cls: 'bg-[#2d6a4f] text-[#a8e7c5]' }
  if (ciclo.estado === 'cosechado') return { label: 'Cosechado', icon: 'check_circle', cls: 'bg-[#fda055] text-[#703800]' }
  return { label: 'Planificado', icon: 'event', cls: 'bg-[#e4e3db] text-[#404943]' }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('es-NI', { day: 'numeric', month: 'short', year: 'numeric' })
}

const goToPage = (page) => router.get(route('lotes.index', { page }), { preserveState: true })

const polygonColor = (ciclo) => {
  if (!ciclo) return '#bfc9c1'
  if (ciclo.estado === 'activo') return '#0f5238'
  if (ciclo.estado === 'cosechado') return '#fda055'
  return '#bfc9c1'
}

const initMap = () => {
  if (!mapContainer.value || props.lotes.length === 0) return
  map = L.map(mapContainer.value)
  const bounds = L.latLngBounds()

  props.lotes.forEach(lote => {
    const poly = lote.poligono
    const coords = poly ? (typeof poly === 'string' ? JSON.parse(poly) : poly) : null

    if (coords && coords.length >= 3) {
      const pts = coords.map(c => [c[0], c[1]])
      pts.forEach(p => bounds.extend(p))
      const color = polygonColor(lote.ciclo_actual)
      L.polygon(pts, {
        color, fillColor: color, fillOpacity: 0.12, weight: 2,
      }).addTo(map)
        .bindPopup(`<b>${lote.nombre}</b><br>${lote.ciclo_actual?.cultivo?.nombre || '—'}<br>${badgeInfo(lote.ciclo_actual).label}`)
    } else if (lote.lat && lote.lng) {
      bounds.extend([lote.lat, lote.lng])
      const color = polygonColor(lote.ciclo_actual)
      L.circleMarker([lote.lat, lote.lng], {
        radius: 10, fillColor: color, fillOpacity: 0.7, color: '#fff', weight: 2,
      }).addTo(map)
        .bindPopup(`<b>${lote.nombre}</b><br>${lote.ciclo_actual?.cultivo?.nombre || '—'}`)
    }
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map)

  if (bounds.isValid()) {
    map.fitBounds(bounds.pad(0.15))
  } else {
    map.setView([11.9848, -86.3088], 13)
  }
  requestAnimationFrame(() => map.invalidateSize())
}

onMounted(() => setTimeout(initMap, 250))
onUnmounted(() => { if (map) map.remove() })
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Lotes" />
    <div class="flex-grow flex flex-col md:flex-row overflow-hidden gap-6" style="height: calc(100vh - 7rem);">
      <div class="flex-grow md:w-3/5 bg-[#f6f4ec] rounded-xl shadow-sm border border-[#bfc9c1] overflow-hidden relative min-h-[300px]">
        <div ref="mapContainer" id="map" class="h-full w-full rounded-lg z-[1]"></div>
        <div class="absolute bottom-4 left-4 z-[1000] bg-[#fbf9f1]/90 backdrop-blur-md p-2 rounded-lg shadow-sm border border-[#bfc9c1]">
          <h4 class="text-sm font-bold mb-1 text-[#1b1c17]">Leyenda</h4>
          <div class="space-y-1">
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#0f5238]"></span><span class="text-xs">Activo</span></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#fda055]"></span><span class="text-xs">Cosechado</span></div>
            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#bfc9c1]"></span><span class="text-xs">Inactivo</span></div>
          </div>
        </div>
      </div>

      <div class="md:w-2/5 flex flex-col gap-3 h-full overflow-hidden">
        <div class="flex items-center justify-between flex-shrink-0">
          <h3 class="text-xl text-[#0f5238]">Lotes <span class="text-sm text-[#404943] font-normal">({{ pagination.total }})</span></h3>
          <Link :href="route('lotes.create')" class="bg-[#0f5238] text-white px-4 py-1.5 rounded-lg text-sm flex items-center gap-1 hover:shadow-md transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">add_circle</span> Nuevo
          </Link>
        </div>

        <div class="flex-grow overflow-y-auto space-y-3 pb-4" style="scrollbar-width: thin; scrollbar-color: #bfc9c1 transparent;">
          <div v-for="lote in lotes" :key="lote.id"
            class="bg-[#fbf9f1] rounded-lg p-3 shadow-sm border-l-4 cursor-pointer group transition-all hover:shadow-md"
            :class="borderColor(lote.ciclo_actual)"
          >
            <div class="flex justify-between items-start mb-1.5">
              <div class="min-w-0">
                <h4 class="text-base font-semibold text-[#1b1c17] truncate group-hover:text-[#0f5238] transition-colors">{{ lote.nombre }}</h4>
                <p class="text-xs text-[#404943] truncate">{{ lote.codigo }} · {{ lote.tipo_suelo || 'Suelo sin especificar' }}</p>
              </div>
              <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium flex items-center gap-1" :class="badgeInfo(lote.ciclo_actual).cls">
                <span class="material-symbols-outlined text-[14px] font-variation-settings: 'FILL' 1">{{ badgeInfo(lote.ciclo_actual).icon }}</span>
                {{ badgeInfo(lote.ciclo_actual).label }}
              </span>
            </div>

            <div v-if="lote.ciclo_actual" class="grid grid-cols-2 gap-2 py-1.5 border-y border-[#eae8e0] text-xs">
              <div><span class="text-[#404943]">Cultivo</span><p class="text-[#1b1c17] font-medium truncate">{{ lote.ciclo_actual.cultivo?.nombre || '—' }}</p></div>
              <div><span class="text-[#404943]">Siembra</span><p class="text-[#1b1c17] font-medium">{{ formatDate(lote.ciclo_actual.fecha_siembra) }}</p></div>
            </div>

            <div class="mt-1.5 flex justify-between items-center">
              <span class="text-xs text-[#404943]">{{ lote.area_manzanas }} Mz</span>
              <Link :href="route('lotes.edit', lote.id)" class="text-[#0f5238] text-xs flex items-center gap-0.5 hover:underline font-medium">
                Editar <span class="material-symbols-outlined text-[16px]">chevron_right</span>
              </Link>
            </div>
          </div>
        </div>

        <div v-if="pagination.last_page > 1" class="flex items-center justify-between pt-2 border-t border-[#eae8e0] flex-shrink-0">
          <button :disabled="pagination.current_page <= 1" @click="goToPage(pagination.current_page - 1)"
            class="text-xs px-3 py-1 rounded border border-[#bfc9c1] disabled:opacity-30 hover:bg-[#eae8e0] transition-colors">Anterior</button>
          <span class="text-xs text-[#404943]">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button :disabled="pagination.current_page >= pagination.last_page" @click="goToPage(pagination.current_page + 1)"
            class="text-xs px-3 py-1 rounded border border-[#bfc9c1] disabled:opacity-30 hover:bg-[#eae8e0] transition-colors">Siguiente</button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
#map { height: 100%; width: 100%; border-radius: 0.5rem; z-index: 1; }
</style>
