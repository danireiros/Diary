import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './components/App.vue'
import router from './router'
import './bootstrap'
import { useThemeStore } from './stores/theme'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')

// Aplicar tema guardado
const theme = useThemeStore()
theme.apply()
