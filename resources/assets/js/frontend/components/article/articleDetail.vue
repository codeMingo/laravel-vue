<template>
    <div class="content-container article-detail-container ql-container ql-snow">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="16" :lg="16">
                <div class="breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item :to="{ path: '/article/index' }">技术篇</el-breadcrumb-item>
                        <el-breadcrumb-item>每周推送 Laravel 最新资讯</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="content-box article-detail-box ql-editor">
                    <h2 class="article-title">{{article_data.title}}</h2>
                    <p class="article-right">
                        <span>作者：<strong>{{article_data.auther}}</strong></span>
                        <span>发表时间：<strong>{{article_data.created_at}}</strong></span>
                        <span>阅读量：<strong>{{article_data.read_count}}</strong></span>
                        <span>评论：<strong>{{article_data.comments | getCount}}</strong></span>
                        <span>点赞：<strong>{{article_data.like_count}}</strong></span>
                    </p>
                    <!-- <div class="article-content" v-html="article_data.content"></div> -->
                    <div class="article-content" v-html="article_data.content"></div>

                    <div class="article-interactive">
                        <div class="article-more">
                            <div class="article-prev">
                                <p><a href="javascript:;"><i class="fa fa-chevron-left"></i>上一篇：Sobel算子边缘检测</a></p>
                                <p>@2017-08-31 阅读(301) 赞(19) 评论(25)</p>
                            </div>
                            <div class="article-next">
                                <p><a href="javascript:;">下一篇：Laravel 5.4 中文文档<i class="fa fa-chevron-right"></i></a></p>
                                <p>@2017-08-31 阅读(301) 赞(19) 评论(25)</p>
                            </div>
                        </div>
                        <div class="article-advertise">
                        </div>
                    </div>
                    <h2 class="sidebar-title">文章评论 （<span>{{article_data.comments | getCount}}</span>条）</h2>
                    <div class="interactive-box comment-list">
                        <div class="interactive-list">
                            <div class="interactive-detail" v-for="(item, index) in article_data.comments">
                                <div class="user-face"><a href="javascript:;"><img :src="item.user.face" /></a></div>
                                <div class="interactive-word">
                                    <p class="user-name"><a href="javascript:;">{{item.user.username}}</a><span>发表时间：{{item.created_at}}</span></p>
                                    <p class="interactive-content" v-html="item.content"></p>
                                    <p class="interactive-response-btn">
                                        <a href="javascript:;" @click="addResponse(item.user.username, item.id)">回复</a>
                                        <a href="javascript:;" @click="showResponse(item)" v-show="item.response && item.response.length > 0">
                                            &nbsp;&nbsp;
                                            <template v-if="!item.show_response">查看回复</template>
                                            <template v-if="item.show_response">收起回复</template>
                                        (<span>{{item.response | getCount}}</span>)</a>
                                    </p>
                                </div>
                                <div class="interactive-response" v-if="item.response && item.show_response">
                                    <div class="interactive-detail" v-for="(response, key) in item.response">
                                        <div class="user-face"><a href="javascript:;"><img :src="response.user.face"/></a></div>
                                        <div class="interactive-word">
                                            <p class="user-name">{{response.user.username}}<span>发表时间：{{response.created_at}}</span></p>
                                            <p class="interactive-content" v-html="response.content"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p style="clear:both;"></p>
                        </div>
                    </div>
                    <h2 class="sidebar-title">我要评论</h2>
                    <div class="interactive-now" id="response-box">
                        <quill-edit class="interactive-now-content" ref="articleQuillEditor" v-model="comment_form.input_content" :options="editorOption" @change="onEditorChange($event)"></quill-edit>
                        <div class="interactive-now-submit">
                            <el-button type="primary" @click="commentSubmit">提　交</el-button>
                        </div>
                    </div>
                </div>
            </el-col>
            <el-col :xs="0" :sm="0" :md="8" :lg="8">
                <div class='right-recommend'>
                    <div class="recommend-box hot-article">
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
                    <div class="recommend-box hot-video">
                        <h3>热门视频<a href="javascript:;">更多视频 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-comment">
                        <h3>精彩评论<a href="javascript:;">更多评论 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-leave">
                        <h3>精彩留言<a href="javascript:;">更多留言 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                    <div class="recommend-box hot-vote">
                        <h3>当前投票<a href="javascript:;">更多投票 ++</a></h3>
                        <ul>
                            <li><a href="javascript:;">1.Laravel 5.4 中文文档</a></li>
                            <li><a href="javascript:;">2.一小时同步一次，更多信息请查阅 文档导读</a></li>
                            <li><a href="javascript:;">3.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">4.每周推送 Laravel 最新资讯</a></li>
                            <li><a href="javascript:;">5.一小时同步一次，更多信息请查阅 文档导读</a></li>
                        </ul>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<style rel="stylesheet/scss" lang="scss" scoped>

