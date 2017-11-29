import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        is_login: false,
        submitLoading: false,

        // 面包屑{{path: '', text: ''}, {path: '', text: ''}}
        breadcrumb: [
            /*{path: '/home', text: '測試1'},
            {path: '', text: '测试2'},*/
        ],

        // 管理员登录信息
        admin_data: {
            username: '',
            permission_text: ''
        },

        // sidebar的class
        sidebarCollapse: false,
        sidebarMainContainerClass: '',
        sidebarWrapperClass: '',
    },
    mutations: {
        toggleSidebar(state) { // sidebar状态切换
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
        },
        setAdminData(state, data) {
            state.admin_data = data;
        },
        changeBreadcrumb(state, data) {
            state.breadcrumb = data;
        },
        setStateValue(state, data) {
            for (var item in data) {
                state[item] = data[item];
            }
        }
    }
});

// 获取登录信息
axios.get('/backend/login-status').then(response => {
    let { status, data, message } = response.data;
    if (status && Object.keys(data).length > 0) {
        store.commit('setStateValue', { 'is_login': true, 'user_data': data.list });
    }
});

export default store;