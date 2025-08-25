<template>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    <div class="lg:col-span-3 space-y-4">
      <div class="flex items-center justify-between gap-2 p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm">
        <div class="flex items-center gap-2">
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="goToday">Hoy</button>
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="step(-1)">◀</button>
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="step(1)">▶</button>
          <div class="font-semibold ml-2 text-lg">{{ title }}</div>
        </div>
        <div class="flex items-center gap-2">
          <button v-for="v in views" :key="v.k" class="px-3 py-2 rounded-xl" :class="{ 'bg-indigo-600 text-white': view===v.k, 'bg-indigo-600/70 text-white': view!==v.k }" @click="view=v.k">{{ v.label }}</button>
        </div>
      </div>

      <component :is="currentView" :date="cursor" :occurrences="occurrences" :categories="categories" @taskClick="openEdit" />
    </div>
    <div class="space-y-4">
      <div class="p-3 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-2"><h3 class="font-semibold">Tareas</h3><button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="openNew">Nueva</button></div>
        <div class="space-y-2 max-h-[420px] overflow-auto pr-1">
          <div v-for="o in occurrences.slice(0,150)" :key="o.task.id + o.start" class="border dark:border-gray-700 rounded-xl p-2 flex items-center justify-between" :class="{ 'opacity-50': new Date(o.end) < now }">
            <div class="cursor-pointer" @click="openEdit(o.task)">
              <div class="font-medium flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full" :style="{ backgroundColor: categoryColor(o.task.category_id) }" />
                {{ o.task.title }}
              </div>
              <div class="text-xs text-gray-600 dark:text-gray-300">{{ fmtDateTime(o.start) }} · {{ duration(o.start, o.end) }} min</div>
            </div>
            <div class="flex items-center gap-2">
              <select class="border dark:border-gray-700 rounded-lg px-2 py-1 text-xs bg-white dark:bg-gray-900" v-model="statusMap[o.task.id]" @change="updateStatus(o.task)">
                <option value="todo">Sin hacer</option>
                <option value="pending">Pendiente</option>
                <option value="done">Finalizada</option>
              </select>
              <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="openEdit(o.task)">Editar</button>
              <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="askRemove(o.task.id, o.task.title)">Borrar</button>
            </div>
          </div>
          <div v-if="occurrences.length===0" class="text-sm text-gray-500">No hay tareas en el rango visible.</div>
        </div>
      </div>
      <CategoryFilter :categories="categories" v-model:selected="catFilter" />
      <CategoryManager title="Categorías de Tareas/Notas" :categories="categories" @create="createCat" @update="updateCat" @remove="removeCat" />
    </div>

    <TaskModal v-if="showForm" :categories="categories" :initial="editing" @close="showForm=false" @save="onSave" />
  </div>
  <ConfirmModal v-if="confirming.show" :title="'Eliminar tarea'" :message="`Vas a eliminar: ${confirming.title}`" confirm-label="Eliminar" confirm-word="ELIMINAR" @confirm="confirmRemove" @close="confirming.show=false" />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { format } from 'date-fns'
import { startOfDay, endOfDay, addDays, addMonths, startOfWeek, endOfWeek } from '../utils/date'
import { useCategoriesStore } from '../stores/categories'
import { useTasksStore } from '../stores/tasks'
import TaskModal from '../widgets/TaskModal.vue'
import DayView from '../widgets/DayView.vue'
import WeekView from '../widgets/WeekView.vue'
import MonthView from '../widgets/MonthView.vue'
import CategoryFilter from '../widgets/CategoryFilter.vue'
import CategoryManager from '../widgets/CategoryManager.vue'
import ConfirmModal from '../ui/ConfirmModal.vue'

const categoriesStore = useCategoriesStore()
const tasksStore = useTasksStore()

const views = [
  { k: 'day', label: 'Día' },
  { k: 'week', label: 'Semana' },
  { k: 'month', label: 'Mes' },
]
const view = ref('month')
const cursor = ref(new Date())
const now = new Date()

const title = computed(()=> {
  if(view.value==='day') return format(cursor.value, 'eeee, dd LLL yyyy')
  if(view.value==='week') return `${format(startOfWeek(cursor.value), 'dd LLL')} – ${format(endOfWeek(cursor.value), 'dd LLL yyyy')}`
  return format(cursor.value, 'LLLL yyyy')
})

const currentView = computed(()=> view.value==='day'? DayView : view.value==='week'? WeekView : MonthView)

const categories = computed(()=> categoriesStore.items)
const catFilter = ref({})
const occurrences = computed(()=> tasksStore.occurrences.map(o=> ({ ...o, start: new Date(o.start), end: new Date(o.end) })))

function goToday(){ cursor.value = new Date() }
function step(dir){ if(view.value==='day') cursor.value = addDays(cursor.value, dir); else if(view.value==='week') cursor.value = addDays(cursor.value, 7*dir); else cursor.value = addMonths(cursor.value, dir) }

function range(){ const cur=cursor.value; if(view.value==='day') return { start: startOfDay(cur), end: endOfDay(cur) }; if(view.value==='week') return { start: startOfWeek(cur), end: endOfWeek(cur) }; return { start: startOfWeek(new Date(cur.getFullYear(),cur.getMonth(),1)), end: endOfWeek(new Date(cur.getFullYear(),cur.getMonth()+1,0)) } }

function fmtDateTime(s){ const d=new Date(s); return `${d.toLocaleDateString()} ${d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}` }
function duration(a,b){ return Math.max(1, Math.round((new Date(b)-new Date(a))/60000)) }
function categoryColor(id){ return categories.value.find(c=>c.id===id)?.color || '#333' }

const showForm = ref(false)
const editing = ref(null)
const statusMap = ref({})
const confirming = ref({ show: false, id: null, title: '' })

function openNew(){ editing.value = null; showForm.value = true }
function openEdit(task){ editing.value = task; showForm.value = true }
async function onSave(payload){ if(payload.id){ await tasksStore.update(payload.id, payload) } else { await tasksStore.create(payload) } showForm.value=false; await refresh() }
async function updateStatus(task){ await tasksStore.update(task.id, { status: statusMap.value[task.id] }); await refresh() }
function askRemove(id, title){ confirming.value = { show: true, id, title } }
async function confirmRemove(){ if(confirming.value.id){ await tasksStore.remove(confirming.value.id); confirming.value = { show:false, id:null, title:'' }; await refresh() } }

function selectedCategoryIds(){ const ids = categories.value.filter(c=> catFilter.value[c.id] !== false).map(c=>c.id); return ids }

async function refresh(){
  await categoriesStore.fetch()
  await tasksStore.fetch()
  const r=range();
  const catIds = selectedCategoryIds();
  await tasksStore.fetchOccurrences({ start: r.start.toISOString(), end: r.end.toISOString(), category_ids: catIds })
}

async function createCat(p){ await categoriesStore.create(p); await refresh() }
async function updateCat(id,p){ await categoriesStore.update(id,p); await refresh() }
async function removeCat(id){ await categoriesStore.remove(id); await refresh() }

onMounted(async ()=>{ await refresh() })
watch([view, cursor, catFilter, categories], async ()=>{ const r=range(); const catIds = selectedCategoryIds(); await tasksStore.fetchOccurrences({ start: r.start.toISOString(), end: r.end.toISOString(), category_ids: catIds }) })
</script>


