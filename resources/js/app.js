import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './components/App.vue'
import router from './router'
import './bootstrap'
import { useThemeStore } from './stores/theme'

const pinia = createPinia()
const app = createApp(App)
app.use(pinia)
app.use(router)
app.mount('#app')

// Aplicar tema guardado usando la instancia de pinia
const theme = useThemeStore(pinia)
theme.apply()
