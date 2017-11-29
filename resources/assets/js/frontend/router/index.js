import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routers.js';
import store from '../vuex';

Vue.use(VueRouter);
//vue-router
const router = new VueRouter({
    routes
});

//vue-router拦截器
router.beforeEach((to, from, next) => {
    if (to.path == '/') {
        next({
            path: '/index'
        });
        return false;
    }
    next();
});
router.afterEach(() => {

});
export default router;