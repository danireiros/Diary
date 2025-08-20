<template>
  <span class="inline-block w-3 h-3 rounded-full mr-2 align-middle" :style="{ backgroundColor: color }" />
</template>

<script setup>
defineProps({ color: { type: String, default: '#888' } })
</script>


