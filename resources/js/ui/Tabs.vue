<template>
  <div class="flex gap-2 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm w-fit">
    <RouterLink v-for="it in items" :key="it.k" :to="it.to" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm" :class="[{ 'bg-gray-900 text-white': isActive(it.to) }, it.disabled? 'opacity-50 pointer-events-none' : '']">{{ it.label }}</RouterLink>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
const route = useRoute()
const items = [
  { k: 'diary', label: 'Diario', to: '/diary' },
  { k: 'notes', label: 'Notas', to: '/notes' },
  { k: 'calendar', label: 'Calendario', to: '/calendar', disabled: true },
]
const isActive = (to)=> route.path.startsWith(to)
</script>


