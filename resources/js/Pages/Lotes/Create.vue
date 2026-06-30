<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Swal from 'sweetalert2'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const form = useForm({
  codigo: '',
  nombre: '',
  area_manzanas: '',
  tipo_suelo: '',
  poligono: '',
  descripcion: '',
  activo: true,
})

const mapContainer = ref(null)
const points = ref([])
const isFinished = ref(false)
const polygonLayer = ref(null)
const vertexMarkers = ref([])

let map = null

const tipoSueloOptions = [
  { value: '', label: 'Seleccionar...' },
  { value: 'Franco arenoso', label: 'Franco arenoso' },
  { value: 'Arcilloso', label: 'Arcilloso' },
  { value: 'Limoso', label: 'Limoso' },
  { value: 'Franco', label: 'Franco' },
  { value: 'Arenoso', label: 'Arenoso' },
  { value: 'Pedregoso', label: 'Pedregoso' },
]

const fieldLabels = {
  codigo: 'Código',
  nombre: 'Nombre',
  area_manzanas: 'Área (Mz)',
}

const canFinish = computed(() => points.value.length >= 3 && !isFinished.value)

const clearDraw = () => {
  if (polygonLayer.value) { map.removeLayer(polygonLayer.value); polygonLayer.value = null }
  vertexMarkers.value.forEach(m => map.removeLayer(m))
  vertexMarkers.value = []
  points.value = []
  isFinished.value = false
  form.poligono = ''
}

const redraw = () => {
  if (polygonLayer.value) map.removeLayer(polygonLayer.value)
  vertexMarkers.value.forEach(m => map.removeLayer(m))
  vertexMarkers.value = []

  if (points.value.length === 0) return

  const pts = points.value.map(p => [p.lat, p.lng])

  if (isFinished.value || points.value.length >= 3) {
    polygonLayer.value = L.polygon(pts, {
      color: '#0f5238', fillColor: '#0f5238', fillOpacity: 0.15, weight: 2,
    }).addTo(map)
  } else if (points.value.length >= 2) {
    polygonLayer.value = L.polyline(pts, {
      color: '#0f5238', weight: 2, dashArray: '6,4',
    }).addTo(map)
  }

  points.value.forEach((p, i) => {
    const m = L.circleMarker([p.lat, p.lng], {
      radius: 5, fillColor: '#0f5238', fillOpacity: 1, color: '#fff', weight: 2,
    }).addTo(map)
      .bindTooltip((i + 1).toString(), { permanent: true, direction: 'top', offset: [0, -8], className: 'vertex-label' })
    vertexMarkers.value.push(m)
  })
}

const handleMapClick = (e) => {
  if (isFinished.value) return
  points.value.push({ lat: e.latlng.lat, lng: e.latlng.lng })
  redraw()
}

const finishPolygon = () => {
  if (!canFinish.value) return
  isFinished.value = true
  form.poligono = JSON.stringify(points.value.map(p => [p.lat, p.lng]))
  redraw()
}

const undoLast = () => {
  if (isFinished.value) {
    isFinished.value = false
    form.poligono = ''
  }
  points.value.pop()
  redraw()
}

const initMap = () => {
  if (!mapContainer.value) return
  map = L.map(mapContainer.value).setView([11.9848, -86.3088], 14)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map)
  map.on('click', handleMapClick)
  map.on('dblclick', (e) => { L.DomEvent.stop(e); if (canFinish.value) finishPolygon() })
  requestAnimationFrame(() => map.invalidateSize())
}

const submit = () => {
  const missing = []
  if (!form.codigo.trim()) missing.push(fieldLabels.codigo)
  if (!form.nombre.trim()) missing.push(fieldLabels.nombre)
  if (!form.area_manzanas || parseFloat(form.area_manzanas) <= 0) missing.push(fieldLabels.area_manzanas)
  if (!form.poligono) missing.push('Polígono (dibuje el lote en el mapa)')

  if (missing.length) {
    Swal.fire({
      icon: 'warning', title: 'Campos pendientes',
      html: `Complete los siguientes campos:<br><b>${missing.join(', ')}</b>`,
      confirmButtonColor: '#0f5238', confirmButtonText: 'Entendido',
    })
    return
  }

  form.post(route('lotes.store'), {
    onError: (errors) => {
      const list = Object.entries(errors).map(([k, v]) => `<b>${fieldLabels[k] || k}:</b> ${v}`).join('<br>')
      Swal.fire({ icon: 'error', title: 'Error de validación', html: list, confirmButtonColor: '#0f5238', confirmButtonText: 'Corregir' })
    },
  })
}

