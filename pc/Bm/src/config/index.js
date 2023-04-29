/* 
配置环境

在企业级开发中一般有三种环境
1、开发环境
2、测试环境
3、线上环境
*/

// 获取当前开发环境
const env = import.meta.env.MODE || 'prod'

// console.log(import.meta)

// 配置不同环境的api接口
const EnvConfig = {
    // 开发环境
    development: {
        baseApi: '/api',
        mockApi: 'https://www.fastmock.site/mock/8c48e4d7420c41e783485aa84ebac394/api'
    },
    // 测试环境
    test: {
        baseApi: '//test/api',
        mockApi: 'https://www.fastmock.site/mock/8c48e4d7420c41e783485aa84ebac394/api'
    },
    // 生产环境
    pro: {
        baseApi: '//pro/api',
        mockApi: 'https://www.fastmock.site/mock/8c48e4d7420c41e783485aa84ebac394/api'
    }
}

// 导出配置
export default {
    // 开发模式
    env,
    // mock总开关
    mock: true,
    // 导出对应api
    ...EnvConfig[env]
}