<template>
    <div class="container">
        <Herader-component></Herader-component>
        <div class="app-main" style="clear: both;">
            <div class="content-container user-index-container">
                <el-row :gutter="30">
                    <el-col :sm="8" :md="6" :lg="6">
                        <div class="user-box">
                            <div class="user-tab list-image-text">
                                <el-row :gutter="10">
                                    <el-col :sm="8" :md="5">
                                        <div class="list-image">
                                            <img v-if="!this.$store.state.user_data.face" src="/images/focus_weixin.png">
                                            <img v-else :src="this.$store.state.user_data.face">
                                        </div>
                                    </el-col>
                                    <el-col :sm="16" :md="19">
                                        <div class="list-text">
                                            <h3>用户名：{{user_main.username}}</h3>
                                            <p>个性签名：{{user_main.sign | defaultValue('未设置')}}</p>
                                        </div>
                                    </el-col>
                                </el-row>
                            </div>
                            <div class="user-tab menu-box">
                                <ul>
                                    <li>
                                        <router-link to="/user/index" :class="menu_active == '/user/index' ? 'active' : ''">我的资料</router-link>
                                    </li>
                                    <li>
                                        <router-link to="/user/collect" :class="menu_active == '/user/collect' ? 'active' : ''">我的收藏</router-link>
                                    </li>
                                    <li>
                                        <router-link to="/user/interactive" :class="menu_active == '/user/interactive' ? 'active' : ''">我的动态</router-link>
                                    </li>
                                    <li>
                                        <router-link to="/user/vip" :class="menu_active == '/user/history' ? 'active' : ''">VIP服务</router-link>
                                    </li>
                                    <li>
                                        <router-link to="/user/history" :class="menu_active == '/user/history' ? 'active' : ''">最近浏览</router-link>
                                    </li>
                                </ul>
                            </div>
                            <div class="user-tab shortcuts-box">
                                <p>上次登录：<span>{{user_main.last_login_time | defaultValue('首次登录')}}</span></p>
                            </div>
                        </div>
                    </el-col>
                    <el-col :sm="16" :md="18" :lg="18">
                        <router-view></router-view>
                    </el-col>
                </el-row>
            </div>
        </div>
        <Footer-component></Footer-component>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.user-index-container {
    .user-box {
        .user-tab {
            margin-bottom: 20px;
        }
        .menu-box {
            border: 1px solid #eee;
            border-radius: 3px;
            overflow: hidden;
            padding: 5px 10px;
            ul {
                width: 100%;
                li {
                    width: 50%;
                    float: left;
                    line-height: 180%;
                    padding: 5px 5px;
                    text-align: center;
                    box-sizing: border-box;
                    a {
                        color: #C1B5B5;
                        border: 1px solid #ccc;
                        display: block;
                        padding: 5px 0;
                    }
                    a.active {
                        color: #9DD9E5;
                        background: #F2FCF9;
                    }
                    a:hover {
                        color: #9DD9E5;
                        background: #F2FCF9;
                    }
                }
            }
        }
    }
}

.list-image-text {
    box-sizing: border-box;
    padding: 5px 8px;
    border: 1px solid #eee;
    border-radius: 3px;
    .list-image {
        img {
            max-width: 100%;
            max-height: 100%;
        }
    }
    .list-text {
        h3 {
            margin-bottom: 3px;
            color: #6B6B6B;
            font-weight: normal;
            font-size: 13px;
        }
        p {
            color: #6B6B6B;
            font-size: 12px;
        }
    }
}
</style>
<script type="text/javascript">
import HeaderComponent from './header-component.vue';
import FooterComponent from './footer-component.vue';
export default {
    components: {
        'Herader-component': HeaderComponent,
        'Footer-component': FooterComponent
    },
    data() {
        return {
            menu_active: this.$route.path,
            user_main: {
                face: '',
                username: '',
                sign: '',
                last_login_time: ''
            }
        };
    },
    mounted() {
        this.getOriginData();
    },
    watch: {
        '$route' (to, from) {
            this.menu_active = this.$route.path;
        }
    },
    methods: {
        getOriginData() {
            let _this = this;
            axios.get('/user/main-show').then(response => {
                let {status, data, message} = response.data;
                _this.user_main = data.list;
            });
        }
    }
}
</script>