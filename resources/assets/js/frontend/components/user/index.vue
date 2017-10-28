<template>
    <div class="content-container user-index-container">
        <el-row :gutter="30">
            <el-col :sm="8" :md="6" :lg="6">
                <div class="user-box">
                    <div class="user-tab list-image-text">
                        <el-row :gutter="10">
                            <el-col :xs="6" :sm="6" :md="4" :lg="4">
                                <div class="list-image">
                                    <img src="/images/focus_weixin.png">
                                </div>
                            </el-col>
                            <el-col :xs="18" :sm="18" :md="20" :lg="20">
                                <div class="list-text">
                                    <h3>ububs阿敏</h3>
                                    <p><span class="text-lable">个性签名：</span>不会做清蒸鱼的PHP工程师不是好的厨师！</p>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="user-tab menu-box">
                        <ul>
                            <li><a href="javascript:;" class="active">我的资料</a></li>
                            <li><a href="javascript:;">我的收藏</a></li>
                            <li><a href="javascript:;">我的动态</a></li>
                            <li><a href="javascript:;">上次观看</a></li>
                        </ul>
                    </div>
                </div>
            </el-col>
            <el-col :sm="16" :md="18" :lg="18">
                <div class="user-detail">
                    <el-row :gutter="60">
                        <el-col :sm="18" :md="18" :lg="18">
                            <h2 class="user-title">我的资料</h2>
                            <el-form :label-position="labelPosition" label-width="80px" :model="user_form" :rules="user_rules" ref="user_form">
                                <el-form-item label="用户名" >
                                    <el-input v-model="user_form.username"></el-input>
                                    <p class="intro-tip">用来登录和显示的名称</p>
                                </el-form-item>
                                <el-form-item label="邮箱地址">
                                    <el-input v-model="user_form.email" disabled></el-input>
                                    <p class="intro-tip">用来登录或找回密码等验证，<a href="javascript:;">修改</a></p>
                                </el-form-item>
                                <el-form-item label="个性签名">
                                    <el-input type="textarea" v-model="user_form.sign"></el-input>
                                    <p class="intro-tip">展示个性的一句短语</p>
                                </el-form-item>
                                <el-form-item label="网站地址">
                                    <el-input v-model="user_form.web_url"></el-input>
                                    <p class="intro-tip">自己的网站地址</p>
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="primary" :loading="update_submit_loading" @click.native.prevent="updateSubmit('user_form')">提交修改</el-button>
                                    <el-button @click="updateReset('user_form')">重置</el-button>
                                </el-form-item>
                            </el-form>
                        </el-col>
                        <el-col :sm="6" :md="6" :lg="6">
                            <div class="user-face">
                                <el-upload class="avatar-uploader" action="https://jsonplaceholder.typicode.com/posts/" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
                                    <img v-if="imageUrl" :src="imageUrl" class="avatar">
                                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                                </el-upload>
                                <p>
                                    <el-button plain style="width: 100%;">上传头像</el-button>
                                </p>
                            </div>
                        </el-col>
                    </el-row>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.user-index-container {
    .user-box {
        .user-tab {
            margin-bottom: 20px;
        }
        .menu-box {
            border: 1px solid #eee;
            border-radius: 3px;
            overflow: hidden;
            padding: 5px 10px;
            ul {
                width: 100%;
                li {
                    width: 50%;
                    float: left;
                    line-height: 180%;
                    padding: 5px 5px;
                    text-align: center;
                    box-sizing: border-box;
                    a {
                        color: #C1B5B5;
                        border: 1px solid #ccc;
                        display: block;
                        padding: 5px 0;
                    }
                    a.active {
                        color: #9DD9E5;
                        background: #F2FCF9;
                    }
                    a:hover {
                        color: #9DD9E5;
                        background: #F2FCF9;
                    }
                }
            }
        }
    }
    .user-detail {
        .user-title {
            font-weight: normal;
            font-size: 20px;
            color: #838181;
            border-bottom: 1px solid #DAD7D7;
            margin-bottom: 20px;
            padding-bottom: 5px;
        }
    }
}

.list-image-text {
    box-sizing: border-box;
    padding: 5px 8px;
    border: 1px solid #eee;
    border-radius: 3px;
    .list-image {
        img {
            max-width: 100%;
            max-height: 100%;
        }
    }
    .list-text {
        h3 {
            margin-bottom: 3px;
            color: #78CAF0;
        }
        p {
            color: #C1BDBD;
            .text-lable {
                color: #D8D1D1;
            }
        }
    }
}

.avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.avatar-uploader .el-upload:hover {
    border-color: #409EFF;
}

.avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
}

.avatar {
    width: 178px;
    height: 178px;
    display: block;
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            imageUrl: '',
            labelPosition: 'top',
            user_form: {
                username: 'usubs先生',
                email: 'linlm1994@gmail.com',
                sign: '不会做清蒸鱼的PHP工程师不是好的厨师！',
                web_url: 'http://www.ububs.com',
            },
            user_rules: {
                account: [
                    { required: true, message: '请输入用户名或邮箱账号', trigger: 'blur' },
                    { min: 2, max: 50, message: '登录账号不正确', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '请输入登录密码', trigger: 'blur' },
                    { min: 6, max: 30, message: '登录密码不正确', trigger: 'blur' }
                ]
            },
            update_submit_loading: false
        };
    },
    mounted() {},
    methods: {
        updateSubmit(formName) {
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    let params = { 'data': _this.loginForm };
                    axios.post('/login', params).then(response => {
                        _this.loginSubmitLoading = false;
                        let { status, data, message } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$store.commit('setStateValue', { 'user_data': data.data });
                        _this.$message.success(message);
                        _this.$router.push({ path: '/index' });
                    }).catch(response => {
                        _this.loginSubmitLoading = false;
                        _this.$message.error('网络连111接失败');
                    });
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        updateReset(formName) {
            this.$refs[formName].resetFields();
        },
        handleAvatarSuccess(res, file) {
            this.imageUrl = URL.createObjectURL(file.raw);
        },
        beforeAvatarUpload(file) {
            const isJPG = file.type === 'image/jpeg';
            const isLt2M = file.size / 1024 / 1024 < 2;

            if (!isJPG) {
                this.$message.error('上传头像图片只能是 JPG 格式!');
            }
            if (!isLt2M) {
                this.$message.error('上传头像图片大小不能超过 2MB!');
            }
            return isJPG && isLt2M;
        }
    }
}
</script>