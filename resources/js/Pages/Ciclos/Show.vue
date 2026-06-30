<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import Swal from 'sweetalert2'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import { es } from 'flatpickr/dist/l10n/es'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
  ciclo: Object,
  insumos: Array,
})

const tab = ref('general')

const tabs = [
  { key: 'general', label: 'General', icon: 'info' },
  { key: 'insumos', label: 'Insumos', icon: 'science' },
  { key: 'mano_obra', label: 'Mano de Obra', icon: 'groups' },
  { key: 'otros_costos', label: 'Otros Costos', icon: 'receipt' },
]

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-NI', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'
const formatCurrency = (n) => n ? 'C$' + Number(n).toLocaleString('es-NI', { minimumFractionDigits: 2 }) : 'C$0.00'

const badgeEstado = computed(() => {
  const e = props.ciclo.estado
  if (e === 'activo') return { label: 'Activo', cls: 'bg-primary-container text-on-primary-container' }
  if (e === 'cosechado') return { label: 'Cosechado', cls: 'bg-surface-container-highest text-on-surface-variant' }
  return { label: 'Planificado', cls: 'bg-surface-container-highest text-on-surface-variant' }
})

// --- Insumo form ---
const showInsumoForm = ref(false)
const insumoForm = ref({ insumo_id: '', insumo_nombre: '', insumo_tipo: 'fertilizante', insumo_unidad: 'kg', cantidad: '', costo_unitario: '', fecha: '', notas: '' })
const crearInsumoNuevo = ref(false)

const fpInsumo = ref(null)
const fpMo = ref(null)
const fpOc = ref(null)
let fpInstanceInsumo = null
let fpInstanceMo = null
let fpInstanceOc = null

const reloadPage = () => {
  router.visit(route('ciclos.show', props.ciclo.id), { preserveScroll: true })
}

const displayInsumo = (i) => i ? `${i.nombre} (${i.tipo} — ${i.unidad})` : ''

const submitInsumo = () => {
  const data = crearInsumoNuevo.value
    ? { ...insumoForm.value, insumo_id: '' }
    : { insumo_id: insumoForm.value.insumo_id, insumo_nombre: '', insumo_tipo: '', insumo_unidad: '', ...insumoForm.value }

  if (!data.cantidad || !data.costo_unitario) {
    Swal.fire({ icon: 'warning', title: 'Campos pendientes', text: 'Complete cantidad y costo unitario.', confirmButtonColor: '#0f5238' })
    return
  }
  if (crearInsumoNuevo.value && !data.insumo_nombre) {
    Swal.fire({ icon: 'warning', title: 'Nombre requerido', text: 'Ingrese el nombre del nuevo insumo.', confirmButtonColor: '#0f5238' })
    return
  }

  router.post(route('ciclos.insumos.store', props.ciclo.id), data, {
    onSuccess: () => reloadPage(),
    onError: (e) => {
      const list = Object.entries(e).map(([k, v]) => `<b>${k}:</b> ${v}`).join('<br>')
      Swal.fire({ icon: 'error', title: 'Error', html: list, confirmButtonColor: '#0f5238' })
    },
  })
}

// --- Mano de obra form ---
const showMoForm = ref(false)
const moForm = ref({ concepto: '', personas: 1, horas: '', costo_hora: '', fecha: '', notas: '' })

const submitMo = () => {
  if (!moForm.value.concepto || !moForm.value.horas || !moForm.value.costo_hora) {
    Swal.fire({ icon: 'warning', title: 'Campos pendientes', confirmButtonColor: '#0f5238' })
    return
  }
  router.post(route('ciclos.mano-obra.store', props.ciclo.id), moForm.value, {
    onSuccess: () => reloadPage(),
    onError: (e) => Swal.fire({ icon: 'error', title: 'Error', html: Object.entries(e).map(([k, v]) => `<b>${k}:</b> ${v}`).join('<br>'), confirmButtonColor: '#0f5238' }),
  })
}

