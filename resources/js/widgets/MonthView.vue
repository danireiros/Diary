<template>
  <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-gray-800 overflow-hidden">
    <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 text-xs font-medium">
      <div v-for="d in ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']" :key="d" class="px-2 py-2 border-l border-gray-200 dark:border-gray-700 first:border-l-0">{{ d }}</div>
    </div>
    <div>
      <div v-for="(week,wi) in weeks" :key="wi" class="relative border-b border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-7">
          <div v-for="(d,di) in week" :key="di" class="h-24 p-1 border-l border-gray-200 dark:border-gray-700 first:border-l-0 relative" :class="cellClass(d)">
            <div class="text-xs text-gray-500">{{ d.getDate() }}</div>
            <div class="mt-1 space-y-1">
              <div v-for="o in daySingles(d)" :key="chipKey(o, d)" class="text-[10px] rounded px-1 py-0.5 text-white truncate cursor-pointer" :class="{ 'opacity-50': o.end < now }" :style="{ backgroundColor: categoryColor(o) }" @click="$emit('taskClick', o.task)">
                <span :class="{ 'line-through': o.task.status==='done' }">{{ chipLabel(o) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="absolute left-0 right-0" style="top: 96px">
          <div v-for="(b,i) in layout[wi].placed" :key="i+b.occ.task.id" class="absolute text-[11px] rounded-lg px-2 py-1 text-white truncate shadow cursor-pointer" :class="{ 'opacity-50': b.occ.end < now }" :style="barStyle(b)" @click="$emit('taskClick', b.occ.task)">
            <span :class="{ 'line-through': b.occ.task.status==='done' }">{{ b.occ.task.title }}</span>
          </div>
        </div>
        <div :style="{ height: Math.max(0, (layout[wi].lanesCount) * 22) + 'px' }" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { startOfDay, startOfWeek, endOfWeek, startOfMonth, endOfMonth, addDays, isSameDay } from '../utils/date'

const props = defineProps({ date: Date, occurrences: Array, categories: Array })
const now = new Date()

const weeks = computed(()=> getWeeksMatrixForMonth(props.date))
const computedData = computed(()=> computeMonthBars(props.date, props.occurrences, props.categories))
const layout = computed(()=> computedData.value.layout)

function getWeeksMatrixForMonth(anyDate){ const first=startOfMonth(anyDate); const last=endOfMonth(anyDate); const gridStart=startOfWeek(first); const gridEnd=endOfWeek(last); const days=[]; let cur=new Date(gridStart); while(cur<=gridEnd){ days.push(new Date(cur)); cur=addDays(cur,1);} const weeks=[]; for(let i=0;i<days.length;i+=7) weeks.push(days.slice(i,i+7)); return weeks; }
function computeMonthBars(monthDate, occurrences){ const weeks=getWeeksMatrixForMonth(monthDate); const gridStart=startOfWeek(startOfMonth(monthDate)); const gridEnd=endOfWeek(endOfMonth(monthDate)); const singles=[]; const multi=occurrences.filter(occ=>{ const isMulti = !occ.task.all_day && startOfDay(occ.end) > startOfDay(occ.start); if(!isMulti){ if(occ.start <= gridEnd && occ.end >= gridStart) singles.push(occ) } return isMulti && occ.start <= gridEnd && occ.end >= gridStart }); const perWeek=weeks.map(()=>[]); multi.forEach(occ=>{ let segStart=new Date(Math.max(startOfDay(occ.start).getTime(), gridStart.getTime())); const last=new Date(Math.min(startOfDay(occ.end).getTime(), gridEnd.getTime())); while(segStart<=last){ const wEnd=endOfWeek(segStart); const sCol=(segStart.getDay()+6)%7; const eDay=new Date(Math.min(last.getTime(), wEnd.getTime())); const eCol=(eDay.getDay()+6)%7; const wIndex=weeks.findIndex(w=>segStart>=startOfDay(w[0])&&segStart<=startOfDay(w[6])); if(wIndex>=0) perWeek[wIndex].push({occ,startCol:sCol,endCol:eCol}); segStart=addDays(wEnd,1);} }); const layout=perWeek.map(bars=>{ const lanes=[]; const placed=bars.map(b=>{ let lane=0; while(lanes[lane]&&lanes[lane].some(x=>!(b.endCol<x.startCol||b.startCol>x.endCol))) lane++; if(!lanes[lane]) lanes[lane]=[]; lanes[lane].push(b); return {...b,lane}; }); return {placed, lanesCount: lanes.length}; }); return { weeks, layout, singles }; }
function daySingles(d){ const singles = computedData.value.singles||[]; return singles.filter(o=> { if(o.task.all_day){ const s = startOfDay(o.start); const e = startOfDay(o.end); const day = startOfDay(d); return day >= s && day <= e } return isSameDay(o.start, d) && startOfDay(o.end) <= startOfDay(o.start) }) }
function categoryColor(o){ return props.categories.find(c=>c.id===o.task.category_id)?.color || '#333' }
function chipKey(o, d){ if(o.task.all_day){ return o.task.id + '-' + startOfDay(d).toISOString() } return o.task.id + '-' + o.start.toISOString() }
function chipLabel(o){ return o.task.all_day ? o.task.title : `${o.start.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})} · ${o.task.title}` }
function cellClass(d){ const month = props.date.getMonth(); return [ d.getMonth()===month? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900/50', isSameDay(d, now)? 'ring-2 ring-amber-400':'' ] }
function barStyle(b){ const width=((b.endCol-b.startCol+1)/7)*100+'%'; const left=(b.startCol/7)*100+'%'; const top=b.lane*22; const color= props.categories.find(c=>c.id===b.occ.task.category_id)?.color || '#333'; return { width, left, top: top+'px', backgroundColor: color } }

</script>


