// 封装加载组件
import { ElLoading } from 'element-plus'

let loading;

export default {
    // 此时的参数 el 是 加载服务的标签
    showLoading(el) {
        loading = ElLoading.service({
            target: el,
            lock: true,
            text: '加载中',
            background: 'rgba(0, 0, 0, 0.7)',
        })
    },
    // 关闭
    hideLoading() {
        loading.close()
    }
}