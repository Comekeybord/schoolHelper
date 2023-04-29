import request from "./request";


export default {
    // 统一编写请求接口

    // 登录
    login(data) {
        return request({
            method: 'post',
            url: '/login',
            data,
        })
    }
}