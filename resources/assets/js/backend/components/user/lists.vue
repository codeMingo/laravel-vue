<template>
    <div class="app-container">
        <table-header-component v-on:create="create" v-on:getList="getList">
            <el-input v-model="searchForm.username" placeholder="请输入用户名" style="width: 200px;"></el-input>
            <el-input v-model="searchForm.email" placeholder="请输入电子邮箱" style="width: 200px;"></el-input>
            <el-select v-model="searchForm.status" placeholder="请选择状态">
                <el-option label="全部状态" value=""></el-option>
                <el-option v-for="item in options.status" :key="item.value" :label="item.text" :value="item.value"></el-option>
            </el-select>
        </table-header-component>
        <!-- 用户快捷窗口 -->
        <shortcut-component ref="describtion"></shortcut-component>
        <el-table :data="user_lists" border style="width: 100%">
            <el-table-column label="用户名">
                <template slot-scope="scope">
                    <a href="javascript:;" @click="getLinkDescribe(scope.row.id)">{{scope.row.username}}</a>
                </template>
            </el-table-column>
            <el-table-column prop="email" label="电子邮件"></el-table-column>
            <el-table-column prop="last_login_ip" label="最后登录ip"></el-table-column>
            <el-table-column prop="last_login_time" label="最后登录时间"></el-table-column>
            <el-table-column align="center" label="是否激活">
                <template slot-scope="scope">
                    <el-tag type="gray" v-show="!scope.row.active" @click.native="changeFieldValue('active', scope.row.id, 1)">未激活</el-tag>
                    <el-tag type="primary" v-show="scope.row.active" @click.native="changeFieldValue('active', scope.row.id, 0)">已激活</el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="状态">
                <template slot-scope="scope">
                    <el-tag type="gray" v-show="!scope.row.status" @click.native="changeFieldValue('status', scope.row.id, 1)">冻结</el-tag>
                    <el-tag type="primary" v-show="scope.row.status" @click.native="changeFieldValue('status', scope.row.id, 0)">正常</el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="操作" width="250">
                <template slot-scope="scope">
                    <router-link to="/home">
                        <el-button size="mini" type="info">查看详情</el-button>
                    </router-link>
                    <el-button size="mini" type="success" @click="detail(scope.row.id)">编辑</el-button>
                    <el-button size="mini" type="danger" @click="trashed(scope.row.id)">删除</el-button>
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
                        <el-option v-for="item in options.active" :key="item.value" :label="item.text" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select v-model="form.status" placeholder="请选择用户状态">
                        <el-option v-for="item in options.status" :key="item.value" :label="item.text" :value="item.value"></el-option>
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
            user_lists: [],
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
            },
            options: {
                status: '',
                active: ''
            },
            rules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 2, max: 15, message: '长度在 2 到 20 个字符', trigger: 'blur' }
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
        window._this = this;
        window._this.getList();
    },
    methods: {
        getList() {
            let paramsData = { 'data': { 'searchForm': window._this.searchForm } };
            axios.get('/backend/users?page=' + window._this.$refs.pagination.pageData.current_page, { params: paramsData }).then(response => {
                let {status, data, message} = response.data;
                window._this.user_lists = data.lists.data;
                window._this.options = data.options;
                window._this.$refs.pagination.pageData.per_page = parseInt(data.lists.per_page);
                window._this.$refs.pagination.pageData.current_page = parseInt(data.lists.current_page);
                window._this.$refs.pagination.pageData.total = parseInt(data.lists.total);
            })
        },
        detail(id) {
            window._this.formTitle = '修改';
            delete window._this.rules.password;
            delete window._this.rules.repassword;
            window._this.user_lists.forEach(function(item) {
                if (item.id === id) {
                    item.password = '';
                    window._this.form = Vue.copyObj(item);
                    window._this.formVisible = true;
                    return true;
                }
            });
        },
        submit(formName) {
            window._this.$refs[formName].validate((valid) => {
                if (valid) {
                    window._this.$store.state.submitLoading = true;
                    if (!window._this.form.id) {
                        var method = 'post',
                            url = '/backend/users',
                            paramsData = { 'data': window._this.form };
                    } else {
                        var method = 'put',
                            url = '/backend/users/' + window._this.form.id,
                            paramsData = { 'data': window._this.form };
                    }
                    axios[method](url, paramsData).then(data => {
                        window._this.$store.state.submitLoading = false;
                        if (!data.status) {
                            window._this.$message.error(data.message);
                            return false;
                        }
                        window._this.$message.success(data.message);
                        window._this.formVisible = false;
                        window._this.getList();
                    });
                }
            });
        },
        trashed(id) {
            window._this.$confirm('确定删除这个用户吗').then(() => {
                axios.delete('/backend/users/' + id).then(data => {
                    window._this.$message.success(data.message);
                    Vue.removeOneData(window._this.user_lists, id);
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
        changeFieldValue(field, id, value) {
            let paramsData = { 'data': { 'field': field, 'value': value } };
            axios.post('/backend/user/change-field-value/' + id, paramsData).then(response => {
                if (!response.data.status) {
                    window._this.$message.error(response.data.message);
                    return false;
                }
                window._this.$message.success(response.data.message);
                window._this.user_lists.forEach((item, index) => {
                    if (item.id == id) {
                        item[field] = value;
                    }
                });
            });
        },
        getLinkDescribe(id) {
            document.getElementById('am-link-container').style.left = (Vue.getX(event) + 30) + 'px';
            document.getElementById('am-link-container').style.top = (Vue.getY(event) - 80) + 'px';
            //重复点击
            if (window._this.$refs.describtion.describeData.id == id && window._this.$refs.describtion.describeData.show) {
                window._this.$refs.describtion.describeData.show = false;
                return false;
            }
            window._this.user_lists.forEach(function(item) {
                if (item.id === id) {
                    window._this.$refs.describtion.describeData.id = item.id;
                    window._this.$refs.describtion.describeData.username = item.username;
                    window._this.$refs.describtion.describeData.show = true;
                }
            });
        }
    }
}
</script>