// --- Otros costos form ---
const showOcForm = ref(false)
const ocForm = ref({ concepto: '', monto: '', fecha: '', notas: '' })

const submitOc = () => {
  if (!ocForm.value.concepto || !ocForm.value.monto) {
    Swal.fire({ icon: 'warning', title: 'Campos pendientes', confirmButtonColor: '#0f5238' })
    return
  }
  router.post(route('ciclos.otros-costos.store', props.ciclo.id), ocForm.value, {
    onSuccess: () => reloadPage(),
    onError: (e) => Swal.fire({ icon: 'error', title: 'Error', html: Object.entries(e).map(([k, v]) => `<b>${k}:</b> ${v}`).join('<br>'), confirmButtonColor: '#0f5238' }),
  })
}

const initFp = (refName, formField) => {
  const el = refName === 'fpInsumo' ? fpInsumo.value : refName === 'fpMo' ? fpMo.value : fpOc.value
  if (!el) return
  fpInstanceInsumo?.destroy()
  fpInstanceMo?.destroy()
  fpInstanceOc?.destroy()
  nextTick(() => {
    const inst = flatpickr(el, {
      locale: es,
      dateFormat: 'Y-m-d',
      onChange: (selectedDates) => {
        formField.value = selectedDates[0] ? flatpickr.formatDate(selectedDates[0], 'Y-m-d') : ''
      },
    })
    if (refName === 'fpInsumo') fpInstanceInsumo = inst
    else if (refName === 'fpMo') fpInstanceMo = inst
    else fpInstanceOc = inst
  })
}

watch(showInsumoForm, (val) => { if (val) setTimeout(() => initFp('fpInsumo', insumoForm), 150) })
watch(showMoForm, (val) => { if (val) setTimeout(() => initFp('fpMo', moForm), 150) })
watch(showOcForm, (val) => { if (val) setTimeout(() => initFp('fpOc', ocForm), 150) })

