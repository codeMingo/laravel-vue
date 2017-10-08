<template>
    <el-menu :unique-opened='true' mode="vertical" theme="dark" :default-active="$route.path" class="el-menu-vertical-demo" :collapse="$store.state.sidebarCollapse">
        <template v-for="item in $router.options.routes" v-if="!item.hidden">
            <el-submenu :index="item.name" v-if="!item.noDropdown || $store.state.sidebarCollapse">
                <template slot="title">
                    <i :class="item.iconCls"></i>&nbsp;&nbsp;&nbsp;
                    <span slot="title">{{item.name}}</span>
                </template>
                <router-link v-for="child in item.children" :key="child.path" v-if="!child.hidden" class="title-link" :to="item.path+'/'+child.path">
                    <el-menu-item :index="item.path+'/'+child.path">
                        {{child.name}}
                    </el-menu-item>
                </router-link>
            </el-submenu>
            <router-link v-if="item.noDropdown && item.children.length > 0 && !$store.state.sidebarCollapse" :to="item.path+'/'+item.children[0].path">
                <el-menu-item :index="item.path+'/'+item.children[0].path">
                    <template slot="title">
                        <i :class="item.iconCls"></i>&nbsp;&nbsp;&nbsp;
                        <span slot="title">{{item.children[0].name}}</span>
                    </template>
                </el-menu-item>
            </router-link>
        </template>
    </el-menu>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.el-menu {
    min-height: 100%;
    border-radius: 0;
}

.el-submenu .el-menu-item:active,
.el-submenu .el-menu-item:hover {
    text-decoration: none;
}

.el-submenu.is-active .el-submenu__title i {
    color: #20a0ff;
}

.wscn-icon {
    margin-right: 10px;
}

.hideSidebar .title-link {
    display: inline-block;
    padding-left: 10px;
}

.el-menu-vertical-demo:not(.el-menu--collapse) {
    width: 180px;
    min-height: 100%;
}

.el-submenu .el-menu-item {
    min-width: 0;
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            permission_routers: ''
        };
    },
    mounted() {

    },
    methods: {
        getMenu: function() {
            axios.get('backend/menu-list').then(function(res) {
                permission_routers = res.data.lists;
            }).catch(function(res) {

            });
        }
    }
}
</script>