// 公共
import Login from './components/common/login.vue';
import Main from './components/common/main.vue';

// 首页
import Index from './components/index/index.vue';

// 管理员管理
import Lists from './components/admin/lists.vue';
import Permission from './components/admin/permissions.vue';
import Permission_detail from './components/index/index.vue';
import Permission_create from './components/index/index.vue';

// 用户管理
import UserLists from './components/user/lists.vue';
import UserDetail from './components/user/detail.vue';
import UserNoActive from './components/user/noActive.vue';

// 文章管理
import ArticleLists from './components/article/lists.vue';
import ArticleCreate from './components/article/create.vue';

export default [{
        path: '/login',
        component: Login,
        name: '登录',
        hidden: true,
    }, {
        path: '/',
        component: Main,
        hidden: true
    }, {
        path: '/index',
        component: Main,
        name: '首页',
        iconCls: 'fa fa-television', //图标样式class
        noDropdown: true,
        children: [
            { path: 'index', component: Index, name: '首页', iconCls: 'el-icon-edit' }
        ]
    }, {
        path: '/admin',
        component: Main,
        name: '管理员管理',
        iconCls: 'fa fa-user-circle', //图标样式class
        children: [
            { path: 'lists', component: Lists, name: '管理员列表' },
            { path: 'permission', component: Permission, name: '管理员权限' },
            { path: 'permission/detail/:id', component: Permission_detail, name: '权限详情', hidden: true },
            { path: 'permission/create', component: Permission_create, name: '新增权限', hidden: true }
        ]
    },
    {
        path: '/user',
        component: Main,
        name: '用户管理',
        iconCls: 'fa fa-user-o', //图标样式class
        menu_id: 4,
        meta: { menu_id: 4 },
        children: [
            { path: 'index', component: UserLists, name: '用户列表' },
            { path: 'no-acitve', component: UserNoActive, name: '未激活列表' },
            { path: 'table', component: Main, name: '可疑用户' },
            { path: 'table', component: Main, name: '冻结用户' },
        ]
    },
    {
        path: '/article',
        component: Main,
        name: '文章管理',
        iconCls: 'fa fa-file-code-o', //图标样式class
        menu_id: 5,
        meta: { menu_id: 5 },
        children: [
            { path: 'index', component: ArticleLists, name: '文章列表' },
            { path: 'create', component: ArticleCreate, name: '新增文章', hidden: true },
        ]
    }
]