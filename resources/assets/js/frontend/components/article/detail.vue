<template>
    <div class="content-container article-detail-container">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="16" :lg="16">
                <div class="breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item :to="{ path: '/article/index' }">技术篇</el-breadcrumb-item>
                        <el-breadcrumb-item v-if="article_data.category" :to="{ path: '/article/index/' + article_data.category.id }">{{article_data.category.title}}</el-breadcrumb-item>
                        <el-breadcrumb-item>{{article_data.title}}</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div class="content-box article-detail-box">
                    <h2 class="article-title">{{article_data.title}}</h2>
                    <p class="article-right">
                        <span>作者：<strong>{{article_data.auther}}</strong></span>
                        <span>发表时间：<strong>{{article_data.created_at}}</strong></span>
                        <span>阅读：<strong>{{article_data.read | getCount}}</strong></span>
                        <span>评论：<strong>{{article_comments | getCount('parent_id', 0)}}</strong></span>
                        <span>点赞：<strong>{{article_data.interact | getCount('like', 1)}}</strong></span>
                    </p>
                    <div class="ql-container ql-snow">
                        <div class="article-content ql-editor" v-html="article_data.content" style="padding: 0;"></div>
                    </div>
                    <div class="article-label">
                        <p v-show="article_data.tag_lists">
                            <strong>标签：</strong>
                            <a href="javascript:;" v-for="item in article_data.tag_lists">{{item.tag_name}}</a>
                        </p>
                    </div>
                    <div class="user-tip" v-show="!this.$store.state.user_data.username">
                        <div id="commentsbmitarear">
                            <div class="guest_link">
                                <span class="log_ico"><i class="fa fa-user-o"></i></span>
                                <span class="txt">目前您尚未登录，请 <router-link to="/login">登录</router-link> 或 <router-link to="/register">注册</router-link> 后进行评论</span>
                            </div>
                        </div>
                    </div>
                    <div class="article-interactive">
                        <div class="article-more">
                            <div class="article-prev">
                                <template v-if="prev_article">
                                    <p>
                                        <router-link :to="{ path: '/article/detail/' + prev_article.id }"><i class="fa fa-chevron-left"></i>上一篇：{{prev_article.title}}</router-link>
                                    </p>
                                    <p>
                                        <span>@{{prev_article.created_at}}</span>
                                        <span>阅读({{prev_article.read | getCount}})</span>
                                        <span>赞({{prev_article.interact | getCount('like', 1)}})</span>
                                        <span>评论({{prev_article.comment | getCount('parent_id', 0)}})</span>
                                    </p>
                                </template>
                                <template v-else>
                                    <p><a href="javascript:;"><i class="fa fa-chevron-left"></i>这是第一篇</a></p>
                                </template>
                            </div>
                            <div class="article-next">
                                <template v-if="next_article">
                                    <p>
                                        <router-link :to="{ path: '/article/detail/' + next_article.id }">下一篇：{{next_article.title}}<i class="fa fa-chevron-right"></i></router-link>
                                    </p>
                                    <p>
                                        <span>@{{next_article.created_at}}</span>
                                        <span>阅读({{next_article.read | getCount}})</span>
                                        <span>赞({{next_article.interact | getCount('like', 1)}})</span>
                                        <span>评论({{next_article.comment | getCount('parent_id', 0)}})</span>
                                    </p>
                                </template>
                                <template v-else>
                                    <p><a href="javascript:;">已经是最后一篇<i class="fa fa-chevron-right"></i></a></p>
                                </template>
                            </div>
                        </div>
                        <div class="article-advertise">
                        </div>
                    </div>
                    <h2 class="sidebar-title">文章评论 （<span>{{article_comment_pagination.total}}</span>条）</h2>
                    <div class="interactive-box comment-list">
                        <div class="interactive-list">
                            <div class="interactive-detail" v-for="(item, index) in article_comments">
                                <div class="user-face"><a href="javascript:;"><img :src="item.user.face" /></a></div>
                                <div class="interactive-word">
                                    <p class="user-name"><a href="javascript:;">{{item.user.username}}</a><span>发表时间：{{item.created_at}}</span></p>
                                    <div class=" ql-container ql-snow">
                                        <p class="interactive-content ql-editor" v-html="item.content"></p>
                                    </div>
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
                                            <div class=" ql-container ql-snow">
                                                <p class="interactive-content ql-editor" v-html="response.content"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p style="clear:both;"></p>
                        </div>
                        <div class="page-box">
                            <el-pagination @current-change="changeCurrentPage" :current-page.sync="article_comment_pagination.current_page" :page-size="article_comment_pagination.per_page" layout="total, prev, pager, next" :total="article_comment_pagination.total">
                            </el-pagination>
                        </div>
                    </div>
                    <h2 class="sidebar-title">我要评论</h2>
                    <div class="interactive-now" id="response-box">
                        <quill-edit class="interactive-now-content" ref="articleQuillEditor" v-model="comment_form.input_content" :options="editorOption"></quill-edit>
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
                strong {
                    font-weight: normal;
                }
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
        .article-label {
            font-size: 13px;
            margin-top: 10px;
            strong {
                display: inline-block;
                padding: 0 8px;
                color: #017E66;
                background-color: rgba(1, 126, 102, 0.08);
                height: 24px;
                line-height: 24px;
                font-weight: normal;
                font-size: 13px;
                text-align: center;
            }
            strong:hover {
                background-color: #017E66;
                color: #fff;
                text-decoration: none;
            }
            p {
                a {
                    display: inline-block;
                    padding: 0.3em 0.9em;
                    margin: 0 0.5em 0.5em 0;
                    white-space: nowrap;
                    background-color: #f1f8ff;
                    border-radius: 3px;
                    color: #0366d6;
                }
                a:hover {
                    background-color: #ddeeff;
                }
            }
        }
        .user-tip {
            .guest_link {
                margin-bottom: 20px;
                clear: both;
                overflow: hidden;
                height: 80px;
                font-family: MicrosoftYaHei;
                font-size: 14px;
                color: #4f4f4f;
                text-align: center;
                background: #e7ecf0;
                .log_ico {
                    display: inline-block;
                    width: 40px;
                    height: 40px;
                    border-radius: 20px;
                    line-height: 40px;
                    margin-top: 20px;
                    margin-right: 20px;
                    text-align: center;
                    vertical-align: top;
                    background: #afbac3;
                    i {
                        color: #e2e9ef;
                    }
                }
                .txt {
                    display: inline-block;
                    vertical-align: top;
                    margin-top: 28px;
                    a {
                        color: #e73131;
                        text-decoration: underline;
                    }
                }
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
            article_comments: {},
            article_comment_pagination: {
                per_page: 0,
                total: 0,
                current_page: 1
            },
            article_id: this.$route.params.id,
            prev_article: '',
            next_article: '',
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
                        [{ 'color': [] }, { 'background': [] }],
                        ['clean'],
                        ['link']
                    ]
                }
            },

        };
    },
    watch: {
        '$route' (to, from) {
            this.article_id = this.$route.params.id;
            this.getList();
            this.getCommentLists();
        }
    },
    mounted() {
        this.getList();
        this.getCommentLists();
    },
    methods: {
        getList() {
            let _this = this;
            axios.get('/article/detail/' + _this.article_id).then(response => {
                let { status, data, message } = response.data;
                _this.article_data = data.list;
                _this.article_options = data.options;
                _this.prev_article = data.prev_article;
                _this.next_article = data.next_article;
            });
        },
        commentSubmit() {
            let _this = this;
            let regx = /<p>(.+?)<\/p>/;

            if (!_this.comment_form.input_content) {
                _this.$message.error('操作失败，内容不可为空');
                return false;
            }
            let reg_text = _this.comment_form.input_content.replace(/\s/g, "");
            if (!reg_text) {
                _this.$message.error('操作失败，内容不可为空');
                return false;
            }
            let regx_content = regx.exec(_this.comment_form.input_content);
            if (_this.comment_form.comment_id) {
                if (!regx_content || regx_content[0] != _this.comment_form.response_demo) {
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
                            if (data.list) {
                                _this.article_comments[_this.article_comments.length] = data.list;
                            }
                            Vue.resetForm(_this.comment_form);
                        }).catch(response => {
                            _this.$message.error('操作失败，未知错误');
                        })
                    }).catch(() => {
                        _this.$message.error('操作失败，未知错误');
                    });
                    return false;
                } else {
                    if (_this.comment_form.input_content == regx_content[0]) {
                        _this.$message.error('回复内容不得为空');
                        return false;
                    }
                    _this.comment_form.content = _this.comment_form.input_content.replace(/<p>(.+?)<\/p>/, '');
                    axios.put('/article/comment/' + _this.article_id, { 'data': _this.comment_form }).then(response => {
                        let { data, message, status } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$message.success(message);
                        if (data.list) {
                            for (let i = 0; i < _this.article_comments.length; i++) {
                                if (_this.article_comments[i].id === data.list.parent_id) {
                                    _this.article_comments[i]['response'][_this.article_comments[i]['response'].length] = data.list;
                                }
                            }
                        }
                        Vue.resetForm(_this.comment_form);
                    }).catch(response => {
                        _this.$message.error('操作失败，未知错误');
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
                        _this.article_comments[_this.article_comments.length] = data.list;
                    }
                    Vue.resetForm(_this.comment_form);
                }).catch(response => {
                    _this.$message.error('操作失败，未知错误');
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
        },
        getCommentLists() {
            let _this = this;
            axios.get('/article/comment-lists/' + _this.article_id + '?page=' + _this.article_comment_pagination.current_page).then(response => {
                let { status, data, message } = response.data;
                _this.article_comments = data.lists.data;
                _this.article_comment_pagination.per_page = parseInt(data.lists.per_page);
                _this.article_comment_pagination.current_page = parseInt(data.lists.current_page);
                _this.article_comment_pagination.total = parseInt(data.lists.total);
            });
        },
        changeCurrentPage(val) {
            this.article_comment_pagination.current_page = val;
            this.getCommentLists();
        }
    }
}
</script>