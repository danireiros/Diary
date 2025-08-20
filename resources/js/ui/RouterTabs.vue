<template>
  <component :is="current" />
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import CalendarPage from '../views/CalendarPage.vue'
import NotesPage from '../views/NotesPage.vue'
import DiaryPage from '../views/DiaryPage.vue'

const route = useRoute()
const current = computed(()=>{
  if(route.path.startsWith('/notes')) return NotesPage
  if(route.path.startsWith('/diary')) return DiaryPage
  return CalendarPage
})
</script>


