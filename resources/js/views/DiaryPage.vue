<template>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    <div class="space-y-4">
      <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm" style="height: 64px;">
        <div class="flex items-center justify-between">
          <div class="font-medium">{{ selected.toLocaleDateString() }}</div>
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="showHidden" /> Mostrar ocultas</label>
        </div>
      </div>
      <CategoryFilter :categories="categories" v-model:selected="filter" />
      <CategoryManager title="Categorías del Diario" :categories="categories" @create="createCat" @update="updateCat" @remove="removeCat" />
    </div>
    <div class="lg:col-span-3 space-y-3">
      <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="step(-1)">◀</button>
          <div class="font-medium">{{ selected.toLocaleDateString() }}</div>
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="goToday">Hoy</button>
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="step(1)">▶</button>
        </div>
        <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white" @click="openNew">Nueva entrada</button>
      </div>
      <div v-for="e in visible" :key="e.id" class="rounded-2xl p-3 border border-gray-200 dark:border-gray-700 text-black" :style="cardStyle(e)">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-full text-xs" :style="badgeStyle(e)">{{ categoryName(e.diary_category_id) }}</span>
            <div class="font-semibold">{{ e.title }}</div>
            <span v-if="e.hidden" class="text-xs bg-black/30 text-white px-2 py-0.5 rounded-full">Oculta</span>
          </div>
          <div class="flex items-center gap-2">
            <button class="p-2 py-1 rounded-xl bg-indigo-600 text-white" @click="openEdit(e)">Editar</button>
            <button class="p-2 py-1 rounded-xl bg-gray-50 dark:text-white dark:bg-gray-700 text-slate-700" @click="askRemove(e.id, e.title)">X</button>
          </div>
        </div>
        <div v-if="e.content" class="text-sm mt-2 whitespace-pre-wrap" :class="{ 'opacity-50': e.hidden && !showHidden }">{{ e.content }}</div>
      </div>
      <div v-if="visible.length===0" class="text-sm text-gray-500">No hay entradas para este día.</div>
    </div>

    <DiaryModal v-if="showForm" :categories="categories" :initial="editing" :default-date="selected.toISOString().slice(0,10)" @close="showForm=false" @save="onSave" />
    <ConfirmModal v-if="confirming.show" :title="'Eliminar entrada'" :message="`Vas a eliminar: ${confirming.title}`" confirm-label="Eliminar" confirm-word="ELIMINAR" @confirm="confirmRemove" @close="confirming.show=false" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { lightenHex, getContrastText } from '../utils/color'
import ConfirmModal from '../ui/ConfirmModal.vue'
import { useDiaryCategoriesStore } from '../stores/categories'
import { useJournalStore } from '../stores/journal'
import DiaryModal from '../widgets/DiaryModal.vue'
import CategoryFilter from '../widgets/CategoryFilter.vue'
import CategoryManager from '../widgets/CategoryManager.vue'

const catStore = useDiaryCategoriesStore()
const journalStore = useJournalStore()

const categories = computed(()=> catStore.items)
const selected = ref(new Date())
const showHidden = ref(false)
const filter = ref({})

const entries = computed(()=> journalStore.items)
const visible = computed(()=> entries.value.filter(e=> e.date === selected.value.toISOString().slice(0,10) && (filter.value[e.diary_category_id] !== false) && (showHidden.value || !e.hidden) ))

function badgeStyle(e){ const c=categories.value.find(x=>x.id===e.diary_category_id)?.color || '#888'; return { backgroundColor: c, color: getContrastText(c) } }
function cardStyle(e){ const c=categories.value.find(x=>x.id===e.diary_category_id)?.color || '#888'; return { backgroundColor: lightenHex(c, 0.5) } }
function categoryName(id){ return categories.value.find(c=>c.id===id)?.name || 'Categoría' }

const showForm = ref(false)
const editing = ref(null)
const confirming = ref({ show: false, id: null, title: '' })
function openNew(){ editing.value=null; showForm.value=true }
function openEdit(e){ editing.value=e; showForm.value=true }
async function onSave(payload){ if(payload.id){ await journalStore.update(payload.id, payload) } else { await journalStore.create(payload) } showForm.value=false; await refresh() }
function askRemove(id, title){ confirming.value = { show: true, id, title } }
async function confirmRemove(){ if(confirming.value.id){ await journalStore.remove(confirming.value.id); confirming.value = { show:false, id:null, title:'' }; await refresh() } }

async function createCat(p){ await catStore.create(p); await refresh() }
async function updateCat(id,p){ await catStore.update(id,p); await refresh() }
async function removeCat(id){ await catStore.remove(id); await refresh() }

function goToday(){ selected.value = new Date(); refresh() }
function step(dir){ const d=new Date(selected.value); d.setDate(d.getDate()+dir); selected.value = d; refresh() }
async function refresh(){ await catStore.fetch(); await journalStore.fetch() }
onMounted(refresh)

// Solicitar contraseña al intentar mostrar las entradas ocultas
const hiddenUnlocked = ref(false)
watch(showHidden, (newVal) => {
  if(newVal){
    if(!hiddenUnlocked.value){
      const pass = window.prompt('Introduce la contraseña para mostrar las notas ocultas:')
      if(pass === 'ocultas58'){
        hiddenUnlocked.value = true
      } else {
        showHidden.value = false
        window.alert('Contraseña incorrecta')
      }
    }
  }
})
</script>


