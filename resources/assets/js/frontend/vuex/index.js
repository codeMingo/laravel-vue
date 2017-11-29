import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        menu_active: '1',
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
            for(var item in data){
                state[item] = data[item];
            }
        }
    }
});

// 判断是否登录
if (!store.state.user_data.is_login) {
    axios.get('/login-status').then(response => {
        let { status, data, message } = response.data;
        if (status && Object.keys(data).length > 0) {
            store.commit('setStateValue', { 'is_login': true, 'user_data': data.list});
        }
    });
}
export default store;