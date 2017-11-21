<template>
    <div class="app-container">
        <el-form ref="form" :model="form" label-width="100px">
            <el-form-item label="权限名称">
                <el-input v-model="form.text"></el-input>
            </el-form-item>
            <el-form-item label="权限节点">
                <!-- <el-tree :data="permission_data" show-checkbox node-key="menu_id" ref="tree" :default-checked-keys="[]" :props="defaultProps"></el-tree> -->
                <el-collapse>
                    <el-collapse-item v-for="item in testData" :key="item.id">
                        <template slot="title">
                            <label name="">
                                <input type="checkbox" name="" value="item.id">{{item.name}}</label>
                        </template>
                        <div>
                            <span><input type="checkbox" name="">新增</span>
                            <span><input type="checkbox" name="">删除</span>
                            <span><input type="checkbox" name="">新增</span>
                            <span><input type="checkbox" name="">删除</span>
                            <span><input type="checkbox" name="">新增</span>
                            <span><input type="checkbox" name="">删除</span>
                            <span><input type="checkbox" name="">新增</span>
                            <span><input type="checkbox" name="">删除</span>
                        </div>
                    </el-collapse-item>
                </el-collapse>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="toLink('/admin/permissions')">返回</el-button>
                <el-button type="primary" @click="submit">保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<style lang="scss" scoped="" type="text/css">
</style>
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
            form: {},
            testData: []
        }
    },
    mounted() {
        this.getList();
    },
    methods: {
        getList() {
            let _this = this;
            get('/backend/permissions/menus-list').then(data => {
                console.log(data.data);
                _this.testData = data.data;
            })
        },
        submit() {
            let _this = this;
            _this.form.include_menus = _this.$refs.tree.getCheckedKeys();
            var method = 'post',
                url = '/backend/permissions',
                params = { 'data': _this.form };
            save(method, url, params).then(data => {
                if (!data.status) {
                    _this.$message.error(data.message);
                    return false;
                }
                _this.$message.success(data.message);
                _this.toLink('/admins/permissions');
            });
        },
        toLink(url) {
            this.$router.push({ path: url });
        }
    }
}
</script>