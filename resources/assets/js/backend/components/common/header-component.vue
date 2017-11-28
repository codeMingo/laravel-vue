<template>
    <el-row :gutter="20" style="margin: 0px; padding: 0px;">
        <el-menu :default-active="activeIndex" class="el-menu-demo" mode="horizontal" @select="handleSelect">
            <div class="hamburger-container" @click="toggleSidebar"><i class="fa fa-navicon"></i></div>
            <el-breadcrumb class="sidebar-breadcrumb" separator="/">
                <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                <template v-for="item in this.$store.state.breadcrumb">
                    <el-breadcrumb-item :to="{ path: item.path }" v-if="item.path">{{item.text}}</el-breadcrumb-item>
                    <el-breadcrumb-item v-else>{{item.text}}</el-breadcrumb-item>
                </template>
            </el-breadcrumb>
            <!-- 浏览记录，模拟多窗口 -->
            <!-- <div class="sidebar-tag">
                <el-tag v-for="tag in tags" :key="tag.name" :closable="true" :type="tag.type">
                    {{tag.name}}
                </el-tag>
            </div> -->
            <el-submenu index="5" style="float: right;margin-right: 5px;">
                <template slot="title">{{this.$store.state.admin_data.permission_text}}：{{this.$store.state.admin_data.username}}</template>
                <el-menu-item index="5-1">个人中心</el-menu-item>
                <el-menu-item index="5-1" @click="refreshCache">刷新缓存</el-menu-item>
                <el-menu-item index="5-2">设置</el-menu-item>
                <el-menu-item index="5-3" @click="logout">退出</el-menu-item>
            </el-submenu>
        </el-menu>
    </el-row>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.el-menu {
    border-radius: 0;
}

.hamburger-container {
    padding: 0 20px;
    float: left;
    height: 60px;
    line-height: 60px;
    color: #48576A;
    cursor: pointer;
    transform: rotate(0deg);
    transition: .38s;
    transform-origin: 50% 50%;
}

.hamburger-container.is-active {
    transform: rotate(90deg);
}

.hamburger-container:hover {
    color: #59A7FC;
}

.el-submenu .el-menu-item {
    min-width: 0 !important;
}

.sidebar-breadcrumb.el-breadcrumb {
    display: inline-block;
    font-size: 14px;
    height: 60px;
    line-height: 60px;
    margin-left: 10px;
    .no-redirect {
        color: #97a8be;
        cursor: text;
    }
}

.sidebar-tag {
    display: inline-block;
    font-size: 14px;
    height: 60px;
    line-height: 60px;
    margin-left: 10px;
    vertical-align: top;
    margin-left: 10px;
    .el-tag {
        margin-right: 5px;
        cursor: pointer;
    }
}
</style>
<script type="text/javascript">
export default {
    name: 'Sidebar',
    data() {
        return {
            activeIndex: '1',
            tags: [
                { name: '标签一', type: '' },
                { name: '标签二', type: '' },
                { name: '标签三', type: '' },
                { name: '标签六', type: 'primary' }
            ]
        };
    },
    mounted() {
        let _this = this;
        var admin_data = sessionStorage.getItem('admin');
        if (admin_data) {
            _this.admin_data = JSON.parse(admin_data);
        }
    },
    methods: {
        refreshCache() {
            let _this = this;
            axios.post('/api/refreshCache').then(function(res) {
                let { status, data, message } = res.data;
                _this.$message.success(message);
            }).catch(function(err) {
                _this.$message.error('网络连接失败');
            });
        },
        handleSelect(key, keyPath) {
            console.log(key, keyPath);
        },
        logout() {
            let _this = this;
            axios.post('/backend/logout').then(function(res) {
                let { status, message } = res.data;
                if (!status) {
                    _this.$message.error('未知错误，管理员退出失败');
                    return false;
                }
                _this.$message.success(message);
                _this.$router.push({ path: '/login' });
            }).catch(function(err) {
                _this.$message.error('网络连接失败');
            });
        },
        toggleSidebar() {
            this.$store.commit('toggleSidebar');
        }
    }
}
</script>