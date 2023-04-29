import { createApp } from 'vue'
import './assets/less/index.less'
// 引入elementplus的样式 那么在所有文件中都可以使用elementplus的组件
import 'element-plus/dist/index.css'
import App from './App.vue'
import router from './router'
import less from 'less'
// 导入全局接口
import api from './api'




const app = createApp(App)

// 将api挂载为全局属性
app.config.globalProperties.$api = api

// 挂载路由
app.use(router).use(less)
app.mount('#app')