.article-detail-container {
    .article-detail-box {
        padding-right: 20px;
        margin-right: 10px;
        border-right: 1px solid #eee;
        .article-title {
            font-size: 20px;
            text-align: center;
            margin: 10px 0;
            color: #222;
        }
        .article-right {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-bottom: 10px;
            span {
                margin: 0 5px;
            }
        }
        .article-content {
            line-height: 180%;
            overflow: hidden;
            font-size: 16px;
            color: #222;
            word-wrap: break-word;
            img {
                max-width: 100%;
            }
        }
        .article-interactive {
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
            .article-more {
                font-size: 12px;
                margin: 10px 0;
                .article-prev {
                    float: left;
                    i {
                        margin-right: 5px;
                    }
                }
                .article-next {
                    float: right;
                    text-align: right;
                    i {
                        margin-left: 5px;
                    }
                }
                .article-prev,
                .article-next {
                    p {
                        margin: 5px;
                        a {
                            color: #20A0FF;
                        }
                    }
                }
            }
            .article-advertise {
                clear: both;
                margin: 10px 0;
            }
        }
    }
}
</style>
<script type="text/javascript">
import { quillEditor } from 'vue-quill-editor';
export default {
    components: {
        'remote-js': {
            render(createElement) {
                return createElement('script', { attrs: { type: 'text/javascript', src: this.src } });
            },
            props: {
                src: { type: String, required: true },
            },
        },
        'remote-css': {
            render(createElement) {
                return createElement('link', { attrs: { type: 'text/css', rel: 'stylesheet', href: this.href } });
            },
            props: {
                href: { type: String, required: true },
            },
        },
        'quill-edit': quillEditor
    },
    data() {
        return {
            article_data: {},
            article_id: this.$route.params.id,
            currentPage1: 5,
            comment_form: {
                comment_id: '',
                input_content: '',
                content: '',
                response_demo: ''
            },
            editorOption: {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'indent': '-1' }, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        //[{ 'size': ['small', false, 'large', 'huge'] }],
                        //[{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        //[{ 'font': [] }],
                        //[{ 'align': [] }],
                        ['clean'],
                        ['link']
                        //['link', 'image', 'video']
                    ]
                }
            },

        };
    },
    mounted() {
        this.getList();
    },
    methods: {
        getList() {
            let _this = this;
            axios.get('/article/detail/' + _this.article_id).then(response => {
                let { status, data, message } = response.data;
                _this.article_data = data.data;
                _this.article_options = data.options;
            });
        },
        handleSizeChange(val) {
            console.log(`每页 ${val} 条`);
        },
        handleCurrentChange(val) {
            console.log(`当前页: ${val}`);
        },
        onEditorChange({ editor, html, text }) {
            console.log('editor change!', editor, html, text)

        },
        commentSubmit() {
            let _this = this;
            if (_this.comment_form.comment_id) {
                let regx = /<p>(.+?)<\/p>/;
                let regx_content = regx.exec(_this.comment_form.input_content);
                if (regx_content[0] != _this.comment_form.response_demo) {
                    _this.$confirm('回复格式错误，是否直接进行评论？', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        _this.comment_form.comment_id = '';
                        _this.comment_form.content = _this.comment_form.input_content;
                        axios.put('/article/comment/' + _this.article_id, { 'data': _this.comment_form }).then(response => {
                            let { data, message, status } = response.data;
                            if (!status) {
                                _this.$message.error(message);
                                return false;
                            }
                            _this.$message.success(message);
                            Vue.resetForm(_this.comment_form);
                        }).catch(response => {
                            _this.$message({
                                type: 'error',
                                message: '操作失败，未知错误'
                            });
                        })
                    }).catch(() => {
                        _this.$message({
                            type: 'info',
                            message: '操作失败，未知错误'
                        });
                    });
                    return false;
                } else {
                    _this.comment_form.content = _this.comment_form.input_content.replace(/<p>(.+?)<\/p>/, '');
                    axios.put('/article/comment/' + _this.article_id, { 'data': _this.comment_form }).then(response => {
                        let { data, message, status } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$message.success(message);
                        if (data.list) {
                            for (let i = 0; i < _this.article_data.comments.length; i++) {
                                if (_this.article_data.comments[i].id === data.list.parent_id) {
                                    _this.article_data.comments[i]['response'][_this.article_data.comments[i]['response'].length] = data.list;
                                }
                            }
                        }
                        Vue.resetForm(_this.comment_form);
                    }).catch(response => {
                        _this.$message({
                            type: 'error',
                            message: '操作失败，未知错误'
                        });
                    })
                }
            } else {
                _this.comment_form.content = _this.comment_form.input_content;
                axios.put('/article/comment/' + _this.article_id, { 'data': _this.comment_form }).then(response => {
                    let { data, message, status } = response.data;
                    if (!status) {
                        _this.$message.error(message);
                        return false;
                    }
                    _this.$message.success(message);
                    if (data.list) {
                        _this.article_data.comments[_this.article_data.comments.length] = data.list;
                    }
                    Vue.resetForm(_this.comment_form);
                }).catch(response => {
                    _this.$message({
                        type: 'error',
                        message: '操作失败，未知错误'
                    });
                })
            }
        },
        addResponse(username, comment_id) {
            /*console.log(document.getElementById('response-box').offsetTop);
            window.scrollTo(0, document.body.scrollHeight);*/
            window.scrollTo(0, document.getElementById('response-box').offsetTop);
            this.$refs.articleQuillEditor.quill.setContents([{
                    insert: '回复：' + username + '：',
                    attributes: {
                        italic: true,
                        underline: true,
                    }
                },
                {
                    insert: '(此行不可编辑，请点击空格至下一行输入内容，否则回复无效)',
                    attributes: {
                        italic: false,
                        underline: false,
                    }
                },
                {
                    insert: ' ',
                    attributes: {
                        italic: false,
                        underline: false,
                    }
                }
            ]);
            this.comment_form.comment_id = comment_id;
            this.comment_form.response_demo = this.comment_form.input_content;
        },
        showResponse(item) {
            let flag = item.show_response ? false : true;
            this.$set(item, 'show_response', flag);
        }
    }
}
</script>