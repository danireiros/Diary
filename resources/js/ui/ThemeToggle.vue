<template>
  <button class="px-3 py-2 rounded-xl border dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700" @click="toggle" :title="isDark? 'Cambiar a claro':'Cambiar a oscuro'">
    {{ isDark? '☀️ Claro' : '🌙 Oscuro' }}
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '../stores/theme'

const theme = useThemeStore()
const isDark = computed(()=> theme.theme === 'dark')
function toggle(){ theme.toggle() }
</script>


