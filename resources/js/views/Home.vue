<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 p-4 md:p-6">
    <div class="max-w-7xl mx-auto space-y-4">
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <h1 class="text-2xl md:text-3xl font-semibold">Gestor del Hogar</h1>
        <div class="flex items-center gap-2">
          <ThemeToggle />
          <Tabs />
        </div>
      </div>

      <RouterTabs />
    </div>
  </div>
</template>

<script setup>
import ThemeToggle from '../ui/ThemeToggle.vue'
import Tabs from '../ui/Tabs.vue'
import RouterTabs from '../ui/RouterTabs.vue'
</script>


