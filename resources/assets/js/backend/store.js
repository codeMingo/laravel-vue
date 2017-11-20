// vue
import Vue from 'vue';

// vuex
import Vuex from 'vuex';


Vue.use(Vuex);


const store = new Vuex.Store({
    state: {
        submitLoading: false,

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
            state.adminData = data;
        }
    }
});
export default store;