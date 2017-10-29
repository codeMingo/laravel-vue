<template>
    <div class="web-header">
        <el-row :gutter="10" style="margin: 0;">
            <el-col :xs="6" :sm="6" :md="6" :lg="6">
                <div class="web-logo">
                    <router-link to="/">quickzz博客</router-link>
                </div>
            </el-col>
            <el-col :xs="18" :sm="18" :md="18" :lg="18">
                <div class="web-menu">
                    <el-menu theme="dark" :default-active="$store.state.menu_active" class="el-menu-demo" mode="horizontal" @select="menuSelect">
                        <el-menu-item index="1">
                            <router-link to="/" class='menu-link'>首页</router-link>
                        </el-menu-item>
                        <el-menu-item index="2">
                            <router-link to="/video/index" class='menu-link'>视频列表</router-link>
                        </el-menu-item>
                        <el-submenu index="3">
                            <template slot="title">技术篇</template>
                            <template v-for="(item, index) in $store.state.article_category">
                                <el-menu-item :index="'3-' +index">
                                    <router-link :to="{ path: '/article/index/' + item.id }">{{item.category_name}}</router-link>
                                </el-menu-item>
                            </template>
                        </el-submenu>
                        <el-menu-item index="4">
                            <router-link to="/vote/index" class='menu-link'>投票</router-link>
                        </el-menu-item>
                        <el-menu-item index="5">
                            <router-link to="/leave/index" class='menu-link'>留言板</router-link>
                        </el-menu-item>
                        <template v-if="this.$store.state.user_data.username">
                            <el-submenu index="6" class="user-menu">
                                <template slot="title"><img :src="this.$store.state.user_data.face" class="user-face">{{this.$store.state.user_data.username | subString(0, 5)}}</template>
                                <el-menu-item index="6-1">
                                    <router-link to="/user/index">个人中心</router-link>
                                </el-menu-item>
                                <el-menu-item index="6-2">
                                    <router-link to="/user/collect">我的收藏</router-link>
                                </el-menu-item>
                                <el-menu-item index="6-3">
                                    <router-link to="/user/vip">VIP服务</router-link>
                                </el-menu-item>
                                <el-menu-item index="6-4" @click="logout" class="link-logout">退出</el-menu-item>
                            </el-submenu>
                        </template>
                        <template v-else>
                            <el-menu-item index="6">
                                <router-link to="/login" class='menu-link'>登录</router-link>
                            </el-menu-item>
                            <el-menu-item index="7">
                                <router-link to="/register" class='menu-link'>注册</router-link>
                            </el-menu-item>
                        </template>
                    </el-menu>
                </div>
                <div class="web-search">
                    <el-input placeholder="请输入内容" v-model="searchContent">
                        <el-button slot="append" icon="search"></el-button>
                    </el-input>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.web-header {
    .user-menu.el-submenu .el-menu-item {
        min-width: 0px !important;
    }
    background-color: #324157;
    .el-menu {
        .el-menu-item {
            padding: 0;
            .menu-link {
                display: block;
            }
            a {
                display: block;
                padding: 0 20px;
            }
        }
        .user-face {
            width: 40px;
            height: 40px;
            border-radius: 3px;
            border: 1px solid #ccc;
            padding: 1px;
            margin-right: 5px;
            overflow: hidden;
        }
        .link-logout {
            text-indent: 20px;
        }
        .el-menu a {
            box-sizing: border-box;
        }
    }
    .web-logo {
        margin-left: 30px;
        font-size: 20px;
        a {
            height: 60px;
            line-height: 60px;
            display: inline-block;
            padding: 0 20px;
            color: #D3DCE6;
        }
    }
    .web-search {
        height: 60px;
        line-height: 60px;
        float: right;
    }
    .web-menu {
        float: right;
    }
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            menuDefaultActive: '1',
            searchContent: '',
            searchSelect: '',
        };
    },
    mounted() {

    },
    methods: {
        menuSelect(key, keyPath) {

        },
        logout() {
            let _this = this;
            axios.post('/logout').then(function(res) {
                let { status, message } = res.data;
                if (!status) {
                    _this.$message.error('未知错误，用户退出失败');
                    return false;
                }
                _this.$store.commit('setSateValue', 'user_data', { username: '', email: '', face: '' });
                _this.$message.success(message);
                _this.$router.push({ path: '/index' });
            }).catch(function(err) {
                _this.$message.error('网络连接失败');
            });
        }
    }
}
</script>