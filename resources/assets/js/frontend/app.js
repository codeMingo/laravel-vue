/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
window.Vue = require('vue');
window.axios = require('axios');
import VueRouter from 'vue-router';
import routes from './routers.js';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-default/index.css';
import NProgress from 'nprogress'; // Progress 进度条
import 'nprogress/nprogress.css'; // Progress 进度条 样式
import Vuex from 'vuex';
import plugins from './plugins.js';
import * as filters from './filters.js';

Vue.use(VueRouter);
Vue.use(ElementUI);
Vue.use(Vuex);
Vue.use(plugins);

//注册全局的过滤函数
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key])
});
//vuex
const store = new Vuex.Store({
    state: {
        submitLoading: false,
        menu_active: '1',
        login_status_already: false,
        user_data: {
            username: '',
            email: '',
            face: '',
        },
        article_category: {},
    },
    mutations: {
        setUserData(state, data) {
            state.user_data = data;
        },
        setStateValue(state, data) {
            for(var item in data){
                state[item] = data[item];
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
    let _this = this;

    // 判断是否登录
    if ((!store.state.user_data.username || !store.state.user_data.email) && !store.state.login_status_already) {
        axios.get('/login-status').then(response => {
            let {status, data, message} = response.data;
            if (status) {
                store.commit('setStateValue', {'login_status_already': true});
                if (Object.keys(data).length > 0) {
                    store.commit('setStateValue', {'user_data': data.data});
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

const app = new Vue({
    beforeCreate() {
        window.laravelCsrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
        // 获取首页技术篇菜单
        axios.get('/article-category').then(response => {
            let {status, data, message} = response.data;
            if (status && Object.keys(data).length > 0) {
                store.commit('setStateValue', {'article_category': data.lists});
            }
        }).catch(response => {
            console.log('获取菜单失败，未知错误');
        });
    },
    router,
    store
}).$mount('#app');