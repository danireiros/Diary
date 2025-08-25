<template>
  <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700  rounded-2xl shadow-sm">
    <div class="flex items-center justify-between"><h3 class="font-semibold">{{ title }}</h3></div>
    <div class="mt-2 flex flex-wrap gap-2">
      <div v-for="c in categories" :key="c.id" class="flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2">
        <span class="inline-block w-3 h-3 rounded-full" :style="{ backgroundColor: c.color }" />
        <span class="font-medium">{{ c.name }}</span>
        <input class="ml-2 w-20 h-8 p-1 border border-gray-200 dark:border-gray-700 rounded-lg" type="color" v-model="colors[c.id]" @change="onColor(c)" />
        <button class="px-3 py-2 rounded-xl bg-gray-50 dark:text-white dark:bg-gray-700 text-slate-700" @click="askRemove(c)">X</button>
      </div>
    </div>
    <div class="mt-3 flex items-center gap-2">
      <input class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 w-48 bg-white dark:bg-gray-900" placeholder="Nombre" v-model="name" />
      <input class="w-16 h-10 p-1 border border-gray-200 dark:border-gray-700 rounded-lg" type="color" v-model="color" />

    </div>
    <div class="mt-3 flex items-center gap-2">
        <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="add">Añadir</button>
    </div>
    <ConfirmModal v-if="confirming.show" :title="'Eliminar categoría'" :message="`Vas a eliminar la categoría: ${confirming.name}`" confirm-label="Eliminar" confirm-word="ELIMINAR" @confirm="confirmRemove" @close="confirming.show=false" />
  </div>
</template>

<script setup>
import { ref, reactive, watchEffect } from 'vue'
import ConfirmModal from '../ui/ConfirmModal.vue'
const props = defineProps({ title: { type: String, default: 'Categorías' }, categories: Array })
const emit = defineEmits(['create','update','remove'])
const name = ref('')
const color = ref('#3b82f6')
const colors = reactive({})
const confirming = ref({ show: false, id: null, name: '' })
watchEffect(()=>{ (props.categories||[]).forEach(c=>{ colors[c.id] = c.color }) })
function add(){ if(!name.value.trim()) return; emit('create', { name: name.value.trim(), color: color.value }); name.value=''; }
function onColor(c){ emit('update', c.id, { color: colors[c.id] }) }
function askRemove(c){ confirming.value = { show: true, id: c.id, name: c.name } }
function confirmRemove(){ if(confirming.value.id){ emit('remove', confirming.value.id); confirming.value = { show: false, id: null, name: '' } } }
</script>


