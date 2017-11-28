/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap.js');
import store from './store.js';
import router from './router.js';
import plugins from './plugins.js';
import * as filters from './filters.js';
import ElementUI from 'element-ui';
import NProgress from 'nprogress'; 
import 'nprogress/nprogress.css'; 
import 'element-ui/lib/theme-default/index.css';

Vue.use(ElementUI);
Vue.use(plugins);

//注册全局的过滤函数
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key])
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
        // 获取首页技术篇菜单
        axios.get('/article-category').then(response => {
            let { status, data, message } = response.data;
            if (status && Object.keys(data).length > 0) {
                store.commit('setStateValue', { 'article_category': data.lists });
            }
        }).catch(response => {
            console.log('获取菜单失败，未知错误');
        });
    },
    router,
    store
}).$mount('#app');