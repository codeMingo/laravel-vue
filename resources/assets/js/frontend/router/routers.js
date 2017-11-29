
const _import = file => () => import('@/views/' + file + '.vue')
// 公共组件
import Main from './components/common/main.vue';
import Register from './components/common/register.vue';
import RegisterActive from './components/common/registerActive.vue';
import Login from './components/common/login.vue';

// 首页
import Index from './components/index/index.vue';

// 视频列表
import Video from './components/video/index.vue';
import VideoDetail from './components/video/detail.vue';
import VideoPlay from './components/video/play.vue';

// 技术篇
import Article from './components/article/index.vue';
import ArticleDetail from './components/article/detail.vue';

// 留言
import Leave from './components/leave/index.vue';

// 用户模块
import User from './components/common/user.vue';
import UserIndex from './components/user/index.vue';
import UserCollect from './components/user/collect.vue';
import UserInteractive from './components/user/interactive.vue';

export default [{
    path: '/',
    component: Main,
    name: '首页',
    children: [
        { path: 'index', component: Index, name: '首页' },
        { path: 'register', component: Register, name: '注册页面' },
        { path: 'register-active', component: RegisterActive, name: '邮箱激活页面' },
        { path: 'login', component: Login, name: '登录页面' },
    ]
}, {
    path: '/video',
    component: Main,
    name: '视频列表',
    children: [
        { path: 'index', component: Video, name: '视频列表' },
        { path: 'detail', component: VideoDetail, name: '视频详情' },
        { path: 'play/:id', component: VideoPlay, name: '在线观看' },
    ]
}, {
    path: '/article',
    component: Main,
    name: '技术篇',
    children: [
        { path: 'index/:category_id', component: Article, name: '技术篇' },
        { path: 'index', component: Article, name: '技术篇' },
        { path: 'detail/:id', component: ArticleDetail, name: '技术篇详情' }
    ]
}, {
    path: '/leave',
    component: Main,
    name: '留言板',
    children: [
        { path: 'index', component: Leave, name: '留言板' },
    ]
}, {
    path: '/user',
    component: User,
    name: '用户中心',
    children: [
        { path: 'index', component: UserIndex, name: '个人中心', s_login: true },
        { path: 'collect', component: UserCollect, name: '我的收藏', s_login: true },
        { path: 'interactive', component: UserInteractive, name: '我的动态', s_login: true },
    ]
}];