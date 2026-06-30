<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import Swal from 'sweetalert2'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import { es } from 'flatpickr/dist/l10n/es'
import SearchableSelect from '@/Components/SearchableSelect.vue'

const props = defineProps({
  lotes: Array,
  cultivos: Array,
})

const form = useForm({
  lote_id: '',
  cultivo_id: '',
  fecha_siembra: '',
  notas: '',
})

const dateInput = ref(null)
const fpInstance = ref(null)

const cultivoSeleccionado = computed(() => {
  return props.cultivos.find(c => c.id == form.cultivo_id)
})

const fechaCosechaEstimada = computed(() => {
  if (!form.fecha_siembra || !cultivoSeleccionado.value) return null
  const d = new Date(form.fecha_siembra)
  d.setDate(d.getDate() + cultivoSeleccionado.value.ciclo_dias)
  return d.toLocaleDateString('es-NI', { day: 'numeric', month: 'long', year: 'numeric' })
})

const fieldLabels = {
  lote_id: 'Lote',
  cultivo_id: 'Cultivo',
  fecha_siembra: 'Fecha de siembra',
}

const displayLote = (l) => l ? `${l.codigo} — ${l.nombre}` : ''
const displayCultivo = (c) => c ? `${c.nombre} (${c.ciclo_dias} días)` : ''

const submit = () => {
  const missing = []
  if (!form.lote_id) missing.push(fieldLabels.lote_id)
  if (!form.cultivo_id) missing.push(fieldLabels.cultivo_id)
  if (!form.fecha_siembra) missing.push(fieldLabels.fecha_siembra)

  if (missing.length) {
    Swal.fire({ icon: 'warning', title: 'Campos pendientes', html: `Complete:<br><b>${missing.join(', ')}</b>`, confirmButtonColor: '#0f5238', confirmButtonText: 'Entendido' })
    return
  }

  form.post(route('ciclos.store'), {
    onError: (e) => {
      const list = Object.entries(e).map(([k, v]) => `<b>${fieldLabels[k] || k}:</b> ${v}`).join('<br>')
      Swal.fire({ icon: 'error', title: 'Error', html: list, confirmButtonColor: '#0f5238', confirmButtonText: 'Corregir' })
    },
  })
}

onMounted(() => {
  if (dateInput.value) {
    fpInstance.value = flatpickr(dateInput.value, {
      locale: es,
      dateFormat: 'Y-m-d',
      altFormat: 'j \\de F \\de Y',
      altInput: true,
      altInputClass: 'w-full h-11 px-3 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all bg-surface-container-lowest cursor-pointer',
      onChange: (selectedDates) => {
        form.fecha_siembra = selectedDates[0] ? flatpickr.formatDate(selectedDates[0], 'Y-m-d') : ''
      },
    })
  }
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Nuevo Ciclo" />
    <div class="max-w-2xl">
      <div class="flex items-center gap-4 mb-6">
        <Link :href="route('ciclos.index')" class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-1">arrow_back</Link>
        <div>
          <h2 class="text-2xl text-primary font-bold">Nuevo Ciclo de Siembra</h2>
          <p class="text-sm text-on-surface-variant">Registre un nuevo ciclo en el lote seleccionado.</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 p-6 space-y-5">
          <h3 class="text-lg font-semibold text-on-surface border-b border-surface-container-high pb-3">Información del Ciclo</h3>

          <div class="space-y-1.5">
            <label class="text-sm font-medium text-on-surface">Lote <span class="text-error">*</span></label>
            <SearchableSelect
              :options="lotes"
              :displayFn="displayLote"
              placeholder="Buscar lote..."
              :model-value="form.lote_id"
              @update:model-value="form.lote_id = $event"
            />
          </div>

          <div class="space-y-1.5">
            <label class="text-sm font-medium text-on-surface">Cultivo <span class="text-error">*</span></label>
            <SearchableSelect
              :options="cultivos"
              :displayFn="displayCultivo"
              placeholder="Buscar cultivo..."
              :model-value="form.cultivo_id"
              @update:model-value="form.cultivo_id = $event"
            />
            <p v-if="cultivoSeleccionado" class="text-xs text-outline">Ciclo: {{ cultivoSeleccionado.ciclo_dias }} días desde siembra a cosecha.</p>
          </div>

          <div class="space-y-1.5">
            <label class="text-sm font-medium text-on-surface">Fecha de Siembra <span class="text-error">*</span></label>
            <div class="relative">
              <input ref="dateInput" type="text" placeholder="Seleccionar fecha..." readonly />
              <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">calendar_today</span>
            </div>
            <p v-if="fechaCosechaEstimada" class="text-xs text-primary font-medium">
              Cosecha estimada: {{ fechaCosechaEstimada }}
            </p>
          </div>

          <div class="space-y-1.5">
            <label class="text-sm font-medium text-on-surface">Notas</label>
            <textarea v-model="form.notas" rows="3" placeholder="Observaciones sobre el ciclo..."
              class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary-fixed outline-none transition-all resize-none"></textarea>
          </div>
        </div>

        <div class="flex items-center gap-4 pb-8">
          <button type="submit" :disabled="form.processing"
            class="h-12 px-8 bg-primary text-white font-semibold rounded-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center gap-2 disabled:opacity-50">
            <span class="material-symbols-outlined">add_circle</span>
            {{ form.processing ? 'Creando...' : 'Crear Ciclo' }}
          </button>
          <Link :href="route('ciclos.index')" class="h-12 px-6 border border-outline-variant text-on-surface-variant rounded-lg hover:bg-surface-container-low transition-all flex items-center">Cancelar</Link>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
