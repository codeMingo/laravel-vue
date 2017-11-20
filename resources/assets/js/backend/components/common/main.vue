<template>
    <div class="container">
        <el-row :gutter="20" style="margin: 0px; padding: 0px;">
            <div class="sidebar-wrapper" :class="$store.state.sidebarWrapperClass">
                <Sidebar-component class="sidebar-container"></Sidebar-component>
            </div>
            <div class="main-container" :class="$store.state.sidebarMainContainerClass">
                <Header-component></Header-component>
                <div class="app-main">
                    <router-view v-on:changePublicComponent="changePublicComponent"></router-view>
                </div>
                <Public-component ref="childComponent" v-show="visitComponent"></Public-component>
            </div>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
</style>
<script type="text/javascript">
import HeaderComponent from './header-component.vue';
import SidebarComponent from './sidebar-component.vue';
import PublicComponent from './public-component.vue';
export default {
    name: 'main',
    components: {
        HeaderComponent,
        SidebarComponent,
        PublicComponent
    },
    data() {
        return {
            visitComponent: false
        };
    },
    mounted() {
        let _this = this;
        let body = document.querySelector('body');
        body.addEventListener('click', (event) => {
            if (_this.visitComponent) {
                let flag = false;
                event.path.forEach(item => {
                    if (item.id === 'shortcut') {
                        flag = true;
                        return false;
                    }
                })
                if (flag) {
                    _this.visitComponent = true;
                } else {
                    _this.visitComponent = false;
                }
            }
        }, false)
    },
    methods: {
        changePublicComponent(name, event, data) {
            let targetElementRect = event.target.getBoundingClientRect();
            let left_x = targetElementRect.left;
            let screen_y = document.body.offsetHeight;
            let origin_y = targetElementRect.top + event.target.clientHeight;
            let origin_b_y = targetElementRect.top;
            let top_y = screen_y - origin_y > origin_b_y ? origin_y : origin_b_y - 300;
            this.visitComponent = true;
            this.$refs.childComponent.currentComponent(name, left_x, top_y);
            this.$refs.childComponent.shortcutDataDetail = data;
        }
    }
}
</script>