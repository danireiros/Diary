import { defineStore } from 'pinia'
import { notesApi } from '../lib/api'

export const useNotesStore = defineStore('notes', {
  state: ()=> ({ items: [] }),
  actions: {
    async fetch(){ this.items = await notesApi.list() },
    async create(p){ const r = await notesApi.create(p); await this.fetch(); return r },
    async update(id,p){ const r = await notesApi.update(id,p); await this.fetch(); return r },
    async remove(id){ await notesApi.remove(id); await this.fetch() },
  }
})


