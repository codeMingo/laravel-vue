<template>
    <div class="app-container">
        <el-row class="filter-container">
            <el-input v-model="search_form.username" class="search-input" placeholder="请输入账户名"></el-input>
            <el-select v-model="search_form.permission_id" placeholder="请选择管理员等级">
                <el-option label="全部权限" value=""></el-option>
                <el-option v-for="item in options.permission" :key="item.id" :label="item.text" :value="item.id"></el-option>
            </el-select>
            <el-button type="primary" icon="search" @click="getLists">搜索</el-button>
            <el-button type="primary" icon="plus" @click="create">添加</el-button>
        </el-row>
        <!-- 用户快捷窗口 -->
        <shortcut-component ref="describtion"></shortcut-component>
        <el-table :data="tableData" border style="width: 100%">
            <el-table-column label="用户名" class-name="am-link-target-td">
                <template slot-scope="scope">
                    <router-link to="/home" :class="scope.row.status ? 'highlight-link' : 'disabled'">{{scope.row.username}}</router-link>
                </template>
            </el-table-column>
            <el-table-column prop="email" label="电子邮件"></el-table-column>
            <el-table-column label="管理员等级">
                <template slot-scope="scope">
                    {{scope.row.permission_id | formatByOptions(options.permission, 'id', 'text')}}
                </template>
            </el-table-column>
            <el-table-column prop="last_login_ip" label="最后登录ip"></el-table-column>
            <el-table-column prop="last_login_time" label="最后登录时间"></el-table-column>
            <el-table-column align="center" label="操作" width="190">
                <template slot-scope="scope">
                    <el-button size="mini" type="success" @click="detail(scope.row.id)">编辑</el-button>
                    <el-button size="mini" type="danger" @click="trashed(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination-component ref="pagination" v-on:getLists="getLists"></pagination-component>
        <el-dialog :title="formTitle" :visible.sync="formVisible" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form class="small-space" :model="form" :rules="rules" ref="form" label-position="left" label-width="100px">
                <input type="hidden" v-model="form.id">
                <el-form-item label="用户名" prop="username">
                    <el-input v-model="form.username" placeholder="登录账号，2-15个字符"></el-input>
                </el-form-item>
                <el-form-item label="登录邮箱" prop="email">
                    <el-input v-model="form.email" placeholder="登录邮箱，使用常用的邮箱"></el-input>
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input type="password" v-model="form.password" placeholder="登录密码，6-30个字符"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="repassword">
                    <el-input type="password" v-model="form.repassword" placeholder="再次输入密码"></el-input>
                </el-form-item>
                <el-form-item label="管理员等级" prop="permission_id">
                    <el-select v-model="form.permission_id" @change="changePermission" placeholder="请选择管理员等级">
                        <el-option v-for="item in options.permission" :key="item.id" :label="item.text" :value="item.id + ''"></el-option>
                    </el-select>
                    <span style="margin-left: 5px;">
              <i class="el-icon-loading" v-show="loadPermissionIconLoading"></i> 权限节点<strong>87</strong>个，
              <router-link to="/home">编辑</router-link>
            </span>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select v-model="form.status" placeholder="请选择管理员状态">
                        <el-option v-for="item in options.status" :key="item.value" :label="item.text" :value="item.value + ''"></el-option>
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
import ShortcutComponent from '../common/shortcut-component.vue';
export default {
    components: {
        'pagination-component': PaginationComponent,
        'table-header-component': TableHeaderComponent,
        'dialog-footer-component': DialogFooterComponent,
        'shortcut-component': ShortcutComponent
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
            loadPermissionIconLoading: false,
            form: {
                id: '',
                username: '',
                email: '',
                password: '',
                repassword: '',
                permission_id: '',
                status: ''
            },
            search_form: {
                username: '',
                permission_id: '',
            },
            options: {
                permission: '',
                status: [
                    { value: 0, text: '冻结' },
                    { value: 1, text: '正常' }
                ]
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
                permission_id: [
                    { required: true, message: '请选择管理员等级', trigger: 'blur' },
                ],
                status: [
                    { required: true, message: '请选择管理员状态', trigger: 'blur' },
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
        this.getLists();
    },
    methods: {
        getLists() {
            let _this = this;
            let paramsData = { 'data': { 'search_form': _this.search_form } };
            axios.get('/backend/admins?page=' + _this.$refs.pagination.pageData.current_page, { params: paramsData }).then(response => {
                let data = response.data;
                _this.tableData = data.data.lists.data;
                _this.options.permission = data.data.permissionOptions;
                _this.$refs.pagination.pageData.per_page = parseInt(data.data.lists.per_page);
                _this.$refs.pagination.pageData.current_page = parseInt(data.data.lists.current_page);
                _this.$refs.pagination.pageData.total = parseInt(data.data.lists.total);
            })
        },
        detail(id) {
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
                            url = '/backend/admins',
                            paramsData = { 'data': _this.form };
                    } else {
                        var method = 'put',
                            url = '/backend/admins/' + _this.form.id,
                            paramsData = { 'data': _this.form };
                    }
                    axios[method](url, paramsData).then(response => {
                        let data = response.data;
                        if (!data.status) {
                            _this.$message.error(data.message);
                            return false;
                        }
                        _this.$message.success(data.message);
                        _this.formVisible = false;
                        _this.getLists();
                        _this.$store.state.submitLoading = false;
                    }).catch(function(response) {
                        _this.$store.state.submitLoading = false;
                    });
                }
            });
        },
        trashed(id) {
            let _this = this;
            _this.$confirm('确定删除这个管理员吗', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                axios.delete('/backend/admins/' + id).then(response => {
                    _this.$message.success(response.data.message);
                    Vue.removeOneData(_this.tableData, id);
                });
            }).catch(function(response) {
                console.log(response);
            });
        },
        close() {
            let _this = this;
            _this.formVisible = false;
            Vue.resetForm(_this.form);
            _this.$refs.form.resetFields();
        },
        create() {
            let _this = this;
            _this.formTitle = '添加管理员';
            Vue.resetForm(_this.form);
            if (!_this.rules.password) {
                _this.rules.password = _this.passwordRules;
            }
            if (!_this.rules.repassword) {
                _this.rules.repassword = _this.repasswordRules;
            }
            _this.formVisible = true;
        },
        changePermission(val) {
            this.loadPermissionIconLoading = true;
        },
        getLinkDescribe(id) {
            document.getElementById('am-link-container').style.left = (Vue.getX(event) + 30) + 'px';
            document.getElementById('am-link-container').style.top = (Vue.getY(event) - 80) + 'px';
            //重复点击
            if (_this.$refs.describtion.describeData.id == id && _this.$refs.describtion.describeData.show) {
                _this.$refs.describtion.describeData.show = false;
                return false;
            }
            _this.tableData.forEach(function(item) {
                if (item.id === id) {
                    _this.$refs.describtion.describeData.id = item.id;
                    _this.$refs.describtion.describeData.username = item.username;
                    _this.$refs.describtion.describeData.show = true;
                }
            });
        }
    }
}
</script>