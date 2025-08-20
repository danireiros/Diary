<template>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    <div class="space-y-4">
      <div class="p-3 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between">
          <div class="font-medium">{{ selected.toLocaleDateString() }}</div>
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="showHidden" /> Mostrar ocultas</label>
        </div>
        <div class="mt-3 flex items-center justify-between"><button class="px-2 py-1 text-xs rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="openNew">Nueva entrada</button></div>
      </div>
      <CategoryFilter :categories="categories" v-model:selected="filter" />
      <CategoryManager title="Categorías del Diario" :categories="categories" @create="createCat" @update="updateCat" @remove="removeCat" />
    </div>
    <div class="lg:col-span-3 space-y-2">
      <div v-for="e in visible" :key="e.id" class="rounded-2xl p-3 border dark:border-gray-700" :style="cardStyle(e)">
        <div class="flex items-start justify-between">
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-full text-xs" :style="badgeStyle(e)">{{ categoryName(e.diary_category_id) }}</span>
            <div class="font-semibold">{{ e.title }}</div>
            <span v-if="e.hidden" class="text-xs bg-black/30 text-white px-2 py-0.5 rounded-full">Oculta</span>
          </div>
          <div class="flex items-center gap-2">
            <button class="px-2 py-1 text-xs rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="openEdit(e)">Editar</button>
            <button class="px-2 py-1 text-xs rounded-lg border dark:border-gray-700 hover:bg-red-50 text-red-600" @click="remove(e.id)">Borrar</button>
          </div>
        </div>
        <div v-if="e.content" class="text-sm mt-2 whitespace-pre-wrap" :class="{ 'opacity-50': e.hidden && !showHidden }">{{ e.content }}</div>
      </div>
      <div v-if="visible.length===0" class="text-sm text-gray-500">No hay entradas para este día.</div>
    </div>

    <DiaryModal v-if="showForm" :categories="categories" :initial="editing" :default-date="selected.toISOString().slice(0,10)" @close="showForm=false" @save="onSave" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { lightenHex, getContrastText } from '../utils/color'
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
function openNew(){ editing.value=null; showForm.value=true }
function openEdit(e){ editing.value=e; showForm.value=true }
async function onSave(payload){ if(payload.id){ await journalStore.update(payload.id, payload) } else { await journalStore.create(payload) } showForm.value=false; await refresh() }
async function remove(id){ await journalStore.remove(id); await refresh() }

async function createCat(p){ await catStore.create(p); await refresh() }
async function updateCat(id,p){ await catStore.update(id,p); await refresh() }
async function removeCat(id){ await catStore.remove(id); await refresh() }

async function refresh(){ await catStore.fetch(); await journalStore.fetch() }
onMounted(refresh)
</script>


