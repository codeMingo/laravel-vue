/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
window.Vue = require('vue');
require('./bootstrap.js');
import store from './vuex';
import router from './router';
import './plugin';
import * as filters from './filter';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-default/index.css';

Vue.use(ElementUI);

//注册全局的过滤函数
Object.keys(filters).forEach(key => {
    Vue.filter(key, filters[key])
});

const app = new Vue({
    beforeCreate() {},
    router,
    store
}).$mount('#app');