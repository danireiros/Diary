<template>
  <div class="p-3 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-sm">
    <div class="flex items-center justify-between"><h3 class="font-semibold">{{ title }}</h3></div>
    <div class="mt-2 flex flex-wrap gap-2">
      <div v-for="c in categories" :key="c.id" class="flex items-center gap-2 rounded-xl border dark:border-gray-700 px-3 py-2">
        <span class="inline-block w-3 h-3 rounded-full" :style="{ backgroundColor: c.color }" />
        <span class="font-medium">{{ c.name }}</span>
        <input class="ml-2 w-20 h-8 p-1 border dark:border-gray-700 rounded-lg" type="color" v-model="colors[c.id]" @change="onColor(c)" />
        <button class="ml-1 text-xs px-2 py-1 rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="$emit('remove', c.id)">Eliminar</button>
      </div>
    </div>
    <div class="mt-3 flex items-center gap-2">
      <input class="border dark:border-gray-700 rounded-lg px-3 py-2 w-48 bg-white dark:bg-gray-900" placeholder="Nombre" v-model="name" />
      <input class="w-16 h-10 p-1 border dark:border-gray-700 rounded-lg" type="color" v-model="color" />
      <button class="px-3 py-2 rounded-xl border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="add">Añadir</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watchEffect } from 'vue'
const props = defineProps({ title: { type: String, default: 'Categorías' }, categories: Array })
const emit = defineEmits(['create','update','remove'])
const name = ref('')
const color = ref('#3b82f6')
const colors = reactive({})
watchEffect(()=>{ (props.categories||[]).forEach(c=>{ colors[c.id] = c.color }) })
function add(){ if(!name.value.trim()) return; emit('create', { name: name.value.trim(), color: color.value }); name.value=''; }
function onColor(c){ emit('update', c.id, { color: colors[c.id] }) }
</script>


