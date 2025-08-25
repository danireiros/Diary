<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-3 z-50" @click.self="$emit('close')">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl">
      <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="font-semibold">{{ title }}</div>
      </div>
      <div class="p-4 space-y-3">
        <p class="text-sm">{{ message }}</p>
        <div>
          <label class="text-xs block mb-1">Para confirmar escribe "{{ confirmWord }}"</label>
          <input class="border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 w-full bg-white dark:bg-gray-900" v-model="typed" @keyup.enter="onConfirmIfValid" />
        </div>
      </div>
      <div class="p-4 flex items-center justify-end gap-2 border-t border-gray-200 dark:border-gray-700">
        <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="$emit('close')">Cancelar</button>
        <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" :class="{ 'opacity-50 pointer-events-none': !valid }" @click="onConfirmIfValid">{{ confirmLabel }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
const props = defineProps({
  title: { type: String, default: 'Confirmar eliminación' },
  message: { type: String, default: 'Esta acción no se puede deshacer.' },
  confirmWord: { type: String, default: 'ELIMINAR' },
  confirmLabel: { type: String, default: 'Eliminar' },
})
const emit = defineEmits(['confirm','close'])
const typed = ref('')
const valid = computed(()=> typed.value.trim().toUpperCase() === props.confirmWord.toUpperCase())
function onConfirmIfValid(){ if(valid.value){ emit('confirm'); emit('close') } }
</script>



