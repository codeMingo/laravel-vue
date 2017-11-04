<template>
    <div class="user-detail">
        <el-row :gutter="60">
            <el-col :sm="18" :md="18" :lg="18">
                <h2 class="user-title">我的资料</h2>
                <el-form :label-position="labelPosition" label-width="80px" :model="user_data" :rules="user_rules" ref="user_data">
                    <el-form-item label="用户名" prop="username">
                        <el-input v-model="user_data.username" placeholder="用来登录和显示的名称"></el-input>
                        <p class="intro-tip">用来登录和显示的名称</p>
                    </el-form-item>
                    <el-form-item label="邮箱地址">
                        <el-input v-model="user_data.email" disabled></el-input>
                        <p class="intro-tip">用来登录或找回密码等验证，<a href="javascript:;">修改</a></p>
                    </el-form-item>
                    <el-form-item label="个性签名" prop="sign">
                        <el-input class="sign-textarea" type="textarea" v-model="user_data.sign" placeholder="展示个性的一句短语"></el-input>
                        <p class="intro-tip">展示个性的一句短语</p>
                    </el-form-item>
                    <el-form-item label="网站地址" prop="web_url">
                        <el-input v-model="user_data.web_url" placeholder="自己的网站地址"></el-input>
                        <p class="intro-tip">自己的网站地址</p>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" :loading="update_submit_loading" @click.native.prevent="updateSubmit('user_data')">提交修改</el-button>
                        <el-button @click="updateReset('user_data')">重置</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
            <el-col :sm="6" :md="6" :lg="6">
                <div class="user-face">
                    <el-upload class="avatar-uploader" action="https://jsonplaceholder.typicode.com/posts/" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
                        <img v-if="imageUrl" :src="imageUrl" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
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

.sign-textarea {
    textarea {
        font-size: 12px !important;
        color: #A3A1A1 !important;
    }
}
</style>
<script type="text/javascript">
export default {
    data() {
        return {
            user_data: {
                id: '',
                username: '',
                email: '',
                face: '',
                sign: '',
                web_url: ''
            },
            imageUrl: '',
            labelPosition: 'top',
            user_rules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 2, max: 20, message: '长度在 2 到 15 个字符', trigger: 'blur' }
                ]
            },
            update_submit_loading: false
        };
    },
    mounted() {
        this.getIndex();
    },
    methods: {
        getIndex() {
            let _this = this;
            axios.get('/user/index').then(response => {
                let { status, data, message } = response.data;
                _this.user_data = data.list;
            }).catch(response => {
                _this.$message.error('发生未知错误');
            });
        },
        updateSubmit(formName) {
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    let params = { 'data': _this.user_data };
                    axios.put('/user/update-user/' + _this.user_data.id, params).then(response => {
                        _this.update_submit_loading = false;
                        let { status, data, message } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$store.commit('setStateValue', { 'user_data': data.list });
                        _this.$message.success(message);
                    }).catch(response => {
                        _this.update_submit_loading = false;
                        _this.$message.error('网络连接失败');
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