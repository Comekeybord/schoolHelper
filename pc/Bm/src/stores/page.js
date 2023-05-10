// 存储当前路由信息
import { defineStore } from 'pinia'





export const usePageStore = defineStore('page', {
    state: () => ({
        // 存储当前页面路由名
        currentPageName: '',
    }),
    actions: {
        setPageName(name) {
            this.currentPageName = name
        }
    }
})

