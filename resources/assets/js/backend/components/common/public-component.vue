<template>
    <div class="public-container">
        <component :dataDetail="shortcutDataDetail" v-bind:is="current_component" v-bind:style="{ left: current_left + 'px', top: current_top + 'px' }"></component>
    </div>
</template>
<script type="text/javascript">
    export default {
        data() {
            return {
                current_component: '',
                current_left: '',
                current_top: '',
                shortcutDataDetail: {}
            }
        },
        methods: {
            currentComponent(name, left, top) {
              this.current_top = top;
              this.current_left = left;
              Vue.component(name, function(resolve) {
                  require(['../common/shortcut-component.vue'], resolve);
              });
              this.current_component = name;
            }
        }
    }
</script>