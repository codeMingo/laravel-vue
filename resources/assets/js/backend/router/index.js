import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routers.js';
import store from '../vuex';

Vue.use(VueRouter);
//vue-router
const router = new VueRouter({
    routes
});

// 访问权限
function canVisit(to) {
    return true;
}

//vue-router拦截器
router.beforeEach((to, from, next) => {
    if (to.path == '/login') {
        store.commit('setStateValue', { 'is_login': false, 'admin_data': { username: '', permission_text: '' } });
        next();
        return false;
    }
    if (!store.state.is_login) {
        next({ path: '/login' });
        return false;
    }
    next();
});
router.afterEach((to, from, next) => {
    // 获取面包屑
    let breadcrumb_data = [
        { path: to.path, text: to.name }
    ];
    store.commit('changeBreadcrumb', breadcrumb_data);
});
export default router;