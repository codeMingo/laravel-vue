/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
window._ = require('lodash');
window.Vue = require('vue');
import './axios';
import store from './vuex';
import router from './router';
import './plugin';
import * as filters from './filter';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-default/index.css';

Vue.use(ElementUI);

//注册全局的过滤函数
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key]);
});

//注入
const app = new Vue({
    beforeCreate() {
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