import { createApp } from 'vue'
import './assets/less/index.less'
// 引入elementplus的样式 那么在所有文件中都可以使用elementplus的组件
import 'element-plus/dist/index.css'
import App from './App.vue'
import router from './router'
import less from 'less'
// 导入全局接口
import api from './api'
// 导入pinia
import { createPinia } from 'pinia'
// 导入userstore
import { useUserStore } from '@/stores/user'
// 导入elementicon
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import { usePageStore } from '@/stores/page'


const pinia = createPinia()
const app = createApp(App)

//使用elementicon
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
}

// 将api挂载为全局属性
app.config.globalProperties.$api = api

// 注册全局前置守卫
router.beforeEach((to) => {
    // ✅ 这样做是可行的，因为路由器是在其被安装之后开始导航的，
    // 而此时 Pinia 也已经被安装。
    const store = useUserStore()
    const pageStore = usePageStore()

    if (!store.userInfo.token && to.name !== 'login') {
        ElMessage({
            type: 'warning',
            message: '请先登录'
        })

        return {
            name: 'login'
        }
    }

    // 记录pagestore
    pageStore.setPageName(to.name)
})

// 挂载路由
app.use(router).use(less).use(pinia)
app.mount('#app')




