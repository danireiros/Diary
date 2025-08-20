import { defineStore } from 'pinia'
import { journalApi } from '../lib/api'

export const useJournalStore = defineStore('journal', {
  state: ()=> ({ items: [] }),
  actions: {
    async fetch(){ this.items = await journalApi.list() },
    async create(p){ const r = await journalApi.create(p); await this.fetch(); return r },
    async update(id,p){ const r = await journalApi.update(id,p); await this.fetch(); return r },
    async remove(id){ await journalApi.remove(id); await this.fetch() },
  }
})


