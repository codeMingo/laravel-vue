<template>
    <div class="content-container article-container">
        <el-row :gutter="10">
            <el-col :xs="24" :sm="24" :md="16" :lg="16">
                <div class="breadcrumb">
                    <el-breadcrumb separator="/">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <template v-if="$route.params.category_id">
                            <el-breadcrumb-item :to="{ path: '/article/index' }">全部文章</el-breadcrumb-item>
                            <el-breadcrumb-item>{{search_form.category_id | formatByOptions(article_options.categories, 'id', 'category_name')}}</el-breadcrumb-item>
                        </template>
                        <template v-else>
                            <el-breadcrumb-item>全部文章</el-breadcrumb-item>
                        </template>
                    </el-breadcrumb>
                </div>
                <div class="content-box article-box">
                    <div class="article-detail" v-for="(item, index) in article_data">
                        <div class='article-picture'><img :src="item.thumbnail"></div>
                        <div class="article-word">
                            <h2 class='article-title'>
                                <el-tag type="primary">{{item.category_id | formatByOptions(article_options.categories, 'id', 'category_name')}}</el-tag>
                                <router-link :to="{ path: '/article/detail/' + item.id }">{{item.title}}</router-link>
                            </h2>
                            <div class='article-right'>
                                <p>
                                    <span>作者：{{item.auther}}</span>
                                    <span>发表时间：{{item.created_at}}</span>
                                </p>
                            </div>
                            <div class='article-intro'>
                                <p>{{item.content | subString(0, 200)}}</p>
                            </div>
                            <div class='article-interactive'>
                                <p>
                                    <a href="javascript:;" @click="interactive(item.id, 'like')">赞：<span>{{item.like_count}}</span></a>
                                    <router-link :to="{ path: '/article/detail/' + item.id }">评论：<span>{{item.comment_count}}</span></router-link>
                                    <a href="javascript:;" @click="interactive(item.id, 'collect')">收藏：<span>{{item.collect_count}}</span></a>
                                    <router-link :to="{ path: '/article/detail/' + item.id }">阅读：<span>{{item.read_count}}</span></router-link>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="page-box">
                        <el-pagination @current-change="changeCurrentPage" :current-page.sync="article_pagination.current_page" :page-size="article_pagination.per_page" layout="total, prev, pager, next" :total="article_pagination.total">
                        </el-pagination>
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
.article-container {
    .article-box {
        padding-right: 20px;
        margin-right: 10px;
        border-right: 1px solid #eee;
        .article-detail {
            width: 100%;
            box-sizing: border-box;
            padding: 5px 8px;
            border: 1px solid #eee;
            box-shadow: 3px 5px 3px #fafafa;
            border-radius: 3px;
            float: left;
            position: relative;
            margin-bottom: 20px;
            .article-picture {
                width: 20%;
                float: left;
                img {
                    width: 100%;
                    border-radius: 3px;
                }
            }
            .article-word {
                float: left;
                width: 80%;
                box-sizing: border-box;
                padding-left: 10px;
                .article-title {
                    margin-bottom: 5px;
                    a {
                        font-size: 16px;
                        color: #333;
                    }
                    a:hover {
                        color: red;
                        text-decoration: underline;
                    }
                }
                .article-right {
                    margin-bottom: 5px;
                    font-size: 12px;
                    span {
                        margin-right: 5px;
                    }
                }
                .article-intro {
                    font-size: 13px;
                    line-height: 180%;
                    margin-bottom: 5px;
                    color: #666;
                }
                .article-interactive {
                    text-align: right;
                    position: absolute;
                    bottom: 5px;
                    right: 10px;
                    a {
                        font-size: 13px;
                        margin-right: 10px;
                        color: #4371BB;
                    }
                    a:hover {
                        text-decoration: underline;
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
            article_data: [],
            article_options: {
                categories: [],
            },
            article_pagination: {
                current_page: 1,
                total: 0,
                per_page: 10,
            },
            search_form: {
                category_id: this.$route.params.category_id
            }
        };
    },
    mounted() {
        this.getLists();
    },
    watch: {
        '$route' (to, from) {
            this.search_form.category_id = this.$route.params.category_id;
            this.getLists();
        }
    },
    methods: {
        getLists() {
            let _this = this;
            let paramsData = { 'data': { 'search_form': _this.search_form } };
            axios.get('/article/lists?page=' + _this.article_pagination.current_page, { params: paramsData }).then(response => {
                let { status, data, message } = response.data;
                _this.article_data = data.lists.data;
                _this.article_options = data.options;
                _this.article_pagination.per_page = parseInt(data.lists.per_page);
                _this.article_pagination.current_page = parseInt(data.lists.current_page);
                _this.article_pagination.total = parseInt(data.lists.total);
            });
        },
        changeCurrentPage(val) {
            this.article_pagination.current_page = val;
            this.getLists();
        },
        interactive(article_id, type) {
            let _this = this;
            axios.put('/article/interactive/' + article_id, {'data': {'type': type}}).then(response => {
                let { status, data, message } = response.data;
                if (!status) {
                    _this.$message.error(message);
                    return false;
                }
                _this.$message.success(message);
                _this.article_data.forEach(function(item) {
                    if (item.id == article_id) {
                        if (type == 'like') {
                            item.like_count ++;
                        } else if (type == 'hate') {
                            item.hate_count ++;
                        } else if (type == 'collect') {
                            item.collect_count ++;
                        }
                        return false;
                    }
                });
            });
        }
    }
}
</script>