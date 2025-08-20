import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
  state: ()=> ({ theme: localStorage.getItem('theme') || 'light' }),
  actions: {
    apply(){ const root=document.documentElement; if(this.theme==='dark') root.classList.add('dark'); else root.classList.remove('dark') },
    toggle(){ this.theme = this.theme==='dark' ? 'light' : 'dark'; localStorage.setItem('theme', this.theme); this.apply() },
  }
})


