/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
window.Vue = require('vue');

// axios
window.axios = require('axios');

// vue-router
import VueRouter from 'vue-router';

// vue的router规则
import routes from './routers.js';

// elementui
import ElementUI from 'element-ui';

// elementui的css
import 'element-ui/lib/theme-default/index.css';

// Progress 进度条
import NProgress from 'nprogress';

// Progress 进度条 样式
import 'nprogress/nprogress.css';

// vue插件
import plugins from './plugins.js';

// vue过滤函数
import * as filters from './filters.js';

// vuex
import store from './store.js';

Vue.use(VueRouter);
Vue.use(ElementUI);
Vue.use(plugins);

//注册全局的过滤函数
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key]);
});


//vue-router
const router = new VueRouter({
    routes
});

//vue-router拦截器
router.beforeEach((to, from, next) => {
    // 判断是否登录
    let _this = this;
    if ((!store.state.adminData.username || !store.state.adminData.permission_text) && to.path != '/login') {
        axios.get('/backend/login-status').then(response => {
            let { status, data, message } = response.data;
            if (status && Object.keys(data).length > 0) {
                store.commit('setAdminData', data.data);
                next();
            } else {
                next({
                    path: '/login'
                });
            }
        }).catch(response => {
            console.log('未知错误');
        });
        return false;
    }

    if (to.path == '/login') {
        store.commit('setAdminData', {
            username: '',
            permission_text: ''
        });
    }
    next();
});
router.afterEach(() => {

});

//axios拦截器
axios.interceptors.request.use(function(config) {
    NProgress.start();
    return config;
}, function(error) {
    return Promise.reject(error);
});
axios.interceptors.response.use(function(response) {
    NProgress.done();
    return response;
}, function(error) {
    return Promise.reject(error);
});

//注入
const app = new Vue({
    beforeCreate() {
        window.laravelCsrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');

        // 记忆sidebar是否收缩
        if (sessionStorage.getItem('sidebarCollapse')) {
            store.state.sidebarCollapse = true;
            store.state.sidebarMainContainerClass = 'main-container-toggle';
            store.state.sidebarWrapperClass = 'sidebar-wrapper-toggle';
        }
    },
    router,
    store
}).$mount('#app');