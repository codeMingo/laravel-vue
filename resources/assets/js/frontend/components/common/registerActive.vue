<template>
    <div class="content-container register-active-container">
        <div class="register-active-box">
            <div class="active-tip">
                <p>激活邮件已下发送至您的邮箱
                    <el-tag type="primary">{{email}}</el-tag>，请注意查收</p>
                <p>未收到？
                    <a href="javascript:;" @click="sendEmail" v-if="sendEmailing == 'not'">重新发送</a>
                    <el-tag type="gray" v-if="sendEmailing == 'doing'">发送中</el-tag>
                    <template v-if="sendEmailing == 'yes'">
                        <el-tag type="success">发送成功</el-tag>
                        <span><strong>{{loadingSecond}}</strong>秒后重新操作</span>
                    </template>
                    <template v-if="sendEmailing == 'fail'">
                        <el-tag type="danger">发送失败</el-tag>
                        <span>请刷新网页重试</span>
                    </template>
                </p>
            </div>
            <div class="other-recommend">
                <h2 class="sidebar-title">其它推荐</h2>
                <el-row :gutter="10">
                    <el-col :xs="8" :sm="8" :md="8" :lg="8">
                        <div class="active-recommend article-recommend">
                            <h3>热门文章<a href="javascript:;">更多推荐 ++</a></h3>
                            <ul>
                                <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                                <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                                <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                                <li><a href="javascript:;">6.一小时同步一次，更多信息请查阅 文档导读</a></li>
                                <li><a href="javascript:;">7.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">8.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">9.一小时同步一次，更多信息请查阅 文档导读</a></li>
                                <li><a href="javascript:;">10.每周推送 Laravel 最新资讯</a></li>
                            </ul>
                        </div>
                    </el-col>
                    <el-col :xs="8" :sm="8" :md="8" :lg="8">
                        <div class="active-recommend video-recommend">
                            <h3>热门视频<a href="javascript:;">更多视频 ++</a></h3>
                            <ul>
                                <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                                <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                                <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            </ul>
                        </div>
                    </el-col>
                    <el-col :xs="8" :sm="8" :md="8" :lg="8">
                        <div class="active-recommend video-recommend">
                            <h3>热门留言<a href="javascript:;">更多留言 ++</a></h3>
                            <ul>
                                <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                                <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                                <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                                <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            </ul>
                        </div>
                    </el-col>
                </el-row>
            </div>
        </div>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>
.register-active-container {
    .register-active-box {
        .active-tip {
            p {
                line-height: 200%;
                a {
                    color: #999;
                    font-size: 12px;
                }
                span {
                    font-size: 12px;
                    strong {
                        font-weight: normal;
                    }
                }
            }
        }
        .other-recommend {
            .active-recommend {

                h3 {
                    border-bottom: 1px solid #eee;
                    color: #58B7FF;
                    padding-bottom: 5px;
                    text-indent: 10px;
                    margin-bottom: 15px;
                    font-size: 16px;
                    font-weight: normal;
                    padding: 0 10px 5px 5px;
                    a {
                        color: #ccc;
                        font-size: 12px;
                        float: right;
                    }
                    a:hover {
                        color: #333;
                    }
                }
                ul {
                    li {
                        margin-bottom: 10px;
                        font-size: 14px;
                        a {
                            color: #666;
                        }
                        a:hover {
                            color: red;
                            text-decoration: underline;
                        }
                    }
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
            id: this.$route.query.id,
            email: this.$route.query.email,
            sendEmailing: 'not',
            loadingSecond: 60
        };
    },
    beforeCreate() {
        let _this = this;
        axios.post('/check-active-user', { 'data': { email: _this.email } }).then(response => {
            let { status, data, message } = response.data;
            if (status && Object.keys(data).length == 0) {
                _this.$message.error(message);
                _this.$router.push({ path: '/index' });
            }
        }).catch(response => {
            _this.$message.error('未知错误，请刷新后重试');
        });
    },
    mounted() {

    },
    methods: {
        sendEmail() {
            let _this = this;
            _this.sendEmailing = 'doing';
            axios.post('/send-active-email', { 'data': { email: _this.email } }).then(response => {
                _this.sendEmailing = 'yes';
                let mailTimeEvent = setInterval(() => {
                    _this.loadingSecond--;
                    if (_this.loadingSecond == 0) {
                        _this.sendEmailing = 'not';
                        _this.loadingSecond = 60;
                        clearInterval(mailTimeEvent);
                    }
                }, 1000);
            }).catch(response => {
                _this.sendEmailing = 'fail';
                _this.$message.error('未知错误，请刷新后重试');
            });
        }
    }
}
</script>