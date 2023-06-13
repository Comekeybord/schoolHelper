// 二次封装axios
// 导入apiconfig
import config from '../config'
// 导入axios
import axios from 'axios'
// 导入elementplus中的ElMessage模块，处理错误
import { ElMessage } from 'element-plus'


// 配置错误信息
const REQUEST_ERROR = '请求错误，请重试！'

// 创建axios实例对象
const service = axios.create({
    // 配置基础路径
    baseURL: config.baseApi
})

// 配置请求拦截器
service.interceptors.request.use((req) => {
    // 自定义header
    // jwt-token认证会用到
    // console.log(req);
    if (!req.data) req.data = null

    let token = localStorage.getItem('userInfo') ? JSON.parse(localStorage.getItem('userInfo')).token : false; // 从浏览器获取token
    if (token)  // 有token值就把token值加入到请求参数中
    {
        req.data.token = token;
    }

    return req
})

// 配置响应拦截器
service.interceptors.response.use((res) => {
    // console.log(res);
    const { data: response } = res
    // const status
    if (res.status !== 200) {
        // 这里不显示是因为elementplus中新版的自动引入没有引入这个组件的样式
        ElMessage({
            showClose: true,
            message: REQUEST_ERROR,
            type: 'error',
        })
        // 返回promise 的reject
        return Promise.reject(REQUEST_ERROR)
    }
    // 返回获得的数据
    return response
})




// 封装核心函数
const request = (options) => {

    /* 
    options eg:
    {
        methos:get,
        data:{
        
        }
    }
    */
    // 可以在这里配置请求头等
    // console.log(options);
    // console.log(config);

    // 若为get方法，那么把params赋值为data
    options.method = options.method || 'get'
    // console.log(options.method.toLowerCase());
    if (options.method.toLowerCase() == 'get') {
        options.params = options.data
    }
    // 对mock进行处理
    let isMock = config.mock
    if (typeof options.mock !== 'undefined') {
        // 如果已经配置是否用mock，那么就重置为options的mock状态
        isMock = options.mock
    }
    // 对prod线上环境做处理
    if (config.env == 'production') {
        service.defaults.baseURL = config.baseApi
    } else {
        service.defaults.baseURL = isMock ? config.mockApi : config.baseApi
    }

    // 返回请求结果
    return service(options)
}

export default request