onUnmounted(() => { fpInstanceInsumo?.destroy(); fpInstanceMo?.destroy(); fpInstanceOc?.destroy() })
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="'Ciclo - ' + (ciclo.lote?.nombre || '')" />

    <div class="w-full space-y-6">
      <div class="flex items-center gap-4">
        <Link :href="route('ciclos.index')" class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-1">arrow_back</Link>
        <div class="flex-1">
          <div class="flex items-center gap-3">
            <h2 class="text-2xl text-primary font-bold">{{ ciclo.lote?.codigo }} — {{ ciclo.lote?.nombre }}</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" :class="badgeEstado.cls">{{ badgeEstado.label }}</span>
          </div>
          <p class="text-sm text-on-surface-variant">{{ ciclo.cultivo?.nombre }} · Inicio: {{ formatDate(ciclo.fecha_siembra) }} · Cosecha est.: {{ formatDate(ciclo.fecha_cosecha_estimada) }}</p>
        </div>
      </div>

      <div class="border-b border-outline-variant">
        <nav class="flex gap-6">
          <button v-for="t in tabs" :key="t.key" @click="tab = t.key"
            class="pb-3 text-sm font-medium border-b-2 transition-colors flex items-center gap-1.5"
            :class="tab === t.key ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface'"
          >
            <span class="material-symbols-outlined text-[18px]">{{ t.icon }}</span>
            {{ t.label }}
          </button>
        </nav>
      </div>

      <!-- TAB: General -->
      <div v-if="tab === 'general'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
          <h3 class="text-lg font-semibold text-on-surface">Detalles del Ciclo</h3>
          <div class="grid grid-cols-2 gap-4">
            <div><span class="text-xs text-on-surface-variant">Lote</span><p class="text-sm font-medium text-on-surface">{{ ciclo.lote?.nombre }} ({{ ciclo.lote?.codigo }})</p></div>
            <div><span class="text-xs text-on-surface-variant">Cultivo</span><p class="text-sm font-medium text-on-surface">{{ ciclo.cultivo?.nombre }}</p></div>
            <div><span class="text-xs text-on-surface-variant">Siembra</span><p class="text-sm font-medium text-on-surface">{{ formatDate(ciclo.fecha_siembra) }}</p></div>
            <div><span class="text-xs text-on-surface-variant">Cosecha estimada</span><p class="text-sm font-medium text-on-surface">{{ formatDate(ciclo.fecha_cosecha_estimada) }}</p></div>
            <div><span class="text-xs text-on-surface-variant">Cosecha real</span><p class="text-sm font-medium text-on-surface">{{ ciclo.fecha_cosecha_real ? formatDate(ciclo.fecha_cosecha_real) : '—' }}</p></div>
            <div><span class="text-xs text-on-surface-variant">Estado</span><p class="text-sm font-medium text-on-surface">{{ ciclo.estado }}</p></div>
          </div>
          <div v-if="ciclo.notas">
            <span class="text-xs text-on-surface-variant">Notas</span>
            <p class="text-sm text-on-surface mt-1">{{ ciclo.notas }}</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-4">
          <h3 class="text-lg font-semibold text-on-surface">Costos Acumulados</h3>
          <p class="text-3xl font-bold text-primary">{{ formatCurrency(ciclo.total_costos) }}</p>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-on-surface-variant">Insumos</span><span>{{ formatCurrency(ciclo.insumos.reduce((a, i) => a + i.costo_total, 0)) }}</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Mano de obra</span><span>{{ formatCurrency(ciclo.mano_obra.reduce((a, m) => a + m.costo_total, 0)) }}</span></div>
            <div class="flex justify-between"><span class="text-on-surface-variant">Otros costos</span><span>{{ formatCurrency(ciclo.otros_costos.reduce((a, o) => a + o.monto, 0)) }}</span></div>
          </div>
        </div>
      </div>

      <!-- TAB: Insumos -->
      <div v-if="tab === 'insumos'" class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-on-surface">{{ ciclo.insumos.length }} insumos registrados</h3>
          <button @click="showInsumoForm = !showInsumoForm" class="bg-primary text-white px-4 py-1.5 rounded-lg text-sm flex items-center gap-1 hover:brightness-110 transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span> Agregar Insumo
          </button>
        </div>

        <div v-show="showInsumoForm" class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-5 space-y-4">
          <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" :value="false" v-model="crearInsumoNuevo" class="text-primary" /> Existente
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" :value="true" v-model="crearInsumoNuevo" class="text-primary" /> Nuevo
            </label>
          </div>

          <SearchableSelect v-if="!crearInsumoNuevo" :options="insumos" :displayFn="displayInsumo" placeholder="Buscar insumo existente..."
            :model-value="insumoForm.insumo_id" @update:model-value="insumoForm.insumo_id = $event" />

          <div v-if="crearInsumoNuevo" class="grid grid-cols-3 gap-3">
            <input v-model="insumoForm.insumo_nombre" type="text" placeholder="Nombre del insumo" class="h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" />
            <select v-model="insumoForm.insumo_tipo" class="h-10 px-3 border border-outline-variant rounded-lg text-sm bg-surface-container-lowest outline-none focus:border-primary">
              <option value="fertilizante">Fertilizante</option><option value="semilla">Semilla</option>
              <option value="pesticida">Pesticida</option><option value="herbicida">Herbicida</option><option value="otro">Otro</option>
            </select>
            <select v-model="insumoForm.insumo_unidad" class="h-10 px-3 border border-outline-variant rounded-lg text-sm bg-surface-container-lowest outline-none focus:border-primary">
              <option value="kg">kg</option><option value="libra">libra</option><option value="litro">litro</option><option value="unidad">unidad</option>
            </select>
          </div>

          <div class="grid grid-cols-3 gap-3">
            <div><label class="text-xs text-on-surface-variant">Cantidad</label>
              <input v-model="insumoForm.cantidad" type="number" step="0.01" placeholder="0.00" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" /></div>
            <div><label class="text-xs text-on-surface-variant">Costo unitario (C$)</label>
              <input v-model="insumoForm.costo_unitario" type="number" step="0.01" placeholder="0.00" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" /></div>
            <div><label class="text-xs text-on-surface-variant">Fecha</label>
              <div class="relative">
                <input ref="fpInsumo" type="text" placeholder="Seleccionar fecha..." readonly class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary bg-surface-container-lowest cursor-pointer flatpickr-input" />
                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[18px] pointer-events-none">calendar_today</span>
              </div></div>
          </div>
          <textarea v-model="insumoForm.notas" rows="2" placeholder="Notas (opcional)" class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary resize-none"></textarea>
          <button @click="submitInsumo" class="bg-primary text-white px-6 py-2 rounded-lg text-sm hover:brightness-110 transition-all">Guardar Insumo</button>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-surface-container-low"><tr>
              <th class="p-3 text-xs text-on-surface-variant">Insumo</th><th class="p-3 text-xs text-on-surface-variant">Tipo</th>
              <th class="p-3 text-xs text-on-surface-variant text-right">Cantidad</th><th class="p-3 text-xs text-on-surface-variant text-right">Costo Unit.</th>
              <th class="p-3 text-xs text-on-surface-variant text-right">Total</th><th class="p-3 text-xs text-on-surface-variant">Fecha</th>
            </tr></thead>
            <tbody class="divide-y divide-[#eae8e0]">
              <tr v-for="i in ciclo.insumos" :key="i.id" class="hover:bg-surface-container-low">
                <td class="p-3 font-medium">{{ i.insumo_nombre }}</td><td class="p-3 text-on-surface-variant">{{ i.insumo_tipo }}</td>
                <td class="p-3 text-right">{{ i.cantidad }} {{ i.insumo_unidad }}</td><td class="p-3 text-right">{{ formatCurrency(i.costo_unitario) }}</td>
                <td class="p-3 text-right font-medium">{{ formatCurrency(i.costo_total) }}</td><td class="p-3 text-on-surface-variant">{{ i.fecha ? formatDate(i.fecha) : '—' }}</td>
              </tr>
              <tr v-if="!ciclo.insumos.length"><td colspan="6" class="p-6 text-center text-on-surface-variant">Sin insumos registrados.</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB: Mano de Obra -->
      <div v-if="tab === 'mano_obra'" class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-on-surface">{{ ciclo.mano_obra.length }} registros</h3>
          <button @click="showMoForm = !showMoForm" class="bg-primary text-white px-4 py-1.5 rounded-lg text-sm flex items-center gap-1 hover:brightness-110 transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span> Agregar
          </button>
        </div>
        <div v-show="showMoForm" class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-5 space-y-3">
          <input v-model="moForm.concepto" type="text" placeholder="Concepto (ej: Siembra, Limpia, Cosecha)" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" />
          <div class="grid grid-cols-4 gap-3">
            <div><label class="text-xs text-on-surface-variant">Personas</label><input v-model.number="moForm.personas" type="number" min="1" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" /></div>
            <div><label class="text-xs text-on-surface-variant">Horas</label><input v-model="moForm.horas" type="number" step="0.5" placeholder="0" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" /></div>
            <div><label class="text-xs text-on-surface-variant">Costo x hora (C$)</label><input v-model="moForm.costo_hora" type="number" step="0.01" placeholder="0.00" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" /></div>
            <div><label class="text-xs text-on-surface-variant">Fecha</label>
              <div class="relative">
                <input ref="fpMo" type="text" placeholder="Seleccionar fecha..." readonly class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary bg-surface-container-lowest cursor-pointer flatpickr-input" />
                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[18px] pointer-events-none">calendar_today</span>
              </div></div>
          </div>
          <div class="flex gap-3">
            <textarea v-model="moForm.notas" rows="2" placeholder="Notas" class="flex-1 px-3 py-2 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary resize-none"></textarea>
            <button @click="submitMo" class="bg-primary text-white px-6 rounded-lg text-sm hover:brightness-110 transition-all self-start">Guardar</button>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-surface-container-low"><tr>
              <th class="p-3 text-xs text-on-surface-variant">Concepto</th><th class="p-3 text-xs text-on-surface-variant text-right">Personas</th>
              <th class="p-3 text-xs text-on-surface-variant text-right">Horas</th><th class="p-3 text-xs text-on-surface-variant text-right">Costo/h</th>
              <th class="p-3 text-xs text-on-surface-variant text-right">Total</th><th class="p-3 text-xs text-on-surface-variant">Fecha</th>
            </tr></thead>
            <tbody class="divide-y divide-[#eae8e0]">
              <tr v-for="m in ciclo.mano_obra" :key="m.id" class="hover:bg-surface-container-low">
                <td class="p-3 font-medium">{{ m.concepto }}</td><td class="p-3 text-right">{{ m.personas }}</td>
                <td class="p-3 text-right">{{ m.horas }}</td><td class="p-3 text-right">{{ formatCurrency(m.costo_hora) }}</td>
                <td class="p-3 text-right font-medium">{{ formatCurrency(m.costo_total) }}</td><td class="p-3 text-on-surface-variant">{{ m.fecha ? formatDate(m.fecha) : '—' }}</td>
              </tr>
              <tr v-if="!ciclo.mano_obra.length"><td colspan="6" class="p-6 text-center text-on-surface-variant">Sin mano de obra registrada.</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB: Otros Costos -->
      <div v-if="tab === 'otros_costos'" class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-on-surface">{{ ciclo.otros_costos.length }} registros</h3>
          <button @click="showOcForm = !showOcForm" class="bg-primary text-white px-4 py-1.5 rounded-lg text-sm flex items-center gap-1 hover:brightness-110 transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span> Agregar
          </button>
        </div>
        <div v-show="showOcForm" class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-5 space-y-3">
          <input v-model="ocForm.concepto" type="text" placeholder="Concepto (ej: Alquiler tractor, Análisis de suelo)" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" />
          <div class="grid grid-cols-2 gap-3">
            <div><label class="text-xs text-on-surface-variant">Monto (C$)</label><input v-model="ocForm.monto" type="number" step="0.01" placeholder="0.00" class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary" /></div>
            <div><label class="text-xs text-on-surface-variant">Fecha</label>
              <div class="relative">
                <input ref="fpOc" type="text" placeholder="Seleccionar fecha..." readonly class="w-full h-10 px-3 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary bg-surface-container-lowest cursor-pointer flatpickr-input" />
                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[18px] pointer-events-none">calendar_today</span>
              </div></div>
          </div>
          <div class="flex gap-3">
            <textarea v-model="ocForm.notas" rows="2" placeholder="Notas" class="flex-1 px-3 py-2 border border-outline-variant rounded-lg text-sm outline-none focus:border-primary resize-none"></textarea>
            <button @click="submitOc" class="bg-primary text-white px-6 rounded-lg text-sm hover:brightness-110 transition-all self-start">Guardar</button></div>
        </div>
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 overflow-hidden">
          <table class="w-full text-left text-sm">
            <thead class="bg-surface-container-low"><tr>
              <th class="p-3 text-xs text-on-surface-variant">Concepto</th><th class="p-3 text-xs text-on-surface-variant text-right">Monto</th>
              <th class="p-3 text-xs text-on-surface-variant">Fecha</th><th class="p-3 text-xs text-on-surface-variant">Notas</th>
            </tr></thead>
            <tbody class="divide-y divide-[#eae8e0]">
              <tr v-for="o in ciclo.otros_costos" :key="o.id" class="hover:bg-surface-container-low">
                <td class="p-3 font-medium">{{ o.concepto }}</td><td class="p-3 text-right font-medium">{{ formatCurrency(o.monto) }}</td>
                <td class="p-3 text-on-surface-variant">{{ o.fecha ? formatDate(o.fecha) : '—' }}</td><td class="p-3 text-on-surface-variant">{{ o.notas || '—' }}</td>
              </tr>
              <tr v-if="!ciclo.otros_costos.length"><td colspan="4" class="p-6 text-center text-on-surface-variant">Sin otros costos registrados.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
