import request from "./request";


export default {
    // 统一编写请求接口

    // 登录
    login(data) {
        return request({
            method: 'post',
            url: '/user/login',
            data,
        })
    },

    // 获取用户列表
    getUserList() {
        return request({
            method: 'post',
            url: '/user/user_list',
            data: {}
        })
    }
}