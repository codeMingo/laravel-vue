<template>
    <div class="content-container leave-container">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="16" :lg="16">
                <div class="breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item>留言板</el-breadcrumb-item>
                    </el-breadcrumb>
                </div>
                <div id="leave-box" class="content-box leave-box">
                    <div class="interactive-now">
                        <quill-edit class="interactive-now-content" ref="leaveQuillEditor" v-model="leave_form.input_content" :options="editorOption"></quill-edit>
                        <div class="interactive-now-submit">
                            <el-button type="primary" @click="leaveSubmit">提　交</el-button>
                        </div>
                    </div>
                    <div class="interactive-box leavel-list">
                        <h2 class="sidebar-title">留言列表 （<span>{{leave_pagination.total}}</span>条）</h2>
                        <div class="interactive-box">
                            <div class="interactive-list">
                                <div class="interactive-detail" v-for="(item, index) in leave_data">
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
                                <el-pagination @current-change="changeCurrentPage" :current-page.sync="leave_pagination.current_page" :page-size="leave_pagination.per_page" layout="total, prev, pager, next" :total="leave_pagination.total">
                                </el-pagination>
                            </div>
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
            leave_data: {},
            leave_form: {
                leave_id: '',
                input_content: '',
                content: '',
                response_demo: ''
            },
            leave_pagination: {
                per_page: 0,
                total: 0,
                current_page: 1
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
            }
        };
    },
    mounted() {
        this.getLeaveLists();
    },
    methods: {
        leaveSubmit() {
            let _this = this;
            let regx = /<p>(.+?)<\/p>/;

            if (!_this.leave_form.input_content) {
                _this.$message.error('操作失败，内容不可为空');
                return false;
            }
            let reg_text = _this.leave_form.input_content.replace(/\s/g, "");
            if (!reg_text) {
                _this.$message.error('操作失败，内容不可为空');
                return false;
            }
            let regx_content = regx.exec(_this.leave_form.input_content);
            if (_this.leave_form.leave_id) {
                if (!regx_content || regx_content[0] != _this.leave_form.response_demo) {
                    _this.$confirm('回复格式错误，是否直接进行评论？', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        _this.leave_form.leave_id = '';
                        _this.leave_form.content = _this.leave_form.input_content;
                        axios.put('/leave/publish', { 'data': _this.leave_form }).then(response => {
                            let { data, message, status } = response.data;
                            if (!status) {
                                _this.$message.error(message);
                                return false;
                            }
                            _this.$message.success(message);
                            if (data.list) {
                                _this.leave_data[_this.leave_data.length] = data.list;
                            }
                            Vue.resetForm(_this.leave_form);
                        }).catch(response => {
                            _this.$message.error('操作失败，未知错误');
                        })
                    }).catch(() => {
                        _this.$message.error('操作失败，未知错误');
                    });
                    return false;
                } else {
                    if (_this.leave_form.input_content == regx_content[0]) {
                        _this.$message.error('回复内容不得为空');
                        return false;
                    }
                    _this.leave_form.content = _this.leave_form.input_content.replace(/<p>(.+?)<\/p>/, '');
                    axios.put('/leave/publish', { 'data': _this.leave_form }).then(response => {
                        let { data, message, status } = response.data;
                        if (!status) {
                            _this.$message.error(message);
                            return false;
                        }
                        _this.$message.success(message);
                        if (data.list) {
                            for (let i = 0; i < _this.leave_data.length; i++) {
                                if (_this.leave_data[i].id === data.list.parent_id) {
                                    _this.leave_data[i]['response'][_this.leave_data[i]['response'].length] = data.list;
                                    _this.leave_data[i]['show_response'] = data.list.show_response;
                                }
                            }
                        }
                        Vue.resetForm(_this.leave_form);
                    }).catch(response => {
                        _this.$message.error('操作失败，未知错误');
                    })
                }
            } else {
                _this.leave_form.content = _this.leave_form.input_content;
                axios.put('/leave/publish', { 'data': _this.leave_form }).then(response => {
                    let { data, message, status } = response.data;
                    if (!status) {
                        _this.$message.error(message);
                        return false;
                    }
                    _this.$message.success(message);
                    if (data.list) {
                        _this.leave_data[_this.leave_data.length] = data.list;
                    }
                    Vue.resetForm(_this.leave_form);
                }).catch(response => {
                    _this.$message.error('操作失败，未知错误');
                })
            }
        },
        addResponse(username, leave_id) {
            /*console.log(document.getElementById('response-box').offsetTop);
            window.scrollTo(0, document.body.scrollHeight);*/
            window.scrollTo(0, document.getElementById('leave-box').offsetTop);
            this.$refs.leaveQuillEditor.quill.setContents([{
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
            this.leave_form.leave_id = leave_id;
            this.leave_form.response_demo = this.leave_form.input_content;
        },
        showResponse(item) {
            let flag = item.show_response ? false : true;
            this.$set(item, 'show_response', flag);
        },
        getLeaveLists() {
            let _this = this;
            axios.get('/leave/lists?page=' + _this.leave_pagination.current_page).then(response => {
                let { status, data, message } = response.data;
                _this.leave_data = data.lists.data;
                _this.leave_pagination.per_page = parseInt(data.lists.per_page);
                _this.leave_pagination.current_page = parseInt(data.lists.current_page);
                _this.leave_pagination.total = parseInt(data.lists.total);
            });
        },
        changeCurrentPage(val) {
            this.leave_pagination.current_page = val;
            this.getLeaveLists();
        }
    }
}
</script>