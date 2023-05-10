import { createRouter, createWebHashHistory } from 'vue-router'




const routes = [
    {
        // 设置不存在的路由
        path: '/:params(.*)+',
        name: 'notFound',
        component: () => import('@/pages/NotFound/NotFound.vue'),
    },
    {
        // 根路径
        path: '/',
        name: 'root',
        redirect: '/login'
    },
    {
        // 登录页
        path: '/login',
        name: 'login',
        component: () => import('../pages/login/login.vue')
    },
    {
        // 管理主页
        path: '/home',
        name: 'home',
        components: {
            // 使用命名视图
            default: () => import('@/pages/home/home.vue'),
        },
        // 开启路由传参
        props: true,
        children: [
            {
                // 默认展示个人中心
                path: '',
                name: 'userCenter',
                components: {
                    mainContent: () => import('@/pages/home/User/UserCenter/UserCenter.vue')
                }
            },
            {
                // 示用户管理页
                path: 'userManage',
                name: 'userManage',
                components: {
                    mainContent: () => import('@/pages/home/User/UserManage/UserManage.vue')
                }
            },
            {
                // 用户组管理页面
                path: 'userGroup',
                name: 'userGroup',
                components: {
                    mainContent: () => import('@/pages/home/User/UserGroup/UserGroup.vue')
                }
            },
            {
                // 用户反馈页面
                path: 'userFeeds',
                name: 'userFeeds',
                components: {
                    mainContent: () => import('@/pages/home/User/UserFeeds/UserFeeds.vue')
                }
            },
            {
                // 系统设置
                path: 'config',
                name: 'config',
                components: {
                    mainContent: () => import('@/pages/home/System/Config/Config.vue')
                }
            },
            {
                // 安全管理
                path: 'safe',
                name: 'safe',
                components: {
                    mainContent: () => import('@/pages/home/System/Safe/Safe.vue')
                }
            },
            {
                // 登录记录
                path: 'loginlog',
                name: 'loginlog',
                components: {
                    mainContent: () => import('@/pages/home/System/LoginLog/LoginLog.vue')
                }
            },
        ]
    }
]


const router = createRouter({
    history: createWebHashHistory(),
    routes
})




export default router

