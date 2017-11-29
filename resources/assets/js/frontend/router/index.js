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
    if (to.path == '/') {
        next({ path: '/index' });
        return false;
    }
    if (!canVisit(to)) {
        next({ path: '/404' });
        return false;
    }
    next();
});
router.afterEach(() => {

});
export default router;