import { defineStore } from 'pinia'
import { tasksApi } from '../lib/api'

export const useTasksStore = defineStore('tasks', {
  state: ()=> ({ items: [], occurrences: [] }),
  actions: {
    async fetch(){ this.items = await tasksApi.list() },
    async create(p){ const r = await tasksApi.create(p); await this.fetch(); return r },
    async update(id,p){ const r = await tasksApi.update(id,p); await this.fetch(); return r },
    async remove(id){ await tasksApi.remove(id); await this.fetch() },
    async fetchOccurrences(params){ this.occurrences = await tasksApi.occurrences(params) },
  }
})


