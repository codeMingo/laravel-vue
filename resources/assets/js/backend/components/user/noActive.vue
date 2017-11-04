<template>
    <div class="app-container">
        <table-header-component v-on:create="create" v-on:getList="getList">
            <el-input v-model="searchForm.username" placeholder="请输入用户名" style="width: 200px;"></el-input>
            <el-input v-model="searchForm.email" placeholder="请输入电子邮箱" style="width: 200px;"></el-input>
            <el-select v-model="searchForm.status" placeholder="请选择状态">
                <el-option label="全部" value=""></el-option>
                <el-option v-for="item in options.statusOptions" :key="item.value" :label="item.text" :value="item.value"></el-option>
            </el-select>
        </table-header-component>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column prop="username" label="用户名"></el-table-column>
            <el-table-column prop="email" label="电子邮件"></el-table-column>
            <el-table-column prop="last_login_ip" label="最后登录ip"></el-table-column>
            <el-table-column prop="last_login_time" label="最后登录时间"></el-table-column>
            <el-table-column prop="active" label="是否激活" :formatter="formatActive"></el-table-column>
            <el-table-column prop="status" label="状态" :formatter="formatStatus"></el-table-column>
            <el-table-column align="center" label="操作" width="250">
                <template slot-scope="scope">
                    <el-button size="small" type="info" @click="toLink('/user/detail/' + scope.row.id)">查看详情</el-button>
                    <el-button size="small" type="success" @click="modify(scope.row.id)">编辑</el-button>
                    <el-button size="small" type="danger" @click="trashed(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination-component ref="pagination" v-on:getList="getList"></pagination-component>
        <el-dialog :title="formTitle" :visible.sync="formVisible" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form class="small-space" :model="form" :rules="rules" ref="form" label-position="left" label-width="100px">
                <input type="hidden" v-model="form.id">
                <el-form-item label="用户名" prop="username">
                    <el-input v-model="form.username" placeholder="登录账号，2-15个字符"></el-input>
                </el-form-item>
                <el-form-item label="电子邮件" prop="email">
                    <el-input v-model="form.email" placeholder="电子邮件，使用常用的邮箱"></el-input>
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input type="password" v-model="form.password" placeholder="登录密码，6-30个字符"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="repassword">
                    <el-input type="password" v-model="form.repassword" placeholder="再次输入密码"></el-input>
                </el-form-item>
                <el-form-item label="是否激活" prop="active">
                    <el-select v-model="form.active" placeholder="请选择用户是否激活">
                        <el-option v-for="item in options.activeOptions" :key="item.value" :label="item.text" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select v-model="form.status" placeholder="请选择用户状态">
                        <el-option v-for="item in options.statusOptions" :key="item.value" :label="item.text" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <dialog-footer-component v-on:submit="submit(formName = 'form')" v-on:close="close"></dialog-footer-component>
        </el-dialog>
    </div>
</template>
<script>
import PaginationComponent from '../common/pagination-component.vue';
import TableHeaderComponent from '../common/table-header-component.vue';
import DialogFooterComponent from '../common/dialog-footer-component.vue';
export default {
    components: {
        'pagination-component': PaginationComponent,
        'table-header-component': TableHeaderComponent,
        'dialog-footer-component': DialogFooterComponent
    },
    data() {
        var checkRepassword = (rule, value, callback) => {
            if (value !== this.form.password) {
                callback(new Error('密码输入不一致!'));
            } else {
                callback();
            }
        };
        return {
            formTitle: '',
            formVisible: false,
            tableData: [],
            form: {
                id: '',
                username: '',
                email: '',
                password: '',
                repassword: '',
                active: '',
                status: ''
            },
            searchForm: {
                username: '',
                email: '',
                status: '',
                active: 0
            },
            options: {
                statusOptions: '',
                activeOptions: ''
            },
            rules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 2, max: 15, message: '长度在 2 到 15 个字符', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: '请输入登录邮箱', trigger: 'blur' },
                    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur,change' }
                ],
                active: [
                    { required: true, message: '请选择用户是否激活', trigger: 'blur' },
                ],
                status: [
                    { required: true, message: '请选择用户状态', trigger: 'blur' },
                ]
            },
            passwordRules: [
                { required: true, message: '请输入登录密码', trigger: 'blur' },
                { min: 6, max: 30, message: '长度在 6 到 30 个字符', trigger: 'blur' }
            ],
            repasswordRules: [
                { required: true, message: '请再次输入密码', trigger: 'blur' },
                { validator: checkRepassword, trigger: 'blur' }
            ],
        }
    },
    mounted() {
        this.getList();
    },
    methods: {
        getList() {
            let _this = this;
            let params = { 'data': { 'searchForm': _this.searchForm } };
            get('/backend/users?page=' + _this.$refs.pagination.pageData.current_page, params).then(data => {
                _this.tableData = data.data.lists.data;
                _this.options.statusOptions = data.data.statusOptions;
                _this.options.activeOptions = data.data.activeOptions;
                _this.$refs.pagination.pageData.per_page = parseInt(data.data.lists.per_page);
                _this.$refs.pagination.pageData.current_page = parseInt(data.data.lists.current_page);
                _this.$refs.pagination.pageData.total = parseInt(data.data.lists.total);
            })
        },
        modify(id) {
            let _this = this;
            _this.formTitle = '修改';
            delete _this.rules.password;
            delete _this.rules.repassword;
            _this.tableData.forEach(function(item) {
                if (item.id === id) {
                    item.password = '';
                    _this.form = Vue.copyObj(item);
                    _this.formVisible = true;
                    return true;
                }
            });
        },
        submit(formName) {
            let _this = this;
            _this.$refs[formName].validate((valid) => {
                if (valid) {
                    _this.$store.state.submitLoading = true;
                    if (!_this.form.id) {
                        var method = 'post',
                            url = '/backend/users',
                            params = { 'data': _this.form };
                    } else {
                        var method = 'put',
                            url = '/backend/users/' + _this.form.id,
                            params = { 'data': _this.form };
                    }
                    save(method, url, params).then(data => {
                        _this.$store.state.submitLoading = false;
                        if (!data.status) {
                            _this.$message.error(data.message);
                            return false;
                        }
                        _this.$message.success(data.message);
                        _this.formVisible = false;
                        _this.getList();
                    });
                }
            });
        },
        trashed(id) {
            let _this = this;
            _this.$confirm('确定删除这个用户吗').then(() => {
                trashed('/backend/users/' + id).then(data => {
                    _this.$message.success(data.message);
                    Vue.removeOneData(_this.tableData, id);
                });
            });
        },
        close() {
            this.formVisible = false;
            Vue.resetForm(this.form);
            this.$refs.form.resetFields();
        },
        create() {
            this.formTitle = '添加用户';
            if (!this.rules.password) {
                this.rules.password = this.passwordRules;
            }
            if (!this.rules.repassword) {
                this.rules.repassword = this.repasswordRules;
            }
            this.formVisible = true;
        },
        formatStatus(row) {
            let text = '-';
            this.options.statusOptions.forEach(function(item) {
                if (row.status == item.value) {
                    return text = item.text;
                }
            });
            return text;
        },
        formatActive(row) {
            let text = '-';
            this.options.activeOptions.forEach(function(item) {
                if (row.active == item.value) {
                    return text = item.text;
                }
            });
            return text;
        },
        toLink(url) {
            this.$router.push({ path: url });
        }
    }
}
</script>