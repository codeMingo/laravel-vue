import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        is_login: false,
        user_data: {
            username: '',
            email: '',
            face: '',
        },
        article_category: {},
    },
    mutations: {
        setStateValue(state, data) {
            for (var item in data) {
                state[item] = data[item];
            }
        }
    }
});


// 获取登录信息
axios.get('/login-status').then(response => {
    let { status, data, message } = response.data;
    if (status && Object.keys(data).length > 0) {
        store.commit('setStateValue', { 'is_login': true, 'user_data': data.list });
    }
});


// 获取菜单
axios.get('/article-category').then(response => {
    let { status, data, message } = response.data;
    if (status && Object.keys(data).length > 0) {
        store.commit('setStateValue', { 'article_category': data.lists });
    }
});
export default store;