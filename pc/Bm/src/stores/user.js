import { defineStore } from 'pinia'


// 创建user信息仓库


export const useUserStore = defineStore("user", {
    state: () => ({
        // 用户详细信息
        userInfo: localStorage.getItem('userInfo') ? JSON.parse(localStorage.getItem('userInfo')) : {}
    }),
    actions: {
        // 存储用户信息
        saveUserInfo(userInfo) {
            this.userInfo = userInfo
            // 存储整个用户信息
            localStorage.setItem('userInfo', JSON.stringify(userInfo))
        },
        loginOut() {
            // 退出登录
            this.userInfo = ''
            // 移除用户信息
            localStorage.removeItem("userInfo");
        }
    }
})


console.log("userstore 启用");