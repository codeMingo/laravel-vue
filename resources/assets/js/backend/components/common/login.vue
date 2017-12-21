<template>
    <div class="login-container">
        <el-form autoComplete="on" :model="loginForm" :rules="loginRules" ref="loginForm" label-position="left" label-width="0px" class="card-box login-form">
            <h3 class="title">Quickzz后台管理</h3>
            <el-form-item prop="account">
                <span class="svg-container"><i class="fa fa-envelope-o fa-fw"></i></span>
                <el-input name="account" type="text" v-model="loginForm.account" autoComplete="on" placeholder="用户名\邮箱"></el-input>
            </el-form-item>
            <el-form-item prop="password">
                <span class="svg-container"><i class="fa fa-key fa-fw"></i></span>
                <el-input name="password" type="password" @keyup.enter.native="loginSubmit" v-model="loginForm.password" autoComplete="on" placeholder="密码"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" style="width:100%;" :loading="loginSubmitLoading" @click.native.prevent="loginSubmit">登录</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss">
.tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 5px;
}

.login-container {
    position: relative;
    height: 100vh;
    background-color: #2d3a4b;
    input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0px 1000px #293444 inset !important;
        -webkit-text-fill-color: #fff !important;
    }
    input {
        background: transparent;
        border: 0px;
        -webkit-appearance: none;
        border-radius: 0px;
        padding: 12px 5px 12px 15px;
        color: #eeeeee;
        height: 47px;
    }
    .el-input {
        display: inline-block;
        height: 47px;
        width: 85%;
    }
    .svg-container {
        padding: 6px 5px 6px 15px;
        color: #889aa4;
    }
    .title {
        font-size: 26px;
        font-weight: 400;
        color: #eeeeee;
        margin: 0px auto 40px auto;
        text-align: center;
        font-weight: bold;
    }
    .login-form {
        position: absolute;
        left: 0;
        right: 0;
        width: 350px;
        padding: 35px 35px 15px 35px;
        margin: 120px auto;
    }
    .el-form-item {
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        color: #454545;
    }
    .forget-pwd {
        color: #fff;
    }
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            loginForm: {
                account: '',
                password: ''
            },
            loginRules: {
                account: [
                    { required: true, message: '请输入登录账户', trigger: 'blur' },
                ],
                password: [
                    { required: true, message: '请输入登录密码', trigger: 'blur' },
                    { min: 6, max: 30, message: '请输入正确的登录密码', trigger: 'blur' }
                ]
            },
            loginSubmitLoading: false
        }
    },
    methods: {
        loginSubmit() {
            let _this = this;
            _this.$refs.loginForm.validate(valid => {
                if (valid) {
                    _this.loginSubmitLoading = true;
                    let params = { 'data': _this.loginForm };
                    axios.post('/backend/login', params).then(function(res) {
                        _this.loginSubmitLoading = false;
                        let { status, data, message } = res.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$store.commit('setStateValue', { 'is_login': true, 'admin_data': data.list });
                        _this.$message.success(message);
                        _this.$router.push({ path: '/index/index' });
                    }).catch(function(err) {
                        _this.loginSubmitLoading = false;
                        _this.$message.error('网络连接失败');
                    });
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
    },
}
</script>