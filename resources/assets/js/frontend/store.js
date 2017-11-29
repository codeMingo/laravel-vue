// vue
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

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
export default store;