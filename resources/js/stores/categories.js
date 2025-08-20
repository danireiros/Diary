import { defineStore } from 'pinia'
import { categoriesApi, diaryCategoriesApi } from '../lib/api'

export const useCategoriesStore = defineStore('categories', {
  state: ()=> ({ items: [], filter: {} }),
  actions: {
    async fetch(){ this.items = await categoriesApi.list() },
    async create(p){ const r = await categoriesApi.create(p); await this.fetch(); return r },
    async update(id,p){ const r = await categoriesApi.update(id,p); await this.fetch(); return r },
    async remove(id){ await categoriesApi.remove(id); await this.fetch() },
    setFilter(v){ this.filter = v; localStorage.setItem('catFilter', JSON.stringify(v)) },
    loadFilter(){ try{ this.filter = JSON.parse(localStorage.getItem('catFilter')||'{}') }catch{ this.filter={} } },
  }
})

export const useDiaryCategoriesStore = defineStore('diaryCategories', {
  state: ()=> ({ items: [], filter: {} }),
  actions: {
    async fetch(){ this.items = await diaryCategoriesApi.list() },
    async create(p){ const r = await diaryCategoriesApi.create(p); await this.fetch(); return r },
    async update(id,p){ const r = await diaryCategoriesApi.update(id,p); await this.fetch(); return r },
    async remove(id){ await diaryCategoriesApi.remove(id); await this.fetch() },
    setFilter(v){ this.filter = v; localStorage.setItem('diaryCatFilter', JSON.stringify(v)) },
    loadFilter(){ try{ this.filter = JSON.parse(localStorage.getItem('diaryCatFilter')||'{}') }catch{ this.filter={} } },
  }
})


