// 公共组件
import Main from './components/common/main.vue';
import Register from './components/common/register.vue';
import RegisterActive from './components/common/registerActive.vue';
import Login from './components/common/login.vue';
// 首页
import Index from './components/index/index.vue';
// 视频列表
import Video from './components/video/video.vue';
import VideoDetail from './components/video/videoDetail.vue';
import VideoPlay from './components/video/videoPlay.vue';
// 技术篇
import Article from './components/article/article.vue';
import ArticleDetail from './components/article/articleDetail.vue';
// 留言
import Leave from './components/leave/leave.vue';
export default [{
    path: '/',
    component: Main,
    name: '首页',
    iconCls: '', //图标样式class
    noDropdown: true,
    children: [
        { path: 'index', component: Index, name: '首页', iconCls: 'el-icon-edit' },
        { path: 'register', component: Register, name: '注册页面', iconCls: 'el-icon-edit' },
        { path: 'register-active', component: RegisterActive, name: '邮箱激活页面', iconCls: 'el-icon-edit' },
        { path: 'login', component: Login, name: '登录页面', iconCls: 'el-icon-edit' },
    ]
}, {
    path: '/video',
    component: Main,
    name: '视频列表',
    iconCls: '', //图标样式class
    noDropdown: true,
    children: [
        { path: 'index', component: Video, name: '视频列表', iconCls: '' },
        { path: 'detail', component: VideoDetail, name: '视频详情', iconCls: '' },
        { path: 'play/:id', component: VideoPlay, name: '在线观看', iconCls: '' },
    ]
}, {
    path: '/article',
    component: Main,
    name: '技术篇',
    iconCls: '', //图标样式class
    noDropdown: true,
    children: [
        { path: 'index/:category_id', component: Article, name: '技术篇', iconCls: '' },
        { path: 'index', component: Article, name: '技术篇', iconCls: '' },
        { path: 'detail/:id', component: ArticleDetail, name: '技术篇详情', iconCls: '' }
    ]
}, {
    path: '/leave',
    component: Main,
    name: '留言板',
    iconCls: '', //图标样式class
    noDropdown: true,
    children: [
        { path: 'index', component: Leave, name: '留言板', iconCls: '' },
    ]
}]