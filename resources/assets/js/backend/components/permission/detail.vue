<template>
    <div class="app-container">
        <el-form ref="form" :model="form" label-width="100px">
            <el-form-item label="权限名称">
                <el-input v-model="form.text"></el-input>
            </el-form-item>
            <el-form-item label="权限名称">
                <el-tree :data="permission_data" show-checkbox node-key="menu_id" ref="tree" :default-checked-keys="form.include_menus" :props="defaultProps">
                </el-tree>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="toLink('/admin/permissions')">返回</el-button>
                <el-button type="primary" @click="submit">保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script type="text/javascript">
export default {
    data() {
        return {
            permission_data: this.$router.options.routes,
            defaultProps: {
                children: 'children',
                label: 'name'
            },
            include_menus: [],
            form: {}
        }
    },
    created() {
    },
    methods: {

    }
}
</script>