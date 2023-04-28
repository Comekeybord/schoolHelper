import { createRouter, createWebHashHistory } from 'vue-router'




const routes = [
    {
        path: '/',
        name: 'root',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/login/login.vue')
    },
    {
        path: '/home',
        name: 'home',
        component: () => import('../pages/home/home.vue')
    }
]


const router = createRouter({
    history: createWebHashHistory(),
    routes
})




export default router

