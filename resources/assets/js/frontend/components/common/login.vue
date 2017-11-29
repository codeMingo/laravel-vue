<template>
    <div class="content-container login-container">
        <el-row :gutter="12">
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div class="login-detail-box">
                    <div class="login-header">
                        <a href="javascript:;">账户登录</a>
                    </div>
                    <div class="login-body">
                        <el-form label-position="right" label-width="80px" :model="login_form" :rules="login_rules" ref="login_form">
                            <el-form-item label="登录账号" prop="account">
                                <el-input v-model="login_form.account" placeholder="登录用户名/电子邮箱"></el-input>
                            </el-form-item>
                            <el-form-item label="密码" prop="password">
                                <el-input type="password" v-model="login_form.password" @keyup.enter.native="login" placeholder="登录密码，6-30个字符"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-checkbox-group v-model="login_form.remember">
                                    <el-checkbox label="记住密码" name="remember"></el-checkbox>
                                </el-checkbox-group>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" :loading="loading" @click.native.prevent="login('login_form')">登录</el-button>
                                <el-button @click="reset('login_form')">重置</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div class="login-other-type">
                    <h3 class='type-header'><span>使用其它方式登录</span></h3>
                    <div class='type-detail'>
                        <p>
                            <el-button type="info"><i class="fa fa-qq"></i>使用QQ登录</el-button>
                        </p>
                        <p>
                            <el-button type="success"><i class="fa fa-weixin"></i>使用微信登录</el-button>
                        </p>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.login-container {
    .login-detail-box {
        border-right: 1px solid #ccc;
        padding-right: 30px;
        .login-header {
            margin: 20px auto;
            text-align: center;
            width: 80%;
            border-bottom: 2px solid #666666;
            a {
                display: inline-block;
                color: #666666;
                padding: 10px 30px;
                i {
                    margin-right: 10px;
                }
            }
        }
        .login-body {
            clear: both;
        }
    }
    .login-other-type {
        text-align: center;
        width: 70%;
        margin: 100px auto;
        .type-header {
            border-bottom: 1px solid #D3DCE6;
            height: 40px;
            line-height: 40px;
            margin-bottom: 20px;
            span {
                display: inline-block;
                padding: 5px 10px;
                line-height: 30px;
                color: #8492A6;
            }
        }
        .type-detail {
            p {
                margin: 10px auto;
                width: 80%;
                button {
                    width: 100%;
                }
            }
        }
    }
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            login_form: {
                account: '',
                password: '',
                remember: true
            },
            login_rules: {
                account: [
                    { required: true, message: '请输入用户名或邮箱账号', trigger: 'blur' },
                    { min: 2, max: 50, message: '登录账号不正确', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '请输入登录密码', trigger: 'blur' },
                    { min: 6, max: 30, message: '登录密码不正确', trigger: 'blur' }
                ]
            },
            loading: false
        };
    },
    mounted() {
    },
    methods: {
        login(formName) {
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    let params = { 'data': _this.login_form };
                    axios.post('/login', params).then(response => {
                        _this.loading = false;
                        let { status, data, message } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$store.commit('setStateValue', {'is_login': true, 'user_data': data.list});
                        _this.$message.success(message);
                        _this.$router.push({ path: '/index' });
                    }).catch(response => {
                        _this.loading = false;
                        _this.$message.error('未知错误，请刷新后重试');
                    });
                }
            });
        },
        reset(formName) {
            this.$refs[formName].resetFields();
        }
    }
}
</script>