onMounted(() => setTimeout(initMap, 200))
onUnmounted(() => { if (map) map.remove() })
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Nuevo Lote" />
    <div class="px-0 md:px-0">
      <div class="flex items-center gap-4 mb-6">
        <Link :href="route('lotes.index')" class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-1">arrow_back</Link>
        <div>
          <h2 class="text-2xl text-primary font-bold">Nuevo Lote</h2>
          <p class="text-sm text-on-surface-variant">Dibuje el perímetro del lote en el mapa.</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
          <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-5">
            <h3 class="text-lg font-semibold text-on-surface border-b border-surface-container-high pb-3">Información General</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div class="space-y-1.5">
                <label class="text-sm font-medium text-on-surface">Código <span class="text-error">*</span></label>
                <input v-model="form.codigo" type="text" placeholder="Ej: L-004" maxlength="20"
                  class="w-full h-11 px-3 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all" />
              </div>
              <div class="space-y-1.5">
                <label class="text-sm font-medium text-on-surface">Nombre <span class="text-error">*</span></label>
                <input v-model="form.nombre" type="text" placeholder="Ej: Lote El Rosario" maxlength="255"
                  class="w-full h-11 px-3 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div class="space-y-1.5">
                <label class="text-sm font-medium text-on-surface">Área (Mz) <span class="text-error">*</span></label>
                <input v-model="form.area_manzanas" type="number" step="0.01" placeholder="0.00"
                  class="w-full h-11 px-3 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all" />
              </div>
              <div class="space-y-1.5">
                <label class="text-sm font-medium text-on-surface">Tipo de Suelo</label>
                <select v-model="form.tipo_suelo"
                  class="w-full h-11 px-3 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all bg-surface-container-lowest">
                  <option v-for="opt in tipoSueloOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-sm font-medium text-on-surface">Descripción</label>
              <textarea v-model="form.descripcion" rows="4" placeholder="Ubicación, características del terreno, accesos..."
                class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all resize-none"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
              <input v-model="form.activo" type="checkbox" id="activo" class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary-fixed" />
              <label for="activo" class="text-sm text-on-surface cursor-pointer select-none">Lote activo</label>
            </div>
          </div>

          <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4 flex flex-col">
            <h3 class="text-lg font-semibold text-on-surface border-b border-surface-container-high pb-3">Perímetro del Lote</h3>

            <div class="flex gap-2 flex-wrap">
              <button @click="finishPolygon" type="button" :disabled="!canFinish"
                class="px-3 h-9 bg-primary text-white rounded-lg text-xs font-medium disabled:opacity-30 hover:brightness-110 transition-all">Cerrar Polígono</button>
              <button @click="undoLast" type="button" :disabled="points.length === 0"
                class="px-3 h-9 border border-outline-variant text-on-surface-variant rounded-lg text-xs font-medium disabled:opacity-30 hover:bg-surface-container-low transition-all">Deshacer</button>
              <button @click="clearDraw" type="button" :disabled="points.length === 0"
                class="px-3 h-9 border border-outline-variant text-error rounded-lg text-xs font-medium disabled:opacity-30 hover:bg-error-container transition-all">Limpiar</button>
            </div>

            <div ref="mapContainer" id="create-map" class="w-full flex-1 min-h-[300px] rounded-lg border border-outline-variant z-10 relative"></div>

            <div class="flex items-center justify-between text-xs">
              <span class="text-outline">
                <span v-if="!isFinished && points.length === 0">Haga clic en el mapa para marcar los vértices del lote.</span>
                <span v-else-if="!isFinished">{{ points.length }} punto(s) — haga clic para agregar, doble clic para cerrar</span>
                <span v-else-if="isFinished">✅ Polígono cerrado ({{ points.length }} vértices)</span>
              </span>
              <span v-if="points.length" class="font-medium text-primary">{{ points.length }} vértices</span>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-4 pb-8">
          <div class="flex-1"></div>
          <button type="submit" :disabled="form.processing"
            class="h-12 px-8 bg-primary text-white font-semibold rounded-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center gap-2 disabled:opacity-50">
            <span class="material-symbols-outlined">save</span>
            {{ form.processing ? 'Guardando...' : 'Guardar Lote' }}
          </button>
          <Link :href="route('lotes.index')"
            class="h-12 px-6 border border-outline-variant text-on-surface-variant rounded-lg hover:bg-surface-container-low transition-all flex items-center">
            Cancelar
          </Link>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<style>
#create-map { z-index: 1; }
.vertex-label { background: none !important; border: none !important; box-shadow: none !important; font-weight: 700; font-size: 11px; color: #0f5238; font-family: Inter, sans-serif; }
.vertex-label::before { display: none; }
</style>
