import request from "./request";


export default {
    // 统一编写请求接口

    // 登录
    login(data) {
        return request({
            method: 'post',
            url: '/login/login',
            data,
        })
    },

    // 获取用户列表
    getUserList(data) {
        return request({
            method: 'post',
            url: '/user/user_list',
            data
        })
    },

    // 搜索用户
    userSearch(data) {
        return request({
            method: 'post',
            url: '/user/user_search',
            data
        })
    },

    // 删除用户
    userDelete(data) {
        return request({
            method: 'post',
            url: '/user/user_del',
            data
        })
    },

    // 添加，编辑用户
    addUser(data) {
        return request({
            method: 'post',
            url: '/user/user_add',
            data
        })
    },

    // 获取用户组列表
    getUserGroup(data) {
        return request({
            method: 'post',
            url: '/UserGroup/group_list',
            data
        })
    }
}