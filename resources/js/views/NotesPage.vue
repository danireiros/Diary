<template>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
    <div class="lg:col-span-3 space-y-4">
      <div class="p-3 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-2"><h3 class="font-semibold">Notas</h3><button class="px-2 py-1 text-xs rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="openNew">Nueva</button></div>
        <div class="space-y-2">
          <div v-for="n in notes" :key="n.id" class="border dark:border-gray-700 rounded-xl p-3 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="inline-block w-3 h-3 rounded-full" :style="{ backgroundColor: categoryColor(n.category_id) }" />
                <div class="font-medium">{{ n.title }}</div>
              </div>
              <div class="flex items-center gap-2">
                <select class="border dark:border-gray-700 rounded-lg px-2 py-1 text-xs bg-white dark:bg-gray-900" v-model="statusMap[n.id]" @change="updateStatus(n)">
                  <option value="todo">Sin hacer</option>
                  <option value="pending">Pendiente</option>
                  <option value="done">Finalizada</option>
                </select>
                <button class="px-2 py-1 text-xs rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700" @click="openEdit(n)">Editar</button>
                <button class="px-2 py-1 text-xs rounded-lg border dark:border-gray-700 hover:bg-red-50 text-red-600" @click="remove(n.id)">Borrar</button>
              </div>
            </div>
            <div v-if="n.content" class="text-sm text-gray-700 dark:text-gray-200 mt-1 whitespace-pre-wrap">{{ n.content }}</div>
          </div>
          <div v-if="notes.length===0" class="text-sm text-gray-500">No hay notas.</div>
        </div>
      </div>
    </div>
    <div class="space-y-4">
      <CategoryFilter :categories="categories" v-model:selected="filter" />
    </div>

    <NoteModal v-if="showForm" :categories="categories" :initial="editing" @close="showForm=false" @save="onSave" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCategoriesStore } from '../stores/categories'
import { useNotesStore } from '../stores/notes'
import NoteModal from '../widgets/NoteModal.vue'
import CategoryFilter from '../widgets/CategoryFilter.vue'

const categoriesStore = useCategoriesStore()
const notesStore = useNotesStore()

const categories = computed(()=> categoriesStore.items)
const filter = ref({})

const notes = computed(()=> notesStore.items.filter(n=> filter.value[n.category_id] !== false ))

function categoryColor(id){ return categories.value.find(c=>c.id===id)?.color || '#333' }

const showForm = ref(false)
const editing = ref(null)
const statusMap = ref({})
function openNew(){ editing.value=null; showForm.value=true }
function openEdit(n){ editing.value=n; showForm.value=true }
async function onSave(payload){ if(payload.id){ await notesStore.update(payload.id, payload) } else { await notesStore.create(payload) } showForm.value=false; await refresh() }
async function updateStatus(n){ await notesStore.update(n.id, { status: statusMap.value[n.id] }); await refresh() }
async function remove(id){ await notesStore.remove(id); await refresh() }

async function refresh(){ await categoriesStore.fetch(); await notesStore.fetch() }
onMounted(refresh)
</script>


