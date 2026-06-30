<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  options: Array,
  placeholder: { type: String, default: 'Seleccionar...' },
  displayFn: { type: Function, default: (o) => o?.nombre || o?.label || o },
  disabled: Boolean,
})

const emit = defineEmits(['update:modelValue'])
const open = ref(false)
const query = ref('')
const inputRef = ref(null)
const listRef = ref(null)

const filtered = computed(() => {
  if (!query.value) return props.options
  const q = query.value.toLowerCase()
  return props.options.filter(o => (props.displayFn(o) || '').toLowerCase().includes(q))
})

const selectedLabel = computed(() => {
  if (!props.modelValue) return ''
  const match = props.options.find(o => o.id == props.modelValue || o.value == props.modelValue)
  return match ? props.displayFn(match) : ''
})

const select = (option) => {
  const val = option.id ?? option.value ?? option
  emit('update:modelValue', val)
  open.value = false
  query.value = ''
}

const toggle = () => {
  if (props.disabled) return
  open.value = !open.value
  if (open.value) {
    query.value = ''
    setTimeout(() => inputRef.value?.focus(), 50)
  }
}

const close = (e) => {
  if (!e.target.closest('.searchable-select')) open.value = false
}

watch(open, (val) => {
  if (val) document.addEventListener('click', close)
  else document.removeEventListener('click', close)
})
</script>

<template>
  <div class="searchable-select relative">
    <div
      @click="toggle"
      class="w-full h-11 px-3 border border-[#bfc9c1] rounded-lg text-sm bg-white flex items-center justify-between cursor-pointer transition-all"
      :class="{ 'ring-2 ring-[#b1f0ce] border-[#0f5238]': open, 'opacity-50': disabled }"
    >
      <span v-if="!selectedLabel" class="text-[#707973]">{{ placeholder }}</span>
      <span v-else class="text-[#1b1c17] truncate">{{ selectedLabel }}</span>
      <span class="material-symbols-outlined text-[#404943] text-[20px]" :class="{ 'rotate-180': open }">expand_more</span>
    </div>

    <Transition name="fade">
      <div v-if="open" ref="listRef" class="absolute z-50 top-full mt-1 left-0 right-0 bg-white border border-[#bfc9c1] rounded-lg shadow-lg max-h-64 overflow-hidden flex flex-col">
        <div class="p-2 border-b border-[#eae8e0]">
          <input ref="inputRef" v-model="query" type="text" placeholder="Buscar..."
            class="w-full h-9 px-3 border border-[#bfc9c1] rounded-lg text-sm outline-none focus:border-[#0f5238] focus:ring-1 focus:ring-[#b1f0ce] transition-all"
            @click.stop
          />
        </div>
        <div class="overflow-y-auto flex-1 custom-scroll">
          <button
            v-for="opt in filtered" :key="opt.id ?? opt.value ?? opt"
            @click="select(opt)"
            type="button"
            class="w-full text-left px-3 py-2.5 text-sm hover:bg-[#f6f4ec] transition-colors border-b border-[#eae8e0]/50 last:border-0"
            :class="{ 'bg-[#2d6a4f]/10 text-[#0f5238] font-medium': (opt.id ?? opt.value ?? opt) == modelValue }"
          >{{ displayFn(opt) }}</button>
          <div v-if="!filtered.length" class="p-3 text-sm text-[#707973] text-center">Sin resultados</div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease, transform 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-4px); }
.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #bfc9c1; border-radius: 10px; }
</style>
