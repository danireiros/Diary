<template>
  <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-gray-800 overflow-hidden">
    <div class="grid grid-cols-[60px_repeat(7,1fr)] border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
      <div />
      <div v-for="d in days" :key="d.toISOString()" class="p-2 text-sm font-medium border-l border-gray-200 dark:border-gray-700" :class="{ 'bg-amber-50 dark:bg-amber-900/20': isSameDay(d, now) }">
        {{ d.toLocaleDateString(undefined,{weekday:'short', day:'numeric', month:'short'}) }}
      </div>
    </div>
    <div class="grid grid-cols-[60px_repeat(7,1fr)]">
      <div class="border-r border-gray-200 dark:border-gray-700">
        <div v-for="h in 24" :key="h" class="h-12 border-b border-gray-200 dark:border-gray-700 text-xs text-gray-500 flex items-start justify-end pr-2 pt-1">{{ h-1 }}:00</div>
      </div>
      <div v-for="(d,idx) in days" :key="idx" class="relative border-l border-gray-200 dark:border-gray-700" :class="{ 'bg-amber-50/30 dark:bg-amber-900/10': isSameDay(d, now) }">
        <div v-for="h in 24" :key="'g'+h" class="h-12 border-b border-gray-200 dark:border-gray-700" />
        <div v-for="o in byDay[idx]" :key="o.task.id + o.start.toISOString()" class="absolute rounded-xl shadow-sm p-1.5 text-xs text-white cursor-pointer" :class="{ 'opacity-50': o.end < now }" :style="styleFor(o)" @click="$emit('taskClick', o.task)">
          <div class="font-semibold truncate" :class="{ 'line-through': o.task.status==='done' }">{{ o.task.title }}</div>
          <div class="opacity-90">{{ fmt(o.start) }} – {{ fmt(o.end) }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { addDays, startOfDay, endOfDay, startOfWeek, isSameDay } from '../utils/date'

const props = defineProps({ date: Date, occurrences: Array, categories: Array })
const now = new Date()
const weekStart = computed(()=> startOfWeek(props.date))
const days = computed(()=> Array.from({length:7}).map((_,i)=> addDays(weekStart.value, i)))

const byDay = computed(()=>{
  const laneByCol = Array.from({length:7}).map(()=>[])
  const list = Array.from({length:7}).map(()=>[])
  const occs = props.occurrences
  for(const o of occs){
    const parts = splitByDaySpan(o)
    for(const p of parts){
      const col = (p.day.getDay()+6)%7
      let lane=0; while(laneByCol[col][lane] && laneByCol[col][lane] > p.start) lane++
      laneByCol[col][lane] = p.end
      list[col].push({ ...p, lane, col })
    }
  }
  return list
})

function splitByDaySpan(occ){ const parts=[]; let s=new Date(occ.start); const end=new Date(occ.end); while(s<end){ const dayEnd=endOfDay(s); const e=new Date(Math.min(dayEnd.getTime(), end.getTime())); parts.push({ ...occ, start:new Date(s), end:e, day:startOfDay(s) }); s=addDays(startOfDay(s),1) } return parts }
// Grid uses 24 rows of h-12 (48px) per hour => 48/60 px per minute
function styleFor(o){ const top = (o.start.getHours()*60+o.start.getMinutes())*(48/60); const height = Math.max(18, (o.end - o.start)/60000*(48/60)); const left = o.lane*6; const width = `calc(100% - ${o.lane*6 + 2}px)`; const color = props.categories.find(c=>c.id===o.task.category_id)?.color || '#333'; return { top: top+'px', height: height+'px', left: left+'px', width, backgroundColor: color } }
function fmt(d){ return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }

</script>


