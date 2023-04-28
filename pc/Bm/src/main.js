import { createApp } from 'vue'
import './assets/less/index.less'
import App from './App.vue'
import router from './router'
import less from 'less'

const app = createApp(App)


// 挂载路由
app.use(router).use(less)
app.mount('#app')




