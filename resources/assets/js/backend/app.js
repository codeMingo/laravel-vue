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

// vuex
import Vuex from 'vuex';

// vue插件
import plugins from './plugins.js';

// vue过滤函数
import * as filters from './filters.js';

Vue.use(VueRouter);
Vue.use(ElementUI);
Vue.use(Vuex);
Vue.use(plugins);

//注册全局的过滤函数
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key]);
});

//vuex
const store = new Vuex.Store({
    state: {
        submitLoading: false,

        // sidebar的class
        sidebarCollapse: false,
        sidebarMainContainerClass: '',
        sidebarWrapperClass: '',
    },
    mutations: {
        toggleSidebar(state) {  // sidebar状态切换
            state.sidebarCollapse = !state.sidebarCollapse;
            if (state.sidebarCollapse) {
                state.sidebarMainContainerClass = 'main-container-toggle';
                state.sidebarWrapperClass = 'sidebar-wrapper-toggle';
                sessionStorage.setItem('sidebarCollapse', 1);
            } else {
                state.sidebarMainContainerClass = '';
                state.sidebarWrapperClass = '';
                sessionStorage.removeItem('sidebarCollapse');
            }
        }
    }
});

//vue-router
const router = new VueRouter({
    routes
});

//vue-router拦截器
router.beforeEach((to, from, next) => {
    if (!sessionStorage.getItem('admin') && to.path != '/login') {
        next({
            path: '/login'
        });
        return false;
    }
    if (to.path == '/login') {
        sessionStorage.removeItem('admin');
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

window.laravelCsrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
//注入
const app = new Vue({
    beforeCreate() {
        // 记忆sidebar是否收缩
        if (sessionStorage.getItem('sidebarCollapse')) {
            this.$store.state.sidebarCollapse = true;
            this.$store.state.sidebarMainContainerClass = 'main-container-toggle';
            this.$store.state.sidebarWrapperClass = 'sidebar-wrapper-toggle';
        }
    },
    router,
    store
}).$mount('#app');