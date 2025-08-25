<template>
  <div class="fixed inset-0 bg-black/30 flex items-center justify-center p-3" @click.self="$emit('close')">
    <div class="w-full max-w-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl">
      <div class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700"><div class="font-semibold">{{ initial? 'Editar entrada':'Nueva entrada' }}</div><button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="$emit('close')">Cerrar</button></div>
      <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="flex flex-col gap-2"><label class="text-sm">Título</label><input class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" v-model="title" /></div>
        <div class="flex flex-col gap-2"><label class="text-sm">Fecha</label><input class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" type="date" v-model="date" /></div>
        <div class="flex flex-col gap-2"><label class="text-sm">Categoría</label><select class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" v-model="categoryId"><option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option></select></div>
        <label class="flex items-center gap-2 mt-2"><input type="checkbox" v-model="hidden" /> Oculta</label>
      </div>
      <div class="px-4 pb-4">
        <div class="mt-3 flex flex-col gap-2"><label class="text-sm">Contenido</label><textarea class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 min-h-[120px] bg-white dark:bg-gray-900" v-model="content" /></div>
        <div class="mt-4 flex items-center justify-end gap-2"><button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="$emit('close')">Cancelar</button><button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="save">Guardar</button></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
const props = defineProps({ categories: Array, initial: Object, defaultDate: String })
const emit = defineEmits(['save','close'])
const title = ref(props.initial?.title || '')
const content = ref(props.initial?.content || '')
const categoryId = ref(props.initial?.diary_category_id || props.categories?.[0]?.id)
const date = ref(props.initial?.date || props.defaultDate || new Date().toISOString().slice(0,10))
const hidden = ref(!!props.initial?.hidden)
function save(){ if(!title.value.trim()) return; const payload = { title: title.value.trim(), content: content.value.trim(), diary_category_id: categoryId.value, date: date.value, hidden: hidden.value }; if(props.initial?.id) payload.id = props.initial.id; emit('save', payload) }
</script>


