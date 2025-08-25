<template>
  <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
    <h3 class="font-semibold mb-2">Filtro de categorías</h3>
    <div class="space-y-2">
      <label v-for="c in categories" :key="c.id" class="flex items-center gap-2 cursor-pointer">
        <input type="checkbox" class="w-4 h-4" :checked="model[c.id] !== false" @change="toggle(c.id)" />
        <span class="inline-block w-3 h-3 rounded-full" :style="{ backgroundColor: c.color }" />
        <span>{{ c.name }}</span>
      </label>
    </div>
    <div class="mt-3 flex items-center gap-2">
      <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="setAll(true)">Mostrar todas</button>
      <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="setAll(false)">Ocultar todas</button>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ categories: Array, selected: Object })
const emit = defineEmits(['update:selected'])
const model = defineModel('selected', { type: Object, default: ()=>({}) })
function toggle(id){ const on = model.value[id] !== false; model.value = { ...model.value, [id]: !on } }
function setAll(v){ const m={}; (props.categories||[]).forEach(c=>m[c.id]=v); model.value = m }
</script>


