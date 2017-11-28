import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routers.js';
import store from './store.js';

Vue.use(VueRouter);
//vue-router
const router = new VueRouter({
    routes
});
//vue-router拦截器
router.beforeEach((to, from, next) => {
    let _this = this;
    // 判断是否登录
    if ((!store.state.user_data.username || !store.state.user_data.email) && !store.state.login_status_already) {
        axios.get('/login-status').then(response => {
            let { status, data, message } = response.data;
            if (status) {
                store.commit('setStateValue', { 'login_status_already': true });
                if (Object.keys(data).length > 0) {
                    store.commit('setStateValue', { 'user_data': data.data });
                }
            }
        }).catch(response => {
            console.log('获取用户登录状态失败，未知错误');
        });
    }

    if (to.path == '/') {
        next({
            path: '/index'
        });
        return false;
    }
    next();
});
router.afterEach(() => {
    //NProgress.done(); // 结束Progress
});
export default router;