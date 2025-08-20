<template>
  <div class="fixed inset-0 bg-black/30 flex items-center justify-center p-3" @click.self="$emit('close')">
    <div class="w-full max-w-3xl bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-xl">
      <div class="flex items-center justify-between p-3 border-b dark:border-gray-700">
        <div class="font-semibold">{{ initial? 'Editar tarea' : 'Nueva tarea' }}</div>
        <button class="px-3 py-1 rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="$emit('close')">Cerrar</button>
      </div>
      <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="flex flex-col gap-2">
          <label class="text-sm">Título</label>
          <input class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" v-model="title" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm">Categoría</label>
          <select class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" v-model="categoryId">
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <label class="flex items-center gap-2"><input type="checkbox" v-model="allDay" /> Todo el día / multi-día</label>
        <div class="flex flex-col gap-2"><label class="text-sm">Estado</label><select class="border dark:border-gray-700 rounded-lg px-2 py-1 text-xs bg-white dark:bg-gray-900" v-model="status"><option value="todo">Sin hacer</option><option value="pending">Pendiente</option><option value="done">Finalizada</option></select></div>
        <template v-if="!rec.freq">
          <div class="flex flex-col gap-2"><label class="text-sm">Inicio</label><input class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" type="datetime-local" v-model="startDT" /></div>
          <div class="flex flex-col gap-2"><label class="text-sm">Fin</label><input class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" type="datetime-local" v-model="endDT" /></div>
        </template>
      </div>
      <div class="px-4 pb-4">
        <div class="mt-2 p-3 rounded-xl border dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
          <div class="flex items-center gap-3 flex-wrap">
            <label class="text-sm font-medium">Repetición</label>
            <select class="border dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900" v-model="rec.freq"><option value="">Sin repetición</option><option value="DAILY">Diaria</option><option value="WEEKLY">Semanal</option><option value="MONTHLY">Mensual</option></select>
            <template v-if="rec.freq">
              <span class="text-sm">cada</span>
              <input class="border dark:border-gray-700 rounded-lg px-2 py-1 w-16 bg-white dark:bg-gray-900" type="number" min="1" v-model.number="rec.interval" />
              <span class="text-sm">{{ rec.freq==='DAILY'?'día(s)':rec.freq==='WEEKLY'?'semana(s)':'mes(es)' }}</span>
              <span class="text-sm">a las</span>
              <input class="border dark:border-gray-700 rounded-lg px-2 py-1 bg-white dark:bg-gray-900" type="time" v-model="rec.time" />
              <span class="text-sm">duración (min)</span>
              <input class="border dark:border-gray-700 rounded-lg px-2 py-1 w-20 bg-white dark:bg-gray-900" type="number" min="1" v-model.number="durationMinutes" />
            </template>
          </div>
          <div v-if="rec.freq==='WEEKLY'" class="mt-2 flex items-center gap-2 flex-wrap">
            <button v-for="(lbl,i) in ['L','M','X','J','V','S','D']" :key="i" class="px-3 py-1 rounded-lg border dark:border-gray-700" :class="{ 'bg-gray-900 text-white': onWeekday((i+1)%7) }" @click="toggleWeekday((i+1)%7)">{{ lbl }}</button>
          </div>
          <div v-if="rec.freq==='MONTHLY'" class="mt-2">
            <label class="text-sm mr-2">Días del mes:</label>
            <input class="border dark:border-gray-700 rounded-lg px-2 py-1 w-full mt-1 bg-white dark:bg-gray-900" placeholder="Ej: 1, 15, 30" v-model="byMonthDayStr" />
          </div>
        </div>
        <div class="mt-4 flex items-center justify-end gap-2"><button class="px-3 py-2 rounded-xl border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="$emit('close')">Cancelar</button><button class="px-3 py-2 rounded-xl border dark:border-gray-700 bg-gray-900 text-white" @click="save">Guardar</button></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { format } from 'date-fns'
const props = defineProps({ categories: Array, initial: Object })
const emit = defineEmits(['save','close'])

const title = ref(props.initial?.title || '')
const categoryId = ref(props.initial?.category_id || props.categories?.[0]?.id)
const allDay = ref(!!props.initial?.all_day)
const status = ref(props.initial?.status || 'todo')
const startDT = ref(props.initial?.start_at ? props.initial.start_at.slice(0,16) : new Date().toISOString().slice(0,16))
const endDT = ref(props.initial?.end_at ? props.initial.end_at.slice(0,16) : new Date(Date.now()+3600000).toISOString().slice(0,16))
const rec = reactive(props.initial?.recurrence_json ? { ...props.initial.recurrence_json } : { freq:'', interval:1, byWeekday:[], byMonthDay:[], time:'' })
const durationMinutes = ref(props.initial?.duration_minutes || 60)
const byMonthDayStr = computed({ get(){ return (rec.byMonthDay||[]).join(', ') }, set(v){ rec.byMonthDay = (v||'').split(/[^0-9]+/).map(n=>parseInt(n,10)).filter(Boolean) }})

function onWeekday(wd){ return (rec.byWeekday||[]).includes(wd) }
function toggleWeekday(wd){ const s=new Set(rec.byWeekday||[]); if(s.has(wd)) s.delete(wd); else s.add(wd); rec.byWeekday = Array.from(s).sort() }

function save(){
  if(!title.value.trim()) return
  const base = {
    title: title.value.trim(),
    category_id: categoryId.value,
    all_day: allDay.value,
    status: status.value,
  }
  let payload
  const toMysqlLocal = (d)=> format(new Date(d), 'yyyy-MM-dd HH:mm:ss')
  if(rec.freq){
    payload = {
      ...base,
      start_at: toMysqlLocal(startDT.value),
      end_at: toMysqlLocal(new Date(new Date(startDT.value).getTime() + (durationMinutes.value||60)*60000)),
      duration_minutes: durationMinutes.value,
      recurrence_json: rec,
    }
  } else {
    payload = {
      ...base,
      start_at: toMysqlLocal(startDT.value),
      end_at: toMysqlLocal(endDT.value),
      duration_minutes: durationMinutes.value,
      recurrence_json: null,
    }
  }
  if(props.initial?.id) payload.id = props.initial.id
  emit('save', payload)
}
</script>


