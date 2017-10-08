<template>
    <div class="container">
        <el-row :gutter="20" style="margin: 0px; padding: 0px;">
            <div class="sidebar-wrapper" :class="$store.state.sidebarWrapperClass">
                <Sidebar-component class="sidebar-container"></Sidebar-component>
            </div>
            <div class="main-container" :class="$store.state.sidebarMainContainerClass">
                <Herader-component></Herader-component>
                <div class="app-main">
                    <router-view></router-view>
                </div>
            </div>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
</style>
<script type="text/javascript">
import HeaderComponent from './header-component.vue';
import sidebarComponent from './sidebar-component.vue';
export default {
    name: 'main',
    components: {
        'Herader-component': HeaderComponent,
        'Sidebar-component': sidebarComponent
    },
    computed: {

    }
}
</script>