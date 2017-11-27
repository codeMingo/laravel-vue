<template>
    <div class="content-container register-container">
        <el-row :gutter="12">
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div class="register-detail-box">
                    <div class="register-header">
                        <a href="javascript:;" class="active"><i class="fa fa-envelope-o"></i>邮箱注册</a>
                        <a href="javascript:;"><i class="fa fa-mobile-phone"></i>手机注册</a>
                    </div>
                    <div class="register-body">
                        <el-form label-position="right" label-width="80px" :model="registerForm" :rules="registerRules" ref="registerForm">
                            <el-form-item label="头像" prop="face">
                                <div id="container">
                                    <div class="face-upload">
                                        <a class="btn btn-default btn-lg" id="face-btn" href="#" v-show="!upload_status"><i class="el-icon-plus avatar-uploader-icon"></i></a>
                                        <div class="upload-success" v-show="upload_status == 'success'"><img :src="registerForm.face" alt="头像"></div>
                                        <div class="upload-process" v-show="upload_status == 'loading'">正在上传{{upload_process}}%</div>
                                    </div>
                                </div>
                                <div class="el-upload__tip">只能上传jpg/png文件，且不超过500kb（不传默认使用系统头像）</div>
                            </el-form-item>
                            <el-form-item label="用户名" prop="username">
                                <el-input v-model="registerForm.username" placeholder="登录账号，2-15个字符"></el-input>
                            </el-form-item>
                            <el-form-item label="电子邮件" prop="email">
                                <el-input v-model="registerForm.email" placeholder="电子邮件，使用常用的邮箱"></el-input>
                            </el-form-item>
                            <el-form-item label="密码" prop="password">
                                <el-input type="password" v-model="registerForm.password" placeholder="登录密码，6-30个字符"></el-input>
                            </el-form-item>
                            <el-form-item label="确认密码" prop="repassword">
                                <el-input type="password" v-model="registerForm.repassword" placeholder="再次输入密码"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="registerSubmit('registerForm')" :loading="registerSubmitLoading">立即注册</el-button>
                                <el-button @click="registerReset('registerForm')">重置</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </el-col>
            <el-col :xs="12" :sm="12" :md="12" :lg="12">
                <div class="register-other-type">
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
.register-container {
    .register-detail-box {
        border-right: 1px solid #ccc;
        padding-right: 30px;
        .register-header {
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
            a.active,
            a:active,
            a:hover,
            a:focus {
                color: #1D8CE0;
            }
        }
        .register-body {
            clear: both;
            .face-upload {
                border: 1px dashed #d9d9d9;
                border-radius: 6px;
                cursor: pointer;
                position: relative;
                overflow: hidden;
                width: 100px;
                height: 100px;
                i {
                    display: inline-block;
                    width: 100px;
                    height: 100px;
                    text-align: center;
                    line-height: 100px;
                    color: #9C9C9C;
                    font-size: 20px;
                }
                .upload-process {
                    position: absolute;
                    width: 100px;
                    height: 100px;
                    line-height: 100px;
                    top: 0;
                    left: 0;
                    font-size: 12px;
                    text-align: center;
                    color: #C1C1C1;
                }
                .upload-success {
                    position: absolute;
                    width: 100px;
                    height: 100px;
                    top: 0;
                    left: 0;
                    img {
                        width: 100%;
                        height: 100%;
                    }
                }
            }
        }
    }
    .register-other-type {
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
import 'qiniu-js/dist/qiniu.js';
export default {
    data() {
        var checkRepassword = (rule, value, callback) => {
            if (value !== this.registerForm.password) {
                callback(new Error('密码输入不一致!'));
            } else {
                callback();
            }
        };
        return {
            upload_status: false,
            upload_process: 0,
            registerSubmitLoading: false,
            registerForm: {
                face: '',
                username: '',
                email: '',
                password: '',
                repassword: ''
            },
            uploadHeaders: {
                'X-CSRF-TOKEN': window.laravelCsrfToken
            },
            registerRules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 2, max: 20, message: '长度在 2 到 15 个字符', trigger: 'blur' }
                ],
                email: [
                    { required: true, message: '请输入登录邮箱', trigger: 'blur' },
                    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur,change' }
                ],
                password: [
                    { required: true, message: '请输入登录密码', trigger: 'blur' },
                    { min: 6, max: 30, message: '长度在 6 到 30 个字符', trigger: 'blur' }
                ],
                repassword: [
                    { required: true, message: '请再次输入密码', trigger: 'blur' },
                    { validator: checkRepassword, trigger: 'blur' }
                ]
            }
        };
    },
    mounted() {
        let _this = this;
        var uploader = Qiniu.uploader({
            runtimes: 'html5,flash,html4', //上传模式,依次退化
            browse_button: 'face-btn', //上传选择的点选按钮，**必需**
            uptoken_url: '/api/qiniu/token', //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
            // uptoken : '', //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
             unique_names: true, // 默认 false，key为文件名。若开启该选项，SDK为自动生成上传成功后的key（文件名）。
            // save_key: true,   // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK会忽略对key的处理
            domain: 'http://owmb1f0qu.bkt.clouddn.com', //bucket 域名，下载资源时用到，**必需**
            get_new_uptoken: false, //设置上传文件的时候是否每次都重新获取新的token
            container: 'container', //上传区域DOM ID，默认是browser_button的父元素，
            max_file_size: '100mb', //最大文件体积限制
            flash_swf_url: '/plupload-2.1.3/js/Moxie.swf', //引入flash,相对路径
            max_retries: 3, //上传失败最大重试次数
            dragdrop: true, //开启可拖曳上传
            drop_element: 'container', //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
            chunk_size: '4mb', //分块上传时，每片的体积
            auto_start: true, //选择文件后自动上传，若关闭需要自己绑定事件触发上传
            init: {
                'FilesAdded': function(up, files) {
                    plupload.each(files, function(file) {
                        // 文件添加进队列后,处理相关的事情
                        _this.upload_status = 'loading';
                    });
                },
                'BeforeUpload': function(up, file) {
                    // 每个文件上传前,处理相关的事情
                    
                    // var progress = new FileProgress(file, 'fsUploadProgress');
                    // var chunk_size = plupload.parseSize(this.getOption(
                    //     'chunk_size'));
                    // if (up.runtime === 'html5' && chunk_size) {
                    //     progress.setChunkProgess(chunk_size);
                    // }
                },
                'UploadProgress': function(up, file) {
                    // 每个文件上传时,处理相关的事情
                    // var progress = new FileProgress(file, 'fsUploadProgress');
                    // var chunk_size = plupload.parseSize(this.getOption(
                    //     'chunk_size'));
                    // progress.setProgress(file.percent + "%", file.speed,
                    //     chunk_size);
                    _this.upload_process = file.percent;
                },
                'FileUploaded': function(up, file, info) {
                    // 每个文件上传成功后,处理相关的事情
                    // 其中 info.response 是文件上传成功后，服务端返回的json，形式如
                    // {
                    //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                    //    "key": "gogopher.jpg"
                    //  }
                    // 参考http://developer.qiniu.com/docs/v6/api/overview/up/response/simple-response.html

                    let domain = up.getOption('domain');
                    let response = JSON.parse(info.response);
                    _this.registerForm.face = domain + '/' + response.key; // 获取上传成功后的文件的Url
                    console.log(_this.registerForm.face);
                    _this.upload_status = 'success';
                },
                'Error': function(up, err, errTip) {
                    //上传出错时,处理相关的事情
                },
                'UploadComplete': function() {
                    //队列文件处理完毕后,处理相关的事情
                },
                'Key': function(up, file) {
                    // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                    // 该配置必须要在 unique_names: false , save_key: false 时才生效

                    var key = "";
                    // do something with key here
                    return key
                }
            }
        });
    },
    methods: {
        registerSubmit(formName) {
            let _this = this;
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    _this.registerSubmitLoading = true;
                    console.log(_this.registerForm);
                    axios.post('/register/create-user', { 'data': _this.registerForm }).then(response => {
                        let data = response.data;
                        if (!data.status) {
                            _this.$message.error(data.message);
                            _this.registerSubmitLoading = false;
                            return false;
                        }
                        _this.$notify({
                            title: '成功',
                            message: data.message,
                            type: 'success'
                        });
                        let params = {
                            'id': data.data.id,
                            'email': data.data.email
                        };
                        _this.$router.push({ path: '/register-active', query: params });
                    }).catch(function(response) {
                        _this.registerSubmitLoading = false;
                    });
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        registerReset(formName) {
            this.$refs[formName].resetFields();
        },
        uploadFaceSuccess(res, file) {
            this.registerForm.face = res.data.faceUrl;
        },
        beforeUploadFace(file) {
            let _this = this,
                fileType = file.type,
                fileSize = file.size / 1024,
                truePictureType = ['image/jpeg', 'image/jpg', 'image/png', 'image/x-png'];
            if (truePictureType.indexOf(fileType) == -1) {
                _this.$message.error('请上传正确的头像图片的格式（jpg/png）');
                return false;
            }
            if (fileSize > 500) {
                _this.$message.error('上传头像图片大小不能超过 500KB');
                return false;
            }
            return true;
        }
    }
}
</script>