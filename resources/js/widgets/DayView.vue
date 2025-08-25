<template>
  <div :class="['relative border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden', isToday? 'ring-2 ring-amber-400':'bg-white dark:bg-gray-800']">
    <div class="grid grid-cols-[60px_1fr]">
      <div class="border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
        <div v-for="h in 24" :key="h" class="h-12 border-b border-gray-200 dark:border-gray-700 text-xs text-gray-500 flex items-start justify-end pr-2 pt-1">{{ h-1 }}:00</div>
      </div>
      <div class="relative">
        <div v-for="h in 24" :key="'g'+h" class="h-12 border-b border-gray-200 dark:border-gray-700" />
        <div v-for="o in placed" :key="o.task.id + o.s.toISOString()" class="absolute rounded-xl shadow-sm p-2 text-xs text-white overflow-hidden cursor-pointer" :class="{ 'opacity-50': o.e < now }" :style="styleFor(o)" @click="$emit('taskClick', o.task)">
          <div class="font-semibold truncate" :class="{ 'line-through': o.task.status==='done' }">{{ o.task.title }}</div>
          <div class="opacity-90">{{ fmt(o.s) }} – {{ fmt(o.e) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { startOfDay, endOfDay } from '../utils/date'

const props = defineProps({ date: Date, occurrences: Array, categories: Array })
const now = new Date()
const isToday = computed(()=> startOfDay(props.date).getTime() === startOfDay(now).getTime())
const dayStart = computed(()=> startOfDay(props.date))
const dayEnd = computed(()=> endOfDay(props.date))
const occs = computed(()=> props.occurrences.filter(o=> o.start <= dayEnd.value && o.end >= dayStart.value))
const placed = computed(()=>{
  const lanes=[]
  return occs.value.map(o=>{
    let s = o.start < dayStart.value ? dayStart.value : o.start
    let e = o.end > dayEnd.value ? dayEnd.value : o.end
    let lane=0; while(lanes[lane] && lanes[lane] > s) lane++; lanes[lane]=e
    return { ...o, s, e, lane }
  })
})

// Grid uses 24 rows of h-12 (48px) per hour => 48/60 px per minute
function topOf(d){ return (d.getHours()*60 + d.getMinutes()) * (48/60) }
function styleFor(o){ const top = topOf(o.s); const height = Math.max(18, (o.e - o.s)/60000*(48/60)); const left = o.lane*8; const width = `calc(100% - ${o.lane*8}px)`; const color = props.categories.find(c=>c.id===o.task.category_id)?.color || '#333'; return { top: top+'px', height: height+'px', left: left+'px', width, backgroundColor: color } }
function fmt(d){ return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }
</script>


